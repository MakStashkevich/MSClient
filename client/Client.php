<?php
/**
 * Created by PhpStorm.
 * User: MakStashkevich
 * Date: 10.03.2019
 * Time: 15:38
 */

namespace client;

use client\entity\inventory\InventoryType;
use console\Console;

class Client
{
	/** @var array */
	private $list = [];
	/** @var Console */
	private $console;
	/** @var bool */
	private $stopped = false;
	/** @var int */
	private $clientId = 0;

	/**
	 * Client constructor.
	 */
	function __construct()
	{
		//init all
		InventoryType::init();

		//add client
		$client = new PocketEditionClient(
            new Address('dragonw.ru', 19999),
//        new Address('bmpe.pw', 19134),
			new Bot(
				'robotXXXsuper',
				'sosipisos',
				new Address('0.0.0.0', mt_rand(10000, 50000)),
				new Skin(CLIENTPATH . 'skins/Robot.png')
			)
		);

		$this->list[] = $client;
		if (count($this->list) < 2) {
			$this->clientId = 0;
			$this->console = new Console($this, true);
		} else $this->console = new Console($this);
	}

	function tick()
	{
		if ($this->stopped) return;
		$this->console->tick();
		/** @var PocketEditionClient $bot */
		foreach ($this->list as &$bot) {
			if ($bot->tick() == false) { //Disconnect
				info('Client disconnect...');
				$params = $bot->getParams();
				$bot->quit();
				$bot = new PocketEditionClient(...$params);
				info('Client reconnected!');
			}
		}
	}

	function stop()
	{
		$this->stopped = true;
		info('Quit bots in server...');
		/** @var PocketEditionClient $bot */
		foreach ($this->list as &$bot) {
			$bot->quit();
			unset($bot);
		}
		info('Client stopped...');
		shutdown();
	}

	/**
	 * @param string $mess
	 */
	function chat(string $mess)
	{
		$client = $this->getPocketClient();
		if ($client !== false) $client->sendMessage($mess);
	}

	/**
	 * @param int $id
	 * @return bool
	 */
	function setClientId(int $id): bool
	{
		if (isset($this->list[$id])) {
			$this->clientId = $id;
			return true;
		}
		return false;
	}

	/**
	 * @return bool|PocketEditionClient
	 */
	function getPocketClient()
	{
		if (isset($this->clientId) && isset($this->list[$this->clientId])) {
			return $this->list[$this->clientId];
		}
		info('Use this command to connect on bot: connect {id}');
		return false;
	}

	function removeClientId()
	{
		if (isset($this->clientId)) unset($this->clientId);
	}
}