<?php

namespace App\Jobs;

use App\Models\Snapshot;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\votingPowersImportedEvent;
use App\Jobs\CreateVotingPowerSnapshotJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SyncVotingPowersFIleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int|float $timeout = 900;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected Snapshot $snapshot,
        protected $filename
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $fileContent = Storage::disk('s3')->get($this->filename);
        $processedRowCount = 0;

        LazyCollection::make(function () use ($fileContent, $processedRowCount) {
            $lines = explode(PHP_EOL, $fileContent);
            foreach ($lines as $line) {
                yield str_getcsv($line);
            }
        })
            ->skip(1)
            ->chunk(1000)
            ->each(function (LazyCollection $chunk) use ($processedRowCount) {
                $chunk->each(function ($row) use ($processedRowCount) {
                    $processedRowCount++;
                    CreateVotingPowerSnapshotJob::dispatch(
                        $this->snapshot->hash,
                        $row[0],
                        $row[1]
                    );
                });
            });

        event(new votingPowersImportedEvent($this->snapshot));
    }
}
