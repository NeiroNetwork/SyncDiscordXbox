<?php

declare(strict_types=1);

require_once dirname(__DIR__) . "/vendor/autoload.php";

use NeiroNetwork\SyncDiscordXbox\Account\DiscordAccount;
use NeiroNetwork\SyncDiscordXbox\Account\XboxAccount;
use NeiroNetwork\SyncDiscordXbox\AccountSynchronizer;
use NeiroNetwork\SyncDiscordXbox\ApplicationInitializer;
use NeiroNetwork\SyncDiscordXbox\Authenticator\DiscordAuthenticator;
use NeiroNetwork\SyncDiscordXbox\Authenticator\XboxliveAuthenticator;
use NeiroNetwork\SyncDiscordXbox\PageGenerator;

ApplicationInitializer::run();
header("X-Frame-Options: DENY");
session_start();

if(isset($_GET["error"])){
	if($_GET["error"] === "access_denied"){
		PageGenerator::DIALOG(
			"キャンセルされました",
			"アプリケーションの認証がユーザーによってキャンセルされました。アカウントの連携は完了していません。"
		);
	}else{
		PageGenerator::DIALOG(
			"認証がキャンセルされました",
			"アプリケーションの認証中にエラーが発生しました。アカウントの連携は完了していません。"
		);
	}
}

if(isset($_GET["reset"])) session_destroy() && session_start();

// Discordの認証
if(empty($_SESSION["step_one"])){
	$_SESSION["step_one"] = new DiscordAccount((new DiscordAuthenticator())->auth());
}

// Xbox Liveの認証
if(!empty($_SESSION["step_one"]) && empty($_SESSION["step_two"])){
	$_SESSION["step_two"] = new XboxAccount((new XboxliveAuthenticator())->auth());
}

// 両方が揃った
if(!empty($_SESSION["step_one"]) && !empty($_SESSION["step_two"])){
	/** @var DiscordAccount $discord */
	$discord = $_SESSION["step_one"];
	/** @var XboxAccount $xbox */
	$xbox = $_SESSION["step_two"];

	if(isset($_SESSION["sync_prepared"])){
		session_destroy();
		AccountSynchronizer::sync($discord, $xbox);
	}

	$_SESSION["sync_prepared"] = true;
	PageGenerator::CONNECT_CONFIRM($discord->avatar, $discord->name, $xbox->avatar, $xbox->name);
}