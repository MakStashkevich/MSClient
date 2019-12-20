<?php

declare(strict_types=1);


namespace protocol;


use pocketmine\item\Item;

class ReplaceItemInSlotPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::REPLACE_ITEM_IN_SLOT_PACKET;

	/** @var Item */
	public $item;

	function decodePayload() : void{
		$this->item = $this->getSlot();
	}

	function encodePayload() : void{
		$this->putSlot($this->item);
	}
}