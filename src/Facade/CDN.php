<?php

namespace TheWebbakery\CDN\Facade;

use Illuminate\Support\Facades\Facade;
use TheWebbakery\CDN\CDNClient;

/**
 * @mixin SimplicateClient
 */
class CDN extends Facade
{
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		return CDNClient::class;
	}
}
