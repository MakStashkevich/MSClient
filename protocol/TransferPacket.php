<?php

declare(strict_types=1);


namespace protocol;


class TransferPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::TRANSFER_PACKET;

	/** @var string */
	public $address;
	/** @var int */
	public $port = 19132;

	function decodePayload() : void{
		$this->address = $this->getString();
		$this->port = $this->getLShort();
	}

	function encodePayload() : void{
		$this->putString($this->address);
		$this->putLShort($this->port);
	}
}