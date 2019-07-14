<?php

namespace DaPigGuy\PiggyCustomEnchants;

use pocketmine\block\Block;
use pocketmine\block\Crops;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\entity\projectile\Arrow;
use pocketmine\entity\projectile\Projectile;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityArmorChangeEvent;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityEffectAddEvent;
use pocketmine\event\entity\EntityEvent;
use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\event\entity\ProjectileHitBlockEvent;
use pocketmine\event\Event;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\cheat\PlayerIllegalMoveEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerToggleSneakEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Axe;
use pocketmine\item\Item;
use pocketmine\item\Sword;
use pocketmine\level\particle\FlameParticle;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\network\mcpe\protocol\PlayerActionPacket;
use pocketmine\Player;
use pocketmine\utils\Random;
use pocketmine\utils\TextFormat;

//SWORD
  $enchantment = $damager->getInventory()->getItemInHand()->getEnchantment(CustomEnchantsIds::WITHER);
                if ($enchantment !== null && $entity->hasEffect(Effect::WITHER) !== true) {
                    $effect = new EffectInstance(Effect::getEffect(Effect::WITHER), 60 * $enchantment->getLevel(), $enchantment->getLevel(), false);
                    $entity->addEffect($effect);
                }
            }

 $enchantment = $damager->getInventory()->getItemInHand()->getEnchantment(CustomEnchantsIds::ANTI_GRAVITY);
                if ($enchantment !== null && $entity->hasEffect(Effect::LEVITATION) !== true) {
                    $effect = new EffectInstance(Effect::getEffect(Effect::LEVITaTION), 4 * $enchantment->getLevel(), $enchantment->getLevel(), false);
                    $entity->addEffect($effect);
                }
            }
         
  
     $enchantment = $damager->getInventory()->getItemInHand()->getEnchantment(CustomEnchantsIds::LIFESTEAL);
            if ($enchantment !== null) {
                if ($damager->getHealth() + 2 + $enchantment->getLevel() <= $damager->getMaxHealth()) {
                    $damager->setHealth($damager->getHealth() + 2 + $enchantment->getLevel());
                } else {
                    $damager->setHealth($damager->getMaxHealth());
                }
            }



//ARMOUR
$enchantment = $armor->getEnchantment(CustomEnchantsIds::GEARS);
                        if ($enchantment !== null && $entity->hasEffect(Effect::SPEED) !== true) {
                            $effect = new EffectInstance(Effect::getEffect(Effect::SPEED), 10000000 * $enchantment->getLevel(), $enchantment->getLevel(), false);
                            $entity->addEffect($effect);
                        }
