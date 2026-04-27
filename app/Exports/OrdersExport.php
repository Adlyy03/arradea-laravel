<?php

namespace App\Exports;

use App\Models\Store;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrdersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithColumnFormatting
{
    private Store $store;

    private int $rowNumber = 0;

    public function __construct(Store $store)
    {
        $this->store = $store;
    }

    public function collection()
    {
        return $this->store->orders()->with(['user', 'product'])->latest()->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'ID Pesanan',
            'Tanggal',
            'Nama Pembeli',
            'Produk',
            'Qty',
            'Harga Satuan (Rp)',
            'Total Harga (Rp)',
            'Status',
        ];
    }

    public function map($order): array
    {
        $statusLabel = [
            'pending' => 'Menunggu',
            'accepted' => 'Diproses',
            'done' => 'Selesai',
            'rejected' => 'Ditolak',
            'dibatalkan' => 'Dibatalkan',
        ][$order->status] ?? $order->status;

        $qty = (int) ($order->quantity ?? 1);
        $unitPrice = (float) ($order->unit_price_final ?? ($order->total_price / max($qty, 1)));

        return [
            ++$this->rowNumber,
            'ARRD-' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT),
            Carbon::parse($order->created_at)->format('d/m/Y H:i'),
            $order->user->name ?? 'Pembeli',
            $order->product->name ?? 'Produk Dihapus',
            $qty,
            $unitPrice,
            (float) $order->total_price,
            $statusLabel,
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

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_NUMBER,
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }
}
