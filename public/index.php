<?php

declare(strict_types=1);

require_once dirname(__DIR__) . "/vendor/autoload.php";

use BigPino67\OAuth2\XBLive\Client\Provider\Profiles\Models\Profile;
use NeiroNetwork\SyncDiscordXbox\AccountSynchronizer;
use NeiroNetwork\SyncDiscordXbox\ApplicationInitializer;
use NeiroNetwork\SyncDiscordXbox\Authenticator\DiscordAuthenticator;
use NeiroNetwork\SyncDiscordXbox\Authenticator\XboxliveAuthenticator;
use NeiroNetwork\SyncDiscordXbox\PageGenerator;
use Wohali\OAuth2\Client\Provider\DiscordResourceOwner;

ApplicationInitializer::run();
session_start();

if(isset($_GET["error"])){
	PageGenerator::DIALOG(
		"認証がキャンセルされました",
		"アプリケーションの認証中にエラーが発生しました。アカウントの連携はまだ完了していません。"
	);
}

// Discordの認証
if(empty($_SESSION["step_one"])){
	$_SESSION["step_one"] = (new DiscordAuthenticator())->auth();
}

// Xbox Liveの認証
if(!empty($_SESSION["step_one"]) && empty($_SESSION["step_two"])){
	$_SESSION["step_two"] = (new XboxliveAuthenticator())->auth();
}

// 両方が揃った
if(!empty($_SESSION["step_one"]) && !empty($_SESSION["step_two"])){
	$discord = $_SESSION["step_one"];
	$xbox = $_SESSION["step_two"];
	session_destroy();
	if($discord instanceof DiscordResourceOwner && $xbox instanceof Profile){
		AccountSynchronizer::sync($discord, $xbox);
	}else{
		PageGenerator::DIALOG(
			"認証フローが失敗しました",
			"プログラムに問題が発生しました：想定されていないデータです。開発者に連絡してください。"
		);
	}
}

PageGenerator::DIALOG(
	"認証フローが失敗しました",
	"プログラムに問題が発生しました：想定されていない処理が発生しました。開発者に連絡してください。"
);