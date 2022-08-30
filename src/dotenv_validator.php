<?php

declare(strict_types=1);

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();
$dotenv->required([
	"XBL_CLIENT_ID",
	"XBL_CLIENT_SECRET",
	"XBL_REDIRECT_URI",
	"DISCORD_CLIENT_ID",
	"DISCORD_CLIENT_SECRET",
	"DISCORD_REDIRECT_URI",
	"DISCORD_BOT_TOKEN",
	"DISCORD_SERVER_ID",
	"MEMBER_ROLE_ID",
])->notEmpty();