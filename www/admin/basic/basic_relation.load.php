<?
include("../../class/database.class");

$db = new Database;

if(false){

	$cid = $_GET['trigger'];
	$depth = $_GET['depth'];
	$target = $_GET['target'];
	$form = $_GET['form'];
	$type = $_GET['type'];
	$length= $depth + 10;

	if($cid){

		if($type == "3"){	//3일 경우 셀러를 불러온다(지사,사업소,영업소 제외한 하위사업장을 불러온다)
			$where_join = " inner join ".TBL_COMMON_SELLER_DETAIL." as csd on (cd.company_id = csd.company_id)";
		}else if($type == "2"){	//2일 경우 지사,사업소,영업소를 불러온다.
			$where = " and cd.com_type in ('BR','BP','BO') ";
		}

		$sql = "select
					cr.relation_code,
					cd.com_name
				from
					".TBL_COMMON_COMPANY_RELATION." as cr
					inner join ".TBL_COMMON_COMPANY_DETAIL." as cd on (cr.company_id = cd.company_id)
					$where_join
				where
					relation_code like '".$cid."%'
					and relation_code != '".$cid."'
					and length(relation_code) = '".$length."'
					$where
					order by relation_code ASC";

		$db->query($sql);
		$data_array = $db->fetchall();
		$cnt = count($data_array);

	}else{

		echo "<script type='text/javascript'>
			top.document.forms['$form'].elements['".$target."'].length = 1;
			top.document.forms['$form'].elements['cid2'].value = '';
		</script>";
		exit;
	}

	if ($cnt > 0){

		echo "<script type='text/javascript'>
				top.document.forms['$form'].elements['".$target."'].length = ".($cnt+1).";
				top.document.forms['$form'].elements['".$target."'].setAttribute('validation','false');
				var op_len=top.document.forms['$form'].elements['".$target."'].options.length;
				for(var i=0;i<op_len;i++) {
					top.document.forms['$form'].elements['".$target."'].options[i].selected = false;
				}
				top.document.forms['$form'].elements['".$target."'].options[0].selected = true;
			</script>";

		for($i=0; $i < $cnt; $i++){
			echo "<script type='text/javascript'>
				top.document.forms['$form'].elements['".$target."'].options[".($i+1)."].text = '".$data_array[$i][com_name]."';
				top.document.forms['$form'].elements['".$target."'].options[".($i+1)."].value = '".$data_array[$i][relation_code]."';
				</script>";
		}

		echo "<script type='text/javascript'>
				top.document.forms['$form'].elements['cid2'].value = '".$cid."';
				top.document.forms['$form'].elements['depth'].value = '".$depth."';
			</script>";
			exit;

	}else{

		echo "<script type='text/javascript'>
				top.document.forms['$form'].elements['cid2'].value = '".$cid."';
				top.document.forms['$form'].elements['depth'].value = '".$depth."';
				top.document.forms['$form'].elements['".$target."'].length = 1;
				top.document.forms['$form'].elements['".$target."'].setAttribute('validation','false');
			</script>";
		exit;
	}

}

if($mode == 'select_company'){

	if($relation_code == ''){
		return false;
	}else{
		
		if($select_type == 'member'){
			$where = " and cd.com_type not in ('G','S')";
		}

		$sql = "select
			cr.relation_code,
			cd.com_name
		from
			".TBL_COMMON_COMPANY_RELATION." as cr
			inner join ".TBL_COMMON_COMPANY_DETAIL." as cd on (cr.company_id = cd.company_id)
			$where_join
		where
			relation_code like '".$relation_code."%'
			and relation_code != '".$relation_code."'
			and length(relation_code) = '".$target_depth."'
			$where
			order by relation_code ASC";

		$db->query($sql);
		$data_array = $db->fetchall();
		
		for($j=0;$j<count($data_array);$j++){
			
			$access_data[$j][com_name] = $data_array[$j][com_name];
			$access_data[$j][relation_code] = $data_array[$j][relation_code];
		}

		if(count($access_data) > 0){
			
			$datas = $access_data;
			$datas = json_encode($datas);
			$datas = str_replace("\"true\"","true",$datas);
			$datas = str_replace("\"false\"","false",$datas);
			echo $datas;
			exit;
		}

	}
}

if($mode == 'select_department'){

	if($group_ix == ''){
		return false;
	}else{
		
		if($select_type == 'member'){
			$where = " and cd.com_type not in ('G','S')";
		}

		$sql = "select 
				*
				from 
					shop_company_department 
				where
					group_ix = '".$group_ix."'
					and disp = '1'
					order by seq ASC";

		$db->query($sql);
		$data_array = $db->fetchall();
		
		for($j=0;$j<count($data_array);$j++){
			
			$access_data[$j][dp_name] = $data_array[$j][dp_name];
			$access_data[$j][dp_ix] = $data_array[$j][dp_ix];
		}

		if(count($access_data) > 0){
			
			$datas = $access_data;
			$datas = json_encode($datas);
			$datas = str_replace("\"true\"","true",$datas);
			$datas = str_replace("\"false\"","false",$datas);
			echo $datas;
			exit;
		}

	}
}

?>