<?php

declare(strict_types=1);

namespace NeiroNetwork\SyncDiscordXbox\Authenticator;

use BigPino67\OAuth2\XBLive\Client\Provider\Profiles\Models\Profile;
use BigPino67\OAuth2\XBLive\Client\Provider\Profiles\ProfilesProvider;
use BigPino67\OAuth2\XBLive\Client\Provider\XBLive;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use NeiroNetwork\SyncDiscordXbox\Account\XboxAccount;

class XboxliveAuthenticator extends AuthenticatorBase{

	public function __construct(){
		$this->provider = new XBLive([
			"clientId" => $_ENV["XBL_CLIENT_ID"],
			"clientSecret" => $_ENV["XBL_CLIENT_SECRET"],
			"redirectUri" => $_ENV["XBL_REDIRECT_URI"],
		]);
		$this->scope = "xbl.signin offline_access";
	}

	public function getAccount() : XboxAccount{
		$token = $this->getAccessToken();

		try{
			$data = $this->fetchUserData($token);
		}catch(IdentityProviderException){
			$this->startAuthentication();
		}

		return new XboxAccount($data, $token->getRefreshToken());
	}

	protected function fetchUserData(AccessToken $token): Profile{
		$token = $this->provider->getXstsToken($this->provider->getXasuToken($token));
		$profiles = new ProfilesProvider($token);

		/** @var Profile $profile */
		$profile = $profiles->getLoggedUserProfile();
		return $profile;
	}
}