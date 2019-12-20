<?php

declare(strict_types=1);


namespace protocol;


class UnknownPacket extends DataPacket{
	public const NETWORK_ID = -1; //Invalid, do not try to write this

	/** @var string */
	public $payload;

	public function pid() : int{
		if(strlen($this->payload ?? "") > 0){
			return ord($this->payload{0});
		}
		return self::NETWORK_ID;
	}

	public function getName() : string{
		return "unknown packet";
	}

	public function decode() : void{
		$this->payload = $this->getRemaining();
	}

	public function encode() : void{
		//Do not reset the buffer, this class does not have a valid NETWORK_ID constant.
		$this->put($this->payload);
	}
}