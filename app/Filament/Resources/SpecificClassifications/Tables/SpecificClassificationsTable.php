<?php

namespace App\Filament\Resources\SpecificClassifications\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SpecificClassificationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Specific Classification')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('parentClassification.name')
                    ->label('Parent Classification')
                    ->placeholder('None')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('generalClassification.name')
                    ->label('General Classification')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
