<?php

declare(strict_types=1);


namespace protocol;


use client\Client;

class InteractPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::INTERACT_PACKET;

	public const ACTION_RIGHT_CLICK = 1;
	public const ACTION_LEFT_CLICK = 2;
	public const ACTION_LEAVE_VEHICLE = 3;
	public const ACTION_MOUSEOVER = 4;

	public const ACTION_OPEN_INVENTORY = 6;

	public $action;
	public $target;

	function decodePayload() : void{
		$this->action = $this->getByte();
		$this->target = $this->getEntityRuntimeId();
	}

	function encodePayload() : void{
		if (Client::STEADFAST2) {
			// todo: not work
//			$this->encodeHeaderSF2();
			$this->putByte($this->action);
			$this->putVarInt($this->action);
			return;
		}
		$this->putByte($this->action);
		$this->putEntityRuntimeId($this->target);
	}
}