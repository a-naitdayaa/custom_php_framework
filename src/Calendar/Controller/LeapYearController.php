<?php

namespace Calendar\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Calendar\Model\LeapYear;

class LeapYearController
{
    public function index(Request $request, int $year): Response
    {
        $leapYear = new LeapYear();
        if ($leapYear->isLeapYear($year)) {
            return new Response(sprintf('The year %d is a leap year.', $year));
        }
        return new Response(sprintf('The year %d is not a leap year.', $year));
    }
}
