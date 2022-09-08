<?php

declare(strict_types=1);

namespace NeiroNetwork\SyncDiscordXbox\Wrapper\Discord;

use GuzzleHttp\Command\Result;

final class Member{

	public readonly ?string $avatar;
	public readonly ?\DateTimeImmutable $communicationDisabledUntil;
	public readonly int $flags;
	public readonly \DateTimeImmutable $joinedAt;
	public readonly ?string $nick;
	public readonly bool $pending;
	public readonly ?\DateTimeImmutable $premiumSince;
	/** @var int[] */
	public readonly array $roles;
	public readonly User $user;
	public readonly bool $mute;
	public readonly bool $deaf;

	public function __construct(Result $result){
		$datetime = fn($timestamp) => is_string($timestamp) ? new \DateTimeImmutable($timestamp) : null;
		$this->avatar = $result["avatar"];
		$this->communicationDisabledUntil = $datetime($result["communication_disabled_until"]);
		$this->flags = $result["flags"];
		$this->joinedAt = $datetime($result["joined_at"]);
		$this->nick = $result["nick"];
		$this->pending = $result["pending"];
		$this->premiumSince = $datetime($result["premium_since"]);
		$this->roles = array_map(fn($role) => (int) $role, $result["roles"]);
		$this->user = new User($result["user"]);
		$this->mute = $result["mute"];
		$this->deaf = $result["deaf"];
	}
}