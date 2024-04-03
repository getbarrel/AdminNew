<?
include("../../class/database.class");
////////////////////
//  2014-05-06 이학봉
//  품목분류 다중검색 프로세스 실행 파일
//
/////////////////////

session_start();

$db = new Database;
$db2 = new Database;

if($mode == "select_category_info"){	//브랜드리스트 분류검색 ajax

	if($cid){
		$sql = "select depth from inventory_category_info where cid = '".$cid."'";
		$db->query($sql);
		$db->fetch();
		$depth = $db->dt[depth];

		$like_cid = substr($cid, 0,(3+$depth*3));
		$for_depth = $depth + 1;
		$sql = "select 
				*
				from
					inventory_category_info
				where
					cid like '".$like_cid."%'
					and cid != '".$like_cid."'
					and depth = '".$for_depth."'
					order by cid ASC";
		$db->query($sql);
		$category_array = $db->fetchall();

		for($i=0;$i<count($category_array);$i++){
			$category_info[$category_array[$i][cid]] = $category_array[$i][cname];
		}

		$datas = json_encode($category_info);
		$datas = str_replace("\"true\"","true",$datas);
		$datas = str_replace("\"false\"","false",$datas);
		echo $datas;
	
	}
}

if($mode == "select_position_md"){	//분류설정 부서불러오기 ajax
	if($dp_ix){

		$sql = "select
					cmd.code,
					AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
					cp.ps_name,
					cd.duty_name
				from
					common_member_detail as cmd 
					left join shop_company_position as cp on (cmd.position = cp.ps_ix)
					left join shop_company_duty as cd on (cmd.duty = cd.cu_ix)
				where
					cmd.department = '".$dp_ix."'";

		$db->query($sql);
		$data_array = $db->fetchall();

		for($i=0;$i<count($data_array);$i++){
			$items[$data_array[$i][code]] = $data_array[$i][name]." [".$data_array[$i][ps_name]."] "."[".$data_array[$i][duty_name]."]";
		}

		$datas = json_encode($items);
		$datas = str_replace("\"true\"","true",$datas);
		$datas = str_replace("\"false\"","false",$datas);
		echo $datas;

	}else{
		echo "";
	}

}

if($mode == "get_category_name"){
	if($cid){
		$sql = "select * from inventory_category_info where cid = '".$cid."'";
		$db->query($sql);
		$db->fetch();
		$depth = $db->dt[depth];
	
		for($i=0;$i<=$depth;$i++){
			$this_cid = substr(substr($cid, 0,($i*3+3)).'000000000000',0,15);
			//echo "$i"."<br>";
			$sql = "select * from inventory_category_info where cid = '".$this_cid."'";
			//echo nl2br($sql)."<br>";
			$db2->query($sql);
			$db2->fetch();
			$cname = $db2->dt[cname];
			
			if($i == $depth){
				$relation_cname .= $cname;
			}else{
				$relation_cname .= $cname." > ";
			}
		}
		$category_info[$cid] = $relation_cname;
	
		$datas = json_encode($category_info);
		$datas = str_replace("\"true\"","true",$datas);
		$datas = str_replace("\"false\"","false",$datas);
		echo $datas;
	}

}

?>