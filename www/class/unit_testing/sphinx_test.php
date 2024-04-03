<?php 
	require '../sphinxfb.class';
	$sfb = new sphinxfb("127.0.0.1" // 스핑크스 서버 주소
					  , "9306"      // 스핑크스 서버 포트
					  
					  , "127.0.0.1" // mysql 서버 주소
					  , "root"      // mysql 서버 계정
					  , "vhqlwm2011" // mysql 서버 패스워드
					  , "3306"      // mysql 서버 포트
					  , "omnichannelnew_db"); // mysql 데이터베이스
	
	// 빌딩
	$sfb->rebuild_index(" id between '1' and '30'");
	
	//조회
	//$rows = $sfb->query(" id between 1 and 20");
	//print_r($rows);
	
	//조회 SphinxQL 참조 
	//$rows = $sfb->query(" MATCH('@pname \"차이나\"') ");
	//print_r($rows);
	
	// and 조건
	//$rows = $sfb->query(" MATCH('@shop_name \"라이프\" @pname \"인기\"') ");
	//print_r($rows);
	
	// 삭제
	//$result = $sfb->remove(" id = 2 ");
	//printf($result);
?>
