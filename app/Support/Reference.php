<?php

namespace App\Support;

use App\Models\Reference as ReferenceModel;

class Reference
{
    /**
     * Constructor method.
     *
     * @return void
     */
    public function __construct($model, $id)
    {
        $this->model = $model::findOrFail($id);
    }

    /**
     * Resolves a reference hash to its model.
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public static function resolve($hash)
    {
        $ref = ReferenceModel::where('hash', $hash)->first();

        if (! $ref) {
            return abort(404);
        }

        return $ref->referenced();
    }

    /**
     * Creates a new reference for the model.
     *
     * @return App\Models\Reference
     */
    public function new()
    {
        return $this->model->references()->save(
            new ReferenceModel([
                'hash' => str_random(12)
            ])
        );
    }

    /**
     * Deletes any other references and creates a fresh one.
     *
     * @return App\Models\Reference
     */
    public function fresh()
    {
        $this->model->references()->delete();

        return $this->new();
    }
}
