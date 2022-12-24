<?php

declare(strict_types=1);

require_once dirname(__DIR__) . "/vendor/autoload.php";

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;
use NeiroNetwork\SyncDiscordXbox\ApplicationInitializer;

ApplicationInitializer::run();

function up(){
	if(!Capsule::schema()->hasTable("linked_data")){
		echo "Creating \"linked_data\" table..." . PHP_EOL;
		Capsule::schema()->create("linked_data", function(Blueprint $table) : void{
			$table->bigInteger("discord")->unsigned()->primary();
			$table->bigInteger("xuid")->unsigned()->nullable(false);
			$table->ipAddress("ip")->nullable(false);
			$table->string("fingerprint", 20)->nullable(false);
		});
	}

	if(!Capsule::schema()->hasTable("discord_tokens")){
		echo "Creating \"discord_tokens\" table..." . PHP_EOL;
		Capsule::schema()->create("discord_tokens", function(Blueprint $table) : void{
			$table->bigInteger("user_id")->unsigned()->primary();
			$table->text("refresh_token")->nullable(false);
		});
	}

	if(!Capsule::schema()->hasTable("xbox_tokens")){
		echo "Creating \"xbox_tokens\" table..." . PHP_EOL;
		Capsule::schema()->create("xbox_tokens", function(Blueprint $table) : void{
			$table->bigInteger("xuid")->unsigned()->primary();
			$table->text("refresh_token")->nullable(false);
		});
	}

	if(!Capsule::schema()->hasTable("fingerprints")){
		echo "Creating \"fingerprints\" table..." . PHP_EOL;
		Capsule::schema()->create("fingerprints", function(Blueprint $table) : void{
			$table->string("request_id", 20)->primary();
			$table->string("visitor_id", 20)->nullable(false);
			$table->ipAddress("ip")->nullable(false);
			$table->bigInteger("timestamp")->unsigned()->nullable(false);
			$table->double("confidence/score")->nullable();
			$table->json("raw");
		});
	}

	if(!Capsule::schema()->hasTable("ip_quality_score")){
		echo "Creating \"ip_quality_score\" table..." . PHP_EOL;
		Capsule::schema()->create("ip_quality_score", function(Blueprint $table) : void{
			$table->ipAddress("ip")->primary();
			$table->boolean("proxy")->nullable(false);
			$table->unsignedTinyInteger("fraud_score")->nullable(false);
			$table->json("raw")->nullable(false);
			$table->unsignedDecimal("updated_at", 65, 6)->nullable(false);
		});
	}

	if(!Capsule::schema()->hasTable("fud_discord")){
		echo "Creating \"fud_discord\" table..." . PHP_EOL;
		Capsule::schema()->create("fud_discord", function(Blueprint $table) : void{
			$table->bigInteger("id")->unsigned()->primary();
			$table->json("json")->nullable(false);
		});
	}

	if(!Capsule::schema()->hasTable("fud_xbox")){
		echo "Creating \"fud_xbox\" table..." . PHP_EOL;
		Capsule::schema()->create("fud_xbox", function(Blueprint $table) : void{
			$table->bigInteger("id")->unsigned()->primary();
			$table->json("json")->nullable(false);
		});
	}
}

function down(){
	if(Capsule::schema()->hasTable("linked_data")){
		echo "Deleting \"linked_data\" table..." . PHP_EOL;
		Capsule::schema()->drop("linked_data");
	}

	if(Capsule::schema()->hasTable("discord_tokens")){
		echo "Deleting \"discord_tokens\" table..." . PHP_EOL;
		Capsule::schema()->drop("discord_tokens");
	}

	if(Capsule::schema()->hasTable("xbox_tokens")){
		echo "Deleting \"xbox_tokens\" table..." . PHP_EOL;
		Capsule::schema()->drop("xbox_tokens");
	}

	if(Capsule::schema()->hasTable("fingerprints")){
		echo "Deleting \"fingerprints\" table..." . PHP_EOL;
		Capsule::schema()->drop("fingerprints");
	}

	if(Capsule::schema()->hasTable("ip_quality_score")){
		echo "Deleting \"ip_quality_score\" table..." . PHP_EOL;
		Capsule::schema()->drop("ip_quality_score");
	}

	if(Capsule::schema()->hasTable("fud_discord")){
		echo "Deleting \"fud_discord\" table..." . PHP_EOL;
		Capsule::schema()->drop("fud_discord");
	}

	if(Capsule::schema()->hasTable("fud_xbox")){
		echo "Deleting \"fud_xbox\" table..." . PHP_EOL;
		Capsule::schema()->drop("fud_xbox");
	}
}

match(strtolower($argv[1] ?? "")){
	"up" => up(),
	"down" => down(),
	default => exit("Usage: $argv[0] <up|down>"),
};