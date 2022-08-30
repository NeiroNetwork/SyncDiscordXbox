<?php

declare(strict_types=1);

use GuzzleHttp\Command\Exception\CommandClientException;
use Illuminate\Database\Capsule\Manager as Capsule;
use RestCord\DiscordClient;

$discord = new DiscordClient(["token" => $_ENV["DISCORD_BOT_TOKEN"]]);
try{
	$discord->guild->addGuildMemberRole([
		"guild.id" => (int) $_ENV["DISCORD_GUILD_ID"],
		"user.id" => (int) $_SESSION["discord_id"],
		"role.id" => (int) $_ENV["MEMBER_ROLE_ID"],
	]);
	$discord->guild->modifyGuildMember([
		"guild.id" => (int) $_ENV["DISCORD_GUILD_ID"],
		"user.id" => (int) $_SESSION["discord_id"],
		"nick" => $_SESSION["gamertag"],
	]);
}catch(CommandClientException $exception){
	session_destroy();
	generatePage("認証に失敗しました", "Discordアカウントとの連携中にエラーが発生しました。最初からやり直してください。");
}

require_once dirname(__DIR__) . "/src/connect_database.php";
Capsule::table("discord_xuid_map")->upsert([
	"discord_id" => $_SESSION["discord_id"],
	"xuid" => $_SESSION["xuid"],
], "discord_id");

session_destroy();
generatePage("認証に成功しました", "アカウントの連携が完了しました。安全にこのページを閉じることができます。");