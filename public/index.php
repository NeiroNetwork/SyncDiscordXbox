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

if(isset($_GET["error"])) {
	header("Location: discord:///channels/" . $_ENV["DISCORD_SERVER_ID"]);
	exit;
}

if(empty($_SESSION["discord_id"]) || !is_string($_SESSION["discord_id"])){
	$result = require_once dirname(__DIR__) . "/src/discord_oauth.php";
	$_SESSION["discord_id"] = $result === 1 ? exit : $result;
}
if(!empty($_SESSION["discord_id"])){
	$result = require_once dirname(__DIR__) . "/src/xboxlive_oauth.php";
	$_SESSION["xuid"] = $result === 1 ? exit : $result[0];
	$_SESSION["gamertag"] = $result[1];
}
if(is_string($_SESSION["discord_id"]) && is_string($_SESSION["xuid"]) && is_string($_SESSION["gamertag"])){
	echo "<pre>";
	var_dump($_SESSION);
	session_destroy();
}else{
	echo "Something went wrong!";
}