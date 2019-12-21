<?php

namespace client;

use pocketmine\entity\Attribute;
use pocketmine\math\Vector3;

/**
 * Created by PhpStorm.
 * User: MakStashkevich
 * Date: 10.03.2019
 * Time: 13:00
 */
class Bot
{
	/** @var string */
	const USERNAME = 'MakSim0101';
	const PASSWORD = 'abcdefg';

	/** @var int */
	const HEALTH = 20;
	const HUNGER = 20;
	const FOOD = 20;

	/** @var int */
	private $id = 0;

	/** @var string */
	private $username;
	/** @var string */
	private $password;
	/** @var string */
	private $skin = '';
	/** @var Address */
	private $address;

	/** @var float */
	public $x = 0.0;
	/** @var float */
	public $y = 0.0;
	/** @var float */
	public $z = 0.0;

	/** @var float */
	public $yaw = 0.0;
	/** @var float */
	public $pitch = 0.0;

	/** @var int */
	private $health = self::HEALTH;
	private $maxHealth = self::HEALTH;

	/** @var int */
	private $hunger = self::HUNGER;
	private $maxHunger = self::HUNGER;

	/** @var int */
	private $food = self::FOOD;
	private $maxFood = self::FOOD;

	/** @var int */
	public $seekId = 0;

	/** @var bool */
	public $death = false;

	/** @var Level */
	public $level;
	/** @var string */
	public $levelName = 'world';

	/**
	 * Bot constructor.
	 * @param string $username
	 * @param string $password
	 * @param Address|null $address
	 * @param Skin|null $skin
	 */
	function __construct(string $username = null, string $password = null, Address $address = null, Skin $skin = null)
	{
		$this->username = $username ?? self::USERNAME;
		$this->password = $password ?? self::PASSWORD;
		$this->address = $address ?? new Address('0.0.0.0', 19130);
		$this->skin = $skin;
		$this->level = new Level();
	}

	/**
	 * @param float $x
	 * @param float $y
	 * @param float $z
	 * @param float $yaw
	 * @param float $pitch
	 */
	function setLocation(float $x, float $y, float $z, float $yaw, float $pitch)
	{
		$this->x = $x;
		$this->y = $y;
		$this->z = $z;
		$this->yaw = $yaw;
		$this->pitch = $pitch;
	}

	/**
	 * @param Vector3 $position
	 */
	function setPosition(Vector3 $position)
	{
		$this->x = $position->x;
		$this->y = $position->y;
		$this->z = $position->z;
	}

	/**
	 * @return Vector3
	 */
	function getPosition(): Vector3
	{
		return new Vector3($this->x, $this->y, $this->z);
	}

	/**
	 * @return float
	 */
	function getYaw(): float
	{
		return $this->yaw;
	}

	/**
	 * @return float
	 */
	function getPitch(): float
	{
		return $this->pitch;
	}

	/**
	 * @param int $id
	 */
	function setId(int $id)
	{
		$this->id = $id;
	}

	/**
	 * @return int
	 */
	function getId(): int
	{
		return $this->id;
	}

	/**
	 * @param string $level
	 */
	function setLevelName(string $level)
	{
		$this->levelName = $level;
	}

	/**
	 * @return mixed
	 */
	function getLevelName(): string
	{
		return $this->levelName;
	}

	/**
	 * @return Level
	 */
	function getLevel(): Level
	{
		return $this->level;
	}

	/**
	 * @return int
	 */
	function getHealth(): int
	{
		return $this->health;
	}

	/**
	 * @return int
	 */
	function getMaxHealth(): int
	{
		return $this->maxHealth;
	}

	/**
	 * @return int
	 */
	function getHunger(): int
	{
		return $this->hunger;
	}

	/**
	 * @return int
	 */
	function getMaxHunger(): int
	{
		return $this->maxHunger;
	}

	/**
	 * @return int
	 */
	function getFood(): int
	{
		return $this->food;
	}

	/**
	 * @return int
	 */
	function getMaxFood(): int
	{
		return $this->maxFood;
	}

	/**
	 * @param $attributes
	 */
	function setAttribute($attributes)
	{
		/** @var Attribute $attribute */
		foreach ($attributes as $attribute) {
			switch ($attribute->getId()) {
				case Attribute::HEALTH:
					$this->health = $attribute->getValue();
					$this->maxHealth = $attribute->getMaxValue();

					if ($this->health < 1) {
						send('Bot death....');
						$this->death = true;
					}
					break;
				case Attribute::HUNGER:
					$this->hunger = $attribute->getValue();
					$this->maxHunger = $attribute->getMaxValue();
					break;
				case Attribute::FOOD:
					$this->food = $attribute->getValue();
					$this->maxFood = $attribute->getMaxValue();
					break;
			}
		}
	}

	/**
	 * @param bool $death
	 */
	function setDeath(bool $death = true)
	{
		$this->death = $death;
	}

	/**
	 * @return bool
	 */
	function isDeath(): bool
	{
		return $this->death;
	}

	/**
	 * @return string
	 */
	function getName(): string
	{
		return $this->username;
	}

	/**
	 * @return Address
	 */
	function getAddress(): Address
	{
		return $this->address;
	}

	/**
	 * @return string
	 */
	function getPassword(): string
	{
		return $this->password;
	}

	/**
	 * @return Skin|null
	 */
	function getSkin()
	{
		return $this->skin;
	}

	/**
	 * @return null|string
	 */
	function getSkinData()
	{
		return isset($this->skin) ? $this->skin->getSkinData() : null;
	}

	/**
	 * @return null|string
	 */
	function getCapeData()
	{
		return isset($this->skin) ? $this->skin->getCapeData() : null;
	}
}