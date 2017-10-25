<?php

namespace App\Traits;

use App\Support\Token;
use App\Support\Payout;
use App\Models\Purchase;
use App\Events\TokensAdded;
use App\Exceptions\PrivacyException;
use App\Exceptions\NoCustomerException;
use Illuminate\Auth\AuthenticationException;
use App\Exceptions\InsufficientFundsException;

trait Purchasable
{
    /**
     * Purchases the model instance.
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function purchase()
    {
        if (auth()->guest()) {
            throw new AuthenticationException;
        }

        $details = (object) $this->getPurchaseDetails();

        if (! $details->allowed) {
            throw new PrivacyException;
        }

        if ((int) auth()->user()->tokens < (int) $details->amount) {
            throw new InsufficientFundsException;
        }

        auth()->user()->tokens -= (int) $details->amount;
        auth()->user()->save();

        event(new TokensAdded(auth()->user(), -(int) $details->amount));

        return $this->purchases()->save(
            new Purchase([
                'name' => $details->name,
                'tokens' => $details->amount,
                'user_id' => auth()->user()->id,
                'amount' => Token::make($details->amount)->toCurrency(),
                'payout' => Token::make($details->amount)->asPayout()->toCurrency(),
            ])
        );
    }

    /**
     * Gets all purchases for the authenticated user.
     *
     * @return Illuminate\Database\Eloquent\QueryBuilder
     */
    public function purchases()
    {
        $query = $this->morphMany(Purchase::class, 'model');

        return auth()->check()
            ? $query->where('user_id', auth()->user()->id)
            : $query;
    }

    /**
     * Determines if the user has purchased the model.
     *
     * @return boolean
     */
    public function purchased()
    {
        return $this->purchases()->exists();
    }
}
