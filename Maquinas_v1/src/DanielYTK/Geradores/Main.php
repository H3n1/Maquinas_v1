<?php

namespace DanielYTK\Geradores;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\item\Item;
use pocketmine\tile\Tile;
use pocketmine\tile\Sign;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\block\Block;
use pocketmine\inventory\Inventory;
use pocketmine\inventory\PlayerInventory;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\Config;
use DanielYTK\Geradores\Tasks\GeneratorTask;
use onebone\economyapi\EconomyAPI;

class Main extends PluginBase implements Listener
{
	private $money;
	public function onEnable()
	{
		@mkdir($this->getDataFolder());
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->config = new Config($this->getDataFolder()."config.yml", Config::YAML, [ "tijolo" => 0, 1 => 0, 2 => 0, 5 => 0, 10 => 0, ]);
		$this->config->save();
		$this->geradores = new Config($this->getDataFolder()."geradores.yml", Config::YAML, []);
		$this->geradores->save();
		$this->money = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
		$this->getServer()->getLogger()->info("§aMáquinas-Free Ligado.");
	}
	
	public function onDisable()
	{
		foreach($this->geradores->getAll() as $all => $key){ $this->geradores->set($all, ["Ativado" => false]);
		$this->geradores->save();
	}
	$this->getServer()->getLogger()->info("§aMáquinas-Free Desligado.");
}
public function onCommand(CommandSender $sender, Command $command, $label, array $args)
{
	switch(strtolower($command->getName()))
	{
		case "maquinas":
		if(!isset($args[0]))
		{
			$sender->sendMessage("§9Máquinas> §f Use /maquinas comprar <item>");
			$sender->sendMessage("§9Máquinas> §f Use /maquinas lista");
		}else{
			if(strtolower($args[0]) == "lista")
			{
				$sender->sendMessage("----=[ §9Lista de Máquinas §f]=----");
				$sender->sendMessage("§9Tijolo:§f ".$this->config->get("tijolo"));
			}elseif(strtolower($args[0]) == "comprar")
			{
				if(!isset($args[1]))
				{
					$sender->sendMessage("§9Máquinas> §f Use /maquinas comprar <item>");
				}else{
					if(strtolower($args[1]) == "tijolo")
					{
						if($this->money->myMoney($sender) >= $reduce = $this->config->get("tijolo"))
						{
							$sender->getInventory()->addItem(Item::get(43, 4, 1)->setCustomName("§6Máquina de Tijolo"));
							$this->money->reduceMoney($sender, $reduce);
							$sender->sendMessage("§aMáquina comprada com sucesso.");
						}else{
							$sender->sendMessage("§cVocê não tem dinheiro suficiente para comprar essa máquina");
						}
					}
				}
			}
		}
		break;
		case "buster": if(!isset($args[0]))
		{
			$sender->sendMessage("§9Máquinas> §f Use /buster tempos");
			$sender->sendMessage("§9Máquinas> §f Use /buster comprar <tempo>");
		}else{
			if(strtolower($args[0]) == "tempos")
			{
				$sender->sendMessage("----=[ §9Tempo das Máquinas §f]=----");
				$sender->sendMessage("§91:§f Um Minuto");
				$sender->sendMessage("§92:§f Dois Minutos");
				$sender->sendMessage("§95:§f Cinco Minutos");
				$sender->sendMessage("§910:§f Dez Minutos");
			}elseif(strtolower($args[0]) == "comprar")
			{
				if(!isset($args[1]))
				{
					$sender->sendMessage("§9Máquinas> §f Use /buster comprar <tempo>");
				}else{
					if($args[1] == 1)
					{
						if($this->money->myMoney($sender) >= $reduce = $this->config->get(1))
						{
							$sender->getInventory()->addItem(Item::get(263, 0, 1)->setCustomName("§cCOMBUSTÍVEL (1 minuto)"));
							$sender->sendMessage("§aCombustível comprado com sucesso.");
							$this->money->reduceMoney($sender, $reduce);
						}else{
							$sender->sendMessage("§cVocê não tem dinheiro o suficiente para comprar o combustível");
						}
					}
					if($args[1] == 2)
					{
						if($this->money->myMoney($sender) >= $reduce = $this->config->get(2))
						{
							$sender->getInventory()->addItem(Item::get(263, 0, 1)->setCustomName("§cCOMBUSTÍVEL (2 minutos)"));
							$sender->sendMessage("§aCombustível comprado com sucesso.");
							$this->money->reduceMoney($sender, $reduce);
						}else{
							$sender->sendMessage("§cVocê não tem dinheiro o suficiente para comprar o combustível");
						}
					}
					if($args[1] == 5)
					{
						if($this->money->myMoney($sender) >= $reduce = $this->config->get(5))
						{
							$sender->getInventory()->addItem(Item::get(263, 0, 1)->setCustomName("§cCOMBUSTÍVEL (5 minutos)"));
							$sender->sendMessage("§aCombustível comprado com sucesso.");
							$this->money->reduceMoney($sender, $reduce);
						}else{
							$sender->sendMessage("§cVocê não tem dinheiro o suficiente para comprar o combustível");
						}
					}
					if($args[1] == 10)
					{
						if($this->money->myMoney($sender) >= $reduce = $this->config->get(10))
						{
							$sender->getInventory()->addItem(Item::get(263, 0, 1)->setCustomName("§cCOMBUSTÍVEL (10 minutos)"));
							$sender->sendMessage("§aCombustível comprado com sucesso.");
							$this->money->reduceMoney($sender, $reduce);
						}else{
							$sender->sendMessage("§cVocê não tem dinheiro o suficiente para comprar o combustível");
						}
					}
				}
			}
		}
		break;
	}
}
public function onInteract(PlayerInteractEvent $ev)
{
	$player = $ev->getPlayer();
	$item = $ev->getItem();
	$block = $ev->getBlock();
	$y = $block->y+1;
	$string = $block->getX() ."-". $y ."-". $block->getZ() . "-" .$block->getLevel()->getFolderName();
	if($item->getId() === 43 && $item->hasCustomName())
	{
		if($item->getCustomName() == "§6Máquina de Tijolo")
		{
			$this->geradores->set($string, ["Ativado" => false]);
			$player->sendMessage("§9Máquinas> §e Máquina Instalada, para fazê-la funcionar coloque seu combustível/buster");
		}
	}
	$this->geradores->save();
	if($item->getId() === 263 && $item->hasCustomName() && $this->isMachine($block))
	{
		$string = $block->getX() ."-". $block->getY() ."-". $block->getZ() . "-" .$block->getLevel()->getFolderName();
		if($this->geradores->get($string)["Ativado"])
		{
			$player->sendMessage("§9Máquinas> §c Essa máquina já está ativada.");
			return;
		}
		if($item->getCustomName() == "§cCOMBUSTÍVEL (1 minuto)")
		{
			$task = $this->getServer()->getScheduler()->scheduleRepeatingTask(new GeneratorTask($this, $block, 60), 20);
			$this->geradores->set($string, ["Ativado" => true, "TaskId" => $task->getTaskId()]);
			$item->setCount($item->getCount()-1);
			$player->getInventory()->setItemInHand($item);
			$player->sendMessage("§9Máquinas> §a A máquina foi ativada com sucesso.");
		}
		if($item->getCustomName() == "§cCOMBUSTÍVEL (2 minutos)")
		{
			$task = $this->getServer()->getScheduler()->scheduleRepeatingTask(new GeneratorTask($this, $block, 120), 20);
			$this->geradores->set($string, ["Ativado" => true, "TaskId" => $task->getTaskId()]);
			$item->setCount($item->getCount()-1);
			$player->getInventory()->setItemInHand($item);
			$player->sendMessage("§9Máquinas> §a A máquina foi ativada com sucesso.");
		}
		if($item->getCustomName() == "§cCOMBUSTÍVEL (5 minutos)")
		{
			$task = $this->getServer()->getScheduler()->scheduleRepeatingTask(new GeneratorTask($this, $block, 300), 20);
			$this->geradores->set($string, ["Ativado" => true, "TaskId" => $task->getTaskId()]);
			$item->setCount($item->getCount()-1);
			$player->getInventory()->setItemInHand($item);
			$player->sendMessage("§9Máquinas> §a A máquina foi ativada com sucesso.");
		}
		if($item->getCustomName() == "§cCOMBUSTÍVEL (10 minutos)")
		{
			$task = $this->getServer()->getScheduler()->scheduleRepeatingTask(new GeneratorTask($this, $block, 600), 20);
			$this->geradores->set($string, ["Ativado" => true, "TaskId" => $task->getTaskId()]);
			$item->setCount($item->getCount()-1);
			$player->getInventory()->setItemInHand($item);
			$player->sendMessage("§9Máquinas> §a A máquina foi ativada com sucesso.");
		}
	}
}
public function onBreak(BlockBreakEvent $ev)
{
	$player = $ev->getPlayer();
	$block = $ev->getBlock();
	$string = $block->x ."-". $block->y ."-". $block->z ."-". $block->getLevel()->getFolderName();
	if($ev->isCancelled()) return true;
	if($this->isMachine($block))
	{
		$player->sendMessage("§9Máquinas> §a A máquina foi retirada com sucesso.");
		$all = $this->geradores->getAll();
		$this->getServer()->getScheduler()->cancelTask($all[$string]["TaskId"]);
		$this->geradores->remove($string);
		$this->geradores->save();
	}
}
public function isMachine(Position $pos)
{
	foreach($this->geradores->getAll() as $key => $val)
	{
		$coords = explode("-", $key);
		if($pos->x == $coords[0] && $pos->y == $coords[1] && $pos->z == $coords[2] && $pos->getLevel()->getFolderName() == $coords[3])
		{
			return true;
		}
	}
}
}