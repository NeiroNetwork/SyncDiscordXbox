<?php

declare(strict_types=1);

require_once dirname(__DIR__) . "/../vendor/autoload.php";

use Illuminate\Database\Capsule\Manager as Capsule;
use NeiroNetwork\SyncDiscordXbox\ApplicationInitializer;

ApplicationInitializer::run();
header("X-Frame-Options: DENY");

if(($_GET["key"] ?? "") !== $_ENV["WEBHOOK_RANDOM"]) die(401);

$rawJson = file_get_contents("php://input");

$data = json_decode($rawJson, true, flags: JSON_THROW_ON_ERROR);
if(!is_array($data) || !isset($data["requestId"], $data["visitorId"])) die(400);

Capsule::table("fingerprints")->insert([
	"request_id" => $data["requestId"],
	"visitor_id" => $data["visitorId"],
	"ip" => $data["ip"],
	"timestamp" => $data["timestamp"],
	"confidence/score" => $data["confidence"]["score"],
	"raw" => $rawJson,
]);