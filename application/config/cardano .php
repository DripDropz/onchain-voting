<?php

return [
    'network' => env('CARDANO_NETWORK', 'preview'),
    'pool' => [
        'hash' => env('CARDANO_POOL_HASH'),
        'block_explorer' => env('CARDANO_BLOCK_EXPLORER', '//cexplorer.io'),
    ],
    'lucidEndpoint' => env('CARDANO_LUCID_ENDPOINT', 'http://localhost:3000'),


];