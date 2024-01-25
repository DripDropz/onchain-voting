<?php

namespace App\DataTransferObjects;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\CamelCaseMapper;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
#[MapName(CamelCaseMapper::class)]
class SignatureData extends Data
{
    public function __construct(
        public string $hash,

        public ?string $email_signature,

        public ?string $wallet_signature,

        public ?string $created_at,

        public ?string $stake_address,

        public ?VoterData $voter,

        public ?BallotData $ballot,

        public ?PollData $pollData,

        public ?PetitionData $petition
    ) {
    }
}
