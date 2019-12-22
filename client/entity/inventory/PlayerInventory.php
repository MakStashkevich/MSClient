<?php

namespace client\entity\inventory;

use pocketmine\item\Item;

class PlayerInventory
{
	/** @var array */
	private $slots = [];

	/** @var int */
	private $type = InventoryType::PLAYER;

	/**
	 * PlayerInventory constructor.
	 */
	function __construct()
	{
		$this->slots = array_fill(0, $this->getMax(), new Item(0));
	}

	/**
	 * @param array $slots
	 */
	function setAll(array $slots = [])
	{
		$max = $this->getMax();
		if (count($slots) !== $max) {
			error('U need give ' . $max . ' slots to setAll()');
			return;
		}
		$this->slots = $slots;
	}

	/**
	 * @return array
	 */
	function getAll(): array
	{
		return $this->slots;
	}

	/**
	 * @param int $slot
	 * @return mixed
	 */
	function getSlot(int $slot): Item
	{
		$max = $this->getMax();
		if ($slot > $max) {
			error('U call getSlot() with $slot = ' . $slot . ', but max = ' . $max);
			return new Item(0);
		}
		return $this->slots[$slot];
	}

	/**
	 * @return InventoryType
	 */
	function getType(): InventoryType
	{
		return InventoryType::get($this->type);
	}

	/**
	 * @return int
	 */
	function getMax(): int
	{
		return $this->getType()->getDefaultSize();
	}
}