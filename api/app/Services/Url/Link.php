<?php

namespace App\Services\Url;

class Link
{
    /**
     * @var string
     */
    private $baseUrl;

    /**
     * Link constructor.
     *
     * @param string $baseUrl
     */
    public function __construct(string $baseUrl = '/')
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * Generate a link to the given path.
     *
     * @param string $path
     * @param array $params
     * @return string
     */
    public function to(string $path, array $params = []): string
    {
        $url = $this->buildUrl($path);

        $params = $this->buildQueryParameters($params);

        if ($params) {
            $url .= '?' . $params;
        }

        return $url;
    }

    /**
     * Build url.
     *
     * @param string $path
     * @return string
     */
    protected function buildUrl(string $path): string
    {
        return implode('/', [
            trim($this->baseUrl, '/'),
            trim($path, '/'),
        ]);
    }

    /**
     * Build query parameters.
     *
     * @param array $params
     * @return string
     */
    protected function buildQueryParameters(array $params = []): string
    {
        return http_build_query($params) ?: '';
    }
}
