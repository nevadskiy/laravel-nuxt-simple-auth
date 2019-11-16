<?php

namespace Tests;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Freeze time.
     *
     * @return Carbon
     */
    protected function freezeTime(): Carbon
    {
        // Allows to use this time in comparing with database time.
        $time = Carbon::createFromTimestamp(
            Carbon::now()->getTimestamp()
        );

        Carbon::setTestNow($time);

        return $time;
    }
}
