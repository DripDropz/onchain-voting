<?php

namespace App\DataTransferObjects;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\WithoutValidation;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\TypeScriptTransformer\Attributes\Optional as TypescriptOptional;

#[TypeScript]
class PollData extends Data
{
    public function __construct(
        #[WithoutValidation]
        public ?string $hash,


        #[Required, StringType]
        public string $title,

        #[TypescriptOptional]
        #[Rule('string')]
        public ?string $description,

        #[WithoutValidation]
        public ?UserData $user,

        #[TypeScriptOptional]
        public ?string $created_at,

        #[TypeScriptOptional]
        public ?string $updated_at,

        #[Required]
        #[Rule('string')]
        public string $status,

        /** @var RuleData[] */
        public ?DataCollection $rules,

    ) {
    }

    public static function attributes(): array
    {
        return [
            'title' => 'title',
            'description' => 'description',
        ];
    }
}
