<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportResource\Pages\ListReports;
use App\Models\Attendance;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ReportResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?string $slug = 'reports';

    public static function getNavigationGroup(): ?string
    {
        return 'Laporan';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                Attendance::query()
                    ->selectRaw('employee_id as id, employee_id, count(*) as total_hadir')
                    ->where('attendance_date', today())
                    ->whereIn('status', ['present', 'late'])
                    ->groupBy('employee_id')
            )
            ->columns([
                TextColumn::make('employee.employee_code')
                    ->label('Kode'),
                TextColumn::make('employee.full_name')
                    ->label('Nama'),
                TextColumn::make('total_hadir')
                    ->label('Hadir'),
            ])
            ->filters([
                Filter::make('date_range')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('start_date')
                            ->label('Tanggal Mulai'),
                        \Filament\Forms\Components\DatePicker::make('end_date')
                            ->label('Tanggal Akhir'),
                    ])
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['start_date'], fn ($q) => $q->where('attendance_date', '>=', $data['start_date']))
                        ->when($data['end_date'], fn ($q) => $q->where('attendance_date', '<=', $data['end_date']))
                    ),
                \Filament\Tables\Filters\SelectFilter::make('employee_id')
                    ->label('Karyawan')
                    ->relationship('employee', 'full_name')
                    ->searchable(),
                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'present' => 'Hadir',
                        'late' => 'Terlambat',
                        'absent' => 'Tidak Hadir',
                    ]),
            ])
            ->paginated(false);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReports::route('/'),
        ];
    }
}
