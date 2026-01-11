<?php

namespace Modules\React\Traits;

use Modules\React\Services\ReactService;

trait HasCounts
{
    /**
     * Increment the count for a specific filter.
     */
    public function incrementCount(string $filter): void
    {
        app(ReactService::class)->incrementCount($this->getMorphClass(), $this->getKey(), $filter);
    }

    /**
     * Decrement the count for a specific filter.
     */
    public function decrementCount(string $filter): void
    {
        app(ReactService::class)->decrementCount($this->getMorphClass(), $this->getKey(), $filter);
    }

    /**
     * Get the count for a specific filter.
     */
    public function getCount(string $filter): int
    {
        return app(ReactService::class)->getCount($this->getMorphClass(), $this->getKey(), $filter);
    }
}
