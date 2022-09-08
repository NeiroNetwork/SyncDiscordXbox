<?php

declare(strict_types=1);

namespace NeiroNetwork\SyncDiscordXbox;

use GuzzleHttp\Command\Exception\CommandClientException;
use Illuminate\Database\Capsule\Manager as Capsule;
use NeiroNetwork\SyncDiscordXbox\Account\DiscordAccount;
use NeiroNetwork\SyncDiscordXbox\Account\XboxAccount;
use NeiroNetwork\SyncDiscordXbox\Wrapper\DiscordGuildBot;

class AccountSynchronizer{

	public static function sync(DiscordAccount $account1, XboxAccount $account2) : never{
		self::storeData($account1->id, $account2->id);
		self::modifyUser($account1->id, (int) $_ENV["MEMBER_ROLE_ID"], $account2->name);

		PageGenerator::DIALOG("認証に成功しました", "アカウントの連携が完了しました。安全にこのページを閉じることができます。");
	}

	private static function storeData(int $duid, int $xuid) : void{
		try{
			Capsule::table("accounts")->upsert(["discord" => $duid, "xuid" => $xuid], "discord");
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