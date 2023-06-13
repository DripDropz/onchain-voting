<?php

namespace App\Http\Integrations\Lucid;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

class WalletConnector extends Connector
{
    use AcceptsJson;

    /**
     * The Base URL of the API
     *
     * @return string
     */
    public function resolveBaseUrl(): string
    {
        return 'http://localhost:3001';
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
            // 'Multiple-Values-Header' => ['Value1', 'Value2'], // Value1;Value2
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
            'timeout' => 60,
        ];
    }
}
