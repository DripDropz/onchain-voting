<?php

/**
 * Copyright (c) Vincent Klaiber.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see https://github.com/vinkla/laravel-hashids
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Default Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the connections below you wish to use as
    | your default connection for all work. Of course, you may use many
    | connections at once using the manager class.
    |
    */

    'default' => 'main',

    /*
    |--------------------------------------------------------------------------
    | Hashids Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the connections setup for your application. Example
    | configuration has been included, but you may add as many connections as
    | you would like.
    |
    */

    'connections' => [
        \App\Models\Ballot::class => [
            'salt' => \App\Models\Ballot::class.env('APP_KEY'),
            'length' => 11,
            'alphabet' => 'abcdefghijklmnopqrstuvwxyz012345678',
        ],
        \App\Models\Petition::class => [
            'salt' => \App\Models\Petition::class.env('APP_KEY'),
            'length' => 11,
            'alphabet' => 'abcdefghijklmnopqrstuvwxyz012345679',
        ],
        \App\Models\Poll::class => [
            'salt' => \App\Models\Poll::class.env('APP_KEY'),
            'length' => 11,
            'alphabet' => 'acdefghijklmnopqrstuvwxyz0123456789',
        ],
        \App\Models\QuestionChoice::class => [
            'salt' => \App\Models\QuestionChoice::class.env('APP_KEY'),
            'length' => 11,
            'alphabet' => 'bcdefghiklmnopqrstuvwxz0123456789',
            'alphabet' => 'bcdefghiklmnopqrstuvwxz0123456789',
        ],
        \App\Models\BallotResponse::class => [
            'salt' => \App\Models\BallotResponse::class.env('APP_KEY'),
            'length' => 11,
            'alphabet' => 'abcdefghilmnopqrstuvwxz0123456789',
        ],
        \App\Models\Question::class => [
            'salt' => \App\Models\Ballot::class.env('APP_KEY'),
            'length' => 11,
            'alphabet' => 'acdefghiklmnopqrstuvwxyz0123456789',
        ],
        \App\Models\Snapshot::class => [
            'salt' => \App\Models\Snapshot::class.env('APP_KEY'),
            'length' => 11,
            'alphabet' => 'abcdefghijklmnoqrstuvwxz0123456789',
        ],
        \App\Models\User::class => [
            'salt' => \App\Models\User::class.env('APP_KEY'),
            'length' => 11,
            'alphabet' => 'abcdefghijklmnoqrstvwxyz0123456789',
        ],

        \App\Models\VotingPower::class => [
            'salt' => \App\Models\VotingPower::class.env('APP_KEY'),
            'length' => 11,
            'alphabet' => 'abcdefghijklmnoqrstuwxyz0123456789',
        ],

        \App\Models\Policy::class => [
            'salt' => \App\Models\Policy::class.env('APP_KEY'),
            'length' => 11,
            'alphabet' => 'abcdefghijklmnoqrstuvxyz0123456789',
        ],

        \App\Models\Rule::class => [
            'salt' => \App\Models\Rule::class . env('APP_KEY'),
            'length' => 11,
            'alphabet' => 'abcdefghijklmnoqrstuvxyz0123456789',
        ],

        \App\Models\Signature::class => [
            'salt' => \App\Models\Signature::class . env('APP_KEY'),
            'length' => 11,
            'alphabet' => 'abcdefghijklmnoqrstuvxyz0123456789',
        ],

        \App\Models\QuestionResponse::class => [
            'salt' => \App\Models\QuestionResponse::class . env('APP_KEY'),
            'length' => 11,
            'alphabet' => 'abcdefghijklmnoqrstuvxyz0123456789',
        ],

        //        'main' => [
        //            'salt' =>  env('APP_KEY'),
        //            'length' => 4,
        //            // 'alphabet' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'
        //        ],
        //
        //        'alternative' => [
        //            'salt' => 'your-salt-string',
        //            'length' => 'your-length-integer',
        //            // 'alphabet' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'
        //        ],

    ],

];
