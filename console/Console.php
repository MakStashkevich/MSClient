<?php

/**
 * Created by PhpStorm.
 * User: MakStashkevich
 * Date: 10.03.2019
 * Time: 15:20
 */

namespace console;

use client\BotHelpers;
use client\Client;
use pocketmine\math\Vector3;

class Console
{
	/** @var */
	private $console;

	/** @var Client */
	private $client;

	/** @var bool */
	private $chat = false;

	/**
	 * Console constructor.
	 * @param Client $client
	 * @param bool $chat
	 */
	function __construct(Client $client, bool $chat = false)
	{
		$this->client = $client;
		$this->console = new ConsoleReader();
		$this->chat = $chat;
	}

	function tick()
	{
		if (($line = $this->console->getLine()) !== null) {
			$this->command($line);
		}
	}

	/**
	 * @param string $command
	 */
	function command(string $command)
	{
		$args = explode(' ', $command);
		switch ($args[0]) {
			case 'stop':
				$this->client->stop();
				break;
			case 'connect':
				if (!isset($args[1])) {
					info('Вы не указали id бота!');
					break;
				}
				$this->chat = $this->client->setClientId((int)$args[1]);
				break;
			case 'disconnect':
				$this->client->removeClientId();
				$this->chat = false;
				break;
			case 'move':
				$client = $this->client->getPocketClient();
				if ($client === false) break;
				if (!isset($args[1]) || !is_numeric($args[1])) {
					info('Вы не указали X');
					break;
				}
				if (!isset($args[2]) || !is_numeric($args[2])) {
					info('Вы не указали Y');
					break;
				}
				if (!isset($args[3]) || !is_numeric($args[3])) {
					info('Вы не указали Z');
					break;
				}
				BotHelpers::moveTo($client, new Vector3($args[1], $args[2], $args[3]));
				break;
			default:
				if ($this->chat) $this->client->chat($command);
				break;
		}
	}
}