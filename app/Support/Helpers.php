<?php

use App\Models\Reference;

/**
 * Returns a view that contains the given Vue component with the given attributes.
 *
 * @return mixed
 */
function vue(string $name, array $attributes = [])
{
    $data = [
        'componentAttributes' => $attributes,
        'componentName' => $name
    ];

    return view('vue.index', $data);
}

/**
 * Reference factory helper function.
 *
 * @return mixed
 */
function reference(string $hash)
{
    $ref = Reference::where('hash', $hash)->first();

    if (! $ref) {
        throw new InvalidArgumentException("Reference '{$hash}' does not exist.");
    }

    return $ref->referenced();
}
