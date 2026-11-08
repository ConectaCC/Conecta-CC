<?php

namespace App\Filament\Resources\Materials;

use App\Filament\Resources\Materials\Pages\ListMaterials;
use App\Models\Material;
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

// consulta somente leitura: retirada, revisão e reversão são pós-MVP (decisoes.md §63)
final class MaterialResource extends Resource
{
    protected static ?string $model = Material::class;

    protected static ?string $slug = 'moderacoes';

    protected static ?string $modelLabel = 'moderação';

    protected static ?string $pluralModelLabel = 'moderações';

    protected static ?string $navigationLabel = 'Moderações';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static ?int $navigationSort = 2;

    // a MaterialPolicy cuida das ações da API; aqui o acesso já é limitado pelo canAccessPanel
    protected static bool $shouldSkipAuthorization = true;

    private const SITUATIONS = [
        'PENDENTE' => 'Pendente',
        'APROVADO' => 'Aprovado',
        'REJEITADO' => 'Rejeitado',
        'CANCELADO' => 'Cancelado',
        'EXCLUIDO' => 'Excluído',
    ];

    /** @return Builder<Material> */
    public static function getEloquentQuery(): Builder
    {
        return Material::withTrashed()->with([
            'author',
            'decider',
            'discipline',
            'revisionOf' => fn ($query) => $query->withTrashed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->stackedOnMobile()
            ->defaultSort('created_at', 'desc')
            ->columns([
                // colunas agrupadas para caber sem rolagem lateral; o detalhe mostra todos os campos
                TextColumn::make('title')
                    ->label('Material')
                    ->description(fn (Material $record): string => $record->code.' · '.$record->discipline->name)
                    ->wrap()
                    ->searchable(),
                TextColumn::make('author.display_name')
                    ->label('Autor')
                    ->description(fn (Material $record): ?string => $record->anonymous ? 'Enviado anonimamente' : null)
                    ->wrap()
                    ->searchable(),
                TextColumn::make('situation')
                    ->label('Situação')
                    ->state(fn (Material $record): array => self::situation($record))
                    ->badge()
                    ->color(fn (string $state): string => self::situationColor($state)),
                TextColumn::make('created_at')->label('Enviado em')->date('d/m/Y', AdminPanelProvider::TIMEZONE)->sortable()->visibleFrom('xl'),
                TextColumn::make('decider.display_name')
                    ->label('Decisão')
                    ->description(fn (Material $record): ?string => $record->decided_at?->setTimezone(AdminPanelProvider::TIMEZONE)->format('d/m/Y'))
                    ->placeholder('—')
                    ->wrap(),
            ])
            ->filters([
                SelectFilter::make('situation')
                    ->label('Situação')
                    ->options(self::SITUATIONS)
                    ->query(fn (Builder $query, array $data): Builder => match ($data['value'] ?? null) {
                        null, '' => $query,
                        'EXCLUIDO' => $query->whereNotNull('deleted_at'),
                        default => $query->whereNull('deleted_at')->where('status', $data['value']),
                    }),
            ])
            ->recordActions([
                ViewAction::make()->label('Ver')->modalHeading('Detalhes do envio'),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            TextEntry::make('code')->label('Código'),
            TextEntry::make('title')->label('Título'),
            TextEntry::make('discipline.name')->label('Disciplina'),
            TextEntry::make('author.display_name')
                ->label('Autor')
                ->belowContent(fn (Material $record): ?string => $record->anonymous ? 'Enviado anonimamente' : null),
            TextEntry::make('situation')
                ->label('Situação')
                ->state(fn (Material $record): array => self::situation($record))
                ->badge()
                ->color(fn (string $state): string => self::situationColor($state)),
            TextEntry::make('created_at')->label('Enviado em')->dateTime('d/m/Y H:i'),
            TextEntry::make('decider.display_name')->label('Decidido por')->placeholder('—'),
            TextEntry::make('decided_at')->label('Decidido em')->dateTime('d/m/Y H:i')->placeholder('—'),
            TextEntry::make('description')->label('Descrição')->placeholder('—'),
            TextEntry::make('url')->label('Link')->color('primary')->url(fn (Material $record): string => $record->url)->openUrlInNewTab(),
            TextEntry::make('rejection_reason')->label('Motivo da rejeição')->visible(fn (Material $record): bool => $record->rejection_reason !== null),
            TextEntry::make('revisionOf.title')->label('Título original')->visible(fn (Material $record): bool => $record->revises_id !== null),
            TextEntry::make('revisionOf.url')
                ->label('Link original')
                ->color('primary')
                ->url(fn (Material $record): ?string => $record->revisionOf?->url)
                ->openUrlInNewTab()
                ->visible(fn (Material $record): bool => $record->revises_id !== null),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => ListMaterials::route('/')];
    }

    /** @return list<string> */
    private static function situation(Material $material): array
    {
        $status = $material->trashed() ? 'EXCLUIDO' : $material->status->value;

        return [self::SITUATIONS[$status], ...($material->revises_id !== null ? ['Alteração'] : [])];
    }

    // decisoes.md §58: Alteração é uma marca à parte e não substitui a situação
    private static function situationColor(string $situation): string
    {
        return match ($situation) {
            'Pendente' => 'warning',
            'Aprovado' => 'success',
            'Rejeitado', 'Excluído' => 'danger',
            'Alteração' => 'change',
            default => 'gray',
        };
    }
}
