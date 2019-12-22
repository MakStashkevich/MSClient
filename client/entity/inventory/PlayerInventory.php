<?php

namespace client\entity\inventory;

class PlayerInventory extends ClientInventory
{
	/** @var int */
	protected $type = InventoryType::PLAYER;

	/**
	 * @return array
	 */
	function getHotbar(): array
	{
		$hotbar = [];
		foreach ($this->slots as $slot => $item) {
			if ($slot < 9) $hotbar[$slot] = $item;
			else break;
		}
		return $hotbar;
	}
}