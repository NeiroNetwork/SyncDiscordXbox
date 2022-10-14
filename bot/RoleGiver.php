<?php

declare(strict_types=1);

require_once dirname(__DIR__) . "/vendor/autoload.php";

use Discord\Discord;
use Discord\Parts\User\Member;
use Discord\WebSockets\Event;
use Discord\WebSockets\Intents;
use Illuminate\Database\Capsule\Manager as Capsule;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use NeiroNetwork\SyncDiscordXbox\AccountSynchronizer;
use NeiroNetwork\SyncDiscordXbox\ApplicationInitializer;
use NeiroNetwork\SyncDiscordXbox\Authenticator\DiscordAuthenticator;
use NeiroNetwork\SyncDiscordXbox\Authenticator\XboxliveAuthenticator;

ApplicationInitializer::run();

$discord = new Discord([
	"token" => $_ENV["DISCORD_BOT_TOKEN"],
	"intents" => Intents::getDefaultIntents() | Intents::GUILD_MEMBERS
]);

$discord->on(Event::GUILD_MEMBER_ADD, function(Member $member){
	$ids = Capsule::table("accounts")->where("discord", "=", (int) $member->id)->first();
	if(empty($ids)) return;

	$token1 = Capsule::table("discord_tokens")->where("id", "=", $ids->discord)->first();
	$token2 = Capsule::table("azure_tokens")->where("xuid", "=", $ids->xuid)->first();
	if(empty($token1) || empty($token2)) return;

	try{
		$account1 = (new DiscordAuthenticator())->getAccount($token1->refresh_token);
		$account2 = (new XboxliveAuthenticator())->getAccount($token2->refresh_token);
		AccountSynchronizer::sync($account1, $account2);
	}catch(IdentityProviderException){
		// TODO: 使えないトークンはデータベースから削除する？
		return;
	}catch(Exception){
	}
});

$discord->run();