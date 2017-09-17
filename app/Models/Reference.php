<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reference extends Model
{
    /**
     * Guarded attributes.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Gets the referenced model.
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function referenced()
    {
        return $this->model_type::findOrFail($this->model_id);
    }
}
