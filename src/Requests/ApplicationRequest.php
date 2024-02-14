<?php

namespace TheWebbakery\CDN\Requests;

use Illuminate\Http\Client\PendingRequest;
use TheWebbakery\CDN\Collections\ApplicationCollection;
use TheWebbakery\CDN\Resources\ApplicationResource;

class ApplicationRequest
{

	private PendingRequest $httpClient;

	public function __construct(PendingRequest $client)
	{
		$this->httpClient = $client;
	}

	public function create(array $attributes): ?ApplicationResource
	{
		$request = $this->httpClient->post('/api/applications/create', $attributes);

        return ApplicationResource::make($request->collect('application'));
	}

	public function delete(string $id): bool
	{
		$request = $this->httpClient->delete(sprintf('/api/applications/%s', $id));

        return $request->collect('ok') && $request->successful();
	}

	public function find(string $id): ?ApplicationResource
	{
		$request = $this->httpClient->get(sprintf('/api/applications/%s', $id));

        return ApplicationResource::make($request->collect('application'));
	}

	public function all(): ?ApplicationCollection
	{
		$request = $this->httpClient->get('/api/applications');

        return ApplicationResource::collection($request->collect('applications'));
	}
}
