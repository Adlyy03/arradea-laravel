<?php

namespace App\Exports;

use App\Models\Complaint;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ComplaintsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    private int $days;
    private int $rowNumber = 0;

    public function __construct(int $days)
    {
        $this->days = $days;
    }

    public function collection()
    {
        $startDate = Carbon::now()->subDays($this->days - 1)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        return Complaint::with('user')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Nama User',
            'Role',
            'Subjek',
            'Pesan',
        ];
    }

    public function map($complaint): array
    {
        $roleLabel = $complaint->user->is_seller ? 'Seller' : 'Buyer';

        return [
            ++$this->rowNumber,
            Carbon::parse($complaint->created_at)->format('d/m/Y H:i'),
            $complaint->user->name ?? 'User',
            $roleLabel,
            $complaint->subject,
            $complaint->message,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true],
            ],
        ];
    }
}
