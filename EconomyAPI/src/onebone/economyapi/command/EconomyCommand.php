<?php

/*
 * EconomyS, the massive economy plugin with many features for PocketMine-MP
 * Copyright (C) 2013-2020  onebone <me@onebone.me>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace onebone\economyapi\command;

use onebone\economyapi\EconomyAPI;
use onebone\economyapi\form\CurrencySelectionForm;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class EconomyCommand extends PluginCommand {
	public function __construct(EconomyAPI $plugin) {
		$desc = $plugin->getCommandMessage("economy");
		parent::__construct("economy", $plugin);
		$this->setDescription($desc["description"]);
		$this->setUsage($desc["usage"]);

		$this->setPermission("economyapi.command.economy");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) {
		if(!$this->testPermission($sender)) {
			return false;
		}

		/** @var EconomyAPI $plugin */
		$plugin = $this->getPlugin();

		$mode = strtolower(array_shift($args));
		$val = array_shift($args);

		switch($mode) {
			case 'lang':
			case 'language':
				if(trim($val) === "") {
					$sender->sendMessage(TextFormat::RED . "Usage: " . $this->getUsage());
					return true;
				}

				if($plugin->setPlayerLanguage($sender->getName(), $val)) {
					$sender->sendMessage($plugin->getMessage("language-set", [$val], $sender->getName()));
				}else{
					$sender->sendMessage(TextFormat::RED . "There is no language such as $val");
				}
				return true;
			case 'currency':
				if(!$sender instanceof Player) {
					$sender->sendMessage(TextFormat::RED . 'Please run this command in-game.');
					return true;
				}

				/** @var EconomyAPI $plugin */
				$plugin = $this->getPlugin();

				if(trim($val) === '') {
					$sender->sendForm(new CurrencySelectionForm($plugin, $plugin->getCurrencies(), $sender));
					return true;
				}
		}

		$sender->sendMessage(TextFormat::RED . "Usage: " . $this->getUsage());
		return false;
	}
}
