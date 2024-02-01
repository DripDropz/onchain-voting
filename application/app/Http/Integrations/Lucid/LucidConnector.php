<?php

namespace App\Http\Integrations\Lucid;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

class LucidConnector extends Connector
{
    use AcceptsJson;

    /**
     * The Base URL of the API
     */
    public function resolveBaseUrl(): string
    {
        return config('services.lucid.endpoint');
    }

    /**
     * Default headers for every request
     *
     * @return string[]
     */
    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Cardano-Network' => config('services.lucid.network'),
        ];
    }

    /**
     * Default HTTP client options
     *
     * @return string[]
     */
    protected function defaultConfig(): array
    {
        return [
            'timeout' => 300,
        ];
    }
}
