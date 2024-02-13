<?php

namespace TheWebbakery\CDN;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class CDNService
{
    // API Version
    const VERSION = "2";

    protected PendingRequest $httpClient;

    public function __construct()
    {
        $this->httpClient = Http::baseUrl($this->getBaseUri())
            ->acceptJson()
            ->asJson()
            ->withHeaders([
                "Authentication-Key" => config("laravel-simplicate.authentication.key"),
                "Authentication-Secret" => config("laravel-simplicate.authentication.secret"),
            ]);
    }

    public function getBaseUri(): string
    {
        return sprintf("https://%s.simplicate.nl/api/v%s", config("laravel-simplicate.domain"), self::VERSION);
    }
}
