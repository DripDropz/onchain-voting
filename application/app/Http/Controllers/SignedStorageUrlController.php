<?php

namespace App\Http\Controllers;

use Aws\S3\S3Client;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Config;
use Laravel\Vapor\Contracts\SignedStorageUrlController as SignedStorageUrlControllerContract;

class SignedStorageUrlController extends \Illuminate\Routing\Controller implements SignedStorageUrlControllerContract
{
    /**
     * Create a new signed URL.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Config::set('filesystems.disks.minio.endpoint', env('MINIO_ENDPOINT'));

        Gate::authorize('uploadFiles', [
            $request->user(),
            $bucket = $request->input('bucket') ?: config('filesystems.disks.minio.bucket'),
        ]);

        $client = $this->storageClient();

        $uuid = (string) Str::uuid();

        $signedRequest = $client->createPresignedRequest(
            $this->createCommand($request, $client, $bucket, $key = ('tmp/' . $uuid)),
            '+10 minutes'
        );

        return response()->json([
            'uuid' => $uuid,
            'bucket' => $bucket,
            'key' => $key,
            'url' => (string) $signedRequest->getUri(),
            'headers' => $this->headers($request, $signedRequest),
        ], 201);
    }

    /**
     * Create a command for the PUT operation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Aws\S3\S3Client  $client
     * @param  string  $bucket
     * @param  string  $key
     * @return \Aws\Command
     */
    protected function createCommand(Request $request, S3Client $client, $bucket, $key)
    {
        return $client->getCommand('putObject', array_filter([
            'Bucket' => $bucket,
            'Key' => $key,
        ]));
    }

    /**
     * Get the headers that should be used when making the signed request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \GuzzleHttp\Psr7\Request
     * @return array
     */
    protected function headers(Request $request, $signedRequest)
    {
        return array_merge(
            $signedRequest->getHeaders(),
            [
                'Content-Type' => $request->input('content_type') ?: 'application/octet-stream',
            ]
        );
    }

    /**
     * Get the S3 storage client instance.
     *
     * @return \Aws\S3\S3Client
     */
    protected function storageClient()
    {
        $config = [
            'region' => config('filesystems.disks.minio.region'),
            'version' => 'latest',
            'use_path_style_endpoint' => true,
            'url' => config('filesystems.disks.minio.endpoint'),
            'endpoint' => config('filesystems.disks.minio.endpoint'),
            'credentials' => [
                'key' => config('filesystems.disks.minio.key'),
                'secret' => config('filesystems.disks.minio.secret'),
            ],
        ];

        return S3Client::factory($config);
    }
}
