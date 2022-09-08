<?php

declare(strict_types=1);

require_once dirname(__DIR__) . "/vendor/autoload.php";

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;
use NeiroNetwork\SyncDiscordXbox\ApplicationInitializer;

ApplicationInitializer::run();

Capsule::schema()->create("accounts", function(Blueprint $table) : void{
	$table->bigInteger("discord")->unsigned()->primary();
	$table->bigInteger("xuid")->unsigned()->nullable(false);
});