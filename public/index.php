<?php

declare(strict_types=1);

require_once dirname(__DIR__) . "/vendor/autoload.php";

use GuzzleHttp\Command\Exception\CommandClientException;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
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
	$denied = $_GET["error"] === "access_denied";
	PageGenerator::DIALOG(
		$denied ? "キャンセルされました" : "認証がキャンセルされました",
		$denied ? "アプリケーションの認証がユーザーによってキャンセルされました。アカウントの連携は完了していません。" : "アプリケーションの認証中にエラーが発生しました。アカウントの連携は完了していません。"
	);
}

if(isset($_GET["reset"])) session_destroy() && session_start();

// Discordの認証
if(empty($_SESSION["step_one"])){
	$authenticator = new DiscordAuthenticator();
	try{
		$discordAccount = $authenticator->getAccount();
	}catch(IdentityProviderException){
		$authenticator->startWebAuthentication();
	}

	if(!$discordAccount->serverJoined){
		PageGenerator::DIALOG(
			"認証に失敗しました",
			"あなたのDiscordアカウントは音色ネットワークのDiscordサーバーに参加していません。アカウントを連携するにはサーバーに参加する必要があります。"
		);
	}

	$_SESSION["step_one"] = $discordAccount;
}

// Xbox Liveの認証
if(!empty($_SESSION["step_one"]) && empty($_SESSION["step_two"])){
	$authenticator = new XboxliveAuthenticator();
	try{
		$_SESSION["step_two"] = $authenticator->getAccount();
	}catch(IdentityProviderException){
		$authenticator->startWebAuthentication();
	}
}

// 両方が揃った
if(!empty($_SESSION["step_one"]) && !empty($_SESSION["step_two"])){
	/** @var DiscordAccount $discord */
	$discord = $_SESSION["step_one"];
	/** @var XboxAccount $xbox */
	$xbox = $_SESSION["step_two"];

	if(isset($_GET["done"])){
		session_destroy();
		try{
			AccountSynchronizer::sync($discord, $xbox);
		}catch(CommandClientException){
			PageGenerator::DIALOG(
				"認証に失敗しました",
				"Discordアカウントとの連携中にエラーが発生しました。もう一度お試しください。"
			);
		}catch(Exception){
			PageGenerator::DIALOG(
				"認証に失敗しました",
				"データベースに内部エラーが発生しました。しばらく待ってから再度お試しください。"
			);
		}
		PageGenerator::DIALOG("認証に成功しました", "アカウントの連携が完了しました。安全にこのページを閉じることができます。");
	}

	PageGenerator::CONNECT_CONFIRM($discord->avatar, $discord->name, $xbox->avatar, $xbox->name);
}