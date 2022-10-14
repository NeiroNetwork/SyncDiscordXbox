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

	/**
	 * @throws IdentityProviderException
	 */
	abstract public function getAccount(string $refreshToken = null) : AccountBase;

	/**
	 * アクセストークンを使用してユーザーのセンシティブなデータを取得します
	 */
	abstract protected function fetchUserData(AccessToken $token) : mixed;

	/**
	 * @throws IdentityProviderException
	 */
	protected function getAccessToken(?string $refreshToken) : AccessToken{
		return match(true){
			$refreshToken !== null
				=> $this->provider->getAccessToken("refresh_token", ["refresh_token" => $refreshToken]),
			isset($_GET["code"], $_GET["state"], $_SESSION["oauth2state"]) && $_GET["state"] === $_SESSION["oauth2state"]
				=> $this->provider->getAccessToken("authorization_code", ["code" => $_GET["code"]]),
			// ↓ これはいいのか？？？
			default => throw new IdentityProviderException("Must begin the authentication flow", 0, ""),
		};
	}

	public function startWebAuthentication() : never{
		unset($_SESSION["oauth2state"]);
		$authorizationUrl = $this->provider->getAuthorizationUrl(["scope" => $this->scope]);
		$_SESSION['oauth2state'] = $this->provider->getState();
		header("Location: $authorizationUrl");
		exit;
	}
}