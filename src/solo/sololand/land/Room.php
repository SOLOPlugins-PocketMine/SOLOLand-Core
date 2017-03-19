<?php

namespace solo\sololand\land;

use pocketmine\Player;
use pocketmine\command\CommandSender;
use pocketmine\math\Vector3;
use solo\sololand\math\Cuboid;

class Room extends Cuboid{

	public $id;
	public $owner = "";
	public $members = [];
	
	public $price = -1;

	public $spawnPoint;
	
	public $welcomeMessage = "";

	
	public function __construct(int $id, Cuboid $cuboid = null){
		$this->id = $id;
		if($cuboid !== null){
			$this->set($cuboid);
		}
	}
	
	public function getId() : int{
		return $this->id;
	}
	
	public function isSail() : bool{
		return $this->price >= 0;
	}
	
	public function getPrice() : bool{
		return $this->price;
	}
	
	public function setPrice($price){
		$this->price = $price < 0 ? -1 : $price;
	}
	
	public function hasOwner() : bool{
		return $this->owner !== "";
	}
	
	public function getOwner() : string{
		return $this->owner;
	}
	
	public function setOwner($player){
		if($player instanceof CommandSender){
			$player = $player->getName();
		}
		$player = strtolower($player);
	
		$this->owner = $player;
		if(isset($this->members[$this->owner])){
			unset($this->members[$this->owner]);
		}
	}
	
	public function isOwner($player) : bool{
		if($player instanceof CommandSender){
			$player = $player->getName();
		}
		$player = strtolower($player);
	
		return $this->owner === $player;
	}
	
	public function getMembers() : array{
		return $this->members;
	}
	
	public function setMembers(array $members){
		$arr = [];
		foreach($members as $member){
			if($member instanceof CommandSender){
				$member = $member->getName();
			}
			$arr[strtolower($member)] = $member;
		}
		$this->members = $arr;
	}
	
	public function isMember($player) : bool{
		if($player instanceof CommandSender){
			$player = $player->getName();
		}
		$player = strtolower($player);
	
		return isset($this->members[$player]);
	}
	
	public function addMember($player) : bool{
		if($player instanceof CommandSender){
			$player = $player->getName();
		}
	
		if(!isset($this->members[strtolower($player)])){
			$this->members[strtolower($player)] = $player;
		}
		return true;
	}
	
	public function removeMember($player) : bool{
		if($player instanceof CommandSender){
			$player = $player->getName();
		}
		$player = strtolower($player);
	
		if(isset($this->members[$player])){
			unset($this->members[$player]);
			return true;
		}
		return false;
	}
	
	public function getSpawnPoint() : Vector3{
		return $this->spawnPoint;
	}
	
	public function setSpawnPoint(Vector3 $vec){
		$this->spawnPoint = new Vector3($vec->x, $vec->y, $vec->z);
	}
	
	public function setWelcomeMessage(string $message){
		$this->welcomeMessage = $message;
	}
	
	public function getWelcomeMessage() : string{
		return $this->welcomeMessage;
	}
}