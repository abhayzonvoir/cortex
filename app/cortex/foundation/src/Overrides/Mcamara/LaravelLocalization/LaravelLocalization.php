<?php

declare(strict_types=1);

namespace Cortex\Foundation\Overrides\Mcamara\LaravelLocalization;

use Mcamara\LaravelLocalization\LaravelLocalization as BaseLaravelLocalization;

class LaravelLocalization extends BaseLaravelLocalization
{
    /**
     * Return default application locales.
     *
     * @return array
     */
    public function getAppLocales(): array
    {
        return array_unique([
            $this->configRepository->get('app.locale'),
            $this->configRepository->get('app.fallback_locale'),
        ]);
    }

    /**
     * Build URL using array data from parse_url.
     *
     * @param array|false $parsed_url Array of data from parse_url function
     *
     * @return string Returns URL as string.
     */
    protected function unparseUrl($parsed_url)
    {
        if (empty($parsed_url)) {
            return '';
        }

        $url = '';
        $url .= isset($parsed_url['scheme']) ? $parsed_url['scheme'].'://' : '';
        $url .= $parsed_url['host'] ?? '';
        $url .= isset($parsed_url['port']) ? ':'.$parsed_url['port'] : '';
        $user = $parsed_url['user'] ?? '';
        $pass = isset($parsed_url['pass']) ? ':'.$parsed_url['pass'] : '';
        $url .= $user.(($user || $pass) ? "{$pass}@" : '');

        if (! empty($url)) {
            $url .= isset($parsed_url['path']) ? '/'.ltrim($parsed_url['path'], '/').(config('cortex.foundation.route.trailing_slash') ? '/' : '') : '';
        } else {
            $url .= isset($parsed_url['path']) ? $parsed_url['path'].(config('cortex.foundation.route.trailing_slash') ? '/' : '') : '';
        }

        $url .= isset($parsed_url['query']) ? '?'.$parsed_url['query'] : '';
        $url .= isset($parsed_url['fragment']) ? '#'.$parsed_url['fragment'] : '';

        return $url;
    }
}
