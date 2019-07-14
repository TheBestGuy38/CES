<?php

namespace DaPigGuy\PiggyCustomEnchants;

use DaPigGuy\PiggyCustomEnchants\CustomEnchants\CustomEnchants;
use DaPigGuy\PiggyCustomEnchants\CustomEnchants\CustomEnchantsIds;
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

    /**
     * @return int
     */
    public function getBounty()
    {
        $random = mt_rand(0, 75);
        $currentchance = 2.5;
        if ($random < $currentchance) {
            return Item::EMERALD;
        }
        $currentchance += 5;
        if ($random < $currentchance) {
            return Item::DIAMOND;
        }
        $currentchance += 15;
        if ($random < $currentchance) {
            return Item::GOLD_INGOT;
        }
        $currentchance += 27.5;
        if ($random < $currentchance) {
            return Item::IRON_INGOT;
        }
        return Item::COAL;
    }
    /**
     * @param EntityEffectAddEvent $event
     *
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function onEffect(EntityEffectAddEvent $event)
    {
        $entity = $event->getEntity();
        if ($entity instanceof Player) {
            $this->checkArmorEnchants($entity, $event);
        }
    }
    /**
     * @param EntityShootBowEvent $event
     *
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function onShoot(EntityShootBowEvent $event)
    {
        $shooter = $event->getEntity();
        $entity = $event->getProjectile();
        if ($shooter instanceof Player) {
            $this->checkBowEnchants($shooter, $entity, $event);
        }
    }
    /**
     * @param InventoryTransactionEvent $event
     *
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function onTransaction(InventoryTransactionEvent $event)
    {
        $transaction = $event->getTransaction();
        foreach ($transaction->getActions() as $action) {
            if ($action instanceof SlotChangeAction) {
                $item_clicked = $action->getSourceItem();
                if ($item_clicked->getId() === Item::ENCHANTED_BOOK) {
                    $enchantedbook_action = $action;
                } elseif (!$item_clicked->isNull()) {
                    $equipment_action = $action;
                }
            }
        }
        if (isset($enchantedbook_action, $equipment_action)) {
            $book = $enchantedbook_action->getSourceItem();
            $equipment = $equipment_action->getSourceItem();
            $player = $transaction->getSource();
            $success = false;
            foreach ($book->getEnchantments() as $enchant) {
                $is_customenchant = $enchant->getType() instanceof CustomEnchants;
                if (!$is_customenchant || $this->plugin->canBeEnchanted($equipment, $enchant, $enchant->getLevel()) === true) {//TODO: Check XP
                    $success = true;
                    if ($is_customenchant) {
                        $equipment = $this->plugin->addEnchantment($equipment, $enchant->getId(), $enchant->getLevel());
                    } else {
                        $equipment->addEnchantment($enchant);
                    }
                } else {
                    $player->sendTip(TextFormat::RED . "The item is not compatible with this enchant.");
                }
            }
            if ($success) {
                $event->setCancelled();
                $book->pop();
                $equipment_action->getInventory()->setItem($equipment_action->getSlot(), $equipment);
                $enchantedbook_action->getInventory()->setItem($enchantedbook_action->getSlot(), $book);
                $player->sendTip(TextFormat::GREEN . "Enchanting succeeded.");
            }
        }
    }
    /**
     * @param PlayerDeathEvent $event
     *
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function onDeath(PlayerDeathEvent $event)
    {
        $player = $event->getEntity();
        $this->checkGlobalEnchants($player, null, $event);
    }
    /**
     * Disable movement being reverted when flying with a Jetpack
     *
     * @param PlayerIllegalMoveEvent $event
     *
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function onIllegalMove(PlayerIllegalMoveEvent $event)
    {
        $player = $event->getPlayer();
        if (isset($this->plugin->flying[$player->getLowerCaseName()]) || $player->getArmorInventory()->getChestplate()->getEnchantment(CustomEnchantsIds::SPIDER) !== null) {
            $event->setCancelled();
        }
    }
    /**
     * @param PlayerInteractEvent $event
     *
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function onInteract(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        $this->checkToolEnchants($player, $event);
    }
    /**
     * Disable kicking for flying when using jetpacks
     *
     * @param PlayerKickEvent $event
     *
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function onKick(PlayerKickEvent $event)
    {
        $player = $event->getPlayer();
        $reason = $event->getReason();
        if ($reason == "Flying is not enabled on this server") {
            if (isset($this->plugin->flying[$player->getLowerCaseName()]) || $player->getArmorInventory()->getChestplate()->getEnchantment(CustomEnchantsIds::SPIDER) !== null) {
                $event->setCancelled();
            }
        }
    }
    /**
     * @param PlayerMoveEvent $event
     *
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function onMove(PlayerMoveEvent $event)
    {
        $player = $event->getPlayer();
        $from = $event->getFrom();
        if (isset($this->plugin->nofall[$player->getLowerCaseName()])) {
            if ($this->plugin->checkBlocks($player, 0, 1) !== true && $this->plugin->nofall[$player->getLowerCaseName()] < time()) {
                unset($this->plugin->nofall[$player->getLowerCaseName()]);
            } else {
                $this->plugin->nofall[$player->getLowerCaseName()]++;
            }
        }
        if ($from->getFloorX() == $player->getFloorX() && $from->getFloorY() == $player->getFloorY() && $from->getFloorZ() == $player->getFloorZ()) {
            $this->plugin->moved[$player->getLowerCaseName()] = 10;
            return;
        }
        $this->plugin->moved[$player->getLowerCaseName()] = 0;
        $this->checkGlobalEnchants($player, null, $event);
        $this->checkArmorEnchants($player, $event);
    }
    /**
     * @param PlayerQuitEvent $event
     */
    public function onQuit(PlayerQuitEvent $event)
    {
        $player = $event->getPlayer();
        $name = $player->getLowerCaseName();
        if (isset($this->plugin->blockface[$name])) {
            unset($this->plugin->blockface[$name]);
        }
        if (isset($this->plugin->glowing[$name])) {
            unset($this->plugin->glowing[$name]);
        }
        if (isset($this->plugin->grew[$name])) {
            unset($this->plugin->grew[$name]);
        }
        if (isset($this->plugin->flying[$name])) {
            unset($this->plugin->flying[$name]);
        }
        if (isset($this->plugin->hallucination[$name])) {
            unset($this->plugin->hallucination[$name]);
        }
        if (isset($this->plugin->implants[$name])) {
            unset($this->plugin->implants[$name]);
        }
        if (isset($this->plugin->mined[$name])) {
            unset($this->plugin->mined[$name]);
        }
        if (isset($this->plugin->nofall[$name])) {
            unset($this->plugin->nofall[$name]);
        }
        for ($i = 0; $i <= 3; $i++) {
            if (isset($this->plugin->overload[$name . "||" . $i])) {
                unset($this->plugin->overload[$name . "||" . $i]);
            }
        }
        if (isset($this->plugin->prowl[$name])) {
            unset($this->plugin->prowl[$name]);
        }
        if (isset($this->plugin->using[$name])) {
            unset($this->plugin->using[$name]);
        }
        if (isset($this->plugin->shrunk[$name])) {
            unset($this->plugin->shrunk[$name]);
        }
    }
    /**
     * @param PlayerToggleSneakEvent $event
     *
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function onSneak(PlayerToggleSneakEvent $event)
    {
        $player = $event->getPlayer();
        if ($event->isSneaking()) {
            $this->checkArmorEnchants($player, $event);
        }
    }
    /**
     * @param ProjectileHitBlockEvent $event
     *
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function onHit(ProjectileHitBlockEvent $event)
    {
        $entity = $event->getEntity();
        $shooter = $entity->getOwningEntity();
        if ($shooter instanceof Player) {
            $this->checkBowEnchants($shooter, $entity, $event);
        }
    }
    /**
     * @param DataPacketReceiveEvent $event
     *
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function onDataPacketReceive(DataPacketReceiveEvent $event)
    {
        $player = $event->getPlayer();
        $packet = $event->getPacket();
        if ($packet instanceof PlayerActionPacket) {
            $action = $packet->action;
            switch ($action) {
                case PlayerActionPacket::ACTION_JUMP:
                    $this->checkArmorEnchants($player, $event);
                    break;
                case PlayerActionPacket::ACTION_CONTINUE_BREAK:
                    $this->plugin->blockface[$player->getLowerCaseName()] = $packet->face;
                    break;
            }
        }
    }
}
