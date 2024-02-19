<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ModelStatusEnum;
use App\Http\Controllers\Controller;
use App\Jobs\CreateVotingPowerSnapshotJob;
use App\Jobs\SyncVotingPowerFIleJob;
use App\Jobs\SyncVotingPowersFIleJob;
use App\Models\Snapshot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Fluent;
use Illuminate\Support\LazyCollection;

class SnapshotImportController extends Controller
{
    /**
     * Display the new snapshots's form.
     */
    public function parseCSV(Request $request, Snapshot $snapshot)
    {
        $filename = $request->filename;
        // temp storage
        Storage::disk('s3')->move(
            $request->key,
            $filename
        );

        // $path = Storage::path($pathName);
        return $this->getParsedCSV(10, $filename, $snapshot);
    }

    public function getParsedCSV($sampleCount = 10, $filename, Snapshot $snapshot)
    {
        // Get the file from Amazon S3
        $file = Storage::disk('s3')->get($filename);

        // Process the file content
        $parsedData = LazyCollection::make(function () use ($file) {
            $lines = explode(PHP_EOL, $file);
            foreach ($lines as $line) {
                yield str_getcsv($line);
            }
        });

        $totalCount = $parsedData->count();
        $snapshot->metadata =  [
            'snapshot_file' => $filename,
            'row_count' => $totalCount
        ];
        $snapshot->save();

        $sampleData = $parsedData->take($sampleCount);
        $sampleCount = count($sampleData);

        // You can then proceed to process the entire data
        $parsedData = $sampleData->skip(1)
            ->filter(function ($row) {
                return isset($row[0]) && isset($row[1]);
            })
            ->map(function ($row) {
                return [
                    'voter_id' => $row[0],
                    'voting_power' => $row[1],
                ];
            });

        // Return response as JSON
        return response()->json([
            'total_uploaded' => $parsedData->count(),
            'sample_data' => new Fluent($parsedData)
        ]);
    }

    public function cancelParsedCSV(Request $request, Snapshot $snapshot)
    {
        Storage::disk('s3')->delete($request->input('filename'));
        $snapshot->metadata = null;
        $snapshot->save();
    }

    public function uploadCSV(Request $request, Snapshot $snapshot)
    {
        $fileName = $request->input('filename');

        //save snapshot's metadata about fil
        $this->updateSnapshotModel($snapshot);
        SyncVotingPowersFIleJob::dispatchAfterResponse(
            $snapshot,
            $fileName
        );

        return response()->json([
            'true' => false,
        ]);
    }

    protected function updateSnapshotModel(Snapshot $snapshot)
    {
        $snap = Snapshot::byHash($snapshot->hash);
        $snap->status = ModelStatusEnum::PENDING->value;
        $snap->save();
    }

    protected function getFirstLine($filePath)
    {
        $lines = $this->getFileLines($filePath);

        return str_getcsv($lines[0]);
    }

    protected function getFileLines($filePath)
    {
        $file = Storage::get($filePath);
        $lines = explode("\n", $file);

        return $lines;
    }
}
