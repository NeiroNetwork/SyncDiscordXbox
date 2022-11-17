<?php

declare(strict_types=1);

require_once dirname(__DIR__) . "/vendor/autoload.php";

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;
use NeiroNetwork\SyncDiscordXbox\ApplicationInitializer;

ApplicationInitializer::run();

function up(){
	if(!Capsule::schema()->hasTable("accounts")){
		echo "Creating \"accounts\" table..." . PHP_EOL;
		Capsule::schema()->create("accounts", function(Blueprint $table) : void{
			$table->bigInteger("discord")->unsigned()->primary();
			$table->bigInteger("xuid")->unsigned()->nullable(false);
		});
	}

	if(!Capsule::schema()->hasTable("discord_tokens")){
		echo "Creating \"discord_tokens\" table..." . PHP_EOL;
		Capsule::schema()->create("discord_tokens", function(Blueprint $table) : void{
			$table->bigInteger("id")->unsigned()->primary();
			$table->text("refresh_token")->nullable(false);
		});
	}

	if(!Capsule::schema()->hasTable("azure_tokens")){
		echo "Creating \"azure_tokens\" table..." . PHP_EOL;
		Capsule::schema()->create("azure_tokens", function(Blueprint $table) : void{
			$table->bigInteger("xuid")->unsigned()->primary();
			$table->text("refresh_token")->nullable(false);
		});
	}

	if(!Capsule::schema()->hasTable("fingerprints")){
		echo "Creating \"fingerprints\" table..." . PHP_EOL;
		Capsule::schema()->create("fingerprints", function(Blueprint $table) : void{
			$table->json("data");
		});
	}
}

function down(){
	if(Capsule::schema()->hasTable("accounts")){
		echo "Deleting \"accounts\" table..." . PHP_EOL;
		Capsule::schema()->drop("accounts");
	}

	if(Capsule::schema()->hasTable("discord_tokens")){
		echo "Deleting \"discord_tokens\" table..." . PHP_EOL;
		Capsule::schema()->drop("discord_tokens");
	}

	if(Capsule::schema()->hasTable("azure_tokens")){
		echo "Deleting \"azure_tokens\" table..." . PHP_EOL;
		Capsule::schema()->drop("azure_tokens");
	}

	if(Capsule::schema()->hasTable("fingerprints")){
		echo "Deleting \"fingerprints\" table..." . PHP_EOL;
		Capsule::schema()->drop("fingerprints");
	}
}

match(strtolower($argv[1] ?? "")){
	"up" => up(),
	"down" => down(),
	default => exit("Usage: $argv[0] <up|down>"),
};