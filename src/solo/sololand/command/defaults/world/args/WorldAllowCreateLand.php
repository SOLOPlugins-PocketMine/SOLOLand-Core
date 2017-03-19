<?php

namespace solo\sololand\command\defaults\world\args;

use pocketmine\command\CommandSender;
use solo\sololand\command\SubCommand;
use solo\sololand\world\World;

class WorldAllowCreateLand extends SubCommand{

	public function __construct(){
		parent::__construct("땅생성허용", "월드의 땅 생성 허용 여부를 설정합니다.");
		$this->setPermission("sololand.command.world.allowcreateland");
	}

	public function execute(CommandSender $sender, array $args){
		$world = World::getWorld($sender);
		$world->getLandProperties()->setAllowCreateLand(!$world->getLandProperties()->isAllowCreateLand());
		Message::normal($sender, $world->getName() . " 월드의 땅 생성을 " . ($world->getLandProperties()->isAllowCreateLand() ? "허용하였습니다." : "금지하였습니다."));
		return true;
	}
}