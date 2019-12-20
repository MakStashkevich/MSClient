<?php

namespace client\utils;

use pocketmine\math\Vector3;

class PlayerLocation extends Vector3
{
	/** @var float */
	private $yaw = 0.0;
	private $headYaw = 0.0;
	private $pitch = 0.0;

	/**
	 * PlayerLocation constructor.
	 * @param float $x
	 * @param float $y
	 * @param float $z
	 * @param float $yaw
	 * @param float $headYaw
	 * @param float $pitch
	 */
	function __construct(float $x = 0.0, float $y = 0.0, float $z = 0.0, float $yaw = 0.0, float $headYaw = 0.0, float $pitch = 0.0)
	{
		parent::__construct($x, $y, $z);
		$this->yaw = $yaw;
		$this->headYaw = $headYaw;
		$this->pitch = $pitch;
	}

	/**
	 * @return float
	 */
	function getYaw(): float
	{
		return (float)$this->yaw;
	}

	/**
	 * @return float
	 */
	function getHeadYaw(): float
	{
		return (float)$this->headYaw;
	}

	/**
	 * @return float
	 */
	function getPitch(): float
	{
		return (float)$this->pitch;
	}

	/**
	 * @param float $yaw
	 */
	function setYaw(float $yaw = 0.0)
	{
		$this->yaw = $yaw;
	}

	/**
	 * @param float $yaw
	 */
	function setHeadYaw(float $yaw = 0.0)
	{
		$this->headYaw = $yaw;
	}

	/**
	 * @param float $pitch
	 */
	function setPitch(float $pitch = 0.0)
	{
		$this->pitch = $pitch;
	}
}