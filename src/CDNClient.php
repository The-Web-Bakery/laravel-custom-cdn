<?php

namespace TheWebbakery\CDN;

use Illuminate\Http\Client\Response;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use TheWebbakery\CDN\Requests\ApplicationRequest;
use TheWebbakery\CDN\Requests\FileRequest;
use TheWebbakery\CDN\Requests\FolderRequest;

class CDNClient
{
	// API Version
	const VERSION = "2";

	protected PendingRequest $httpClient;

	public function __construct(string $appId = null, string $appSecret = null)
	{
		$this->httpClient = $this->buildClient(
            $appId ?: config('cdn.authentication.id'),
            $appSecret ?: config('cdn.authentication.secret'),
        );
	}

    protected function buildClient(string $appId, string $appSecret) {
        return Http::baseUrl($this->getBaseUri())
            ->acceptJson()
            ->throwIf(config('cdn.throw_exceptions'))
            ->withHeaders([
                "X-Admin-Secret" => config("cdn.authentication.admin"),
                "X-App-Id" => $appId,
                "X-App-Secret" => $appSecret,
            ]);
    }

	public function getBaseUri(): string
	{
		return config('cdn.base_url');
	}

    public function changeApplicationCredentials(string $id, string $secret): PendingRequest {
       return $this->httpClient->withHeaders([
           "X-App-Id" => $id,
           "X-App-Secret" => $secret,
       ]);
    }

    public function ping(): \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response
    {
        return $this->httpClient->get('');
    }


    public function isLive() {
        return $this->ping()->successful();
    }

    public function isDown() {
        return !$this->isLive();
    }

	public function applications(): ApplicationRequest
	{
		return new ApplicationRequest($this->httpClient);
	}

    public function files(): FileRequest {
        return new FileRequest($this->httpClient);
    }

    public function folders(): FolderRequest {
        return new FolderRequest($this->httpClient);
    }
}
