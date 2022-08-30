<?php

declare(strict_types=1);

require_once dirname(__DIR__) . "/vendor/autoload.php";

require_once dirname(__DIR__) . "/src/dotenv_validator.php";
require_once dirname(__DIR__) . "/html/page_generator.php";

session_start();

if(isset($_GET["error"])){
	generatePage(
		"認証がキャンセルされました",
		"アプリケーションの認証中にエラーが発生しました。アカウントの連携はまだ完了していません。"
	);
}

// Discordの認証
if(empty($_SESSION["discord_id"]) || !is_string($_SESSION["discord_id"])){
	$result = require_once dirname(__DIR__) . "/src/discord_oauth.php";
	$_SESSION["discord_id"] = $result === 1 ? exit : $result;
}

// Xbox Liveの認証
if(!empty($_SESSION["discord_id"]) && empty($_SESSION["xuid"])){
	$result = require_once dirname(__DIR__) . "/src/xboxlive_oauth.php";
	$_SESSION["xuid"] = $result === 1 ? exit : $result[0];
	$_SESSION["gamertag"] = $result[1];
}

// 両方が揃った
if(is_string($_SESSION["discord_id"]) && is_string($_SESSION["xuid"]) && is_string($_SESSION["gamertag"])){
	require_once dirname(__DIR__) . "/src/sync_accounts.php";
	// 到達不能
}else{
	session_destroy();
	generatePage("認証に失敗しました", "想定されていないエラーが発生しました。最初からやり直してください。");
}