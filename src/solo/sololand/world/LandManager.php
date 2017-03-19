<?php

namespace solo\sololand\world;

use pocketmine\math\Vector3;
use solo\sololand\Main;
use solo\sololand\land\Land;
use solo\solocore\util\Debug;

class LandManager implements ILandManager{

	public $lands = [];
	public $sections = [];
	
	public $lastRemember = 1;
	
	public function __construct(array $lands){
		$this->lands = $lands;
	}
	
	public function getSection(Vector3 $vec) : Section{
		$sectionX = Section::getSectionX($vec->getFloorX());
		$sectionZ = Section::getSectionZ($vec->getFloorZ());
		$sectionHash = $sectionX . ":" . $sectionZ;
		
		if(!isset($this->sections[$sectionHash])){
			$this->sections[$sectionHash] = new Section($sectionX, $sectionZ, $this);
			Debug::normal(Main::getInstance(), "Section (" . $sectionX . ":" . $sectionZ . ") 생성됨");
		}
		return $this->sections[$sectionHash];
	}
	
	public function getNextLandId() : int{
		if(!isset($this->lands[$this->lastRemember])){
			return $this->lastRemember;
		}else if(!isset($this->lands[++$this->lastRemember])){
			return $this->lastRemember;
		}
		$id = 0;
		while(isset($this->lands[++$id])){
			// :)
		}
		$this->lastRemember = $id;
		return $id;
	}
	
	public function addLand(Land $land) : Land{
		if(isset($this->lands[$land->getId()])){
			$this->removeLand($land->getId());
		}
		$this->lands[$land->getId()] = $land;
		foreach($this->sections as $section){
			if($section->isOverlap($land)){
				$section->addLand($land->getId());
			}
		}
		return $land;
	}
	
	public function removeLand(int $id) : bool{
		if(isset($this->lands[$id])){
			unset($this->lands[$id]);
			foreach($this->sections as $section){
				$section->removeLand($id);
			}
			return true;
		}
		return false;
	}
	
	public function getLandById(int $id){
		return $this->lands[$id] ?? null;
	}
	
	public function getLand(Vector3 $vec){
		return $this->getSection($vec)->getLand($vec);
	}
	
	public function getLands($condition = null) : array{
		if($condition === null){
			return $this->lands;
		}else{
			$ret = [];
			foreach($this->lands as $land){
				if($condition($land)){
					$ret[$land->getId()] = $land;
				}
			}
			return $ret;
		}
	}
}