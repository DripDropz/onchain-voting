<?php

namespace App\DataTransferObjects;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\WithoutValidation;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\TypeScriptTransformer\Attributes\Optional as TypescriptOptional;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class QuestionResponseData extends Data
{
    public function __construct(
        #[WithoutValidation]
        public ?string $hash,

        #[TypeScriptOptional]
        public ?string $model_type,

        #[TypeScriptOptional]
        public ?int $model_id,

        #[TypeScriptOptional]
        public ?string $created_at,

        public ?QuestionData $question,

        #[TypescriptOptional]
        #[DataCollectionOf(QuestionChoiceData::class)]
        /** @var QuestionChoiceData[] */
        public ?DataCollection $choices,

        public ?UserData $user,

        public ?VotingPowerData $voting_power,

        public ?string $submit_tx,

        public ?int $rank,
    ) {
    }
}
