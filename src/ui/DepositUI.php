<?php
declare(strict_types=1);

namespace Bank\ui;

use Bank\Bank;
use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\SimpleForm;
use onebone\economyapi\EconomyAPI;
use pocketmine\player\Player;

class DepositUI extends BaseUI
{
	private float $balance;

	public function __construct(Player $player, Bank $bank, float $balance)
	{
		$this->balance = $balance;
		parent::__construct($player, $bank);
	}

	public function execute(Player $player) : void
	{
		$purse = EconomyAPI::getInstance()->myMoney($player);
		$all = $purse;
		$half = round($purse / 2, 2, PHP_ROUND_HALF_DOWN);
		$min = round($purse / 5, 2, PHP_ROUND_HALF_DOWN);

		$form = new SimpleForm(function(Player $player, ?int $data) use ($all, $half, $min)
		{
			if ($data == null) return;

			switch ($data)
			{
				case 0:
					new BankUI($player, $this->getBank());
					break;
				case 1:
					$this->deposit($player, $all);
					break;
				case 2:
					$this->deposit($player, $half);
					break;
				case 3:
					$this->deposit($player, $min);
					break;
				case 4:
					$this->specificAmount($player);
					break;
			}
		});
		$form->setTitle("Bank deposit");
		$form->addButton("Back");
		$form->addButton("Your whole purse\n" . $all . " coin");
		$form->addButton("Half your purse\n" . $half . " coin");
		$form->addButton("Deposit 20%\n" . $min . " coin");
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
			$this->deposit($player, (float)$data[0]);
		});

		$form->setTitle("Deposit specific amount");
		$form->addInput("Amount:", "123456789");

		$player->sendForm($form);
	}

	public function deposit(Player $player, float $amount): void
	{
		$purse = EconomyAPI::getInstance()->myMoney($player);

		if ($amount > $purse)
		{
			$player->sendMessage("You can't deposit with amount that higher than your coins in purse !");
			return;
		}
		$this->getBank()->getProvider()->update($player, $this->balance + $amount);
		EconomyAPI::getInstance()->reduceMoney($player, $amount);
		$player->sendMessage("Deposit success full (- " . $amount . " coin)");
	}
}