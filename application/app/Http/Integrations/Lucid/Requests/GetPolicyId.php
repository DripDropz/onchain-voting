<?php

namespace App\Http\Integrations\Lucid\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasJsonBody;

class GetPolicyId extends Request implements HasBody
{
    use HasJsonBody;

    /**
     * Define the HTTP method
     */
    protected Method $method = Method::GET;

    /**
     * Define the endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/wallet/get-policy-id';
    }
}
