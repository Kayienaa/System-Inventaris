<?php

namespace App\Support\Borrowings;

use Carbon\CarbonInterface;

class BorrowingDueDateCalculator
{
    public const int DURATION_DAYS = 3;

    public function fromCheckout(CarbonInterface $checkedOutAt): CarbonInterface
    {
        return $checkedOutAt->copy()->addDays(self::DURATION_DAYS);
    }

    public function forSiswa(?CarbonInterface $date = null): CarbonInterface
    {
        return ($date ? $date->copy() : now())->setTimezone('Asia/Jakarta')->setTime(15, 15, 0);
    }
}
