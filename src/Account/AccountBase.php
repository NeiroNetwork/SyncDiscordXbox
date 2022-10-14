<?php

declare(strict_types=1);

namespace NeiroNetwork\SyncDiscordXbox\Account;

abstract class AccountBase{

	public function __construct(public readonly ?string $refreshToken){}
}