<?php
declare(strict_types=1);

namespace Bank;

use Bank\command\BankCommand;
use Bank\provider\SqliteProvider;
use Bank\task\AutoInterestTask;
use pocketmine\plugin\PluginBase;

class Bank extends PluginBase
{
	private SqliteProvider $provider;

	public function onEnable() : void
	{
		$this->provider = new SqliteProvider($this);
		$this->getProvider()->init();

		$this->getServer()->getCommandMap()->register("bank", new BankCommand($this, "bank"));
		$repeat = (int) $this->getConfig()->get("setting")["repeat"];
		$this->getScheduler()->scheduleDelayedRepeatingTask(new AutoInterestTask($this), $repeat, $repeat);
	}

	public function onDisable() : void
	{
		$this->getProvider()->close();
	}

	public function getProvider(): SqliteProvider
	{
		return $this->provider;
	}
}