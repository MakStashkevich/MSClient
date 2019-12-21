<?php

namespace client\nbt;

use pocketmine\nbt\tag\CompoundTag;

class DefaultNbt
{
	/** @var CompoundTag */
	protected $nbt;

	/** @var int */
	protected $x = 0;
	protected $y = 0;
	protected $z = 0;

	/**
	 * DefaultNbt constructor.
	 * @param CompoundTag $tag
	 */
	function __construct(CompoundTag $tag)
	{
		$this->nbt = $tag;

		if ($tag->offsetExists('x')) $this->x = (int)$tag->offsetGet('x');
		if ($tag->offsetExists('y')) $this->y = (int)$tag->offsetGet('y');
		if ($tag->offsetExists('z')) $this->z = (int)$tag->offsetGet('z');
	}

	/**
	 * @return CompoundTag
	 */
	function getNbt(): CompoundTag
	{
		return $this->nbt;
	}

	/**
	 * @return int
	 */
	function getX(): int
	{
		return $this->x;
	}

	/**
	 * @return int
	 */
	function getY(): int
	{
		return $this->y;
	}

	/**
	 * @return int
	 */
	function getZ(): int
	{
		return $this->z;
	}
}