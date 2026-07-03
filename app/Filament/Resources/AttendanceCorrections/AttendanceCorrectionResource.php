<?php

namespace App\Filament\Resources\AttendanceCorrections;

use App\Filament\Resources\AttendanceCorrections\Pages\ManageAttendanceCorrections;
use App\Models\AttendanceCorrection;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AttendanceCorrectionResource extends Resource
{
    protected static ?string $model = AttendanceCorrection::class;

    protected static ?string $slug = 'attendance-corrections';

    protected static ?string $recordTitleAttribute = 'id';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $navigationLabel = 'Koreksi Absensi';

    protected static ?string $modelLabel = 'Koreksi Absensi';

    protected static ?string $pluralModelLabel = 'Koreksi Absensi';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('employee_id')
                    ->label('Karyawan')
                    ->relationship('employee', 'full_name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('attendance_id')
                    ->label('Absensi')
                    ->relationship('attendance', 'id')
                    ->required()
                    ->searchable(),
                Textarea::make('reason')
                    ->label('Alasan')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.full_name')
                    ->label('Karyawan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('reason')
                    ->label('Alasan')
                    ->limit(50),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pending',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        default => $state,
                    }),
                TextColumn::make('reviewer.name')
                    ->label('Direview Oleh')
                    ->sortable(),
                TextColumn::make('reviewed_at')
                    ->label('Tanggal Review')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ]),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    Action::make('approve')
                        ->label('Setujui')
                        ->icon(Heroicon::OutlinedCheckCircle)
                        ->color('success')
                        ->action(fn (AttendanceCorrection $record) => $record->update([
                            'status' => 'approved',
                            'reviewed_by' => auth()->id(),
                            'reviewed_at' => now(),
                        ]))
                        ->visible(fn (AttendanceCorrection $record): bool => $record->status === 'pending'),
                    Action::make('reject')
                        ->label('Tolak')
                        ->icon(Heroicon::OutlinedXCircle)
                        ->color('danger')
                        ->form([
                            TextInput::make('rejection_reason')
                                ->label('Alasan Penolakan')
                                ->required(),
                        ])
                        ->action(fn (AttendanceCorrection $record, array $data) => $record->update([
                            'status' => 'rejected',
                            'reviewed_by' => auth()->id(),
                            'reviewed_at' => now(),
                            'rejection_reason' => $data['rejection_reason'],
                        ]))
                        ->visible(fn (AttendanceCorrection $record): bool => $record->status === 'pending'),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAttendanceCorrections::route('/'),
        ];
    }
}
