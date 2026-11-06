<?php

namespace App\Filament\Resources\Users;

use App\Enums\Role;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use BackedEnum;
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

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')->label('Nome')->searchable(),
                TextColumn::make('display_name')->label('Nome de exibição')->placeholder('—')->searchable(),
                TextColumn::make('email')->label('E-mail')->searchable(),
                TextColumn::make('role')->label('Papel')->formatStateUsing(fn (Role $state): string => $state->label()),
                TextColumn::make('created_at')->label('Cadastro')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('role')->label('Papel')->options(Role::labels()),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ListUsers::route('/')];
    }
}
