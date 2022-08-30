<?php

declare(strict_types=1);

function generatePage(string $title, string $message) : never{
	$template = file_get_contents(dirname(__FILE__) . "/template.html");
	$html = str_replace(["%INSERT_TITLE%", "%INSERT_MESSAGE%"], [$title, $message], $template);
	$html = str_replace("%DISCORD_URI%", "discord:///channels/" . $_ENV["DISCORD_GUILD_ID"], $html);
	echo str_replace(["\n", "\r", "\t"], "", $html);
	exit;
}