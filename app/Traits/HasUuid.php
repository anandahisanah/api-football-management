<?php

namespace App\Traits;

use Ramsey\Uuid\Uuid;

trait HasUuid
{
    /**
     * Boot the trait and attach creating event listener.
     */
    protected static function bootHasUuid()
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                // UUID v7
                $model->id = Uuid::uuid7()->toString();
            }
        });
    }
}
