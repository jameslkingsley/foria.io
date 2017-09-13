<?php

namespace App\Traits;

use Stripe\Plan;
use Carbon\Carbon;
use App\Models\User;
use App\Support\Chat;
use App\Models\Subscription;
use App\Events\NewSubscription;
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

        $plan = Plan::retrieve($plan);
        $amountFormatted = '£'.($plan->amount / 100);

        // Create the Stripe subscription
        $stripeSubscription = $this->newSubscription($plan->id);

        // Create our record of the subscription
        $subscription = Subscription::create([
            'from_id' => $this->id,
            'to_id' => $user->id,
            'stripe_id' => $stripeSubscription->id,
            'stripe_plan' => $plan->id,
            'ends_at' => Carbon::now()->addMonth(),
        ]);

        // Raise the event for the broadcast
        event(new NewSubscription($subscription));

        // Alert the broadcaster's chat of the subscription
        (new Chat($user))->alert("{$this->name} subscribed for {$amountFormatted}", [
            'is_subscription' => true
        ]);

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
