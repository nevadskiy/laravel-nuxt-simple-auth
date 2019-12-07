<?php

namespace Module\Auth;

interface Link
{
    /**
     * Generate a link to the given path.
     *
     * @param string $path
     * @param array $params
     * @return string
     */
    public function to(string $path, array $params = []): string;
}
