<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApprovalResource\Pages;
use App\Models\Approval;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ApprovalResource extends Resource
{
    protected static ?string $model = Approval::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-badge';

    protected static ?string $navigationLabel = 'Approvals';

    protected static ?string $navigationGroup = 'Fleet Management';

    protected static ?int $navigationSort = 2;

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
            ->columns([
                Tables\Columns\Layout\Stack::make([

                    Tables\Columns\TextColumn::make('vehicle')
                        ->label('Vehicle')
                        ->icon('heroicon-m-truck')
                        ->badge()
                        ->color('info')
                        ->state(
                            fn($record) =>
                            optional($record->booking->vehicle)->plate_number .
                                ' - ' .
                                optional($record->booking->vehicle)->brand .
                                ' ' .
                                optional($record->booking->vehicle)->model
                        ),

                    Tables\Columns\TextColumn::make('driver')
                        ->label('Driver')
                        ->icon('heroicon-m-user')
                        ->badge()
                        ->color('success')
                        ->state(
                            fn($record) =>
                            optional($record->booking->driver)->name ?? 'No Driver'
                        ),

                    Tables\Columns\TextColumn::make('booking.destination')
                        ->label('Destination')
                        ->icon('heroicon-m-map-pin')
                        ->weight('bold'),

                    Tables\Columns\TextColumn::make('booking.purpose')
                        ->label('Purpose')
                        ->limit(60)
                        ->color('gray'),

                    Tables\Columns\TextColumn::make('booking.start_date')
                        ->label('Start')
                        ->dateTime('d M Y H:i')
                        ->icon('heroicon-m-calendar'),

                    Tables\Columns\TextColumn::make('booking.end_date')
                        ->label('End')
                        ->dateTime('d M Y H:i')
                        ->icon('heroicon-m-calendar-days'),

                    Tables\Columns\TextColumn::make('level')
                        ->label('Approval Level')
                        ->badge()
                        ->color(fn($state) => $state == 1 ? 'info' : 'warning'),

                    Tables\Columns\BadgeColumn::make('status')
                        ->colors([
                            'warning' => 'pending',
                            'success' => 'approved',
                            'danger'  => 'rejected',
                        ]),

                    Tables\Columns\TextColumn::make('notes')
                        ->label('Notes')
                        ->limit(50)
                        ->color('gray'),

                ])
                    ->space(3)
                    ->extraAttributes([
                        'class' => '
                w-full
                p-6
                rounded-xl
                border
                bg-white dark:bg-gray-900
                shadow-sm
                hover:shadow-xl
                transition
            ',
                    ]),
            ])


            ->actions([

                Tables\Actions\ViewAction::make()
                    ->label('View Details')
                    ->icon('heroicon-m-eye')
                    ->color('gray')
                    ->button()
                    ->size('lg'),

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
                        $record->update([
                            'status' => 'rejected',
                            'approved_at' => now(),
                            'notes' => $data['notes'],
                        ]);
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
                        }
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
            'view' => Pages\ViewApproval::route('/{record}'),
        ];
    }
}
