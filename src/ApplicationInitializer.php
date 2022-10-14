<?php

declare(strict_types=1);

namespace NeiroNetwork\SyncDiscordXbox;

use Dotenv\Dotenv;
use Illuminate\Database\Capsule\Manager as Capsule;

final class ApplicationInitializer{

	public static function run() : void{
		self::loadDotenv();
		self::initDatabase();
	}

	private static function loadDotenv() : void{
		$dotenv = Dotenv::createImmutable(dirname(__DIR__));
		$dotenv->load();

		$dotenv->required(["XBL_CLIENT_ID", "XBL_CLIENT_SECRET", "XBL_REDIRECT_URI"])->notEmpty();
		$dotenv->required(["DISCORD_CLIENT_ID", "DISCORD_CLIENT_SECRET", "DISCORD_REDIRECT_URI"])->notEmpty();
		$dotenv->required(["DISCORD_BOT_TOKEN", "DISCORD_GUILD_ID", "MEMBER_ROLE_ID"])->notEmpty();

		$dotenv->required(["DB_DRIVER"]);
		$dotenv->required(match($_ENV["DB_DRIVER"]){
			"mysql" => ["DB_HOST", "DB_DATABASE", "DB_USERNAME", "DB_PASSWORD"],
			"sqlite" => ["DB_DATABASE"],
			default => throw new \InvalidArgumentException("Undefined database driver " . $_ENV["DB_DRIVER"]),
		})->notEmpty();
	}

	private static function initDatabase() : void{
		$capsule = new Capsule();
		$capsule->addConnection([
			"driver" => $_ENV["DB_DRIVER"],
			"host" => $_ENV["DB_HOST"] ?? "",
			"database" => $_ENV["DB_DATABASE"],
			"username" => $_ENV["DB_USERNAME"] ?? "",
			"password" => $_ENV["DB_PASSWORD"] ?? "",
		]);
		$capsule->setAsGlobal();
	}
}