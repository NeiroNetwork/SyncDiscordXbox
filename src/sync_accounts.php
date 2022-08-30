<?php

declare(strict_types=1);

use GuzzleHttp\Command\Exception\CommandClientException;
use Illuminate\Database\Capsule\Manager as Capsule;
use RestCord\DiscordClient;

$guildId = (int) $_ENV["DISCORD_GUILD_ID"];
$roleId = (int) $_ENV["MEMBER_ROLE_ID"];
$userId = (int) $_SESSION["discord_id"];
$xuid = (int) $_SESSION["xuid"];
$gamertag = $_SESSION["gamertag"];

$discord = new DiscordClient(["token" => $_ENV["DISCORD_BOT_TOKEN"]]);
try{
	$discord->guild->addGuildMemberRole(["guild.id" => $guildId, "user.id" => $userId, "role.id" => $roleId]);
	$discord->guild->modifyGuildMember(["guild.id" => $guildId, "user.id" => $userId, "nick" => $gamertag]);
}catch(CommandClientException $exception){
	session_destroy();
	generatePage("認証に失敗しました", "Discordアカウントとの連携中にエラーが発生しました。最初からやり直してください。");
}

try{
	require_once dirname(__DIR__) . "/src/connect_database.php";
	Capsule::table("discord_xuid_map")->upsert(["discord_id" => $userId, "xuid" => $xuid], "discord_id");
}catch(PDOException){
	session_destroy();
	$discord->guild->removeGuildMemberRole(["guild.id" => $guildId, "user.id" => $userId, "role.id" => $roleId]);
	$discord->guild->modifyGuildMember(["guild.id" => $guildId, "user.id" => $userId, "nick" => ""]);
	generatePage(
		"認証に失敗しました",
		"データベースに内部エラーが発生しました。しばらく待ってから再度お試しください。解決しない場合はお問い合わせください。"
	);
}

session_destroy();
generatePage("認証に成功しました", "アカウントの連携が完了しました。安全にこのページを閉じることができます。");