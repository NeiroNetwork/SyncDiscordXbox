<?php

declare(strict_types=1);

require_once dirname(__DIR__) . "/vendor/autoload.php";

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();
$dotenv->required(["XBL_CLIENT_ID",
	"XBL_CLIENT_SECRET",
	"XBL_REDIRECT_URI",
	"DISCORD_CLIENT_ID",
	"DISCORD_CLIENT_SECRET",
	"DISCORD_REDIRECT_URI",
	"DISCORD_SERVER_ID",
])->notEmpty();

session_start();

if(isset($_GET["error"])){
	header("Location: discord:///channels/" . $_ENV["DISCORD_SERVER_ID"]);
	exit;
}

if(!isset($_SESSION["discord_id"])){
	$result = require_once dirname(__DIR__) . "/src/discord_oauth.php";
	$_SESSION["discord_id"] = $result !== 1 ?: exit;
}

echo "Done: Discord";
// TODO: ユーザーがDiscordサーバーに参加しているかチェック