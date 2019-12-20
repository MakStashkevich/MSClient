<?php

declare(strict_types=1);


namespace protocol;


use pocketmine\item\Item;

class AddItemPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::ADD_ITEM_PACKET;

	/** @var Item */
	public $item;

	function decodePayload() : void{
		$this->item = $this->getSlot();
	}

	function encodePayload() : void{
		$this->putSlot($this->item);
	}
}