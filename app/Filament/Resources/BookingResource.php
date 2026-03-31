<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Models\Booking;
use App\Models\User;
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
use Carbon\Carbon;
use Filament\Infolists\Components\ImageEntry;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\Auth;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Support\HtmlString;

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
                            ->relationship(
                                'vehicle',
                                'plate_number',
                                fn($query) => $query->where('status', 'available')
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->placeholder('Select a vehicle')

                            ->getOptionLabelFromRecordUsing(function ($record) {
                                return new HtmlString("
                                        <div style='display:flex; align-items:center; gap:12px'>
                                            <img src='" . asset('storage/' . ($record->image ?? 'default-car.png')) . "' 
                                                onerror=\"this.src='" . asset('images/default-car.png') . "'\"
                                                style='width:50px; height:50px; object-fit:cover; border-radius:8px;'>
                                            <div>
                                                <div style='font-weight:600'>
                                                    {$record->plate_number}
                                                </div>
                                                <div style='font-size:12px; color:gray'>
                                                    {$record->brand} - {$record->model}
                                                </div>
                                            </div>
                                        </div>
                                        ");
                            })

                            ->getSearchResultsUsing(function (string $search) {
                                return \App\Models\Vehicle::query()
                                    ->where('status', 'available') // ⬅️ penting
                                    ->where(function ($query) use ($search) {
                                        $query
                                            ->where('plate_number', 'like', "%{$search}%")
                                            ->orWhere('brand', 'like', "%{$search}%");
                                    })
                                    ->limit(10)
                                    ->get()
                                    ->mapWithKeys(function ($record) {
                                        return [
                                            $record->id => new HtmlString("
                    <div style='display:flex; align-items:center; gap:12px'>
                        <img src='" . asset('storage/' . ($record->image ?? 'default-car.png')) . "' 
                            onerror=\"this.src='" . asset('images/default-car.png') . "'\"
                            style='width:50px; height:50px; object-fit:cover; border-radius:8px;'>
                        <div>
                            <div style='font-weight:600'>
                                {$record->plate_number}
                            </div>
                            <div style='font-size:12px; color:gray'>
                                {$record->brand} - {$record->model}
                            </div>
                        </div>
                    </div>
                ")
                                        ];
                                    })
                                    ->toArray();
                            })
                            ->allowHtml()
                            ->helperText('Choose a vehicle'),

                        Select::make('driver_id')
                            ->label('Driver')
                            ->relationship(
                                'driver',
                                'name',
                                fn($query) => $query
                                    ->where('status', 'available')
                                    ->where('active', 1)
                            )
                            ->searchable()
                            ->preload()
                            ->placeholder('Assign driver')
                            ->helperText('Can be null'),

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
            ->striped()
            ->columns([

                ImageColumn::make('image')
                    ->size(40)
                    ->label('')
                    ->state(fn($record) => $record->vehicle?->image),

                TextColumn::make('vehicle')
                    ->label('Vehicle')
                    ->weight('bold')
                    ->searchable(query: function ($query, $search) {
                        $query->whereHas('vehicle', function ($q) use ($search) {
                            $q->where('plate_number', 'like', "%{$search}%")
                                ->orWhere('brand', 'like', "%{$search}%")
                                ->orWhere('model', 'like', "%{$search}%");
                        });
                    })
                    ->description(
                        fn($record) =>
                        optional($record->vehicle)->brand . ' ' .
                            optional($record->vehicle)->model
                    )
                    ->state(
                        fn($record) =>
                        optional($record->vehicle)->plate_number
                    ),

                ImageColumn::make('driver_image')
                    ->size(40)
                    ->label('')
                    ->circular()
                    ->state(
                        fn($record) =>
                        $record->driver?->image
                    ),

                TextColumn::make('driver_name')
                    ->label('Driver')
                    ->searchable(query: function ($query, $search) {
                        $query->whereHas('driver', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
                    })
                    ->state(fn($record) => $record->driver?->name ?? "-")
                    ->description(
                        fn($record) => $record->driver?->license_number
                    ),

                TextColumn::make('destination')
                    ->label('Destination')
                    ->icon('heroicon-m-map-pin')
                    ->limit(40)
                    ->tooltip(fn($record) => $record->destination)
                    ->searchable(),

                TextColumn::make('schedule')
                    ->label('Schedule')
                    ->icon('heroicon-m-calendar-date-range')
                    ->state(
                        fn($record) =>
                        Carbon::parse($record->start_date)->format('d M H:i')
                            . ' → ' .
                            Carbon::parse($record->end_date)->format('d M H:i')
                    )
                    ->color('gray'),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->icons([
                        'heroicon-m-clock' => 'pending',
                        'heroicon-m-check-circle' => 'approved',
                        'heroicon-m-x-circle' => 'rejected',
                        'heroicon-m-check-badge' => 'completed'
                    ])
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn($state) => ucfirst($state))
                    ->sortable(),

                TextColumn::make('note_1')
                    ->label('Note Approver 1')
                    ->color('gray')
                    ->limit(40)
                    ->state(fn($record) => optional($record->approvals->where('level', 1)->first())->notes ?? '-'),

                TextColumn::make('note_2')
                    ->label('Note Approver 2')
                    ->color('gray')
                    ->limit(40)
                    ->state(fn($record) => optional($record->approvals->where('level', 2)->first())->notes ?? '-'),

            ])

            ->filters([

                SelectFilter::make('status')
                    ->label('Booking Status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->indicator('Status'),

            ])

            ->actions([
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-m-eye')
                    ->color('gray')
                    ->label('View')
                    ->tooltip('View Booking'),

                Tables\Actions\Action::make('cancel')
                    ->label('Cancel Booking')
                    ->icon('heroicon-m-x-circle')
                    ->color('danger')
                    ->visible(fn($record) => $record->status === 'pending')

                    ->requiresConfirmation()
                    ->modalHeading('Cancel Booking Request')
                    ->modalDescription('Are you sure you want to cancel this booking?')
                    ->modalSubmitActionLabel('Cancel Booking')

                    ->action(function ($record) {

                        // 🔥 update semua approval dalam booking ini
                        \App\Models\Approval::where('booking_id', $record->id)
                            ->update([
                                'status' => 'canceled',
                                'approved_at' => now(),
                                'notes' => 'Canceled by requester',
                            ]);

                        // 🔥 update booking
                        $record->update([
                            'status' => 'canceled',
                        ]);

                        // 🔥 set vehicle jadi available
                        $record->vehicle()->update([
                            'status' => 'available',
                        ]);

                        // 🔥 set driver jadi available (kalau ada)
                        if ($record->driver) {
                            $record->driver()->update([
                                'status' => 'available',
                            ]);
                        }
                    }),
            ])

            ->bulkActions([])

            ->paginated(false)

            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(function ($query) {
                $query
                    ->orderByRaw("
            CASE 
                WHEN status = 'pending' THEN 0
                ELSE 1
            END
        ")
                    ->orderByDesc('created_at'); // paling baru di atas
            });
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
