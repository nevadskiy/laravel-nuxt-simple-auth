<?php

namespace Nevadskiy\Tokens\Generator;

interface Generator
{
    /**
     * Generate the token
     *
     * @return string
     */
    public function generate(): string;
}
