<?php

declare(strict_types=1);

namespace NeiroNetwork\SyncDiscordXbox;

final class PageGenerator{

	private static function prepare(string $filename, array $params = []) : string{
		$template = file_get_contents(dirname(__DIR__) . "/html/$filename");
		$replaceValues = array_map(htmlspecialchars(...), array_values($params));
		$html = str_replace(array_keys($params), $replaceValues, $template);
		return str_replace(["\n", "\r", "\t"], "", $html);
	}

	public static function DIALOG(string $title, string $message) : never{
		echo self::prepare("template1.html", [
			"%INSERT_TITLE%" => $title,
			"%INSERT_MESSAGE%" => $message,
			"%DISCORD_URI%" => "discord:///channels/" . $_ENV["DISCORD_GUILD_ID"],
		]);
		exit;
	}

	public static function CONNECT_CONFIRM(string $discordIcon, string $discordName, string $xblIcon, string $gamertag) : never{
		echo self::prepare("template2.html", [
			"%INSERT_ICON_1%" => $discordIcon,
			"%INSERT_ACCOUNT_1%" => $discordName,
			"%INSERT_ICON_2%" => $xblIcon,
			"%INSERT_ACCOUNT_2%" => $gamertag,
			"%INSERT_FP_SCRIPT_FILE%" => $_ENV["FP_PUBLIC_KEY"],
			"%INSERT_CUSTOM_ENDPOINT%" => $_ENV["FP_ENDPOINT"],
		]);
		exit;
	}
}