<?php

declare(strict_types=1);

namespace NeiroNetwork\SyncDiscordXbox\Authenticator;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use NeiroNetwork\SyncDiscordXbox\Account\AccountBase;

abstract class AuthenticatorBase{

	protected AbstractProvider $provider;

	protected string $scope;

	abstract public function getAccount() : AccountBase;

	/**
	 * アクセストークンを使用してユーザーのセンシティブなデータを取得します
	 * @throws IdentityProviderException
	 */
	abstract protected function fetchUserData(AccessToken $token) : mixed;

	protected function getAccessToken() : AccessToken{
		if(isset($_GET["code"], $_GET["state"], $_SESSION["oauth2state"]) && $_GET["state"] === $_SESSION["oauth2state"]){
			try{
				return $this->provider->getAccessToken("authorization_code", ["code" => $_GET["code"]]);
			}catch(IdentityProviderException){
			}
		}

		$this->startAuthentication();
	}

	protected function startAuthentication() : never{
		unset($_SESSION["oauth2state"]);
		$authorizationUrl = $this->provider->getAuthorizationUrl(["scope" => $this->scope]);
		$_SESSION['oauth2state'] = $this->provider->getState();
		header("Location: $authorizationUrl");
		exit;
	}
}