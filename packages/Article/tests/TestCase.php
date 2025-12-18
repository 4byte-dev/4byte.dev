<?php

namespace Packages\Article\Tests;

use Carbon\Carbon;
use Tests\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function isValidUrl(?string $url): bool
    {
        return is_string($url)
            && filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    protected function isValidDate(String $date): bool
    {
        try {
            Carbon::parse($date);

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
