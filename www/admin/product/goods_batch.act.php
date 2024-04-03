<?
include("../class/layout.class");
require $_SERVER["DOCUMENT_ROOT"].'/class/sphinxfb.class';


if($admininfo[company_id] == ""){
	//echo "<script language='javascript' src='../_language/language.php'></script><script language='javascript'>alert(language_data['common']['C'][language]);location.href='/admin/admin.php'</script>";
	//'관리자 로그인후 사용하실수 있습니다.'
	//exit;
}

if($search_searialize_value){
	$unserialize_search_value = unserialize(urldecode($search_searialize_value));
	extract($unserialize_search_value);
}

$db = new Database;
$db2 = new Database;

//search 조건	시작
if($admininfo[admin_level] == 9){	//시스템 관리자일경우
	$where .= "";
}else{								//셀러업체일경우
	$where .= "and admin ='".$admininfo[company_id]."' ";
}
//상품승인대기 리스트에서 넘오왓을경우 검색한 상품 수정시 상품승인대기 중인 상품에 대한 조건 추가 시작2014-09-16 이학봉
if($page_type =="update_state"){
	if($info == "state_waite" || $info == ""){
		$where .=" and p.state = '6'";
	}else if($info == 'state_cancel'){
		$where .=" and p.state = '8'";
	}
}
//상품승인대기 리스트에서 넘오왓을경우 검색한 상품 수정시 상품승인대기 중인 상품에 대한 조건 추가 끝2014-09-16 이학봉


include ('./product_query_search.php');
//search 조건	끝

/*선택한 회원이나 , 전체 검색한 회원은 같기에 위에서 한번만 선언한다. 2014-04-16 이학봉*/
if($update_type == '1'){	//검색한 회원
	$sql = "SELECT 
				HIGH_PRIORITY p.id
			FROM 
				".TBL_SHOP_PRODUCT." p
				left join ".TBL_SHOP_PRODUCT_RELATION."  r on (p.id = r.pid and r.basic = '1')
				inner join ".TBL_COMMON_COMPANY_DETAIL."  ccd on (p.admin = ccd.company_id)
				left join ".TBL_COMMON_SELLER_DETAIL." csd on (ccd.company_id = csd.company_id)
			where 
				1
				and p.is_delete = '0'
				".$where."";

	$db->query ($sql);
	$select_pid = $db->fetchall();
    $search_act_real_total = $db->total;

    /* 검색한 상품 전체 수정시 전체 수량 비교 170718 */
    if ($update_type == "1" && $search_act_total != $search_act_real_total)    {
        echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('검색한 상품 일괄변경 중 오류가 발생했습니다.');</script>");
        echo("<script>if(parent.document.getElementById('act').src != 'about:blank'){parent.select_update_unloading();parent.location.reload();}else{parent.location.reload();}</script>");
        exit;
    }

	for($i=0;$i<count($select_pid);$i++){
		$select_pid[$i] = $select_pid[$i][id];
	}
}else if($update_type == '2'){	//선택한 회원
	$select_pid = $select_pid;
}


if($update_kind == "shopping_all"){
	
	if($disp_naver){
		$reserve_yn_set = ", p.disp_naver = 1 ";
	}else{
		$reserve_yn_set = ", p.disp_naver = 0 ";
	}

	if($disp_daum){
		$reserve_str = ", p.disp_daum = 1 ";
	}else{
		$reserve_str = ", p.disp_daum = 0 ";
	}
	

	if($update_type == 2){// 선택회원일때

		for($i=0;$i<count($select_pid);$i++){//disp='".$_POST["disp".$select_pid[$i]]."',
			if($admininfo[admin_level] == 9){
				$sql = "update ".TBL_SHOP_PRODUCT." p SET  editdate = NOW() $reserve_yn_set  $reserve_str where id = '".$select_pid[$i]."' "; 
				$db->query ($sql);
			}
		}
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택상품의 노출 정보변경이 적상적으로 완료되었습니다.');</script>");
		echo("<script>if(top.document.getElementById('act').src != 'about:blank'){top.select_update_unloading();top.location.reload();}else{top.location.reload();}</script>");

	}else{// 검색회원일때

        if($db->dbms_type == "oracle"){
            $sql = "select * from ".TBL_SHOP_PRODUCT."  where id in (select p.id from ".TBL_SHOP_PRODUCT." p left join ".TBL_SHOP_PRODUCT_RELATION." r on p.id = r.pid $where)";
        }else{
            $sql = "select * from ".TBL_SHOP_PRODUCT." p left join ".TBL_SHOP_PRODUCT_RELATION." r on p.id = r.pid  $where  ";
        }
        $search_act_real_total = $db->total;

        /* 검색한 상품 전체 수정시 전체 수량 비교 170718 */
        if ($update_type == "1" && $search_act_total != $search_act_real_total)    {
            echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('검색한 상품 일괄변경 중 오류가 발생했습니다.');</script>");
            echo("<script>if(parent.document.getElementById('act').src != 'about:blank'){parent.select_update_unloading();parent.location.reload();}else{parent.location.reload();}</script>");
            exit;
        }
        else {
            if($db->dbms_type == "oracle"){
                $sql = "update ".TBL_SHOP_PRODUCT." set  editdate = NOW() $reserve_yn_set $reserve_str
			where id in (select p.id from ".TBL_SHOP_PRODUCT." p left join ".TBL_SHOP_PRODUCT_RELATION." r on p.id = r.pid $where)";
            }else{
                $sql = "update ".TBL_SHOP_PRODUCT." p left join ".TBL_SHOP_PRODUCT_RELATION." r on p.id = r.pid set editdate = NOW() $reserve_yn_set $reserve_str $where  ";
            }
            //echo $sql;
            $db->query($sql);
            echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('전체상품의 노출 정보변경이 적상적으로 완료되었습니다.');</script>");
            echo("<script>if(parent.document.getElementById('act').src != 'about:blank'){parent.select_update_unloading();parent.location.reload();}else{parent.location.reload();}</script>");
        }

	}

}

if($update_kind == "reserve"){

	if($reserve_type == 1){
		if($whole_use == 'R'){//소매
			$reserve_str = ", p.reserve = '".$reserve."' ";
		}else{
			$reserve_str = ", p.wholesale_reserve = '".$reserve."' ";
		}
	}else if($reserve_type == 2){
		if($whole_use == 'R'){//소매
			$reserve_str = ", p.reserve = p.reserve - ".$reserve." ";
		}else{
			$reserve_str = ", p.wholesale_reserve = p.wholesale_reserve - ".$reserve." ";
		}
	}else if($reserve_type == 3){
		if($whole_use == 'R'){//소매
			$reserve_str = ", p.reserve = p.reserve + ".$reserve." ";
		}else{
			$reserve_str = ", p.wholesale_reserve = p.wholesale_reserve + ".$reserve." ";
		}
	}

	if($whole_use == 'R'){//소매
		$reserve_yn_set = ", reserve_yn = '".$reserve_yn."' ";
	}else{
		$reserve_yn_set = ", wholesale_reserve_yn = '".$reserve_yn."' ";
	}

	if($update_type == 2){// 선택회원일때

		for($i=0;$i<count($select_pid);$i++){//disp='".$_POST["disp".$select_pid[$i]]."',
			if($admininfo[admin_level] == 9){
				$sql = "update ".TBL_SHOP_PRODUCT." p SET  editdate = NOW() $reserve_yn_set  $reserve_str where id = '".$select_pid[$i]."' "; 
				$db->query ($sql);
			}
		}
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택상품의 적립금 정보변경이 적상적으로 완료되었습니다.');</script>");
		echo("<script>if(top.document.getElementById('act').src != 'about:blank'){top.select_update_unloading();top.location.reload();}else{top.location.reload();}</script>");

	}else{// 검색회원일때

        if($db->dbms_type == "oracle"){
            $sql = "select * from ".TBL_SHOP_PRODUCT." where id in (select p.id from ".TBL_SHOP_PRODUCT." p left join ".TBL_SHOP_PRODUCT_RELATION." r on p.id = r.pid $where)";
        }else{
            $sql = "select * from ".TBL_SHOP_PRODUCT." p left join ".TBL_SHOP_PRODUCT_RELATION." r on p.id = r.pid $where  ";
        }
        $db->query($sql);
        $search_act_real_total = $db->total;

        /* 검색한 상품 전체 수정시 전체 수량 비교 170718 */
        if ($update_type == "1" && $search_act_total != $search_act_real_total) {
            echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('검색한 상품 일괄변경 중 오류가 발생했습니다.');</script>");
            echo("<script>if(parent.document.getElementById('act').src != 'about:blank'){parent.select_update_unloading();parent.location.reload();}else{parent.location.reload();}</script>");
            exit;
        }
        else {
            if($db->dbms_type == "oracle"){
                $sql = "update ".TBL_SHOP_PRODUCT." set  editdate = NOW() $reserve_yn_set $reserve_str
			    where id in (select p.id from ".TBL_SHOP_PRODUCT." p left join ".TBL_SHOP_PRODUCT_RELATION." r on p.id = r.pid $where)";
            }else{
                $sql = "update ".TBL_SHOP_PRODUCT." p left join ".TBL_SHOP_PRODUCT_RELATION." r on p.id = r.pid set editdate = NOW() $reserve_yn_set $reserve_str $where  ";
            }
            //echo $sql;
            $db->query($sql);
            echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('전체상품의 적립금 정보변경이 적상적으로 완료되었습니다.');</script>");
            //parent.document.location.reload();
            //echo("<script>if(parent.document.frames['act'].location != 'about:blank'){parent.select_update_unloading();parent.document.frames['act'].location.reload();}else{parent.document.location.reload();}</script>");//kbk
            echo("<script>if(parent.document.getElementById('act').src != 'about:blank'){parent.select_update_unloading();parent.location.reload();}else{parent.location.reload();}</script>");
        }
	}

}


if($update_kind == "product_delete"){	//2014-01-07 이학봉 상품일괄삭제 기능 추가

	if($product_del == '0'){	//상품삭제 일경우에만 삭제 

		for($i=0;$i<count($select_pid);$i++){
			//1.상품 관련 삭제
			//2.장바구니 삭제
			//3.찜목록 삭제
			//4.상품이미지 삭제

			$id = $select_pid[$i];	//상품아이디

			$db->query("update ".TBL_SHOP_PRODUCT." p SET  editdate = NOW(), is_delete='1' , state='2' , disp='0'  WHERE id='".$id."'");

			/*
			$uploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product", $id, 'Y');
			if ($id && file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/")){
				rmdirr($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/");
			}

			$db->query("DELETE FROM ".TBL_SHOP_PRODUCT." WHERE id='".$id."'");
			$db->query("DELETE FROM ".TBL_SHOP_PRICEINFO." WHERE pid='".$id."'");
			$db->query("DELETE FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." WHERE pid='".$id."'");
			$db->query("DELETE FROM ".TBL_SHOP_PRODUCT_RELATION." WHERE pid='".$id."'");
			$db->query("DELETE FROM shop_product_buyingservice_priceinfo WHERE pid='".$id."'");
			$db->query("DELETE FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE pid='".$id."'");
			$db->query("DELETE FROM ".TBL_SHOP_PRODUCT_DISPLAYINFO." WHERE pid='".$id."'");
			$db->query("DELETE FROM ".TBL_SHOP_CART." WHERE id='$id'");

			if($id && is_dir("$DOCUMENT_ROOT".$admin_config[mall_data_root]."/images/product_detail/$id")){
				rmdirr("$DOCUMENT_ROOT".$admin_config[mall_data_root]."/images/product_detail/$id");
			}

			$adduploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg", $id, 'Y');
			if ($id && is_dir($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/")){
				rmdirr($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/");
			}
			*/
		}

		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택상품을 정상적으로 삭제 했습니다.');</script>");
		echo("<script>if(top.document.getElementById('act').src != 'about:blank'){top.select_update_unloading();top.location.reload();}else{top.location.reload();}</script>");
	}else{

		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('상품 삭제 안함을 선택하였습니다.');</script>");
		echo("<script>if(top.document.getElementById('act').src != 'about:blank'){top.select_update_unloading();top.location.reload();}else{top.location.reload();}</script>");
	
	}

}


if ($update_kind == "priod_date"){		//판매기간 일괄수정

	if(!$sell_priod_sdate && !$sell_priod_edate && $btach_is_sell_date == '1'){
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('변경하시고자 하는 판매기간을 선택해주세요');</script>");
		echo("<script>if(parent.document.getElementById('act').src != 'about:blank'){parent.select_update_unloading();parent.location.reload();}else{parent.location.reload();}</script>");
		exit;
	}

	$sell_priod_sdate = $sell_priod_sdate." ".$sell_priod_sdate_h.":".$sell_priod_sdate_i.":".$sell_priod_sdate_s;
	$sell_priod_edate = $sell_priod_edate." ".$sell_priod_edate_h.":".$sell_priod_edate_i.":".$sell_priod_edate_s;

	for($i=0;$i<count($select_pid);$i++){

		$sql = "update ".TBL_SHOP_PRODUCT." p set 
					p.is_sell_date = '".$btach_is_sell_date."',";

		if($btach_is_sell_date == '1'){
			$sql .= "p.sell_priod_sdate = '".$sell_priod_sdate."',";
			$sql .= "p.sell_priod_edate = '".$sell_priod_edate."',";
		}

		$sql .= "	editdate = NOW()
				where 
					id = '".$select_pid[$i]."' "; 
		//echo nl2br($sql)."<br><br>";
		$db->query ($sql);
		
	}

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택상품의 판매/진열 상태 정보변경이 적상적으로 완료되었습니다.');</script>");
	echo("<script>if(top.document.getElementById('act').src != 'about:blank'){top.select_update_unloading();top.location.reload();}else{top.location.reload();}</script>");
	exit;
}


if ($update_kind == "disp"){		//노출여부 일괄수정

	for($i=0;$i<count($select_pid);$i++){

		$sql = "select disp from ".TBL_SHOP_PRODUCT." where id='".$select_pid[$i]."' ";
		$db->query($sql);
		if($db->total){
			$db->fetch();
			$b_disp = $db->dt['disp'];
			
			$historyBool = true;
		}else{
			$historyBool = false;
		}

		$sql = "UPDATE ".TBL_SHOP_PRODUCT." p SET p.disp = ".$c_disp.", editdate = NOW() Where id = '".$select_pid[$i]."' ";
		$db->query ($sql);

		if($historyBool){
			if( $b_disp != $c_disp ){
				product_edit_history_insert($select_pid[$i],'disp', '노출여부', $b_disp ,$c_disp ,$_SESSION[admininfo][charger_ix],$_SESSION[admininfo][charger]);
			}
		}
	}
	
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택상품의 판매/진열 상태 정보변경이 적상적으로 완료되었습니다.');</script>");
	echo("<script>if(top.document.getElementById('act').src != 'about:blank'){top.select_update_unloading();top.location.reload();}else{top.location.reload();}</script>");
	exit;
}

if ($update_kind == "category"){	//카테고리 일괄 업데이트 새로 변경 2014-04-16 이학봉

	if($basic == ""){
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('변경하시고자 하는 카테고리를 선택해주세요');</script>");
		echo("<script>if(parent.document.getElementById('act').src != 'about:blank'){parent.select_update_unloading();parent.location.reload();}else{parent.location.reload();}</script>");
		exit;
	}

	if($category_change_type == "1"){ // 카테고리 추가일때
			
			//카테고리 추가만 셀러,본사 전부 가능하게끔 처리 2014-07-17 이학봉
			for($i=0;$i<count($select_pid);$i++){
			
				$sql = "select * from ".TBL_SHOP_PRODUCT_RELATION." Where pid = '".$select_pid[$i]."' and basic = '1'"; 
				$db->query ($sql);
		
				for($j=0;$j<count($display_category);$j++){	//다중카테고리 추가
					
					if($display_category[$j] == $basic){	//기본카테고리 체크유무
						if($db->total > 0){
							$basic_category = 0;
						}else{
							$basic_category = 1;
						}
					}else{
						$basic_category = 0;
					}
					
					//$sql = "select * from ".TBL_SHOP_PRODUCT_RELATION." Where pid = '".$select_pid[$i]."' and cid = '".$display_category[$j]."'";
					$db->query("select * from ".TBL_SHOP_PRODUCT_RELATION." Where pid = '".$select_pid[$i]."' and cid = '".$display_category[$j]."'");
					if($db->total > 0){
						continue;
					}else{
						$sql = "insert into ".TBL_SHOP_PRODUCT_RELATION." (cid,pid,disp,basic,insert_yn,regdate) values('".$display_category[$j]."','".$select_pid[$i]."','1','".$basic_category."','Y',NOW())";
						$db->sequences = "SHOP_GOODS_LINK_SEQ";
						$db->query ($sql);
					}
					
					/*
					$sql = "REPLACE into ".TBL_SHOP_PRODUCT_RELATION." (rid,cid,pid,disp,basic,insert_yn,regdate)
							select
								case when (r.cid = '".$display_category[$j]."') then r.rid else '' end rid, 
								'".$display_category[$j]."' as cid,
								p.id as pid, 
								'1' as disp,
								IF((r.cid = '".$display_category[$j]."' and r.basic = '1') ,'1','0')  as basic,
								'Y' as insert_yn,
								IFNULL(r.regdate,NOW())
							from
								".TBL_SHOP_PRODUCT." p
								left join  ".TBL_SHOP_PRODUCT_RELATION." r on p.id = r.pid
							where
								1
								and p.id = '".$select_pid[$i]."'
					";
					*/
				}

				$sql = "update ".TBL_SHOP_PRODUCT." set reg_category = 'Y', editdate = NOW() where id = '".$select_pid[$i]."'";
				$db->query ($sql);
			}
		

	}else if($category_change_type == "2"){ // 카테고리 변경일때(없으면 추가하기)(기존카테고리 삭제후 새로 추가 2014-04-16 이학봉)
		if($admininfo[admin_level] == 9){
			for($i=0;$i<count($select_pid);$i++){
				// 해당 상품의 존재여부 판단

				$sql = "delete from ".TBL_SHOP_PRODUCT_RELATION." where pid = '".$select_pid[$i]."'";
				$db->query ($sql);

				for($j=0;$j<count($display_category);$j++){	//다중카테고리 추가
					if($display_category[$j] == $basic){	
						$basic_category = '1';
					}else{
						$basic_category = '0';
					}

					$sql = "insert into ".TBL_SHOP_PRODUCT_RELATION." (rid, cid,pid,disp,basic,insert_yn,regdate) values('','".$display_category[$j]."','".$select_pid[$i]."','1','$basic_category','Y',NOW())";
					$db->sequences = "SHOP_GOODS_LINK_SEQ";
					$db->query ($sql);
				}

				$sql = "update ".TBL_SHOP_PRODUCT." set reg_category = 'Y', editdate = NOW() where id = '".$select_pid[$i]."'";
				$db->query ($sql);
			}
		}

	}else if($category_change_type == "3"){ // 기본 카테고리 변경일때 (기존 카테고리 전부삭제후 기본카테고리 하나만 추가)

		if($admininfo[admin_level] == 9){
			for($i=0;$i<count($select_pid);$i++){//disp='".$_POST["disp".$select_pid[$i]]."',

				$sql = "delete from ".TBL_SHOP_PRODUCT_RELATION." where pid = '".$select_pid[$i]."'";
				$db->query ($sql);

				$basic_category = '1';

				$sql = "insert into ".TBL_SHOP_PRODUCT_RELATION." (rid, cid,pid,disp,basic,insert_yn,regdate) values('','".$basic."','".$select_pid[$i]."','1','$basic_category','Y',NOW())";
				$db->sequences = "SHOP_GOODS_LINK_SEQ";
				$db->query ($sql);

				$sql = "update ".TBL_SHOP_PRODUCT." set reg_category = 'Y', editdate = NOW() where id = '".$select_pid[$i]."'";
				$db->query ($sql);
			}
		}
	}

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택상품의 카테고리 정보변경이 적상적으로 완료되었습니다.');</script>");
	echo("<script>if(top.document.getElementById('act').src != 'about:blank'){top.select_update_unloading();top.location.reload();}else{top.location.reload();}</script>");
	exit;
	exit;
	
}



if ($update_kind == "standard_category"){	//카테고리 일괄 업데이트 새로 변경 2014-04-16 이학봉

	if($basic == ""){
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('변경하시고자 하는 카테고리를 선택해주세요');</script>");
		echo("<script>if(parent.document.getElementById('act').src != 'about:blank'){parent.select_update_unloading();parent.location.reload();}else{parent.location.reload();}</script>");
		exit;
	}
//$db->debug = true;
	if($standard_category_change_type == "1"){ // 카테고리 추가일때
			//print_r($_POST);
			//카테고리 추가만 셀러,본사 전부 가능하게끔 처리 2014-07-17 이학봉
			for($i=0;$i<count($select_pid);$i++){
			
				$sql = "select * from shop_product_standard_relation Where pid = '".$select_pid[$i]."' and basic = '1'"; 
				$db->query ($sql);
		
				for($j=0;$j<count($display_standard_category);$j++){	//다중카테고리 추가
					
					if($display_standard_category[$j] == $basic){	//기본카테고리 체크유무
						if($db->total > 0){
							$basic_category = 0;
						}else{
							$basic_category = 1;
						}
					}else{
						$basic_category = 0;
					}
					
					//$sql = "select * from shop_product_standard_relation Where pid = '".$select_pid[$i]."' and cid = '".$display_standard_category[$j]."'";
					$db->query("select * from shop_product_standard_relation Where pid = '".$select_pid[$i]."' and cid = '".$display_standard_category[$j]."'");
					if($db->total > 0){
						continue;
					}else{
						$sql = "insert into shop_product_standard_relation (cid,pid,disp,basic,insert_yn,regdate) values('".$display_standard_category[$j]."','".$select_pid[$i]."','1','".$basic_category."','Y',NOW())";
						$db->sequences = "SHOP_GOODS_LINK_SEQ";
						$db->query ($sql);
					}
			 
				}

				$sql = "update ".TBL_SHOP_PRODUCT." set reg_standard_category = 'Y', editdate = NOW() where id = '".$select_pid[$i]."'";
				$db->query ($sql);
			}
		

	}else if($standard_category_change_type == "2"){ // 카테고리 변경일때(없으면 추가하기)(기존카테고리 삭제후 새로 추가 2014-04-16 이학봉)
		if($admininfo[admin_level] == 9){
			for($i=0;$i<count($select_pid);$i++){
				// 해당 상품의 존재여부 판단

				$sql = "delete from shop_product_standard_relation where pid = '".$select_pid[$i]."'";
				$db->query ($sql);

				for($j=0;$j<count($display_standard_category);$j++){	//다중카테고리 추가
					if($display_standard_category[$j] == $basic){	
						$basic_category = '1';
					}else{
						$basic_category = '0';
					}

					$sql = "insert into shop_product_standard_relation (psr_ix, cid,pid,disp,basic,insert_yn,regdate) values('','".$display_standard_category[$j]."','".$select_pid[$i]."','1','$basic_category','Y',NOW())";
					$db->sequences = "SHOP_GOODS_LINK_SEQ";
					$db->query ($sql);
				}

				$sql = "update ".TBL_SHOP_PRODUCT." set reg_standard_category = 'Y', editdate = NOW() where id = '".$select_pid[$i]."'";
				$db->query ($sql);
			}
		}

	}else if($standard_category_change_type == "3"){ // 기본 카테고리 변경일때 (기존 카테고리 전부삭제후 기본카테고리 하나만 추가)

		if($admininfo[admin_level] == 9){
			for($i=0;$i<count($select_pid);$i++){//disp='".$_POST["disp".$select_pid[$i]]."',

				$sql = "delete from shop_product_standard_relation where pid = '".$select_pid[$i]."'";
				$db->query ($sql);

				$basic_category = '1';

				$sql = "insert into shop_product_standard_relation (psr_ix, cid,pid,disp,basic,insert_yn,regdate) values('','".$basic."','".$select_pid[$i]."','1','$basic_category','Y',NOW())";
				$db->sequences = "SHOP_GOODS_LINK_SEQ";
				$db->query ($sql);

				$sql = "update ".TBL_SHOP_PRODUCT." set reg_standard_category = 'Y', editdate = NOW() where id = '".$select_pid[$i]."'";
				$db->query ($sql);
			}
		}
	}
	//exit;
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택상품의 카테고리 정보변경이 적상적으로 완료되었습니다.');</script>");
	echo("<script>if(top.document.getElementById('act').src != 'about:blank'){top.select_update_unloading();top.location.reload();}else{top.location.reload();}</script>");
	exit;

	
}

if ($update_kind == "update_brand"){		//브랜드,원산지,제조사 일괄수정	셀러수정가능 2014-06-30

	if($brand_check == "" && $ori_check == "" && $company_check == ""){

		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('변경하시고자 하는 브랜드,원산지,제조사를 선택해주세요');</script>");
		echo("<script>parent.select_update_unloading();parent.location.reload();</script>");
		exit;
	}
	if($brand_check == '1'){
	$batch_b_ix = $batch_b_ix;	//브랜드코드
	$batch_brand_name = $batch_brand_name;	//브랜드명
		$update_set .= " p.brand = '".$batch_b_ix."', p.brand_name = '".$batch_brand_name."', ";
	}
	
	if($ori_check == '1'){
		$og_ix = $og_ix;	//원산지 코드
		$origin = $origin;	//원산지명
		$update_set .= " p.origin = '".$origin."', ";
	}

	if($company_check == '1'){
		$update_set .= " p.c_ix = '".$c_ix."', p.company = '".$company."', ";
	}
	for($i=0;$i<count($select_pid);$i++){
		//if($admininfo[admin_level] == 9){
			$sql = "UPDATE ".TBL_SHOP_PRODUCT." p SET 
						$update_set
						editdate = NOW() 
					Where
						id = '".$select_pid[$i]."' ";
			$db->query ($sql);
		//}
	}
	
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택상품의 브랜드,원산지,제조사 정보변경이 적상적으로 완료되었습니다.');</script>");
	echo("<script>if(top.document.getElementById('act').src != 'about:blank'){top.select_update_unloading();top.location.reload();}else{top.location.reload();}</script>");
	exit;
}

if ($update_kind == "update_product_point"){
	//print_r($_POST);
	for($i=0;$i<count($select_pid);$i++){
		insertProductPoint($state,$use_state,$oid,$od_ix,$point,$select_pid[$i],$etc,$admininfo); 
	}
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('상품레벨점수 일괄지급이 정상적으로 완료되었습니다.');</script>");
	echo("<script>if(top.document.getElementById('act').src != 'about:blank'){top.select_update_unloading();top.location.reload();}else{top.location.reload();}</script>");
	exit;
}

if ($update_kind == "delivery_policy"){		//배송정책 일괄수정	셀러수정가능
		
		if( empty($company_id) ){
			$company_id = $_POST['company_id'];
		}
		
		if($batch_delivery_type == '1'){	//통합배송일경우 본사 정책
			$sql = "select company_id from common_company_detail where com_type = 'A' limit 0,1";
			$db->query($sql);
			$db->fetch();
			$company_id = $db->dt[company_id];
		}

		for($i=0;$i<count($select_pid);$i++){
			//if($admininfo[admin_level] == 9){
				//배송템플릿 저장 시작 2014-05-13 이학봉
				Insert_product_delivery($dt_ix,$company_id,$select_pid[$i],$batch_delivery_policy);
				//배송템플릿 저장 끝 2014-05-13 이학봉
			//}
			
			if($batch_delivery_type == "1" && $admininfo[admin_level] == '9'){	//배송타입 변경 관리자만 가능 2014-07-23 이학봉
				$sql = "update shop_product set delivery_type = '".$batch_delivery_type."' where id='".$select_pid[$i]."'";
				$db->query($sql);
			}

		}

		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택상품의 배송정책 정보변경이 적상적으로 완료되었습니다.');</script>");
		echo("<script>if(top.document.getElementById('act').src != 'about:blank'){top.select_update_unloading();top.location.reload();}else{top.location.reload();}</script>");
		exit;
	
}


if ($update_kind == "update_seller"){		//셀러/매입처 일괄수정

	if($seller_check == '' && $trade_check ==""){
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('변경하시고자 하는 셀러/매입처를 선택해주세요');</script>");
		echo("<script>parent.select_update_unloading();parent.location.reload();</script>");
		exit;
	}

	if($seller_check == '1'){	//셀러
		$update_set .= " p.admin = '".$batch_company_id."', ";
	}
	
	if($trade_check == '1'){
		$update_set .= " p.trade_admin = '".$trade_admin."', ";
	}

	for($i=0;$i<count($select_pid);$i++){
		if($admininfo[admin_level] == 9){
			$sql = "UPDATE ".TBL_SHOP_PRODUCT." p SET
						$update_set
						editdate = NOW()
					Where
						id = '".$select_pid[$i]."' ";
			$db->query ($sql);
		}
	}
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택상품의 배송정책 정보변경이 적상적으로 완료되었습니다.');</script>");
	echo("<script>if(top.document.getElementById('act').src != 'about:blank'){top.select_update_unloading();top.location.reload();}else{top.location.reload();}</script>");
	exit;
}

if($update_kind == "update_icon"){	//아이콘 일괄변경 2014-05-26 이학봉
	
	if(count($icon_check) < 1){
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('변경하시고자 하는 아이콘을 선택해주세요');</script>");
		echo("<script>if(parent.document.getElementById('act').src != 'about:blank'){parent.select_update_unloading();parent.location.reload();}else{parent.location.reload();}</script>");
		exit;
	}

	if($update_icon_type == "1"){ // 기존 보존후 아이콘 추가
		
		if($admininfo[admin_level] == 9){
			$icons = implode(";",$icon_check);

			for($i=0;$i<count($select_pid);$i++){
				$sql = "select * from ".TBL_SHOP_PRODUCT." where id = '".$select_pid[$i]."'";
				$db->query($sql);
				$db->fetch();
				$icon_data = $db->dt[icons];
				if($icon_data){
					$icons_value = $icon_data.";".$icons;
					$icon_array = explode(";",$icons_value);
					$icon_array = array_unique ($icon_array);
					$icons_value = implode(";",$icon_array);
				}else{
					$icons_value = $icons;
				}
				$sql = "update ".TBL_SHOP_PRODUCT." set icons='".$icons_value."' where id = '".$select_pid[$i]."'";
				$db->query ($sql);
				unset($icons_value);
			}
		}

	}else if($update_icon_type == "2"){ //기존 아이콘 삭제후 아이콘 추가
	
		if($admininfo[admin_level] == 9){
			$icons = implode(";",$icon_check);
			for($i=0;$i<count($select_pid);$i++){
				$sql = "update ".TBL_SHOP_PRODUCT." set icons='".$icons."' where id = '".$select_pid[$i]."'";
				$db->query ($sql);
			}
		}
	}else if($update_icon_type == "3"){ //아이콘 전체 미노출

		if($admininfo[admin_level] == 9){
			for($i=0;$i<count($select_pid);$i++){
				$sql = "update ".TBL_SHOP_PRODUCT." set icons='' where id = '".$select_pid[$i]."'";
				$db->query ($sql);
			}
		}
	}

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택상품의 아이콘 정보변경이 적상적으로 완료되었습니다.');</script>");
	echo("<script>if(parent.document.getElementById('act').src != 'about:blank'){parent.select_update_unloading();parent.location.reload();}else{parent.location.reload();}</script>");
	exit;
	
}


if($update_kind == "update_sns"){	//sns 일괄변경 2014-05-26 이학봉

	if(count($sns_btn) < 1){
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('변경하시고자 하는 SNS를 선택해주세요');</script>");
		echo("<script>if(parent.document.getElementById('act').src != 'about:blank'){parent.select_update_unloading();parent.location.reload();}else{parent.location.reload();}</script>");
		exit;
	}

	if($update_sns_type == "1"){ // 기존 보존후 sns 추가
		
		if($admininfo[admin_level] == 9){
			
			for($i=0;$i<count($select_pid);$i++){

				$sql = "select * from ".TBL_SHOP_PRODUCT." where id = '".$select_pid[$i]."'";
				$db->query($sql);
				$db->fetch();
				$ori_sns_btn = $db->dt[sns_btn];		//SNS 공유버튼
				$ori_sns_btn_yn = $db->dt[sns_btn_yn];	//SNS 공유버튼 사용여부

				if($ori_sns_btn){
					$ori_value_array = unserialize($ori_sns_btn);
					$ori_array = array_merge($ori_value_array,$sns_btn);
					$sns_array = array_unique ($ori_array);
					$sns_value = serialize($sns_array);
	
				}else{
					$sns_value = $sns_btn;
				}
				$sql = "update ".TBL_SHOP_PRODUCT." set sns_btn_yn = '".$sns_btn_yn."', sns_btn='".$sns_value."' where id = '".$select_pid[$i]."'";
				$db->query ($sql);

				$sns_value = '';
				$ori_sns_btn = '';
				$ori_sns_btn_yn = '';
			}
		}

	}else if($update_sns_type == "2"){ //기존 sns 삭제후 sns 추가
		if($admininfo[admin_level] == 9){
			$sns_btn = serialize($sns_btn);
			for($i=0;$i<count($select_pid);$i++){
				$sql = "update ".TBL_SHOP_PRODUCT." set sns_btn_yn = '".$sns_btn_yn."', sns_btn='".$sns_btn."' where id = '".$select_pid[$i]."'";
				$db->query ($sql);
			}
		}
	}else if($update_sns_type == "3"){ //sns 전체 미노출
		if($admininfo[admin_level] == 9){
			//$sns_btn = serialize($sns_btn);
			for($i=0;$i<count($select_pid);$i++){
				$sql = "update ".TBL_SHOP_PRODUCT." set sns_btn_yn = '' where id = '".$select_pid[$i]."'";
				$db->query ($sql);
			}
		}
	}

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택상품의 SNS 정보변경이 적상적으로 완료되었습니다.');</script>");
	echo("<script>if(parent.document.getElementById('act').src != 'about:blank'){parent.select_update_unloading();parent.location.reload();}else{parent.location.reload();}</script>");
	exit;
	
}

if($update_kind == "update_keyword"){	//검색키워드 일괄변경 2014-05-26 이학봉	셀러수정가능 2014-06-30

	if(!$search_keyword){
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('변경하시고자 하는 검색어를 입력해주세요');</script>");
		echo("<script>if(parent.document.getElementById('act').src != 'about:blank'){parent.select_update_unloading();parent.location.reload();}else{parent.location.reload();}</script>");
		exit;
	}
	
	$search_keyword = str_replace("'","",$search_keyword);

	if($update_keyword_type == "1"){ // 기존 보존후 검색키워드 추가
		//if($admininfo[admin_level] == 9){
			for($i=0;$i<count($select_pid);$i++){

				$sql = "select * from ".TBL_SHOP_PRODUCT." where id = '".$select_pid[$i]."'";
				$db->query($sql);
				$db->fetch();
				$ori_search_keyword = str_replace("'","",$db->dt[search_keyword]);		//검색키워드

				if($ori_search_keyword){
					$search_keyword_value = $ori_search_keyword.",".$search_keyword;
				}else{
					$search_keyword_value = $search_keyword;
				}

				$sql = "update ".TBL_SHOP_PRODUCT." set search_keyword='".$search_keyword_value."' , editdate = NOW() where id = '".$select_pid[$i]."'";
				$db->query ($sql);
				$search_keyword_value = '';
			}
		//}
	}else if($update_keyword_type == "2"){ //기존 검색키워드 삭제후 검색키워드 추가
		//if($admininfo[admin_level] == 9){
			for($i=0;$i<count($select_pid);$i++){
				$sql = "update ".TBL_SHOP_PRODUCT." set search_keyword = '".$search_keyword."' , editdate = NOW() where id = '".$select_pid[$i]."'";
				$db->query ($sql);
			}
		//}
	}else if($update_keyword_type == "3"){ //검색키워드 전체 미노출
		//if($admininfo[admin_level] == 9){
			for($i=0;$i<count($select_pid);$i++){
				$sql = "update ".TBL_SHOP_PRODUCT." set search_keyword = '' , editdate = NOW() where id = '".$select_pid[$i]."'";
				$db->query ($sql);
			}
		//}
	}

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택상품의 검색어 정보변경이 적상적으로 완료되었습니다.');</script>");
	echo("<script>if(parent.document.getElementById('act').src != 'about:blank'){parent.select_update_unloading();parent.location.reload();}else{parent.location.reload();}</script>");
	exit;
}


if ($update_kind == "movie"){		//동영상/바이럴URL 변경
	
	if($movie_url_check == '1'){
		if($admininfo[admin_level] == 9){
			for($i=0;$i<count($select_pid);$i++){
				$sql = "update ".TBL_SHOP_PRODUCT." set movie='".$movie."' where id = '".$select_pid[$i]."'";
				$db->query ($sql);
			}
		}
	}

	if($virals && $virals_check == '1'){	//바이럴 업데이트 체크후 실행
		for($i=0;$i<count($select_pid);$i++){
			$sql = "update shop_product_viralinfo set insert_yn='N'	where pid = '".$select_pid[$i]."' ";
			$db->query($sql);
			foreach($_POST["virals"] as $do_key=>$do_value) {
				if($virals[$do_key]["viral_name"] && $virals[$do_key]["viral_url"]){
					if($virals[$do_key]["vi_use"]){
						$vi_use = $virals[$do_key]["vi_use"];
					}else{
						$vi_use = "0";
					}

					$vi_ix=$virals[$do_key]["vi_ix"];//디스플레이 옵션 수정 kbk 12/06/19
					if($vi_ix!="0" && $vi_ix!="") {//디스플레이 옵션 수정 kbk 12/06/19

						$sql = "update shop_product_viralinfo set
										viral_name = '".$virals[$do_key]["viral_name"]."',
										viral_url = '".$virals[$do_key]["viral_url"]."',
										viral_desc = '".$virals[$do_key]["viral_desc"]."',
										insert_yn = 'Y' ,vi_use = '".$vi_use."'
										where vi_ix = '$vi_ix'";
					}else{
						$sql = "insert into shop_product_viralinfo (vi_ix,pid,viral_name,viral_url,viral_desc, vi_use, regdate) values('','".$select_pid[$i]."','".$virals[$do_key]["viral_name"]."','".$virals[$do_key]["viral_url"]."','".$virals[$do_key]["viral_desc"]."','".$vi_use."',NOW()) ";
						//echo $sql;
					}
					$db->sequences = "SHOP_GOODS_VIRALINFO_SEQ";
					$db->query($sql);
				}
			}
		}
		$db->query("delete from shop_product_viralinfo where pid = '$pid' and insert_yn = 'N' ");
	}

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택상품의 동영상/바이럴URL 정보변경이 적상적으로 완료되었습니다.');</script>");
	echo("<script>if(parent.document.getElementById('act').src != 'about:blank'){parent.select_update_unloading();parent.location.reload();}else{parent.location.reload();}</script>");
	exit;
}

if($update_kind == "commission"){

	if($admininfo[admin_level] == 9){
		for($j=0;$j<count($select_pid);$j++){
			$pid = $select_pid[$j];
			$sql = "update shop_product set
						one_commission = '".$batch_one_commission."',
						account_type = '".$account_type."',
						commission = '".$commission."',
						wholesale_commission = '".$wholesale_commission."'
					where id = '".$pid."'";
			$db->query($sql);
		}
	}

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택상품의 담당MD 정보변경이 적상적으로 완료되었습니다.');</script>");
	echo("<script>if(parent.document.getElementById('act').src != 'about:blank'){parent.select_update_unloading();parent.location.reload();}else{parent.location.reload();}</script>");
	exit;

}

if($update_kind == "md"){
	
	if($md_code_1){
		for($j=0;$j<count($select_pid);$j++){
			$pid = $select_pid[$j];
			$sql = "update shop_product set md_code = '".$md_code_1."' where id = '".$pid."'";
			$db->query($sql);
		}
	}

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택상품의 담당MD 정보변경이 적상적으로 완료되었습니다.');</script>");
	echo("<script>if(parent.document.getElementById('act').src != 'about:blank'){parent.select_update_unloading();parent.location.reload();}else{parent.location.reload();}</script>");
	exit;

}

if ($update_kind == "update_wish"){		//관련상품

	if(count($sns_btn) > '0' && count($category[0]) > "0"){
		echo("<script>alert('변경하시고자 하는 관련상품을 선택해주세요');</script>");
		echo("<script>parent.select_update_unloading();parent.location.reload();</script>");
		exit;
	}

	if($relation_display_type == 'M'){
		if(count($rpid[1]) > '0'){
			for($j=0;$j<count($select_pid);$j++){

				$pid = $select_pid[$j];

				$db->query("update ".TBL_SHOP_RELATION_PRODUCT." set insert_yn = 'N' where pid = '".$pid."'");
				for($i=0;$i<count($rpid[1]);$i++){
					$sql = "select rp_ix from ".TBL_SHOP_RELATION_PRODUCT." where pid = '$pid' and rp_pid = '".$rpid[1][$i]."' ";
					$db->query($sql);
					$db->fetch();
					if($db->total){
						$sql = "update ".TBL_SHOP_RELATION_PRODUCT." set insert_yn = 'Y' , vieworder = '".($i+1)."' where rp_ix ='".$db->dt[rp_ix]."'";
						$db->query($sql);
					}else{
						$sql = "insert into ".TBL_SHOP_RELATION_PRODUCT." (rp_ix,pid,rp_pid,vieworder,insert_yn,regdate) values ('','$pid','".$rpid[1][$i]."','".($i+1)."','Y',NOW())";
						$db->sequences = "SHOP_RELATION_PRODUCT_SEQ";
						$db->query($sql);
					}
				}
				$db->query("delete from ".TBL_SHOP_RELATION_PRODUCT." where insert_yn = 'N' and pid ='".$pid."' ");

				$sql = "update shop_product set 
							relation_display_type = '".$relation_display_type."',
							relation_product_cnt='".$relation_product_cnt."'
						where
							id = '".$pid."'";
				$db->query($sql);
			}
		}
	}else{
	//자동상품 : 카테고리
		if(count($category[0]) > '0'){	//카테고리 선택시 depth 없음 수정필요 2014-07-02 이학봉

			for($i=0;$i<count($select_pid);$i++){

				$pid = $select_pid[$i];

				$db->query("update shop_relation_category set insert_yn = 'N'  where pid = '".$pid."' ");

				for($j=0;$j < count($category[0]);$j++){
					$db->query("select rc_ix from shop_relation_category where pid = '".$pid."' and cid = '".$category[0][$j]."' ");

					if(!$db->total){
						$sql = "select depth from shop_category_info where cid = '".$category[0][$j]."'";
						$db->query($sql);
						$db->fetch();
						$depth = $db->dt[depth];

						$sql = "insert into shop_relation_category (rc_ix,cid,depth,pid, vieworder, insert_yn, regdate) values ('','".$category[0][$j]."','".$depth."','".$pid."','".($j+1)."','Y', NOW())";//depth 컬럼 추가 kbk 13/07/01
	
						$db->sequences = "SHOP_MAIN_CT_LINK_SEQ";
						$db->query($sql);
					}else{
						$sql = "update shop_relation_category set insert_yn = 'Y',vieworder='".($j+1)."' where pid = '".$pid."' and cid = '".$category[0][$j]."' ";
						$db->query($sql);
					}
				}

				$db->query("delete from shop_relation_category where pid = '".$pid."' and insert_yn = 'N' ");

				$sql = "update shop_product set 
							relation_display_type = '".$relation_display_type."',
							relation_display_order_type = '".$relation_display_order_type."',
							relation_display_order_date = '".$relation_display_order_date."',
							relation_product_cnt='".$relation_product_cnt."'
						where
							id = '".$pid."'";
				$db->query($sql);

			}
		}
	}

	echo("<script>alert('선택상품의 관련상품이 적상적으로 완료되었습니다.');</script>");
	echo("<script>if(top.document.getElementById('act').src != 'about:blank'){top.select_update_unloading();top.location.reload();}else{top.location.reload();}</script>");
	exit;
}

if($update_kind == "mandatory"){	//셀러수정 가능

    $sql = "select mall_ix from shop_mandatory_info where mi_code = '".$mandatory_type_1."' ";
    $db->query($sql);
    $db->fetch();
    $mandatory_mall_ix = $db->dt['mall_ix'];

    if($mandatory_mall_ix == '20bd04dac38084b2bafdd6d78cd596b2'){
        $mandatory_table = "shop_product_mandatory_info_global";
        $mandatory_type_column = "mandatory_type_global";
    }else{
        $mandatory_table = "shop_product_mandatory_info";
        $mandatory_type_column = "mandatory_type";
    }

	for($j=0;$j<count($select_pid);$j++){
		$pid = $select_pid[$j];

		//상품필수고시 - START
		$sql = "update $mandatory_table set insert_yn='N' where pid = '".$pid."' ";
		$db->query($sql);

		if(is_array($mandatory_info)){
			foreach ($mandatory_info as $m_info){
				if($m_info[pmi_ix] != ""){
					if($m_info[pmi_title] !="" || $m_info[pmi_code] !="" ){

						$sql = "update $mandatory_table set pmi_code='".$m_info[pmi_code]."', pmi_title='".$m_info[pmi_title]."',pmi_desc='".$m_info[pmi_desc]."',insert_yn='Y' where pmi_ix = '".$m_info[pmi_ix]."' ";
						$db->query($sql);
					}
				}else{
					if($m_info[pmi_title] !="" || $m_info[pmi_desc] !="" ){
						$sql = "insert into $mandatory_table(pmi_ix,pid,pmi_code,pmi_title,pmi_desc,insert_yn,regdate) values('','".$pid."','".$m_info[pmi_code]."','".$m_info[pmi_title]."','".$m_info[pmi_desc]."','Y',NOW())";
						$db->sequences = "SHOP_GOODS_MANDATORY_INFO_SEQ";
						$db->query($sql);
					}
				}
			}
		}

		$sql = "delete from $mandatory_table where pid = '".$pid."' and insert_yn='N' ";
		$db->query($sql);
		//상품필수고시 - END
		if($mandatory_type_1 !=""){
			$mandatory_type = $mandatory_type_1."|".$mandatory_type_2;	// 상품고시 제대로 substr 되지 않아서 | 구분값으로 분리 시킴 2013-06-05 이학봉
		}else{
			$mandatory_type ="";
		}

		$sql = "update shop_product set $mandatory_type_column = '".$mandatory_type."' where id = '".$pid."'";
		$db->query($sql);
	}
	

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택상품의 담당MD 정보변경이 적상적으로 완료되었습니다.');</script>");
	echo("<script>if(parent.document.getElementById('act').src != 'about:blank'){parent.select_update_unloading();parent.location.reload();}else{parent.location.reload();}</script>");
	exit;

}

if($update_kind == 'price'){

	if($admininfo[admin_level] == 9){
		for($i=0;$i<count($select_pid);$i++){//disp='".$_POST["disp".$select_pid[$i]]."',

			$pid = $select_pid[$i];
			//가격변경 히스토리 쌓기 시작 
			$sql = "select * from ".TBL_SHOP_PRODUCT." where id = '".$pid."'";
			$db->query($sql);
			$db->fetch();
			
			$bcoprice = $db->dt[coprice];
			$blistprice = $db->dt[listprice];
			$bsellprice = $db->dt[sellprice];

			$bwholesale_sellprice = $db->dt[wholesale_sellprice];
			$bwholesale_price = $db->dt[wholesale_price];

			if($sellprice != $bsellprice || $coprice != $bcoprice  ||  $listprice != $blistprice || $wholesale_sellprice != $bwholesale_sellprice || $wholesale_price != $bwholesale_price){
				$sql = "INSERT INTO ".TBL_SHOP_PRICEINFO." (id, pid, listprice, sellprice, coprice, reserve,  company_id, charger_info,regdate,wholesale_price,wholesale_sellprice) ";
				$sql = $sql." values('', '".$pid."','$listprice','$sellprice', '$coprice', '$reserve',  '".$admininfo[company_id]."','[".$admininfo[company_name]."] ".$admininfo[charger]."(".$admininfo[charger_id].")',NOW(),'".$wholesale_price."','".$wholesale_sellprice."') ";
				$db->query($sql);
			}
			//가격변경 히스토리 쌓기 끝

			$sql = "update ".TBL_SHOP_PRODUCT." set 
						coprice = '".$coprice."',
						listprice = '".$listprice."',
						sellprice = '".$sellprice."',
						wholesale_price = '".$wholesale_price."',
						wholesale_sellprice = '".$wholesale_sellprice."',
						editdate = NOW()
					where
						id = '".$pid."'";
			$db->query($sql);

		}
	}

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택상품의 가격 정보변경이 적상적으로 완료되었습니다.');</script>");
	echo("<script>if(parent.document.getElementById('act').src != 'about:blank'){parent.select_update_unloading();parent.location.reload();}else{parent.location.reload();}</script>");
	exit;

}

if($update_kind == 'mileage'){
	if($admininfo[admin_level] == 9){
		for($i=0;$i<count($select_pid);$i++){//disp='".$_POST["disp".$select_pid[$i]]."',

			$pid = $select_pid[$i];

			$sql = "update ".TBL_SHOP_PRODUCT." set 
						wholesale_reserve_yn = '".$wholesale_reserve_yn."',
						wholesale_reserve = ".($wholesale_rate1 > 0 ? "round(wholesale_sellprice * ( ".$wholesale_rate1." / 100))" : "'0'").",
						wholesale_reserve_rate = '".$wholesale_rate1."',
						wholesale_rate_type = '".$wholesale_rate_type."',
						reserve_yn = '".$reserve_yn."',
						reserve = ".($rate1 > 0 ? "round(sellprice * ( ".$rate1." / 100))" : "'0'").",
						reserve_rate = '".$rate1."',
						rate_type = '".$rate_type."',
						editdate = NOW()
					where
						id = '".$pid."'";
			$db->query($sql);

		}
	}

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택상품의 가격 정보변경이 적상적으로 완료되었습니다.');</script>");
	echo("<script>if(parent.document.getElementById('act').src != 'about:blank'){parent.select_update_unloading();parent.location.reload();}else{parent.location.reload();}</script>");
	exit;

}


if($update_kind == 'state'){	//셀러수정가능 2014-06-30

	if($_SESSION['admininfo']['admin_level'] < 9 ){
		$state_where = " and state not in ('6','7','8') ";
	}

	for($i=0;$i<count($select_pid);$i++){//disp='".$_POST["disp".$select_pid[$i]]."',

		$pid = $select_pid[$i];

		/*판매상태가 일시품절일경우 입고예정일과 자동판매중 상태전환일/ 상태변경에 따른 변경사유 시작 2014-02-14 이학봉*/
		if($c_state == "0"){

			if($is_auto_change == "1"){
				$input_date = $input_date." ".$input_stime.":".$input_smin.":00";
				$auto_change_state = $auto_change_state." ".$auto_change_stime.":".$auto_change_smin.":00";
				$where_state_div = " , input_date = '".$input_date."', is_auto_change = '".$is_auto_change."', auto_change_state='".$auto_change_state."'";
			}else{
				$input_date = $input_date." ".$input_stime.":".$input_smin.":00";
				$where_state_div = " , input_date = '".$input_date."', is_auto_change = '".$is_auto_change."'";
			}

		}else if($c_state == "2" || $c_state == "8" || $c_state == "9" || $c_state == "7"){

			$sql = "insert into shop_product_state_history set
						pid = '".$pid."',
						state = '".$c_state."',
						state_div = '".$state_div."',
						state_msg = '".$state_msg."',
						charger = '".$admininfo[charger]."',
						charger_ix = '".$admininfo[charger_ix]."',
						regdate = NOW()";
			$db->query($sql);
		}
		/*판매상태가 일시품절일경우 입고예정일과 자동판매중 상태전환일/ 상태변경에 따른 변경사유 시작 2014-02-14 이학봉*/


		$sql = "select state from ".TBL_SHOP_PRODUCT." where id='".$pid."' ".$state_where."";
		$db->query($sql);
		if($db->total){
			$db->fetch();
			$b_state = $db->dt['state'];
			
			$historyBool = true;
		}else{
			$historyBool = false;
		}

		$sql = "update ".TBL_SHOP_PRODUCT." set
					state = '".$c_state."' , editdate = NOW()
					$where_state_div
				where
					id='".$pid."' ".$state_where."";
			
		$db->query($sql);

		$sql = "update ".TBL_SHOP_PRODUCT_GLOBAL." set
					state = '".$c_state."' , editdate = NOW()
					$where_state_div
				where
					id='".$pid."' ".$state_where."";

		$db->query($sql);

		if($historyBool){
			if( $b_state != $c_state ){
				product_edit_history_insert($pid,'state', '판매상태', $b_state ,$c_state ,$_SESSION[admininfo][charger_ix],$_SESSION[admininfo][charger]);
			}
		}
	}
	
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택상품의 가격 정보변경이 적상적으로 완료되었습니다.');</script>");
	echo("<script>if(parent.document.getElementById('act').src != 'about:blank'){parent.select_update_unloading();parent.location.reload();}else{parent.location.reload();}</script>");
	exit;

}


if($update_kind == 'is_mobile_use'){	//모바일상품 사용유무 2014-08-13	//관리자만 사용가능

	for($i=0;$i<count($select_pid);$i++){//disp='".$_POST["disp".$select_pid[$i]]."',

		$pid = $select_pid[$i];

		$sql = "update ".TBL_SHOP_PRODUCT." set
					is_mobile_use = '".$batch_is_mobile_use."' , 
					editdate = NOW()
				where
					id='".$pid."'";
		$db->query($sql);
	}
	
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택상품의 모바일상품 사용유무가 적상적으로 처리 완료되었습니다.');</script>");
	echo("<script>if(parent.document.getElementById('act').src != 'about:blank'){parent.select_update_unloading();parent.location.reload();}else{parent.location.reload();}</script>");
	exit;

}


if($update_kind == "available_stock"){		//가용재고 일괄수정

	for($i=0;$i<count($select_pid);$i++){
		$sql = "UPDATE ".TBL_SHOP_PRODUCT." p SET p.available_stock = ".$available_stock.", editdate = NOW() Where id = '".$select_pid[$i]."' ";
		$db->query ($sql);
	}
	
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택상품의 가용재고 정보변경이 적상적으로 완료되었습니다.');</script>");
	echo("<script>if(top.document.getElementById('act').src != 'about:blank'){top.select_update_unloading();top.location.reload();}else{top.location.reload();}</script>");
	exit;
}



if($update_kind == "sellertool_goods"){		//가용재고 일괄수정

	for($i=0;$i<count($select_pid);$i++){
		//$sql = "UPDATE ".TBL_SHOP_PRODUCT." p SET p.available_stock = ".$available_stock.", editdate = NOW() Where id = '".$select_pid[$i]."' ";
		//$db->query ($sql);

		if(is_array($partner_prd_reg) && count($partner_prd_reg) > 0){
	
			foreach($partner_prd_reg as $p_reg){
				$sql = "select * from sellertool_get_product where site_code = '".$p_reg."' and pid = '".$select_pid[$i]."'";
				$db->query($sql);
				if($db->total){
					$db->fetch();
					$sgp_ix = $db->dt[sgp_ix];
					$sql = "update sellertool_get_product set state = '1' where sgp_ix = '".$sgp_ix."' and state = '0'";
					$db->query($sql);
				}else{
					$sql = "insert into sellertool_get_product (pid,site_code,state) values ('".$select_pid[$i]."','".$p_reg."','1')";
					$db->query($sql);
				}
				if(function_exists('sellertool_single_update')  && $sellertool_single_update_bool ){
					sellertool_single_update($p_reg,str_pad($pid,"10","0",STR_PAD_LEFT),'regist');
				}
			}
		}

	}
	
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택상품의 가용재고 정보변경이 적상적으로 완료되었습니다.');</script>");
	echo("<script>if(top.document.getElementById('act').src != 'about:blank'){top.select_update_unloading();top.location.reload();}else{top.location.reload();}</script>");
	exit;
}

if($act == "select_delete"){

	for($i=0;$i<count($select_pid);$i++){
		
		$db->query("update ".TBL_SHOP_PRODUCT." p SET  editdate = NOW(), is_delete='1' , state='2' , disp='0'  WHERE id='".$select_pid[$i]."'");

		/*
		$uploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product", $select_pid[$i], 'Y');
		if ($uploaddir && file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/")){
			rmdirr($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/");
		}


		$db->query("DELETE FROM ".TBL_SHOP_PRODUCT." WHERE id='".$select_pid[$i]."' "); //limit 1 2012-11-07 홍진영 오라클 쿼리에서 오류나서 빼놓음
		$db->query("DELETE FROM ".TBL_SHOP_PRICEINFO." WHERE pid='".$select_pid[$i]."'");

		$db->query("DELETE FROM ".TBL_SHOP_PRODUCT_RELATION." WHERE pid='".$select_pid[$i]."'");
		$db->query("DELETE FROM shop_product_buyingservice_priceinfo WHERE pid='".$select_pid[$i]."'");

		$db->query("DELETE FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." WHERE pid='".$select_pid[$i]."'");
		$db->query("DELETE FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE pid='".$select_pid[$i]."'");
		$db->query("DELETE FROM ".TBL_SHOP_CART." WHERE id='".$select_pid[$i]."'");
		//$db->query("DELETE FROM ".TBL_SHOP_ORDER." WHERE id='".$select_pid[$i]."'");



		if($select_pid[$i] && is_dir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product_detail/".$select_pid[$i])){
			rmdirr($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product_detail/".$select_pid[$i]);
		}

		$adduploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg", $select_pid[$i], 'Y');
		if ($select_pid[$i] && is_dir($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/")){
			rmdirr($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/");
		}
		*/
		echo "<script language='javascript'>parent.document.location.reload();</script>";

	}

	echo("<script>if(parent.document.getElementById('act').src != 'about:blank'){parent.select_update_unloading();parent.location.reload();}else{parent.location.reload();}</script>");
}



if($update_kind == 'category_code'){		//카테고리 추가 (코드형 ) 2014-07-12 이학봉

	if($admininfo[admin_level] == '9'){

			$basic_code = trim($basic_code);

			if(strlen($basic_code) == '15'  && $category_change_type_code == '4'){

			if(strpos($display_category_code,",") !== false){
				$category_array = explode(",",$display_category_code);
				$category_array = array_filter($category_array, create_function('$a','return preg_match("#\S#", $a);'));
				array_push($category_array,$basic_code);	//기본카테고리와 같이 조합

			}else if(strpos($display_category_code,"\n") !== false){//\n
				$category_array = explode("\n",$display_category_code);
				$category_array = array_filter($category_array, create_function('$a','return preg_match("#\S#", $a);'));
				array_push($category_array,$basic_code);	//기본카테고리와 같이 조합
			}else{
				$category_array[0] = $basic_code;
			}

			for($i=0;$i<count($category_array);$i++){
				$category_array[$i] = trim($category_array[$i]);
				
				if($category_array[$i] == $basic_code){
					$category_infos[$i][basic] = '1';
				}else{
					$category_infos[$i][basic] = '0';
				}
				if(strlen($category_array[$i]) == '15'){
					$category_infos[$i][cid] = $category_array[$i];
				}
			}

			for($i=0;$i<count($select_pid);$i++){
				$pid = $select_pid[$i];
				$db->query("delete from shop_product_relation where pid ='".$pid."'");

				for($j=0;$j<count($category_infos);$j++){
					$sql = "insert into ".TBL_SHOP_PRODUCT_RELATION." (cid,pid,disp,basic,insert_yn,regdate) values('".$category_infos[$j][cid]."','".$pid."','1','".$category_infos[$j][basic]."','Y',NOW())";
					$db->query ($sql);
				}

				$db->query("update shop_product set reg_category = 'Y' , editdate = NOW() where id ='".$pid."'");
			}

			echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('전체상품의 카테고리 정보변경이 적상적으로 완료되었습니다.');</script>");
			echo("<script>if(parent.document.getElementById('act').src != 'about:blank'){parent.select_update_unloading();parent.location.reload();}else{parent.location.reload();}</script>");

		}else{
			echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('관리자만 수정가능합니다.');</script>");
			echo("<script>if(parent.document.getElementById('act').src != 'about:blank'){parent.select_update_unloading();parent.location.reload();}else{parent.location.reload();}</script>");
		}
	}else{
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('관리자만 수정가능합니다.');</script>");
		echo("<script>if(parent.document.getElementById('act').src != 'about:blank'){parent.select_update_unloading();parent.location.reload();}else{parent.location.reload();}</script>");
	}

}
//코드형 카테고리 추가 형태 ajax 실행부분 

if($mode == 'check_category_code'){
	
	if(!$category_code){
		echo "N";
	}
	$category_code = trim($category_code);
	$sql = "select * from shop_category_info where cid = '".$category_code."'";
	$db->query($sql);
	$db->fetch();

	if($db->total > 0){
		echo "Y";
	}else{
		echo "N";
	}

}


?>