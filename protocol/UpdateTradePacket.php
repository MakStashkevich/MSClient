<?php

declare(strict_types=1);


namespace protocol;


use pocketmine\network\mcpe\protocol\types\WindowTypes;

class UpdateTradePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::UPDATE_TRADE_PACKET;

	//TODO: find fields
	public $windowId;
	public $windowType = WindowTypes::TRADING; //Mojang hardcoded this -_-
	public $varint1;
	public $varint2;
	public $isWilling;
	public $traderEid;
	public $playerEid;
	public $displayName;
	public $offers;

	function decodePayload() : void{
		$this->windowId = $this->getByte();
		$this->windowType = $this->getByte();
		$this->varint1 = $this->getVarInt();
		$this->varint2 = $this->getVarInt();
		$this->isWilling = $this->getBool();
		$this->traderEid = $this->getEntityUniqueId();
		$this->playerEid = $this->getEntityUniqueId();
		$this->displayName = $this->getString();
		$this->offers = $this->getRemaining();
	}

	function encodePayload() : void{
		$this->putByte($this->windowId);
		$this->putByte($this->windowType);
		$this->putVarInt($this->varint1);
		$this->putVarInt($this->varint2);
		$this->putBool($this->isWilling);
		$this->putEntityUniqueId($this->traderEid);
		$this->putEntityUniqueId($this->playerEid);
		$this->putString($this->displayName);
		$this->put($this->offers);
	}
}