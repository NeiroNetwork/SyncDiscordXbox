<?php

declare(strict_types=1);

use RestCord\DiscordClient;

$discord = new DiscordClient(["token" => $_ENV["DISCORD_BOT_TOKEN"]]);
$discord->guild->addGuildMemberRole([
	"guild.id" => $_ENV["DISCORD_GUILD_ID"],
	"user.id" => $_SESSION["discord_id"],
	"role.id" => $_ENV["MEMBER_ROLE_ID"],
]);