<?php

declare(strict_types=1);

use BigPino67\OAuth2\XBLive\Client\Provider\Profiles\Models\Profile;
use BigPino67\OAuth2\XBLive\Client\Provider\Profiles\ProfilesProvider;
use BigPino67\OAuth2\XBLive\Client\Provider\XBLive;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

$provider = new XBLive([
	"clientId" => $_ENV["XBL_CLIENT_ID"],
	"clientSecret" => $_ENV["XBL_CLIENT_SECRET"],
	"redirectUri" => $_ENV["XBL_REDIRECT_URI"],
]);

if(isset($_GET["code"], $_GET["state"], $_SESSION["oauth2state"]) && $_GET["state"] === $_SESSION["oauth2state"]){
	try{
		$token = $provider->getAccessToken("authorization_code", ["code" => $_GET["code"]]);
		$token = $provider->getXstsToken($provider->getXasuToken($token));
		$profiles = new ProfilesProvider($token);
		/** @var Profile $profile */
		$profile = $profiles->getLoggedUserProfile();

		return [$profile->getId(), $profile->getSettings()->getGamertag()];
	}catch(IdentityProviderException){}
}

unset($_SESSION["oauth2state"]);
$authorizationUrl = $provider->getAuthorizationUrl(["scope" => "xbl.signin"]);
$_SESSION['oauth2state'] = $provider->getState();
header("Location: $authorizationUrl");
