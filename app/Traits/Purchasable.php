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

        $details = (object) array_merge([
            'once' => false,
            'payee' => null,
            'amount' => null,
            'payout' => null,
            'allowed' => true,
            'name' => 'Purchase',
            'via_tokens' => true,
        ], $this->getPurchaseDetails());

        // Throw if already bought and once is true
        if ($details->once && $this->purchased()) {
            throw new AlreadyPurchasedException;
        }

        if (! $details->allowed) {
            throw new PrivacyException;
        }

        $stripe = null;

        if ($details->via_tokens) {
            if ((int) auth()->user()->tokens < (int) $details->amount) {
                throw new InsufficientFundsException;
            }

            auth()->user()->tokens -= (int) $details->amount;
            auth()->user()->save();

            // TODO We're not adding tokens, we're just providing the feedback
            event(new TokensAdded(auth()->user(), -(int) $details->amount));
        } else {
            $stripe = auth()->user()->charge((int) $details->amount);
        }

        return $this->purchases()->save(
            new Purchase([
                'name' => $details->name,
                'tokens' => $details->via_tokens
                    ? $details->amount
                    : null,
                'payee_id' => $details->payee,
                'user_id' => auth()->user()->id,
                'stripe_id' => optional($stripe)->id,
                'via_tokens' => $details->via_tokens,
                'amount' => $details->via_tokens
                    ? Token::make($details->amount)->toCurrency()
                    : $details->amount,
                'payout' => $details->via_tokens
                    ? Token::make($details->amount)->asPayout()->toCurrency()
                    : $details->payout,
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
