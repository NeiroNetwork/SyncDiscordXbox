<?php

declare(strict_types=1);

namespace NeiroNetwork\SyncDiscordXbox\Authenticator;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;

abstract class AbstractAuthenticator{

	protected AbstractProvider $provider;

	protected string $scope;

	public function auth() : mixed{
		if(isset($_GET["code"], $_GET["state"], $_SESSION["oauth2state"]) && $_GET["state"] === $_SESSION["oauth2state"]){
			try{
				$token = $this->provider->getAccessToken("authorization_code", ["code" => $_GET["code"]]);
				return $this->authenticateUser($token);
			}catch(IdentityProviderException){}
		}

		unset($_SESSION["oauth2state"]);
		$authorizationUrl = $this->provider->getAuthorizationUrl(["scope" => $this->scope]);
		$_SESSION['oauth2state'] = $this->provider->getState();
		header("Location: $authorizationUrl");
		exit;
	}

	/** @throws IdentityProviderException */
	abstract protected function authenticateUser(AccessToken $token) : mixed;
}