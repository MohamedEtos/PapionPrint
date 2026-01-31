<?php

require __DIR__ . '/vendor/autoload.php';

use Carbon\Carbon;

$year = 2026;
$month = 1;

echo "--- Debugging Friday Count ---\n";
echo "Simulated Date (Now): " . Carbon::now()->toDateTimeString() . "\n";
echo "Simulated Today: " . Carbon::today()->toDateTimeString() . "\n";

$daysInMonth = Carbon::createFromDate($year, $month)->daysInMonth;
$startOfMonth = Carbon::createFromDate($year, $month, 1);
$endOfMonth = Carbon::createFromDate($year, $month, $daysInMonth);

echo "Month Start: " . $startOfMonth->toDateString() . "\n";
echo "Month End: " . $endOfMonth->toDateString() . "\n";

// Emulate User
$user = new stdClass();
$user->joining_date = '2026-01-01'; // User setting
$user->resignation_date = null;
$user->created_at = Carbon::parse('2026-01-29'); // Irrelevant if joining_date set

// Logic from Controller
$effectiveStartDate = $startOfMonth->copy();
$userStartDate = $user->joining_date ? Carbon::parse($user->joining_date) : $user->created_at;

echo "User Start Date: " . $userStartDate->toDateString() . "\n";

if ($userStartDate->gt($effectiveStartDate)) {
    $effectiveStartDate = $userStartDate->copy();
}

$effectiveEndDate = $endOfMonth->copy();

// Cap at today
if (Carbon::today()->lt($effectiveEndDate)) {
    echo "Capping End Date at Today (" . Carbon::today()->toDateString() . ")\n";
    $effectiveEndDate = Carbon::today();
}

if ($user->resignation_date) {
    // ... resignation logic
}

echo "Effective Start: " . $effectiveStartDate->toDateString() . "\n";
echo "Effective End: " . $effectiveEndDate->toDateString() . "\n";

$fridays = 0;
if ($effectiveStartDate->lte($effectiveEndDate) && $effectiveStartDate->month == $month) {
    $currentDay = $effectiveStartDate->copy();
    while ($currentDay->lte($effectiveEndDate)) {
        if ($currentDay->isFriday()) {
            $fridays++;
            echo "Friday Found: " . $currentDay->toDateString() . "\n";
        }
        $currentDay->addDay();
    }
}

echo "Total Fridays: " . $fridays . "\n";
