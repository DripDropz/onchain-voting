<?php

namespace App\DataTransferObjects;

use Spatie\LaravelData\Attributes\WithoutValidation;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\Optional as TypescriptOptional;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class BallotResponseData extends Data
{
    public function __construct(
        #[WithoutValidation]
        public ?string $hash,

        #[TypeScriptOptional]
        public ?string $created_at,

        public ?BallotData $ballot,

        public ?QuestionData $question,

        public QuestionChoiceData $choice,

        public ?UserData $user,

        public ?VotingPowerData $voting_power,

        public ?string $submit_tx,
    ) {
    }
}
