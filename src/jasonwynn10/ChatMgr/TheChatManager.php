<?php
namespace jasonwynn10\ChatMgr;

use jasonwynn10\PermMgr\event\GroupChangeEvent;
use jasonwynn10\PermMgr\event\PermissionAttachEvent;
use jasonwynn10\PermMgr\ThePermissionManager;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\lang\BaseLang;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class TheChatManager extends PluginBase implements Listener {
	/** @var ThePermissionManager $permManager */
	private $permManager;

	/** @var BaseLang $baseLang */
	private $baseLang;

	/** @var Config $players */
	private $players;

	public function onLoad() {
		$lang = $this->getConfig()->get("lang", BaseLang::FALLBACK_LANGUAGE);
		$this->baseLang = new BaseLang($lang,$this->getFile() . "resources/");
	}

	public function onEnable() {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->permManager = $this->getServer()->getPluginManager()->getPlugin("ThePermissionManager");
		$this->saveResource("players.yml");
		$this->players = new Config($this->getDataFolder()."players.yml", Config::YAML, []);
	}

	public function getLanguage() : BaseLang {
		return $this->baseLang;
	}

	public function getPermissionManager() : ThePermissionManager {
		return $this->permManager;
	}

	/**
	 * @priority MONITOR
	 * @ignoreCancelled false
	 *
	 * @param GroupChangeEvent $ev
	 */
	public function onGroupChanged(GroupChangeEvent $ev) {
		if($ev->isCancelled())
			return;
		$player = $ev->getPlayer();
		$levelName = $this->getConfig()->get("enable-multiworld-chat") ? $player->getLevel()->getName() : null;
		$player->setNameTag($this->getPlayerNametag($player, $levelName));
	}

	/**
	 * @priority MONITOR
	 * @ignoreCancelled false
	 *
	 * @param PlayerChatEvent $ev
	 */
	public function onPlayerChat(PlayerChatEvent $ev) {
		if($ev->isCancelled())
			return;
		$player = $ev->getPlayer();
		$levelName = $this->getConfig()->get("enable-multiworld-chat") ? $player->getLevel()->getName() : null;
		$ev->setFormat($this->getChatFormat($player, $ev->getMessage(), $levelName));
	}

	/**
	 * @priority MONITOR
	 *
	 * @param PermissionAttachEvent $ev
	 */
	public function onAttach(PermissionAttachEvent $ev) {
		$player = $ev->getPlayer();
		$levelName = $this->getConfig()->get("enable-multiworld-chat", false) ? $player->getLevel()->getName() : null;
		$player->setNameTag($this->getPlayerNametag($player, $levelName));
	}

	#API

	# Colors

	/**
	 * @param string $string
	 *
	 * @return string
	 */
	public function convertColors(string $string) : string {
		$string = str_replace("&0", TextFormat::BLACK, $string);
		$string = str_replace("&1", TextFormat::DARK_BLUE, $string);
		$string = str_replace("&2", TextFormat::DARK_GREEN, $string);
		$string = str_replace("&3", TextFormat::DARK_AQUA, $string);
		$string = str_replace("&4", TextFormat::DARK_RED, $string);
		$string = str_replace("&5", TextFormat::DARK_PURPLE, $string);
		$string = str_replace("&6", TextFormat::GOLD, $string);
		$string = str_replace("&7", TextFormat::GRAY, $string);
		$string = str_replace("&8", TextFormat::DARK_GRAY, $string);
		$string = str_replace("&9", TextFormat::BLUE, $string);
		$string = str_replace("&a", TextFormat::GREEN, $string);
		$string = str_replace("&b", TextFormat::AQUA, $string);
		$string = str_replace("&c", TextFormat::RED, $string);
		$string = str_replace("&d", TextFormat::LIGHT_PURPLE, $string);
		$string = str_replace("&e", TextFormat::YELLOW, $string);
		$string = str_replace("&f", TextFormat::WHITE, $string);
		$string = str_replace("&k", TextFormat::OBFUSCATED, $string);
		$string = str_replace("&l", TextFormat::BOLD, $string);
		$string = str_replace("&m", TextFormat::STRIKETHROUGH, $string);
		$string = str_replace("&n", TextFormat::UNDERLINE, $string);
		$string = str_replace("&o", TextFormat::ITALIC, $string);
		$string = str_replace("&r", TextFormat::RESET, $string);
		return $string;
	}

	/**
	 * @param string $string
	 *
	 * @return string
	 */
	public function removeColors(string $string) : string {
		$string = str_replace(TextFormat::BLACK, '', $string);
		$string = str_replace(TextFormat::DARK_BLUE, '', $string);
		$string = str_replace(TextFormat::DARK_GREEN, '', $string);
		$string = str_replace(TextFormat::DARK_AQUA, '', $string);
		$string = str_replace(TextFormat::DARK_RED, '', $string);
		$string = str_replace(TextFormat::DARK_PURPLE, '', $string);
		$string = str_replace(TextFormat::GOLD, '', $string);
		$string = str_replace(TextFormat::GRAY, '', $string);
		$string = str_replace(TextFormat::DARK_GRAY, '', $string);
		$string = str_replace(TextFormat::BLUE, '', $string);
		$string = str_replace(TextFormat::GREEN, '', $string);
		$string = str_replace(TextFormat::AQUA, '', $string);
		$string = str_replace(TextFormat::RED, '', $string);
		$string = str_replace(TextFormat::LIGHT_PURPLE, '', $string);
		$string = str_replace(TextFormat::YELLOW, '', $string);
		$string = str_replace(TextFormat::WHITE, '', $string);
		$string = str_replace(TextFormat::OBFUSCATED, '', $string);
		$string = str_replace(TextFormat::BOLD, '', $string);
		$string = str_replace(TextFormat::STRIKETHROUGH, '', $string);
		$string = str_replace(TextFormat::UNDERLINE, '', $string);
		$string = str_replace(TextFormat::ITALIC, '', $string);
		$string = str_replace(TextFormat::RESET, '', $string);
		return $string;
	}

	# Prefix

	/**
	 * @param Player $player
	 * @param string $levelName
	 *
	 * @return string
	 */
	public function getPrefix(Player $player, string $levelName = null) : string {
		if(empty($levelName)) {
			return $this->players->getNested("{$player->getName()}.prefix", "");
		}else{
			return $this->players->getNested("{$player->getName()}.worlds.{$levelName}.prefix", "");
		}
	}

	/**
	 * @param string $prefix
	 * @param Player $player
	 * @param string|null $levelName
	 *
	 * @return bool
	 */
	public function setPrefix(string $prefix, Player $player, string $levelName = null) : bool {
		if(empty($levelName)) {
			$this->players->setNested("{$player->getName()}.prefix", $prefix);
		}else{
			$this->players->setNested("{$player->getName()}.worlds.{$levelName}.prefix", $prefix);
		}
		return $this->players->save();
	}

	# Suffix

	/**
	 * @param Player $player
	 * @param string|null $levelName
	 *
	 * @return string
	 */
	public function getSuffix(Player $player, string $levelName = null) : string {
		if(empty($levelName)) {
			return $this->players->getNested("{$player->getName()}.suffix", "");
		}else{
			return $this->players->getNested("{$player->getName()}.worlds.{$levelName}.suffix", "");
		}
	}

	/**
	 * @param string $suffix
	 * @param Player $player
	 * @param string|null $levelName
	 *
	 * @return bool
	 */
	public function setSuffix(string $suffix, Player $player, string $levelName = null) : bool {
		if(empty($levelName)) {
			$this->players->setNested("{$player->getName()}.suffix", $suffix);
		}else{
			$this->players->setNested("{$player->getName()}.worlds.{$levelName}.suffix", $suffix);
		}
		return $this->players->save();
	}

	# Chat

	/**
	 * @param Player $player
	 * @param string $message
	 * @param string|null $levelName
	 *
	 * @return string
	 */
	public function getChatFormat(Player $player, $message, string $levelName = null) : string {
		return $this->applyTags($this->convertColors($this->getOriginalChatFormat($player, $levelName)), $player, $message, $levelName);
	}

	/**
	 * @param Player $player
	 * @param string|null $levelName
	 *
	 * @return string
	 */
	public function getOriginalChatFormat(Player $player, string $levelName = null) : string {
		$group = $this->permManager->getPlayerProvider()->getGroup($player);
		if(empty($levelName)) {
			return $this->getConfig()->getNested("groups.{$group}.chat", "&8&l[{$group}]&f&r {display_name}");
		}else{
			return $this->getConfig()->getNested("groups.{$group}.worlds.$levelName.chat","&8&l[{$group}]&f&r {display_name}");
		}
	}

	/**
	 * @param string $group
	 * @param string $format
	 * @param string $levelName
	 *
	 * @return bool
	 */
	public function setOriginalChatFormat(string $group, string $format, string $levelName = "") : bool {
		if(empty($levelName)){
			$this->getConfig()->setNested("groups.{$group}.nametag", $format);
		}else{
			$this->getConfig()->setNested("groups.{$group}.worlds.$levelName.nametag", $format);
		}
		return $this->getConfig()->save();
	}

	# Nametag

	/**
	 * @param Player $player
	 * @param string|null $levelName
	 *
	 * @return string
	 */
	public function getPlayerNametag(Player $player, string $levelName = null) : string {
		$originalNametag = $this->getOriginalNametag($player, $levelName);
		if($player->hasPermission("ChatManager.coloredTag")) {
			$nameTag = $this->convertColors($originalNametag);
		}else{
			$nameTag = $this->removeColors($originalNametag);
		}
		$nameTag = $this->applyTags($nameTag, $player, "", $levelName);
		return $nameTag;
	}

	/**
	 * @param Player $player
	 * @param string|null $levelName
	 *
	 * @return string
	 */
	public function getOriginalNametag(Player $player, string $levelName = null) : string {
		$group = $this->permManager->getPlayerProvider()->getGroup($player);
		if(empty($levelName)) {
			return $this->getConfig()->getNested("groups.{$group}.nametag", "&8&l[{$group}]&f&r {display_name}");
		}else{
			return $this->getConfig()->getNested("groups.{$group}.worlds.$levelName.nametag","&8&l[{$group}]&f&r {display_name}");
		}
	}

	/**
	 * @param string $group
	 * @param string $nameTag
	 * @param string $levelName
	 *
	 * @return bool
	 */
	public function setOriginalNametag(string $group, string $nameTag, string $levelName = "") : bool {
		if(empty($levelName)){
			$this->getConfig()->setNested("groups.{$group}.nametag", $nameTag);
		}else{
			$this->getConfig()->setNested("groups.{$group}.worlds.$levelName.nametag",$nameTag);
		}
		return $this->getConfig()->save();
	}

	# General

	/**
	 * @param string $string
	 * @param Player $player
	 * @param string $message
	 * @param string|null $levelName
	 *
	 * @return string
	 */
	public function applyTags(string $string, Player $player, string $message = "", string $levelName = null) : string { // TODO add more tags
		$string = str_replace("{display_name}", $player->getDisplayName(), $string);
		$string = str_replace("{user_name}", $player->getName(), $string);
		if($player->hasPermission("ChatManager.coloredMessages")) {
			$string = str_replace("{msg}", $this->convertColors($message), $string);
		}else{
			$string = str_replace("{msg}", $this->removeColors($message), $string);
		}
		$string = str_replace("{world}", ($levelName === null ? "" : $levelName), $string);
		$string = str_replace("{prefix}", $this->getPrefix($player, $levelName), $string);
		$string = str_replace("{suffix}", $this->getSuffix($player, $levelName), $string);
		return $string;
	}
}