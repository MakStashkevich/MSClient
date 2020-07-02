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
	 *
	 * @return string
	 */
	static function _hex_dump(string $buffer)
	{
		$output = "";
		$bin = str_split($buffer, 16);
		foreach ($bin as $counter => $line) {
			$hex = chunk_split(chunk_split(str_pad(bin2hex($line), 32, " ", STR_PAD_RIGHT), 2, " "), 24, " ");
			$ascii = preg_replace('#([^\x20-\x7E])#', ".", $line);
			$output .= str_pad(dechex($counter << 4), 4, "0", STR_PAD_LEFT) . "  " . $hex . " " . $ascii . PHP_EOL;
		}

		return $output;
	}

	/**
	 * @param string $buffer
	 */
	static function _packet(string $buffer)
	{
		echo PHP_EOL .  PacketPool::getPacketById(Binary::readByte($buffer{0}))->getName() . PHP_EOL;
	}
}