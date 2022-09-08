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
		// https://github.com/wohali/oauth2-discord-new/issues/10#issuecomment-436761132
		$requestUrl = $this->provider->getResourceOwnerDetailsUrl($token) . "/guilds";
		$guildsRequest = $this->provider->getAuthenticatedRequest("GET", $requestUrl, $token);
		/** @var array $guilds */
		$guilds = $this->provider->getParsedResponse($guildsRequest);

		$guildIds = array_map(fn(array $guild) => $guild["id"], $guilds);
		if(!in_array($_ENV["DISCORD_GUILD_ID"], $guildIds, true)){
			PageGenerator::DIALOG(
				"認証に失敗しました",
				"あなたのDiscordアカウントは音色ネットワークのDiscordサーバーに参加していません。"
				. "アカウントを連携するにはサーバーに参加する必要があります。"
			);
		}

		/** @var DiscordResourceOwner $user */
		$user = $this->provider->getResourceOwner($token);
		return $user;
	}
}