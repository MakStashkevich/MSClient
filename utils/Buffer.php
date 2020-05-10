<?php

namespace utils;

use pocketmine\network\mcpe\protocol\PacketPool;
use pocketmine\utils\Binary;

class Buffer
{
	/**
	 * @param string $buffer
	 */
	static function _debug(string $buffer)
	{
		echo PHP_EOL . (
			"\"" . implode(array_map(function($s){
				return "\\x$s";
			}, str_split(bin2hex($buffer), 2))) . "\""
		) . PHP_EOL;
	}

	/**
	 * @param string $buffer
	 */
	static function _packet(string $buffer)
	{
		echo PHP_EOL .  PacketPool::getPacketById(Binary::readByte($buffer{0}))->getName() . PHP_EOL;
	}
}