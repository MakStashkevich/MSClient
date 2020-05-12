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
	/** @var bool */
	const STEADFAST2 = true;
	/** @var bool */
	const DISABLE_TIP = true;
	/** @var bool */
	const DEBUG_PACKETS_RAKLIB = false;
	const DEBUG_PACKETS_PE = false;
	const DEBUG_PACKETS_PE_ALL = false;
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
			new Server('91.214.71.84', 19132, 0),
//			new Server('bombacraft.ru', 19133, 0),

//			new Server('54.38.216.98', 19132, 1),
//			new Server('54.38.216.98', 17132, 1),
//			new Server('54.38.216.98', 12008, 1),

//            new Server('dragonw.ru', 19133),
//			new Server('bmpe.pw', 19134),
			new Bot(
				'robotXXXsuper',
				'sosipisos',
				new Address('0.0.0.0', mt_rand(10000, 50000)),
				new Skin(CLIENTPATH . 'skins/Robot.png')
			)
		);

		info('Bots loaded..');
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
			if ($bot->tick() == false) { // Disconnect
				info('Client disconnect...');
				$params = $bot->getParams();
				$bot->quit();
				$bot = new PocketEditionClient(...$params);
				info('Client reconnected!');
			}
		}
	}

	function __destruct()
	{
		$this->stop();
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
		if ($client !== false) {
			$args = explode(' ', $mess);
			switch ($args[0]) {
				case 'online':
					send('On server ' . count($client->getPlayer()->getPlayersOnline()) . ' players online');
					return;
				case 'bug':
					send('Start send bug packets');
					$client->sendBug();
					return;
				case 'damage':
					$value = 1;
					if (isset($args[1]) && is_numeric($args[1])) {
						$value = (int)$args[1];
					}

					send('Set damage ' . $value . ' for all players');
					$client->damageAll($value);
					return;
				case 'seek':
					$value = true;
					if (isset($args[1]) && $args[1] == 'off') {
						$value = false;
					}

					send(($value ? 'Start' : 'End') . ' seeked from player');
					$client->seekFromPlayer($value);
					return;
			}

			// send message
			$client->sendMessage($mess);
		}
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

	function removeClientId()
	{
		if (isset($this->clientId)) unset($this->clientId);
	}
}