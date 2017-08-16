<?php
namespace jasonwynn10\ChatMgr\commands;

use jasonwynn10\ChatMgr\TheChatManager;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;

class SetNametag extends PluginCommand {
	public function __construct(TheChatManager $plugin) {
		parent::__construct($plugin->getLanguage()->get("setnametag.name"), $plugin);
		$this->setPermission("ChatManager.command.setNametag");
		$this->setAliases([$this->getPlugin()->getLanguage()->get("setnametag.alias")]);
		$this->setUsage($plugin->getLanguage()->get("setnametag.usage"));
		$this->setDescription($plugin->getLanguage()->get("setnametag.desc"));
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
		$sender->sendMessage($this->getPlugin()->getLanguage()->translateString("setnametag.success"));
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
		$groups = $this->getPlugin()->getPermissionManager()->getGroups()->getGroupsConfig()->getAll(true);
		$worlds = [];
		foreach($this->getPlugin()->getServer()->getLevels() as $level) {
			$worlds[] = $level->getName();
		}
		sort($worlds, SORT_FLAG_CASE);
		$commandData["overloads"]["default"]["input"]["parameters"] = [
			[
				"name" => "group",
				"type" => "stringenum",
				"optional" => false,
				"enum_values" => $groups
			],
			[
				"name" => "worlds",
				"type" => "stringenum",
				"optional" => false,
				"enum_values" => $worlds
			],
			[
				"name" => "format",
				"type" => "rawtext",
				"optional" => false
			]
		];
		$commandData["permission"] = $this->getPermission();
		return $commandData;
	}
}