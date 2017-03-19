<?php

namespace solo\sololand\world;

use pocketmine\math\Vector3;
use solo\sololand\land\Land;

interface ILandManager{
	
	public function getNextLandId() : int;
	
	public function addLand(Land $land);
	
	public function removeLand(int $id) : bool;
	
	public function getLand(Vector3 $vec);
	
	public function getLandById(int $id);
	
	public function getLands($condition = null) : array;
	
}