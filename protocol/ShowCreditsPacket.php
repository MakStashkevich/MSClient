<?php

declare(strict_types=1);


namespace protocol;


class ShowCreditsPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SHOW_CREDITS_PACKET;

	public const STATUS_START_CREDITS = 0;
	public const STATUS_END_CREDITS = 1;

	public $playerEid;
	public $status;

	function decodePayload() : void{
		$this->playerEid = $this->getEntityRuntimeId();
		$this->status = $this->getVarInt();
	}

	function encodePayload() : void{
		$this->putEntityRuntimeId($this->playerEid);
		$this->putVarInt($this->status);
	}
}