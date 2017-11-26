<?php

namespace nlog\NLOGNoticeUI;

use pocketmine\utils\Config;
use pocketmine\Server;
use pocketmine\Player;
use onebone\economyapi\EconomyAPI;

class Settings {
	
	/** @var Config */
	protected $config;
	
	/** @var Server */
	protected $server;
	
	/** @var array */
	protected $availableParameter;
	
	public function __construct(string $path) {
		$this->config = new Config($path, Config::YAML);
		$this->server = Server::getInstance();
		$this->availableParameter = [
				"@playername", "@playercount", "@playermaxcount", "@motd", "@mymoney", "@health", "@maxhealth", "@year", "@month", "@day", "@hour"
		];
		
		$this->init();
	}
	
	protected function init() {
		if (!$this->config->get("title") || is_array($this->config->get("title"))) {
			$this->config->set("title", "서버에 오신 것을 환영합니다.");
			$this->save();
		}
		if (!$this->config->get("message") || is_array($this->config->get("message"))) {
			$this->config->set("message", "안녕하세요 @playername님\n서버에 오신 것을 환영합니다!");
			$this->save();
		}
	}
	
	public final function getConfig(): Config {
		return $this->config;
	}
	
	public final function save(bool $async = false) {
		$this->config->save($async);
	}
	
	public function getMessage(Player $player, $economy = null) {
		$economy instanceof EconomyAPI ? $economy : EconomyAPI::getInstance();
		$msg = $this->config->get("message");
		$msg = str_replace($this->availableParameter, [
				$player->getName(), 
				count($this->server->getOnlinePlayers()), 
				$this->server->getMaxPlayers(),
				$this->server->getNetwork()->getName(),
				$economy->myMoney($player),
				$player->getHealth(),
				$player->getMaxHealth(),
				date("Y"),
				date("m"),
				date("d"),
				date("g")
		], $msg);
		
		$msg = str_replace('\n', "\n", $msg);
		
		return $msg;
	}
	
	public function getTitle(Player $player, $economy = null) {
		if (!$economy instanceof EconomyAPI) {
 +			$economy = EconomyAPI::getInstance();
		}
		$msg = $this->config->get("title");
		$msg = str_replace($this->availableParameter, [
				$player->getName(), 
				count($this->server->getOnlinePlayers()), 
				$this->server->getMaxPlayers(),
				$this->server->getNetwork()->getName(),
				$economy->myMoney($player),
				$player->getHealth(),
				$player->getMaxHealth(),
				date("Y"),
				date("m"),
				date("d"),
				date("g")
		], $msg);
		
		$msg = str_replace('\n', "\n", $msg);
		
		return $msg;
	}
}
