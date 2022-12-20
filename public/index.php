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

ApplicationInitializer::run();
header("X-Frame-Options: DENY");
session_start();

if(isset($_GET["error"])){
	$denied = $_GET["error"] === "access_denied";
	PageGenerator::DIALOG(
		$denied ? "キャンセルされました" : "認証がキャンセルされました",
		$denied ? "アプリケーションの認証がユーザーによってキャンセルされました。アカウントの連携は完了していません。" :
			"アプリケーションの認証中にエラーが発生しました。アカウントの連携は完了していません。"
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
			"あなたはDiscordサーバーに参加していません。アカウントを連携するにはサーバーに参加する必要があります。"
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

	if(isset($_POST["request_id"])){
		if($_ENV["FP_ENABLED"]){
			$result = Capsule::table("fingerprints")->where("request_id", "=", $_POST["request_id"])->first();
			if(!isset($result->request_id, $result->ip) || $result->request_id !== $_POST["request_id"] || $result->ip !== $_SERVER["REMOTE_ADDR"]){
				PageGenerator::DIALOG("認証に失敗しました", "不正なリクエストを受け取りました。もう一度お試しください。");
			}
		}

		if($_ENV["IPQS_ENABLED"]){
			if(empty($_SERVER["HTTP_USER_AGENT"]) || empty($_SERVER["HTTP_ACCEPT_LANGUAGE"])){
				PageGenerator::DIALOG("認証に失敗しました", "不正なリクエストを受け取りました。もう一度お試しください。");
			}
			$json = file_get_contents("https://ipqualityscore.com/api/json/ip", context: stream_context_create([
				"http" => [
					"method" => "POST",
					"header" => "Content-Type: application/x-www-form-urlencoded",
					"content" => http_build_query([
						"key" => $_ENV["IPQS_TOKEN"],
						"ip" => $_SERVER["REMOTE_ADDR"],
						"allow_public_access_points" => false,
						"strictness" => 1,
						"user_agent" => $_SERVER["HTTP_USER_AGENT"],
						"user_language" => $_SERVER["HTTP_ACCEPT_LANGUAGE"],
					], arg_separator: "&")
				]
			]));
			$result = json_decode($json, true);
			if(($result["success"] ?? false) !== true){
				PageGenerator::DIALOG("認証に失敗しました", "リクエストの検証中にエラーが発生しました。時間をおいてから、もう一度お試しください。");
			}
			try{
				Capsule::table("ip_quality_score")->upsert([
					"ip" => $_SERVER["REMOTE_ADDR"],
					"proxy" => $result["proxy"],
					"fraud_score" => $result["fraud_score"],
					"raw" => $json,
					"updated_at" => microtime(true),
				], "ip");
			}catch(PDOException | LogicException){
				PageGenerator::DIALOG(
					"認証に失敗しました",
					"データベースに内部エラーが発生しました。しばらく待ってから再度お試しください。"
				);
			}
		}

		session_destroy();
		try{
			AccountSynchronizer::storeData($discord, $xbox, $_SERVER["REMOTE_ADDR"], $result->visitor_id ?? "");
			AccountSynchronizer::modifyUser($discord->id, (int) $_ENV["MEMBER_ROLE_ID"], $xbox->name);
		}catch(CommandClientException){
			PageGenerator::DIALOG(
				"認証に失敗しました",
				"Discordアカウントとの連携中にエラーが発生しました。もう一度お試しください。"
			);
		}catch(PDOException | LogicException){
			PageGenerator::DIALOG(
				"認証に失敗しました",
				"データベースに内部エラーが発生しました。しばらく待ってから再度お試しください。"
			);
		}
		PageGenerator::DIALOG("認証に成功しました", "アカウントの連携が完了しました。安全にこのページを閉じることができます。");
	}

	PageGenerator::CONNECT_CONFIRM($discord->avatar, $discord->name, $xbox->avatar, $xbox->name);
}