<?php
declare(strict_types=1);

namespace Bank\ui;

use Bank\Bank;
use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\SimpleForm;
use onebone\economyapi\EconomyAPI;
use pocketmine\player\Player;

class WithdrawUI extends BaseUI
{
	private float $balance;

	public function __construct(Player $player, Bank $bank, float $balance)
	{
		$this->balance = $balance;
		parent::__construct($player, $bank);
	}

	public function execute(Player $player) : void
	{
		$all = $this->balance;
		$half = round($this->balance / 2, 2, PHP_ROUND_HALF_DOWN);
		$min = round($this->balance / 5, 2, PHP_ROUND_HALF_DOWN);

		$form = new SimpleForm(function(Player $player, ?int $data) use ($all, $half, $min)
		{
			if ($data == null) return;

			switch ($data)
			{
				case 0:
					new BankUI($player, $this->getBank());
					break;
				case 1:
					$this->withdraw($player, $all);
					break;
				case 2:
					$this->withdraw($player, $half);
					break;
				case 3:
					$this->withdraw($player, $min);
					break;
				case 4:
					$this->specificAmount($player);
					break;
			}
		});
		$form->setTitle("Bank withdraw");
		$form->addButton("Back");
		$form->addButton("Everything in the account\n" . $all . " coin");
		$form->addButton("Half the account\n" . $half . " coin");
		$form->addButton("Withdraw 20%\n" . $min . " coin");
		$form->addButton("Specific amount");

		$player->sendForm($form);
	}

	public function specificAmount(Player $player)
	{
		$form = new CustomForm(function(Player $player, ?array $data)
		{
			if ($data == null) return;
			if (!is_numeric($data[0]))
			{
				$player->sendMessage("Amount must be numeric !");
				return;
			}
			$this->withdraw($player, (float)$data[0]);
		});

		$form->setTitle("Withdraw specific amount");
		$form->addInput("Amount:", "123456789");

		$player->sendForm($form);
	}

	public function withdraw(Player $player, float $amount): void
	{
		if ($amount > $this->balance)
		{
			$player->sendMessage("You can't withdraw with amount that higher than your balance !");
			return;
		}
		$this->getBank()->getProvider()->update($player, $this->balance - $amount);
		EconomyAPI::getInstance()->addMoney($player, $amount);
		$player->sendMessage("Withdraw success full (+ " . $amount . " coin)");
	}
}