<?php

namespace client\entity\inventory;

use pocketmine\item\Item;

class ClientInventory
{
	/** @var int */
	protected $type = InventoryType::DEFAULT;

	/** @var array */
	protected $slots = [];

	/**
	 * ClientInventory constructor.
	 */
	function __construct()
	{
		$this->slots = array_fill(0, $this->getMax(), new Item(0));
	}

	/**
	 * @param array $slots
	 */
	function addItems(array $slots = [])
	{
		$max = $this->getMax();
		foreach ($slots as $slot => $item) {
			if ($slot <= $max && $slot >= 0) {
				$this->slots[$slot] = $item;
			}
		}
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