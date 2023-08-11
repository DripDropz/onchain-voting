<?php

namespace App\Http\Integrations\Blockfrost\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class BlockfrostRequest extends Request
{
    /**
     * Define the HTTP method
     */
    protected Method $method = Method::GET;

    public function __construct(
        protected string $endpoint,
    ) {
    }

    /**
     * Define the endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return $this->endpoint;
    }
}
