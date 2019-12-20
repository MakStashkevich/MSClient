<?php

declare(strict_types=1);


namespace protocol;


class ShowStoreOfferPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SHOW_STORE_OFFER_PACKET;

	public $offerId;

	function decodePayload() : void{
		$this->offerId = $this->getString();
	}

	function encodePayload() : void{
		$this->putString($this->offerId);
	}
}