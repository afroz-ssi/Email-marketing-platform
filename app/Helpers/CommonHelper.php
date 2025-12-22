<?php

namespace App\Helpers;

use App\Models\ServiceBooking;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CommonHelper
{

   
    public static function getMonthlyData($year, $month, $isCancelled)
    {
        $rawData = ServiceBooking::selectRaw("
            DATE_FORMAT(date, '%b') as month,
            MONTH(date) as month_index,
            SUM(total_price) as amount")
            ->whereYear('date', $year)
            ->whereMonth('date', '<=', $month)
            ->where('is_cancelled', $isCancelled)
            ->groupByRaw("MONTH(date), DATE_FORMAT(date, '%b')")
            ->orderByRaw("MONTH(date)")
            ->get()
            ->keyBy('month_index');

        $monthlyData = collect();

        for ($i = 1; $i <= $month; $i++) {
            $monthlyData->push([
                'month' => Carbon::create()->month($i)->format('M'),
                'month_index' => $i,
                'amount' => isset($rawData[$i]) ? (float) $rawData[$i]->amount : 0,
            ]);
        }

        return $monthlyData;
    }

  
}
