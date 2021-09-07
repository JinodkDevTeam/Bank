<?php
declare(strict_types=1);

namespace Bank\event;

use Bank\Bank;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\plugin\PluginEvent;
use pocketmine\player\Player;
use pocketmine\Server;

class BankBalanceUpdateEvent extends PluginEvent implements Cancellable
{
	use CancellableTrait;

	public const TYPE_DEPOSIT = 0;
	public const TYPE_WITHDRAW = 1;

	protected Player|string $player;
	protected float $amount;
	protected int $type;

	public function __construct(Player|string $player, float $amount, int $type)
	{
		parent::__construct($this->getBank());
		$this->player = $player;
		$this->amount = $amount;
		$this->type = $type;
	}

	private function getBank(): ?Bank
	{
		$bank = Server::getInstance()->getPluginManager()->getPlugin("Bank");
		if ($bank instanceof Bank) return $bank;
		return null;
	}

	public function getPlayer(): Player|string
	{
		return $this->player;
	}

	public function getType(): int
	{
		return $this->type;
	}

	public function getAmount(): float
	{
		return $this->amount;
	}

	public function setAmount(float $amount): void
	{
		$this->amount = $amount;
	}

}