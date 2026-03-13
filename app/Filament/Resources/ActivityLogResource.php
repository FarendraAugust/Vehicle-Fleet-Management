<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Spatie\Activitylog\Models\Activity;

class ActivityLogResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Activity Logs';

    protected static ?string $navigationGroup = 'System';

    protected static ?int $navigationSort = 99;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('causer.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Activity')
                    ->searchable(),

                BadgeColumn::make('subject_type')
                    ->label('Module')
                    ->formatStateUsing(fn ($state) =>
                        class_basename($state)
                    ),

                TextColumn::make('created_at')
                    ->label('Time')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

            ])

            ->filters([])

            ->actions([])

            ->bulkActions([])

            ->defaultSort('created_at', 'desc')

            ->recordUrl(null);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
        ];
    }
}