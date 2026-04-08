<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApprovalResource\Pages;
use App\Models\Approval;
use App\Models\Booking;
use App\Models\Vehicle;
use Carbon\Carbon;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Infolists\Components\Section as InfoSection;
use Illuminate\Support\Facades\Auth as FacadesAuth;

class ApprovalResource extends Resource
{
    protected static ?string $model = Approval::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-badge';

    protected static ?string $navigationLabel = 'Approvals';

    protected static ?string $navigationGroup = 'Fleet Operations';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        $userId = Auth::id();

        return Booking::whereHas('approvals', function ($query) use ($userId) {
            $query->where('approver_id', $userId)
                ->where('status', 'pending');
        })->count();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table

            ->recordUrl(false)

            ->columns([

                ImageColumn::make('image')
                    ->size(40)
                    ->label('')
                    ->state(fn($record) => $record->booking->vehicle?->image),

                TextColumn::make('vehicle')
                    ->label('Vehicle')
                    ->searchable(query: function ($query, $search) {
                        $query->whereHas('booking.vehicle', function ($q) use ($search) {
                            $q->where('plate_number', 'like', "%{$search}%")
                                ->orWhere('brand', 'like', "%{$search}%")
                                ->orWhere('model', 'like', "%{$search}%");
                        });
                    })
                    ->weight('bold')
                    ->description(
                        fn($record) =>
                        optional($record->booking->vehicle)->brand . ' ' .
                            optional($record->booking->vehicle)->model
                    )
                    ->state(
                        fn($record) =>
                        optional($record->booking->vehicle)->plate_number
                    ),

                ImageColumn::make('driver_image')
                    ->size(40)
                    ->label('')
                    ->circular()
                    ->state(
                        fn($record) =>
                        $record->booking->driver?->image
                    ),

                TextColumn::make('driver_name')
                    ->label('Driver')
                    ->searchable(query: function ($query, $search) {
                        $query->whereHas('booking.driver', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
                    })
                    ->state(fn($record) => $record->booking->driver?->name ?? "-")
                    ->description(
                        fn($record) => $record->booking->driver?->license_number
                    ),

                TextColumn::make('destination')
                    ->label('Destination')
                    ->searchable(query: function ($query, $search) {
                        $query->whereHas('booking', function ($q) use ($search) {
                            $q->where('destination', 'like', "%{$search}%");
                        });
                    })
                    ->icon('heroicon-m-map-pin')
                    ->weight('medium')
                    ->state(fn($record) => $record->booking?->destination ?? '-')
                    ->limit(30),

                TextColumn::make('purpose')
                    ->label('Purpose')
                    ->color('gray')
                    ->size('sm')
                    ->state(fn($record) => $record->booking?->purpose ?? '-')
                    ->limit(40)
                    ->tooltip(fn($state) => $state),

                TextColumn::make('schedule')
                    ->label('Schedule')
                    ->searchable(query: function ($query, $search) {
                        $query->whereHas('booking', function ($q) use ($search) {
                            $q->where('start_date', 'like', "%{$search}%")
                                ->orWhere('end_date', 'like', "%{$search}%");
                        });
                    })
                    ->icon('heroicon-m-calendar-date-range')
                    ->state(
                        fn($record) =>
                        Carbon::parse($record->booking->start_date)->format('d M H:i')
                            . ' → ' .
                            Carbon::parse($record->booking->end_date)->format('d M H:i')
                    )
                    ->color('gray'),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn($state) => ucfirst($state))
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger'  => 'rejected',
                    ]),

                TextColumn::make('level')
                    ->label('Level')
                    ->searchable()
                    ->badge()
                    ->color(fn($state) => $state == 1 ? 'info' : 'warning'),

                TextColumn::make('notes')
                    ->label('Notes')
                    ->limit(30)
                    ->tooltip(fn($state) => $state)
                    ->color('gray')

            ])

            ->defaultSort('status', 'asc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->default(null), // penting biar gak override query awal
            ])

            ->actions([

                Tables\Actions\Action::make('view')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->label('View')
                    ->icon('heroicon-m-eye')
                    ->color('gray')
                    ->modalHeading('Approval Detail')
                    ->modalWidth('4xl')
                    ->infolist([

                        InfoSection::make('Booking Information')
                            ->icon('heroicon-o-truck')
                            ->schema([

                                // 🚗 VEHICLE
                                ImageEntry::make('vehicle_image')
                                    ->label('Vehicle')
                                    ->inlineLabel()
                                    ->state(fn($record) => $record->booking->vehicle?->image),

                                TextEntry::make('vehicle')
                                    ->label('')
                                    ->inlineLabel()
                                    ->formatStateUsing(function ($record) {

                                        $vehicle = $record->vehicle;

                                        if (!$vehicle) return '-';

                                        return "
                        <div>
                            <div style='font-weight:600'>
                                {$vehicle->plate_number}
                            </div>
                            <div style='font-size:12px; color:gray'>
                                {$vehicle->brand} {$vehicle->model}
                            </div>
                        </div>
                    ";
                                    })
                                    ->html(),

                                // 👨 DRIVER
                                ImageEntry::make('driver_image')
                                    ->label('Driver')
                                    ->inlineLabel()
                                    ->circular()
                                    ->state(fn($record) => $record->booking->driver?->image),

                                TextEntry::make('driver')
                                    ->label('')
                                    ->inlineLabel()
                                    ->formatStateUsing(function ($record) {
                                        $driver = $record->driver;

                                        if (!$driver) return '-';

                                        return "
                        <div>
                            <div style='font-weight:600'>
                                {$driver->name}
                            </div>
                            <div style='font-size:12px; color:gray'>
                                {$driver->phone}
                            </div>
                        </div>
                    ";
                                    })
                                    ->html(),

                                TextEntry::make('destination')
                                    ->label('Destination')
                                    ->inlineLabel()
                                    ->badge()
                                    ->icon('heroicon-m-map-pin')
                                    ->state(fn($record) => $record->booking->destination),

                                TextEntry::make('purpose')
                                    ->label('Purpose')
                                    ->inlineLabel()
                                    ->badge()
                                    ->columnSpanFull()
                                    ->color('gray')
                                    ->state(fn($record) => $record->booking->purpose),

                                TextEntry::make('schedule')
                                    ->label('Schedule')
                                    ->inlineLabel()
                                    ->badge()
                                    ->icon('heroicon-m-calendar-date-range')
                                    ->state(
                                        fn($record) =>
                                        \Carbon\Carbon::parse($record->booking->start_date)->format('d M H:i')
                                            . ' → ' .
                                            \Carbon\Carbon::parse($record->booking->end_date)->format('d M H:i')
                                    ),

                                TextEntry::make('booking_status')
                                    ->label('Booking Status')
                                    ->inlineLabel()
                                    ->badge()
                                    ->state(fn($record) => ucfirst($record->booking->status))
                                    ->color(fn($record) => match ($record->booking->status) {
                                        'pending' => 'warning',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        default => 'gray',
                                    }),

                            ])
                            ->columns(2),

                        // 🔥 KHAS APPROVAL
                        InfoSection::make('Approval Information')
                            ->icon('heroicon-o-check-badge')
                            ->schema([

                                TextEntry::make('level')
                                    ->label('Approval Level')
                                    ->badge()
                                    ->inlineLabel()
                                    ->color(fn($state) => $state == 1 ? 'info' : 'warning'),

                                TextEntry::make('status')
                                    ->label('Approval Status')
                                    ->inlineLabel()
                                    ->badge()
                                    ->formatStateUsing(fn($state) => ucfirst($state))
                                    ->color(fn($state) => match ($state) {
                                        'pending' => 'warning',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                    }),

                                TextEntry::make('notes')
                                    ->label('Notes')
                                    ->badge()
                                    ->inlineLabel()
                                    ->columnSpanFull()
                                    ->color('gray')
                                    ->state(fn($record) => $record->notes ?? '-'),

                                TextEntry::make('approved_at')
                                    ->label('Approved At')
                                    ->inlineLabel()
                                    ->dateTime('d M Y H:i')
                                    ->placeholder('-'),

                            ])
                            ->columns(2),
                    ]),

                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-m-x-circle')
                    ->color('danger')
                    ->button()
                    ->size('lg')
                    ->visible(fn($record) => $record->status === 'pending')

                    ->requiresConfirmation()
                    ->modalHeading('Reject Booking Request')
                    ->modalDescription('Please provide a reason for rejecting this booking request.')
                    ->modalSubmitActionLabel('Reject Booking')

                    ->form([
                        Forms\Components\Textarea::make('notes')
                            ->label('Rejection Reason')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function ($record, $data) {

                        // update current (level 1)
                        $record->update([
                            'status' => 'rejected',
                            'approved_at' => now(),
                            'notes' => $data['notes'] ?? null,
                        ]);

                        // 🔥 kalau level 1 reject → paksa level 2 ikut reject
                        if ($record->level == 1) {

                            Approval::where('booking_id', $record->booking_id)
                                ->where('level', 2)
                                ->update([
                                    'status' => 'rejected',
                                    'approved_at' => now(),
                                    'notes' => 'Auto rejected (level 1 rejected)',
                                ]);

                            // juga update booking
                            $record->booking()->update([
                                'status' => 'rejected'
                            ]);
                        }

                        // existing logic level 2
                        if ($record->level == 2) {
                            $record->booking()->update([
                                'status' => 'rejected'
                            ]);
                        }

                        activity('approval')
                            ->causedBy(Auth::user())
                            ->performedOn($record)
                            ->event('rejected')
                            ->withProperties([
                                'booking_id' => $record->booking_id,
                                'approval_id' => $record->id,
                            ])
                            ->log('Approval rejected');
                    }),

                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->button()
                    ->size('lg')
                    ->visible(fn($record) => $record->status === 'pending')

                    ->requiresConfirmation()
                    ->modalHeading('Approve Booking Request')
                    ->modalDescription('Are you sure you want to approve this booking request?')
                    ->modalSubmitActionLabel('Approve Booking')

                    ->form([
                        Forms\Components\Textarea::make('notes')
                            ->label('Approval Notes')
                            ->placeholder('Optional notes...')
                            ->rows(3),
                    ])

                    ->action(function ($record, $data) {

                        $record->update([
                            'status' => 'approved',
                            'approved_at' => now(),
                            'notes' => $data['notes'] ?? null,
                        ]);

                        if ($record->level == 2) {
                            $record->booking()->update([
                                'status' => 'approved'
                            ]);

                            $record->booking->vehicle->update([
                                'status' => 'in_use',
                            ]);

                            if ($record->booking->driver) {
                                $record->booking->driver()->update([
                                    'status' => 'assigned',
                                ]);
                            }
                        }

                        activity('approval')
                            ->causedBy(Auth::user())
                            ->performedOn($record)
                            ->event('approved')
                            ->withProperties([
                                'booking_id' => $record->booking_id,
                                'approval_id' => $record->id,
                                'notes' => $data['notes'] ?? null,
                            ])
                            ->log('Approval approved');
                    })

            ])

            ->bulkActions([])
            ->paginated(false);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getEloquentQuery(): Builder
    {
        $userId = Auth::id();

        return parent::getEloquentQuery()

            ->with([
                'booking.vehicle',
                'booking.driver'
            ])

            ->where('approver_id', $userId)
            ->where(function ($query) {

                $query->where('level', 1)

                    ->orWhere(function ($q) {

                        $q->where('level', 2)
                            ->whereExists(function ($sub) {

                                $sub->selectRaw(1)
                                    ->from('approvals as a1')
                                    ->whereColumn('a1.booking_id', 'approvals.booking_id')
                                    ->where('a1.level', 1)
                                    ->where('a1.status', 'approved');
                            });
                    });
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApprovals::route('/'),
        ];
    }
}
