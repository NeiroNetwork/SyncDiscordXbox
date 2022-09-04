<?php

declare(strict_types=1);

namespace NeiroNetwork\SyncDiscordXbox;

class PageGenerator{

	protected static function generate(string $name, array $params) : never{
		$template = file_get_contents(dirname(__DIR__) . "/html/$name.html");
		$html = str_replace(array_keys($params), array_values($params), $template);
		echo str_replace(["\n", "\r", "\t"], "", $html);
		exit;
	}

	public static function DIALOG(string $title, string $message) : never{
		self::generate("template1", [
			"%INSERT_TITLE%" => $title,
			"%INSERT_MESSAGE%" => $message,
			"%DISCORD_URI%" => "discord:///channels/" . $_ENV["DISCORD_GUILD_ID"],
		]);
	}
}