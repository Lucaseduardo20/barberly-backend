<?php

function formatCommission(int $amount): string
{
    return 'R$ ' . number_format($amount, 2, ',', '.');
}

function formatTime(string $time): string
{
    return date('H\hi', strtotime($time));
}

function formatDate(string $date, string $format = 'd/m/Y'): string
{
    return \Carbon\Carbon::parse($date)->format($format);
}

function formatMinutesToHours(int $minutes): string
{
    $hours = intdiv($minutes, 60);
    $remainingMinutes = $minutes % 60;

    return ($hours > 0 ? "{$hours}h" : '') . ($remainingMinutes > 0 ? "{$remainingMinutes}m" : '');
}
