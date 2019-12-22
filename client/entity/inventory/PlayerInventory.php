<?php

namespace client\entity\inventory;

class PlayerInventory extends ClientInventory
{
	/** @var int */
	protected $type = InventoryType::PLAYER;
}