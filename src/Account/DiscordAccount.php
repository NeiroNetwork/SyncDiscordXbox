<?php

declare(strict_types=1);

namespace NeiroNetwork\SyncDiscordXbox\Account;

use NeiroNetwork\SyncDiscordXbox\Wrapper\DiscordGuildBot;
use Wohali\OAuth2\Client\Provider\DiscordResourceOwner;

class DiscordAccount extends AccountBase{

	public readonly int $id;
	public readonly string $name;
	public readonly string $avatar;
	public readonly bool $serverJoined;

	public function __construct(DiscordResourceOwner $discord, string $refreshToken = null){
		parent::__construct($refreshToken);

		$this->id = (int) $discord->getId();
		$this->name = $discord->getUsername() . "#" . $discord->getDiscriminator();
		$this->avatar = $discord->getAvatarHash() === null
			// https://stackoverflow.com/a/54569844
			? "https://cdn.discordapp.com/embed/avatars/" . $discord->getDiscriminator() % 5 . ".png"
			// https://github.com/wohali/oauth2-discord-new/issues/14#issuecomment-432018789
			: "https://cdn.discordapp.com/avatars/{$discord->getId()}/{$discord->getAvatarHash()}."
			// a_ で始まる場合はGIFアイコン (どこかでそんなような記述を見つけたのだが、後から見つけるのは無理だった)
			. (str_starts_with($discord->getAvatarHash(), "a_") ? "gif" : "png");
		$this->serverJoined = (new DiscordGuildBot((int) $_ENV["DISCORD_GUILD_ID"], $this->id))->fetchMember() !== null;
	}
}