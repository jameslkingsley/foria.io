<?php

namespace App\Models;

use App\Models\User;
use App\Support\Token;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    /**
     * Guarded attributes.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Appended attributes.
     *
     * @var array
     */
    protected $appends = [
        'profit'
    ];

    /**
     * Gets the user model.
     *
     * @return App\Models\User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Gets the profit attribute.
     *
     * @return integer
     */
    public function getProfitAttribute()
    {
        if ($this->via_tokens) {
            return Token::make($this->tokens)->asProfit()->toCurrency();
        }

        return $this->amount - $this->payout;
    }
}
