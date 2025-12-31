<?php

namespace Modules\CodeSpace\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\CodeSpace\Database\Factories\CodeSpaceFactory;
use Modules\User\Models\User;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property int $id
 * @property string $name
 * @property array<array-key, mixed> $files
 * @property string $slug
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CodeSpace newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CodeSpace newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CodeSpace query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CodeSpace whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CodeSpace whereFiles($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CodeSpace whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CodeSpace whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CodeSpace whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CodeSpace whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CodeSpace whereUserId($value)
 *
 * @mixin \Eloquent
 */
class CodeSpace extends Model
{
    /** @use HasFactory<\Modules\CodeSpace\Database\Factories\CodeSpaceFactory> */
    use HasFactory;

    use LogsActivity;

    protected $fillable = [
        'name',
        'slug',
        'files',
        'user_id',
    ];

    protected $casts = [
        'files' => 'array',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the activity log options for CodeSpace model.
     * Logs changes to the "name" attribute.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('codespace')
            ->logOnly(['name'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): CodeSpaceFactory
    {
        return CodeSpaceFactory::new();
    }
}
