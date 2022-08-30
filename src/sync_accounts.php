<?php

declare(strict_types=1);

use GuzzleHttp\Command\Exception\CommandClientException;
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

// TODO: データベースに登録

session_destroy();
generatePage("認証に成功しました", "アカウントの連携が完了しました。安全にこのページを閉じることができます。");