<?php

namespace App\Filament\Resources\AuditEvents;

use App\Enums\AuditAction;
use App\Enums\Role;
use App\Filament\Resources\AuditEvents\Pages\ListAuditEvents;
use App\Models\AuditEvent;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

// só leitura: a tabela recusa alterações no banco (trigger audit_events_immutable)
final class AuditEventResource extends Resource
{
    protected static ?string $model = AuditEvent::class;

    protected static ?string $slug = 'auditoria';

    protected static ?string $modelLabel = 'evento';

    protected static ?string $pluralModelLabel = 'auditoria';

    protected static ?string $navigationLabel = 'Auditoria';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?int $navigationSort = 3;

    // ações em que quem fez é o autor do material; na decisão, quem fez é quem moderou
    private const AUTHOR_ACTIONS = [
        AuditAction::MaterialSubmitted,
        AuditAction::RevisionSubmitted,
        AuditAction::SubmissionCancelled,
        AuditAction::MaterialDeleted,
    ];

    /** @return Builder<AuditEvent> */
    public static function getEloquentQuery(): Builder
    {
        return AuditEvent::query()->with(['actor', 'user', 'material']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->stackedOnMobile()
            ->defaultSort(fn (Builder $query) => $query->orderByDesc('created_at')->orderByDesc('id'))
            ->columns([
                TextColumn::make('created_at')->label('Data')->dateTime('d/m/Y H:i'),
                TextColumn::make('action')->label('Ação')->state(fn (AuditEvent $record): string => $record->action->label()),
                TextColumn::make('actor')
                    ->label('Quem fez')
                    ->state(fn (AuditEvent $record): string => self::actor($record))
                    ->description(fn (AuditEvent $record): ?string => self::anonymousMark($record))
                    ->wrap(),
                TextColumn::make('subject')->label('Sobre')->state(fn (AuditEvent $record): string => self::subject($record))->wrap(),
                TextColumn::make('summary')->label('Resumo')->state(fn (AuditEvent $record): ?string => self::summary($record))->placeholder('—')->wrap(),
            ])
            ->filters([
                SelectFilter::make('action')->label('Ação')->options(AuditAction::labels()),
                SelectFilter::make('actor_id')->label('Quem fez')->relationship('actor', 'display_name')->searchable(),
                Filter::make('period')
                    ->schema([
                        DatePicker::make('from')->label('De'),
                        DatePicker::make('until')->label('Até'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'] ?? null, fn (Builder $query, string $date) => $query->whereDate('created_at', '>=', $date))
                        ->when($data['until'] ?? null, fn (Builder $query, string $date) => $query->whereDate('created_at', '<=', $date))),
            ])
            ->recordActions([
                ViewAction::make()->label('Ver')->modalHeading('Detalhes do evento'),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        $previous = fn (AuditEvent $record): bool => isset($record->data['previous']);

        return $schema->columns(1)->components([
            TextEntry::make('created_at')->label('Data')->dateTime('d/m/Y H:i:s'),
            TextEntry::make('action')->label('Ação')->state(fn (AuditEvent $record): string => $record->action->label()),
            TextEntry::make('actor')
                ->label('Quem fez')
                ->state(fn (AuditEvent $record): string => self::actor($record))
                ->belowContent(fn (AuditEvent $record): ?string => self::anonymousMark($record)),
            TextEntry::make('subject')->label('Sobre')->state(fn (AuditEvent $record): string => self::subject($record)),
            TextEntry::make('summary')->label('Resumo')->state(fn (AuditEvent $record): ?string => self::summary($record))->placeholder('—'),
            TextEntry::make('data.previous.title')->label('Versão anterior: título')->visible($previous),
            TextEntry::make('data.previous.description')->label('Versão anterior: descrição')->placeholder('—')->visible($previous),
            TextEntry::make('data.previous.url')->label('Versão anterior: link')->visible($previous),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => ListAuditEvents::route('/')];
    }

    private static function actor(AuditEvent $event): string
    {
        return $event->actor_id === null ? 'Workflow do GitHub' : (string) $event->actor?->display_name;
    }

    // o ADMINISTRADOR vê o autor real do envio anônimo, com a marca (decisoes.md §55.2)
    private static function anonymousMark(AuditEvent $event): ?string
    {
        return ($event->data['anonymous'] ?? false) && in_array($event->action, self::AUTHOR_ACTIONS, true) ? 'Enviado anonimamente' : null;
    }

    private static function subject(AuditEvent $event): string
    {
        if ($event->material !== null) {
            return $event->material->code.' · '.$event->material->title;
        }

        return (string) $event->user?->display_name;
    }

    private static function summary(AuditEvent $event): ?string
    {
        if ($event->action === AuditAction::RoleChanged) {
            return Role::from($event->data['from'])->label().' → '.Role::from($event->data['to'])->label();
        }

        return ($event->data['revision'] ?? false) ? 'Alteração' : null;
    }
}
