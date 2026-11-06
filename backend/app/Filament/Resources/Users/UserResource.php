<?php

namespace App\Filament\Resources\Users;

use App\Actions\ChangeUserRole;
use App\Enums\Role;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

final class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $slug = 'usuarios';

    protected static ?string $modelLabel = 'usuário';

    protected static ?string $pluralModelLabel = 'usuários';

    protected static ?string $navigationLabel = 'Usuários';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    // é a primeira do menu, então o painel abre aqui
    protected static ?int $navigationSort = 1;

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')->label('Nome')->searchable(),
                TextColumn::make('display_name')->label('Nome de exibição')->searchable(),
                TextColumn::make('email')->label('E-mail')->searchable(),
                TextColumn::make('role')->label('Papel')->formatStateUsing(fn (Role $state): string => $state->label()),
                TextColumn::make('created_at')->label('Cadastro')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('role')->label('Papel')->options(Role::labels()),
            ])
            ->recordActions([
                self::changeRoleAction(),
            ]);
    }

    private static function changeRoleAction(): Action
    {
        return Action::make('changeRole')
            ->label('Alterar papel')
            ->icon(Heroicon::OutlinedUserCircle)
            ->hidden(fn (User $record): bool => $record->is(Filament::auth()->user()))
            ->modalHeading('Alterar papel')
            ->modalDescription('Quem for definido como ADMINISTRADOR passa a acessar este painel e a identidade dos autores de materiais anônimos.')
            ->modalSubmitActionLabel('Salvar papel')
            ->fillForm(fn (User $record): array => ['role' => $record->role->value])
            ->schema([
                Select::make('role')->label('Papel')->options(Role::labels())->required()->selectablePlaceholder(false),
            ])
            ->successNotificationTitle('Papel atualizado.')
            ->action(function (array $data, User $record, Action $action): void {
                /** @var User $actor */
                $actor = Filament::auth()->user();

                app(ChangeUserRole::class)->handle($actor, $record, Role::from($data['role']));
                $action->success();
            });
    }

    public static function getPages(): array
    {
        return ['index' => ListUsers::route('/')];
    }
}
