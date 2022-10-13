<?php

declare(strict_types=1);

require_once dirname(__DIR__) . "/vendor/autoload.php";

use Discord\Discord;
use Discord\WebSockets\Event;
use Illuminate\Database\Capsule\Manager as Capsule;
use NeiroNetwork\SyncDiscordXbox\ApplicationInitializer;

ApplicationInitializer::run();

$discord = new Discord(["token" => $_ENV["DISCORD_BOT_TOKEN"]]);
$discord->on(Event::GUILD_MEMBER_ADD, function(Discord $discord){
	$account = Capsule::table("accounts")->where("discord", "=", (int) $discord->id)->get();
});