<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('migrateUser')
            ->label('Migrate User')
            ->color('success')
            ->icon('heroicon-o-user-group')
            ->modalHeading('Migrate User')
            ->schema([
                Select::make('user')
                    ->label('Select User')
                    ->prefixIcon('heroicon-o-user')
                    ->searchable()
                    ->options(fn (): array => DB::connection('hrmis')
                        ->table('users')
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->toArray()
                    ),
                Select::make('roles')
                    ->relationship('roles', 'name')
                    ->preload()
                    ->searchable(),
            ])
            ->modalSubmitActionLabel('Migrate')
            ->action(function (array $data) {
                try {
                    $user = DB::connection('hrmis')
                        ->table('users')
                        ->select([
                            'name', 'email', 'password',
                            'email_verified_at', 'remember_token',
                            'created_at', 'updated_at',
                        ])
                        ->where('id', $data['user'])
                        ->first();

                    $isStored = \App\Models\User::create([
                        'name' => $user->name,
                        'email' => $user->email,
                        'password' => $user->password,
                        'email_verified_at' => $user->email_verified_at,
                        'remember_token' => $user->remember_token,
                        'created_at' => $user->created_at,
                        'updated_at' => $user->updated_at,
                    ])->syncRoles($data['roles']);
    
                    Notification::make()
                        ->title('User Migrated Successfully')
                        ->success()
                        ->send();

                } catch (\Throwable $th) {
                    Notification::make()
                        ->title('Error Migrating User')
                        ->body($th->getMessage())
                        ->icon('heroicon-o-exclamation-circle')
                        ->danger()
                        ->send();
                }
            }),

            CreateAction::make(),
        ];
    }
}