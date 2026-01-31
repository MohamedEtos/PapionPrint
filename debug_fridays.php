<?php

require __DIR__ . '/vendor/autoload.php';

use Carbon\Carbon;

$year = 2026;
$month = 1;

$startOfMonth = Carbon::createFromDate($year, $month, 1);
$daysInMonth = $startOfMonth->daysInMonth;
$endOfMonth = Carbon::createFromDate($year, $month, $daysInMonth);

echo "Start of Month: " . $startOfMonth->toDateTimeString() . "\n";
echo "End of Month: " . $endOfMonth->toDateTimeString() . "\n";

// Emulate Controller Logic
$effectiveStartDate = $startOfMonth->copy();
$effectiveEndDate = $endOfMonth->copy();

// Cap at today (Simulation: 2026-01-30)
$today = Carbon::create(2026, 1, 30); 
echo "Today Simulated: " . $today->toDateTimeString() . "\n";

if ($today->lt($effectiveEndDate)) {
    $effectiveEndDate = $today;
}

echo "Effective Start: " . $effectiveStartDate->toDateTimeString() . "\n";
echo "Effective End: " . $effectiveEndDate->toDateTimeString() . "\n";

$fridays = 0;
if ($effectiveStartDate->lte($effectiveEndDate) && $effectiveStartDate->month == $month) {
    $currentDay = $effectiveStartDate->copy();
    while ($currentDay->lte($effectiveEndDate)) {
        if ($currentDay->isFriday()) {
            $fridays++;
            echo "Found Friday: " . $currentDay->toDateString() . "\n";
        }
        $currentDay->addDay();
    }
}

echo "Total Fridays: " . $fridays . "\n";
