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
    public function parseCSV(Request $request)
    {
        $file = $request->file('file');
        $directory = 'voting_powers';
        $pathName = "voting_powers/".$file->getClientOriginalName();

        //$pathName already exist then delete already existing record
        if ($request->input('count') == '0' && Storage::exists($pathName)) {
            File::delete(Storage::path($pathName));
        };

        //create directory if it doesn't exist
        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }

        $path = Storage::path($pathName);
        File::append($path, $file->get());
    }

    public function getParsedCSV(Request $request, $filename) {
        $sampleCount = $request->input('count', 10);
        $pathName = "voting_powers/".$filename;
        $path = Storage::path($pathName);

        $parsedSample = LazyCollection::make(function () use ($path, $sampleCount) {
            $handle = fopen($path, 'r');

            $count = 0;
            while ((($line = fgetcsv($handle, null)) !== false) && $count <= $sampleCount) {
                yield $line;
                $count++;
            }

            fclose($handle);
        })
        ->skip(1)
        ->map(function($row) {
            return [
                'voter_id' => $row[0],
                'voting_power' => $row[1],
            ];
        });

        return response()->json([
            'total_uploaded' => count(file($path)) - 1,
            'sample_data' => new Fluent($parsedSample)
        ]);

    }

    public function cancelParsedCSV(Request $request) {
        $filePath = Storage::path('voting_powers/'.$request->input('filename'));
        File::delete($filePath);
    }

    public function uploadCSV(Request $request, Snapshot $snapshot)
    {
        $response = Gate::inspect('update', Snapshot::class);

        $fileName = $request->input('filename');
        $filePath = 'voting_powers/'.$fileName;
        $storagePath = Storage::path($filePath);

        //save snapshot's metadata about fil
        $this->updateSnapshotModel($snapshot, $storagePath, $fileName);
        SyncVotingPowersFIleJob::dispatchAfterResponse(
            $snapshot,
            $storagePath
        );

        return response()->json([
            'true' => false,
        ]);
    }

    protected function updateSnapshotModel(Snapshot $snapshot, $storagePath, $fileName)
    {
        $snap = Snapshot::byHash($snapshot->hash);
        $snap->status = ModelStatusEnum::PENDING->value;
        $snap->metadata =  [
            'snapshot_file' => $fileName,
            'row_count' => count(file($storagePath)) - 1
        ];
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
