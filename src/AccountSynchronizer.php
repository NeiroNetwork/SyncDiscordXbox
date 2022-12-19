<?php

declare(strict_types=1);

namespace NeiroNetwork\SyncDiscordXbox;

use GuzzleHttp\Command\Exception\CommandClientException;
use Illuminate\Database\Capsule\Manager as Capsule;
use NeiroNetwork\SyncDiscordXbox\Account\DiscordAccount;
use NeiroNetwork\SyncDiscordXbox\Account\XboxAccount;
use NeiroNetwork\SyncDiscordXbox\Wrapper\DiscordGuildBot;

class AccountSynchronizer{

	/**
	 * @throws \PDOException
	 * @throws \LogicException
	 */
	public static function storeData(DiscordAccount $discord, XboxAccount $xbox, string $ip, string $fingerprint) : void{
		Capsule::table("linked_data")->upsert(["discord" => $discord->id, "xuid" => $xbox->id, "ip" => $ip, "fingerprint" => $fingerprint], "discord");
		Capsule::table("discord_tokens")->upsert(["id" => $discord->id, "refresh_token" => $discord->refreshToken], "id");
		Capsule::table("azure_tokens")->upsert(["xuid" => $xbox->id, "refresh_token" => $xbox->refreshToken], "xuid");
	}

	/**
	 * @throws CommandClientException
	 */
	public static function modifyUser(int $userId, int $roleId, string $nick) : void{
		$bot = new DiscordGuildBot((int) $_ENV["DISCORD_GUILD_ID"]);
		$bot->addRole($userId, $roleId);
		$bot->changeNick($userId, $nick);
	}
}