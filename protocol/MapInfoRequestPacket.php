<?php

declare(strict_types=1);


namespace protocol;


class MapInfoRequestPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::MAP_INFO_REQUEST_PACKET;

	public $mapId;

	function decodePayload() : void{
		$this->mapId = $this->getEntityUniqueId();
	}

	function encodePayload() : void{
		$this->putEntityUniqueId($this->mapId);
	}
}