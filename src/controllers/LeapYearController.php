<?php

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class LeapYearController
{
    public function index(Request $request): Response
    {
        $year = $request->attributes->get('year');
        $isLeap = is_leap_year($year);
        if ($isLeap) {
            return new Response(sprintf('The year %d is a leap year.', $year));
        }
        return new Response(sprintf('The year %d is not a leap year.', $year));
    }
}
