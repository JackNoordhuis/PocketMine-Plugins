<?php

/**
 * Created by PhpStorm.
 * User: Jack
 * Date: 27/09/2016
 * Time: 5:01 PM
 */

namespace explosivearrows;

use pocketmine\entity\Arrow;
use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\event\Listener;
use pocketmine\level\Explosion;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\Player;

class EventListener implements Listener {

	/** @var ExplosiveArrows */
	private $plugin = null;

	/** @var bool */
	protected $terrainDamage;

	/** @var int */
	protected $defaultSize;

	/** @var bool */
	private $closed = false;

	public function __construct(ExplosiveArrows $plugin) {
		$this->plugin = $plugin;
		$this->terrainDamage = $plugin->getSettings()->get("terrain-damage", false);
		$this->defaultSize = $plugin->getSettings()->get("default-explosion-size", 4);
		$plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
	}

	public function getPlugin() : ExplosiveArrows {
		return $this->plugin;
	}

	/**
	 * Make sure we add the custom bow data to the arrow
	 *
	 * @param EntityShootBowEvent $event
	 *
	 * @ignoreCancelled true
	 *
	 * @priority HIGHEST
	 */
	public function onShoot(EntityShootBowEvent $event) {
		$projectile = $event->getProjectile();
		if($projectile instanceof Arrow) {
			$bow = $event->getBow();
			if($bow->hasCompoundTag()) {
				$tag = $bow->getNamedTag();
				if(isset($tag->TerrainDamage)) {
					$projectile->namedtag->TerrainDamage = new ByteTag("TerrainDamage", $tag["TerrainDamage"]);
				}
				if(isset($tag->ExplosionSize)) {
					$projectile->namedtag->ExplosionSize = new IntTag("ExplosionSize", $tag["ExplosionSize"]);
				}
			}
		}
	}

	/**
	 * Handle our awesome explosions ^-^
	 *
	 * @param ProjectileHitEvent $event
	 *
	 * @ignoreCancelled true
	 *
	 * @priority HIGHEST
	 */
	public function onHit(ProjectileHitEvent $event) {
		$projectile = $event->getEntity();
		if($projectile->isAlive() and $projectile instanceof Arrow) {
			$shooter = $projectile->shootingEntity;
			if($shooter instanceof Player) {
				$tag = $projectile->namedtag;
				$terrainDamage = $this->terrainDamage;
				$size = $this->defaultSize;
				if($tag instanceof CompoundTag) {
					if(isset($tag->TerrainDamage)) {
						$terrainDamage = ExplosiveArrows::byteToBool($tag["TerrainDamage"]);
					}
					if(isset($tag->ExplosionSize)) {
						$size = (int)$tag["ExplosionSize"];
					}
				}
				if($size > 0) {
					$explosion = new Explosion($projectile->getPosition(), $size);
					if($terrainDamage) {
						$explosion->explodeA();
					}
					$explosion->explodeB();
					$projectile->kill();
				}
			}
		}
	}

	/**
	 * Dump all the class properties safely
	 */
	public function close() {
		if(!$this->closed) {
			$this->closed = true;
			unset($this->plugin, $this->toHit);
		}
	}

	public function __destruct() {
		$this->close();
	}
}