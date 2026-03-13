<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RegionResource\Pages;
use App\Models\Region;

use Filament\Forms\Form;
use Filament\Forms\Components\{
    Section,
    TextInput,
    Select,
    Grid
};

use Filament\Resources\Resource;

use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\{
    TextColumn,
    BadgeColumn
};

class RegionResource extends Resource
{
    protected static ?string $model = Region::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationLabel = 'Regions';

    protected static ?string $navigationGroup = 'Company Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('Region Information')
                    ->description('Define the company operational region')
                    ->icon('heroicon-o-globe-alt')
                    ->schema([

                        Grid::make(2)
                            ->schema([

                                TextInput::make('name')
                                    ->label('Region Name')
                                    ->placeholder('Example: Head Office')
                                    ->required()
                                    ->maxLength(100),

                                Select::make('type')
                                    ->label('Region Type')
                                    ->options([
                                        'head_office' => 'Head Office',
                                        'branch' => 'Branch Office',
                                        'mine' => 'Mining Site',
                                    ])
                                    ->native(false)
                                    ->required(),

                            ]),

                    ])
                    ->columns(1),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('name')
                    ->label('Region Name')
                    ->icon('heroicon-m-map')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                BadgeColumn::make('type')
                    ->label('Region Type')
                    ->icons([
                        'heroicon-m-building-office-2' => 'head_office',
                        'heroicon-m-building-office' => 'branch',
                        'heroicon-m-map-pin' => 'mine',
                    ])
                    ->colors([
                        'primary' => 'head_office',
                        'success' => 'branch',
                        'warning' => 'mine',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'head_office' => 'Head Office',
                        'branch' => 'Branch',
                        'mine' => 'Mining Site',
                        default => $state,
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])

            ->filters([

                Tables\Filters\SelectFilter::make('type')
                    ->label('Region Type')
                    ->options([
                        'head_office' => 'Head Office',
                        'branch' => 'Branch',
                        'mine' => 'Mining Site',
                    ]),

            ])

            ->actions([
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-m-pencil-square'),
            ])

            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRegions::route('/'),
            'create' => Pages\CreateRegion::route('/create'),
            'edit' => Pages\EditRegion::route('/{record}/edit'),
        ];
    }
}