<?php

namespace App\Traits;

use App\Models\Reference;
use Illuminate\Database\Eloquent\Model;

trait Referenceable
{
    /**
     * Constructor method.
     *
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->appends[] = 'ref';
    }

    /**
     * Boot the referenceable trait.
     *
     * @return void
     */
    protected static function bootReferenceable()
    {
        static::created(function (Model $model) {
            $model->references()->save(
                new Reference([
                    'hash' => str_random(12)
                ])
            );
        });
    }

    /**
     * Gets the references for the model.
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function references()
    {
        return $this->morphMany(Reference::class, 'model');
    }

    /**
     * Gets the reference factory instance.
     *
     * @return App\Models\Reference
     */
    public function reference()
    {
        return $this->references()->first();
    }

    /**
     * Gets the reference hash attribute.
     *
     * @return string
     */
    public function getRefAttribute()
    {
        return optional($this->reference())->hash;
    }
}
