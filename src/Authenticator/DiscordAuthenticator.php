<?php

declare(strict_types=1);

namespace NeiroNetwork\SyncDiscordXbox\Authenticator;

use League\OAuth2\Client\Token\AccessToken;
use NeiroNetwork\SyncDiscordXbox\PageGenerator;
use Wohali\OAuth2\Client\Provider\Discord;
use Wohali\OAuth2\Client\Provider\DiscordResourceOwner;

class DiscordAuthenticator extends AbstractAuthenticator{

	public function __construct(){
		$this->provider = new Discord([
			"clientId" => $_ENV["DISCORD_CLIENT_ID"],
			"clientSecret" => $_ENV["DISCORD_CLIENT_SECRET"],
			"redirectUri" => $_ENV["DISCORD_REDIRECT_URI"],
		]);
		$this->scope = "identify";
	}

	protected function authenticateUser(AccessToken $token): DiscordResourceOwner{
		/** @var DiscordResourceOwner $user */
		$user = $this->provider->getResourceOwner($token);
		return $user;
	}
}