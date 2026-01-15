<?php

namespace Modules\React\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\React\Actions\SaveAction;
use Modules\React\Actions\UnsaveAction;
use Modules\React\Models\Save;
use Modules\React\Services\ReactService;

trait HasSaves
{
    /**
     * @return MorphMany<Save, $this>
     */
    public function saves(): MorphMany
    {
        return $this->morphMany(Save::class, 'saveable');
    }

    /**
     * Determine whether the given user has saved this model.
     */
    public function isSavedBy(?int $userId): bool
    {
        if (! $userId) {
            return false;
        }

        return app(ReactService::class)->checkSaved($this->getMorphClass(), $this->getKey(), $userId);
    }

    /**
     * Add a save from the given user.
     */
    public function saveFor(int $userId): void
    {
        if (! $this->isSavedBy($userId)) {
            app(SaveAction::class)->execute($this->getMorphClass(), $this->getKey(), $userId);
        }
    }

    /**
     * Remove a save by the given user.
     */
    public function unsave(int $userId): void
    {
        app(UnsaveAction::class)->execute($this->getMorphClass(), $this->getKey(), $userId);
    }

    /**
     * Toggle the save state for the given user.
     */
    public function toggleSave(int $userId): void
    {
        $this->isSavedBy($userId)
            ? $this->unsave($userId)
            : $this->saveFor($userId);
    }
}
