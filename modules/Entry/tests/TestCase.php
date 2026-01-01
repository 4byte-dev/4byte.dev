<?php

namespace Modules\Entry\Tests;

use Carbon\Carbon;
use Tests\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
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
