<?php
/**
 * Created by PhpStorm.
 * User: MakStashkevich
 * Date: 10.03.2019
 * Time: 15:38
 */

namespace client;

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
	private $chatId;

	/**
	 * Client constructor.
	 */
	function __construct()
	{
		$client = new PocketEditionClient(
            new Address('dragonw.ru', 19999),
//        new Address('bmpe.pw', 19134),
			new Bot(
				'robotXXXsuper',
				'sosipisos',
				new Address('0.0.0.0', 11111),
				new Skin(CLIENTPATH . 'skins/Robot.png')
			)
		);

		$this->list[] = $client;
		if (count($this->list) < 2) {
			$this->chatId = 0;
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
		if (isset($this->chatId)) {
			/** @var PocketEditionClient $bot */
			$bot = $this->list[$this->chatId];
			$bot->sendMessage($mess);
		} else info('Для использования чата необходимо выбрать бота! Команда: chat start id');
	}

	/**
	 * @param int $id
	 * @return bool
	 */
	function setChatId(int $id): bool
	{
		if (isset($this->list[$id])) {
			$this->chatId = $id;
			return true;
		}
		return false;
	}

	function removeChatId()
	{
		if (isset($this->chatId)) unset($this->chatId);
	}
}