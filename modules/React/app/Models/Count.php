<?php

namespace Modules\React\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property string $countable_type
 * @property int $countable_id
 * @property string|null $filter
 * @property int $count
 *
 * @property-read Model $countable
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Count newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Count newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Count query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Count whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Count whereCountableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Count whereCountableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Count whereFilter($value)
 *
 * @mixin \Eloquent
 */
class Count extends Model
{
    public $timestamps = false;

    public $incrementing = false;

    protected $primaryKey = null;

    protected $fillable = [
        'countable_id',
        'countable_type',
        'filter',
        'count',
    ];

    /**
     * @return MorphTo<Model, $this>
     */
    public function countable(): MorphTo
    {
        return $this->morphTo();
    }
}
