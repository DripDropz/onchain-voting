<?php

namespace App\Observers;

use App\Http\Integrations\Lucid\LucidConnector;
use App\Http\Integrations\Lucid\Requests\GetPolicyId;
use App\Models\Policy;
use Saloon\Exceptions\Request\FatalRequestException;

class PolicyObserver
{
    public function created(Policy $policy): void
    {
    }
}
