<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;

/**
 * @method static \Illuminate\Database\Eloquent\Builder query()
 * @method static static|null find($id, $columns = null)
 * @method static static findOrFail($id, $columns = null)
 * @method static \Illuminate\Database\Eloquent\Builder where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder whereIn($column, $values, $boolean = 'and', $not = false)
 * @method static bool exists()
 * @method static bool doesntExist()
 * @method static static|null first($columns = null)
 * @method static \Illuminate\Database\Eloquent\Collection get($columns = null)
 * @method static static create(array $attributes = [])
 * @method static \Illuminate\Database\Eloquent\Builder select(array|string|null $columns = null)
 * @method static \Illuminate\Database\Eloquent\Builder orderBy($column, $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder orderby($column, $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder withCount($relations)
 * @method static \Illuminate\Database\Eloquent\Builder with($relations)
 */
abstract class BaseModel extends EloquentModel
{
}
