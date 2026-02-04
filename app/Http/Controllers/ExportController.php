<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Booking;
use App\Models\Lapangan;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KeuanganExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportController extends Controller
{
    public function exportKeuangan(Request $request)
    {
        $request->validate([
            'format' => 'required|in:excel,pdf,csv',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'lapangan_id' => 'nullable|exists:lapangans,id',
            'include_summary' => 'boolean',
            'include_transactions' => 'boolean',
            'include_charts' => 'boolean',
        ]);

        $format = $request->format;
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $lapanganId = $request->lapangan_id;
        $includeSummary = $request->boolean('include_summary', true);
        $includeTransactions = $request->boolean('include_transactions', true);
        $includeCharts = $request->boolean('include_charts', false);

        // Get payments data
        $payments = Payment::where('status', 'verified')
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereHas('booking', function ($qb) use ($startDate, $endDate) {
                    $qb->whereBetween('tanggal', [$startDate, $endDate]);
                });
            })
            ->when($lapanganId, function ($q) use ($lapanganId) {
                $q->whereHas('booking', function ($qb) use ($lapanganId) {
                    $qb->where('lapangan_id', $lapanganId);
                });
            })
            ->with(['booking.lapangan', 'booking.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate totals
        $totalIncome = $payments->sum('jumlah');
        $completedBookings = $payments->where('booking.status', 'completed')->count();
        $avgTransaction = $payments->count() > 0 ? $totalIncome / $payments->count() : 0;

        // Group by lapangan
        $byLapangan = $payments->groupBy(function ($p) {
            return $p->booking->lapangan->nama ?? 'Unknown';
        })->map(function ($group) {
            return $group->sum('jumlah');
        });

        // Prepare data for export
        $exportData = [
            'payments' => $payments,
            'totalIncome' => $totalIncome,
            'completedBookings' => $completedBookings,
            'avgTransaction' => $avgTransaction,
            'byLapangan' => $byLapangan,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'lapanganId' => $lapanganId,
            'includeSummary' => $includeSummary,
            'includeTransactions' => $includeTransactions,
            'includeCharts' => $includeCharts,
        ];

        try {
            switch ($format) {
                case 'excel':
                    return $this->exportToExcel($exportData);
                case 'pdf':
                    return $this->exportToPDF($exportData);
                case 'csv':
                    return $this->exportToCSV($exportData);
                default:
                    return response()->json(['error' => 'Format tidak didukung'], 400);
            }
        } catch (\Exception $e) {
            \Log::error('Export error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal mengekspor data: ' . $e->getMessage()], 500);
        }
    }

    private function exportToExcel($data)
    {
        $fileName = 'laporan_keuangan_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        return Excel::download(new KeuanganExport($data), $fileName);
    }

    private function exportToPDF($data)
    {
        $fileName = 'laporan_keuangan_' . date('Y-m-d_H-i-s') . '.pdf';
        
        $pdf = PDF::loadView('exports.keuangan_pdf', $data)
            ->setPaper('a4', 'landscape')
            ->setOption('margin-top', 20)
            ->setOption('margin-bottom', 20);
        
        return $pdf->download($fileName);
    }

    private function exportToCSV($data)
    {
        $fileName = 'laporan_keuangan_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for proper Excel compatibility
            fwrite($file, "\xEF\xBB\xBF");
            
            // Header
            fputcsv($file, [
                'Tanggal',
                'Kode Booking',
                'Customer',
                'Lapangan',
                'Jam',
                'Total',
                'Status',
                'Payment'
            ]);
            
            // Data
            foreach ($data['payments'] as $payment) {
                fputcsv($file, [
                    $payment->created_at->format('Y-m-d'),
                    'BK' . str_pad($payment->booking_id, 3, '0', STR_PAD_LEFT),
                    $payment->booking->user->name ?? 'Unknown',
                    $payment->booking->lapangan->nama ?? 'Unknown',
                    $payment->booking->jam_mulai->format('H:i') . '-' . $payment->booking->jam_selesai->format('H:i'),
                    number_format($payment->jumlah, 0, ',', '.'),
                    $payment->booking->status,
                    ucfirst($payment->status)
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
