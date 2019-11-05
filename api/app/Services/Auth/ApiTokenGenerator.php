<?php

namespace App\Services\Auth;

interface ApiTokenGenerator
{
    /**
     * Generate an api token.
     *
     * @return string
     */
    public function generate(): string;
}
