<?php

namespace App\Auth\Services\TokenGenerator;

interface ApiTokenGenerator
{
    /**
     * Generate an api token.
     *
     * @return string
     */
    public function generate(): string;
}
