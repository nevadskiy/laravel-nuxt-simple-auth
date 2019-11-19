<?php

namespace App\Core;

use Illuminate\Foundation\Application as BaseApplication;

class Application extends BaseApplication
{
    /**
     * Get the path to the application configuration files.
     *
     * @param  string  $path Optionally, a path to append to the config path
     * @return string
     */
    public function configPath($path = ''): string
    {
        return __DIR__ . '/Config' . ($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Get the path to the resources directory.
     *
     * @param  string  $path
     * @return string
     */
    public function resourcePath($path = ''): string
    {
        return __DIR__ . '/Resources' . ($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}
