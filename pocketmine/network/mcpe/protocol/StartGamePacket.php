<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>


use client\Client;
use pocketmine\network\mcpe\NetworkSession;

class StartGamePacket extends DataPacket{
	const NETWORK_ID = ProtocolInfo::START_GAME_PACKET;

	public $entityUniqueId;
	public $entityRuntimeId;
	public $playerGamemode;
	public $x;
	public $y;
	public $z;
	public $pitch;
	public $yaw;
	public $seed;
	public $dimension;
	public $generator = 1; //default infinite - 0 old, 1 infinite, 2 flat
	public $worldGamemode;
	public $difficulty;
	public $spawnX;
	public $spawnY;
	public $spawnZ;
	public $hasAchievementsDisabled = true;
	public $dayCycleStopTime = -1; //-1 = not stopped, any positive value = stopped at that time
	public $eduMode = false;
	public $rainLevel;
	public $lightningLevel;
	public $commandsEnabled;
	public $isTexturePacksRequired = true;
	public $gameRules = []; //TODO: implement this
	public $levelId = ""; //base64 string, usually the same as world folder name in vanilla
	public $worldName;
	public $premiumWorldTemplateId = "";
	public $unknownBool = false;
	public $currentTick = 0;

	public function decodePayload(){
		if (Client::STEADFAST2) {
			$this->decodeHeaderSF2();
			// https://github.com/Hydreon/Steadfast2/blob/7b5775cb60edeedf1b91a62d1faef514fda13e22/src/pocketmine/network/protocol/StartGamePacket.php#L58

			$this->entityRuntimeId = $this->getVarIntSF2(); // 1
			$this->entityUniqueId = 0; // 0 (not read)

			$this->playerGamemode = $this->getSignedVarIntSF2(); // 0

			$this->x = $this->getLFloat(); // 0
			$this->y = $this->getLFloat(); // 1.6200000047684
			$this->z = $this->getLFloat(); // 0

			$this->pitch = $this->getLFloat(); // 0 (static)
			$this->yaw = $this->getLFloat(); // 0 (static)

			// level settings

			$this->seed = $this->getSignedVarIntSF2(); // -1
			$this->dimension = $this->getSignedVarIntSF2(); // 0
			$this->generator = $this->getSignedVarIntSF2(); // 1
			$this->worldGamemode = $this->getSignedVarIntSF2(); // 0
			$this->difficulty = $this->getSignedVarIntSF2(); // 1 (static)

			// default spawn 3x VarInt
			$this->spawnX = $this->getSignedVarIntSF2(); // 0
			$this->spawnY = $this->getVarIntSF2(); // 1
			$this->spawnZ = $this->getSignedVarIntSF2(); // 0

			$this->hasAchievementsDisabled = $this->getBool(); // 1 (static)

			// DayCycleStopTyme 1x VarInt
			$this->dayCycleStopTime = $this->getSignedVarIntSF2(); // 0 (static)

			$this->eduMode = $this->getBool(); // 0 (static)

			$this->rainLevel = $this->getLFloat(); // 0 (static)
			$this->lightningLevel = $this->getLFloat(); // 0 (static)

			// commands enabled
			$this->commandsEnabled = $this->getBool(); // 1 (static)

			// isTexturepacksRequired 1x Byte
			$this->isTexturePacksRequired = $this->getBool(); // 0 (static)
			return;
		}

		$this->entityUniqueId = $this->getEntityUniqueId();
		$this->entityRuntimeId = $this->getEntityRuntimeId();
		$this->playerGamemode = $this->getVarInt();
		$this->getVector3f($this->x, $this->y, $this->z);
		$this->pitch = $this->getLFloat();
		$this->yaw = $this->getLFloat();
		$this->seed = $this->getVarInt();
		$this->dimension = $this->getVarInt();
		$this->generator = $this->getVarInt();
		$this->worldGamemode = $this->getVarInt();
		$this->difficulty = $this->getVarInt();
		$this->getBlockPosition($this->spawnX, $this->spawnY, $this->spawnZ);
		$this->hasAchievementsDisabled = $this->getBool();
		$this->dayCycleStopTime = $this->getVarInt();
		$this->eduMode = $this->getBool();
		$this->rainLevel = $this->getLFloat();
		$this->lightningLevel = $this->getLFloat();
		$this->commandsEnabled = $this->getBool();
		$this->isTexturePacksRequired = $this->getBool();
		$this->gameRules = $this->getGameRules();
		$this->levelId = $this->getString();
		$this->worldName = $this->getString();
		$this->premiumWorldTemplateId = $this->getString();
		$this->unknownBool = $this->getBool();
		$this->currentTick = $this->getLLong();

	}

	public function encodePayload(){
		$this->putEntityUniqueId($this->entityUniqueId);
		$this->putEntityRuntimeId($this->entityRuntimeId);
		$this->putVarInt($this->playerGamemode);
		$this->putVector3f($this->x, $this->y, $this->z);
		$this->putLFloat($this->pitch);
		$this->putLFloat($this->yaw);
		$this->putVarInt($this->seed);
		$this->putVarInt($this->dimension);
		$this->putVarInt($this->generator);
		$this->putVarInt($this->worldGamemode);
		$this->putVarInt($this->difficulty);
		$this->putBlockPosition($this->spawnX, $this->spawnY, $this->spawnZ);
		$this->putBool($this->hasAchievementsDisabled);
		$this->putVarInt($this->dayCycleStopTime);
		$this->putBool($this->eduMode);
		$this->putLFloat($this->rainLevel);
		$this->putLFloat($this->lightningLevel);
		$this->putBool($this->commandsEnabled);
		$this->putBool($this->isTexturePacksRequired);
		$this->putGameRules($this->gameRules);
		$this->putString($this->levelId);
		$this->putString($this->worldName);
		$this->putString($this->premiumWorldTemplateId);
		$this->putBool($this->unknownBool);
		$this->putLLong($this->currentTick);
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleStartGame($this);
	}

}
