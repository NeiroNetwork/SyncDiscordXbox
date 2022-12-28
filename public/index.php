<?php

declare(strict_types=1);

require_once dirname(__DIR__) . "/vendor/autoload.php";

use GuzzleHttp\Command\Exception\CommandClientException;
use Illuminate\Database\Capsule\Manager as Capsule;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use NeiroNetwork\SyncDiscordXbox\Account\DiscordAccount;
use NeiroNetwork\SyncDiscordXbox\Account\XboxAccount;
use NeiroNetwork\SyncDiscordXbox\AccountSynchronizer;
use NeiroNetwork\SyncDiscordXbox\ApplicationInitializer;
use NeiroNetwork\SyncDiscordXbox\Authenticator\DiscordAuthenticator;
use NeiroNetwork\SyncDiscordXbox\Authenticator\XboxliveAuthenticator;
use NeiroNetwork\SyncDiscordXbox\PageGenerator;
use NeiroNetwork\SyncDiscordXbox\Wrapper\IPQualityScore\Exception\ProxyDetectionException;
use NeiroNetwork\SyncDiscordXbox\Wrapper\IPQualityScore\ProxyDetector;

ApplicationInitializer::run();
header("X-Frame-Options: DENY");
session_start();

if(isset($_GET["error"])){
	$denied = $_GET["error"] === "access_denied";
	PageGenerator::DIALOG(
		$denied ? "キャンセルされました" : "連携がキャンセルされました",
		$denied ? "アプリケーションの認証がユーザーによってキャンセルされました。アカウントの連携は完了していません。" :
			"アプリケーションの認証中にエラーが発生しました。アカウントの連携は完了していません。"
	);
}

if(isset($_GET["reset"])) session_destroy() && session_start();

// Discordの認証
if(empty($_SESSION["step_one"])){
	$authenticator = new DiscordAuthenticator();
	try{
		$discordAccount = $_SESSION["step_one"] = $authenticator->getAccount();
	}catch(IdentityProviderException){
		$authenticator->startWebAuthentication();
	}

	if(!$discordAccount->serverJoined){
		PageGenerator::DIALOG("連携に失敗しました", "あなたはDiscordサーバーに参加していません。アカウントを連携するにはサーバーに参加する必要があります。");
	}
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

	// アカウント連携ボタンを押していない
	if(!isset($_POST["request_id"])){
		PageGenerator::CONNECT_CONFIRM($discord->avatar, $discord->name, $xbox->avatar, $xbox->name);
	}

	if($_ENV["FP_ENABLED"]){
		$result = Capsule::table("fingerprints")->where("request_id", "=", $_POST["request_id"])->first();
		if(!isset($result->request_id, $result->ip) || $result->request_id !== $_POST["request_id"] || $result->ip !== $_SERVER["REMOTE_ADDR"]){
			PageGenerator::DIALOG("連携に失敗しました", "リクエストの検証に失敗しました。もう一度お試しください。");
		}
		/** @var string $visitorId */
		$visitorId = $result->visitor_id;
	}

	if($_ENV["IPQS_ENABLED"]){
		if(empty($_SERVER["HTTP_USER_AGENT"]) || empty($_SERVER["HTTP_ACCEPT_LANGUAGE"])){
			PageGenerator::DIALOG("連携に失敗しました", "不正なリクエストを受け取りました。もう一度お試しください。");
		}
		try{
			$result = (new ProxyDetector($_ENV["IPQS_TOKEN"]))->check(
				$_SERVER["REMOTE_ADDR"],
				1,
				$_SERVER["HTTP_USER_AGENT"],
				$_SERVER["HTTP_ACCEPT_LANGUAGE"]
			);
			Capsule::table("ip_quality_score")->upsert([
				"ip" => $_SERVER["REMOTE_ADDR"],
				"proxy" => $result->proxy,
				"fraud_score" => $result->fraudScore,
				"raw" => $result->rawJson,
				"updated_at" => microtime(true),
			], "ip");
		}catch(ProxyDetectionException $e){
			PageGenerator::DIALOG("連携に失敗しました", "リクエストの検証中にエラーが発生しました。時間をおいてから、もう一度お試しください。");
		}catch(PDOException | LogicException){
			PageGenerator::DIALOG("連携に失敗しました", "データベースに内部エラーが発生しました。しばらく待ってから再度お試しください。");
		}
	}

	session_destroy();
	try{
		Capsule::table("fud_discord")->upsert(["id" => $discord->id, "dump" => $discord->dump], "id");
		Capsule::table("fud_xbox")->upsert(["id" => $xbox->id, "dump" => $xbox->dump], "id");
		AccountSynchronizer::storeData($discord, $xbox, $_SERVER["REMOTE_ADDR"], $visitorId ?? "");
		AccountSynchronizer::modifyUser($discord->id, (int) $_ENV["MEMBER_ROLE_ID"], $xbox->name);
	}catch(CommandClientException){
		PageGenerator::DIALOG("連携に失敗しました", "Discordアカウントとの連携中にエラーが発生しました。もう一度お試しください。");
	}catch(PDOException | LogicException){
		PageGenerator::DIALOG("連携に失敗しました", "データベースに内部エラーが発生しました。しばらく待ってから再度お試しください。");
	}
	PageGenerator::DIALOG("連携に成功しました", "アカウントの連携が完了しました。安全にこのページを閉じることができます。");
}