<?php

namespace solo\sololand\world;

use pocketmine\Server;
use pocketmine\level\Level;
use pocketmine\level\Position;
use solo\sololand\event\world\WorldCreationEvent;
use solo\sololand\event\world\WorldInitEvent;

class World{

	public static $classes = [];
	public static $worlds = [];
	
	public static function registerWorld($generator, $worldClass, $worldProviderClass = null, $landProviderClass = null, $landManagerClass = null){
		self::$classes[$generator] = [];
		self::$classes[$generator]["world"] = $worldClass;
		if($worldProviderClass !== null){
			self::$classes[$generator]["worldProvider"] = $worldProviderClass;
		}
		if($landProviderClass !== null){
			self::$classes[$generator]["landProvider"] = $landProviderClass;
		}
		if($landManagerClass !== null){
			self::$classes[$generator]["landManager"] = $landManagerClass;
		}
	}
	
	public static function loadWorld(Level $level) : bool{
		if(isset(self::$worlds[$level->getFolderName()])){
			return false;
		}
		$generator = $level->getProvider()->getGenerator();
		$ev = new WorldCreationEvent(
				$level,
				self::$classes[$generator]["world"] ?? World::class,
				self::$classes[$generator]["worldProvider"] ?? WorldProvider::class,
				self::$classes[$generator]["landProvider"] ?? LandProvider::class,
				self::$classes[$generator]["landManager"] ?? LandManager::class
				);
		Server::getInstance()->getPluginManager()->callEvent($ev);
		if($ev->isCancelled()){
			return false;
		}
		
		$worldClass = $ev->getWorldClass();
		$worldProviderClass = $ev->getWorldProviderClass();
		$landProviderClass = $ev->getLandProviderClass();
		$landManagerClass = $ev->getLandManagerClass();
		
		$world = new $worldClass($ev->getLevel(), $worldProviderClass, $landProviderClass, $landManagerClass);
		
		$ev = new WorldInitEvent($world);
		Server::getInstance()->getPluginManager()->callEvent($ev);
		if($ev->isCancelled()){
			return false;
		}
		
		self::$worlds[$world->getName()] = $world;
		return true;
	}
	
	public static function unloadWorld(World $world) : bool{
		if(isset(self::$worlds[$world->getName()])){
			self::$worlds[$world->getName()]->save();
			unset(self::$worlds[$world->getName()]);
			return true;
		}
		return false;
	}
	
	public static function getWorld($object){
		if($object instanceof Position){
			return self::$worlds[$object->getLevel()->getFolderName()] ?? null;
		}else if($object instanceof Level){
			return self::$worlds[$object->getFolderName()] ?? null;
		}else{
			return self::$worlds[$object] ?? null;
		}
		return null;
	}
	
	public static function getWorlds($condition = null) : array{
		if($condition === null){
			return self::$worlds;
		}else{
			$ret = [];
			foreach(self::$worlds as $world){
				if($condition($world)){
					$ret[] = $world;
				}
			}
			return $ret;
		}
	}

	
	
	
	
	//instance
	public $level;
	private $name;
	
	private $provider;
	public $properties;

	//lands
	private $landManager;
	private $landProvider;
	
	//temporary
	public $lastRemember = 1;

	public function __construct(Level $level, $worldProviderClass, $landProviderClass, $landManagerClass){
		$server = Server::getInstance();
		
		$this->level = $level;
		$this->name = $level->getFolderName();
		
		if(!is_a($worldProviderClass, IProvider::class, true)){
			throw new \RuntimeException("worldProviderClass must extend IProvider class");
		}
		
		if(!is_a($landProviderClass, IProvider::class, true)){
			throw new \RuntimeException("landProviderClass must extend IProvider class");
		}

		if(!is_a($landManagerClass, ILandManager::class, true)){
			throw new \RuntimeException("landManagerClass must extend ILandManager class");
		}
		
		$providerPath = $server->getDataPath() . "worlds/" . $level->getFolderName() . "/";
		$landProviderPath = $server->getDataPath() . "worlds/" . $level->getFolderName() . "/lands/";
		@mkdir($providerPath);
		@mkdir($landProviderPath);
		
		$this->provider = new $worldProviderClass($providerPath);
		$this->landProvider = new $landProviderClass($landProviderPath);
		
		$this->properties = $this->provider->load();
		$this->landManager = new $landManagerClass($this->landProvider->load());
	}
	
	
	
	
	
	public function getName() : string{
		return $this->name;
	}

	public function getLevel() : Level{
		return $this->level;
	}

	
	
	public function getProperties(){
		return $this->properties;
	}
	
	public function getWorldProperties(){
		return new class($this){
			public $world;
			
			public function __construct(World $world){
				$this->world = $world;
			}
			
			public function isProtected() : bool{
				return $this->world->properties["world"]["protect"];
			}
			
			public function setProtection(bool $bool){
				$this->world->properties["world"]["protect"] = $bool;
			}
			
			public function isInvensave() : bool{
				return $this->world->properties["world"]["invensave"];
			}
			
			public function setInvensave(bool $bool){
				$this->world->properties["world"]["invensave"] = $bool;
			}
			
			public function isAllowExplosion() : bool{
				return $this->world->properties["world"]["explosion"];
			}
			
			public function setAllowExplostion(bool $bool){
				$this->world->properties["world"]["explosion"] = $bool;
			}
			
			public function isAllowPVP() : bool{
				return $this->world->properties["world"]["pvp"];
			}
			
			public function setAllowPVP(bool $bool){
				$this->world->properties["world"]["pvp"] = $bool;
			}
		};
	}
	
	public function getLandProperties(){
		return new class($this){
			public $world;
				
			public function __construct(World $world){
				$this->world = $world;
			}
				
			public function isAllowCreate() : bool{
				return $this->world->properties["land"]["allowCreate"];
			}
				
			public function setAllowCreate(bool $bool){
				$this->world->properties["land"]["allowCreate"] = $bool;
			}
			
			public function isAllowCombine() : bool{
				return $this->world->properties["land"]["allowCombine"];
			}
			
			public function setAllowCombine(bool $bool){
				$this->world->properties["land"]["allowCombine"] = $bool;
			}
			
			public function isAllowResize() : bool{
				return $this->world->properties["land"]["allowResize"];
			}
			
			public function setAllowResize(bool $bool){
				$this->world->properties["land"]["allowResize"] = $bool;
			}
			
			public function getDefaultPrice(){
				return $this->world->properties["land"]["defaultPrice"];
			}
			
			public function setDefaultPrice($price){
				$this->world->properties["land"]["defaultPrice"] = $price;
			}
			
			public function getPricePerBlock(){
				return $this->world->properties["land"]["pricePerBlock"];	
			}
			
			public function setPricePerBlock($price){
				$this->world->properties["land"]["pricePerBlock"] = $price;
			}
			
			public function getMaxCountPerPlayer() : int{
				return $this->world->properties["land"]["maxCountPerPlayer"];
			}
			
			public function setMaxCountPerPlayer(int $count){
				$this->world->properties["land"]["maxCountPerPlayer"] = $count;
			}
			
			public function getMinLength() : int{
				return $this->world->properties["land"]["minLength"];
			}
			
			public function setMinLength(int $length){
				$this->world->properties["land"]["minLength"] = $length;
			}
			
			public function getMaxLength() : int{
				return $this->world->properties["land"]["maxLength"];
			}
			
			public function setMaxLength(int $length){
				$this->world->properties["land"]["maxLength"] = $length;
			}
		};
	}
	

	public function getRoomProperties(){
		return new class($this){
			public $world;
	
			public function __construct(World $world){
				$this->world = $world;
			}
	
			public function isAllowCreate() : bool{
				return $this->world->properties["room"]["allowCreate"];
			}
	
			public function setAllowCreate(bool $bool){
				$this->world->properties["room"]["allowCreate"] = $bool;
			}
				
			public function getDefaultPrice(){
				return $this->world->properties["room"]["defaultPrice"];
			}
				
			public function setDefaultPrice($price){
				$this->world->properties["room"]["defaultPrice"] = $price;
			}
				
			public function getPricePerBlock(){
				return $this->world->properties["room"]["pricePerBlock"];
			}
				
			public function setPricePerBlock($price){
				$this->world->properties["room"]["pricePerBlock"] = $price;
			}
				
			public function getMaxCountPerLand() : int{
				return $this->world->properties["room"]["maxCountPerLand"];
			}
				
			public function setMaxCountPerLand(int $count){
				$this->world->properties["room"]["maxCountPerLand"] = $count;
			}
				
			public function getMinLength() : int{
				return $this->world->properties["room"]["minLength"];
			}
				
			public function setMinLength(int $length){
				$this->world->properties["room"]["minLength"] = $length;
			}
				
			public function getMaxLength() : int{
				return $this->world->properties["room"]["maxLength"];
			}
				
			public function setMaxLength(int $length){
				$this->world->properties["room"]["maxLength"] = $length;
			}
		};
	}
	
	

	public function getLandManager() : ILandManager{
		return $this->landManager;
	}
	
	public function save(){
		$this->provider->save($this->properties);
		$this->landProvider->save($this->landManager->getLands());
	}
}