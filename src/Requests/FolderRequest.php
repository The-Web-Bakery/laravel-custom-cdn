<?php

namespace TheWebbakery\CDN\Requests;

use Illuminate\Http\Client\PendingRequest;
use TheWebbakery\CDN\Collections\FileCollection;
use TheWebbakery\CDN\Collections\FolderCollection;
use TheWebbakery\CDN\Resources\FileResource;
use TheWebbakery\CDN\Resources\FolderResource;

class FolderRequest
{

	private PendingRequest $httpClient;

	public function __construct(PendingRequest $client)
	{
		$this->httpClient = $client;
	}

	public function delete(string $path): bool
	{
		$request = $this->httpClient->delete(sprintf('/api/files?path=%s', $path));

        return $request->collect('ok') && $request->successful();
	}

    public function clearAll(): bool {
        $request = $this->httpClient->delete('/api/files/clear');

        return $request->collect('ok') && $request->successful();
    }

    public function create(string $name, string $path = '') {
        $request = $this->httpClient->post('/api/files/folder', [
            'name' => $name,
            'path' => $path,
        ]);

        return FolderResource::make($request->collect('folder'));
    }


	public function all(string $path = null, bool $recursive = false): ?FolderResource
	{
		$request = $this->httpClient->get('/api/files/folders', [
            'path' => $path,
            'recursive' => $recursive
        ]);

        return FolderResource::make(
            $request->collect('root_folder'),
            FolderResource::collection($request->collect('folders')),
            FileResource::collection($request->collect('files')),
        );
	}
}
