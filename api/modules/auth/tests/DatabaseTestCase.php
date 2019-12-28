<?php

namespace Module\Auth\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class DatabaseTestCase extends AuthTestCase
{
    use RefreshDatabase, AuthTestingMethods;
}
