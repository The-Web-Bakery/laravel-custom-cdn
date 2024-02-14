<?php

return [
	"base_url" => env("CDN_BASE_URL"),
	"authentication" => [
		"admin" => env("CDN_AUTHENTICATION_ADMIN"),
		"id" => env("CDN_AUTHENTICATION_ID"),
		"secret" => env("CDN_AUTHENTICATION_SECRET"),
	],
];
