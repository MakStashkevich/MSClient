<?php

namespace client\level\chunk;

use client\nbt\ChestNbt;

class Chunk extends \pocketmine\level\format\Chunk
{
	/** @var array */
	protected $chests = [];

	/**
	 * @param array $data
	 */
	function setChests(array $data)
	{
		$this->chests = $data;
	}

	/**
	 * @return array
	 */
	function getChests()
	{
		return $this->chests;
	}

	/**
	 * @param int $x
	 * @param int $y
	 * @param int $z
	 * @return ChestNbt|null
	 */
	function getChest(int $x, int $y, int $z): ?ChestNbt
	{
		foreach ($this->chests as $chest) {
			if ($chest instanceof ChestNbt &&
				$x === $chest->getX() &&
				$y === $chest->getY() &&
				$z === $chest->getZ()
			) {
				return $chest;
			}
		}
		return null;
	}
}