<?php

namespace solo\sololand\world;

use pocketmine\math\Vector3;

class TestSection extends Section{
	
	public function __construct(int $sectionX, int $sectionZ, LandManager $manager){
		$this->startX = $sectionX * self::WIDTH;
		$this->startZ = $sectionZ * self::DEPTH;
		$this->endX = $sectionX * self::WIDTH + self::WIDTH - 1;
		$this->endZ = $sectionZ * self::DEPTH + self::DEPTH - 1;
	
		$this->manager = $manager;
		$section = $this;
		$condition = function($land) use ($section){
			// TEST CODE
			/*return true;*/
			
			return $section->isOverlap($land);
		};
	
		foreach($manager->getLands($condition) as $land){
			$this->addLand($land->getId());
		}
	}
	
	public function getLand(Vector3 $vec){
		$start = microtime(true);
		foreach($this->lands as $id => $fake){
			$land = $this->manager->getLandById($id);
			if($land->isInside($vec)){
				$this->record($start);
				return $land;
			}
		}
		$this->record($start);
		return null;
	}
	
	private function record($start){
		echo round((microtime(true) - $start) * 1000, 3) . "ms" . PHP_EOL;
	}
}