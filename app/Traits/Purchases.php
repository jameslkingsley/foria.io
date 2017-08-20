<?php

namespace App\Traits;

use App\Models\Purchase;
use App\Events\Purchased;

trait Purchases
{
    /**
     * Makes a purchase record for the user.
     *
     * @return App\Models\Purchase
     */
    public function purchased(string $description, int $amount)
    {
        $purchase = Purchase::create([
            'user_id' => $this->id,
            'description' => $description,
            'amount' => $amount
        ]);

        event(new Purchased($purchase));

        return $purchase;
    }

    /**
     * Gets all purchases for the user.
     *
     * @return Collection App\Models\Purchase
     */
    public function purchases()
    {
        return Purchase::where('user_id', $this->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
