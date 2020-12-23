<?php

/*
 *  ____                           _   _  ___
 * |  _ \ _ __ ___  ___  ___ _ __ | |_| |/ (_)_ __ ___
 * | |_) | '__/ _ \/ __|/ _ \ '_ \| __| ' /| | '_ ` _ \
 * |  __/| | |  __/\__ \  __/ | | | |_| . \| | | | | | |
 * |_|   |_|  \___||___/\___|_| |_|\__|_|\_\_|_| |_| |_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the MIT License. see <https://opensource.org/licenses/MIT>.
 *
 *
 * @author      PresentKim (debe3721@gmail.com)
 * @link        https://github.com/PresentKim
 * @license     https://opensource.org/licenses/MIT MIT License
 *
 *   (\ /)
 *  ( . .) ♥
 *  c(")(")
 */

declare(strict_types=1);

namespace kim\present\vanillapickblock;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerBlockPickEvent;
use pocketmine\plugin\PluginBase;

class VanillaPickBlock extends PluginBase implements Listener{
    public function onEnable() : void{
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    /** @priority LOWEST */
    public function onPlayerPickBlock(PlayerBlockPickEvent $event) : void{
        $event->setCancelled();

        $player = $event->getPlayer();
        $inventory = $player->getInventory();
        $resultItem = $event->getResultItem();
        $hatbarSize = $inventory->getHotbarSize();

        $originSlot = -1;
        foreach($inventory->getContents() as $i => $item){ //Find origin slot (with exact-check, and without count-cehck)
            if($resultItem->equals($item,true,true)){
                $resultItem = $item;
                $originSlot = $i;
                break;
            }
        }
        if($originSlot >= 0 && $originSlot < $hatbarSize){ //If origin item in hotbar, set held slot to orgin slot
            $inventory->setHeldItemIndex($originSlot);
            return;
        }

        $targetItem = $inventory->getItemInHand();
        $targetSlot = $inventory->getHeldItemIndex();
        if(!$targetItem->isNull()){
            for($i = 0; $i < $hatbarSize; ++$i){ //Find empty hotbar slot
                $item = $inventory->getItem($i);
                if($item->isNull()){
                    $targetItem = $item;
                    $targetSlot = $i;
                    $inventory->setHeldItemIndex($targetSlot);
                    break;
                }
            }
        }

        if($originSlot !== -1){ //If found origin item, swap target slot with origin slot
            $inventory->setItem($targetSlot, $resultItem);
            $inventory->setItem($originSlot, $targetItem);
        }elseif($player->isCreative()){ //If not found origin item and player is creative mode, give item.
            $inventory->setItem($targetSlot, $resultItem);
            if(!$targetItem->isNull()){ //If target item is not null, return target item into inventory.
                $inventory->addItem($targetItem);
            }
        }
    }
}
