<?php

namespace Module\Auth\Tests;

use Carbon\Carbon;
use Tests\TestCase;

abstract class AuthTestCase extends TestCase
{
    /**
     * Freeze the application time on the current or given timestamp.
     *
     * @param Carbon|null $time
     * @return Carbon
     */
    protected function freezeTime(Carbon $time = null): Carbon
    {
        $time = $time ?: Carbon::now();

        $timestamp = Carbon::createFromTimestamp($time->getTimestamp());

        Carbon::setTestNow($timestamp);

        return $time;
    }
}
