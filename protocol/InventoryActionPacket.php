<?php

declare(strict_types=1);


namespace protocol;


class InventoryActionPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::INVENTORY_ACTION_PACKET;

	public const ACTION_GIVE_ITEM = 0;
	public const ACTION_ENCHANT_ITEM = 2;

	public $actionId;
	public $item;
	public $enchantmentId = 0;
	public $enchantmentLevel = 0;

	function decodePayload() : void{
		$this->actionId = $this->getUnsignedVarInt();
		$this->item = $this->getSlot();
		$this->enchantmentId = $this->getVarInt();
		$this->enchantmentLevel = $this->getVarInt();
	}

	function encodePayload() : void{
		$this->putUnsignedVarInt($this->actionId);
		$this->putSlot($this->item);
		$this->putVarInt($this->enchantmentId);
		$this->putVarInt($this->enchantmentLevel);
	}
}