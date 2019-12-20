<?php

namespace client;

use client\utils\PlayerLocation;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use protocol\MobEquipmentPacket;
use protocol\PlayerActionPacket;

class BotHelpers
{
	/**
	 * @param Vector3 $source
	 * @param Vector3 $target
	 * @return PlayerLocation
	 */
	static function lookAt(Vector3 $source, Vector3 $target): PlayerLocation
	{
		$dx = $target->getX() - $source->getX();
		$dz = $target->getZ() - $source->getZ();

		$pos = new PlayerLocation($source->getX(), $source->getY(), $source->getZ());

		$angle = atan2($dz, $dx);
		$yaw = self::radianToDegree($angle) - 90;

		$dy = $target->getY() - $source->getY();
		$dist = sqrt(($dx ** 2) + ($dz ** 2));
		$angle = atan2($dist, $dy);
		$pitch = self::radianToDegree($angle) - 90;

		$pos->setYaw($yaw);
		$pos->setHeadYaw($yaw);
		$pos->setPitch($pitch);
		return $pos;
	}

	/**
	 * @param float $angle
	 * @return float
	 */
	private static function radianToDegree(float $angle): float
	{
		return (float)$angle * (180.0 / M_PI);
	}

	/**
	 * @param PocketEditionClient $client
	 * @param int $action
	 */
	static function action(PocketEditionClient $client, int $action)
	{
		$player = $client->getPlayer();
		$pk = new PlayerActionPacket();
		$pk->entityRuntimeId = $player->getId();
		$pk->action = PlayerActionPacket::ACTION_RESPAWN;

		$pos = $player->getPosition();
		$pk->x = $pos->getFloorX();
		$pk->y = $pos->getFloorY();
		$pk->z = $pos->getFloorZ();

		$pk->face = 0;
		$client->sendDataPacket($pk);
	}

	/**
	 * @param PocketEditionClient $client
	 */
	static function respawn(PocketEditionClient $client)
	{
		self::action($client, PlayerActionPacket::ACTION_RESPAWN);
	}

	/**
	 * @param PocketEditionClient $client
	 * @param Item $item
	 * @param int $selectedSlot
	 */
	static function mobEquipment(PocketEditionClient $client, Item $item, int $selectedSlot)
	{
		$message = new MobEquipmentPacket();
		$message->entityRuntimeId = $client->getId();
		$message->item = $item;
		$message->inventorySlot = $selectedSlot;
		$message->hotbarSlot = $selectedSlot + 9;
		$client->sendDataPacket($message);
	}
}