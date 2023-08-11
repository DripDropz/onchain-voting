<?php

namespace App\Http\Integrations\Blockfrost;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

class BlockfrostConnector extends Connector
{
    use AcceptsJson;

    /**
     * The Base URL of the API
     */
    public function resolveBaseUrl(): string
    {
        return config('services.blockfrost.baseUrl');
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
            'Accept' => '*/*',
            'project_id' => config('services.blockfrost.projectId'),
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
            'timeout' => 300,
        ];
    }
}
