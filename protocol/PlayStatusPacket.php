<?php

declare(strict_types=1);


namespace protocol;


class PlayStatusPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::PLAY_STATUS_PACKET;

	public const LOGIN_SUCCESS = 0;
	public const LOGIN_FAILED_CLIENT = 1;
	public const LOGIN_FAILED_SERVER = 2;
	public const PLAYER_SPAWN = 3;
	public const LOGIN_FAILED_INVALID_TENANT = 4;
	public const LOGIN_FAILED_VANILLA_EDU = 5;
	public const LOGIN_FAILED_EDU_VANILLA = 6;

	/** @var int */
	public $status;

	function decodePayload() : void{
		$this->status = $this->getInt();
	}

	function encodePayload() : void{
		$this->putInt($this->status);
	}
}