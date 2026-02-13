<?php

namespace App\Traits;

/**
 * Sanitizes email attributes when saving models.
 * Trims whitespace (including trailing periods/spaces) to avoid RFC 2822 validation errors.
 *
 * Add to any model with email fields. Override $emailAttributes if the column name differs from 'email'.
 * Example: protected $emailAttributes = ['contact_email'];
 */
trait SanitizesEmail
{
    protected static function bootSanitizesEmail()
    {
        static::saving(function ($model) {
            $attributes = property_exists($model, 'emailAttributes')
                ? $model->emailAttributes
                : ['email'];

            foreach ((array) $attributes as $attr) {
                if (array_key_exists($attr, $model->attributes) && is_string($model->attributes[$attr])) {
                    $model->attributes[$attr] = trim($model->attributes[$attr]);
                }
            }
        });
    }
}
