<?php
namespace jasonwynn10\ChatMgr\commands;

use jasonwynn10\ChatMgr\TheChatManager;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;

class SetFormat extends PluginCommand {
	public function __construct(TheChatManager $plugin) {
		parent::__construct($plugin->getLanguage()->get("setformat.name"), $plugin);
		$this->setPermission("ChatManager.command.setFormat");
		$this->setUsage($plugin->getLanguage()->get("setformat.usage"));
		$this->setDescription($plugin->getLanguage()->get("setformat.desc"));
		$this->setPermissionMessage($plugin->getLanguage()->get("nopermission"));
	}

	/**
	 * @param CommandSender $sender
	 * @param string $commandLabel
	 * @param string[] $args
	 *
	 * @return bool
	 */
	public function execute(CommandSender $sender, string $commandLabel, array $args) {
		if(!$this->testPermission($sender)) {
			return true;
		}
		//TODO
		return true;
	}

	/**
	 * @return TheChatManager
	 */
	public function getPlugin() : Plugin {
		return parent::getPlugin();
	}

	/**
	 * @param Player $player
	 *
	 * @return array
	 */
	public function generateCustomCommandData(Player $player) : array {
		$commandData = parent::generateCustomCommandData($player);
		$players = [];
		foreach($this->getPlugin()->getServer()->getOnlinePlayers() as $player) {
			$players[] = $player->getName();
		}
		sort($players, SORT_FLAG_CASE);
		$worlds = [];
		foreach($this->getPlugin()->getServer()->getLevels() as $level) {
			if(!$level->isClosed()) {
				$worlds[] = $level->getName();
			}
		}
		sort($worlds, SORT_FLAG_CASE);
		$commandData["overloads"]["default"]["input"]["parameters"] = []; //TODO client command syntax
		$commandData["permission"] = $this->getPermission();
		return $commandData;
	}
}