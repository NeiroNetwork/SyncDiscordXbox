<?php

declare(strict_types=1);

namespace NeiroNetwork\SyncDiscordXbox;

use BigPino67\OAuth2\XBLive\Client\Provider\Profiles\Models\Profile;
use GuzzleHttp\Command\Exception\CommandClientException;
use Illuminate\Database\Capsule\Manager as Capsule;
use RestCord\DiscordClient;
use Wohali\OAuth2\Client\Provider\DiscordResourceOwner;

class AccountSynchronizer{

	public static function sync(DiscordResourceOwner $account1, Profile $account2) : never{
		$duid = (int) $account1->getId();
		self::storeData($duid, (int) $account2->getId());
		self::modifyUser($duid, (int) $_ENV["MEMBER_ROLE_ID"], $account2->getSettings()->getGamertag());

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
		$discord = new DiscordClient(["token" => $_ENV["DISCORD_BOT_TOKEN"]]);
		$base = ["guild.id" => (int) $_ENV["DISCORD_GUILD_ID"], "user.id" => $userId];

		try{
			$discord->guild->addGuildMemberRole([...$base, "role.id" => $roleId]);
			$discord->guild->modifyGuildMember([...$base, "nick" => $nick]);
		}catch(CommandClientException){
			PageGenerator::DIALOG(
				"認証に失敗しました",
				"Discordアカウントとの連携中にエラーが発生しました。もう一度お試しください。"
			);
		}
	}
}