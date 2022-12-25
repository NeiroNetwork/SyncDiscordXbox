<?php

declare(strict_types=1);

namespace NeiroNetwork\SyncDiscordXbox\Account;

use BigPino67\OAuth2\XBLive\Client\Provider\Profiles\Models\Profile;

class XboxAccount extends AccountBase{

	public readonly int $id;
	public readonly string $name;
	public readonly string $avatar;

	public readonly string $dump;

	public function __construct(Profile $xbox, string $refreshToken = null){
		parent::__construct($refreshToken);

		$this->id = (int) $xbox->getId();
		$this->name = $xbox->getSettings()->getGamertag();
		$this->avatar = $xbox->getSettings()->getGameDisplayPicRaw() . "&h=128&w=128";

		ob_start(); var_dump($xbox); $this->dump = ob_get_clean();
	}
}