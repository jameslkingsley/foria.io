<?php

namespace App\Traits;

use App\Support\Reference;
use App\Models\Reference as ReferenceModel;

trait Referenceable
{
    /**
     * Gets the references for the model.
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function references()
    {
        return $this->morphMany(ReferenceModel::class, 'model');
    }

    /**
     * Gets the reference factory instance.
     *
     * @return TODO
     */
    public function reference()
    {
        return new Reference(get_class($this), $this->id);
    }

    /**
     * Gets the reference hash attribute.
     *
     * @return string
     */
    public function getRefAttribute()
    {
        return optional($this->references()->first())->hash;
    }
}
