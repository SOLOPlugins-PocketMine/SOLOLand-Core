<?php

namespace solo\sololand\world;

use pocketmine\utils\Config;
use pocketmine\math\Vector3;
use solo\sololand\land\Land;

class LandProvider implements IProvider{
	
	const CHUNK_SIZE = 500;
	
	public $path;
	
	public function __construct(string $path){
		$this->path = $path;
	}
	
	public function load($condition = null) : array{
		$lands = [];
		
		$files = [];
		$handle = opendir($this->path);
		while($file = readdir($handle)){
			if($file != "." && $file != ".." && is_dir($file) != "1"){
				$files[] = $file;
			}
		}
		closedir($handle);
		
		foreach($files as $file){
			$config = new Config($this->path . $file, Config::YAML);
			foreach($config->getAll() as $id => $landData){
				$lands[$id] = $this->deserializeLand($id, $landData);
			}
		}
		return $lands;
	}
	
	public function save(array $lands, $condition = null){
		$serialize = [];
		foreach($lands as $land){
			$fileIndex = (int) $land->getId() / self::CHUNK_SIZE;
			if(!isset($serialize[$fileIndex])){
				$serialize[$fileIndex] = [];
			}
			$serialize[$fileIndex][$land->getid()] = $this->serializeLand($land);
		}
		
		foreach($serialize as $key => $chunk){
			$config = new Config($this->path . "lands." . $key . ".yml", Config::YAML);
			$config->setAll($chunk);
			$config->save();
		}
	}
	
	protected function deserializeLand(int $id, array $landData) : Land{
		$land = new Land($id);
		$land->owner = $landData["owner"] ?? "";
		$land->members = $landData["members"] ?? [];
		$land->startX = $landData["startX"];
		$land->startZ = $landData["startZ"];
		$land->endX = $landData["endX"];
		$land->endZ = $landData["endZ"];
		$land->price = $landData["price"] ?? -1;
		$v = explode(":", $landData["spawnPoint"]);
		$land->spawnPoint = new Vector3($v[0], $v[1], $v[2]);
		$land->allowPVP = $landData["pvp"] ?? false;
		$land->allowAccess = $landData["access"] ?? true;
		$land->allowPickupItem = $landData["pickupItem"] ?? false;
		$land->welcomeMessage = $landData["welcomeMessage"] ?? "";
		$land->welcomeParticle = $landData["welcomeParticle"] ?? 0;
		foreach($landData["rooms"] ?? [] as $roomId => $roomData){
			$room = new Room($roomId);
			$room->members = $roomData["members"] ?? [];
			$room->startX = $roomData["startX"];
			$room->startY = $roomData["startY"];
			$room->startZ = $roomData["startZ"];
			$room->endX = $roomData["endX"];
			$room->endY = $roomData["endY"];
			$room->endZ = $roomData["endZ"];
			$room->price = $roomData["price"] ?? -1;
			$vv = explode(":", $roomData["spawnPoint"]);
			$room->spawnPoint = new Vector3($vv[0], $vv[1], $vv[2]);
			$room->welcomeMessage = $roomData["welcomeMessage"] ?? "";
				
			$land->rooms[$roomId] = $room;
		}
		return $land;
	}
	
	protected function serializeLand(Land $land) : array{
		$landData = [];
		if($land->owner !== ""){
			$landData["owner"] = $land->owner;
		}
		if(!empty($land->members)){
			$landData["members"] = $land->members;
		}
		$landData["startX"] = $land->startX;
		$landData["startZ"] = $land->startZ;
		$landData["endX"] = $land->endX;
		$landData["endZ"] = $land->endZ;
		if($land->price !== -1){
			$landData["price"] = $land->price;
		}
		$landData["spawnPoint"] = $land->spawnPoint->x . ":" . $land->spawnPoint->y . ":" . $land->spawnPoint->z;
		if($land->allowPVP){ // default value : false
			$landData["pvp"] = $land->allowPVP;
		}
		if(!$land->allowAccess){ // default value : true
			$landData["access"] = $land->allowAccess;
		}
		if($land->allowPickupItem){ // default value : false
			$landData["pickupItem"] = $land->allowPickupItem;
		}
		if($land->welcomeMessage !== ""){
			$landData["welcomeMessage"] = $land->welcomeMessage;
		}
		if($land->welcomeParticle !== 0){
			$landData["welcomeParticle"] = $land->welcomeParticle;
		}
		if(!empty($land->rooms)){
			$landData["rooms"] = [];
			foreach($land->getRooms() as $room){
				if($room->owner !== ""){
					$roomData["owner"] = $room->owner;
				}
				if(!empty($room->members)){
					$roomData["members"] = $room->members;
				}
				$roomData["startX"] = $room->startX;
				$roomData["startY"] = $room->startY;
				$roomData["startZ"] = $room->startZ;
				$roomData["endX"] = $room->endX;
				$roomData["endY"] = $room->endY;
				$roomData["endZ"] = $room->endZ;
				if($room->price !== -1){
					$roomData["price"] = $room->price;
				}
				$roomData["spawnPoint"] = $room->spawnPoint->x . ":" . $room->spawnPoint->y . ":" . $room->spawnPoint->z;
				if($room->welcomeMessage !== ""){
					$roomData["welcomeMessage"] = $room->welcomeMessage;
				}
				
				$landData["rooms"][$room->getId()] = $roomData;
			}
		}
		return $landData;
	}
}