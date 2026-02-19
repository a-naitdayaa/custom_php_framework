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
            $response = new Response(sprintf('The year %d is a leap year.' . ' - ' . rand() . ' - ', $year));
        }
        $response = new Response(sprintf('The year %d is not a leap year.' . ' - ' . rand() . ' - ', $year));

        //cache the response for 10 seconds
        $response->setTtl(10);
        return $response;
    }
}
