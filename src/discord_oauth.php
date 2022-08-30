<?php

declare(strict_types=1);

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Wohali\OAuth2\Client\Provider\Discord;
use Wohali\OAuth2\Client\Provider\DiscordResourceOwner;

$provider = new Discord([
	"clientId" => $_ENV["DISCORD_CLIENT_ID"],
	"clientSecret" => $_ENV["DISCORD_CLIENT_SECRET"],
	"redirectUri" => $_ENV["DISCORD_REDIRECT_URI"],
]);

if(isset($_GET["code"], $_GET["state"], $_SESSION["oauth2state"]) && $_GET["state"] === $_SESSION["oauth2state"]){
	try{
		$token = $provider->getAccessToken("authorization_code", ["code" => $_GET["code"]]);

		// https://github.com/wohali/oauth2-discord-new/issues/10#issuecomment-436761132
		$requestUrl = $provider->getResourceOwnerDetailsUrl($token) . "/guilds";
		$guildsRequest = $provider->getAuthenticatedRequest("GET", $requestUrl, $token);
		/** @var array $guilds */
		$guilds = $provider->getParsedResponse($guildsRequest);

		$guildIds = array_map(fn(array $guild) => $guild["id"], $guilds);
		if(!in_array($_ENV["DISCORD_GUILD_ID"], $guildIds, true)) {
			generatePage(
				"認証がキャンセルされました",
				"あなたは音色ネットワークのDiscordサーバーに参加していません。
				アカウントを連携するには、音色ネットワークのDiscordサーバーに参加する必要があります。"
			);
		}

		/** @var DiscordResourceOwner $user */
		$user = $provider->getResourceOwner($token);
		return $user->getId();
	}catch(IdentityProviderException){}
}

unset($_SESSION["oauth2state"]);
$authorizationUrl = $provider->getAuthorizationUrl(["scope" => ["identify", "guilds"]]);
$_SESSION['oauth2state'] = $provider->getState();
header("Location: $authorizationUrl");