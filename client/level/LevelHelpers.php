<?php

namespace client\level;

use InvalidArgumentException;

class LevelHelpers
{
	/** @var int */
	const Y_MAX = 0x100; //256
	const Y_MASK = 0xFF;

	/** @var int */
	const PACKETS_THRESHOLD = 32;

	/** @var int */
	const TIME_DAY = 0;
	const TIME_SUNSET = 12000;
	const TIME_NIGHT = 14000;
	const TIME_SUNRISE = 23000;

	/** @var int */
	const TIME_FULL = 24000;

	/**
	 * @param int $x
	 * @param int $z
	 * @return int
	 */
	static function chunkHash(int $x, int $z)
	{
		return (($x & 0xFFFFFFFF) << 32) | ($z & 0xFFFFFFFF);
	}

	/**
	 * @param int $x
	 * @param int $y
	 * @param int $z
	 * @return int
	 */
	static function blockHash(int $x, int $y, int $z)
	{
		if ($y < 0 or $y >= self::Y_MAX) {
			throw new InvalidArgumentException("Y coordinate $y is out of range!");
		}
		return (($x & 0xFFFFFFF) << 36) | (($y & self::Y_MASK) << 28) | ($z & 0xFFFFFFF);
	}

	/**
	 * @param $hash
	 * @param $x
	 * @param $y
	 * @param $z
	 */
	static function getBlockXYZ($hash, &$x, &$y, &$z)
	{
		$x = $hash >> 36;
		$y = ($hash >> 28) & self::Y_MASK; //it's always positive
		$z = ($hash & 0xFFFFFFF) << 36 >> 36;
	}

	/**
	 * @param string|int $hash
	 * @param int|null $x
	 * @param int|null $z
	 */
	static function getXZ($hash, &$x, &$z)
	{
		$x = $hash >> 32;
		$z = ($hash & 0xFFFFFFFF) << 32 >> 32;
	}
}