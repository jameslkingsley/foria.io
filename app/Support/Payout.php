<?php

namespace App\Support;

class Payout
{
    /**
     * The percentage of the amount that is payed out.
     *
     * @var integer
     */
    const PERCENTAGE = 0.8;

    /**
     * Gets the calculated payout amount.
     *
     * @return integer
     */
    public static function amount(int $amount)
    {
        return static::PERCENTAGE * $amount;
    }
}
