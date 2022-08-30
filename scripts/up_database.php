<?php

declare(strict_types=1);

require_once dirname(__DIR__) . "/vendor/autoload.php";

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

require_once dirname(__DIR__) . "/src/dotenv_validator.php";
require_once dirname(__DIR__) . "/src/connect_database.php";

Capsule::schema()->create("discord_xuid_map", function(Blueprint $table) : void{
	$table->bigInteger("discord_id")->unsigned()->primary();
	$table->bigInteger("xuid")->unsigned()->nullable(false);
	$table->timestampsTz();
});