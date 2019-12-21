<?php

namespace client;

use client\utils\PlayerLocation;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use protocol\ContainerSetSlotPacket;
use protocol\MobEquipmentPacket;
use protocol\MovePlayerPacket;
use protocol\PlayerActionPacket;
use protocol\UseItemPacket;

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

	/**
	 * @param PocketEditionClient $client
	 * @param int $windowId
	 * @param Item $item
	 * @param int $selectedSlot
	 */
	static function containerSetSlot(PocketEditionClient $client, int $windowId, Item $item, int $selectedSlot)
	{
		$pk = new ContainerSetSlotPacket();
		$pk->windowid = $windowId;
		$pk->slot = $selectedSlot;
		$pk->hotbarSlot = $selectedSlot + 9;
		$pk->item = $item;
		$client->sendDataPacket($pk);
	}

	/**
	 * @param PocketEditionClient $client
	 * @param Item $item
	 * @param Vector3 $coords
	 */
	static function useItem(PocketEditionClient $client, Item $item, Vector3 $coords)
	{
		$pk = new UseItemPacket();
		$pk->x = $coords->getX();
		$pk->y = $coords->getY();
		$pk->z = $coords->getZ();
		$pk->face = 1;
		$pk->blockId = 116;
		$pk->from = new Vector3(0.1, 0.1, 0.1);
		$pk->position = $client->getPlayer()->getPosition();
		$pk->item = $item;
		$client->sendDataPacket($pk);
	}

	/**
	 * @param PocketEditionClient $client
	 * @param Vector3 $target
	 * @return bool
	 */
	static function moveTo(PocketEditionClient $client, Vector3 $target): bool
	{
		$player = $client->getPlayer();
		$originalPosition = $client->getPlayer()->getPosition();
		$targetPosition = $target;

		// First just rotate towards target pos
		$lookAtPos = self::lookAt($originalPosition->add(0, 1.62), $targetPosition);
		$move = new MovePlayerPacket();
		$move->entityRuntimeId = $player->getId();
		$move->position = $lookAtPos->asVector3();
		$move->yaw = $lookAtPos->getYaw();
		$move->bodyYaw = $lookAtPos->getHeadYaw();
		$move->pitch = $lookAtPos->getPitch();
		$client->sendDataPacket($move);

		$length = abs(($originalPosition->add(-$targetPosition->getX(), -$targetPosition->getY(), -$targetPosition->getZ()))->length());
		$stepLen = 0.5;
		$sleep = 0;

		while (true) {
			if ($sleep > microtime(true)) continue;

			$currentPosition = $player->getPosition();
			if (abs(($targetPosition->add(-$currentPosition->getX(), -$currentPosition->getY(), -$currentPosition->getZ()))->length()) > $stepLen) {
				$lenLeft = abs(($currentPosition->add(-$targetPosition->getX(), -$targetPosition->getY(), -$targetPosition->getZ()))->length());
				$weight = abs((float)(($lenLeft - $stepLen) / $length));

				$player->setPosition($currentPosition = self::lerp($originalPosition, $targetPosition, 1 - $weight));

				$move = new MovePlayerPacket();
				$move->entityRuntimeId = $player->getId();
				$move->position = $currentPosition->asVector3();
				$move->yaw = $lookAtPos->getYaw();
				$move->bodyYaw = $lookAtPos->getHeadYaw();
				$move->pitch = $lookAtPos->getPitch();
				$client->sendDataPacket($move);

				$sleep = microtime(true) + 0.05;
				continue;
			} else {
				$player->setPosition($targetPosition);

				$move = new MovePlayerPacket();
				$move->entityRuntimeId = $player->getId();
				$move->position = $currentPosition->asVector3();
				$move->yaw = $lookAtPos->getYaw();
				$move->bodyYaw = $lookAtPos->getHeadYaw();
				$move->pitch = $lookAtPos->getPitch();
				$client->sendDataPacket($move);

				return true;
			}
			break;
		}

		return false;
	}

	/**
	 * Linearly interpolates between two vectors based on the given weighting.
	 *
	 * @param Vector3 $value1
	 * @param Vector3 $value2
	 * @param float $amount
	 * @return Vector3
	 */
	static function lerp(Vector3 $value1, Vector3 $value2, float $amount)
	{
		return new Vector3(
			$value1->getX() + ($value2->getX() - $value1->getX()) * $amount,
			$value1->getY() + ($value2->getY() - $value1->getY()) * $amount,
			$value1->getZ() + ($value2->getZ() - $value1->getZ()) * $amount
		);
	}
}