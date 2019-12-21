<?php

namespace client\nbt;

use pocketmine\nbt\tag\CompoundTag;

class ChestNbt extends DefaultNbt
{
	/**
	 * ChestNbt constructor.
	 * @param CompoundTag $tag
	 */
	function __construct(CompoundTag $tag)
	{
		parent::__construct($tag);
	}
}