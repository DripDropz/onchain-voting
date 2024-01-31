<?php

namespace App\Http\Integrations\Blockfrost\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Connector;
use Saloon\Traits\Request\HasConnector;
use App\Http\Integrations\Blockfrost\BlockfrostConnector;

class BlockfrostRequest extends Request
{
    use HasConnector;

    /**
     * Define the HTTP method
     */
    protected Method $method = Method::GET;

    protected ?string $endpoint;



    public function resolveConnector(): Connector
    {
        return app(BlockfrostConnector::class);
    }
    
    /**
     * Define the endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return $this->endpoint;
    }

    public function setEndPoint($endpoint)
    {
        return $this->endpoint = $endpoint;
    }
}
