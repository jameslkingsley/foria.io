<?php

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
