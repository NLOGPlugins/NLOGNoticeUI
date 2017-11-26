<?php

namespace nlog\NLOGNoticeUI;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;
use pocketmine\utils\Config;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;

class Main extends PluginBase implements Listener {
	
	public $player;
	
	/** @var Settings */
	public $setting;
	
	public $time;
	
	const FORM_ID = 1412;
	
	const TAG = "§b§o[ NoticeUI ] §7";
	
	public function onEnable() {
		
		@mkdir($this->getDataFolder());
		$this->setting = new Settings($this->getDataFolder() . "setting.yml");
		
		$this->player = new Config($this->getDataFolder() . "players.yml", Config::YAML);
		
		$this->time = new Config($this->getDataFolder() . "time.yml", Config::YAML);
		if (!$this->time->get("time") || $this->time->get("time") !== $this->getDay()) {
			$this->time->set("time", $this->getDay());
			$this->time->save();
			
			$this->player->setAll([]);
			$this->player->save();
		}
		
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->info("NoticeUI 플러그인 활성화");
	}
	
	public function onDataRecieve(DataPacketReceiveEvent $ev) {
		$pl = $ev->getPlayer()->getName();
		$pk = $ev->getPacket();
		if ($pk instanceof ModalFormResponsePacket && $pk->formId === self::FORM_ID) {
			$data = json_decode($pk->formData, true);
			if ($data) {
				$this->player->set($ev->getPlayer()->getName(), true);
				$this->player->save();
			}
		}
	}
	
	public function getDay() {
		return date("Y-m-d");
	}
	
	public function onPlayerJoin (PlayerJoinEvent $ev) {
		$pl = $ev->getPlayer();
		if ($this->player->get($name) === false) {
			sleep(5);
			$json = [];
			$json["type"] = "modal";
			$json["title"] = $this->setting->getTitle($pl);
			$json["content"] = $this->setting->getMessage($pl);
			$json["button1"] = ">> 하루 동안 보지 않기 <<"; //true
			$json["button2"] = ">> 닫기 <<"; //false
			
			$pk = new ModalFormRequestPacket();
			$pk->formId = self::FORM_ID;
			$pk->formData = json_encode($json);
			
			$ev->getPlayer()->dataPacket($pk);
		}
	}
	
	
} //클래스 괄호

?>
