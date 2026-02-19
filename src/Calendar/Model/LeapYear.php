<?php

namespace Calendar\Model;

class LeapYear
{
    public function isLeapYear(?int $year = null): bool
    {
        if ($year === null) {
            $year = (int) date('Y');
        }
        return ($year % 4 === 0 && $year % 100 !== 0) || ($year % 400 === 0);
    }
}
