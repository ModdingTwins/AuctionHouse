<?php

namespace AuctionHouse;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;
use PrestigeSociety\AuctionHouse\Task\OpenPurchasesMessageForm;

class EventListener implements Listener{

        /** @var AuctionHouse */
        protected $loader;

        /**
         *
         * EventListener constructor.
         *
         * @param AuctionHouse $loader
         *
         */
        public function __construct(AuctionHouse $loader){
                $this->loader = $loader;
        }

        /**
         *
         * @param DataPacketReceiveEvent $event
         *
         */
        public function onDataPacketReceiveEvent(DataPacketReceiveEvent $event) {
                $pk = $event->getPacket();
                $player = $event->getPlayer();
                if($pk instanceof ModalFormResponsePacket) {
                        $data = json_decode($pk->formData, true);
                        if($data !== null){
                                $this->loader->handleFormResponse($player, $data, $pk->formId);
                        }else{
                                $this->loader->resetCache($player);
                        }
                }
        }

        /**
         *
         * @param PlayerJoinEvent $event
         *
         */
        public function onJoin(PlayerJoinEvent $event){
                $player = $event->getPlayer();
                $cache = $this->loader->getCache($player->getName());
                if(isset($cache['purchasesMessage'])){
                        $this->loader->getScheduler()->scheduleDelayedTask(new OpenPurchasesMessageForm($this->loader, $cache['purchasesMessage'], $player), 20);
                        $this->loader->resetCache($player);
                }
        }
}