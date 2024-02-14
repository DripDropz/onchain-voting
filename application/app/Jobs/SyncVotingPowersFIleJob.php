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
        protected $storagePath
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        LazyCollection::make(function () {
            $handle = fopen($this->storagePath, 'r');

            while (($line = fgetcsv($handle, null)) !== false) {
                yield $line;
            }

            fclose($handle);
        })
            ->skip(1)
            ->chunk(1000)
            ->each(function (LazyCollection $chunk) {
                $chunk->each(function ($row) {
                    CreateVotingPowerSnapshotJob::dispatch(
                        $this->snapshot->hash,
                        $row[0],
                        $row[1]
                    );
                });
            });

        File::delete($this->storagePath);
        event(new votingPowersImportedEvent($this->snapshot));
    }
}
