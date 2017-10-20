<?php

namespace DanielYTK\Geradores\Tasks;

use pocketmine\Player;
use pocketmine\scheduler\PluginTask;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\level\Position;
use pocketmine\block\Chest;
use pocketmine\level\Level;
use pocketmine\inventory\ChestInventory;
use pocketmine\block\Block;
use pocketmine\tile\Tile;
use pocketmine\tile\Chest as ChestTile;
use pocketmine\tile\Sign;
use DanielYTK\Geradores\Main;

class GeneratorTask extends PluginTask
{
	private $plugin, $block, $time;
	public function __construct(Main $plugin, Block $block, int $time)
	{
		parent::__construct($plugin);
		$this->plugin = $plugin;
		$this->block = $block;
		$this->time = $time;
	}
	public function getServer(){ return $this->plugin->getServer();
}
public function addItemInChest(int $id)
{
	if($this->block->getLevel()->getBlock(new Vector3($this->block->x, $this->block->y+1, $this->block->z))->getId() === Block::CHEST)
	{
		$tile = $this->block->getLevel()->getTile(new Vector3($this->block->x, $this->block->y+1, $this->block->z));
		if($tile instanceof ChestTile)
		{
			$tile->getInventory()->addItem(Item::get($id, 0, 1));
		}
	}else{
		$this->block->getLevel()->dropItem(new Vector3($this->block->x, $this->block->y+1, $this->block->z), Item::get($id, 0, 1));
	}
}
public function onRun($ticks)
{
	$this->time--;
	$string = $this->block->x."-".$this->block->y."-".$this->block->z."-".$this->block->getLevel()->getFolderName();
	if($this->time === 0)
	{
		foreach($this->getServer()->getOnlinePlayers() as $ps)
		{
			if($ps->distance($this->block) <= 7)
			{
				$ps->sendMessage("§9Máquinas> §cUma máquina próxima a você foi desligada.");
			}
		}
		$this->getServer()->getScheduler()->cancelTask($this->getTaskId());
		$this->plugin->geradores->set($string, ["Ativado" => false]);
		$this->plugin->geradores->save();
	}
	if($this->block->getId() === 43){ $this->addItemInChest(336); 
}
}
}