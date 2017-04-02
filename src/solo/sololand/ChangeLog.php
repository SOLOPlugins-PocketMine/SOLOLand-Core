<?php

namespace solo\sololand;

class ChangeLog{
	
	private function __construct(){
		
	}
	
	public static $changeLog = [
			"0.0.1" => [
					"Nukkit -> PocketMine 포팅, 첫 릴리즈",
					"땅은 이제 500개 단위로 분할하여 저장합니다.",
					"getLand 메소드를 최적화하였습니다. (10~15ms -> 0.001~0.5ms)",
					"API를 간결하게 수정, 코드를 최대한 깔끔하게 정리하였습니다.",
					"서브 커맨드의 aliases 기능을 추가하였습니다. Ex) /땅 pvp = /땅 pvp허용 = /땅 전투허용",
					"제너레이터 기능은 본 플러그인과 분리됩니다."
			],
			"0.0.2" => [
					"디펜던시 로드 에러 수정"
			],
			"0.0.3" => [
					"많은 버그 픽스",
					"클래스 구조 향상"
			]
	];
	
	public static function getChangeLog(string $version){
	}	
}
	

/*

   
   _____  ____  _      ____  _                     _    
  / ____|/ __ \| |    / __ \| |                   | |   
 | (___ | |  | | |   | |  | | |     __ _ _ __   __| |   
  \___ \| |  | | |   | |  | | |    / _` | '_ \ / _` |   
  ____) | |__| | |___| |__| | |___| (_| | | | | (_| |   
 |_____/ \____/|______\____/|______\__,_|_| |_|\__,_|   
   _____ _                            _                 
  / ____| |                          | |                
 | |    | |__   __ _ _ __   __ _  ___| |     ___   __ _ 
 | |    | '_ \ / _` | '_ \ / _` |/ _ \ |    / _ \ / _` |
 | |____| | | | (_| | | | | (_| |  __/ |___| (_) | (_| |
  \_____|_| |_|\__,_|_| |_|\__, |\___|______\___/ \__, |
                            __/ |                  __/ |
                           |___/                  |___/ 





==================================
INTRO
==================================

[ SOLOLand ]
* PocketMine을 위한 월드, 땅, 섬 관리 플러그인 입니다.

http://blog.naver.com/solo_5 에서 최신 버전을 다운로드 받을 수 있습니다.





==================================
CHANGELOG
==================================

0.0.1
- Nukkit -> PocketMine 포팅 (땅 크기변경/합치기, 방 기능은 아직 포팅되지 않았습니다.)
- 땅은 이제 500개 단위로 분할하여 저장합니다.
- getLand 메소드를 최적화하였습니다. (10~15ms -> 0.001~0.5ms)
- API를 간결하게 수정, 코드를 최대한 깔끔하게 정리하였습니다.
- 서브 커맨드의 aliases 기능을 다시 추가하였습니다. /땅 pvp = /땅 pvp허용 = /땅 전투허용
- 제너레이터 기능은 본 플러그인과 분리됩니다.


*/