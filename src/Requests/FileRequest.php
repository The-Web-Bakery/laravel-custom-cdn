<?php

namespace TheWebbakery\CDN\Requests;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Http\UploadedFile;
use TheWebbakery\CDN\Collections\FileCollection;
use TheWebbakery\CDN\Resources\FileResource;

class FileRequest
{

	private PendingRequest $httpClient;

	public function __construct(PendingRequest $client)
	{
		$this->httpClient = $client;
	}

	public function upload(UploadedFile|string $file, ?string $filename = null, ?string $path = null): FileResource
    {
        if(is_a($file, UploadedFile::class)) {
            if(is_null($filename)) {
                $filename = $file->getClientOriginalName();
            }

            $file = $file->getContent();
        }

        if(is_null($filename)) {
            $filename = last(explode('/', $path));
        }

		$request = $this->httpClient->send('POST', '/api/files/upload', [
            'multipart' => [
                [
                    'name' => 'filename',
                    'contents' => $filename,
                ],
                [
                    'name' => 'file',
                    'filename' => $filename,
                    'contents' => $file,
                    'Content-Type' => 'multipart/form-data',
                ],
                [
                    'name' => 'path',
                    'contents' => $path,
                ],
            ],
        ]);

        return FileResource::make($request->collect());
	}

	public function delete(string $path): bool
	{
		$request = $this->httpClient->delete(sprintf('/api/files?path=%s', $path));

        return $request->collect('ok') && $request->successful();
	}

    public function find(string $path): ?FileResource {
        $request = $this->httpClient->get('/api/file', [
            'path' => $path
        ]);

        if($request->notFound()) {
            return null;
        }

        return FileResource::make($request->collect('file'));
    }


	public function all(): ?FileCollection
	{
		$request = $this->httpClient->get('/api/files');

        return FileResource::collection($request->collect('files'));
	}
}
