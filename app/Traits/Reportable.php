<?php

namespace App\Traits;

use App\Models\Report;

trait Reportable
{
    /**
     * Gets the reports for the model.
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function reports()
    {
        return $this->morphMany(Report::class, 'model');
    }

    /**
     * Reports the model with the given reason.
     *
     * @return App\Models\Report
     */
    public function report(string $reason)
    {
        if (! array_key_exists($reason, Report::reasons())) {
            throw new InvalidArgumentException;
        }

        // TODO Raise reported event

        return $this->reports()->save(
            new Report([
                'user_id' => auth()->check() ? auth()->user()->id : null,
                'reason' => $reason
            ])
        );
    }
}
