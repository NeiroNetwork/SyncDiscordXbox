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
	 * @throws CommandClientException
	 * @throws \Exception
	 */
	public static function sync(DiscordAccount $discord, XboxAccount $xbox) : void{
		self::storeData($discord, $xbox);
		self::modifyUser($discord->id, (int) $_ENV["MEMBER_ROLE_ID"], $xbox->name);
	}

	/**
	 * @throws \Exception \PDOException とか \LogicException とか 色々投げてくる
	 */
	private static function storeData(DiscordAccount $discord, XboxAccount $xbox) : void{
		Capsule::table("accounts")->upsert(["discord" => $discord->id, "xuid" => $xbox->id], "discord");
		Capsule::table("discord_tokens")->upsert(["id" => $discord->id, "refresh_token" => $discord->refreshToken], "id");
		Capsule::table("azure_tokens")->upsert(["xuid" => $xbox->id, "refresh_token" => $xbox->refreshToken], "xuid");
	}

	/**
	 * @throws CommandClientException
	 */
	private static function modifyUser(int $userId, int $roleId, string $nick) : void{
		$bot = new DiscordGuildBot((int) $_ENV["DISCORD_GUILD_ID"]);
		$bot->addRole($userId, $roleId);
		$bot->changeNick($userId, $nick);
	}
}