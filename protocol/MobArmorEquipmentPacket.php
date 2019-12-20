<?php

declare(strict_types=1);


namespace protocol;


use pocketmine\item\Item;

class MobArmorEquipmentPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::MOB_ARMOR_EQUIPMENT_PACKET;

	public $entityRuntimeId;
	/** @var Item[] */
	public $slots = [];

	function decodePayload() : void{
		$this->entityRuntimeId = $this->getEntityRuntimeId();
		$this->slots[0] = $this->getSlot();
		$this->slots[1] = $this->getSlot();
		$this->slots[2] = $this->getSlot();
		$this->slots[3] = $this->getSlot();
	}

	function encodePayload() : void{
		$this->putEntityRuntimeId($this->entityRuntimeId);
		$this->putSlot($this->slots[0]);
		$this->putSlot($this->slots[1]);
		$this->putSlot($this->slots[2]);
		$this->putSlot($this->slots[3]);
	}
}