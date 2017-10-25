<?php

namespace App\Support;

class Token
{
    /**
     * Amount of tokens.
     *
     * @var integer
     */
    protected $amount;

    /**
     * Constructor method.
     *
     * @return void
     */
    public function __construct(int $amount)
    {
        $this->amount = $amount;
    }

    /**
     * Makes a new token instance.
     *
     * @return App\Support\Token
     */
    public static function make(int $amount)
    {
        return new static($amount);
    }

    /**
     * Gets the payout for the given amount of tokens.
     *
     * @return App\Support\Token
     */
    public function asPayout()
    {
        $this->amount = $this->amount * 0.8;

        return $this;
    }

    /**
     * Gets the profit made for the given amount of tokens.
     *
     * @return App\Support\Token
     */
    public function asProfit()
    {
        $this->amount = $this->amount * 0.2;

        return $this;
    }

    /**
     * Converts the token amount to currency.
     *
     * @return (currency) integer
     */
    public function toCurrency()
    {
        return $this->amount * 10;
    }
}
