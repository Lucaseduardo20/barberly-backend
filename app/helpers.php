<?php

function formatCommission(int $amount): string
{
    return 'R$ ' . number_format($amount, 2, ',', '.');
}

function formatTime(string $time): string
{
    return date('H\hi', strtotime($time));
}
