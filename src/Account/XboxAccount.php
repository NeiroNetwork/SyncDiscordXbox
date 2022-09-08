<?php

declare(strict_types=1);

namespace NeiroNetwork\SyncDiscordXbox\Account;

use BigPino67\OAuth2\XBLive\Client\Provider\Profiles\Models\Profile;

class XboxAccount{

	public readonly int $id;
	public readonly string $name;
	public readonly string $avatar;

	public function __construct(Profile $xbox){
		$this->id = (int) $xbox->getId();
		$this->name = $xbox->getSettings()->getGamertag();
		$this->avatar = $xbox->getSettings()->getGameDisplayPicRaw() . "&h=128&w=128";
	}
}