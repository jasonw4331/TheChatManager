<?php
namespace jasonwynn10\ChatMgr\commands;

use jasonwynn10\ChatMgr\TheChatManager;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;

class SetSuffix extends PluginCommand {
	/**
	 * SetSuffix constructor.
	 *
	 * @param TheChatManager $plugin
	 */
	public function __construct(TheChatManager $plugin) {
		parent::__construct($plugin->getLanguage()->get("setsuffix.name"), $plugin);
		$this->setPermission("ChatManager.command.setSuffix");
		$this->setUsage($plugin->getLanguage()->get("setsuffix.usage"));
		$this->setDescription($plugin->getLanguage()->get("setsuffix.desc"));
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
		if(!$sender instanceof Player) {
			return true;
		}
		$levelName = $this->getPlugin()->getConfig()->get("enable-multiworld-chat") ? $sender->getLevel()->getName() : null;
		$prefix = str_replace("{BLANK}", ' ', implode('', $args));
		$this->getPlugin()->setSuffix($prefix, $sender, $levelName);
		$sender->sendMessage($this->getPlugin()->getLanguage()->translateString("setsuffix.success", [$prefix]));
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
		$commandData["overloads"]["default"]["input"]["parameters"] = [
			[
				"name" => "suffix",
				"type" => "rawtext",
				"optional" => false
			]
		];
		$commandData["permission"] = $this->getPermission();
		return $commandData;
	}
}