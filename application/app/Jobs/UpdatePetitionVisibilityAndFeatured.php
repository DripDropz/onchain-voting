<?php

namespace App\Jobs;

use App\Enums\RuleV1Enum;
use App\Models\Petition;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;

class UpdatePetitionVisibilityAndFeatured implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $petition;

    /**
     * Create a new job instance.
     */
    public function __construct(Petition $petition)
    {
        $this->petition = $petition;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $rules = $this->petition->petition_goals;

        if (isset($rules[RuleV1Enum::VISIBLE->value])) {
            $visibleCount = intval($rules[RuleV1Enum::VISIBLE->value]['value2']);

            if (is_numeric($visibleCount) && $visibleCount > 0) {
                $signaturesCount = $this->petition->signatures()->count();

                if ($signaturesCount >= $visibleCount) {
                    $this->petition->update(['is_visible' => true]);
                } else {
                    $this->petition->update(['is_visible' => false]);
                }
            }
        }

        if (isset($rules[RuleV1Enum::FEATURE_PETITION->value])) {
            $featuredCount = intval($rules[RuleV1Enum::FEATURE_PETITION->value]['value2']);

            if (is_numeric($featuredCount) && $featuredCount > 0) {
                $signaturesCount = $this->petition->signatures()->count();

                if ($signaturesCount >= $featuredCount) {
                    $this->petition->update(['is_featured' => true]);
                } else {
                    $this->petition->update(['is_featured' => false]);
                }
            }
        }
    }


    /**
     * Get the middleware the job should pass through.
     *
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return [new WithoutOverlapping($this->petition->id)];
    }
}
