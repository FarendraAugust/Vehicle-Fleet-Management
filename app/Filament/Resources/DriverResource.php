<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DriverResource\Pages;
use App\Filament\Resources\DriverResource\RelationManagers;
use App\Models\Driver;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DriverResource extends Resource
{
    protected static ?string $model = Driver::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Drivers';

    protected static ?string $navigationGroup = 'Fleet Management';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Driver Information')
                    ->description('Basic information about the driver')
                    ->schema([

                        FileUpload::make('image')
                            ->label('Driver Photo')
                            ->image()
                            ->disk('public')
                            ->directory('drivers')
                            ->visibility('public')
                            ->imagePreviewHeight('200')
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('name')
                            ->label('Driver Name')
                            ->placeholder('Bambang')
                            ->required()
                            ->maxLength(100),

                        TextInput::make('phone')
                            ->label('Phone Number')
                            ->tel()
                            ->placeholder('08123456789')
                            ->maxLength(20),

                        TextInput::make('license_number')
                            ->label('License Number')
                            ->placeholder('81926457826183')
                            ->required()
                            ->maxLength(50)
                            ->columnSpanFull(),

                        Select::make('status')
                            ->label('Driver Status')
                            ->options([
                                'available' => 'Available',
                                'assigned' => 'Assigned',
                                'inactive' => 'Inactive',
                            ])
                            ->default('available')
                            ->required(),

                        Toggle::make('active')
                            ->label('Active Driver')
                            ->inline(false)
                            ->default(true),

                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->contentGrid([
                'sm' => 1,
                'md' => 2,
                'lg' => 3,
                'xl' => 4,
                '2xl' => 5,
            ])

            ->columns([
                Stack::make([

                    Stack::make([

                        ImageColumn::make('image')
                            ->disk('public')
                            ->circular()
                            ->size(120)
                            ->alignCenter(),

                        TextColumn::make('name')
                            ->weight('bold')
                            ->size(TextColumn\TextColumnSize::Large)
                            ->alignCenter()
                            ->searchable(),

                        TextColumn::make('phone')
                            ->icon('heroicon-m-phone')
                            ->alignCenter(),

                        BadgeColumn::make('status')
                            ->colors([
                                'success' => 'available',
                                'warning' => ['assigned', 'inactive'],
                            ])
                            ->alignCenter(),

                        ToggleColumn::make('active')
                            ->label('Active')
                            ->alignCenter(),

                    ])
                        ->space(5)

                        ->extraAttributes(fn($record) => [
                            'width' => 200,
                            'class' => '
                p-12
                hover:shadow-xl
                hover:-translate-y-1
                transition
                min-h-[320px]
                gap-2
            '
                                .
                                match ($record->status) {
                                    'available' => ' border-green-500',
                                    default => ' border-yellow-400',
                                }
                        ])

                ])
            ])

            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'available' => 'Available',
                        'assigned' => 'Assigned',
                        'inactive' => 'Inactive',
                    ]),
            ])

            ->searchable()

            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])

            ->recordAction('view')

            ->paginated(false);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDrivers::route('/'),
            // 'index' => Pages\DriverGrid::route('/'),
            'create' => Pages\CreateDriver::route('/create'),
            'edit' => Pages\EditDriver::route('/{record}/edit'),
        ];
    }
}
