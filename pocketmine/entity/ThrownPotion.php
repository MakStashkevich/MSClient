<?php

/**
 *
 *  ____       _                          _
 * |  _ \ _ __(_)___ _ __ ___   __ _ _ __(_)_ __   ___
 * | |_) | '__| / __| '_ ` _ \ / _` | '__| | '_ \ / _ \
 * |  __/| |  | \__ \ | | | | | (_| | |  | | | | |  __/
 * |_|   |_|  |_|___/_| |_| |_|\__,_|_|  |_|_| |_|\___|
 *
 * Prismarine is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author Prismarine Team
 * @link   https://github.com/PrismarineMC/Prismarine
 *
 *
 */

declare(strict_types=1);

namespace pocketmine\entity;

use pocketmine\entity\Living;
use pocketmine\item\Potion;
use pocketmine\level\Level;
use pocketmine\level\particle\SplashPotionParticle;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\Player;
use pocketmine\utils\Color;

class ThrownPotion extends Projectile{
	const NETWORK_ID = 86;

	const DATA_POTION_ID = 37;

	public $width = 0.25;
	public $length = 0.25;
	public $height = 0.25;

	protected $gravity = 0.1;
	protected $drag = 0.05;

	private $hasSplashed = false;

	/**
	 * ThrownPotion constructor.
	 *
	 * @param Level       $level
	 * @param CompoundTag $nbt
	 * @param Entity|null $shootingEntity
	 */
	public function __construct(Level $level, CompoundTag $nbt, Entity $shootingEntity = null){
		if(!isset($nbt->PotionId)){
			$nbt->PotionId = new ShortTag("PotionId", Potion::AWKWARD);
		}

		parent::__construct($level, $nbt, $shootingEntity);
		$this->setDataProperty(self::DATA_POTION_ID, self::DATA_TYPE_SHORT, $this->getPotionId());
	}

	/**
	 * @return int
	 */
	public function getPotionId() : int{
		return (int) $this->namedtag["PotionId"];
	}

	public function splash(){
		if(!$this->hasSplashed){
			$this->hasSplashed = true;
			$color = [0x38, 0x5d, 0xc6];
			$effect = Potion::getEffectByMeta($this->getPotionId());
			if($effect !== null){
				$color = $effect->getColor();
			}
			$this->getLevel()->addParticle(new SplashPotionParticle($this, $color[0], $color[1], $color[2]));
			$this->getLevel()->broadcastLevelSoundEvent($this, LevelSoundEventPacket::SOUND_GLASS);
			if($effect !== null){
				foreach($this->getLevel()->getNearbyEntities($this->getBoundingBox()->grow(4.125, 2.125, 4.125)) as $e){
					if($e instanceof Living){
						$distanceSquared = $e->distanceSquared($this);
						if($distanceSquared > 16){
							continue;
						}
						$modifier = 0.25 * (4 - floor(sqrt($distanceSquared)));
						if($modifier <= 0){
							continue;
						}
						$eff = clone $effect;
						if($eff->isInstant()){
							$eff->setPotency($modifier);
						}else{
							$duration = (int) round($effect->getDuration() * 0.75 * $modifier);
							if($duration < 20){
								continue;
							}
							$eff->setDuration($duration);
						}
						$e->addEffect($eff);
					}
				}
			}

			$this->kill();
		}
	}

	public function onCollideWithEntity(Entity $entity){
		if($entity instanceof Player and $entity->isSpectator()){
			return;
		}
		
		$this->splash();
	}

	/**
	 * @param $currentTick
	 *
	 * @return bool
	 */
	public function onUpdate($currentTick){
		if($this->closed){
			return false;
		}

		$this->timings->startTiming();

		$hasUpdate = parent::onUpdate($currentTick);

		if($this->age > 1200){
			$this->kill();
			$hasUpdate = true;
		}elseif($this->isCollided){
			$this->splash();
			$hasUpdate = true;
		}

		$this->timings->stopTiming();

		return $hasUpdate;
	}

	/**
	 * @param Player $player
	 */
	public function spawnTo(Player $player){
		$pk = new AddEntityPacket();
		$pk->type = ThrownPotion::NETWORK_ID;
		$pk->entityRuntimeId = $this->getId();
		$pk->x = $this->x;
		$pk->y = $this->y;
		$pk->z = $this->z;
		$pk->speedX = $this->motionX;
		$pk->speedY = $this->motionY;
		$pk->speedZ = $this->motionZ;
		$pk->metadata = $this->dataProperties;
		$player->dataPacket($pk);

		parent::spawnTo($player);
	}
}
