<?php

namespace App\Traits;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Subscription;
use App\Exceptions\InvalidSubscriptionException;

trait Subscriptions
{
    /**
     * Determines whether the user is subscribed to the given model user.
     *
     * @return boolean
     */
    public function subscribedTo(User $user)
    {
        return ! is_null($this->subscriptionTo($user));
    }

    /**
     * Gets the subscription object for the given model user.
     *
     * @return App\Models\Subscription|null
     */
    public function subscriptionTo(User $user)
    {
        return Subscription::where('from_id', $this->id)
            ->where('to_id', $user->id)
            ->with('from')
            ->with('to')
            ->first();
    }

    /**
     * Subscribes to the given user.
     *
     * @return App\Models\Subscription
     */
    public function subscribeTo(User $user, string $plan = 'bronze')
    {
        if (! $this->canSubscribeTo($user)) {
            return abort(403, 'Cannot subscribe to model');
        }

        $stripeSubscription = $this->newSubscription($plan);

        $subscription = Subscription::create([
            'from_id' => $this->id,
            'to_id' => $user->id,
            'stripe_id' => $stripeSubscription->id,
            'stripe_plan' => $plan,
            'ends_at' => Carbon::now()->addMonth(),
        ]);

        // event(new SubscriptionEvent($subscription));

        return $subscription;
    }

    /**
     * Unsubscribes from the given user.
     *
     * @return mixed
     */
    public function unsubscribeFrom(User $user)
    {
        $subscription = $this->subscriptionTo($user);

        $stripeSubscription = $this->cancelSubscription($subscription->stripe_id);

        $subscription->update([
            'cancels_at' => Carbon::createFromTimestamp($stripeSubscription->current_period_end)
        ]);
    }

    /**
     * Determines if the user can subscribe to the given user.
     *
     * @return boolean
     */
    public function canSubscribeTo(User $user)
    {
        // Cannot subscribe to own account
        if ($this->id == $user->id) {
            return false;
        }

        // Must have card on file in billing settings
        if (! $this->hasCardOnFile()) {
            return false;
        }

        // User must be an approved model
        if (! $user->is_model) {
            return false;
        }

        // Must not already be subscribed
        if ($this->subscribedTo($user)) {
            return false;
        }

        // TODO Disallow blocked users from subscribing

        return true;
    }
}
