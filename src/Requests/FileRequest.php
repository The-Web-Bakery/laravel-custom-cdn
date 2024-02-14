<?php

namespace TheWebbakery\CDN\Requests;

use Illuminate\Http\Client\PendingRequest;
use TheWebbakery\CDN\Collections\FileCollection;
use TheWebbakery\CDN\Resources\FileResource;

class FileRequest
{

	private PendingRequest $httpClient;

	public function __construct(PendingRequest $client)
	{
		$this->httpClient = $client;
	}

	public function upload(\Illuminate\Http\UploadedFile $file, ?string $filename = null, ?string $path = null): ?FileResource
	{
        if(is_null($filename)) {
           $filename = $file->getClientOriginalName();
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
                    'contents' => $file->getContent(),
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


	public function all(): ?FileCollection
	{
		$request = $this->httpClient->get('/api/files');

        return FileResource::collection($request->collect('files'));
	}
}
