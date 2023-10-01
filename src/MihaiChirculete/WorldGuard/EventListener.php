<?php

/**
 *
 *  _     _  _______  ______    ___      ______   _______  __   __  _______  ______    ______
 * | | _ | ||       ||    _ |  |   |    |      | |       ||  | |  ||   _   ||    _ |  |      |
 * | || || ||   _   ||   | ||  |   |    |  _    ||    ___||  | |  ||  |_|  ||   | ||  |  _    |
 * |       ||  | |  ||   |_||_ |   |    | | |   ||   | __ |  |_|  ||       ||   |_||_ | | |   |
 * |       ||  |_|  ||    __  ||   |___ | |_|   ||   ||  ||       ||       ||    __  || |_|   |
 * |   _   ||       ||   |  | ||       ||       ||   |_| ||       ||   _   ||   |  | ||       |
 * |__| |__||_______||___|  |_||_______||______| |_______||_______||__| |__||___|  |_||______|
 *
 * By MihaiChirculete.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * GitHub: https://github.com/MihaiChirculete
 */

namespace MihaiChirculete\WorldGuard;

use pocketmine\event\block\{BlockPlaceEvent,
    BlockBreakEvent,
    LeavesDecayEvent,
    BlockGrowEvent,
    BlockUpdateEvent,
    BlockSpreadEvent,
    BlockBurnEvent
};
use pocketmine\event\entity\{EntityDamageEvent, EntityDamageByEntityEvent, EntityExplodeEvent, ProjectileLaunchEvent};
use pocketmine\block\BlockTypeIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\projectile\Arrow;
use pocketmine\entity\projectile\EnderPearl;
use pocketmine\event\Listener;
use pocketmine\event\player\{PlayerJoinEvent,
    PlayerMoveEvent,
    PlayerInteractEvent,
    PlayerItemConsumeEvent,
    PlayerDropItemEvent,
    PlayerBedEnterEvent,
    PlayerChatEvent,
    PlayerExhaustEvent,
    PlayerDeathEvent,
    PlayerQuitEvent
};
use pocketmine\event\server\CommandEvent;
use pocketmine\item\Bucket;
use pocketmine\item\LiquidBucket;
use pocketmine\item\VanillaItems;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;
use pocketmine\world\Position;
use function json_encode;

class EventListener implements Listener{

    /** @var WorldGuard $plugin */
    private WorldGuard $plugin;

    /** @var array $POTIONS */
    public static array $POTIONS;
    /** @var array $OTHER */
    public static array $OTHER;
    /** @var array $USABLES */
    public static array $USABLES;
    /** @var array $ITEM_FRAME */
    public static array $ITEM_FRAME;

    public function __construct(WorldGuard $plugin){
        $this->plugin = $plugin;
        self::$POTIONS = [VanillaItems::POTION()->getTypeId(), VanillaItems::SPLASH_POTION()->getType(),
            VanillaItems::GLASS_BOTTLE()->getTypeId(), VanillaItems::EXPERIENCE_BOTTLE()->getTypeId(),
            VanillaItems::DRAGON_BREATH()->getTypeId()];

        self::$OTHER = [VanillaItems::FLINT_AND_STEEL()->getTypeId(),
            VanillaItems::WOODEN_SHOVEL()->getTypeId(), VanillaItems::GOLDEN_SHOVEL()->getTypeId(),
            VanillaItems::DIAMOND_SHOVEL()->getTypeId(), VanillaItems::IRON_SHOVEL()->getTypeId(),
            VanillaItems::NETHERITE_SHOVEL()->getTypeId(), VanillaItems::STONE_SHOVEL()->getTypeId(),
            VanillaItems::WOODEN_HOE()->getTypeId(), VanillaItems::GOLDEN_HOE()->getTypeId(),
            VanillaItems::DIAMOND_HOE()->getTypeId(), VanillaItems::IRON_HOE()->getTypeId(),
            VanillaItems::NETHERITE_HOE()->getTypeId(), VanillaItems::STONE_HOE()->getTypeId()];

        self::$USABLES = [
            VanillaBlocks::BARREL()->getTypeId(), VanillaBlocks::CHEST()->getTypeId(), VanillaBlocks::ENDER_CHEST()->getTypeId(), 
            VanillaBlocks::TRAPPED_CHEST()->getTypeId(), VanillaBlocks::ENCHANTING_TABLE()->getTypeId(),
            
            VanillaBlocks::FURNACE()->getTypeId(), VanillaBlocks::BLAST_FURNACE()->getTypeId(), VanillaBlocks::SMOKER()->getTypeId(),

            VanillaBlocks::SHULKER_BOX()->getTypeId(), VanillaBlocks::DYED_SHULKER_BOX()->getTypeId(),
            
            VanillaBlocks::OAK_DOOR()->getTypeId(), VanillaBlocks::DARK_OAK_DOOR()->getTypeId(), VanillaBlocks::BIRCH_DOOR()->getTypeId(),
            VanillaBlocks::ACACIA_DOOR()->getTypeId(), VanillaBlocks::CHERRY_DOOR()->getTypeId(), VanillaBlocks::JUNGLE_DOOR()->getTypeId(), VanillaBlocks::SPRUCE_DOOR()->getTypeId(),
            VanillaBlocks::WARPED_DOOR()->getTypeId(), VanillaBlocks::CRIMSON_DOOR()->getTypeId(), VanillaBlocks::MANGROVE_DOOR()->getTypeId(),

            VanillaBlocks::OAK_TRAPDOOR()->getTypeId(), VanillaBlocks::IRON_TRAPDOOR()->getTypeId(), VanillaBlocks::BIRCH_TRAPDOOR()->getTypeId(),
            VanillaBlocks::ACACIA_TRAPDOOR()->getTypeId(), VanillaBlocks::CHERRY_TRAPDOOR()->getTypeId(), VanillaBlocks::JUNGLE_TRAPDOOR()->getTypeId(),
            VanillaBlocks::SPRUCE_TRAPDOOR()->getTypeId(), VanillaBlocks::WARPED_TRAPDOOR()->getTypeId(), VanillaBlocks::CRIMSON_TRAPDOOR()->getTypeId(),
            VanillaBlocks::DARK_OAK_TRAPDOOR()->getTypeId(), VanillaBlocks::MANGROVE_TRAPDOOR()->getTypeId(), 
            
            VanillaBlocks::OAK_FENCE()->getTypeId(), VanillaBlocks::BIRCH_FENCE()->getTypeId(), VanillaBlocks::ACACIA_FENCE()->getTypeId(), VanillaBlocks::CHERRY_FENCE()->getTypeId(), 
            VanillaBlocks::JUNGLE_FENCE()->getTypeId(), VanillaBlocks::SPRUCE_FENCE()->getTypeId(), VanillaBlocks::WARPED_FENCE()->getTypeId(), VanillaBlocks::CRIMSON_FENCE()->getTypeId(), VanillaBlocks::MANGROVE_FENCE()->getTypeId(),

            VanillaBlocks::OAK_FENCE_GATE()->getTypeId(), VanillaBlocks::BIRCH_FENCE_GATE()->getTypeId(), VanillaBlocks::ACACIA_FENCE_GATE()->getTypeId(), VanillaBlocks::CHERRY_FENCE_GATE()->getTypeId(), 
            VanillaBlocks::JUNGLE_FENCE_GATE()->getTypeId(),VanillaBlocks::SPRUCE_FENCE_GATE()->getTypeId(), VanillaBlocks::WARPED_FENCE_GATE()->getTypeId(), VanillaBlocks::CRIMSON_FENCE_GATE()->getTypeId(), VanillaBlocks::DARK_OAK_FENCE_GATE()->getTypeId(),
            VanillaBlocks::MANGROVE_FENCE_GATE()->getTypeId(), 
            
            VanillaBlocks::ANVIL()->getTypeId(), VanillaBlocks::CAULDRON()->getTypeId(), VanillaBlocks::BEACON()->getTypeId(),
            VanillaBlocks::CRAFTING_TABLE()->getTypeId(), VanillaBlocks::NOTE_BLOCK()->getTypeId(),

            VanillaBlocks::OAK_PRESSURE_PLATE()->getTypeId(), VanillaBlocks::BIRCH_PRESSURE_PLATE()->getTypeId(), VanillaBlocks::STONE_PRESSURE_PLATE()->getTypeId(),
            VanillaBlocks::ACACIA_PRESSURE_PLATE()->getTypeId(), VanillaBlocks::CHERRY_PRESSURE_PLATE()->getTypeId(), VanillaBlocks::JUNGLE_PRESSURE_PLATE()->getTypeId(),
            VanillaBlocks::SPRUCE_PRESSURE_PLATE()->getTypeId(), VanillaBlocks::WARPED_PRESSURE_PLATE()->getTypeId(), VanillaBlocks::CRIMSON_PRESSURE_PLATE()->getTypeId(),
            VanillaBlocks::DARK_OAK_PRESSURE_PLATE()->getTypeId(), VanillaBlocks::MANGROVE_PRESSURE_PLATE()->getTypeId(), VanillaBlocks::WEIGHTED_PRESSURE_PLATE_HEAVY()->getTypeId(),
            VanillaBlocks::WEIGHTED_PRESSURE_PLATE_LIGHT()->getTypeId(), VanillaBlocks::POLISHED_BLACKSTONE_PRESSURE_PLATE()->getTypeId(),

            VanillaBlocks::OAK_BUTTON()->getTypeId(), VanillaBlocks::BIRCH_BUTTON()->getTypeId(), VanillaBlocks::STONE_BUTTON()->getTypeId(), VanillaBlocks::ACACIA_BUTTON()->getTypeId(),
            VanillaBlocks::CHERRY_BUTTON()->getTypeId(), VanillaBlocks::JUNGLE_BUTTON()->getTypeId(), VanillaBlocks::SPRUCE_BUTTON()->getTypeId(),
            VanillaBlocks::WARPED_BUTTON()->getTypeId(), VanillaBlocks::CRIMSON_BUTTON()->getTypeId(), VanillaBlocks::DARK_OAK_BUTTON()->getTypeId(),
            VanillaBlocks::MANGROVE_BUTTON()->getTypeId(), VanillaBlocks::POLISHED_BLACKSTONE_BUTTON()->getTypeId(),
        ];

        self::$ITEM_FRAME = [
            VanillaBlocks::ITEM_FRAME()->getTypeId(), VanillaBlocks::GLOWING_ITEM_FRAME()->getTypeId()
        ];
    }

    /**
     * @priority MONITOR
     */
    public function onJoin(PlayerJoinEvent $event): void{
        $this->plugin->sessionizePlayer($event->getPlayer());
    }

    public function onLeave(PlayerQuitEvent $event): void{
        $this->plugin->onPlayerLogoutRegion($event->getPlayer());
    }

    /**
     * @param PlayerInteractEvent $event
     * @return void
     */
    public function onInteract(PlayerInteractEvent $event): void{
        if($event->getItem() instanceof Bucket || $event->getItem() instanceof LiquidBucket){
            $player = $event->getPlayer();
            if(($reg = $this->plugin->getRegionFromPosition($event->getBlock()->getPosition())) !== ""){
                if($reg->getFlag("block-place") === "false"){
                    if($player->hasPermission("worldguard.place." . $reg->getName()) || $player->hasPermission("worldguard.block-place." . $reg->getName())){
                        return;
                    }
                    if($player->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
                        return;
                    }else{
                        $event->cancel();
                        if($reg->getFlag("deny-msg") === "true"){
                            $player->sendMessage(TF::RED. $this->plugin->resourceManager->getMessages()["denied-block-place"]);
                        }
                        return;
                    }
                }
            }
        }
        if(isset($this->plugin->creating[$id = ($player = $event->getPlayer())->getUniqueId()->getBytes()])){
            if($event->getAction() === $event::RIGHT_CLICK_BLOCK){
                $block = $event->getBlock();
                $x = $block->getPosition()->getX();
                $y = $block->getPosition()->getY();
                $z = $block->getPosition()->getZ();
                $world = $block->getPosition()->getWorld()->getDisplayName();
                if($x < 0){
                    $x = ($x + 1);
                }
                if($z < 0){
                    $z = ($z + 1);
                }
                $player->sendMessage(TF::YELLOW.'Selected position: X'.$x.', Y: '.$y.', Z: '.$z.', Level: '.$world);
                if(!isset($this->plugin->extended[$id = ($player = $event->getPlayer())->getUniqueId()->getBytes()])){
                    $this->plugin->creating[$id][] = [$x, $y, $z, $world];
                }
                else{
                    if(count($this->plugin->creating[$id]) == 0){
                        $this->plugin->creating[$id][] = [$x, 0, $z, $world];
                    }
                    elseif(count($this->plugin->creating[$id]) >= 1){
                        $this->plugin->creating[$id][] = [$x, 255, $z, $world];
                    }
                }
                if(count($this->plugin->creating[$id]) >= 2){
                    if(($reg = $this->plugin->processCreation($player)) !== false){
                        $player->sendMessage(TF::GREEN.'Successfully created region '.$reg);
                    }else{
                        $player->sendMessage(TF::RED.'An error occurred while creating the region.');
                    }
                }
                $event->cancel();
                return;
            }
        }
        if(($reg = $this->plugin->getRegionByPlayer($player)) !== ""){
            if($reg->getFlag("pluginbypass") === "false"){
                $block = $event->getBlock()->getTypeId();
                if($reg->getFlag("interactframe") === "false"){
                        if($player->hasPermission("worldguard.interactframe") && ($block === BlockTypeIds::ITEM_FRAME || $block === BlockTypeIds::GLOWING_ITEM_FRAME))
                            return;
                        if(in_array($block, self::$ITEM_FRAME)){
                            $event->cancel();
                            return;
                        }
                }else{
                    $event->uncancel();
                    return;
                }
                if($reg->getFlag("use") === "false"){
                    if($player->hasPermission("worldguard.usebarrel." . $reg->getName()) && $block === BlockTypeIds::BARREL)
                        return;
                    if($player->hasPermission("worldguard.usechest." . $reg->getName()) && $block === BlockTypeIds::CHEST)
                        return;
                    if($player->hasPermission("worldguard.usechestender." . $reg->getName()) && $block === BlockTypeIds::ENDER_CHEST)
                        return;
                    if($player->hasPermission("worldguard.usetrappedchest." . $reg->getName()) && $block === BlockTypeIds::TRAPPED_CHEST)
                        return;
                    if($player->hasPermission("worldguard.enchantingtable." . $reg->getName()) && $block === BlockTypeIds::ENCHANTING_TABLE)
                        return;
                    if($player->hasPermission("worldguard.useshulkers." . $reg->getName()) && ($block === BlockTypeIds::SHULKER_BOX || $block === BlockTypeIds::DYED_SHULKER_BOX))
                        return;
                    if($player->hasPermission("worldguard.usefurnaces." . $reg->getName()) && ($block === BlockTypeIds::FURNACE || $block === BlockTypeIds::BLAST_FURNACE || $block === BlockTypeIds::SMOKER))
                        return;
                    if($player->hasPermission("worldguard.usedoors." . $reg->getName()) && ($block === BlockTypeIds::ACACIA_DOOR || $block === BlockTypeIds::BIRCH_DOOR || $block === BlockTypeIds::DARK_OAK_DOOR || $block === BlockTypeIds::IRON_DOOR || $block === BlockTypeIds::JUNGLE_DOOR || $block === BlockTypeIds::OAK_DOOR || $block === BlockTypeIds::SPRUCE_DOOR || $block === BlockTypeIds::CHERRY_DOOR || ($block === BlockTypeIds::IRON_DOOR || in_array($block, [BlockTypeIds::ACACIA_DOOR, BlockTypeIds::BIRCH_DOOR, BlockTypeIds::CRIMSON_DOOR, BlockTypeIds::JUNGLE_DOOR, BlockTypeIds::DARK_OAK_DOOR, BlockTypeIds::MANGROVE_DOOR, BlockTypeIds::SPRUCE_DOOR, BlockTypeIds::CHERRY_DOOR, BlockTypeIds::CRIMSON_DOOR, BlockTypeIds::WARPED_DOOR, BlockTypeIds::OAK_DOOR]))))
                        return;
                    if($player->hasPermission("worldguard.usetrapdoors." . $reg->getName()) && ($block === BlockTypeIds::IRON_TRAPDOOR || in_array($block, [BlockTypeIds::CHERRY_TRAPDOOR, BlockTypeIds::ACACIA_TRAPDOOR, BlockTypeIds::BIRCH_TRAPDOOR, BlockTypeIds::CRIMSON_TRAPDOOR, BlockTypeIds::JUNGLE_TRAPDOOR, BlockTypeIds::DARK_OAK_TRAPDOOR, BlockTypeIds::MANGROVE_TRAPDOOR, BlockTypeIds::SPRUCE_TRAPDOOR, BlockTypeIds::CRIMSON_TRAPDOOR, BlockTypeIds::WARPED_TRAPDOOR, BlockTypeIds::OAK_TRAPDOOR])))
                        return;
                    if($player->hasPermission("worldguard.usegates." . $reg->getName()) && ($block === BlockTypeIds::ACACIA_FENCE_GATE  || $block === BlockTypeIds::BIRCH_FENCE_GATE || $block === BlockTypeIds::DARK_OAK_FENCE_GATE || $block === BlockTypeIds::OAK_FENCE_GATE || $block === BlockTypeIds::JUNGLE_FENCE_GATE || $block === BlockTypeIds::OAK_FENCE_GATE || $block === BlockTypeIds::SPRUCE_FENCE_GATE || $block === BlockTypeIds::CHERRY_FENCE_GATE))
                        return;
                    if($player->hasPermission("worldguard.useanvil." . $reg->getName()) && ($block === BlockTypeIds::ANVIL))
                        return;
                    if($player->hasPermission("worldguard.usecauldron." . $reg->getName()) && ($block === BlockTypeIds::CAULDRON))
                        return;
                    if($player->hasPermission("worldguard.usebrewingstand." . $reg->getName()) && ($block === BlockTypeIds::BREWING_STAND))
                        return;
                    if($player->hasPermission("worldguard.usebeacon." . $reg->getName()) && ($block === BlockTypeIds::BEACON ))
                        return;
                    if($player->hasPermission("worldguard.usecraftingtable." . $reg->getName()) && ($block === BlockTypeIds::CRAFTING_TABLE ))
                        return;
                    if($player->hasPermission("worldguard.usenoteblock." . $reg->getName()) && ($block === BlockTypeIds::NOTE_BLOCK ))
                        return;
                    if($player->hasPermission("worldguard.usePRESSURE_PLATE." . $reg->getName()) && (in_array($block, [BlockTypeIds::CHERRY_PRESSURE_PLATE, BlockTypeIds::ACACIA_PRESSURE_PLATE, BlockTypeIds::BIRCH_PRESSURE_PLATE, BlockTypeIds::CRIMSON_PRESSURE_PLATE, BlockTypeIds::JUNGLE_PRESSURE_PLATE, BlockTypeIds::DARK_OAK_PRESSURE_PLATE, BlockTypeIds::MANGROVE_PRESSURE_PLATE, BlockTypeIds::SPRUCE_PRESSURE_PLATE, BlockTypeIds::CRIMSON_PRESSURE_PLATE, BlockTypeIds::WARPED_PRESSURE_PLATE, BlockTypeIds::OAK_PRESSURE_PLATE])  || $block === BlockTypeIds::WEIGHTED_PRESSURE_PLATE_LIGHT || $block === BlockTypeIds::WEIGHTED_PRESSURE_PLATE_HEAVY || $block === BlockTypeIds::STONE_PRESSURE_PLATE))
                        return;
                    if($player->hasPermission("worldguard.usebutton." . $reg->getName()) && ($block === BlockTypeIds::STONE_BUTTON || (in_array($block, [BlockTypeIds::CHERRY_BUTTON, BlockTypeIds::ACACIA_BUTTON, BlockTypeIds::BIRCH_BUTTON, BlockTypeIds::CRIMSON_BUTTON, BlockTypeIds::JUNGLE_BUTTON, BlockTypeIds::DARK_OAK_BUTTON, BlockTypeIds::MANGROVE_BUTTON, BlockTypeIds::SPRUCE_BUTTON, BlockTypeIds::CRIMSON_BUTTON, BlockTypeIds::WARPED_BUTTON, BlockTypeIds::OAK_BUTTON]))))
                        return;
                    if($player->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
                        return;
                    }
                    if(in_array($block, self::$USABLES)){
                        if($reg->getFlag("deny-msg") === "true"){
                            //$player->sendMessage(TF::RED.'You cannot interact with '.$event->getBlock()->getName().'s. in this area.');
                            $player->sendMessage(TF::RED. (string)str_replace(["{BLOCK_NAME}"], [$event->getBlock()->getName()], $this->plugin->resourceManager->getMessages()["denied-block-interact"]));
                        }
                        $event->cancel();
                        return;
                    }
                }else $event->uncancel();

                if($reg->getFlag("potions") === "false"){
                    if(in_array($event->getItem()->getTypeId(), self::$POTIONS)){
                        //$player->sendMessage(TF::RED.'You cannot use '.$event->getItem()->getName().' in this area.');
                        $player->sendMessage(TF::RED. (string)str_replace(["{ITEM_NAME}"], [$event->getItem()->getName()], $this->plugin->resourceManager->getMessages()["denied-item-interact"]));
                        $event->cancel();
                        return;
                    }
                }else $event->uncancel();
                if(!$player->hasPermission("worldguard.edit." . $reg->getName())){
                    if(in_array($event->getItem()->getTypeId(), self::$OTHER)){
                        //$player->sendMessage(TF::RED.'You cannot use '.$event->getItem()->getName().'. in this area.');
                        $player->sendMessage(TF::RED. (string)str_replace(["{ITEM_NAME}"], [$event->getItem()->getName()], $this->plugin->resourceManager->getMessages()["denied-item-use"]));
                        $event->cancel();
                        return;
                    }
                }else $event->uncancel();
                return;
            }
        }
    }

    /**
     * @param ProjectileLaunchEvent $event
     * @return void
     */
    public function blockEnderpeals(ProjectileLaunchEvent $event): void{
        $tile = $event->getEntity();
        $player = $tile->getOwningEntity();
        if($player instanceof Player){
            if($tile instanceof EnderPearl){
                if(($region = $this->plugin->getRegionByPlayer($player)) !== ""){
                    if($region->getFlag("enderpearl") === "false"){
                        $event->cancel();
                        if($region->getFlag("deny-msg") === "true"){
                            $player->sendMessage(TF::RED . $this->plugin->resourceManager->getMessages()["denied-ender-pearls"]);
                        }
                    }
                }
            }elseif($tile instanceof Arrow){
                if(($region = $this->plugin->getRegionByPlayer($player)) !== ""){
                    if($region->getFlag("bow") === "false"){
                    	$event->cancel();
                        if($region->getFlag("deny-msg") === "true"){
                        	$player->sendMessage(TF::RED . $this->plugin->resourceManager->getMessages()["denied-bow"]);
			            }
                    }
                }
            }
        }
    }

    /**
     * @param BlockUpdateEvent $event
     * @return void
     */
    public function onBlockUpdate(BlockUpdateEvent $event): void{
        $block = $event->getBlock();
        $getblpos = $block->getPosition();
        $position = new Position($getblpos->getX(),$getblpos->getY(),$getblpos->getZ(),$block->getPosition()->getWorld());
        $region = $this->plugin->getRegionFromPosition($position);
        if($region !== ""){
            if($region->getFlag("pluginbypass") === "false"){
                if($block->getName() === "Lava" || $block->getName() === "Water"){
                    if($region->getFlag("flow") === "false"){
                        $event->cancel();
                    }
                }
            }
        }
    }

    /**
     * @param BlockPlaceEvent $event
     * @return void
     */
    public function onPlace(BlockPlaceEvent $event): void{
        $player = $event->getPlayer();
        $transaction = $event->getTransaction();
        foreach ($transaction->getBlocks() as [$x, $y, $z, $block]){
            $x = $block->getPosition()->getX();
            $z = $block->getPosition()->getZ();
            if($x < 0){
                $x = ($x + 1);
            }
            if($z < 0){
                $z = ($z + 1);
            }
            $position = new Position($x, $block->getPosition()->getY(), $z, $block->getPosition()->getWorld());
            if(($region = $this->plugin->getRegionFromPosition($position)) !== ""){
                if($region->getFlag("pluginbypass") === "false"){
                    if($region->getFlag("block-place") === "false"){
                        if($event->getPlayer()->hasPermission("worldguard.place." . $region->getName()) || $event->getPlayer()->hasPermission("worldguard.block-place." . $region->getName())){
                            return;
                        }else if($event->getPlayer()->hasPermission("worldguard.build-bypass")){
                            return;
                        }else if($player->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
                            return;
                        }else{
                            if($region->getFlag("deny-msg") === "true"){
                                $player->sendMessage(TF::RED . $this->plugin->resourceManager->getMessages()["denied-block-place"]);
                            }
                            $event->cancel();
                        }
                    }
                }
            }
        }
    }

    /**
     * @param BlockBreakEvent $event
     * @return void
     */
    public function onBreak(BlockBreakEvent $event): void{
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $x = $block->getPosition()->x;
        $z = $block->getPosition()->z;
        if($x < 0){
            $x = ($x + 1);
        }
        if($z < 0){
            $z = ($z + 1);
        }
        $position = new Position($x, $block->getPosition()->y, $z, $block->getPosition()->getWorld());
        if(($region = $this->plugin->getRegionFromPosition($position)) !== ""){
            if($region->getFlag("pluginbypass") === "false"){
                if($region->getFlag("block-break") === "false"){
                    if($event->getPlayer()->hasPermission("worldguard.break." . $region->getName()) || $event->getPlayer()->hasPermission("worldguard.block-break." . $region->getName()) || $event->getPlayer()->hasPermission("worldguard.break") || $event->getPlayer()->hasPermission("worldguard.block-break")){
                        return;
                    }else if($event->getPlayer()->hasPermission("worldguard.break-bypass")){
                        return;
                    }else{
                        if($region->getFlag("deny-msg") === "true"){
                            $player->sendMessage(TF::RED . $this->plugin->resourceManager->getMessages()["denied-block-break"]);
                        }
                        $event->cancel();
                    }
                }
            }
            if($region->getFlag("exp-drops") === "false"){
                $event->setXpDropAmount(0);
            }

        }
    }

    /**
     * @param PlayerDeathEvent $event
     * @return void
     */
    public function onDeathItemDrop(PlayerDeathEvent $event): void{
        if(($reg = $this->plugin->getRegionByPlayer($player = $event->getPlayer())) !== ""){
            if($reg->getFlag("item-by-death") === "false" && !$player->hasPermission("worldguard.deathdrop." . $reg->getName()) and !$player->hasPermission("worldguard.deathdrop")){
                if($reg->getFlag("deny-msg") === "true"){
                    $player->sendMessage(TF::RED . $this->plugin->resourceManager->getMessages()["denied-item-death-drop"]);
                }
                $event->setDrops([]);
                return;
            }
        }
    }

    /**
     * @param BlockBurnEvent $event
     * @return void
     */
    public function onBurn(BlockBurnEvent $event): void{
        if(($region = $this->plugin->getRegionFromPosition($event->getBlock()->getPosition())) !== ""){
            if($region->getFlag("allow-block-burn") === "false")
                $event->cancel();
        }
    }

    /**
     * @param PlayerMoveEvent $event
     * @return void
     */
    public function onMove(PlayerMoveEvent $event): void{
        if(!$event->getFrom()->equals($event->getTo())){
            if($this->plugin->updateRegion($player = $event->getPlayer()) !== true){
		        $player->setMotion($event->getFrom()->subtract($player->getPosition()->getX(), $player->getPosition()->getY(), $player->getPosition()->getZ())->normalize()->multiply($this->plugin->getKnockback()));
            }
        }
    }

    /**
     * @param EntityDamageByEntityEvent $event
     * @return void
     */
    public function onHurtByEntity(EntityDamageByEntityEvent $event): void{
        $victim = $event->getEntity();
        $damager = $event->getDamager();
        if(($victim) instanceof Player){
            if(($reg = $this->plugin->getRegionByPlayer($victim)) !== ""){
                if($reg->getFlag("pvp") === "false"){
                    if(($damager) instanceof Player){
                        if($reg->getFlag("deny-msg") === "true"){
                            $damager->sendMessage(TF::RED . $this->plugin->resourceManager->getMessages()["denied-pvp"]);
                        }
                        $event->cancel();
                        return;
                    }
                }
            }
            if(($damager) instanceof Player){
                if(($reg = $this->plugin->getRegionByPlayer($damager)) !== ""){
                    if($reg->getFlag("pvp") === "false"){
                        if(($victim) instanceof Player){
                            if($reg->getFlag("deny-msg") === "true"){
                                $damager->sendMessage(TF::RED . $this->plugin->resourceManager->getMessages()["denied-pvp"]);
                            }
                            $event->cancel();
                            return;
                        }
                    }
                }
            }
        }

        // $this->plugin->getLogger()->notice(get_class($event->getEntity()));

        if(Utils::isAnimal($event->getEntity())){
            if(($player = $event->getDamager()) instanceof Player)
                if(($region = $this->plugin->getRegionFromPosition($event->getEntity()->getPosition())) !== ""){
                    if($region->getFlag("allow-damage-animals") === "false"){
                        if($region->getFlag("deny-msg") === "true"){
                            $player->sendMessage(TF::RED . $this->plugin->resourceManager->getMessages()["denied-hurt-animal"]);
                        }
                        $event->cancel();
                        return;
                    }
                }
        }

        if(Utils::isMonster($event->getEntity())){
            if(($player = $event->getDamager()) instanceof Player)
                if(($region = $this->plugin->getRegionFromPosition($event->getEntity()->getPosition())) !== ""){
                    if($region->getFlag("allow-damage-animals") === "false"){
                        $player->sendMessage(TF::RED . $this->plugin->resourceManager->getMessages()["denied-hurt-monster"]);
                        $event->cancel();
                        return;
                    }
                }
        }

        if(strpos(get_class($event->getEntity()), "monster") !== false){
            if(($player = $event->getDamager()) instanceof Player)
                if(($region = $this->plugin->getRegionFromPosition($event->getEntity()->getPosition())) !== ""){
                    if($region->getFlag("allow-damage-monsters") === "false"){
                        $player->sendMessage(TF::RED . 'You cannot hurt monsters of this region.');
                        $event->cancel();
                        return;
                    }
                }
        }
    }

    /**
     * @param EntityDamageEvent $event
     * @return void
     */
    public function onHurt(EntityDamageEvent $event): void{
        if(($this->plugin->getRegionFromPosition($event->getEntity()->getPosition())) !== ""){
            if($this->plugin->getRegionFromPosition($event->getEntity()->getPosition())->getFlag("invincible") === "true"){
                if($event->getEntity() instanceof Player){
                    $event->cancel();
                }
            }
        }
        return;
    }

    /**
     * @param EntityDamageEvent $event
     * @return void
     */
    public function onFallDamage(EntityDamageEvent $event): void{
        if(($this->plugin->getRegionFromPosition($event->getEntity()->getPosition())) !== ""){
            $cause = $event->getCause();
            if($this->plugin->getRegionFromPosition($event->getEntity()->getPosition())->getFlag("fall-dmg") === "false"){
                if($cause == EntityDamageEvent::CAUSE_FALL){
                    $event->cancel();
                }
            }
        }
        return;
    }

    /**
     * @param CommandEvent $event
     * @return void
     */
    public function onCommand(CommandEvent $event){
        $player = $event->getSender();
        if(!$player instanceof Player){
            return;
        }
        
        if($this->plugin->getRegionByPlayer($player) !== "")
            if(str_starts_with(strtolower($event->getCommand()), '/f claim')){
                $player->sendMessage(TF::RED . 'You cannot claim plots in this area.');
                $event->cancel();
            }


        $cmd = explode(" ", $event->getCommand())[0];
        if(substr($cmd, 0, 1) === '/'){
            if(($region = $this->plugin->getRegionByPlayer($player = $player)) !== "" && !$region->isCommandAllowed($cmd)){
                if(!$player->hasPermission("worldguard.bypass-cmd." . $region->getName()) and !$player->hasPermission("worldguard.bypass-cmd")){
                    $player->sendMessage(TF::RED . 'You cannot use ' . $cmd . ' in this area.');
                    $event->cancel();
                }
            }
        }
    }

    /**
     * @param PlayerDropItemEvent $event
     * @return void
     */
    public function onDrop(PlayerDropItemEvent $event): void{
        if(($reg = $this->plugin->getRegionByPlayer($player = $event->getPlayer())) !== ""){
            if($reg->getFlag("item-drop") === "false" && !$player->hasPermission("worldguard.drop." . $reg->getName()) && !$player->hasPermission("worldguard.drop")){
                if($reg->getFlag("deny-msg") === "true"){
                    $player->sendMessage(TF::RED . $this->plugin->resourceManager->getMessages()["denied-item-drop"]);
                }
                $event->cancel();
                return;
            }
        }
    }

    /**
     * @param EntityExplodeEvent $event
     * @return void
     */
    public function onExplode(EntityExplodeEvent $event): void{
        foreach ($event->getBlockList() as $block){
            if(($region = $this->plugin->getRegionFromPosition($block->getPosition())) !== ""){
                if($region->getFlag("explosion") === "false"){
                    $event->cancel();
                    return;
                }
            }
        }
    }

    /**
     * @param PlayerBedEnterEvent $event
     * @return void
     */
    public function onSleep(PlayerBedEnterEvent $event): void{
        if(($region = $this->plugin->getRegionFromPosition($event->getBed()->getPosition())) !== ""){
            if($region->getFlag("sleep") === "false"){
                $event->cancel();
            }
        }
    }

    /**
     * @param PlayerChatEvent $event
     * @return void
     */
    public function onChat(PlayerChatEvent $event): void{
        if(($reg = $this->plugin->getRegionByPlayer($player = $event->getPlayer())) !== ""){
            if($reg->getFlag("send-chat") === "false"){
                if($reg->getFlag("deny-msg") === "true"){
                    $player->sendMessage(TF::RED . $this->plugin->resourceManager->getMessages()["denied-chat"]);
                }
                $event->cancel();
                return;
            }
        }
        if(!empty($this->plugin->muted)){
            $diff = array_diff($this->plugin->getServer()->getOnlinePlayers(), $this->plugin->muted);
            if(!in_array($player, $diff)){
                $diff[] = $player;
            }
            $event->setRecipients($diff);
        }
    }

    /**
     * @param PlayerItemConsumeEvent $event
     * @return void
     */
    public function onItemConsume(PlayerItemConsumeEvent $event): void{
        $player = $event->getPlayer();
        if($player instanceof Player){
            if(($region = $this->plugin->getRegionByPlayer($event->getPlayer())) !== ""){
                if($region->getFlag("eat") === "false" && !$player->hasPermission("worldguard.eat." . $region->getName()) && !$player->hasPermission("worldguard.eat")){
                    $event->cancel();
                    if($region->getFlag("deny-msg") === "true"){
                        $player->sendMessage(TF::RED . $this->plugin->resourceManager->getMessages()["denied-eat"]);
                    }
                }
            }
        }
    }

    /**
     * @param PlayerExhaustEvent $exhaustEvent
     * @return void
     */
    public function noHunger(PlayerExhaustEvent $exhaustEvent): void{
        if($exhaustEvent->getPlayer() instanceof Player){
            if(($region = $this->plugin->getRegionByPlayer($exhaustEvent->getPlayer())) !== ""){
                if($region->getFlag("hunger") === "false"){
                    $exhaustEvent->cancel();
                }
            }
        }
    }

    /**
     * @param LeavesDecayEvent $event
     * @return void
     */
    public function onLeafDecay(LeavesDecayEvent $event): void{
        if(($region = $this->plugin->getRegionFromPosition($event->getBlock()->getPosition())) !== "")
            if($region->getFlag("allow-leaves-decay") === "false")
                $event->cancel();
    }

    /**
     * @param BlockGrowEvent $event
     * @return void
     */
    public function onPlantGrowth(BlockGrowEvent $event): void{
        if(($region = $this->plugin->getRegionFromPosition($event->getBlock()->getPosition())) !== "")
            if($region->getFlag("allow-plant-growth") === "false")
                $event->cancel();
    }

    /**
     * @param BlockSpreadEvent $event
     * @return void
     */
    public function onBlockSpread(BlockSpreadEvent $event): void{
        if(($region = $this->plugin->getRegionFromPosition($event->getBlock()->getPosition())) !== "")
            if($region->getFlag("allow-spreading") === "false")
                $event->cancel();
    }
}
