<?php

declare(strict_types=1);


namespace protocol;


class StopSoundPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::STOP_SOUND_PACKET;

	public $soundName;
	public $stopAll;

	function decodePayload() : void{
		$this->soundName = $this->getString();
		$this->stopAll = $this->getBool();
	}

	function encodePayload() : void{
		$this->putString($this->soundName);
		$this->putBool($this->stopAll);
	}
}