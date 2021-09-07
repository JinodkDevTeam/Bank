<?php
declare(strict_types=1);

namespace Bank\provider;

use Bank\Bank;
use Exception;
use Generator;
use pocketmine\player\Player;
use pocketmine\Server;
use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;
use SOFe\AwaitGenerator\Await;

class SqliteProvider
{
	public const INIT = "bank.init";
	public const REGISTER = "bank.register";
	public const GET = "bank.get";
	public const REMOVE = "bank.remove";
	public const UPDATE = "bank.update";
	public const GET_ALL = "bank.getall";
	public const TOP = "bank.top";

	private DataConnector $database;
	private Bank $bank;

	public function __construct(Bank $bank)
	{
		$this->bank = $bank;
	}

	private function getBank(): Bank
	{
		return $this->bank;
	}

	public function init(): void
	{
		try
		{
			$this->database = libasynql::create($this->getBank(), $this->getBank()->getConfig()->get("database"), [
				"sqlite" => "sqlite.sql"
			]);
			$this->database->executeGeneric(self::INIT);
		}
		catch(Exception)
		{

			$this->getBank()->getLogger()->error("Failed create database.");
			Server::getInstance()->getPluginManager()->disablePlugin($this->bank);
		}
	}

	public function close(): void
	{
		if (isset($this->database)) $this->database->close();
	}

	public function asyncSelect(string $query, array $args = []) : Generator
	{
		$this->database->executeSelect($query, $args, yield, yield Await::REJECT);

		return yield Await::ONCE;
	}

	public function remove(Player|string $player): void
	{
		if($player instanceof Player) $player = $player->getName();

		$this->database->executeChange(self::REMOVE, [
			"player" => $player
		]);
	}

	public function update(Player|string $player, float $value): void
	{
		if ($player instanceof Player) $player = $player->getName();

		$this->database->executeChange(self::UPDATE, [
			"player" => $player,
			"value" => $value
		]);
	}

	public function register (Player|string $player, float $value = 0): void
	{
		if ($player instanceof Player) $player = $player->getName();

		$this->database->executeChange(self::REGISTER, [
			"player" => $player,
			"value" => $value
		]);
	}

	public function get(Player|string $player): Generator
	{
		if ($player instanceof Player) $player = $player->getName();

		return yield $this->asyncSelect(self::GET, [
			"player" => $player
		]);
	}

	public function getAll(): Generator
	{
		return yield $this->asyncSelect(self::GET_ALL);
	}
}