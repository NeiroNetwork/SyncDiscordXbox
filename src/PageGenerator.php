<?php

declare(strict_types=1);

namespace NeiroNetwork\SyncDiscordXbox;

final class PageGenerator{

	protected static function generate(string $name, array $params = []) : void{
		$template = file_get_contents(dirname(__DIR__) . "/html/$name.html");
		$replaceValues = array_map(htmlspecialchars(...), array_values($params));
		$html = str_replace(array_keys($params), $replaceValues, $template);
		echo str_replace(["\n", "\r", "\t"], "", $html);
	}

	public static function DIALOG(string $title, string $message) : never{
		self::generate("template1", [
			"%INSERT_TITLE%" => $title,
			"%INSERT_MESSAGE%" => $message,
			"%DISCORD_URI%" => "discord:///channels/" . $_ENV["DISCORD_GUILD_ID"],
		]);
		exit;
	}

	public static function CONNECT_CONFIRM(string $discordIcon, string $discordName, string $xblIcon, string $gamertag) : never{
		self::generate("template2", [
			"%INSERT_ICON_1%" => $discordIcon,
			"%INSERT_ACCOUNT_1%" => $discordName,
			"%INSERT_ICON_2%" => $xblIcon,
			"%INSERT_ACCOUNT_2%" => $gamertag,
		]);
		exit;
	}

	/**
	 * @deprecated この関数は set_error_handler で使うことしか想定されていません！
	 */
	public static function ERROR_DIALOG(string $error) : void{
		self::generate("template3", [
			"%INSERT_ERROR_CODE%" => $error,
			"%DISCORD_URI%" => "discord:///channels/" . $_ENV["DISCORD_GUILD_ID"],
		]);
	}
}