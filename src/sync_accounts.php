<?php

declare(strict_types=1);

use RestCord\DiscordClient;

$discord = new DiscordClient(["token" => $_ENV["DISCORD_BOT_TOKEN"]]);
// 既についているロールを消してしまうが、一度に変更した方が速い…
$discord->guild->modifyGuildMember([
	"guild.id" => (int) $_ENV["DISCORD_GUILD_ID"],
	"user.id" => (int) $_SESSION["discord_id"],
	"nick" => $_SESSION["gamertag"],
	"roles" => [(int) $_ENV["MEMBER_ROLE_ID"]],
]);