<?php

declare(strict_types=1);

require_once dirname(__DIR__) . "/vendor/autoload.php";

require_once dirname(__DIR__) . "/src/dotenv_validator.php";

session_start();

if(isset($_GET["error"])) {
	header("Location: discord:///channels/" . $_ENV["DISCORD_GUILD_ID"]);
	exit;
}

// Discordの認証
if(empty($_SESSION["discord_id"]) || !is_string($_SESSION["discord_id"])){
	$result = require_once dirname(__DIR__) . "/src/discord_oauth.php";
	$_SESSION["discord_id"] = $result === 1 ? exit : $result;
}

// Xbox Liveの認証
if(!empty($_SESSION["discord_id"])){
	$result = require_once dirname(__DIR__) . "/src/xboxlive_oauth.php";
	$_SESSION["xuid"] = $result === 1 ? exit : $result[0];
	$_SESSION["gamertag"] = $result[1];
}

// 両方が揃った
if(is_string($_SESSION["discord_id"]) && is_string($_SESSION["xuid"]) && is_string($_SESSION["gamertag"])){
	require_once dirname(__DIR__) . "/src/sync_accounts.php";
	session_destroy();
}else{
	echo "Something went wrong!";
}