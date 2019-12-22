<?php

namespace client\entity\inventory;

use client\entity\inventory\utils\WindowTypes;

class InventoryType
{
	/** @var int */
	const DEFAULT = -1;

	/** @var int */
	const CHEST = 0;
	const DOUBLE_CHEST = 1;
	const PLAYER = 2;
	const ARMOR = 3;
	const FURNACE = 4;
	const CRAFTING = 5;
	const WORKBENCH = 6;
	const STONE_CUTTER = 7;
	const BREWING_STAND = 8;
	const ANVIL = 9;
	const ENCHANT_TABLE = 10;

	const PLAYER_FLOATING = 254;

	/** @var array */
	private static $default = [];

	/** @var int */
	private $size;
	/** @var string */
	private $title;
	/** @var int */
	private $typeId;

	/**
	 * @param $index
	 *
	 * @return InventoryType
	 */
	public static function get(int $index = self::DEFAULT)
	{
		return static::$default[$index] ?? static::$default[self::DEFAULT];
	}

	public static function init()
	{
		if (count(static::$default) > 0) {
			return;
		}
		
		static::$default = [
			static::DEFAULT => new InventoryType(0, 'Default', null),

			static::CHEST => new InventoryType(27, 'Chest', WindowTypes::CONTAINER),
			static::DOUBLE_CHEST => new InventoryType(27 + 27, 'Double Chest', WindowTypes::CONTAINER),
			static::PLAYER => new InventoryType(36 + 9, 'Player', WindowTypes::INVENTORY), //36 CONTAINER, 9 HOTBAR
			static::ARMOR => new InventoryType(4, 'Armor', WindowTypes::INVENTORY), //4 ARMOR
			static::CRAFTING => new InventoryType(5, 'Crafting', WindowTypes::INVENTORY), //yes, the use of INVENTORY is intended! 4 CRAFTING slots, 1 RESULT
			static::WORKBENCH => new InventoryType(10, 'Crafting', WindowTypes::WORKBENCH), //9 CRAFTING slots, 1 RESULT
			static::FURNACE => new InventoryType(3, 'Furnace', WindowTypes::FURNACE), //2 INPUT, 1 OUTPUT
			static::ENCHANT_TABLE => new InventoryType(2, 'Enchant', WindowTypes::ENCHANTMENT), //1 INPUT/OUTPUT, 1 LAPIS
			static::BREWING_STAND => new InventoryType(4, 'Brewing', WindowTypes::BREWING_STAND), //1 INPUT, 3 POTION
			static::ANVIL => new InventoryType(3, 'Anvil', WindowTypes::ANVIL), //2 INPUT, 1 OUT
			static::PLAYER_FLOATING => new InventoryType(36, 'Floating', null) //Mirror all slots of main inventory (needed for large item pickups)
		];
	}

	/**
	 * @param int $defaultSize
	 * @param string $defaultTitle
	 * @param int $typeId
	 */
	private function __construct($defaultSize, $defaultTitle, $typeId = 0)
	{
		$this->size = $defaultSize;
		$this->title = $defaultTitle;
		$this->typeId = $typeId;
	}

	/**
	 * @return int
	 */
	public function getDefaultSize(): int
	{
		return $this->size;
	}

	/**
	 * @return string
	 */
	public function getDefaultTitle(): string
	{
		return $this->title;
	}

	/**
	 * @return int
	 */
	public function getNetworkType(): int
	{
		return $this->typeId;
	}
}