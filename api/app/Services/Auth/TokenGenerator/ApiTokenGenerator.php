<?php

namespace App\Services\Auth\TokenGenerator;

interface ApiTokenGenerator
{
    /**
     * Generate an api token.
     *
     * @return string
     */
    public function generate(): string;
}
