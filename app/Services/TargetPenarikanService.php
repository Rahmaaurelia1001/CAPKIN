<?php

namespace App\Services;

use App\Models\TargetPenarikanDana;

class TargetPenarikanService
{
    /**
     * Get target percentages per month for a given year.
     * Returns an array where the key is the month (1‑12) and the value is the percent.
     * If a month has no entry, it will be omitted from the array.
     */
    public function getTargetByYear(int $tahun): array
    {
        return TargetPenarikanDana::where('tahun', $tahun)
            ->orderBy('bulan')
            ->pluck('percent', 'bulan')
            ->toArray();
    }
}
?>
