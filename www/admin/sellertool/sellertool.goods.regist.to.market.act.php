<?
include_once("sellertool.lib.php");
include_once("../openapi/openapi.lib.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/include/global_util.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/include/lib.function.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/include/admin.util.php");


if($_POST['act']){
	$act         = $_POST['act'];
}
if($_POST['site_code']){
	$site_code   = $_POST['site_code'];
}
if($_POST['update_type']){
	$update_type = $_POST['update_type'];
}
$list = NULL;
//test area
//$stte_code ="11st";
//$act = 'regist';
//test area end
//print_r($_POST);

$db = new Database;

$sql = "select company_id from common_company_detail  where com_type = 'A'  ";
$db->query($sql);
$db->fetch();
if(!$_SESSION['admininfo']){
	$admininfo[company_id] = $db->dt[company_id];
}


$sql = "select mall_data_root, mall_type ,mall_domain from shop_shopinfo  where mall_div = 'B'  ";
$db->query($sql);
$db->fetch();
if(!$_SESSION['admininfo']){
	$admininfo[mall_data_root] = $db->dt[mall_data_root];
	$admininfo[admin_level] = 9;
	$admininfo[language] = 'korea';
	$admininfo[mall_type] = $db->dt[mall_type];
}

if(!$_SESSION['admin_config']){
	$_SESSION["admin_config"]["mall_data_root"] = $db->dt[mall_data_root];
	$_SESSION["admin_config"]["mall_domain"] = $db->dt[mall_domain];
}
//검색된상품 전체일때 아래루틴 적용해서 list얻는다.
//TODO:라이브러리쪽으로이동필요한데 몇가지 에러나서 이동못했음.
if($update_type == 1){
    if($search_searialize_value){
        //	echo $search_searialize_value;
        $unserialize_search_value = unserialize(urldecode($search_searialize_value));
        //print_r ($unserialize_search_value);
        //exit;
        extract($unserialize_search_value);

    	switch ($depth){
    		case 0:
    			$cut_num = 3;
    			break;
    		case 1:
    			$cut_num = 6;
    			break;
    		case 2:
    			$cut_num = 9;
    			break;
    		case 3:
    			$cut_num = 9;
    			break;
    	}
    	if($admininfo[admin_level] == 9){
    		$where = "where p.id Is NOT NULL and p.id = r.pid and r.basic = 1 AND p.product_type NOT IN (".implode(',',$sns_product_type).")  ";
    	}else{
    		$where = "where p.id Is NOT NULL and p.id = r.pid and r.basic = 1 AND p.product_type NOT IN (".implode(',',$sns_product_type).") and admin ='".$admininfo[company_id]."'  ";
    	}

    	if($pid != ""){
    		$where = $where."and p.id = '$pid' ";
    	}
    	if($company_id != ""){
    		$where = $where."and p.admin = '".$company_id."' ";

    	}
		
		if($mult_search_use == '1'){	//다중검색 체크시 (검색어 다중검색)
			//다중검색 시작 2014-04-10 이학봉
			if($search_text != ""){
				if(strpos($search_text,",") !== false){
					$search_array = explode(",",$search_text);
					$search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
					$where .= "and ( ";
					for($i=0;$i<count($search_array);$i++){
						$search_array[$i] = trim($search_array[$i]);
						if($search_array[$i]){
							if($i == count($search_array) - 1){
								$where .= $search_type." = '".trim($search_array[$i])."'";
							}else{
								$where .= $search_type." = '".trim($search_array[$i])."' or ";
							}
						}
					}
					$where .= ")";
				}else if(strpos($search_text,"\n") !== false){//\n
					$search_array = explode("\n",$search_text);
					$search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
					$where .= "and ( ";

					for($i=0;$i<count($search_array);$i++){
						$search_array[$i] = trim($search_array[$i]);
						if($search_array[$i]){
							if($i == count($search_array) - 1){
								$where .= $search_type." = '".trim($search_array[$i])."'";
							}else{
								$where .= $search_type." = '".trim($search_array[$i])."' or ";
							}
						}
					}
					$where .= ")";
				}else{
					$where .= " and ".$search_type." = '".trim($search_text)."'";
				}
			}

		}else{	//검색어 단일검색
			if($search_text != ""){
				if($search_type == "sellprice"){
					$where = $where."and ".$search_type." = '".trim($search_text)."' ";
				}else{
					$where = $where."and ".$search_type." LIKE '%".trim($search_text)."%' ";
				}
			}else{
				if($search_type == "bimg" && $search_text == ""){
					$where .= "and ".$search_type." = '' ";
				}
			}
		}

    	if($disp != ""){
    		$where .= " and p.disp = ".$disp;
    	}

    	if($bs_site != ""){
    		$where .= " and p.bs_site = '".$bs_site."'";
    	}

    	if($currency_ix != ""){
    		$where .= " and p.currency_ix = '".$currency_ix."'";
    	}

    	if($state2 != ""){
    		$where = $where." and p.state = ".$state2." ";
    	}

    	if($brand2 != ""){
    		$where .= " and brand = ".$brand2."";
    	}

    	if($brand_name != ""){
    		$where .= " and p.brand_name LIKE '%".trim($brand_name)."%' ";
    	}

        $startDate = $FromYY.$FromMM.$FromDD;
    	$endDate = $ToYY.$ToMM.$ToDD;

    	if($startDate != "" && $endDate != ""){
    		$where .= " and  date_format(p.regdate,'%Y%m%d') between  $startDate and $endDate ";
    	}

    	if($cid2 != ""){
    		$where .= " and r.cid LIKE '".substr($cid2,0,$cut_num)."%'";
    	}else{
    		$where .= "";
    	}

        $db = new Database;
        $sql = "select id from ".TBL_SHOP_PRODUCT." p right join ".TBL_SHOP_PRODUCT_RELATION." r on p.id = r.pid and r.basic = 1  $where  ";

    	$db->query($sql);
        $list = $db->fetchAll();

    }
}

if($act == 'cron_regist'){
	//echo "aaa";
	if(!is_object($OAL)){
		$OAL = new OpenAPI($site_code);
	}

	//echo $a."<br>pid : ".$pid."  site_code : ".$site_code."<br>" ;
	$result = $OAL->lib->registGoods($pid,$addinfo);
	//print_r($result);
	//echo "<br>";
    //exit;
}



if($act == 'regist'){
	
	set_time_limit(9999999);

    $result_msg = "";
    $result_code = "";
	$t_count = 0;
	$s_count = 0;
	$f_count = 0;

    $OAL = new OpenAPI($site_code);
	$OAL->lib->set_error_type("return");

    if($list == NULL){
        $list = $_POST['select_pid'];
    }
	
	//echo("<script src='/admin/js/jquery-1.8.3.js'></script>");

    $addinfo = $_POST['add_info'];

    if(is_array($list)){

        foreach($list as $lt):
            if($update_type == '1'){
                $pid = $lt[id];
            }else{
                $pid = $lt;
            }

			//echo $pid."<br>" ;
           if($work_type == 'stock'){
			$result = $OAL->lib->modifyStock($pid);
		   }else{
		   $result = $OAL->lib->registGoods($pid,$addinfo);
		   }
            $result_msg .= " [".$result->message."] ";
            $result_code .= " [".$result->resultCode."] ";

			$t_count++;

			if($result->resultCode=="success" || $result->resultCode=="200"){
				$s_count++;
			}else{
				$f_count++;
			}

			//echo("<script>$('#select_update_loadingbar td',parent.document).html('<img src=\"/admin/images/indicator.gif\" border=\"0\" width=\"32\" height=\"32\" align=\"absmiddle\"> ".$result->message."');</script>");

        endforeach;
    }

	echo("<script language='javascript' src='../js/message.js.php'></script><script>alert('총 $t_count 개 상품중 $s_count 개의 등록완료, $f_count 개의 등록실패 되었습니다.');</script>");
    echo("<script>if(parent.document.getElementById('act').src != 'about:blank'){parent.select_update_unloading();parent.location.reload();}else{parent.location.reload();}</script>");

    exit;
}

?>
