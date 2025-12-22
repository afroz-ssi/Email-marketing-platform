<?php

namespace App\Exports;

use App\Models\ServiceBooking;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BookingExport implements FromView, ShouldAutoSize, WithStyles
{
    public function view(): View
    {
        // $parents = User::role('LEADER')->latest()->get()->toArray();
        $bookings = ServiceBooking::with(['user','service.user', 'slot', 'service.assignedTo', 'service', 'service_session'])->latest()->get()->toArray();
        
        return view('exports.booking', [
            'bookings' => $bookings
        ]);
    }

    public function styles(Worksheet $sheet)
    {
    
        return [
            'A:Z' => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ]
            ],
        ];
    }
}
