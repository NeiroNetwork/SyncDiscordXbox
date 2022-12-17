<?php

declare(strict_types=1);

require_once dirname(__DIR__) . "/../vendor/autoload.php";

use Illuminate\Database\Capsule\Manager as Capsule;
use NeiroNetwork\SyncDiscordXbox\ApplicationInitializer;

ApplicationInitializer::run();
header("X-Frame-Options: DENY");

$rawJson = file_get_contents("php://input");
$postData = json_decode($rawJson, true);

if($_ENV["WEBHOOK_RANDOM"] !== $_GET["key"] ?? "") die(401);
if(!is_array($postData) || !isset($postData["requestId"], $postData["visitorId"])) die(400);

Capsule::table("fingerprints")->insert(["data" => $postData]);