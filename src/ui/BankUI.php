<?php
declare(strict_types=1);

namespace Bank\ui;

use jojoe77777\FormAPI\SimpleForm;
use onebone\economyapi\EconomyAPI;
use pocketmine\player\Player;
use SOFe\AwaitGenerator\Await;

class BankUI extends BaseUI
{
	public function execute(Player $player) : void
	{
		Await::f2c(function() use ($player)
		{
			$data = yield $this->getBank()->getProvider()->get($player);
			if (empty($data))
			{
				$this->getBank()->getProvider()->register($player);
			}
			$data = yield $this->getBank()->getProvider()->get($player);
			if (empty($data))
			{
				$player->sendMessage("Error: Can't get data from database, please report this error to admin !");
				return;
			}
			$balance = $data[0]["Money"];
			$purse = EconomyAPI::getInstance()->myMoney($player);
			$form = new SimpleForm(function(Player $player, ?int $data) use ($balance)
			{
				if (($data == null) or ($data == 0)) return;

				match ($data) {
					1 => new DepositUI($player, $this->getBank(), $balance),
					2 => new WithdrawUI($player, $this->getBank(), $balance)
				};
			});

			$form->setTitle("Personal Bank Account");
			$form->setContent("Current balance: " . $balance . "\n" . "Your purse: " . $purse);
			$form->addButton("EXIT");
			$form->addButton("Deposit coins");
			$form->addButton("Withdraw coins");

			$player->sendForm($form);
		});
	}
}