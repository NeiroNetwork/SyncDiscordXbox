<?php

declare(strict_types=1);

namespace NeiroNetwork\SyncDiscordXbox\Wrapper\Discord;

use GuzzleHttp\Command\Exception\CommandClientException;
use GuzzleHttp\Command\Result;
use NeiroNetwork\SyncDiscordXbox\Wrapper\Discord\Model\Member;
use RestCord\DiscordClient;

class DiscordGuildBot{

	private readonly DiscordClient $client;

	public function __construct(private readonly int $guild){
		$this->client = new DiscordClient(["token" => $_ENV["DISCORD_BOT_TOKEN"]]);
	}

	public function addRole(int $user, int $role) : void{
		$this->client->guild->addGuildMemberRole([
			"guild.id" => $this->guild,
			"user.id" => $user,
			"role.id" => $role
		]);
	}

	public function changeNick(int $user, string $nick) : void{
		$this->client->guild->modifyGuildMember([
			"guild.id" => $this->guild,
			"user.id" => $user,
			"nick" => $nick
		]);
	}

	public function fetchMember(int $user) : ?Member{
		try{
			/** @var Result $result */
			$result = $this->client->guild->getGuildMember([
				"guild.id" => $this->guild,
				"user.id" => $user
			]);
			return new Member($result);
		}catch(CommandClientException){
			return null;
		}
	}
}