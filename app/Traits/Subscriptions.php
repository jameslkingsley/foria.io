<?php

namespace App\Traits;

use App\Models\User;
use App\Models\Subscription;
use App\Exceptions\InvalidSubscription;

trait Subscriptions
{
    /**
     * Determines whether the user is subscribed to the given model user.
     *
     * @return boolean
     */
    public function subscribedTo(User $user)
    {
        return ! is_null(
            Subscription::where('from_id', $this->id)
                ->where('to_id', $user->id)
                ->first()
        );
    }

    /**
     * Subscribes to the given user.
     *
     * @return App\Models\Subscription
     */
    public function subscribeTo(User $user, string $plan = 'basic')
    {
        if (! $this->canSubscribeTo($user)) {
            throw new InvalidSubscription;
        }

        $stripeSubscription = $this->newSubscription($plan);

        $subscription = Subscription::create([
            'from_id' => $this->id,
            'to_id' => $user->id,
            'stripe_id' => $stripeSubscription->id,
            'stripe_plan' => $plan,
            'ends_at' => null, // TODO
        ]);

        // event(new SubscriptionEvent($subscription));

        return $subscription;
    }

    /**
     * Determines if the user can subscribe to the given user.
     *
     * @return boolean
     */
    public function canSubscribeTo(User $user)
    {
        if (! $this->hasCardOnFile()) {
            return false;
        }

        if (! $user->is_model) {
            return false;
        }

        // TODO Disallow blocked users from subscribing

        return true;
    }
}
