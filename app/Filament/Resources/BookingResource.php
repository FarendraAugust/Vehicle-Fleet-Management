<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Filament\Resources\BookingResource\RelationManagers;
use App\Models\Booking;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Carbon\Carbon;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\Auth;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section as InfoSection;

use function Symfony\Component\Clock\now;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Bookings';

    protected static ?string $navigationGroup = 'Fleet Operations';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationBadgeTooltip = 'Pending bookings';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('Booking Information')
                    ->description('Fill in the booking details for the vehicle request.')
                    ->icon('heroicon-o-truck')
                    ->schema([

                        Select::make('vehicle_id')
                            ->label('Vehicle')
                            ->relationship('vehicle', 'plate_number')
                            ->searchable()
                            ->preload()
                            ->placeholder('Select a vehicle')
                            ->required(),

                        Select::make('driver_id')
                            ->label('Driver')
                            ->relationship('driver', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Assign driver')
                            ->required(),

                        Hidden::make('requested_by')
                            ->default(fn() => Auth::id()),

                        TextInput::make('destination')
                            ->label('Destination')
                            ->placeholder('Example: Mining Site A')
                            ->required()
                            ->columnSpanFull(),

                        Textarea::make('purpose')
                            ->label('Purpose')
                            ->placeholder('Describe the purpose of this vehicle request')
                            ->rows(3)
                            ->required()
                            ->columnSpanFull(),

                        DateTimePicker::make('start_date')
                            ->label('Start Date')
                            ->required()
                            ->minDate(now()->format('Y-m-d H:i'))
                            ->seconds(false)
                            ->displayFormat('Y-m-d H:i')
                            ->helperText('Select when the vehicle will start being used.')
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                $end = $get('end_date');

                                if ($end && Carbon::parse($end)->lessThan(Carbon::parse($state))) {
                                    $set('end_date', null);
                                }
                            }),

                        DateTimePicker::make('end_date')
                            ->label('End Date')
                            ->required()
                            ->seconds(false)
                            ->displayFormat('Y-m-d H:i')
                            ->helperText('End date must be after start date.')
                            ->reactive()
                            ->disabled(fn($get) => blank($get('start_date')))
                            ->minDate(fn($get) => $get('start_date') ?? now()->format('Y-m-d H:i'))
                            ->afterStateUpdated(function ($state, $set, $get) {
                                $start = $get('start_date');

                                if ($start && $state && Carbon::parse($state)->lessThan(Carbon::parse($start))) {
                                    $set('end_date', null);
                                }
                            })
                            ->rule('after:start_date'),

                        Hidden::make('status')
                            ->default('pending'),

                    ])
                    ->columns(2),

                Section::make('Approval Configuration')
                    ->description('Select the approvers responsible for reviewing this booking.')
                    ->icon('heroicon-o-check-badge')
                    ->schema([

                        Select::make('approver_level_1')
                            ->label('Approver Level 1')
                            ->options(User::pluck('name', 'id'))
                            ->searchable()
                            ->placeholder('Select first approver')
                            ->required()
                            ->dehydrated(false),

                        Select::make('approver_level_2')
                            ->label('Approver Level 2')
                            ->options(User::pluck('name', 'id'))
                            ->searchable()
                            ->placeholder('Select second approver')
                            ->required()
                            ->dehydrated(false),

                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->columns([

                TextColumn::make('vehicle.plate_number')
                    ->label('Vehicle')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('driver.name')
                    ->label('Driver')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('destination')
                    ->label('Destination')
                    ->searchable()
                    ->limit(30),

                TextColumn::make('start_date')
                    ->label('Start Date')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label('End Date')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->sortable(),

            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([])
            ->defaultSort('start_date', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([

                InfoSection::make('Booking Information')
                    ->schema([

                        TextEntry::make('vehicle.plate_number')
                            ->label('Vehicle'),

                        TextEntry::make('driver.name')
                            ->label('Driver'),

                        TextEntry::make('destination')
                            ->label('Destination'),

                        TextEntry::make('purpose')
                            ->label('Purpose')
                            ->columnSpanFull(),

                        TextEntry::make('start_date')
                            ->label('Start Date')
                            ->dateTime('d M Y H:i'),

                        TextEntry::make('end_date')
                            ->label('End Date')
                            ->dateTime('d M Y H:i'),

                        TextEntry::make('status')
                            ->badge()
                            ->color(fn($state) => match ($state) {
                                'pending' => 'warning',
                                'approved' => 'success',
                                'rejected' => 'danger',
                            })

                    ])
                    ->columns(2),

                InfoSection::make('Request Information')
                    ->schema([

                        TextEntry::make('requester.name')
                            ->label('Requested By'),

                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime('d M Y H:i'),

                    ])
                    ->columns(2),

            ]);
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
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'view' => Pages\ViewBooking::route('/view/{record}'),
        ];
    }
}
