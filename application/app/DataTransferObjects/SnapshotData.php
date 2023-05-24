<?php

namespace App\DataTransferObjects;

use Illuminate\Validation\NestedRules;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Attributes\Validation\Sometimes;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\WithoutValidation;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\Optional as TypescriptOptional;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class SnapshotData extends Data
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

        #[TypescriptOptional]
        public ?string $policy_id,

        #[TypescriptOptional]
        #[Rule('string')]
        public ?string $type,

        #[Required]
        #[Rule('string')]
        public string $status
    ) {}

    public static function attributes(): array
    {
        return [
            'title' => 'title',
            'description' => 'description'
        ];
    }

    public static function rules(): array
    {
        return [
//            'user.*' => [
//                new NestedRules(fn() => [new Sometimes(), new Nullable()])
//            ],
        ];
    }
}
