<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseTableWidget;

class LatestAttendanceWidget extends BaseTableWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Attendance::query()
                    ->with('employee')
                    ->latest('attendance_date')
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('employee.full_name')
                    ->label('Karyawan')
                    ->searchable(),
                TextColumn::make('attendance_date')
                    ->label('Tanggal')
                    ->date('d M Y'),
                TextColumn::make('checkin_time')
                    ->label('Check-In')
                    ->dateTime('H:i:s'),
                TextColumn::make('checkout_time')
                    ->label('Check-Out')
                    ->dateTime('H:i:s'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'present' => 'success',
                        'late' => 'warning',
                        'absent' => 'danger',
                        default => 'gray',
                    }),
            ]);
    }
}
