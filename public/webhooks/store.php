<?php

declare(strict_types=1);

require_once dirname(__DIR__) . "/../vendor/autoload.php";

use Illuminate\Database\Capsule\Manager as Capsule;
use NeiroNetwork\SyncDiscordXbox\ApplicationInitializer;

ApplicationInitializer::run();
header("X-Frame-Options: DENY");

$rawJson = file_get_contents("php://input");
$postData = json_decode($rawJson, true);

if(!is_array($postData)) die(400);
if($_GET["random"] ?? null !== $_ENV["WEBHOOK_RANDOM"]) die(401);
if(!isset($postData["requestId"], $postData["visitorId"])) die(400);

Capsule::table("fingerprints")->insert(["data" => $postData]);