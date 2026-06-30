<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->separator(','),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('sync')
                ->label('Sync Account')
                ->icon('heroicon-o-arrow-path')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Sync Account')
                ->modalDescription('This will sync the user account with the HRMIS database.')
                ->modalSubmitActionLabel('Sync')
                ->action(function ($record) {
                    $hrmisPassword = DB::connection('hrmis')
                        ->table('users')
                        ->where('email', $record->email)
                        ->value('password');

                    if (! $hrmisPassword) {
                        Notification::make()
                            ->title('Sync failed')
                            ->body('No matching HRMIS account was found for this email.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $record->update([
                        'password' => $hrmisPassword,
                    ]);

                    Notification::make()
                        ->title('Account synced successfully')
                        ->success()
                        ->send();
                })

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
