<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;

class KeuanganExport implements FromView, WithTitle, WithEvents, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('exports.keuangan_excel', $this->data);
    }

    public function title(): string
    {
        return 'Laporan Keuangan';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
            'A1:H1' => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F81BD']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true]
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Auto-size columns
                foreach (range('A', 'H') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
                
                // Set orientation to landscape
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                
                // Add summary section if included
                if ($this->data['includeSummary']) {
                    $lastRow = $sheet->getHighestRow();
                    $summaryStartRow = $lastRow + 3;
                    
                    // Summary title
                    $sheet->mergeCells('A' . $summaryStartRow . ':H' . $summaryStartRow);
                    $sheet->setCellValue('A' . $summaryStartRow, 'RINGKASAN LAPORAN');
                    $sheet->getStyle('A' . $summaryStartRow)->applyFromArray([
                        'font' => ['bold' => true, 'size' => 14],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['rgb' => '92D050']
                        ],
                        'alignment' => ['horizontal' => 'center']
                    ]);
                    
                    // Summary data - use helper function if exists, otherwise format manually
                    $formatRupiah = function($amount) {
                        if (function_exists('formatRupiah')) {
                            return formatRupiah($amount);
                        }
                        return 'Rp ' . number_format($amount, 0, ',', '.');
                    };
                    
                    $summaryData = [
                        ['Total Pendapatan', '', '', '', '', $formatRupiah($this->data['totalIncome']), '', ''],
                        ['Booking Selesai', '', '', '', '', $this->data['completedBookings'], '', ''],
                        ['Rata-rata Transaksi', '', '', '', '', $formatRupiah($this->data['avgTransaction']), '', ''],
                    ];
                    
                    $row = $summaryStartRow + 1;
                    foreach ($summaryData as $dataRow) {
                        $sheet->fromArray($dataRow, null, 'A' . $row);
                        $row++;
                    }
                }
            },
        ];
    }
}