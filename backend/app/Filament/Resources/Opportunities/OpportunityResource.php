<?php

namespace App\Filament\Resources\Opportunities;

use App\Enums\OpportunityType;
use App\Filament\Resources\Opportunities\Pages\ListOpportunities;
use App\Models\Opportunity;
use App\Providers\Filament\AdminPanelProvider;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

// consulta somente leitura; o ADMINISTRADOR vê quem enviou, o que a área pública nunca mostra (decisoes.md §70)
final class OpportunityResource extends Resource
{
    protected static ?string $model = Opportunity::class;

    protected static ?string $slug = 'oportunidades';

    protected static ?string $modelLabel = 'oportunidade';

    protected static ?string $pluralModelLabel = 'oportunidades';

    protected static ?string $navigationLabel = 'Oportunidades';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static ?int $navigationSort = 3;

    // a OpportunityPolicy cuida das ações da API; aqui o acesso já é limitado pelo canAccessPanel
    protected static bool $shouldSkipAuthorization = true;

    private const SITUATIONS = [
        'PENDENTE' => 'Pendente',
        'APROVADO' => 'Aprovada',
        'ENCERRADO' => 'Encerrada',
        'REJEITADO' => 'Rejeitada',
        'CANCELADO' => 'Cancelada',
        'EXCLUIDO' => 'Excluída',
    ];

    /** @return Builder<Opportunity> */
    public static function getEloquentQuery(): Builder
    {
        // o Filament já carrega junto as relações das colunas (author, decider)
        return Opportunity::withTrashed();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->stackedOnMobile()
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->label('Oportunidade')
                    ->description(fn (Opportunity $record): string => $record->code.' · '.$record->type->label())
                    ->wrap()
                    ->searchable(),
                TextColumn::make('author.display_name')->label('Quem enviou')->wrap()->searchable(),
                TextColumn::make('situation')
                    ->label('Situação')
                    ->state(fn (Opportunity $record): string => self::situation($record))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pendente' => 'warning',
                        'Aprovada' => 'success',
                        'Rejeitada', 'Excluída' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')->label('Enviada em')->date('d/m/Y', AdminPanelProvider::TIMEZONE)->sortable()->visibleFrom('xl'),
                TextColumn::make('decider.display_name')
                    ->label('Decisão')
                    ->description(fn (Opportunity $record): ?string => $record->decided_at?->setTimezone(AdminPanelProvider::TIMEZONE)->format('d/m/Y')) // @pest-mutate-ignore: RemoveNullSafeOperator
                    ->placeholder('—')
                    ->wrap(),
            ])
            ->filters([
                SelectFilter::make('type')->label('Tipo')->options(OpportunityType::labels()),
            ])
            ->recordActions([
                ViewAction::make()->label('Ver')->modalHeading('Detalhes da oportunidade'),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        // layout do detalhe, sem regra
        return $schema->columns(1)->components([ // @pest-mutate-ignore: IncrementInteger,DecrementInteger
            TextEntry::make('code')->label('Código'),
            TextEntry::make('title')->label('Título'),
            TextEntry::make('type')->label('Tipo')->formatStateUsing(fn (Opportunity $record): string => $record->type->label()),
            TextEntry::make('modality')->label('Modalidade')->formatStateUsing(fn (Opportunity $record): string => $record->modality->label()),
            TextEntry::make('deadline')->label('Inscrições até')->date('d/m/Y')->placeholder('Sem data: 15 dias após a aprovação'),
            TextEntry::make('description')->label('Descrição'),
            TextEntry::make('url')->label('Link')->color('primary')->url(fn (Opportunity $record): string => $record->url)->openUrlInNewTab(),
            TextEntry::make('author.display_name')->label('Quem enviou'),
            TextEntry::make('situation')->label('Situação')->state(fn (Opportunity $record): string => self::situation($record)),
            TextEntry::make('rejection_reason')->label('Motivo da rejeição')->visible(fn (Opportunity $record): bool => $record->rejection_reason !== null),
            TextEntry::make('created_at')->label('Enviada em')->dateTime('d/m/Y H:i'),
            TextEntry::make('decider.display_name')->label('Decidida por')->placeholder('—'),
            TextEntry::make('decided_at')->label('Decidida em')->dateTime('d/m/Y H:i')->placeholder('—'),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => ListOpportunities::route('/')];
    }

    private static function situation(Opportunity $opportunity): string
    {
        return self::SITUATIONS[match (true) {
            $opportunity->trashed() => 'EXCLUIDO',
            $opportunity->isExpired() => 'ENCERRADO',
            default => $opportunity->status->value,
        }];
    }
}
