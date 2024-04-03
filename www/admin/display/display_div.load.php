<?

///////////////////////////////////////////////////////////////////
//
// 제목 : 전시관리 > 분류선택 동적 select box  : 이현우(2013-05-10)
//
///////////////////////////////////////////////////////////////////

include("../../class/database.class");
$div_ix = $_GET['div_ix'];
$target = $_GET['target'];
$form = $_GET['form'];

$db = new Database;

$sql = 	"SELECT *
		FROM ".TBL_SHOP_DISPLAY_DIV."	where parent_div_ix = '$div_ix' ";
$db->query($sql);
 
if(!$db->total){
	// 2차분류 div_ix 가 넘어왔다면 다시 대분류를 구함
	$sql = 	"SELECT *
			FROM ".TBL_SHOP_DISPLAY_DIV."	where div_ix = '$div_ix' ";
	$db->query($sql);
	$db->fetch(0);
	$parent_ix = $db->dt[parent_div_ix];
	$depth = $db->dt[depth];
/*
	if (!$parent_ix){
		echo "<script type='text/javascript'>
			parent.document.forms['$form'].elements['".$target."'].length = 1;
		</script>";
		exit;				
	}
*/
	if ($depth==0){	// 현재 넘어온 div_ix 는 1차분류임

	}else if ($db->dt[div_ix]==0){			
		$sql = 	"SELECT * FROM ".TBL_SHOP_DISPLAY_DIV."	where parent_div_ix = '$parent_ix' ";
		$db->query($sql);
	}
	
	 
}else{	
		// 넘어온 div_ix 에 대한 2차분류들이 존재
		/*
		echo "<script type='text/javascript'>
			parent.document.forms['$form'].elements['".$target."'].length = 1;
		</script>";
		exit;				
		*/
}

if ($db->total){
		echo "<script type='text/javascript'>
			parent.document.forms['$form'].elements['".$target."'].length = ".($db->total+1).";			
			parent.document.forms['$form'].elements['".$target."'].options[0].selected = true;
		</script>\n";

        for($i=0; $i < $db->total; $i++){
                $db->fetch($i);
				echo "<script type='text/javascript'>
					top.document.forms['$form'].elements['".$target."'].options[".($i+1)."] = new Option('".$db->dt[div_name]."', '".$db->dt[div_ix]."');
				</script>";
        }

		// 등록된 2차분류 selected 처리하기 (div_ix 는 2차분류의 div_ix 가 파라미터로 넘어와야함)
		if ($parent_ix){ // 2차분류임을 확인해주는 체크
			for($i=0; $i < $db->total; $i++){
					$db->fetch($i);
					//alert($div_ix,"x");
					echo "<script type='text/javascript'>			
						if (top.document.forms['$form'].elements['".$target."'].options[".($i+1)."].value == ".$div_ix."){
							top.document.forms['$form'].elements['".$target."'].selectedIndex = ".($i+1).";			
						}
					</script>";
			}
		}
		exit;

}else{
		echo "<script type='text/javascript'>
			parent.document.forms['$form'].elements['".$target."'].length = 1;
		</script>";
		exit;
}
?>