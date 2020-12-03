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

        $inventory = $event->getPlayer()->getInventory();
        $hatbarSize = $inventory->getHotbarSize();
        $item = $event->getResultItem();
        $emptyHatbar = null;
        for($i = 0; $i < $hatbarSize; ++$i){
            $hatbar = $inventory->getItem($i);
            if($hatbar->equals($item, true, true)){
                $inventory->setHeldItemIndex($i);
                return;
            }elseif($emptyHatbar === null && $hatbar->isNull()){
                $emptyHatbar = $i;
            }
        }
        if($emptyHatbar !== null){
            $inventory->setItem($emptyHatbar, $item);
            $inventory->setHeldItemIndex($emptyHatbar);
        }else{
            $inventory->setItemInHand($item);
        }
    }
}
