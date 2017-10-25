<?php

namespace App\Models;

use App\Support\Token;
use App\Contracts\Purchase;
use App\Traits\Purchasable;
use Illuminate\Database\Eloquent\Model;

class TokenPackage extends Model implements Purchase
{
    use Purchasable;

    /**
     * Guarded attributes.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Gets the purchasable details.
     *
     * @return array
     */
    public function getPurchaseDetails()
    {
        return [
            'once' => false,
            'via_tokens' => false,
            'amount' => $this->cost,
            'name' => "{$this->token_count} Tokens",
            'allowed' => auth()->check() && auth()->user()->hasCardOnFile(),
            'payout' => Token::make($this->token_count)->asPayout()->toCurrency(),
        ];
    }
}
