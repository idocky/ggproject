<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;

/**
 * Laravel's built-in `exists:table,column` rule relies on a presence verifier
 * that wraps the lookup value in a regex, which never matches MongoDB's
 * BSON ObjectId `_id` field. This checks existence through the model's own
 * query builder instead, which converts the id correctly.
 *
 * @template TModel of Model
 */
class MongoExists implements ValidationRule
{
    /**
     * @param  class-string<TModel>  $model
     */
    public function __construct(
        private readonly string $model,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || $this->model::query()->find($value) === null) {
            $fail('The selected :attribute is invalid.');
        }
    }
}
