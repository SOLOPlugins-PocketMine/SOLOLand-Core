<?php

namespace solo\sololand\math;

class Sphere{
	
	private function __construct(){
		
	}
	
	public static function getElements(int $centerX, int $centerY, int $centerZ, int $radius) : array{
		$minX = $centerX - $radius;
		$maxX = $centerX + $radius;
		$minY = $centerY - $radius;
		$maxY = $centerY + $radius;
		$minZ = $centerZ - $radius;
		$maxZ = $centerZ + $radius;
		$ret = [];

		for($x = $minX; $x <= $maxX; $x++){
			for($y = $minY; $y <= $maxY; $y++){
				for($z = $minZ; $z <= $maxZ; $z++){
					$diff = self::getDiff($x, $y, $z, $centerX, $centerY, $centerZ);
					if($diff < $radius - 0.2){
						$ret[] = [$x, $y, $z];
					}
				}
			}
		}
		return $ret;
	}

	protected static function getDiff(int $startX, int $startY, int $startZ, int $endX, int $endY, int $endZ) : double{
		$xzDiff = sqrt(pow(abs($startX - $endX), 2) + pow(abs($startZ - $endZ), 2));
		return sqrt(pow($xzDiff, 2) + pow(abs($startY - $endY), 2));
	}
}