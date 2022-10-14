<?php

declare(strict_types=1);

namespace NeiroNetwork\SyncDiscordXbox\Authenticator;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use NeiroNetwork\SyncDiscordXbox\Account\DiscordAccount;
use Wohali\OAuth2\Client\Provider\Discord;
use Wohali\OAuth2\Client\Provider\DiscordResourceOwner;

class DiscordAuthenticator extends AuthenticatorBase{

	public function __construct(){
		$this->provider = new Discord([
			"clientId" => $_ENV["DISCORD_CLIENT_ID"],
			"clientSecret" => $_ENV["DISCORD_CLIENT_SECRET"],
			"redirectUri" => $_ENV["DISCORD_REDIRECT_URI"],
		]);
		$this->scope = "identify";
	}

	public function getAccount() : DiscordAccount{
		$token = $this->getAccessToken();

		try{
			$data = $this->fetchUserData($token);
		}catch(IdentityProviderException){
			$this->startAuthentication();
		}

		return new DiscordAccount($data, $token->getRefreshToken());
	}

	protected function fetchUserData(AccessToken $token) : DiscordResourceOwner{
		/** @var DiscordResourceOwner $user */
		$user = $this->provider->getResourceOwner($token);
		return $user;
	}
}