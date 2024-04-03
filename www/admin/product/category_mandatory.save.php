<?
include("../../class/database.class");
////////////////////
//  2013.05.07 신훈식
//  수정 : 인클루드 패스 오류
//
/////////////////////
//include("../class/database.class");
include('../../include/xmlWriter.php');
session_start();

$db = new Database;
$db2 = new Database;

if($mode == 'update'){	//카테고리별 상품고시정부 추가 부분 2014-07-14 이학봉
	

	if($cid != ""){
		$sql = "update shop_category_info set mandatory_type = '".$mi_ix."' where cid = '".$cid."'";
		$db->query($sql);
	}

	if(count($pcode) > 0 ){
		for($i=0;$i<count($pcode);$i++){
			$man_cid = $pcode[$i];

			$sql = "update shop_category_info set mandatory_type = '".$mi_ix."' where cid = '".$man_cid."'";
			$db->query($sql);
		}
		
	}

	echo "<Script Language='JavaScript'>alert('처리완료 되었습니다.');parent.location.reload();</Script>";

}

if($mode == 'category_mandatory'){

	if($cid){
		$sql = "select 
					ci.cid,mi.*

				from
					shop_category_info as ci 
					left join shop_mandatory_info as mi on (ci.mandatory_type = mi.mi_ix)
				where
					ci.cid = '".$cid."'";
		$db->query($sql);
		$data_array = $db->fetchall();
		for($i=0;$i<count($data_array);$i++){
			$access_data[mi_code] = $data_array[$i][mi_code];
			$access_data[mandatory_name] = $data_array[$i][mandatory_name];
		}
		

		$datas = $access_data;
		$datas = json_encode($datas);
		$datas = str_replace("\"true\"","true",$datas);
		$datas = str_replace("\"false\"","false",$datas);
		echo $datas;
		
	}
}

?>