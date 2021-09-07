<?php
declare(strict_types=1);

namespace Bank\command;

use Bank\Bank;
use Bank\ui\BankUI;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\PluginOwnedTrait;

class BankCommand extends Command implements PluginOwned
{
	use PluginOwnedTrait;

	private Bank $bank;

	public function __construct(Bank $bank, string $name, string $description = "", ?string $usageMessage = null, array $aliases = [])
	{
		parent::__construct($name, $description, $usageMessage, $aliases);
		$this->bank = $bank;
		$this->setDescription("Bank manager");
		$this->setPermission("bank.command");
	}

	private function getBank(): Bank
	{
		return $this->bank;
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		if (!$sender instanceof Player)
		{
			$sender->sendMessage("Please use this command ingame !");
			return;
		}
		new BankUI($sender, $this->getBank());
	}
}