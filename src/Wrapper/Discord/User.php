<?php

declare(strict_types=1);

namespace NeiroNetwork\SyncDiscordXbox\Wrapper\Discord;

final class User{

	public readonly int $id;
	public readonly string $username;
	public readonly ?string $avatar;
	public readonly mixed $avatarDecoration;
	public readonly int $discriminator;
	public readonly int $publicFlags;
	public readonly bool $bot;

	public function __construct(array $user){
		$this->id = (int) $user["id"];
		$this->username = $user["username"];
		$this->avatar = $user["avatar"];
		$this->avatarDecoration = $user["avatar_decoration"];
		$this->discriminator = (int) $user["discriminator"];
		$this->publicFlags = $user["public_flags"];
		$this->bot = $user["bot"] ?? false;
	}
}