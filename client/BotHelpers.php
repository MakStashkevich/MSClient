<?php

namespace client;

use client\utils\PlayerLocation;
use pocketmine\math\Vector3;

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

		/*if ($dx > 0 || $dz > 0) {
			$tanOutput = 90 - self::radianToDegree(atan($dx / $dz));
			$thetaOffset = 270.0;
			if ($dz < 0) {
				$thetaOffset = 90.0;
			}
			$yaw = $thetaOffset + $tanOutput;

			$bDiff = sqrt(($dx * $dx) + ($dz * $dz));
			$dy = $source->getY() - $target->getY();
			$pitch = self::radianToDegree(atan($dy / $bDiff));

			$pos->setYaw($yaw);
			$pos->setHeadYaw($yaw);
			$pos->setPitch($pitch);
		}*/

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
}