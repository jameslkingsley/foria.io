<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    /**
     * The reason constants.
     *
     * @var const
     */
    const VIOLENCE = 'Violent or abusive content';
    const CHILD_ABUSE = 'Child abuse';
    const PROMOTES_TERRORISM = 'Promotes terrorism';
    const COPYRIGHT = 'Infringes my rights';

    /**
     * Guarded attributes.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Gets the static array of reasons.
     *
     * @return array
     */
    public static function reasons()
    {
        return [
            'VIOLENCE' => static::VIOLENCE,
            'CHILD_ABUSE' => static::CHILD_ABUSE,
            'PROMOTES_TERRORISM' => static::PROMOTES_TERRORISM,
            'COPYRIGHT' => static::COPYRIGHT,
        ];
    }
}
