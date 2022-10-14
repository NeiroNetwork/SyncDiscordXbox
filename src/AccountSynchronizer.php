<?php

declare(strict_types=1);

namespace NeiroNetwork\SyncDiscordXbox;

use GuzzleHttp\Command\Exception\CommandClientException;
use Illuminate\Database\Capsule\Manager as Capsule;
use NeiroNetwork\SyncDiscordXbox\Account\DiscordAccount;
use NeiroNetwork\SyncDiscordXbox\Account\XboxAccount;
use NeiroNetwork\SyncDiscordXbox\Wrapper\DiscordGuildBot;

class AccountSynchronizer{

	public static function sync(DiscordAccount $discord, XboxAccount $xbox) : never{
		self::storeData($discord, $xbox);
		self::modifyUser($discord->id, (int) $_ENV["MEMBER_ROLE_ID"], $xbox->name);

		PageGenerator::DIALOG("認証に成功しました", "アカウントの連携が完了しました。安全にこのページを閉じることができます。");
	}

	private static function storeData(DiscordAccount $discord, XboxAccount $xbox) : void{
		try{
			Capsule::table("accounts")->upsert(["discord" => $discord->id, "xuid" => $xbox->id], "discord");
			Capsule::table("discord_tokens")->upsert(["id" => $discord->id, "refresh_token" => $discord->refreshToken], "id");
			Capsule::table("azure_tokens")->upsert(["xuid" => $xbox->id, "refresh_token" => $xbox->refreshToken], "xuid");
		}catch(\Exception){
			PageGenerator::DIALOG(
				"認証に失敗しました",
				"データベースに内部エラーが発生しました。しばらく待ってから再度お試しください。"
			);
		}
	}

	private static function modifyUser(int $userId, int $roleId, string $nick) : void{
		$bot = new DiscordGuildBot((int) $_ENV["DISCORD_GUILD_ID"], $userId);
		try{
			$bot->addRole($roleId);
			$bot->changeNick($nick);
		}catch(CommandClientException){
			PageGenerator::DIALOG(
				"認証に失敗しました",
				"Discordアカウントとの連携中にエラーが発生しました。もう一度お試しください。"
			);
		}
	}
}