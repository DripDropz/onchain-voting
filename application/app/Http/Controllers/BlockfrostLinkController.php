<?php

namespace App\Http\Controllers;

use App\Http\Integrations\Blockfrost\Requests\BlockfrostRequest;
use Illuminate\Http\Request;

class BlockfrostLinkController extends Controller
{
    public BlockfrostRequest $frost;

    public function getAssetDetail(Request $request, BlockfrostRequest $frost)
    {
        $frost->setEndPoint('/assets/policy/'. $request->policy);
        $response = $frost->send()->json();
        $assetCount = count($response);
        $asset  = $response[0]['asset'];
        $frost->setEndPoint('/assets/' . $asset);
        $response = $frost->send()->json();
        return [
            'asset' => $response,
            'assetCount' => $assetCount
        ] ;
    }


    public  function queryChain(string $uri, ?BlockfrostRequest $frost)
    {
        $frost->setEndPoint($uri);
        $response = $frost->send();
        return $response->json();
    }
}
