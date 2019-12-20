<?php

declare(strict_types=1);


namespace protocol;


class PurchaseReceiptPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::PURCHASE_RECEIPT_PACKET;

	/** @var string[] */
	public $entries = [];

	function decodePayload() : void{
		$count = $this->getUnsignedVarInt();
		for($i = 0; $i < $count; ++$i){
			$this->entries[] = $this->getString();
		}
	}

	function encodePayload() : void{
		$this->putUnsignedVarInt(count($this->entries));
		foreach($this->entries as $entry){
			$this->putString($entry);
		}
	}
}