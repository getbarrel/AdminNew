<?
/*
CompanyList2() 함수 안에 들어있던 $( '#company_id').combobox(); 로 인하여 프로모션 상품 검색 레이어 스크립트 오류 발생 대표님과 상의 후 주석 처리 kbk 13/05/09
layout.xml 파일을 사용할 경우 페이지 새로 생성 시 getDesignTempletPath() 함수에서 파일 경로 잡아주지 못하던 부분 수정 kbk 13/05/21
*/
include($_SERVER["DOCUMENT_ROOT"]."/admin/include/lib/number.function.php");
/*
if($_SERVER["HTTP_HOST"] != "welbay.mallstory.com"){
	if($_SERVER["HTTP_HOST"] != "admin.welbay.co.kr" && ($_SERVER["PHP_SELF"] == "/admin" || $_SERVER["PHP_SELF"] == "/admin/admin.php")){
		//header("Location:http://admin.thezoom.co.kr/admin/admin.php");
		echo "<script >document.location.href='http://admin.welbay.co.kr/admin/admin.php'</script>";
		exit;
	}
}
*/

$goods_options_tmp_type ="charger_ix";	//company_id,charger_ix

$sattle_module = array(
					'kcp'=>'KCP',
					//'lgdacom'=>'LG데이콤',
    				//'lguplus'=>'LGU+',
					//'inicis'=>'이니시스',
					//'inipay_standard'=>'이니페이스텐다드',
					//'plugnpay'=>'플러그앤페이',
					'nicepay'=>'나이스페이',
    				//'nicepay_tx'=>'나이스페이(TX)',
					//'mobilians'=>'모빌리언스',
					//'billgate'=>'빌게이트',
					//'//kspay'=>'케이에스페이',
					//'paytus'=>'페이투스',
					//'gmopg'=>'GMOPG',
					//'payline'=>'페이라인', 너무 특수한 경우라 제외
					''=>'사용안함'
				);
$mobile_sattle_module = array('danal'=>'다날','mobilians'=>'모빌리언스',''=>'사용안함');

$exclue_bbs_templet = array('cafebbs','blogbbs','admin', 'consult','mobile_consult','sns_consult','blogbbs_bakup','basic_dmarket','sns_coupon_bs','sns_basic');

$LargeImageSize = "ms";

$required_path = "/admin/icon/required.gif";
$required2_path = "/admin/icon/required2.gif";
$required3_path = "/admin/icon/required3.gif";

//$currency_types = array('KRW','USD','IDR', 'CNY','JPY');
$currencys[0] = array('currency_type_name'=>'한화(KRW)','currency_type'=>'KRW');
$currencys[1] = array('currency_type_name'=>'달러(USD)','currency_type'=>'USD');
$currencys[2] = array('currency_type_name'=>'루피아(IDR)','currency_type'=>'IDR');
$currencys[3] = array('currency_type_name'=>'위엔화(CNY)','currency_type'=>'CNY');
$currencys[4] = array('currency_type_name'=>'엔화(JPY)','currency_type'=>'JPY');

$currency_display["KRW"]["front"] = "";
$currency_display["KRW"]["back"] = "원";
$currency_display["USD"]["front"] = "$";
$currency_display["USD"]["back"] = "";
$currency_display["IDR"]["front"] = "Rp.";
$currency_display["IDR"]["back"] = "";
$currency_display["CNY"]["front"] = "￥";
$currency_display["CNY"]["back"] = "";
$currency_display["JPY"]["front"] = "￥";
$currency_display["JPY"]["back"] = "";

if($_SESSION["admininfo"]["language"] == "english"){
	$auth_write_msg = "javascript:alert('You do not have write permission on the menu.');";
	$auth_update_msg = "javascript:alert('You do not have permission to modify the menu.');";
	$auth_delete_msg = "javascript:alert('You do not have permission to delete the menu.');";
	$auth_excel_msg = "javascript:alert('해당 메뉴에 대한 엑셀출력 권한이 없습니다.');";
}else if($_SESSION["admininfo"]["language"] == "indonesian"){
	$auth_write_msg = "javascript:alert('Anda tidak memiliki izin menulis pada menu.');";
	$auth_update_msg = "javascript:alert('Anda tidak memiliki izin untuk memodifikasi menu.');";
	$auth_delete_msg = "javascript:alert('Anda tidak memiliki izin untuk menghapus menu.');";
	$auth_excel_msg = "javascript:alert('해당 메뉴에 대한 엑셀출력 권한이 없습니다.');";
}else{
	$auth_write_msg = "javascript:alert('해당 메뉴에 대한 쓰기권한이 없습니다.');";
	$auth_update_msg = "javascript:alert('해당 메뉴에 대한 수정권한이 없습니다.');";
	$auth_delete_msg = "javascript:alert('해당 메뉴에 대한 삭제권한이 없습니다.');";
    $auth_excel_msg = "javascript:alert('해당 메뉴에 대한 엑셀출력 권한이 없습니다.');";
}

$font_style_family = array('굴림','돋움','바탕','궁서','Arial','Sans Serif','Tahoma','Verdana','Courier New','Georgia','Times New Roman','Impact','Comic Sans MS');
$font_style_size = array('1','2','3','4','5','6','7');
$font_style_weight = array('bolg'=>'굵게','thin'=>'얇게','nomal'=>'기본');
$font_style_decoration = array('underline'=>'아래에 줄긋기','overline'=>'위에 줄긋기','line-through'=>'가운데 줄긋기');

$display_yn = ($_SESSION["admininfo"]["admin_id"]=="forbiz")?'':'display:none';
 

  function convertWrongUnicode($data){
		$str  = str_replace('u', '\u', $data);
		$result = json_decode(sprintf('"%s"', $str));
		return $result;
}


// shop/common/util.php 에서 이동
function GetParentStandardCategory($subcid,$subdepth)// 상위 카테고리 이름을 반환하는 함수
{
	global $slave_mdb;
	//$slave_mdb = new Database;

	$sql = "select c.cid,c.cname from standard_category_info c where cid LIKE '".substr($subcid,0,$subdepth*3)."%' and depth = ".($subdepth-1)."  ";

	$slave_mdb->query($sql);
	$slave_mdb->fetch(0);

	$category_string = $slave_mdb->dt[cname];
 
	if ($subdepth > 1){// 3depth 이상일 경우 카테고리 값을 제대로 못 불러와서 위의 것을 수정함 kbk 12/02/27		// 2차분류부터 사용가능함???
		for($i=($subdepth-1);$i>=1;$i--) {
			$sql = "select c.cid,c.cname from standard_category_info c where cid LIKE '".substr($subcid,0,($i)*3)."%' and depth = ".($i-1)."  ";
			$slave_mdb->query($sql);
			$slave_mdb->fetch(0);
			if($slave_mdb->dt[cname]){
				$category_string = $slave_mdb->dt[cname]." > ".$category_string;
			}
		}
	}

	return $category_string	;
}


function getStyle($object_name="style", $style_ix="" , $property="", $return_type="selectbox", $styleinfo=""){
	global $admininfo;

	$mdb = new MySQL;
	$mdb->query("SELECT style_ix, style_name FROM shop_product_style where disp = 1 ");
	$styles = $mdb->fetchall("object");

	if($return_type == "selectbox"){
		$mstring = "<select name='".$object_name."' $property validation='false' title='스타일분류'>";
		$mstring .= "<option value=''>스타일선택</option>";
		for($i=0;$i < count($styles);$i++){
		$mstring .= "<option value='".$styles[$i][style_ix]."' ".($styles[$i][style_ix] == $style_ix ? "selected":"").">".$styles[$i][style_name]."</option>";
		}
		$mstring .= "</select>";
	}else{
        //print_r($styles['style_ix']);
		for($i=0;$i < count($styles);$i++){
		//$mstring .= "<div style='float:left;margin-right:5px;'><input type=checkbox name='".$object_name."' id='".$object_name."_".$styles[$i][style_ix]."'  value='".$styles[$i][style_ix]."' ".($styles[$i][style_ix] == $style_ix ? "checked":"")."><label for='".$object_name."_".$styles[$i][style_ix]."'>".$styles[$i][style_name]."</label></div>";
        $is_chk = (in_array((string)$styles[$i][style_ix], (array)$styleinfo)) ? "checked" : "";
		$mstring .= "<div style='float:left;margin-right:5px;'><input type=checkbox name='".$object_name."' id='".$object_name."_".$styles[$i][style_ix]."'  value='".$styles[$i][style_ix]."' ".$is_chk."><label for='".$object_name."_".$styles[$i][style_ix]."'>".$styles[$i][style_name]."</label></div>";
		}
	}
	return $mstring;
}

function getTag($object_name="tag", $tag_ix="" , $property="", $return_type="selectbox", $taginfo=""){
	global $admininfo;

	$mdb = new MySQL;
	$mdb->query("SELECT tag_ix, tag_name FROM shop_product_tag where disp = 1 ");
	$tags = $mdb->fetchall("object");

	if($return_type == "selectbox"){
		$mstring = "<select name='".$object_name."' $property validation='false' title='태그분류'>";
		$mstring .= "<option value=''>태그선택</option>";

		for($i=0;$i < count($tags);$i++){
			$mstring .= "<option value='".$tags[$i][tag_ix]."' ".($tags[$i][tag_ix] == $tag_ix ? "selected":"").">".$tags[$i][tag_name]."</option>";
		}
		$mstring .= "</select>";
	}else{
        //print_r($styles['style_ix']);
		for($i=0;$i < count($tags);$i++){
			$is_chk = (in_array((string)$tags[$i][tag_ix], (array)$taginfo)) ? "checked" : "";
			$mstring .= "<div style='float:left;margin-right:5px;'><input type=checkbox name='".$object_name."' id='".$object_name."_".$tags[$i][tag_ix]."'  value='".$tags[$i][tag_ix]."' ".$is_chk."><label for='".$object_name."_".$tags[$i][tag_ix]."'>".$tags[$i][tag_name]."</label></div>";
		}
	}

	return $mstring;
}


function is_excel_csv(){
	//
	return true;
}


function o2oSendPush($div,$data_ix,$type)
{
	$db = new Database;
	
	$data_type			=	$div; //P:상점 E:이벤트
	$act_type			=	$type;

	if($data_type=='P'){

		$sql = "select place_ix, place_name, place_radius as radius, place_latitude as latitude,  place_longitude as longitude
					from shop_event_place ep
					where place_ix = '".$data_ix."' ";

		$db->query($sql);
		$db->fetch();
		
		
		$place_ix			=	$db->dt["place_ix"];
		$place_name			=	$db->dt["place_name"];
		$radius				=	$db->dt["radius"];
		$latitude			=	$db->dt["latitude"];
		$longitude			=	$db->dt["longitude"];

		/*
		$sql = "select ep.place_ix, place_name, place_radius as radius, place_latitude as latitude,  place_longitude as longitude ,
						from_unixtime(event_use_sdate,'%Y-%m-%d %H:%i:%s') as event_use_sdate, from_unixtime(event_use_edate,'%Y-%m-%d %H:%i:%s') as event_use_edate,
						ep.editdate as place_editdate, e.editdate as event_editdate  
					from shop_event e, shop_event_place ep
					where agent_type = 'M' AND ep.place_ix = '".$place_ix."' ";
		
		$db->query($sql);
		$db->fetch();


		$event_use_sdate	=	(string)$db->dt["event_use_sdate"];
		$event_use_edate	=	(string)$db->dt["event_use_edate"];
		$place_editdate		=	(string)$db->dt["place_editdate"];
		$event_editdate		=	(string)$db->dt["event_editdate"];

		,

				'event_use_sdate'	=> $event_use_sdate,
				'event_use_edate'	=> $event_use_edate,
				'place_editdate'	=> $place_editdate,
				'event_editdate'	=> $event_editdate

		*/

		$o2oPushData = array(	
				'app_div'		=>	"webapp",
				'data_type'      =>	$data_type,
				'data_ix'      =>	$place_ix,
				'act_type'		=>  $act_type,
				'place_name'	=>	$place_name,
				'latitude'      =>	$latitude,
				'longitude'     =>	$longitude,
				'radius'		=> $radius
			);

	}elseif($data_type=='E'){
		
		if(empty($_SERVER["HTTPS"])){
			$http = "http://";
		}else{
			$http = "https://";
		}

		$sql = "SELECT 
					event_ix as data_ix
					, event_title 
					, send_cond
					, send_duration
					, wait
					, wait_duration
					, CONCAT('".$http.$_SERVER["HTTP_HOST"].$_SESSION["layout_config"]["mall_data_root"]."/images/event/',event_ix,'/event_banner_',event_ix,'.gif') as image
					, 'U' as event_type
					, CONCAT('".$http.$_SERVER["HTTP_HOST"]."/link/e.php?ix=',event_ix) as url
					, FROM_UNIXTIME(event_use_sdate,'%Y%m%d%H%i%s') as event_use_sdate
					, FROM_UNIXTIME(event_use_edate,'%Y%m%d%H%i%s') as event_use_edate
				FROM 
					shop_event
				WHERE 
					event_ix = '".$data_ix."'
				AND agent_type = 'M' 
				"; //AND disp = '1' AND NOW() BETWEEN FROM_UNIXTIME(event_use_sdate,'%Y-%m-%d %H:%i:%s') AND FROM_UNIXTIME(event_use_edate,'%Y-%m-%d %H:%i:%s')

		$db->query($sql);
		if($db->total > 0){
			$db->fetch(0,"assoc");
			$o2oPushData = $db->dt;

			$db->query("SELECT place_ix FROM shop_event_place_relation where event_ix= '$data_ix'");
			$place_ix_array = $db->fetchall("object");
			if(count($place_ix_array) > 0){
				$place_ix = array();
				foreach($place_ix_array as $p_ix){
					$place_ix[] = $p_ix['place_ix'];
				}
			}else{
				$place_ix = null;
			}

			$o2oPushData['place_ix_array']=$place_ix;
			$o2oPushData['app_div']="webapp";
			$o2oPushData['data_type']=$data_type;
			$o2oPushData['act_type']=$act_type;

		}
	}
	
	if( !empty($o2oPushData) ){
		include_once $_SERVER["DOCUMENT_ROOT"]."/openapi/mainApi/pushService/sendPlaceInfo.php";
	}
}


function getEventPlaceInfo($place_ix, $type = "select"){
	$mdb = new Database;

	if($type == "select"){
		$mdb->query("SELECT * FROM shop_event_place where 1  ");
		$shopinfos = $mdb->fetchall("object");

		$mstring = "<select name='place_ix' validation=false title='이벤트 플레이스'>
						<option value=''>전체</option>";
						for($i=0; $i < count($shopinfos) ; $i++){
						$mstring .= "<option value='".$shopinfos[$i][place_ix]."' ".($shopinfos[$i][place_ix] == $place_ix ? "selected":"").">".$shopinfos[$i][place_name]."  </option>";
						}
						$mstring .= "</select>";
		return $mstring;
	}else if($type == "multiple"){
		$mdb->query("SELECT * FROM shop_event_place where 1  ");
		$shopinfos = $mdb->fetchall("object");
		if(!is_array($place_ix)){
			$place_ix = array();
		}
		$mstring = "<select id='place_ix' name='place_ix[]' validation=false title='이벤트 플레이스' multiple>";
						for($i=0; $i < count($shopinfos) ; $i++){
						$mstring .= "<option value='".$shopinfos[$i][place_ix]."' ".( in_array($shopinfos[$i][place_ix], $place_ix) ? "selected":"").">".$shopinfos[$i][place_name]."  </option>";
						}
						$mstring .= "</select>

						<link href='../css/multiple-select.css' rel='stylesheet'/>
						<script src='../js/multiple-select.js'></script>
						<script type='text/javascript'>
						<!--
							//http://wenzhixin.net.cn/p/multiple-select/docs/#the-filter1

							$('#place_ix').multipleSelect({ width: 200, selectAllText : '전체' , allSelected : '전체' });
						//-->
						</script>";
		return $mstring;

	}else if($type == "text"){
		$sql = "SELECT place_ix, place_name FROM shop_event_place where  place_ix = '".$place_ix."'  ";
		//echo $sql;
		$mdb->query($sql);
		if($mdb->total){
			$mdb->fetch();
			return $mdb->dt["place_name"];
		}else{
			return "전체";
		}

	}else{
		$mdb->query("SELECT place_ix, place_name FROM shop_event_place where 1  ");
		$shopinfos = $mdb->fetchall("object");
		return $shopinfos;
	}

}


function GetDisplayDivision($mall_ix, $type = "array"){
	$mdb = new Database;

	if($type == "select"){
		$mdb->query("SELECT mall_ix, mall_ename, mall_domain,mall_templete_type FROM ".TBL_SHOP_SHOPINFO." where mall_div in ('B')  ");
		$shopinfos = $mdb->fetchall("object");

		$mstring = "<select name='mall_ix' validation=false title='프론트 전시구분'>
						<option value=''>전체</option>";
						for($i=0; $i < count($shopinfos) ; $i++){
						$mstring .= "<option value='".$shopinfos[$i][mall_ix]."' ".($shopinfos[$i][mall_ix] == $mall_ix ? "selected":"").">".$shopinfos[$i][mall_templete_type]." (".$shopinfos[$i][mall_domain].")</option>";
						}
						$mstring .= "</select>";
		return $mstring;
	}else if($type == "text"){
		$sql = "SELECT mall_ix, mall_ename, mall_domain,mall_templete_type FROM ".TBL_SHOP_SHOPINFO." where mall_div in ('B') and mall_ix = '".$mall_ix."'  ";
		//echo $sql;
		$mdb->query($sql);
		if($mdb->total){
			$mdb->fetch();
			return $mdb->dt["mall_templete_type"];
		}else{
			return "전체";
		}

	}else{
		$mdb->query("SELECT mall_ix, mall_ename, mall_domain,mall_templete_type FROM ".TBL_SHOP_SHOPINFO." where mall_div in ('B')  ");
		$shopinfos = $mdb->fetchall("object");
		return $shopinfos;
	}

}

function GetDisplayDivision_estimate($mall_ix, $type = "array"){
	$mdb = new Database;


	if($type == "select"){
		$mdb->query("SELECT mall_ix, mall_ename, mall_domain FROM ".TBL_SHOP_SHOPINFO." where mall_div in ('B','S2')  ");
		$shopinfos = $mdb->fetchall("object");

		$mstring = "<select name='mall_ix' validation=false title='프론트 전시구분'>
						<option value=''>프론트 선택</option>";
						for($i=0; $i < count($shopinfos) ; $i++){
						$mstring .= "<option value='".$shopinfos[$i][mall_ix]."' ".($shopinfos[$i][mall_ix] == $mall_ix ? "selected":"").">".$shopinfos[$i][mall_ename]." (".$shopinfos[$i][mall_domain].")</option>";
						}
						$mstring .= "</select>";
		return $mstring;
	}else if($type == "text"){
		$sql = "SELECT mall_ix, mall_ename, mall_domain FROM ".TBL_SHOP_SHOPINFO." where mall_div in ('B','S2') and mall_ix = '".$mall_ix."'  ";
		//echo $sql;
		$mdb->query($sql);
		if($mdb->total){
			$mdb->fetch();
			return $mdb->dt["mall_ename"];
		}else{
			return "전체";
		}

	}else{
		$mdb->query("SELECT mall_ix, mall_ename, mall_domain FROM ".TBL_SHOP_SHOPINFO." where mall_div in ('B','S2')  ");
		$shopinfos = $mdb->fetchall("object");
		return $shopinfos;
	}

}



function AutoPenaltyInput($a, $b){

}

function getMyserviceInfo(){
	include_once($_SERVER["DOCUMENT_ROOT"]."/admin/logstory/class/sharedmemory.class");
	$shmop = new Shared("myservice_info");
	$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$_SESSION["admininfo"]["mall_data_root"]."/_shared/";
	$shmop->SetFilePath();
	$myservice_info = $shmop->getObjectForKey("myservice_info");
	$myservice_info = unserialize(urldecode($myservice_info));
	//print_r($myservice_info->CMS->SOHO->si_status);
	return $myservice_info;
}

function checkMyService($service_type,$solution_type, $service_use_value = ""){
	$service_mall_type = array("H","F","R","B");
	if(!in_array($_SESSION["admininfo"]["mall_type"],$service_mall_type)){
		//return true;
	}
	include_once($_SERVER["DOCUMENT_ROOT"]."/admin/logstory/class/sharedmemory.class");
	$shmop = new Shared("myservice_info");
	$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$_SESSION["admininfo"]["mall_data_root"]."/_shared/";
	$shmop->SetFilePath();
	$myservice_info = $shmop->getObjectForKey("myservice_info");
	$myservice_info = unserialize(urldecode($myservice_info));
	//echo $solution_type;
	//$__service_info = eval("\$myservice_info->".$service_type);
	$_service_info = (array)$myservice_info[$service_type];
	$myservice_info = (array)$_service_info[$solution_type];
	//print_r($myservice_info);
	//return $myservice_info->$service_type->$solution_type->si_status;
	//echo $myservice_info[si_status];
	//echo $myservice_info[service_unit_value];
	if($myservice_info[si_status] == "SI" && $myservice_info[sm_edate] >= date("Y-m-d")){
		if($service_use_value != ""){
			//echo $myservice_info[service_unit_value];
			if($myservice_info[service_unit_value] > $service_use_value){
				return true;
			}else{
				return false;
			}
		}else{
			return true;
		}
	}else{
		return false;
	}
	//return $myservice_info;
}



function MallstoryServicePopUp($popupinfo)
{

	if ($popupinfo[0] == ""){
		return "";
	}else{
			$mstring .= "<!--script type='text/javascript' language=javascript src='".$_SESSION["layout_config"]["mall_data_root"]."/templet/".$_SESSION["layout_config"]["mall_use_templete"]."/js/basic_head.js'></script-->\n
								<script Language='JavaScript'>\n";

			for($i=0;$i<count($popupinfo);$i++){

				if($popupinfo[$i]->popup_type == "W"){
					$mstring .= "if(!readCookie('Notice_".$popupinfo[$i]->popup_ix."')){\n";
					$mstring .= "	PoPWindow('/pop.php?no=".$popupinfo[$i]->popup_ix."&popup_type=W&popup_way=mallstory',".$popupinfo[$i]->popup_width.",".$popupinfo[$i]->popup_height.",".$popupinfo[$i]->popup_top.",".$popupinfo[$i]->popup_left.",'pop".$popupinfo[$i]->popup_ix."');\n";
					$mstring .= "}\n";
				}else{
					$mstring .= "if(!readCookie('Notice_".$popupinfo[$i]->popup_ix."')){\n";
					$mstring .= "document.write(\"<div id='divpop".$popupinfo[$i]->popup_ix."' style='position:absolute;left:".$popupinfo[$i]->popup_left."px;top:".$popupinfo[$i]->popup_top."px;z-index:200;visibility:visible;'>\");\n";
					$mstring .= "document.write(\"<IFRAME id=popup".$popupinfo[$i]->popup_ix." name=popup".$popupinfo[$i]->popup_ix." src='/pop.php?no=".$popupinfo[$i]->popup_ix."&popup_type=L&popup_way=mallstory' frameBorder=0 width=".$popupinfo[$i]->popup_width." height=".$popupinfo[$i]->popup_height." scrolling=no ></IFRAME>\");\n";

					$mstring .= "document.write(\"</div>\");\n";
					$mstring .= "}\n";
				}
			}
			$mstring .= "</script>\n";

			return $mstring;
	}
}


function getMyService($service_type,$solution_type){
	$service_mall_type = array("H","F","R","B");
	if(!in_array($_SESSION["admininfo"]["mall_type"],$service_mall_type)){
		//return true;
	}
	include_once($_SERVER["DOCUMENT_ROOT"]."/admin/logstory/class/sharedmemory.class");
	$shmop = new Shared("myservice_info");
	$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$_SESSION["admininfo"]["mall_data_root"]."/_shared/";
	$shmop->SetFilePath();
	$myservice_info = $shmop->getObjectForKey("myservice_info");
	$myservice_info = unserialize(urldecode($myservice_info));
	//echo $solution_type;
	//print_r($myservice_info);
	//$__service_info = eval("\$myservice_info->".$service_type);
	$_service_info = (array)$myservice_info[$service_type];
	$myservice_info = (array)$_service_info[$solution_type];

	return $myservice_info;

	//return $myservice_info;
}


function OrderByLink($title_text, $orderby, $ordertype="asc"){

	//20130715 hjy
	if(strlen($_SERVER["QUERY_STRING"])>0){
		$_query_string = str_replace(array("&mode=iframe","mode=iframe&"), "",$_SERVER["QUERY_STRING"]);
		$query_string_array=explode('&',$_query_string);


		for($i=0;$i<count($query_string_array);$i++){
			if(substr_count($query_string_array[$i],'orderby=')){
				$tmp_array[$i]="orderby=".$orderby;
			}elseif(substr_count($query_string_array[$i],'ordertype=')){
				$tmp_array[$i]="ordertype=".($_GET["ordertype"] == "desc" ? "asc":"desc");
			}else{
				$tmp_array[$i]=$query_string_array[$i];
			}
		}
		$query_string = implode('&',$tmp_array);
	}

	if(!substr_count($query_string,'ordertype=')){
		$query_string .= "&orderby=".$orderby."&ordertype=".($_GET["ordertype"] == "desc" ? "desc":"asc");
	}
	/*
	$query_string = str_replace("orderby=".$orderby."&","",$_SERVER["QUERY_STRING"]."") ;

	if(substr_count($query_string,'&')){
		$query_string = str_replace("ordertype=".$_GET["ordertype"]."&","",$query_string) ;
	}else{
		$query_string = str_replace("ordertype=".$_GET["ordertype"],"",$query_string) ;
	}
	//echo $query_string."<br><br>";
	$query_string = str_replace("view=".$_GET["view"]."&","",$query_string) ;
	$query_string = str_replace("&x=".$_GET["x"]."","",$query_string) ;

	//echo $query_string."<br><br>";
	if(!empty($query_string)){//20130715 hjy
		$query_string = $query_string."&orderby=".$orderby."&ordertype=".($_GET["ordertype"] == "desc" ? "asc":"desc");
	}else{
		$query_string = "orderby=".$orderby."&ordertype=".($_GET["ordertype"] == "desc" ? "asc":"desc");
	}
	*/
	//echo $query_string."<br><br>";

	if($orderby == $_GET["orderby"] || ($_GET["orderby"] == "" && $orderby == "regdate")){
		$mstring = "<a href='?".$query_string."'>".$title_text." <img src='/admin/images/ico_arrow_".(($ordertype == "asc") ? "up":"down").".png' border=0></a>";
	}else{
		$mstring = "<a href='?".$query_string."' style='text-decoration:underline;'>".$title_text."</a>";
	}

		return $mstring;
}



function mail_box($mode, $_INFO){
	global $admin_config;
	if ($mode == "insert"){
		$mail_title		= $_INFO["mail_subject"];
		$mail_text		= $_INFO["mail_content"];
		$mail_ix		= $_INFO["mail_ix"];
		$code			= $_INFO['code'];

		$mdb = new Database;

		if($save_mail){
			$disp = 1;
		}else{
			$disp = 0;
		}

		if($mail_ix == ""){
			$mdb->query("insert into shop_mail_box(mail_ix,mail_history,mail_code,mail_title,mail_text,disp,regdate) values('','".$disp."','".$code."','".$mail_title."','".$mail_text."','".$disp."',NOW())");

			$mdb->query("SELECT mail_ix FROM shop_mail_box WHERE mail_ix = LAST_INSERT_ID()");
			$mdb->fetch();
			$mail_ix = $mdb->dt[0];

			$data_text = $mail_text;
			$data_text_convert = $mail_text;
			$data_text_convert = str_replace("\\","",$data_text_convert);
			preg_match_all("|<IMG .*src=\"(.*)\".*>|U",$data_text_convert,$out, PREG_PATTERN_ORDER);


			$path = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/mail/";
			if(!is_dir($path)){
				mkdir($path, 0777);
			}

			$path = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/mail/$mail_ix/";

			if(substr_count($data_text,"<IMG") > 0){
				if(!is_dir($path)){
					mkdir($path, 0777);
					//chmod($path,0777)
				}
			}


			for($i=0;$i < count($out);$i++){
				for($j=0;$j < count($out[$i]);$j++){

					$img = returnImagePath($out[$i][$j]);
					$img = ClearText($img);


						copy("$img",$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/mail/$mail_ix/".returnFileName($img));
						if(substr_count($img,"$HTTP_HOST")>0){

							unlink(str_replace("http://$HTTP_HOST".$admin_config[mall_data_root]."/images/upfile/",$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/",$img));
						}

						$data_text = str_replace($img,$admin_config[mall_data_root]."/images/mail/$mail_ix/".returnFileName($img),$data_text);
				}
			}


			$mdb->query("UPDATE shop_mail_box SET mail_text = '$data_text' WHERE mail_ix='$mail_ix'");
			//echo("<script>top.location.href = 'mail_list.php';</script>");
		}

		return $mail_ix;
	}
}

function getStatusByType($status_type){
	/*	if($status_type == "bs_general"){
		//$status = array(ORDER_STATUS_INCOM_READY,ORDER_STATUS_INCOM_COMPLETE,ORDER_STATUS_DELIVERY_READY,ORDER_STATUS_DELIVERY_ING, ORDER_STATUS_DELIVERY_COMPLETE,ORDER_STATUS_WAREHOUSING_STANDYBY);
		$status = array(ORDER_STATUS_INCOM_READY,ORDER_STATUS_INCOM_COMPLETE,ORDER_STATUS_DELIVERY_READY,ORDER_STATUS_DELIVERY_ING, ORDER_STATUS_DELIVERY_COMPLETE,ORDER_STATUS_WAREHOUSING_STANDYBY, ORDER_STATUS_OVERSEA_WAREHOUSE_DELIVERY_READY, ORDER_STATUS_OVERSEA_WAREHOUSE_DELIVERY_ING, ORDER_STATUS_AIR_TRANSPORT_READY, ORDER_STATUS_AIR_TRANSPORT_ING);
	}else if($status_type == "general"){
		$status = array(ORDER_STATUS_INCOM_READY,ORDER_STATUS_INCOM_COMPLETE,ORDER_STATUS_DELIVERY_READY,ORDER_STATUS_DELIVERY_ING, ORDER_STATUS_DELIVERY_COMPLETE,ORDER_STATUS_WAREHOUSING_STANDYBY);
	}else if($status_type == "cancel"){
		$status = array(ORDER_STATUS_CANCEL_APPLY,ORDER_STATUS_CANCEL_COMPLETE,ORDER_STATUS_SOLDOUT_CANCEL);
	}else if($status_type == "return"){
		$status = array(ORDER_STATUS_RETURN_APPLY,ORDER_STATUS_RETURN_ING,ORDER_STATUS_RETURN_DELIVERY,ORDER_STATUS_RETURN_COMPLETE,ORDER_STATUS_RETURN_DEFER,ORDER_STATUS_RETURN_DENY,ORDER_STATUS_REFUND_APPLY,ORDER_STATUS_REFUND_COMPLETE);
	}else if($status_type == "exchange"){
		$status = array(ORDER_STATUS_EXCHANGE_APPLY,ORDER_STATUS_EXCHANGE_ING,ORDER_STATUS_EXCHANGE_DELIVERY,ORDER_STATUS_EXCHANGE_ACCEPT,ORDER_STATUS_EXCHANGE_AGAIN_DELIVERY,ORDER_STATUS_EXCHANGE_COMPLETE,ORDER_STATUS_EXCHANGE_DEFER,ORDER_STATUS_EXCHANGE_DENY);
	}*/
	if($status_type == "bs_general"){
		$status = array(ORDER_STATUS_INCOM_READY,ORDER_STATUS_INCOM_COMPLETE,ORDER_STATUS_DEFERRED_PAYMENT,ORDER_STATUS_DELIVERY_READY,ORDER_STATUS_DELIVERY_DELAY,ORDER_STATUS_DELIVERY_ING,ORDER_STATUS_DELIVERY_COMPLETE, ORDER_STATUS_BUY_FINALIZED, ORDER_STATUS_OVERSEA_WAREHOUSE_DELIVERY_READY, ORDER_STATUS_OVERSEA_WAREHOUSE_DELIVERY_ING, ORDER_STATUS_AIR_TRANSPORT_READY, ORDER_STATUS_AIR_TRANSPORT_ING);
	}else if($status_type == "general"){
		$status = array(ORDER_STATUS_INCOM_READY,ORDER_STATUS_INCOM_COMPLETE,ORDER_STATUS_DEFERRED_PAYMENT,ORDER_STATUS_DELIVERY_READY,ORDER_STATUS_DELIVERY_DELAY,ORDER_STATUS_DELIVERY_ING,ORDER_STATUS_DELIVERY_COMPLETE, ORDER_STATUS_BUY_FINALIZED);
	}else if($status_type == "incom_after_cancel"){
		$status = array(ORDER_STATUS_CANCEL_APPLY,ORDER_STATUS_CANCEL_COMPLETE,ORDER_STATUS_SOLDOUT_CANCEL);
	}else if($status_type == "incom_befor_cancel"){
		$status = array(ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE);
	}else if($status_type == "claim"){
		$status = array(ORDER_STATUS_EXCHANGE_APPLY,ORDER_STATUS_EXCHANGE_DENY,ORDER_STATUS_EXCHANGE_ING,ORDER_STATUS_EXCHANGE_DELIVERY,ORDER_STATUS_EXCHANGE_ACCEPT,ORDER_STATUS_EXCHANGE_DEFER,ORDER_STATUS_EXCHANGE_IMPOSSIBLE,ORDER_STATUS_EXCHANGE_COMPLETE,ORDER_STATUS_RETURN_APPLY,ORDER_STATUS_RETURN_DENY,ORDER_STATUS_RETURN_ING,ORDER_STATUS_RETURN_DELIVERY,ORDER_STATUS_RETURN_ACCEPT,ORDER_STATUS_RETURN_DEFER,ORDER_STATUS_RETURN_IMPOSSIBLE,ORDER_STATUS_RETURN_COMPLETE,ORDER_STATUS_EXCHANGE_READY);
	}
	return $status;
}

function getMySellerList($md_code, $return_type = ""){
	global $admininfo;

	$db2 = new Database;


	$sql = "SELECT ccd.company_id
			FROM common_company_detail ccd, common_seller_detail csd
			where ccd.company_id = csd.company_id and csd.md_code = '".$md_code."'
			and ccd.com_type = 'S' ";
	//echo $sql."<br>";
	$db2->query($sql);
	//$db2->fetch();
	//$sellers = $db2->getrows();
	$sellers = $db2->fetchall('object');
	//print_r($sellers);
	if(count($sellers) > 0){
		for($i=0;$i < count($sellers);$i++){
			if(!$seller_str){
				$seller_str .= "'".$sellers[$i][company_id]."'";
			}else{
				$seller_str .= ",'".$sellers[$i][company_id]."'";
			}
		}
	}else{
		$seller_str = "'".$_SESSION["admininfo"][company_id]."'";
	}
	if($return_type == "array"){
		return $sellers;
	}else{
		return $seller_str;
	}
}


function getLanguage($language_type="" , $property=""){
	global $admininfo;
	if($_SESSION["admininfo"]["charger_id"] == "forbiz" || $_SESSION["admininfo"]["charger_id"] == "hmpartner1" || $_SESSION["admininfo"]["charger_id"] == "hmpartner2"){
	$mstring = "<select name='language_type' id='language_type' $property validation='true' title='사용자 언어'>
			<option value=''>랭귀지선택</option>
			<option value='korean' ".($language_type == "korean" ? "selected":"").">한국어</option>
			<option value='english' ".($language_type == "english" ? "selected":"").">영어</option>
			<option value='chinese' ".($language_type == "chinese" ? "selected":"").">중국어</option>
			<option value='indonesian' ".($language_type == "indonesian" ? "selected":"").">인도네시아어</option>
			<option value='japan' ".($language_type == "japan" ? "selected":"").">일본어</option>
		 </select>";
	}else{
	$mstring = "<select name='language_type' $property>
			<option value=''>랭귀지선택</option>
			<option value='korean' ".($language_type == "korean" ? "selected":"").">한국어</option>
		 </select>";
	}
	return $mstring;

}

function getAuthTemplet($selected="" , $admin_level=9){
	global $admininfo;
	$mdb = new Database;

	if($_SESSION["admininfo"][admin_id] != "forbiz"){
		$where = " and auth_templet_ix != '1' ";
	}

	if($_SESSION["admininfo"][mall_type] == "F" || $_SESSION["admininfo"][mall_type] == "R"){
		$sql = 	"SELECT *
			FROM admin_auth_templet
			where disp=1 and auth_templet_level <= $admin_level and use_soho = 'Y' $where ";
	}else if($_SESSION["admininfo"][mall_type] == "O"){
		$sql = 	"SELECT *
			FROM admin_auth_templet
			where disp=1 and auth_templet_level <= $admin_level and use_openmarket = 'Y' $where ";
	}else if($_SESSION["admininfo"][mall_type] == "B"){
		$sql = 	"SELECT *
			FROM admin_auth_templet
			where disp=1 and auth_templet_level <= $admin_level and use_biz = 'Y' $where ";
	}else{
		$sql = 	"SELECT *
				FROM admin_auth_templet
				where disp=1 and auth_templet_level <= $admin_level $where  ";
	}

	//echo $sql;
	$mdb->query($sql);

	$mstring = "<select name='auth' id='auth' style='border:1px solid silver' validation='true'  title='사용자 권한'>";
	$mstring .= "<option value=''>권한 선택</option>";
	if($mdb->total){


		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			if($mdb->dt[auth_templet_ix] == $selected){
				$mstring .= "<option value='".$mdb->dt[auth_templet_ix]."' selected>".$mdb->dt[auth_templet_name]."</option>";
			}else{
				$mstring .= "<option value='".$mdb->dt[auth_templet_ix]."'>".$mdb->dt[auth_templet_name]."</option>";
			}
		}

	}
	$mstring .= "</select>";

	return $mstring;
}


function makeCompanyUserList($company_id, $object_id = "charger_ix", $department="", $code="",$property=""){

	$mdb = new Database;
	if($department){

		if($mdb->dbms_type == "oracle"){
			$sql = "select cmd.code , ps_name, AES_DECRYPT(cmd.name,'".$mdb->ase_encrypt_key."') as name
				from common_user cu left join common_company_detail ccd on cu.company_id = ccd.company_id and cu.company_id ='".$_SESSION["admininfo"]["company_id"]."'
				left join  common_member_detail cmd on cu.code = cmd.code
				left join shop_company_position cp on cmd.position = cp.ps_ix
				where cu.authorized = 'Y'
				and cu.company_id ='".$company_id."'
				and cmd.department = '".$department."' ";
		}else{
			$sql = "select cmd.code , ps_name, AES_DECRYPT(UNHEX(cmd.name),'".$mdb->ase_encrypt_key."') as name
				from common_user cu left join common_company_detail ccd on cu.company_id = ccd.company_id and cu.company_id ='".$_SESSION["admininfo"]["company_id"]."'
				left join  common_member_detail cmd on cu.code = cmd.code
				left join shop_company_position cp on cmd.position = cp.ps_ix
				where cu.authorized = 'Y'
				and cu.company_id ='".$company_id."'
				and cmd.department = '".$department."' ";
		}
		//echo $sql;
		$mdb->query($sql);
	}else{

		if($mdb->dbms_type == "oracle"){
			$sql = "select cmd.code , ps_name, AES_DECRYPT(cmd.name,'".$mdb->ase_encrypt_key."') as name
				from common_user cu left join common_company_detail ccd on cu.company_id = ccd.company_id and cu.company_id ='".$company_id."'
				left join  common_member_detail cmd on cu.code = cmd.code
				left join shop_company_position cp on cmd.position = cp.ps_ix
				where cu.authorized = 'Y'
				and cu.company_id ='".$company_id."'";
		}else{
			$sql = "select cmd.code , ps_name, AES_DECRYPT(UNHEX(cmd.name),'".$mdb->ase_encrypt_key."') as name
				from common_user cu left join common_company_detail ccd on cu.company_id = ccd.company_id and cu.company_id ='".$company_id."'
				left join  common_member_detail cmd on cu.code = cmd.code
				left join shop_company_position cp on cmd.position = cp.ps_ix
				where cu.authorized = 'Y'
				and cu.company_id ='".$company_id."'";
		}
		$mdb->query($sql);
	}
	//if ($mdb->total){
			$SelectString = "<Select name='".$object_id."'  $property>";//id='".str_replace("[]","",$object_id)."'
			$SelectString = $SelectString."<option value=''>담당자 선택</option>";
		for($i=0; $i < $mdb->total; $i++){
			$mdb->fetch($i);

			if(is_array($code)){
				if(in_array($mdb->dt[code],$code)){
					$SelectString = $SelectString."<option value='".$mdb->dt[code]."' ps_name='".$mdb->dt[ps_name]."' selected>".cut_str($mdb->dt[name],10)." </option>";
				}else{
					//$SelectString = $SelectString."<option value='".$mdb->dt[code]."' ps_name='".$mdb->dt[ps_name]."' >".$mdb->dt[name]." </option>";
				}
			}else{
				if($code == $mdb->dt[code]){
					$SelectString = $SelectString."<option value='".$mdb->dt[code]."' ps_name='".$mdb->dt[ps_name]."' selected>".cut_str($mdb->dt[name],10)." </option>";
				}else{
					$SelectString = $SelectString."<option value='".$mdb->dt[code]."' ps_name='".$mdb->dt[ps_name]."' >".cut_str($mdb->dt[name],10)." </option>";
				}
			}
		}
		$SelectString = $SelectString."</Select>";
	//}
	return $SelectString;
}

function makePositionSelectBox($mdb,$select_name, $ps_ix="",  $display_name = '전체보기', $property=""){
	global $admininfo;

	$mdb->query("SELECT * FROM shop_company_position where disp=1 and company_id = '".$_SESSION["admininfo"][company_id]."'  order by ps_level asc ");

	$mstring = "<select name='$select_name' id='$select_name' style='width:110px;font-size:12px;' $property>";
	$mstring .= "<option value=''>".$display_name."</option>";
	if($mdb->total){
		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			$mstring .= "<option value='".$mdb->dt[ps_ix]."' ".($mdb->dt[ps_ix] == $ps_ix ? "selected":"").">".$mdb->dt[ps_name]."</option>";
		}
	}else{
		$mstring .= "<option value=''>".$msg."</option>";
	}
	$mstring .= "</select>";

	return $mstring;
}

function makeDepartmentSelectBox($mdb,$select_name, $dp_ix="", $return_type="select", $display_name = '전체보기', $property=""){
	global $admininfo;
	$mdb->query("SELECT * FROM shop_company_department where disp=1 and company_id = '".$_SESSION["admininfo"][company_id]."' order by dp_level asc ");

	if($return_type == "select"){
		$mstring = "<select name='$select_name' $property>";//id='$select_name'
		$mstring .= "<option value=''>".$display_name."</option>";
		if($mdb->total){
			for($i=0;$i < $mdb->total;$i++){
				$mdb->fetch($i);
				$mstring .= "<option value='".$mdb->dt[dp_ix]."' ".($mdb->dt[dp_ix] == $dp_ix ? "selected":"").">".$mdb->dt[dp_name]."</option>";
			}
		}else{
			$mstring .= "<option value=''>".$msg."</option>";
		}
		$mstring .= "</select>";

		return $mstring;
	}else if($return_type == "array"){
		$datas = $mdb->fetchall();
		return $datas;
	}
}

function makeGroupSelectBox($mdb,$select_name,$gp_ix, $property=""){
	$mdb->query("SELECT * FROM ".TBL_SHOP_GROUPINFO." where disp=1 order by gp_level asc ");

	$mstring = "<select name='$select_name' id='$select_name' style='width:120px;font-size:12px;' $property>";
	$mstring .= "<option value=''>전체보기</option>";
	if($mdb->total){
		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			$mstring .= "<option value='".$mdb->dt[gp_ix]."' ".($mdb->dt[gp_ix] == $gp_ix ? "selected":"").">(".$mdb->dt[gp_level].")".$mdb->dt[gp_name]."".($mdb->dt[organization_name] ? "(".$mdb->dt[organization_name].")":"")."</option>";
		}
	}else{
		$mstring .= "<option value=''>".$msg."</option>";
	}
	$mstring .= "</select>";

	return $mstring;
}

function talkcategorySelectBox($mdb,$select_name,$tc_ix, $property=""){
	$mdb->query("SELECT * FROM shop_member_talk_category where disp=1 order by tc_ix asc ");

	$mstring = "<select name='$select_name' id='$select_name' style='width:210px;font-size:12px;' $property>";
	$mstring .= "<option value=''>전체보기</option>";
	if($mdb->total){
		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			$mstring .= "<option value='".$mdb->dt[tc_ix]."' ".($mdb->dt[tc_ix] == $tc_ix ? "selected":"").">".$mdb->dt[tc_name]."</option>";
		}
	}else{
		$mstring .= "<option value=''>".$msg."</option>";
	}
	$mstring .= "</select>";

	return $mstring;
}

function makeGroupLevelSelectBox($mdb,$select_name,$gp_level, $property=""){
	$mdb->query("SELECT distinct gp_level, gp_name FROM ".TBL_SHOP_GROUPINFO." where disp=1 order by gp_level asc ");

	$mstring = "<select name='$select_name' id='$select_name' style='width:80px;font-size:12px;' $property>";
	$mstring .= "<option value=''>전체보기</option>";
	if($mdb->total){
		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			$mstring .= "<option value='".$mdb->dt[gp_level]."' ".($mdb->dt[gp_level] == $gp_level ? "selected":"").">".$mdb->dt[gp_name]."</option>";
		}
	}else{
		$mstring .= "<option value=''>".$msg."</option>";
	}
	$mstring .= "</select>";

	return $mstring;
}

function getCategoryMultipleSelect($category_text ="기본카테고리 선택", $object_name="cid",$id="cid", $onchange_handler="", $depth=0, $cid="")
{
	$mdb = new Database;
	$tb = (defined('TBL_SNS_CATEGORY_INFO'))	?	TBL_SNS_CATEGORY_INFO:TBL_SHOP_CATEGORY_INFO;
	//echo "<script>alert('1')</script>";
	if($depth == 0 || $cid != ""){
		$sql = "SELECT * FROM ".$tb." where depth ='$depth' and cid LIKE '".substr($cid,0,($depth)*3)."%' and category_type = 'C' and category_use != '0'  order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 ";
		//echo $sql;
		$mdb->query($sql);
	}

	if ($mdb->total){
		$mstring = "<Select name='$object_name' depth='$depth' $onchange_handler id='$id' class='$id' validation=false multiple style='1px solid silver;height:155px;width:100%;'>\n";
		$mstring = $mstring."<option value=''>$category_text</option>\n";
		for($i=0; $i < $mdb->total; $i++){
			$mdb->fetch($i);
			//if(substr_count($cid,substr($mdb->dt[cid],0,$mdb->dt[depth]+1))){
			if(substr($cid,0,($depth+1)*3) == substr($mdb->dt[cid],0,($depth+1)*3)){

				$mstring = $mstring."<option value='".$mdb->dt[cid]."' selected>".$mdb->dt[cname]."</option>\n";
				//$mstring = $mstring."<option value='".$mdb->dt[cid]."' selected>".substr($cid,0,($depth+1)*3)." == ".substr($mdb->dt[cid],0,($depth+1)*3)."</option>\n";
			}else{
				$mstring = $mstring."<option value='".$mdb->dt[cid]."' >".$mdb->dt[cname]."</option>\n";
				//$mstring = $mstring."<option value='".$mdb->dt[cid]."' >".substr($cid,0,($depth+1)*3)." == ".substr($mdb->dt[cid],0,($depth+1)*3)."</option>\n";
			}
		}
	}else{
		$mstring = "<Select name='$object_name' depth='$depth' $onchange_handler id='$id' class='$id' validation=false multiple  style='border:1px solid silver;height:155px;width:100%;'>\n";
		$mstring = $mstring."<option value=''> $category_text</option>\n";
	}

	$mstring = $mstring."</Select>\n";

	return $mstring;
}

function getCategoryMinishopMultipleSelect($category_text ="기본카테고리 선택", $object_name="cid",$id="cid", $onchange_handler="", $depth=0, $cid="")
{
	$mdb = new Database;
	$tb = "shop_minishop_category_info";
	//echo "<script>alert('1')</script>";
	if($depth == 0 || $cid != ""){
		$sql = "SELECT * FROM ".$tb." where depth ='$depth' and cid LIKE '".substr($cid,0,($depth)*3)."%' and category_type = 'C' and category_use != '0' and company_id = '".$_SESSION["admininfo"]['company_id']."' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 ";
		//echo $sql;
		$mdb->query($sql);
	}

	if ($mdb->total){
		$mstring = "<Select name='$object_name' depth='$depth' $onchange_handler id='$id' class='$id' validation=false multiple style='1px solid silver;height:155px;width:100%;'>\n";
		$mstring = $mstring."<option value=''>$category_text</option>\n";
		for($i=0; $i < $mdb->total; $i++){
			$mdb->fetch($i);
			//if(substr_count($cid,substr($mdb->dt[cid],0,$mdb->dt[depth]+1))){
			if(substr($cid,0,($depth+1)*3) == substr($mdb->dt[cid],0,($depth+1)*3)){

				$mstring = $mstring."<option value='".$mdb->dt[cid]."' selected>".$mdb->dt[cname]."</option>\n";
				//$mstring = $mstring."<option value='".$mdb->dt[cid]."' selected>".substr($cid,0,($depth+1)*3)." == ".substr($mdb->dt[cid],0,($depth+1)*3)."</option>\n";
			}else{
				$mstring = $mstring."<option value='".$mdb->dt[cid]."' >".$mdb->dt[cname]."</option>\n";
				//$mstring = $mstring."<option value='".$mdb->dt[cid]."' >".substr($cid,0,($depth+1)*3)." == ".substr($mdb->dt[cid],0,($depth+1)*3)."</option>\n";
			}
		}
	}else{
		$mstring = "<Select name='$object_name' depth='$depth' $onchange_handler id='$id' class='$id' validation=false multiple  style='border:1px solid silver;height:155px;width:100%;'>\n";
		$mstring = $mstring."<option value=''> $category_text</option>\n";
	}

	$mstring = $mstring."</Select>\n";

	return $mstring;
}

function makeGroupCheckButton($mdb,$select_name,$gp_ix, $property=""){
	$mdb->query("SELECT * FROM ".TBL_SHOP_GROUPINFO." where disp=1 order by gp_level asc ");


	if($mdb->total){
		//$mstring .= "<input type=checkbox name='".$select_name."' id='".str_replace("[]","",$select_name)."_all' value='' ".CompareReturnValue("",$gp_ix,"checked")." >";
		//$mstring .= "<label for='".str_replace("[]","",$select_name)."_all'>전체</label> ";

		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			$mstring .= " <input type=checkbox name='".$select_name."' id='".str_replace("[]","",$select_name)."_".$mdb->dt[gp_ix]."' value='".$mdb->dt[gp_ix]."' ".CompareReturnValue($mdb->dt[gp_ix],$gp_ix,"checked")." >";
			$mstring .= "<label for='".str_replace("[]","",$select_name)."_".$mdb->dt[gp_ix]."'>".$mdb->dt[gp_name]."".($mdb->dt[organization_name] ? "(".$mdb->dt[organization_name].")":"")."</label>";
		}
	}

	return $mstring;
}

function getCategoryList($category_text ="기본카테고리 선택", $object_name="cid",$id="cid", $onchange_handler="", $depth=0, $cid="")
{
	$mdb = new Database;
	$tb = (defined('TBL_SNS_CATEGORY_INFO'))	?	TBL_SNS_CATEGORY_INFO:TBL_SHOP_CATEGORY_INFO;
	if($depth == 0 || $cid != ""){
		$sql = "SELECT * FROM $tb where depth ='$depth' and cid LIKE '".substr($cid,0,($depth)*3)."%' and category_type = 'C' and category_use = '1'  order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 ";
		//echo $sql;
		$mdb->query($sql);
	}

	if ($mdb->total){
		$mstring = "<Select name='$object_name' depth='$depth' $onchange_handler id='$id' validation=true style='width:165px;font-size:12px;' title='카테고리'>\n";
		$mstring = $mstring."<option value=''>$category_text</option>\n";
		for($i=0; $i < $mdb->total; $i++){
			$mdb->fetch($i);
			//if(substr_count($cid,substr($mdb->dt[cid],0,$mdb->dt[depth]+1))){
			if(substr($cid,0,($depth+1)*3) == substr($mdb->dt[cid],0,($depth+1)*3)){

				$mstring = $mstring."<option value='".$mdb->dt[cid]."' selected>".$mdb->dt[cname]."</option>\n";
				//$mstring = $mstring."<option value='".$mdb->dt[cid]."' selected>".substr($cid,0,($depth+1)*3)." == ".substr($mdb->dt[cid],0,($depth+1)*3)."</option>\n";
			}else{
				$mstring = $mstring."<option value='".$mdb->dt[cid]."' >".$mdb->dt[cname]."</option>\n";
				//$mstring = $mstring."<option value='".$mdb->dt[cid]."' >".substr($cid,0,($depth+1)*3)." == ".substr($mdb->dt[cid],0,($depth+1)*3)."</option>\n";
			}
		}
	}else{
		$mstring = "<Select name='$object_name' depth='$depth' $onchange_handler id='$id' validation=false  style='width:140px;'>\n";
		$mstring = $mstring."<option value=''> $category_text</option>\n";
	}

	$mstring = $mstring."</Select>\n";

	return $mstring;
}

function getCategoryList3($category_text ="기본카테고리 선택", $object_name="cid", $onchange_handler="", $depth=0, $cid="", $id="cid")
{
	$mdb = new Database;
	$tb = (defined('TBL_SNS_CATEGORY_INFO'))	?	TBL_SNS_CATEGORY_INFO:TBL_SHOP_CATEGORY_INFO;
	//echo "<script>alert('1')</script>";
	if($depth == 0 || $cid != ""){
		$sql = "SELECT * FROM ".$tb." where depth ='$depth' and cid LIKE '".substr($cid,0,($depth)*3)."%' and category_type = 'C' and category_use != '0' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 ";
		$mdb->query($sql);
	}

	if ($mdb->total){
		$mstring = "<Select name='$object_name' id='$id' depth='$depth' $onchange_handler style='width:140px;font-size:12px;'>\n";
		$mstring = $mstring."<option value=''>$category_text</option>\n";
		for($i=0; $i < $mdb->total; $i++){
			$mdb->fetch($i);
			//if(substr_count($cid,substr($mdb->dt[cid],0,$mdb->dt[depth]+1))){
			//echo substr($cid,0,($depth+1)*3)." ".substr($mdb->dt[cid],0,($depth+1)*3)."<BR>";
			if(substr($cid,0,($depth+1)*3) == substr($mdb->dt[cid],0,($depth+1)*3)){

				$mstring = $mstring."<option value='".$mdb->dt[cid]."' selected>".$mdb->dt[cname]."</option>\n";
				//$mstring = $mstring."<option value='".$mdb->dt[cid]."' selected>".substr($cid,0,($depth+1)*3)." == ".substr($mdb->dt[cid],0,($depth+1)*3)."</option>\n";
			}else{
				$mstring = $mstring."<option value='".$mdb->dt[cid]."' >".$mdb->dt[cname]."</option>\n";
				//$mstring = $mstring."<option value='".$mdb->dt[cid]."' >".substr($cid,0,($depth+1)*3)." == ".substr($mdb->dt[cid],0,($depth+1)*3)."</option>\n";
			}
		}
	}else{
		$mstring = "<Select name='$object_name' id='$id' depth='$depth' $onchange_handler validation=false  style='width:140px;font-size:12px;'>\n";
		$mstring = $mstring."<option value=''> $category_text</option>\n";
	}

	$mstring = $mstring."</Select>\n";

	return $mstring;
}

function getInventoryCategoryList($category_text ="기본카테고리 선택", $object_name="cid", $onchange_handler="", $depth=0, $cid="",$validation="false")
{
	$mdb = new Database;
	//$tb = (defined('TBL_SNS_CATEGORY_INFO'))	?	TBL_SNS_CATEGORY_INFO:TBL_SHOP_CATEGORY_INFO;
	$tb = $_SESSION['admin_config']["mall_inventory_category_div"]=="P"	?	TBL_SHOP_CATEGORY_INFO:"inventory_category_info";
	//echo "<script>alert('1')</script>";
	if($depth == 0 || $cid != ""){
		$sql = "SELECT * FROM ".$tb." where depth ='$depth' and cid LIKE '".substr($cid,0,($depth)*3)."%' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 ";
		//echo $sql;
		$mdb->query($sql);
	}

	if ($mdb->total){
		$mstring = "<Select name='$object_name' depth='$depth' $onchange_handler style='min-width:140px;font-size:12px;' validation='$validation'>\n";
		$mstring = $mstring."<option value=''>$category_text</option>\n";
		for($i=0; $i < $mdb->total; $i++){
			$mdb->fetch($i);
			//if(substr_count($cid,substr($mdb->dt[cid],0,$mdb->dt[depth]+1))){
			if(substr($cid,0,($depth+1)*3) == substr($mdb->dt[cid],0,($depth+1)*3)){

				$mstring = $mstring."<option value='".$mdb->dt[cid]."' selected>".$mdb->dt[cname]."</option>\n";
				//$mstring = $mstring."<option value='".$mdb->dt[cid]."' selected>".substr($cid,0,($depth+1)*3)." == ".substr($mdb->dt[cid],0,($depth+1)*3)."</option>\n";
			}else{
				$mstring = $mstring."<option value='".$mdb->dt[cid]."' >".$mdb->dt[cname]."</option>\n";
				//$mstring = $mstring."<option value='".$mdb->dt[cid]."' >".substr($cid,0,($depth+1)*3)." == ".substr($mdb->dt[cid],0,($depth+1)*3)."</option>\n";
			}
		}
	}else{
		$mstring = "<Select name='$object_name' depth='$depth' $onchange_handler validation=false  style='width:140px;font-size:12px;' validation='$validation'>\n";
		$mstring = $mstring."<option value=''> $category_text</option>\n";
	}

	$mstring = $mstring."</Select>\n";

	return $mstring;
}

function getInventoryCategoryListMultiple($category_text ="기본카테고리 선택", $object_name="cid", $onchange_handler="", $depth=0, $cid="",$validation="false")
{
	$mdb = new Database;
	//$tb = (defined('TBL_SNS_CATEGORY_INFO'))	?	TBL_SNS_CATEGORY_INFO:TBL_SHOP_CATEGORY_INFO;
	$tb = $_SESSION['admin_config']["mall_inventory_category_div"]=="P"	?	TBL_SHOP_CATEGORY_INFO:"inventory_category_info";
	//echo "<script>alert('1')</script>";
	if($depth == 0 || $cid != ""){
		$sql = "SELECT * FROM ".$tb." where depth ='$depth' and cid LIKE '".substr($cid,0,($depth)*3)."%' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 ";
		//echo $sql;
		$mdb->query($sql);
	}

	if ($mdb->total){
		$mstring = "<Select name='$object_name' class='cid' depth='$depth' $onchange_handler style='min-width:216px;height:105px;font-size:12px;' multiple validation='$validation'>\n";
		$mstring = $mstring."<option value=''>$category_text</option>\n";
		for($i=0; $i < $mdb->total; $i++){
			$mdb->fetch($i);
			//if(substr_count($cid,substr($mdb->dt[cid],0,$mdb->dt[depth]+1))){
			if(substr($cid,0,($depth+1)*3) == substr($mdb->dt[cid],0,($depth+1)*3)){

				$mstring = $mstring."<option value='".$mdb->dt[cid]."' selected>".$mdb->dt[cname]."</option>\n";
				//$mstring = $mstring."<option value='".$mdb->dt[cid]."' selected>".substr($cid,0,($depth+1)*3)." == ".substr($mdb->dt[cid],0,($depth+1)*3)."</option>\n";
			}else{
				$mstring = $mstring."<option value='".$mdb->dt[cid]."' >".$mdb->dt[cname]."</option>\n";
				//$mstring = $mstring."<option value='".$mdb->dt[cid]."' >".substr($cid,0,($depth+1)*3)." == ".substr($mdb->dt[cid],0,($depth+1)*3)."</option>\n";
			}
		}
	}else{
		$mstring = "<Select name='$object_name' depth='$depth' class='cid' $onchange_handler validation=false  style='width:216px;height:105px;font-size:12px;' multiple validation='$validation'>\n";
		$mstring = $mstring."<option value=''> $category_text</option>\n";
	}

	$mstring = $mstring."</Select>\n";

	return $mstring;
}

function getbasicCompanyList($category_text ="기본카테고리 선택", $object_name="cid", $onchange_handler="", $depth=0, $cid="",$validation="false")
{
	$mdb = new Database;
	//$tb = (defined('TBL_SNS_CATEGORY_INFO'))	?	TBL_SNS_CATEGORY_INFO:TBL_SHOP_CATEGORY_INFO;
	$tb = $_SESSION['admin_config']["mall_inventory_category_div"]=="P"	?	TBL_SHOP_CATEGORY_INFO:"inventory_category_info";
	//echo "<script>alert('1')</script>";
	if($depth == 0 || $cid != ""){
		$sql = "SELECT * FROM ".$tb." where depth ='$depth' and cid LIKE '".substr($cid,0,($depth)*3)."%' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 ";
		//echo $sql;
		$mdb->query($sql);
	}

	if ($mdb->total){
		$mstring = "<Select name='$object_name' depth='$depth' $onchange_handler style='width:140px;font-size:12px;' validation='$validation'>\n";
		$mstring = $mstring."<option value=''>$category_text</option>\n";
		for($i=0; $i < $mdb->total; $i++){
			$mdb->fetch($i);
			//if(substr_count($cid,substr($mdb->dt[cid],0,$mdb->dt[depth]+1))){
			if(substr($cid,0,($depth+1)*3) == substr($mdb->dt[cid],0,($depth+1)*3)){

				$mstring = $mstring."<option value='".$mdb->dt[cid]."' selected>".$mdb->dt[cname]."</option>\n";
				//$mstring = $mstring."<option value='".$mdb->dt[cid]."' selected>".substr($cid,0,($depth+1)*3)." == ".substr($mdb->dt[cid],0,($depth+1)*3)."</option>\n";
			}else{
				$mstring = $mstring."<option value='".$mdb->dt[cid]."' >".$mdb->dt[cname]."</option>\n";
				//$mstring = $mstring."<option value='".$mdb->dt[cid]."' >".substr($cid,0,($depth+1)*3)." == ".substr($mdb->dt[cid],0,($depth+1)*3)."</option>\n";
			}
		}
	}else{
		$mstring = "<Select name='$object_name' depth='$depth' $onchange_handler validation=false  style='width:140px;font-size:12px;' validation='$validation'>\n";
		$mstring = $mstring."<option value=''> $category_text</option>\n";
	}

	$mstring = $mstring."</Select>\n";

	return $mstring;
}

function getbasicWorderList($category_text ="기본카테고리 선택", $object_name="cid", $onchange_handler="", $depth=0, $cid="",$validation="false")
{
	$mdb = new Database;
	//$tb = (defined('TBL_SNS_CATEGORY_INFO'))	?	TBL_SNS_CATEGORY_INFO:TBL_SHOP_CATEGORY_INFO;
	$tb = $_SESSION['admin_config']["mall_inventory_category_div"]=="P"	?	TBL_SHOP_CATEGORY_INFO:"inventory_category_info";
	//echo "<script>alert('1')</script>";
	if($depth == 0 || $cid != ""){
		$sql = "SELECT * FROM ".$tb." where depth ='$depth' and cid LIKE '".substr($cid,0,($depth)*3)."%' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 ";
		//echo $sql;
		$mdb->query($sql);
	}

	if ($mdb->total){
		$mstring = "<Select name='$object_name' depth='$depth' $onchange_handler style='width:140px;font-size:12px;' validation='$validation'>\n";
		$mstring = $mstring."<option value=''>$category_text</option>\n";
		for($i=0; $i < $mdb->total; $i++){
			$mdb->fetch($i);
			//if(substr_count($cid,substr($mdb->dt[cid],0,$mdb->dt[depth]+1))){
			if(substr($cid,0,($depth+1)*3) == substr($mdb->dt[cid],0,($depth+1)*3)){

				$mstring = $mstring."<option value='".$mdb->dt[cid]."' selected>".$mdb->dt[cname]."</option>\n";
				//$mstring = $mstring."<option value='".$mdb->dt[cid]."' selected>".substr($cid,0,($depth+1)*3)." == ".substr($mdb->dt[cid],0,($depth+1)*3)."</option>\n";
			}else{
				$mstring = $mstring."<option value='".$mdb->dt[cid]."' >".$mdb->dt[cname]."</option>\n";
				//$mstring = $mstring."<option value='".$mdb->dt[cid]."' >".substr($cid,0,($depth+1)*3)." == ".substr($mdb->dt[cid],0,($depth+1)*3)."</option>\n";
			}
		}
	}else{
		$mstring = "<Select name='$object_name' depth='$depth' $onchange_handler validation=false  style='width:140px;font-size:12px;' validation='$validation'>\n";
		$mstring = $mstring."<option value=''> $category_text</option>\n";
	}

	$mstring = $mstring."</Select>\n";

	return $mstring;
}


function codeName($quick){
	$mdb = new Database;

	if(!trim($quick)) return '';

	$sql="select code_name from shop_code where code_ix = '$quick' and code_gubun='02'";
	$mdb->query($sql);
	$mdb->fetch();
	return $mdb->dt[code_name];
}

function Company_basic($company_id, $dispaly_type="wharehouse", $validation="false",$load_js){//behavior: url('../js/selectbox.htc');

	global $admininfo, $HTTP_URL;
	global $admin_config;

	if(!$admin_config[mall_use_multishop]){
		return "";
	}
	
	$where = "";
	if($_SESSION["admininfo"][admin_level] != 9){
		$where .= " and ccd.company_id = '".$_SESSION["admininfo"]['company_id']."' ";
	}

	if($dispaly_type == "wharehouse"){
		$where .= " and ccd.is_wharehouse = '1' ";
	}
	$mdb = new Database;

	if($_SESSION["admininfo"][mem_type] == "MD"){
		$sql = "SELECT count(*) as total
				FROM ".TBL_COMMON_COMPANY_DETAIL." ccd
				where  ccd.com_type in ('A','G','S') and ccd.seller_auth = 'Y'
				$where
				and ccd.company_id in (".getMySellerList($_SESSION["admininfo"][charger_ix]).") ";
		//echo $sql;
		$mdb->query($sql);
		$mdb->fetch();
		$total = $mdb->dt[total];
	}else{
		$sql = "SELECT count(*) as total FROM ".TBL_COMMON_COMPANY_DETAIL." ccd  where  ccd.com_type in ('A','G','S') $where  and ccd.seller_auth = 'Y'   ";
		//echo $sql;
		$mdb->query($sql);
		$total = $mdb->dt[total];
	}
	//echo MAX_VENDOR_VIEW_CNT;
	if($total < constant("MAX_VENDOR_VIEW_CNT")){

		if($_SESSION["admininfo"][mem_type] == "MD"){
			$sql = "SELECT com_name, ccd.company_id
					FROM ".TBL_COMMON_COMPANY_DETAIL." ccd
					where  ccd.com_type in ('A','G','S') and ccd.seller_auth = 'Y'
					$where
					and ccd.company_id in (".getMySellerList($_SESSION["admininfo"][charger_ix]).")
					order by com_name asc";
			//echo $sql;
			$mdb->query($sql);
		}else{
			$sql = "SELECT com_name, ccd.company_id FROM ".TBL_COMMON_COMPANY_DETAIL." ccd  where  ccd.com_type in ('A','G','S') $where  and ccd.seller_auth = 'Y'  order by com_name asc";
			//echo $sql;
			$mdb->query($sql);
		}

		$mstring ="<div class='ui-widget' style='margin:5px 0px;'>
			<select id='company_id' name='company_id' $load_js style=\"$dispaly_type height: 20px; width: 150px;font-size:12px;\" validation='$validation' title='입점업체'>";
		$mstring .="<option value=''>전체보기</option>";

		for($i=0;$i<$mdb->total;$i++){
		$mdb->fetch($i);
			if($company_id == $mdb->dt[company_id]){
				$mstring .="<option value='".$mdb->dt[company_id]."' selected>".$mdb->dt[com_name]."</option>";
			}else{
				$mstring .="<option value='".$mdb->dt[company_id]."'>".$mdb->dt[com_name]."</option>";
			}
		}

		$mstring .="</select></div>";

		$mstring .= "
			<script>
			$(function() {
				//$( '#company_id').combobox();

			});
			</script>";
	}else{

	$mstring =	"
				<table cellpadding=0 cellspacing=0>
				<tr>
					<td><input type=hidden class='textbox' name='company_id' id='company_id'  value='".$company_id."' ></td>
					<td><input type=text class='textbox' name='com_name' id='com_name' value='".$com_name."' onclick=\"ShowModalWindow('../seller_search.php?code=".$db->dt[code]."',600,380,'seller_search')\"  style='width:130px;' readonly></td>
					<td style='padding-left:5px;'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"ShowModalWindow('../seller_search.php?code=".$db->dt[code]."',600,380,'seller_search')\"  style='cursor:pointer;'></td>
				</tr>
			</table>";
	}

	return $mstring;
}


function CompanyList2($company_id, $dispaly_type="", $validation="false"){//behavior: url('../js/selectbox.htc');
	global $admininfo, $HTTP_URL;
	global $admin_config;

	if(!$admin_config[mall_use_multishop]){
		return "";
	}

	if($_SESSION["admininfo"][admin_level] != 9){
		return "";
	}
	$mdb = new Database;

	if($_SESSION["admininfo"][mem_type] == "MD"){
		$sql = "SELECT count(*) as total
				FROM ".TBL_COMMON_COMPANY_DETAIL." ccd
				where  ccd.com_type in ('S','A') and ccd.seller_auth = 'Y'
				and ccd.company_id in (".getMySellerList($_SESSION["admininfo"][charger_ix]).") ";
		//echo $sql;
		$mdb->query($sql);
		$mdb->fetch();
		$total = $mdb->dt[total];
	}else{
		$sql = "SELECT count(*) as total FROM ".TBL_COMMON_COMPANY_DETAIL." ccd  where  ccd.com_type in ('S','A') and ccd.seller_auth = 'Y'   ";
		//echo $sql;
		$mdb->query($sql);
		$total = $mdb->dt[total];
	}
	//echo MAX_VENDOR_VIEW_CNT;
	if($total < constant("MAX_VENDOR_VIEW_CNT")){
		if($_SESSION["admininfo"][mem_type] == "MD"){
			$sql = "SELECT com_name, ccd.company_id
					FROM ".TBL_COMMON_COMPANY_DETAIL." ccd
					where  ccd.com_type in ('S','A') and ccd.seller_auth = 'Y'
					and ccd.company_id in (".getMySellerList($_SESSION["admininfo"][charger_ix]).")
					order by com_name asc";
			//echo $sql;
			$mdb->query($sql);
		}else{
			$sql = "SELECT com_name, ccd.company_id FROM ".TBL_COMMON_COMPANY_DETAIL." ccd  where  ccd.com_type in ('S','A') and ccd.seller_auth = 'Y'  order by com_name asc";
			//echo $sql;
			$mdb->query($sql);
		}

		$mstring ="<div class='ui-widget' style='margin:5px 0px;'><select id='company_id' name='company_id' style=\"$dispaly_type height: 20px; width: 150px;font-size:12px;\" validation='$validation' title='입점업체'>";
		$mstring .="<option value=''>전체보기</option>";
		for($i=0;$i<$mdb->total;$i++){
		$mdb->fetch($i);
			if($company_id == $mdb->dt[company_id]){
				$mstring .="<option value='".$mdb->dt[company_id]."' selected>".$mdb->dt[com_name]."</option>";
			}else{
				$mstring .="<option value='".$mdb->dt[company_id]."'>".$mdb->dt[com_name]."</option>";
			}
		}
		$mstring .="</select></div>";

		/*$mstring .= "
			<script>
			$(function() {
				$( '#company_id').combobox();
			});
			</script>";*///프로모션 상품 검색 레이어 띄울 때 오류 발생함 대표님과 상의 후 주석 처리 kbk 13/05/09
	}else{

	$mstring =	"
						<table cellpadding=0 cellspacing=0>
						<tr>
							<td><input type=hidden class='textbox' name='company_id' id='company_id'  value='".$company_id."' ></td>
							<td><input type=text class='textbox' name='com_name' id='com_name' value='".$com_name."' onclick=\"ShowModalWindow('../seller_search.php?code=".$db->dt[code]."',600,380,'seller_search')\"  style='width:130px;' readonly></td>
							<td style='padding-left:5px;'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"ShowModalWindow('../seller_search.php?code=".$db->dt[code]."',600,380,'seller_search')\" class='com_search_btn'  style='cursor:pointer;'></td>
						</tr>
					</table>";
	}

	return $mstring;
}


function companyAuthList($company_id , $property="",$select_id='company_id',$input_id='company_id',$input_name='com_name',$type='list'){
	global $admininfo, $HTTP_URL;
	global $admin_config;

	if(!$admin_config[mall_use_multishop]){
		return "";
	}

	if($_SESSION["admininfo"][admin_level] != 9){
		return "";
	}
	$mdb = new Database;
	$db = new Database;

	//echo $_SESSION["admininfo"]["mem_type"];
	if($_SESSION["admininfo"]["mem_type"] == "MD"){
		$addWhere = " and ccd.company_id in (".getMySellerList($_SESSION["admininfo"][charger_ix]).") ";
	}

	$sql = "SELECT
				count(*) as total
			FROM
				".TBL_COMMON_COMPANY_DETAIL." as ccd
				inner join ".TBL_COMMON_SELLER_DETAIL." as csd on (ccd.company_id = csd.company_id)
				left join ".TBL_COMMON_USER." as cu on (csd.charge_code = cu.code)
			where
				cu.company_id = ccd.company_id
				and ccd.com_type in ('S')
				and ccd.seller_auth = 'Y' $addWhere";
	$mdb->query($sql);
	$mdb->fetch();
	$total = $mdb->dt[total] + 1;
	//echo constant("MAX_VENDOR_VIEW_CNT")."<br>";

	if($total < constant("MAX_VENDOR_VIEW_CNT")){

		$sql = "SELECT
					ccd.com_name,
					ccd.company_id
				FROM
					".TBL_COMMON_COMPANY_DETAIL." as ccd
					inner join ".TBL_COMMON_SELLER_DETAIL." as csd on (ccd.company_id = csd.company_id)
					left join ".TBL_COMMON_USER." as cu on (csd.charge_code = cu.code)
				where
					cu.company_id = ccd.company_id
					and ccd.com_type in ('S')
					and ccd.seller_auth = 'Y' $addWhere
				union
				select
						ccd2.com_name,ccd2.company_id
				from
					".TBL_COMMON_COMPANY_DETAIL." as ccd2
				where
					ccd2.company_id = '".$admininfo[company_id]."'
					order by com_name asc";

		$mdb->query($sql);

		$mstring ="<select name='admin' id='company_id' style=\"$dispaly_type height: 20px; width: 150px;font-size:12px;\" $property >";
		$mstring .="<option value=''>입점업체 선택</option>";
		for($i=0;$i<$mdb->total;$i++){
		$mdb->fetch($i);
			if($company_id == $mdb->dt[company_id]){
				$mstring .="<option value='".$mdb->dt[company_id]."' selected>".$mdb->dt[com_name]."</option>";
			}else{
				$mstring .="<option value='".$mdb->dt[company_id]."'>".$mdb->dt[com_name]."</option>";
			}
		}
		$mstring .="</select>";
		$mstring .= "
		<script>
		$(function() {
			$( '#company_id').combobox();
		});
		</script>";

	}else{

		$sql = "select company_id from common_company_detail where com_type = 'A'";
		$db->query($sql);
		$db->fetch();
		$ori_company_id = $db->dt[company_id];

		if($company_id) $where = "and ccd.company_id = '".$company_id."' ";

		$sql = "select
						ccd2.com_name,ccd2.company_id
				from
					".TBL_COMMON_COMPANY_DETAIL." as ccd2
				where
					ccd2.company_id = '".$company_id."'
					order by com_name asc";
		$db->query($sql);
		$db->fetch();
		$com_name = $db->dt[com_name];

		$sql = "SELECT
					ccd.com_name,
					ccd.company_id
				FROM
					".TBL_COMMON_COMPANY_DETAIL." as ccd
					inner join ".TBL_COMMON_SELLER_DETAIL." as csd on (ccd.company_id = csd.company_id)
					left join ".TBL_COMMON_USER." as cu on (csd.charge_code = cu.code)
				where
					cu.company_id = ccd.company_id
					and ccd.com_type in ('S')
					and ccd.seller_auth = 'Y' $addWhere
				union
				select
						ccd2.com_name,ccd2.company_id
				from
					".TBL_COMMON_COMPANY_DETAIL." as ccd2
				where
					ccd2.company_id = '".$company_id."'
					order by com_name asc";

		$mdb->query($sql);
		$mdb->fetch();
		$mstring =	"
					<table cellpadding=0 cellspacing=0>
					<tr>
						<td><input type=hidden class='textbox' name='ori_company_id' id='ori_company_id'  value='".$ori_company_id."' ></td>
						<td><input type=hidden class='textbox' name='".$input_id."' id='".$select_id."'  value='".$company_id."' ></td>
						<td><input type=text class='textbox point_color' name='".$input_name."' id='".$input_name."' value='".$com_name."'  ".$property." onclick=\"ShowModalWindow('../seller_search.php?code=".$db->dt[code]."&select_id=$select_id&input_id=$select_id&input_name=$input_name&type=$type',650,550,'seller_search')\"   style='width:140px;' readonly></td>
						<td style='padding-left:5px;'>
						<img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle class='com_search_btn' onclick=\"ShowModalWindow('../seller_search.php?code=".$db->dt[code]."&select_id=$select_id&input_id=$select_id&input_name=$input_name&type=$type',650,550,'seller_search')\"  style='cursor:pointer;'>
						</td>
						<td>
						<img src='../images/btn_x.gif' style='cursor:pointer;margin:0px 3px;' onclick=\"$(this).parent().parent().find('#".$input_id."').val('');$(this).parent().parent().find('#".$input_name."').val('');\">
						</td>
					</tr>
				</table>";
	}


	return $mstring;
}


function TradeCompanyList($company_id , $property="",$select_id='trade_admin',$input_id='trade_admin',$input_name='trade_name'){

	global $admininfo, $HTTP_URL;
	global $admin_config;

	if(!$admin_config[mall_use_multishop]){
		return "";
	}

	if($_SESSION["admininfo"][admin_level] != 9){
		return "";
	}

	$mdb = new Database;

	$sql = "SELECT  count(*) as total
			FROM ".TBL_COMMON_COMPANY_DETAIL." ccd
			where ccd.com_type in ('G') and ccd.seller_auth = 'Y' $addWhere ";
	$mdb->query($sql);
	$mdb->fetch();
	$total = $mdb->dt[total];

	if($total < constant("MAX_VENDOR_VIEW_CNT")){

			$sql = "SELECT  com_name, ccd.company_id
					FROM ".TBL_COMMON_COMPANY_DETAIL." ccd
					where ccd.com_type in ('G') and ccd.seller_auth = 'Y' $addWhere order by com_name asc";
			$mdb->query($sql);

			$mstring ="<select name='trade_admin' id='".$select_id."' style=\"$dispaly_type height: 20px; width: 150px;font-size:12px;\" $property >";
			$mstring .="<option value=''>거래처 선택</option>";
			for($i=0;$i<$mdb->total;$i++){
			$mdb->fetch($i);
				if($company_id == $mdb->dt[company_id]){
					$mstring .="<option value='".$mdb->dt[company_id]."' selected>".$mdb->dt[com_name]."</option>";
				}else{
					$mstring .="<option value='".$mdb->dt[company_id]."'>".$mdb->dt[com_name]."</option>";
				}
			}
			$mstring .="</select>";
			$mstring .= "
			<script>
			$(function() {
				$( '#".$select_id."').combobox();
			});
			</script>";

	}else{

	//if($company_id) $where = "and company_id = '".$company_id."' ";

	$sql = "SELECT
				com_name, ccd.company_id
			FROM
				".TBL_COMMON_COMPANY_DETAIL." ccd
			where
				ccd.com_type in ('G')
				and company_id = '".$company_id."'
				and ccd.seller_auth = 'Y' $where";

	$mdb->query($sql);
	$mdb->fetch();
	$mstring =	"
			<table cellpadding=0 cellspacing=0>
			<tr>
				<td><input type=hidden class='textbox' name='trade_admin' id='".$input_id."'  value='".$company_id."' ></td>
				<td><input type=text class='textbox point_color' name='".$input_name."' id='".$input_name."' value='".$mdb->dt[com_name]."' onclick=\"ShowModalWindow('../trade_search.php?select_id=$select_id&input_id=$input_id&input_name=$input_name',800,380,'trade_search')\"   style='width:140px;' readonly></td>
				<td style='padding-left:5px;'>
				<img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"ShowModalWindow('../trade_search.php?select_id=$select_id&input_id=$input_id&input_name=$input_name',800,380,'trade_search')\"  style='cursor:pointer;'>
				</td>
				<td>
				<img src='../images/btn_x.gif' style='cursor:pointer;margin:0px 3px;' onclick=\"$(this).parent().parent().find('#".$input_id."').val('');$(this).parent().parent().find('#".$input_id."').val('');\">
				</td>
			</tr>
		</table>";
	}

	return $mstring;
}


function CompayChargerSearch($company_id , $charger_ix = "", $property="",$return_type="selectbox"){
	global $admininfo, $HTTP_URL;
	global $admin_config;

	if(!$admin_config[mall_use_multishop]){
		return "";
	}

	if($_SESSION["admininfo"][admin_level] != 9){
		return "";
	}
	$mdb = new Database;

	if($return_type == "selectbox" || $return_type == "select"){

			$sql = "SELECT  count(*) as total
					FROM ".TBL_COMMON_USER." cu left join ".TBL_COMMON_MEMBER_DETAIL." cmd on cu.code = cmd.code
					where cu.company_id = '".$company_id."'   ";
			$mdb->query($sql);
			$mdb->fetch();
			$total = $mdb->dt[total];

			if($total < constant("MAX_WOKER_VIEW_CNT")){

					$sql = "SELECT  cu.code, AES_DECRYPT(UNHEX(cmd.name),'".$mdb->ase_encrypt_key."') as name,  AES_DECRYPT(UNHEX(cmd.mail),'".$mdb->ase_encrypt_key."') as mail
								FROM ".TBL_COMMON_USER." cu left join ".TBL_COMMON_MEMBER_DETAIL." cmd on cu.code = cmd.code
								where cu.company_id = '".$company_id."'  order by  name asc  ";
					//echo nl2br($sql);
					$mdb->query($sql);

					$mstring ="<select name='charger_ix' id='charger_ix' style=\"$dispaly_type height: 20px; width: 130px;font-size:12px;\" $property >";
					$mstring .="<option value=''>회원 선택</option>";
					for($i=0;$i<$mdb->total;$i++){
					$mdb->fetch($i);
						if($charger_ix == $mdb->dt[code]){
							$mstring .="<option value='".$mdb->dt[code]."' selected>".$mdb->dt[name]."</option>";
						}else{
							$mstring .="<option value='".$mdb->dt[code]."'>".$mdb->dt[name]."</option>";
						}
					}
					$mstring .="</select>";
					$mstring .= "
					<script>
					$(function() {
						$( '#charger_ix').combobox();
					});
					</script>";

			}else{

			$sql = "SELECT  cu.code, AES_DECRYPT(UNHEX(cmd.name),'".$mdb->ase_encrypt_key."') as charger_name,  AES_DECRYPT(UNHEX(cmd.mail),'".$mdb->ase_encrypt_key."') as mail
						FROM ".TBL_COMMON_USER." cu left join ".TBL_COMMON_MEMBER_DETAIL." cmd on cu.code = cmd.code
						where cu.company_id = '".$company_id."' and cu.code = '".$charger_ix."' order by  name asc  ";
			//echo nl2br($sql);
			$mdb->query($sql);
			$mdb->fetch();
			$mstring =	"
								<table cellpadding=0 cellspacing=0>
								<tr>
									<td><input type=hidden class='textbox' name='charger_ix' id='charger_ix'  value='".$charger_ix."' ></td>
									<td><input type=text class='textbox point_color' name='charger_name' id='charger_name' value='".$mdb->dt[charger_name]."' style='width:130px;' onclick=\"ShowModalWindow('../charger_search.php?company_id=".$_SESSION["admininfo"]["company_id"]."&code=".$db->dt[code]."',600,530,'charger_search')\"  readonly></td>
									<td style='padding-left:5px;'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_member_search.gif' align=absmiddle onclick=\"ShowModalWindow('../charger_search.php?company_id=".$_SESSION["admininfo"]["company_id"]."&code=".$db->dt[code]."',600,530,'charger_search')\"  style='cursor:pointer;'></td>
								</tr>
							</table>";
			}
	}else{
			$sql = "SELECT  cu.code, AES_DECRYPT(UNHEX(cmd.name),'".$mdb->ase_encrypt_key."') as charger_name,  AES_DECRYPT(UNHEX(cmd.mail),'".$mdb->ase_encrypt_key."') as mail
						FROM ".TBL_COMMON_USER." cu left join ".TBL_COMMON_MEMBER_DETAIL." cmd on cu.code = cmd.code
						where cu.code = '".$charger_ix."' order by  name asc  "; //cu.company_id = '".$company_id."' and
			//echo nl2br($sql);
			$mdb->query($sql);
			$mdb->fetch();

			$mstring =	"".$mdb->dt[charger_name]."<input type=hidden class='textbox' name='charger_name' id='charger_name'  value='".$mdb->dt[charger_name]."' ><input type=hidden class='textbox' name='charger_ix' id='charger_ix'  value='".$charger_ix."' >";
	}

	return $mstring;
}

function SearchMember($company_id , $charger_ix = "", $property="",$return_type="selectbox"){
	global $admininfo, $HTTP_URL;
	global $admin_config;

	$mdb = new Database;

	if(!$admin_config[mall_use_multishop]){
		return "";
	}

	if($_SESSION["admininfo"][admin_level] != 9){
		if($charger_ix){
			$sql = "select
						AES_DECRYPT(UNHEX(name),'".$mdb->ase_encrypt_key."') as name
					from
						common_member_detail
					where
						code = '".$charger_ix."'";
			$mdb->query($sql);
			$mdb->fetch();
			$name = $mdb->dt[name];
		}
		return "$name";
	}

	if($return_type == "selectbox" || $return_type == "select"){

			$sql = "SELECT  count(*) as total
					FROM ".TBL_COMMON_USER." cu left join ".TBL_COMMON_MEMBER_DETAIL." cmd on cu.code = cmd.code
					where 1  ";
			$mdb->query($sql);
			$mdb->fetch();
			$total = $mdb->dt[total];

			if($total < constant("MAX_WOKER_VIEW_CNT")){

					$sql = "SELECT  cu.code, AES_DECRYPT(UNHEX(cmd.name),'".$mdb->ase_encrypt_key."') as name,  AES_DECRYPT(UNHEX(cmd.mail),'".$mdb->ase_encrypt_key."') as mail
								FROM ".TBL_COMMON_USER." cu left join ".TBL_COMMON_MEMBER_DETAIL." cmd on cu.code = cmd.code
								where 1  order by  name asc  ";
					//echo nl2br($sql);
					$mdb->query($sql);

					$mstring ="<select name='charger_ix' id='charger_ix' style=\"$dispaly_type height: 20px; width: 130px;font-size:12px;\" $property >";
					$mstring .="<option value=''>회원 선택</option>";
					for($i=0;$i<$mdb->total;$i++){
					$mdb->fetch($i);
						if($charger_ix == $mdb->dt[code]){
							$mstring .="<option value='".$mdb->dt[code]."' selected>".$mdb->dt[name]."</option>";
						}else{
							$mstring .="<option value='".$mdb->dt[code]."'>".$mdb->dt[name]."</option>";
						}
					}
					$mstring .="</select>";
					$mstring .= "
					<script>
					$(function() {
						$( '#charger_ix').combobox();
					});
					</script>";

			}else{

				$sql = "SELECT  cu.code, AES_DECRYPT(UNHEX(cmd.name),'".$mdb->ase_encrypt_key."') as charger_name,  AES_DECRYPT(UNHEX(cmd.mail),'".$mdb->ase_encrypt_key."') as mail
							FROM ".TBL_COMMON_USER." cu left join ".TBL_COMMON_MEMBER_DETAIL." cmd on cu.code = cmd.code
							where cu.code = '".$charger_ix."' order by  name asc  ";
				//echo nl2br($sql);
				$mdb->query($sql);
				$mdb->fetch();
				$mstring =	"
							<table cellpadding=0 cellspacing=0>
								<tr>
									<td><input type=hidden class='textbox' name='charger_ix' id='charger_ix'  value='".$charger_ix."' ></td>
									<td><input type=text class='textbox point_color' name='charger_name' id='charger_name' value='".$mdb->dt[charger_name]."' style='width:130px;' onclick=\"ShowModalWindow('../seller/search_member.php?company_id=".$_SESSION["admininfo"]["company_id"]."&code=".$db->dt[code]."',620,570,'search_member')\"  readonly></td>
									<td style='padding-left:5px;'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"ShowModalWindow('../seller/search_member.php?company_id=".$_SESSION["admininfo"]["company_id"]."&code=".$db->dt[code]."',620,570,'search_member')\"  style='cursor:pointer;'></td>
								</tr>
							</table>";
			}
	}else{
			$sql = "SELECT  cu.code, AES_DECRYPT(UNHEX(cmd.name),'".$mdb->ase_encrypt_key."') as charger_name,  AES_DECRYPT(UNHEX(cmd.mail),'".$mdb->ase_encrypt_key."') as mail
						FROM ".TBL_COMMON_USER." cu left join ".TBL_COMMON_MEMBER_DETAIL." cmd on cu.code = cmd.code
						where cu.code = '".$charger_ix."' order by  name asc  "; //cu.company_id = '".$company_id."' and
			//echo nl2br($sql);
			$mdb->query($sql);
			$mdb->fetch();

			$mstring =	"".$mdb->dt[charger_name]."<input type=hidden class='textbox' name='charger_name' id='charger_name'  value='".$mdb->dt[charger_name]."' ><input type=hidden class='textbox' name='charger_ix' id='charger_ix'  value='".$charger_ix."' >";
	}

	return $mstring;
}

function SearchCompany($company_id , $charger_ix = "", $property="",$return_type="selectbox",$seller_type=""){
	global $admininfo, $HTTP_URL;
	global $admin_config;

	$mdb = new Database;
	$db = new Database;

	if($company_id){
		$sql = "select relation_code from ".TBL_COMMON_COMPANY_RELATION." where company_id = '".$company_id."'";
		$db->query($sql);
		$db->fetch();
		$relation_code = $db->dt[relation_code];

		if(strlen($relation_code) == '5'){
			$relation_code = $relation_code;
		}else{
			$relation_code = substr($relation_code,0,5);
		}
	}

	if(!$admin_config[mall_use_multishop]){
		return "";
	}

	if($_SESSION["admininfo"][admin_level] != 9){
		return "";
	}

	if($seller_type == "1"){
		$where = " and ccd.seller_type like '%1%' ";
	}else if($seller_type == "2"){
		$where = " and ccd.seller_type like '%2%' ";
	}

	if($return_type == "selectbox" || $return_type == "select"){

			$sql = "SELECT
						count(*) as total
					FROM
						".TBL_COMMON_COMPANY_DETAIL." as ccd
						inner join ".TBL_COMMON_SELLER_DETAIL." as csd on ccd.company_id = csd.company_id
						inner join ".TBL_COMMON_COMPANY_RELATION." as ccr on ccd.company_id = ccr.company_id
					where
						ccr.relation_code like '".$relation_code."%'
						and ccd.com_type !='A'
						$where";
			$mdb->query($sql);
			$mdb->fetch();
			$total = $mdb->dt[total];

			if($total < constant("MAX_WOKER_VIEW_CNT")){

					$sql = "SELECT
								ccd.company_id,
								ccd.com_name,
								ccd.com_number,
								ccd.com_mobile,
								ccd.com_ceo
							FROM
								".TBL_COMMON_COMPANY_DETAIL." as ccd
								inner join ".TBL_COMMON_SELLER_DETAIL." as csd on ccd.company_id = csd.company_id
								inner join ".TBL_COMMON_COMPANY_RELATION." as ccr on ccd.company_id = ccr.company_id
							where
								ccr.relation_code like '".$relation_code."%'
								and ccd.com_type !='A'
								$where";

					$mdb->query($sql);

					$mstring ="<select name='company_id' id='company_id' style=\"$dispaly_type height: 20px; width: 130px;font-size:12px;\" $property >";
					$mstring .="<option value=''>거래처 선택</option>";
					for($i=0;$i<$mdb->total;$i++){
					$mdb->fetch($i);
						if($charger_ix == $mdb->dt[company_id]){
							$mstring .="<option value='".$mdb->dt[company_id]."' selected>".$mdb->dt[com_name]."</option>";
						}else{
							$mstring .="<option value='".$mdb->dt[company_id]."'>".$mdb->dt[company_id]."</option>";
						}
					}
					$mstring .="</select>";
					$mstring .= "
					<script>
					$(function() {
						$( '#company_id').combobox();
					});
					</script>";

			}else{

			$sql = "SELECT
						ccd.company_id,
						ccd.com_name,
						ccd.com_number,
						ccd.com_mobile,
						ccd.com_ceo
					FROM
						".TBL_COMMON_COMPANY_DETAIL." as ccd
						inner join ".TBL_COMMON_SELLER_DETAIL." as csd on ccd.company_id = csd.company_id
						inner join ".TBL_COMMON_COMPANY_RELATION." as ccr on ccd.company_id = ccr.company_id
					where
						ccd.company_id = '".$company_id."'
						";

			//echo nl2br($sql);
			$mdb->query($sql);
			$mdb->fetch();
			$mstring =	"
						<table cellpadding=0 cellspacing=0>
						<tr>
							<td><input type=hidden class='textbox' name='company_id' id='company_id'  value='".$company_id."' ></td>
							<td><input type=text class='textbox point_color' name='company_code' id='company_code' value='".$mdb->dt[company_id]."' style='width:130px;' onclick=\"ShowModalWindow('../seller/search_company.php?relation_code=".$relation_code."&company_id=".$db->dt[company_id]."&seller_type=".$seller_type."',600,530,'search_company')\"  readonly></td>
							<td style='padding-left:5px;'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"ShowModalWindow('../seller/search_company.php?relation_code=".$relation_code."&company_id=".$db->dt[company_id]."&seller_type=".$seller_type."',600,530,'search_company')\"  style='cursor:pointer;'></td>
						</tr>
					</table>";
			}
	}else{
			$sql = "SELECT
						ccd.company_id,
						ccd.com_name,
						ccd.com_number,
						ccd.com_mobile,
						ccd.com_ceo
					FROM
						".TBL_COMMON_COMPANY_DETAIL." as ccd
						inner join ".TBL_COMMON_SELLER_DETAIL." as csd on ccd.company_id = csd.company_id
						inner join ".TBL_COMMON_COMPANY_RELATION." as ccr on ccd.company_id = ccr.company_id
					where
						ccd.company_id = '".$company_id."'
						";

			//echo nl2br($sql);
			$mdb->query($sql);
			$mdb->fetch();

			$mstring =	"".$mdb->dt[com_name]."<input type=hidden class='textbox' name='company_id' id='company_id'  value='".$mdb->dt[company_id]."' ><input type=hidden class='textbox' name='company_id' id='company_id'  value='".$company_id."' >";
	}

	return $mstring;
}



function GetBrandData($brand, $cid){
	$tb = (defined('TBL_SNS_BRAND'))	?	TBL_SNS_BRAND:TBL_SHOP_BRAND;

	$mdb = new Database;
	$mdb->query("SELECT * FROM ".$tb." where b_ix='".$brand."' ");
	$mdb->fetch();

	return $mdb->dt;
}

function BrandListSelect($brand, $cid,$select_id='brand',$input_id='brand_name',$b_ix_id = 'b_ix')
{//한페이지에서 동일 사용사 id, name 중복됨으로 select_id , input_id, b_ix_id 를 외부에서 받아서 처리하도록 수정 2014-04-17 이학봉
	global $admininfo;
	$mdb = new Database;
	$tb = (defined('TBL_SNS_BRAND'))	?	TBL_SNS_BRAND:TBL_SHOP_BRAND;

	if($cid){
		$mdb->query("SELECT count(*) as total FROM ".$tb." where disp=1 and cid = '$cid'  ");
	}else{
		$mdb->query("SELECT count(*) as total FROM ".$tb." where disp=1   ");
	}

	$mdb->fetch();
	$total = $mdb->dt[total];
	if($total < constant("MAX_BRAND_VIEW_CNT")){
			if($cid){
				$mdb->query("SELECT * FROM ".$tb." where disp=1 and cid = '$cid' order by brand_name asc ");
			}else{
				$mdb->query("SELECT * FROM ".$tb." where disp=1 order by brand_name asc  ");
			}

			$bl = "<Select name='b_ix' id='".$select_id."'>";
			if ($mdb->total == 0)	{
				$bl = $bl."<Option value=''>등록된 브랜드가 없습니다.</Option>";
			}else{

				$bl = $bl."<Option value=''>브랜드 선택</Option>";
				for($i=0 ; $i <$mdb->total ; $i++)
				{
					$mdb->fetch($i);
					if ($brand == $mdb->dt[b_ix])
					{
						$strSelected = "Selected";
					}else{
						$strSelected = "";
					}

					$bl = $bl."<Option value='".$mdb->dt[b_ix]."' $strSelected>".$mdb->dt[brand_name]."</Option>";

				}
			}

			$bl = $bl."</Select>";
			$bl .= "
			<script>
			$(function() {
				$( '#".$select_id."').combobox();
			});
			</script>";

			$mstring = "
			<table cellpadding=0 cellspacing=0>
						<tr>
							<td><div id='brand_select_area'>".$bl."</div></td><td style='padding:0px 3px 0px 32px; '><a href=\"javascript:PoPWindow3('../product/brand.php?mmode=pop',960,700,'brand')\"'><img src='../images/".$_SESSION["admininfo"]["language"]."/btn_pop_manage.gif' align=absmiddle border=0></a></td>
						</tr>
					</table>";

	}else{

		$mdb->query("SELECT * FROM ".$tb." where b_ix='".$brand."' ");
		$mdb->fetch();

		$mstring =	"	<table cellpadding=0 cellspacing=0>
						<tr>
							<td><input type=hidden class='textbox' name='".$b_ix_id."' id='".$b_ix_id."'  value='".$brand."' ></td>
							<td><input type=text class='textbox point_color' name='".$input_id."' id='".$input_id."' value='".$mdb->dt[brand_name]."'";
		//if(strpos($_SERVER['SCRIPT_FILENAME'], 'goods_input') == true){
		//	$mstring .=	" title='브랜드' validation=true ";
		//}
		$mstring .=	"	style='width:140px;' readonly onclick=\"PoPWindow('../brand_search.php?select_id=$select_id&input_id=$input_id&b_ix_id=$b_ix_id',600,380,'brand_search')\"></td>
							<td style='padding-left:5px;'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"PoPWindow('../brand_search.php?select_id=$select_id&input_id=$input_id&b_ix_id=$b_ix_id',600,380,'brand_search')\"  style='cursor:pointer;'> </td>
							<td style='padding-left:3px;'> <img src='../images/btn_x.gif' style='cursor:pointer' onclick=\"$(this).parent().parent().find('#".$b_ix_id."').val('');$(this).parent().parent().find('#".$input_id."').val('');\"></td>
						</tr>
					</table>";
	}
	return $mstring;

}


function OriginSelect($origin,$select_name='origin',$select_id='origin_name',$input_name='og_ix',$type='')//한페이지에 중복노출시 id 값을 구분하기 위하여 추가 2014-05-27 이학봉
{
	global $admininfo;
	$mdb = new Database;

	$mdb->query("SELECT count(*) as total FROM common_origin where disp=1   ");
	$mdb->fetch();

	$total = $mdb->dt[total];
	//echo constant("MAX_BRAND_VIEW_CNT").":::".$total ;
	if($total < constant("MAX_ORIGIN_VIEW_CNT")){
			if($cid){
				$mdb->query("SELECT * FROM common_origin where disp=1 and cid = '$cid' order by origin_name asc ");
			}else{
				$mdb->query("SELECT * FROM common_origin where disp=1 order by origin_name asc  ");
			}

			$bl = "<Select name='".$select_name."' id='".$select_name."'>";
			if ($mdb->total == 0)	{
				$bl = $bl."<Option value=''>등록된 원산지가 없습니다.</Option>";
			}else{
				$bl = $bl."<Option value=''>원산지 선택</Option>";
				for($i=0 ; $i <$mdb->total ; $i++)
				{
					$mdb->fetch($i);
					if ($origin == $mdb->dt[origin_name])
					{
						$strSelected = "Selected";
					}else{
						$strSelected = "";
					}
					$bl = $bl."<Option value='".$mdb->dt[og_ix]."' $strSelected>".$mdb->dt[origin_name]."</Option>";
				}
			}

			$bl = $bl."</Select>";
			$bl .= "
			<script>
			$(function() {
				$('#".$select_name."').combobox();
			});
			</script>";

			$mstring = "
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td>
						<div id='origin_select_area'>".$bl."</div>
					</td>
					<td style='padding:0px 3px 0px 32px; '>
						<a href=\"javascript:PoPWindow3('../product/origin_list.php?mmode=pop',960,700,'origin')\"'><img src='../images/".$_SESSION["admininfo"]["language"]."/btn_pop_manage.gif' align=absmiddle border=0></a>
					</td>
				</tr>
			</table>";

	}else{
		$sql = "SELECT * FROM common_origin where origin_name = '".$origin."' ";
		$mdb->query($sql);
		$mdb->fetch();

		$mstring =	"	<table cellpadding=0 cellspacing=0>
						<tr>
							<td><input type=hidden class='textbox' name='".$input_name."' id='".$input_name."'  value='".$mdb->dt[og_ix]."' ></td>
							<td><input type=text class='textbox point_color' name='".$select_name."' id='".$select_id."' value='".$mdb->dt[origin_name]."' title='원산지' style='width:140px;' ".$type." readonly onclick=\"PoPWindow('../origin_search.php?input_id=".$input_name."&select_id=".$select_id."',600,380,'origin_search')\"></td>
							<td style='padding-left:5px;'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"PoPWindow('../origin_search.php?input_id=".$input_name."&select_id=".$select_id."',600,356,'origin_search')\"  style='cursor:pointer;'> </td>
							<td style='padding-left:3px;'> <img src='../images/btn_x.gif' style='cursor:pointer' onclick=\"$(this).parent().parent().find('#".$input_name."').val('');$(this).parent().parent().find('#".$select_id."').val('');\"></td>
						</tr>
						</table>";
	}

	return $mstring;
}

function MDSelect($md_code, $obj_name = "md_code", $name_obj_name = "md_name", $group_code="", $css_class="point_color",$validation='')
{
	global $admininfo;
	$mdb = new Database;
	$mdb2 = new Database;

	$sql = "select
				AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
				cu.code
			from
				".TBL_COMMON_USER." as cu
				inner join ".TBL_COMMON_MEMBER_DETAIL." as cmd on (cu.code = cmd.code)
			where
				1
				and mem_div = 'MD'
				and mem_type = 'A'
				order by cmd.name ASC
			";
//echo nl2br($sql);
	$mdb->query($sql);

	$mdb->fetch();
	$total = $mdb->total;
	//echo constant("MAX_BRAND_VIEW_CNT").":::".$total ;
	if($total < constant("MAX_MD_VIEW_CNT")){
			if($company_id ){
					$sql = "select
								AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
								cu.code
							from
								".TBL_COMMON_USER." as cu
								inner join ".TBL_COMMON_MEMBER_DETAIL." as cmd on (cu.code = cmd.code)
							where
								cu.company_id = '".$company_id."'
								and mem_div = 'MD'
								and mem_type = 'A'
								order by cmd.name ASC
							";
				$mdb->query($sql);
			}else{
					$sql = "select
									AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
									cu.code
								from
									".TBL_COMMON_USER." as cu
									inner join ".TBL_COMMON_MEMBER_DETAIL." as cmd on (cu.code = cmd.code)
								where
									1
									and mem_div = 'MD'
									and mem_type = 'A'
									order by cmd.name ASC
								";
				$mdb->query($sql);
			}

			$bl = "<Select name='".$obj_name."' id='".$obj_name."_1'>";
			if ($mdb->total == 0)	{
				$bl = $bl."<Option value=''>등록된 MD담당자가 없습니다.</Option>";
			}else{

				$bl = $bl."<Option value=''>MD담당자 선택</Option>";
				for($i=0 ; $i <$mdb->total ; $i++)
				{
					$mdb->fetch($i);
					if ($code == $mdb->dt[code])
					{
						$strSelected = "Selected";
					}else{
						$strSelected = "";
					}

					$bl = $bl."<Option value='".$mdb->dt[code]."' $strSelected>".$mdb->dt[name]."</Option>";

				}
			}

			$bl = $bl."</Select>";
			$bl .= "
			<script>
			$(function() {
				$( '#".$obj_name."_1').combobox();
			});
			</script>";

			$mstring = "
			<table cellpadding=0 cellspacing=0>
						<tr>
							<td><div id='origin_select_area'>".$bl."</div></td><td style='padding:0px 3px 0px 32px; '><a href=\"javascript:PoPWindow3('../store/md_manage.php?mmode=pop',960,700,'md')\"'><img src='../images/".$_SESSION["admininfo"]["language"]."/btn_pop_manage.gif' align=absmiddle border=0></a></td>
						</tr>
					</table>";

	}else{
		$sql = "SELECT
						AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
						cu.code
					FROM
						".TBL_COMMON_USER." as cu
						inner join ".TBL_COMMON_MEMBER_DETAIL." as cmd on (cu.code = cmd.code)
					where cu.code='".$md_code."' ";
		//echo $sql;
		$mdb2->query($sql);
		$mdb2->fetch();

		$mstring =	"	<table cellpadding=0 cellspacing=0>
						<tr>
							<td><input type=hidden class='textbox' name='".$obj_name."' id='md_code".($group_code == "" ? "":"_".$group_code)."'  value='".$mdb2->dt[code]."' ></td>
							<td><input type=text class='textbox ".$css_class."' name='".$name_obj_name."' id='md_name".($group_code == "" ? "":"_".$group_code)."' value='".$mdb2->dt[name]."' style='width:140px;' readonly ".$validation." title='MD명'></td>
							<td style='padding-left:5px;'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"ShowModalWindow('../md_search.php?group_code=".$group_code."',600,345,'origin_search')\"  style='cursor:pointer;'> </td>
							<td style='padding-left:3px;'> <img src='../images/btn_x.gif' style='cursor:pointer' onclick=\"$(this).parent().parent().find('#md_code').val('');$(this).parent().parent().find('#md_name".($group_code == "" ? "":"_".$group_code)."').val('');\"></td>
						</tr>
					</table>";
	}

	return $mstring;
}


function adminEtcSelect($code, $obj_name = "code", $name_obj_name = "name", $group_code="", $css_class="point_color",$validation='')
{
	global $admininfo;
	$mdb = new Database;

	$sql = "select
					AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
					cu.code
				from
					".TBL_COMMON_USER." as cu
					inner join ".TBL_COMMON_MEMBER_DETAIL." as cmd on (cu.code = cmd.code)
				where
					1
					and mem_div = 'D'
					and mem_type = 'A'
					order by cmd.name ASC
				";
	$mdb->query($sql);

	$bl = "<Select name='".$obj_name."' id='".$obj_name."_1'>";
	if ($mdb->total == 0)	{
		$bl = $bl."<Option value=''>등록된 MD담당자가 없습니다.</Option>";
	}else{

		$bl = $bl."<Option value=''>MD담당자 선택</Option>";
		for($i=0 ; $i <$mdb->total ; $i++)
		{
			$mdb->fetch($i);
			if ($code == $mdb->dt[code])
			{
				$strSelected = "Selected";
			}else{
				$strSelected = "";
			}

			$bl = $bl."<Option value='".$mdb->dt[code]."' $strSelected>".$mdb->dt[name]."</Option>";

		}
	}

	$bl = $bl."</Select>";
	$bl .= "
	<script>
	$(function() {
		$( '#".$obj_name."_1').combobox();
	});
	</script>";

	$mstring = "
	<table cellpadding=0 cellspacing=0>
				<tr>
					<td><div id='adminetc_select_area'>".$bl."</div></td>
				</tr>
			</table>";


	return $mstring;
}

function MBSelect($obj_name = "", $name_obj_name = "", $name_value = "", $css_class="point_color")
{
	global $admininfo;
	$mdb = new Database;
	$mdb2 = new Database;

	$mstring =	"	<table cellpadding=0 cellspacing=0>
					<tr>
						<td><input type=hidden class='textbox' name='".$obj_name."' id='ucode'  value='".$mdb2->dt[code]."' ></td>
						<td><input type=text class='textbox ".$css_class."' name='".$name_obj_name."' id='user_name' value='".$name_value."' style='width:140px;' readonly></td>
						<td style='padding-left:5px;'>
							<img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"ShowModalWindow('../mb_search.php',600,380,'origin_search')\"  style='cursor:pointer;'>
						</td>
						<td style='padding-left:3px;'>
						<img src='../images/btn_x.gif' style='cursor:pointer' onclick=\"$(this).parent().parent().find('#md_code').val('');$(this).parent().parent().find('#user_name').val('');\">
						</td>
						<td>
							<input type='checkbox' name='user_type' value='1' id='nomb' />
							<label for='nomb'>비회원</label>
						</td>
					</tr>
				</table>";


	return $mstring;
}

function CASelect($name_obj_name = "", $name_value = "", $css_class="point_color")
{
	global $admininfo;
	$mdb = new Database;
	$mdb2 = new Database;

	$mstring =	"	<table cellpadding=0 cellspacing=0>
					<tr>
						<td><input type=text class='textbox ".$css_class."' name='".$name_obj_name."' id='charger_name' value='".$name_value."' style='width:140px;' readonly></td>
						<td style='padding-left:5px;'>
							<img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"ShowModalWindow('../ca_search.php',600,380,'origin_search')\"  style='cursor:pointer;'>
						</td>
						<td style='padding-left:3px;'>
						<img src='../images/btn_x.gif' style='cursor:pointer' onclick=\"$(this).parent().parent().find('#md_code').val('');$(this).parent().parent().find('#user_name').val('');\">
						</td>
					</tr>
				</table>";


	return $mstring;
}


function getDesignTempletPath($cid, $depth='-1'){
	global $user, $HTTP_URL, $admin_config, $admininfo;

    $layoutXmlPath = $_SERVER["DOCUMENT_ROOT"] . $admininfo["mall_data_root"] . "/_layout/";

    switch ($_SESSION["admin_config"]["mall_page_type"]){
        case "P":
            $layoutXmlPath .= $admin_config["selected_templete"] . ".xml";
            break;
        case "MI":
            $layoutXmlPath .= $admin_config["selected_templete_minishop"] . ".xml";
            break;
        case "M":
            //echo($admin_config["mall_page_type"]);
            //echo("<br />");
            $layoutXmlPath .= $admin_config["selected_templete_mobile"] . ".xml";
            break;
    }
	//if (class_exists(LayoutXml)){//클래스 파일이 없어서 layout.xml 파일을 사용하는 경우 저장되지않음 kbk 13/05/21
		//if (file_exists($layoutXmlPath)){//이것과 위의 조건을 바꿈 kbk 13/05/21
	if (is_file($layoutXmlPath)){
		if (class_exists(LayoutXml)){
			$layoutXml = new LayoutXml($layoutXmlPath);
			switch($depth)
			{
				case '0' :
					$xpathString = sprintf("/layouts/layout[@depth=0 and substring(@pcode, 1, 3) = '%s']", substr($cid,0,3));
					break;
				case '1' :
					$xpathString = sprintf("/layouts/layout[@depth<=1 and ((@depth=0 and substring(@pcode, 1, 3) = '%s') or (@depth=1 and substring(@pcode, 1, 6) = '%s') )]", substr($cid,0,3), substr($cid, 0, 6));
					break;
				case '2' :
					$xpathString = sprintf("/layouts/layout[@depth<=2 and ((@depth=0 and substring(@pcode, 1, 3) = '%s') or (@depth=1 and substring(@pcode, 1, 6) = '%s') or (@depth=2 and substring(@pcode, 1, 9) = '%s') )]", substr($cid,0,3), substr($cid, 0, 6), substr($cid, 0, 9));
					break;
				case '3' :
					$xpathString = sprintf("/layouts/layout[@depth<=3 and ((@depth=0 and substring(@pcode, 1, 3) = '%s') or (@depth=1 and substring(@pcode, 1, 6) = '%s') or (@depth=2 and substring(@pcode, 1, 9) = '%s') or (@depth=3 and substring(@pcode, 1, 12) = '%s') )]", substr($cid,0,3), substr($cid, 0, 6), substr($cid, 0, 9), substr($cid, 0, 12));
					break;
				case '4' :
					$xpathString = sprintf("/layouts/layout[@depth<=4 and ((@depth=0 and substring(@pcode, 1, 3) = '%s') or (@depth=1 and substring(@pcode, 1, 6) = '%s') or (@depth=2 and substring(@pcode, 1, 9) = '%s') or (@depth=3 and substring(@pcode, 1, 12) = '%s') or (@depth=4 and substring(@pcode, 1, 15) = '%s') )]", substr($cid,0,3), substr($cid, 0, 6), substr($cid, 0, 9), substr($cid, 0, 12), substr($cid, 0, 15));
					break;
				default :
					return "";
					//$xpathString = "/layouts/layout";
					//break;

			}// 전부 /layouts/layout[@depth<=1 로 되어있던 것을 depth에 따라서 1,2,3,4로 변경함 bgh 13/08/20

			$result = $layoutXml->simpleXml->xpath($xpathString);
			//print_r($result);
			//echo count($result);

			for($i = 0; $i < count($result); $i++)
			{
				//echo $result[$i]->page_path;
				if($i == 0){
					$mstring = $result[$i]->path;//page_path;
				} else {
					$mstring .= "/" . $result[$i]->path;//page_path;
				}
			}
		}
			//echo $mstring;
	} else {
			$mdb = new Database;
			if($admin_config[mall_page_type] == "MI"){
				if($depth == '0'){
					$sql = "select * from ".TBL_SHOP_MINISHOP_LAYOUT_INFO." where depth = 0 and cid LIKE '".substr($cid,0,3)."%' order by depth asc";
				}else if($depth == '1'){
					$sql = "select * from ".TBL_SHOP_MINISHOP_LAYOUT_INFO." where depth <= 1 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%'))  order by depth asc";
				}else if($depth == '2'){
					$sql = "select * from ".TBL_SHOP_MINISHOP_LAYOUT_INFO." where depth <= 2 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%'))  order by depth asc";
				}else if($depth == '3'){
					$sql = "select * from ".TBL_SHOP_MINISHOP_LAYOUT_INFO." where depth <= 3 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0,12)."%'))  order by depth asc";
				}else if($depth == '4'){
					$sql = "select * from ".TBL_SHOP_MINISHOP_LAYOUT_INFO." where depth <= 4 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0,12)."%') or (depth = 4 and cid LIKE '".substr($cid,0,15)."%'))  order by depth asc";
					//$sql = "select * from ".TBL_SHOP_LAYOUT_INFO." where (depth = 0 and cid '".substr($cid,0,3)."%') or (depth = 0 and cid '".substr($cid,0,3)."%')LIKE cid LIKE '".substr($cid,0,3*($depth+1))."%' and depth <= '$depth' order by depth asc";
				}else{
					return "";
				}
			}else{
				if($depth == '0'){
					$sql = "select * from ".TBL_SHOP_LAYOUT_INFO." where depth = 0 and cid LIKE '".substr($cid,0,3)."%' order by depth asc";
				}else if($depth == '1'){
					$sql = "select * from ".TBL_SHOP_LAYOUT_INFO." where depth <= 1 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%'))  order by depth asc";
				}else if($depth == '2'){
					$sql = "select * from ".TBL_SHOP_LAYOUT_INFO." where depth <= 2 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%'))  order by depth asc";
				}else if($depth == '3'){
					$sql = "select * from ".TBL_SHOP_LAYOUT_INFO." where depth <= 3 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0,12)."%'))  order by depth asc";
				}else if($depth == '4'){
					$sql = "select * from ".TBL_SHOP_LAYOUT_INFO." where depth <= 4 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0,12)."%') or (depth = 4 and cid LIKE '".substr($cid,0,15)."%'))  order by depth asc";
					//$sql = "select * from ".TBL_SHOP_LAYOUT_INFO." where (depth = 0 and cid '".substr($cid,0,3)."%') or (depth = 0 and cid '".substr($cid,0,3)."%')LIKE cid LIKE '".substr($cid,0,3*($depth+1))."%' and depth <= '$depth' order by depth asc";
				}else{
					return "";
				}
			}
			//echo $sql;
			$mdb->query($sql);

			for($i=0;$i < $mdb->total;$i++){
				$mdb->fetch($i);

				if($i == 0){
					$mstring = $mdb->dt[path];
				}else{
					$mstring .= "/".$mdb->dt[path];
				}
			}
	}
	return $mstring;
}


function DirList ( $path , $select_file="", $maxdepth = -1 , $mode = "FULL" , $d = 0 ){
	global $exclue_bbs_templet;

   if ( substr ( $path , strlen ( $path ) - 1 ) != '/' ) { $path .= '/' ; }
   $dirlist = array () ;

   //if ( $mode != "FILES" ) { $dirlist[] = $path ; }
   if ( $handle = @opendir ( $path ) )
   {
       while ( false !== ( $file = readdir ( $handle ) ) )
       {
           if ( $file != '.' && $file != '..' )
           {
               $only_file = $file;
               $file = $path . $file ;

               if ( ! is_dir ( $file ) || $mode == "FULL"){
               		if(is_dir ( $file )){
						if(substr_count($only_file,".org") == 0 && substr_count($only_file,".") == 0 && !in_array($only_file,$exclue_bbs_templet)){//&& substr_count($only_file,"cafebbs") == 0 && substr_count($only_file,"blogbbs") == 0 && substr_count($only_file,"admin") == 0){//$only_file != "admin" &&
							if($select_file == $only_file){
								$mstring .=  "<option value='".$only_file ."' selected>".$only_file ."</option>";
							}else{
								$mstring .=  "<option value='".$only_file ."'>".$only_file ."</option>";
							}
               			}
               		}else{
               			/*
               			if($select_file == $only_file){
               				$mstring .=  "<option value='".$only_file ."' selected>".$only_file ."</option>";
               			}else{
               				$mstring .=  "<option value='".$only_file ."'>".$only_file ."</option>";
               			}
               			*/
               		}

               }elseif ($d >=0 && ($d < $maxdepth || $maxdepth < 0) ){
                   $mstring .= DirList ( $file . '/' , $maxdepth , $mode , $d + 1 ) ;

                  $mstring .=  "<option value='".Icon($only_file,"path",filetype($file))."'>".$only_file ."</option>";
               }
           }
       }
       closedir ( $handle ) ;
   }
   if ( $d == 0 ) { natcasesort ( $dirlist ) ; }
   return ( $mstring ) ;
}


function SelectDirList($objname, $path, $select_file, $validation="true"){
	global $DOCUMENT_ROOT, $mod, $SubID;
	if($path == ""){
		//$path = $_SERVER["DOCUMENT_ROOT"]."/data/bbs_templet";
		$path = $_SERVER["DOCUMENT_ROOT"]."/bbs_templet";
	}

	$mstring =  "<select name='$objname' id='$objname' validation='".$validation."' title='디자인 템플릿'>";
	$mstring .= "<option value=''>템플릿을 선택해주세요</option>";
	if(DirList($path, 0, "FULL")){
		$mstring .= DirList($path, $select_file, 0, "FULL");
	}else{
		$mstring .= "<option>템플릿이 존재하지 않습니다..</option>";
	}
	$mstring .=  "</select>";

	return $mstring;
}



function FileList ( $path , $select_file="", $maxdepth = -1 , $mode = "FULL" , $d = 0 ){

   if ( substr ( $path , strlen ( $path ) - 1 ) != '/' ) { $path .= '/' ; }
   $dirlist = array () ;
   //if ( $mode != "FILES" ) { $dirlist[] = $path ; }
   if ( $handle = @opendir ( $path ) )
   {

       while ( false !== ( $file = readdir ( $handle ) ) )
       {
           if ( $file != '.' && $file != '..' )
           {
               $only_file = $file;
               $file = $path . $file ;
               if ( ! is_dir ( $file ) || $mode == "FULL"){
               		if(is_dir ( $file )){
               			//$mstring .=  "<option value='".$only_file ."'>".$only_file ."</option>";
               		}else{
               			if($select_file == $only_file){
               				$mstring .=  "<option value='".$only_file ."' selected>".$only_file ."</option>";
               			}else{
               				$mstring .=  "<option value='".$only_file ."'>".$only_file ."</option>";
               			}
               		}

               }elseif ($d >=0 && ($d < $maxdepth || $maxdepth < 0) ){
                   $mstring .= FileList ( $file . '/' , $maxdepth , $mode , $d + 1 ) ;
                  $mstring .=  "<option value='".Icon($only_file,"path",filetype($file))."'>".$only_file ."</option>";
               }
           }
       }
       closedir ( $handle ) ;
   }
   if ( $d == 0 ) { natcasesort ( $dirlist ) ; }
   return ( $mstring ) ;
}


function SelectFileList($objname, $path, $select_file){
	global $DOCUMENT_ROOT, $mod, $SubID;
	if($path == ""){
		$path = $_SERVER["DOCUMENT_ROOT"]."/data/sample/templet/basic";
	}

	$mstring =  "<select name='$objname' id='$objname' style='font-size:12px;width:200px;'><!--onchange=\"document.location.href='design.php?SubID=$SubID&mod=$mod&page_name='+this.value\"-->";
	$mstring .= "<option value=''>파일을 선택해주세요</option>";
	if(FileList($path, 0, "FULL")){
		$mstring .= FileList($path, $select_file, 0, "FULL");
	}else{
		$mstring .= "<option>파일이 존재하지않습니다.</option>";
	}
	$mstring .=  "</select>";

	return $mstring;
}

function HelpBox($title, $text, $title_size="151"){

	$mstring = "<table width='100%' border=0>
					<tr><td colspan=3 nowrap><div style='z-index:0;vertical-align:bottom;position:relative;top:12px;left:10px;width:".$title_size."px;height:15px;font-weight:bold;padding:0px 10px 0px 55px;background-color:#ffffff' class='help_title' nowrap> $title</div></td></tr>
					<tr height=2><td class='help_col' colspan=3></td></tr>
					<tr height=60>
						<td width=1 class='help_row1'></td>
						<td class='top p10 lh160'>
							$text

						</td>
						<td width=1 class='help_row2'></td>
					</tr>
					<tr height=2><td class='help_col' colspan=3></td></tr>
				</table>";

	return $mstring;
}

function HelpBox2($title, $text, $title_size="151"){

	$mstring = "<table width='100%' border=0>
					<col width='60px'>
					<col width='*'>
					<tr height=60> 
						<td  style='vertical-align:top;padding-top:15px;'><img src='../v3/images/btn/guide.png' align=absmiddle style='position:relative;'> </td>
						<td class='top p10 lh160'>
							$text

						</td> 
					</tr> 
				</table>";

	return $mstring;
}


function GetTitleNavigation($menu_title, $navigation, $favorite_add_display=true){
	global $admininfo, $P, $admin_root;

	$mstring = "<table width='100%' border='0' cellspacing='0' cellpadding='0' >
				<tr>
					<td width='10%' height='31' valign='middle' style='color:#000000;border-bottom:1px solid #efefef;font-family:굴림;font-size:16px;font-weight:bold;letter-spacing:-2px;word-spacing:0px;padding-right:20px;' nowrap>
						<img src='./v3/images/common/arrow_icon02.gif' align=absmiddle> $menu_title
					</td>
					<td width='90%' align='right' valign='middle' style='border-bottom:1px solid #efefef;'>
						&nbsp;$navigation
					</td>
				</tr>
				<tr height=10>
					<td colspan=2 align=right style='padding:5px 0 5px 0'>";
			if($favorite_add_display){
				$mstring .= "<a href='/admin/favorite.act.php?act=add&page_id=".md5($_SERVER["PHP_SELF"])."&favorite_link=".urlencode($_SERVER["REQUEST_URI"])."&favorite_name=".urlencode($menu_title)."' target='iframe_act' class=small><img src='../images/".$_SESSION["admininfo"]["language"]."/btn_add_menu.gif' align='absmiddle'> 해당 메뉴 즐겨찾기추가</a>";
			}
			$mstring .= "
					</td>
				</tr>
			</table>";
	if($admin_root != ""){
		if(substr_count($_SERVER["PHP_SELF"],$admin_root)){
			//$P->title = $menu_title;
			//$P->Navigation = $navigation;
			return "";
		}else{
			return $mstring;
		}
	}else{
		return $mstring;
	}

}

function GetTitlebbs($menu_title,$board_group){
	$mdb = new Database;
	$mdb->query("select div_name from bbs_group where div_ix = '$board_group'");
	$mdb->fetch();

	$mstring = "<table width='100%' border='0' cellspacing='0' cellpadding='0' >
				<tr>
					<td width='10%' height='31' valign='middle' style='border-bottom:2px solid #ff9b00;font-family:굴림;font-size:16px;font-weight:bold;letter-spacing:-2px;word-spacing:0px;padding-right:20px;' nowrap>
						<img src='/admin/images/icon/sub_title_dot.gif' align=absmiddle> $menu_title
					</td>
					<td width='90%' align='right' valign='middle' style='border-bottom:2px solid #efefef;'>
						&nbsp;".$mdb->dt[div_name]." > $menu_title
					</td>
				</tr>
				<tr height=10><td colspan=2 align=right style='padding:5px 5px 5px 15px;'><a href='/admin/favorite.act.php?act=add&page_id=".md5($_SERVER["PHP_SELF"])."&favorite_link=".urlencode($_SERVER["REQUEST_URI"])."&favorite_name=".urlencode($menu_title)."' target='iframe_act' class=small>해당 메뉴 즐겨찾기추가</a></td></tr>
			</table>";

	return $mstring;

}


function dirsizex($path)
{
  $old_path = getcwd();

  //if(!is_dir($old_path."/".$path)) return -1;
  $size = trim(shell_exec("cd \"".$path."\"; du -sb; cd \"".$old_path."\";"), "\x00..\x2F\x3A..\xFF");

  return $size;
}

function dirsize($path)
{
$result=explode("\t",exec("du -hs ".$path),2);
//print_r($result);
return ($result[1]==$path ? $result[0] : "error");
}


function get_folder_size($target, $output=false)
{
   if(!is_dir($target)){
	mkdir($target, 0000);
   }

   $sourcedir = opendir($target);
   while(false !== ($filename = readdir($sourcedir)))
   {
       if($output)
       { echo "Processing: ".$target."/".$filename."<br>"; }
       if($filename != "." && $filename != "..")
       {
           if(is_dir($target."/".$filename))
           {
               // recurse subdirectory; call of function recursive
               $totalsize += get_folder_size($target."/".$filename, $exceptions);
           }
           else if(is_file($target."/".$filename))
           {
               $totalsize += filesize($target."/".$filename);
           }
       }
   }
   closedir($sourcedir);
   return $totalsize;
}


function showdate($date)
{

	$date = date("Y.m.d H:i:s", $date);
	//$date = str_replace("am","오전",$date);
	//$date = str_replace("pm","오후",$date);
	//$date = ereg_replace("0([0-9]분)","\\1", $date);

	return $date;
}


function page_bar_backup($total, $page, $max,$add_query="",$paging_type="inner"){
//($total, $page, $max, $templet_path="/bbs/bbs_templet/basic/", $add_query=""){
		global $nset;
		global $HTTP_URL;

		if(!$nset || $nset=="") $nset=1;//kbk


		if ($total % $max > 0){
			$total_page = floor($total / $max) + 1;
		}else{
			$total_page = floor($total / $max);
		}

		$total_nset = ceil($total_page/10);

		$next = (($nset)+1);
		$prev = (($nset)-1);

		if ($total){

				$first = "<a href='".$HTTP_URL."?board=".$this->bbs_config->dt[board_ename]."&".$paging_type_param."nset=".($nset-1)."&page=1$add_query' ".$paging_type_target."><img src='".$templet_path."/img/".$_SESSION["admininfo"]["language"]."/arrowleft01.gif' border=0 align=absmiddle onmouseover=\"this.src='".$templet_path."/img/".$_SESSION["admininfo"]["language"]."/arrowleft01.gif'\" onmouseout=\"this.src='".$templet_path."/img/".$_SESSION["admininfo"]["language"]."/arrowleft01.gif'\" style='vertical-align:middle;' /></a>&nbsp;";
				$prev_mark_10 = ($nset > 1) ? "<a href='".$HTTP_URL."?board=".$this->bbs_config->dt[board_ename]."&".$paging_type_param."nset=".($nset-1)."&page=".(($nset-2)*10+1)."$add_query' ".$paging_type_target."><img src='".$templet_path."/img/".$_SESSION["admininfo"]["language"]."/arrowleft02.gif' border=0 align=absmiddle onmouseover=\"this.src='".$templet_path."/img/".$_SESSION["admininfo"]["language"]."/arrowleft02.gif'\" onmouseout=\"this.src='".$templet_path."/img/".$_SESSION["admininfo"]["language"]."/arrow_prev.gif'\" style='vertical-align:middle;' /></a>&nbsp;" : "<img src='".$templet_path."/img/".$_SESSION["admininfo"]["language"]."/arrowleft02.gif' border=0 align=absmiddle style='vertical-align:middle;'>&nbsp;";
				$next_mark_10 = ($nset < $total_nset) ? "&nbsp;<a href='".$HTTP_URL."?board=".$this->bbs_config->dt[board_ename]."&".$paging_type_param."nset=".($nset+1)."&page=".($nset*10+1)."$add_query' ".$paging_type_target."> <img src='".$templet_path."/img/".$_SESSION["admininfo"]["language"]."/arrowright02.gif' border=0 align=absmiddle onmouseover=\"this.src='".$templet_path."/img/".$_SESSION["admininfo"]["language"]."/arrowright02.gif'\" onmouseout=\"this.src='".$templet_path."/img/".$_SESSION["admininfo"]["language"]."/arrowright02.gif'\" style='vertical-align:middle;' /></a>" :  "&nbsp;<img src='".$templet_path."/img/".$_SESSION["admininfo"]["language"]."/arrowright02.gif' border=0 align=absmiddle style='vertical-align:middle;'>";
				$last = " <a href='".$HTTP_URL."?board=".$this->bbs_config->dt[board_ename]."&".$paging_type_param."nset=".($nset-1)."&page=".$total_page."$add_query' ".$paging_type_target."><img src='".$templet_path."/img/".$_SESSION["admininfo"]["language"]."/arrowright01.gif' border=0 align=absmiddle onmouseover=\"this.src='".$templet_path."/img/".$_SESSION["admininfo"]["language"]."/arrowright01.gif'\" onmouseout=\"this.src='".$templet_path."/img/".$_SESSION["admininfo"]["language"]."/arrowright01.gif'\" style='vertical-align:middle;' /></a>&nbsp;";



		}


		if ($page >= 1 && $page <= 10){
			$nset = 1;
		}else if($page >= 11 && $page <= 20){
			$nset = 2;
		}else if($page >= 21 && $page <= 30){
			$nset = 3;
		}else if($page >= 31 && $page <= 40){
			$nset = 4;
		}else if($page >= 41 && $page <= 50){
			$nset = 5;
		}else if($page >= 51 && $page <= 60){
			$nset = 6;
		}else if($page >= 61 && $page <= 70){
			$nset = 7;
		}else if($page >= 71 && $page <= 80){
			$nset = 8;
		}else if($page >= 81 && $page <= 90){
			$nset = 9;
		}else if($page >= 91 && $page <= 100){
			$nset = 10;
		}

		$next = (($nset)+1);
		$prev = (($nset)-1);

	//	echo $total_page.":::".$next."::::".$prev."<br>";

		if ($total){
			/*
			$prev_mark = ($total_page > 1 || $page > 1) ? "<a href='".$HTTP_URL."?board=".$this->bbs_config->dt[board_ename]."&".$paging_type_param."nset=".($nset)."&page=".($page-1)."$add_query' ".$paging_type_target."><a href='".$HTTP_URL."?nset=".($nset)."&page=".($page-1)."$add_query' ><img src='".$templet_path."/img/prev.gif' border=0 align=top>&nbsp;</a>" : "<img src='".$templet_path."/img/prev.gif' border=0 align=top>&nbsp;";
			$next_mark = ($total_page > 1 || $total_page < $page) ? "&nbsp;&nbsp;<a href='".$HTTP_URL."?board=".$this->bbs_config->dt[board_ename]."&".$paging_type_param."nset=".($nset)."&page=".($page+1)."$add_query' ".$paging_type_target."><img src='".$templet_path."/img/next.gif' border=0 align=top></a>" :  "<img src='".$templet_path."/img/next.gif' border=0 align=top>&nbsp;";
			*/

		}

		$page_string = $prev_mark;

	//	for ($i = $page - 10; $i <= $page + 10; $i++)

		for ($i = ($nset-1)*10+1 ; $i <= (($nset-1)*10 + 10); $i++)
		{
			if ($i > 0)
			{
				if ($i <= $total_page)
				{
					if ($i != $page){
						$page_string .= '<span onclick="location.href=\''.$HTTP_URL.'?nset='.$nset.'&page='.$i.$add_query.'\';" style="color:#797979;border:1px solid #DCDCDC;font-weight:bold;padding:2px 6px;margin:0 2px;cursor:pointer;height:24px;" onmouseover="$(this).css({\'color\':\'red\', \'borderColor\':\'red\'});" onmouseout="$(this).css({\'color\':\'#797979\', \'borderColor\':\'#DCDCDC\'});">'.$i.'</span>';
						/*
						if($i != (($nset-1)*10+1)){
							$page_string = $page_string.("<font color='silver'>|</font> <a href='".$HTTP_URL."?board=".$this->bbs_config->dt[board_ename]."&nset=$nset&page=$i$add_query' style='font-weight:bold;color:gray' >$i</a> ");
						}else{
							$page_string = $page_string.(" <a href='".$HTTP_URL."?board=".$this->bbs_config->dt[board_ename]."&nset=$nset&page=$i$add_query' style='font-weight:bold;color:gray' >$i</a> ");
						}
						*/

					}else{
						$page_string .= '<span style="color:#333333;border:1px solid #333;font-weight:bold;padding:2px 6px;margin:0 2px;vertical-align:middle;color:#333;">'.$i.'</span>';
						/*
						if($i != (($nset-1)*10+1)){
							$page_string = $page_string.("<font color='silver'>|</font> <font color=#ff7635 style='font-weight:bold'>$i</font> ");
						}else{
							$page_string = $page_string.("<font color=#ff7635 style='font-weight:bold'>$i</font> ");
						}
						*/
					}


				}
			}
		}

		$page_string = $page_string.$next_mark;

		$page_string = $first.$prev_mark_10.$page_string.$next_mark_10.$last;

		$page_string="<div style='padding-bottom:1px;'>".$page_string."</div>";

		return $page_string;
	}

function page_bar_20131021($total, $page, $max,$add_query="",$paging_type="inner"){
	//$page_string;
	global $cid,$depth,$category_load, $company_id;
	global $nset, $orderby;
	global $HTTP_URL, $admininfo;
	//echo $HTTP_URL;

	if ($total % $max > 0){
		$total_page = floor($total / $max) + 1;
	}else{
		$total_page = floor($total / $max);
	}

	if ($nset == ""){
		$nset = 1;
	}

	$next = (($nset)*10+1);
	$prev = (($nset-2)*10+1);

	if($paging_type == "inner"){
		$paging_type_param = "view=innerview&";
		$paging_type_target = " target=act";
	}else{
		$paging_type_param = "";
		$paging_type_target = "";
	}


	//echo $total_page.":::".$next."::::".$prev."<br>";
	//&cid=$cid&depth=$depth&company_id=$company_id&orderby=$orderby
	if ($total){
		$prev_mark = ($prev > 0) ? "<a href='".$HTTP_URL."?".$paging_type_param."nset=".($nset-1)."&page=".(($nset-2)*10+1)."$add_query' ".$paging_type_target." style='padding:0px;margin:0px;' onclick='blockLoading();'><img src='/admin/images/paging/arrowleft02.gif' border=0  style='padding:0px;margin:0px; vertical-align:middle;' align='absmiddle'></a> " : "<img src='/admin/images/paging/arrowleft02.gif' border=0 style='vertical-align:middle;' align='absmiddle'> ";
		$next_mark = ($next <= $total_page) ? "<a href='".$HTTP_URL."?".$paging_type_param."nset=".($nset+1)."&page=".($nset*10+1)."$add_query' ".$paging_type_target." onclick='blockLoading();'><img src='/admin/images/paging/arrowright02.gif' border=0 style='vertical-align:middle;' align='absmiddle'></a>" :  " <img src='/admin/images/paging/arrowright02.gif' border=0  style='vertical-align:middle;' align='absmiddle'>";
	}

	$page_string = "";

//	for ($i = $page - 10; $i <= $page + 10; $i++)

	for ($i = ($nset-1)*10+1 ; $i <= (($nset-1)*10 + 10); $i++)
	{
		if ($i > 0)
		{
			if ($i <= $total_page)
			{
				if ($i != $page){
					if($i != (($nset-1)*10+1)){
						$page_string = $page_string.("<a href='".$HTTP_URL."?".$paging_type_param."nset=$nset&page=$i$add_query' style='font-weight:bold;color:gray' ".$paging_type_target." onclick='blockLoading();'><span style='color:#797979;border:1px solid #DCDCDC;font-weight:bold;padding:5px 6px;margin:0 1px;cursor:pointer;height:24px;'>".$i."</span></a> ");
					}else{
						$page_string = $page_string.(" <a href='".$HTTP_URL."?".$paging_type_param."nset=$nset&page=$i$add_query' style='font-weight:bold;color:gray;margin:0px;' ".$paging_type_target." onclick='blockLoading();'><span style='color:#797979;border:1px solid #DCDCDC;font-weight:bold;padding:5px 6px;margin:0 1px;cursor:pointer;height:24px;'>".$i."</span></a> ");
					}

				}else{
					if($i != (($nset-1)*10+1)){
						$page_string = $page_string.("<span style='color:#333333;border:1px solid #333;font-weight:bold;padding:5px 6px;margin:0 1px;color:#333;background:#fff7da;'>".$i."</span> ");
					}else{
						$page_string = $page_string.("<span style='color:#333333;border:1px solid #333;font-weight:bold;padding:5px 6px;margin:0 1px;color:#333;background:#fff7da;'>".$i."</span> ");
					}
				}


			}
		}
	}
	if($nset != "1"){
		$first_page_string = " <a href='".$HTTP_URL."?".$paging_type_param."nset=1&page=1$add_query' style='margin:0px;' ".$paging_type_target."><span style='color:#797979;border:1px solid #DCDCDC;font-weight:bold;padding:5px 6px;margin:0 2px;cursor:pointer;vertical-align:middle;' title='첫 페이지로'>1</span></a> <!--font color='silver'>|</font--> <span style='color:gray'>...</span>";
	}

	if($nset < (floor($total_page/10)+1)){
		$last_page_string = "<span style='color:gray'>...</span>  <a href='".$HTTP_URL."?".$paging_type_param."nset=".(floor($total_page/10)+1)."&page=$total_page$add_query' style='margin:0px;'  ".$paging_type_target."><span style='color:#797979;border:1px solid #DCDCDC;font-weight:bold;padding:5px 6px;margin:0 2px;cursor:pointer;' title='마지막 페이지로'>".$total_page."</span></a> ";
	}
	if ($total){
	$page_string = "<table border=0 ><tr><td style='padding:0;margin:0;'>".$prev_mark."</td><td nowrap style='height:26px;_padding:6px 0;margin:0;'>".$first_page_string.$page_string.$last_page_string."</td><td style='padding:0;margin:0;'>".$next_mark."</td><td nowrap style='padding:0;margin:0;' > <span style='margin-left:20px;'>페이지로 이동 <input type='text' class='textbox number' name='page' id='page' value='' size=4 style='margin-left:3px;' onkeydown='page_num=this.value;' onkeyup='page_num=this.value;' > / ".$total_page." <span onclick='goPage()' style='padding:5px 6px;cursor:pointer;border:1px solid silver;margin-left:5px;font-weight:bold;'>이동</span> </span></td></tr></table>
	<script language='javascript'>
		var paging_type = '$paging_type';
		var page_num = '';
		function goPage(){
			//alert(page_num);
			if(parseInt(page_num) <=  ".$total_page." && page_num != '' ){
				if(paging_type == 'inner'){
					window.frames['act'].location.href='?".$paging_type_param."nset='+(Math.floor(page_num/10)+1)+'&page='+page_num+'".$add_query."';
				}else{
					document.location.href='?nset='+(Math.floor(page_num/10)+1)+'&page='+page_num+'".$add_query."';
				}

			}else{
				alert('페이지 정보가 입력되지 않았거나 검색가능 페이지를 초과했습니다.');
			}
		}
	</script>
	";
	}

	return $page_string;
}



function page_bar($total, $page, $max,$add_query="",$paging_type="inner"){
	//$page_string;
	global $cid,$depth,$category_load, $company_id;
	global $nset, $orderby;
	global $HTTP_URL, $admininfo;

	if($direct_page){
		$page = $direct_page;
	}
	//echo $HTTP_URL;
	if(!$add_query){
		if($_SERVER["QUERY_STRING"] == "nset=$nset&page=$page"){
			$add_query = str_replace("nset=$nset&page=$page","",$_SERVER["QUERY_STRING"]) ;
		}else{
			$add_query = str_replace("nset=$nset&page=$page&","","&".$_SERVER["QUERY_STRING"]) ;

			//echo $_SERVER["QUERY_STRING"];
		}
	}

    if($add_query == '&'){
        $add_query = '';
    }

	if ($total % $max > 0){
		$total_page = floor($total / $max) + 1;
	}else{
		$total_page = floor($total / $max);
	}

	if ($nset == ""){
		$nset = 1;
	}

	$next = (($nset)*10+1);
	$prev = (($nset-2)*10+1);

	if($paging_type == "inner"){
		$paging_type_param = "view=innerview&";
		$paging_type_target = " target=act";
	}else{
		$paging_type_param = "";
		$paging_type_target = "";
	}


	//echo $total_page.":::".$next."::::".$prev."<br>";
	//&cid=$cid&depth=$depth&company_id=$company_id&orderby=$orderby
	if ($total){
		$prev_mark = ($prev > 0) ? "<a href='".$HTTP_URL."?".$paging_type_param."nset=".($nset-1)."&page=".(($nset-2)*10+1)."$add_query' ".$paging_type_target." style='padding:0px;margin:0px;' onclick='blockLoading();'><img src='/admin/images/paging/arrowleft02.gif' border=0  style='padding:0px;margin:0px; vertical-align:middle;' align='absmiddle'></a> " : "<img src='/admin/images/paging/arrowleft02.gif' border=0 style='vertical-align:middle;' align='absmiddle'> ";
		$next_mark = ($next <= $total_page) ? "<a href='".$HTTP_URL."?".$paging_type_param."nset=".($nset+1)."&page=".($nset*10+1)."$add_query' ".$paging_type_target." onclick='blockLoading();'><img src='/admin/images/paging/arrowright02.gif' border=0 style='vertical-align:middle;' align='absmiddle'></a>" :  " <img src='/admin/images/paging/arrowright02.gif' border=0  style='vertical-align:middle;' align='absmiddle'>";
	}

	$page_string = "";

//	for ($i = $page - 10; $i <= $page + 10; $i++)

	for ($i = ($nset-1)*10+1 ; $i <= (($nset-1)*10 + 10); $i++)
	{
		if ($i > 0)
		{
			if ($i <= $total_page)
			{
				if ($i != $page){
					if($i != (($nset-1)*10+1)){
						$page_string = $page_string.("<a href='".$HTTP_URL."?".$paging_type_param."nset=$nset&page=$i$add_query' style='font-weight:bold;color:gray' ".$paging_type_target." onclick='blockLoading();'><span style='color:#797979;border:1px solid #DCDCDC;font-weight:bold;padding:5px 6px;margin:0 1px;cursor:pointer;height:24px;'>".$i."</span></a> ");
					}else{
						$page_string = $page_string.(" <a href='".$HTTP_URL."?".$paging_type_param."nset=$nset&page=$i$add_query' style='font-weight:bold;color:gray;margin:0px;' ".$paging_type_target." onclick='blockLoading();'><span style='color:#797979;border:1px solid #DCDCDC;font-weight:bold;padding:5px 6px;margin:0 1px;cursor:pointer;height:24px;'>".$i."</span></a> ");
					}

				}else{
					if($i != (($nset-1)*10+1)){
						$page_string = $page_string.("<span style='color:#333333;border:1px solid #333;font-weight:bold;padding:5px 6px;margin:0 1px;color:#333;background:#fff7da;'>".$i."</span> ");
					}else{
						$page_string = $page_string.("<span style='color:#333333;border:1px solid #333;font-weight:bold;padding:5px 6px;margin:0 1px;color:#333;background:#fff7da;'>".$i."</span> ");
					}
				}


			}
		}
	}
	if($nset != "1"){
		$first_page_string = " <a href='".$HTTP_URL."?".$paging_type_param."nset=1&page=1$add_query' style='margin:0px;' ".$paging_type_target."><span style='color:#797979;border:1px solid #DCDCDC;font-weight:bold;padding:5px 6px;margin:0 2px;cursor:pointer;vertical-align:middle;' title='첫 페이지로'>1</span></a> <!--font color='silver'>|</font--> <span style='color:gray'>...</span>";
	}

	if($nset < (floor($total_page/10)+1)){
		$last_page_string = "<span style='color:gray'>...</span>  <a href='".$HTTP_URL."?".$paging_type_param."nset=".(floor($total_page/10)+1)."&page=$total_page$add_query' style='margin:0px;'  ".$paging_type_target."><span style='color:#797979;border:1px solid #DCDCDC;font-weight:bold;padding:5px 6px;margin:0 2px;cursor:pointer;' title='마지막 페이지로'>".$total_page."</span></a> ";
	}
	if ($total){
	$page_string = "<div id='page_area'><table border=0 ><tr><td style='padding:0;margin:0;'>".$prev_mark."</td><td nowrap style='height:26px;_padding:6px 0;margin:0;'>".$first_page_string.$page_string.$last_page_string."</td><td style='padding:0;margin:0;'>".$next_mark."</td><td nowrap style='padding:0;margin:0;' > <span style='margin-left:20px;'>페이지로 이동 <input type='text' class='textbox number' name='direct_page' id='page' value='' size=4 style='margin-left:3px;' onkeydown='page_num=this.value;' onkeyup='page_num=this.value;' > / ".$total_page." <span onclick=\"goPage(".$total_page.",'".$add_query."','".$paging_type_param."','".$paging_type."')\" style='padding:5px 6px;cursor:pointer;border:1px solid silver;margin-left:5px;font-weight:bold;'>이동</span> </span></td></tr></table>
	<script language='javascript'>
		//var paging_type = '$paging_type';

	</script></div>
	";
	}

	return $page_string;
}


if(!function_exists('getStandardCategoryPathByAdmin')){
	function getStandardCategoryPathByAdmin($cid, $depth='-1'){

		global $user;
		$tb = " standard_category_info ";
		if($cid == "" || strlen($cid) != '15'){
			return "전체";
		}
		$mdb = new Database;

		if($depth == '0'){
			$sql = "select * from ".$tb." where depth = 0 and cid LIKE '".substr($cid,0,3)."%' order by depth asc";
		}else if($depth == '1'){
			$sql = "select * from ".$tb." where depth <= 1 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%'))  order by depth asc";
		}else if($depth == '2'){
			$sql = "select * from ".$tb." where depth <= 2 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%'))  order by depth asc";
		}else if($depth == '3'){
			$sql = "select * from ".$tb." where depth <= 3 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0,12)."%'))  order by depth asc";
		}else if($depth == '4'){
			$sql = "select * from ".$tb." where depth <= 4 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0,12)."%') or (depth = 4 and cid LIKE '".substr($cid,0,15)."%'))  order by depth asc";
		}else{
			$sql = "select * from ".$tb." where depth <= 4 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0,12)."%') or (depth = 4 and cid LIKE '".substr($cid,0,15)."%'))  order by depth asc";
			return "전체";
		}
		//echo $sql."<br>";
		$mdb->query($sql);

		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);

			if($i == 0){
				$mstring .= $mdb->dt[cname];
			}else{
				$mstring .= " > ".$mdb->dt[cname];
			}
		}
		return $mstring;
	}
}


if(!function_exists('getCategoryPathByAdmin')){
	function getCategoryPathByAdmin($cid, $depth='-1'){

		global $user;
		$tb = (defined('TBL_SNS_CATEGORY_INFO'))	?	TBL_SNS_CATEGORY_INFO:TBL_SHOP_CATEGORY_INFO;
		if($cid == "" || strlen($cid) != '15'){
			return "전체";
		}
		$mdb = new Database;

		if($depth == '0'){
			$sql = "select * from ".$tb." where depth = 0 and cid LIKE '".substr($cid,0,3)."%' order by depth asc";
		}else if($depth == '1'){
			$sql = "select * from ".$tb." where depth <= 1 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%'))  order by depth asc";
		}else if($depth == '2'){
			$sql = "select * from ".$tb." where depth <= 2 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%'))  order by depth asc";
		}else if($depth == '3'){
			$sql = "select * from ".$tb." where depth <= 3 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0,12)."%'))  order by depth asc";
		}else if($depth == '4'){
			$sql = "select * from ".$tb." where depth <= 4 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0,12)."%') or (depth = 4 and cid LIKE '".substr($cid,0,15)."%'))  order by depth asc";
		}else{
			$sql = "select * from ".$tb." where depth <= 4 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0,12)."%') or (depth = 4 and cid LIKE '".substr($cid,0,15)."%'))  order by depth asc";
			return "전체";
		}
		//echo $sql."<br>";
		$mdb->query($sql);

		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);

			if($i == 0){
				$mstring .= $mdb->dt[cname];
			}else{
				$mstring .= " > ".$mdb->dt[cname];
			}
		}
		return $mstring;
	}
}

if(!function_exists('getContentPathByAdmin')){
	function getContentPathByAdmin($cid, $depth='-1'){

		global $user;
		$tb = "shop_content_class";
		if($cid == "" || strlen($cid) != '15'){
			return "전체";
		}
		$mdb = new Database;

		if($depth == '0'){
			$sql = "select * from ".$tb." where depth = 0 and cid LIKE '".substr($cid,0,3)."%' order by depth asc";
		}else if($depth == '1'){
			$sql = "select * from ".$tb." where depth <= 1 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%'))  order by depth asc";
		}else if($depth == '2'){
			$sql = "select * from ".$tb." where depth <= 2 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%'))  order by depth asc";
		}else if($depth == '3'){
			$sql = "select * from ".$tb." where depth <= 3 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0,12)."%'))  order by depth asc";
		}else if($depth == '4'){
			$sql = "select * from ".$tb." where depth <= 4 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0,12)."%') or (depth = 4 and cid LIKE '".substr($cid,0,15)."%'))  order by depth asc";
		}else{
			$sql = "select * from ".$tb." where depth <= 4 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0,12)."%') or (depth = 4 and cid LIKE '".substr($cid,0,15)."%'))  order by depth asc";
			return "전체";
		}
		//echo $sql."<br>";
		$mdb->query($sql);

		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);

			if($i == 0){
				$mstring .= $mdb->dt[cname];
			}else{
				$mstring .= " > ".$mdb->dt[cname];
			}
		}
		return $mstring;
	}
}

if(!function_exists('getLaundryPathByAdmin')){
	function getLaundryPathByAdmin($cid, $depth='-1'){

		global $user;
		$tb = 'shop_laundry_info';
		if($cid == "" || strlen($cid) != '15'){
			return "전체";
		}
		$mdb = new Database;

		if($depth == '0'){
			$sql = "select * from ".$tb." where depth = 0 and cid LIKE '".substr($cid,0,3)."%' order by depth asc";
		}else if($depth == '1'){
			$sql = "select * from ".$tb." where depth <= 1 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%'))  order by depth asc";
		}else if($depth == '2'){
			$sql = "select * from ".$tb." where depth <= 2 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%'))  order by depth asc";
		}else if($depth == '3'){
			$sql = "select * from ".$tb." where depth <= 3 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0,12)."%'))  order by depth asc";
		}else if($depth == '4'){
			$sql = "select * from ".$tb." where depth <= 4 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0,12)."%') or (depth = 4 and cid LIKE '".substr($cid,0,15)."%'))  order by depth asc";
		}else{
			$sql = "select * from ".$tb." where depth <= 4 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0,12)."%') or (depth = 4 and cid LIKE '".substr($cid,0,15)."%'))  order by depth asc";
			return "전체";
		}
		//echo $sql."<br>";
		$mdb->query($sql);

		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);

			if($i == 0){
				$mstring .= $mdb->dt[title];
			}else{
				$mstring .= " > ".$mdb->dt[title];
			}
		}
		return $mstring;
	}
}

if(!function_exists('getIventoryCategoryPathByAdmin')){
	function getIventoryCategoryPathByAdmin($cid, $depth='-1'){
		global $user;
		//$tb = (defined('TBL_SNS_CATEGORY_INFO'))	?	TBL_SNS_CATEGORY_INFO:TBL_SHOP_CATEGORY_INFO;
		$tb = $_SESSION['admin_config']["mall_inventory_category_div"]=="P"	?	TBL_SHOP_CATEGORY_INFO:"inventory_category_info";
		if($cid == ""){
			return "기타";
		}
		$mdb = new Database;

		if($depth == '0'){
			$sql = "select * from ".$tb." where depth = 0 and cid LIKE '".substr($cid,0,3)."%' order by depth asc";
		}else if($depth == '1'){
			$sql = "select * from ".$tb." where depth <= 1 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%'))  order by depth asc";
		}else if($depth == '2'){
			$sql = "select * from ".$tb." where depth <= 2 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%'))  order by depth asc";
		}else if($depth == '3'){
			$sql = "select * from ".$tb." where depth <= 3 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0,12)."%'))  order by depth asc";
		}else if($depth == '4'){
			$sql = "select * from ".$tb." where depth <= 4 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0,12)."%') or (depth = 4 and cid LIKE '".substr($cid,0,15)."%'))  order by depth asc";
		}else{
			$sql = "select * from ".$tb." where depth <= 4 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0,12)."%') or (depth = 4 and cid LIKE '".substr($cid,0,15)."%'))  order by depth asc";
			return "전체";
		}
		//echo $sql."<br>";
		$mdb->query($sql);

		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);

			if($i == 0){
				$mstring .= $mdb->dt[cname];
			}else{
				$mstring .= " > ".$mdb->dt[cname];
			}
		}
		return $mstring;
	}
}
/*
function page_bar($total, $page, $max,$add_query=""){
	$page_string;
	global $cid,$depth,$category_load, $company_id;
	global $nset, $orderby;

	if ($total % $max > 0){
		$total_page = floor($total / $max) + 1;
	}else{
		$total_page = floor($total / $max);
	}

	$next = (($nset)*10+1);
	$prev = (($nset-2)*10+1);

	if ($nset == ""){
		$nset = 1;
	}


	if ($total){
		$prev_mark = ($prev > 0) ? "<a href='./product_order.php?".$paging_type_param."nset=".($nset-1)."&page=".(($nset-2)*10+1)."&cid=$cid&depth=$depth&company_id=$company_id$add_query' ".$paging_type_target.">◀</a> " : "◁ ";
		$next_mark = ($next < $total_page) ? "<a href='./product_order.php?".$paging_type_param."nset=".($nset+1)."&page=".($nset*10+1)."&cid=$cid&depth=$depth&company_id=$company_id$add_query' ".$paging_type_target.">▶</a>" : " ▷";
	}

	$page_string = $prev_mark;

//	for ($i = $page - 10; $i <= $page + 10; $i++)

	for ($i = ($nset-1)*10+1 ; $i <= (($nset-1)*10 + 10); $i++)
	{
		if ($i > 0)
		{
			if ($i <= $total_page)
			{
				if ($i != $page){
					$page_string = $page_string.(" <a href='./product_order.php?".$paging_type_param."nset=$nset&page=$i&cid=$cid&depth=$depth&company_id=$company_id$add_query' ".$paging_type_target.">$i</a> ");
				}else{
					$page_string = $page_string.("<font color=#FF0000>$i</font>");
				}
			}
		}
	}

	$page_string = $page_string.$next_mark;

	return $page_string;
}
*/

function page_bar_search($total, $page, $max,$listtype=""){
	$page_string;
	global $cid,$depth,$category_load;
	global $nset,$pid, $pname, $from_price, $to_price,$mode;

	if ($total % $max > 0){
		$total_page = floor($total / $max) + 1;
	}else{
		$total_page = floor($total / $max);
	}

	$next = (($nset)*10+1);
	$prev = (($nset-2)*10+1);

	if ($nset == ""){
		$nset = 1;
	}


	if ($total){
		$prev_mark = ($prev > 0) ? "<a href='".$HTTP_URL."?nset=".($nset-1)."&page=".(($nset-2)*10+1)."&cid=$cid&depth=$depth&listtype=$listtype&pid=$pid&pname=$pname&mode=$mode&from_price=$from_pirce&to_price=$to_price'>◀</a> " : "◁ ";
		$next_mark = ($next < $total_page) ? "<a href='".$HTTP_URL."?nset=".($nset+1)."&page=".($nset*10+1)."&cid=$cid&depth=$depth&listtype=$listtype&pid=$pid&pname=$pname&mode=$mode&from_price=$from_pirce&to_price=$to_price'>▶</a>" : " ▷";
	}

	$page_string = $prev_mark;

//	for ($i = $page - 10; $i <= $page + 10; $i++)

	for ($i = ($nset-1)*10+1 ; $i <= (($nset-1)*10 + 10); $i++)
	{
		if ($i > 0)
		{
			if ($i < $total_page)
			{
				if ($i != $page){
					$page_string = $page_string.(" <a href=".$HTTP_URL."?nset=$nset&page=$i&cid=$cid&depth=$depth&listtype=$listtype&pid=$pid&pname=$pname&mode=$mode&from_price=$from_pirce&to_price=$to_price>$i</a> ");
				}else{
					$page_string = $page_string.("<font color=#FF0000>$i</font>");
				}
			}
		}
	}

	$page_string = $page_string.$next_mark;

	return $page_string;
}



function CompanyList($company_id, $dispaly_type="", $onchange="basic",$max="", $script=true,$select_id='company_id',$input_id='company_id',$input_name='com_name',$validation = 'false'){//behavior: url('../js/selectbox.htc');
	global $admininfo, $HTTP_URL;
	global $admin_config;

	if(!$admin_config[mall_use_multishop]){
		return "";
	}

	if($onchange == "basic"){
		$onchange = "onchange=\"document.location.href='".$HTTP_URL."?company_id='+this.value\"";
	} else if($onchange =="max") {
		$onchange = "onchange=\"document.location.href='".$HTTP_URL."?company_id='+this.value+'&max=".$max."'\"";
	}

	if($_SESSION["admininfo"][admin_level] != 9){
		return "";
	}
	$mdb = new Database;
	if($_SESSION["admininfo"]["mem_type"] == "MD"){
		$addWhere = " and ccd.company_id in (".getMySellerList($_SESSION["admininfo"][charger_ix]).") ";
	}

	$sql = "SELECT count(*) as total FROM ".TBL_COMMON_COMPANY_DETAIL." ccd  where ccd.com_type in ('S','A') and ccd.seller_auth = 'Y' $addWhere ";
	$mdb->query($sql);
	$mdb->fetch();
	$total = $mdb->dt[total];
	if($total < constant("MAX_VENDOR_VIEW_CNT")){

			$sql = "SELECT ccd.company_id, com_name  FROM ".TBL_COMMON_COMPANY_DETAIL." ccd  where ccd.com_type in ('S','A') and ccd.seller_auth = 'Y' $addWhere order by com_name asc";
			$mdb->query($sql);

			$mstring ="<select name='company_id' id='".$select_id."' style=\"$dispaly_type height: 20px; font-size:12px;\" $onchange >";
			$mstring .="<option value=''>전체보기</option>";
			for($i=0;$i<$mdb->total;$i++){
			$mdb->fetch($i);
				if($company_id == $mdb->dt[company_id]){
					$mstring .="<option value='".$mdb->dt[company_id]."' selected>".$mdb->dt[com_name]."</option>";
				}else{
					$mstring .="<option value='".$mdb->dt[company_id]."'>".$mdb->dt[com_name]."</option>";
				}
			}
			$mstring .="</select>";
			if($script){
				$mstring .= "
				<script>
				$(function() {
					if(typeof($( '#".$select_id."').combobox) != 'undefined') {//함수가 있을때만 검사 kbk 13/04/16
						$( '#".$select_id."').combobox();
					}
				});
				</script>";
			}
	}else{

		if($company_id){
			$sql = "SELECT com_name, ccd.company_id FROM ".TBL_COMMON_COMPANY_DETAIL." ccd  where company_id = '".$company_id."' ";
			$mdb->query($sql);
			$mdb->fetch();
			$com_name = $mdb->dt[com_name];
		}

		$mstring =	"
						<table cellpadding=0 cellspacing=0>
						<tr>
							<td><input type=hidden class='textbox' name='company_id' id='".$input_id."'  value='".$company_id."' validation='".$validation."'></td>
							<td><input type=text class='textbox point_color' name='".$input_name."' id='".$input_name."' value='".$com_name."' style='width:140px;' readonly  validation='".$validation."' title='입점업체'></td>
							<td style='padding-left:5px;'>
								<img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"PoPWindow('../seller_search.php?code=".$db->dt[code]."&select_id=$select_id&input_id=$input_id&input_name=$input_name',600,380,'seller_search')\"  style='cursor:pointer;'>
							</td>
							<td style='padding-left:3px;'>
								<img src='../images/btn_x.gif' style='cursor:pointer' onclick=\"$(this).parent().parent().find('#".$input_id."').val('');$(this).parent().parent().find('#com_name').val('');\">
							</td>
						</tr>
					</table>";
	}

	return $mstring;
}




function BoardSelect($board_ename,$return_type="select"){
	$mdb = new Database;


	$mdb->query("select bmc.* from bbs_manage_config bmc , bbs_group bg where bmc.board_group = bg.div_ix and disp = 1");

	if($return_type=="select"){
		$mstring ="<select name='bbs_name' class='select_box'  >\n";
		$mstring .="<option value=''>게시판 선택</option>\n";
		for($i=0;$i<$mdb->total;$i++){
			$mdb->fetch($i);
			if($mdb->dt[board_ename] == $board_ename){
				$mstring .="<option value='".$mdb->dt[board_ename]."' selected>".$mdb->dt[board_name]."</option>\n";
			}else{
				$mstring .="<option value='".$mdb->dt[board_ename]."'>".$mdb->dt[board_name]."</option>\n";
			}
		}
		$mstring .="</select>\n";
	}else if($return_type=="array"){
		$mstring = $mdb->fetchall("object");
	}
	return $mstring;

}



function BoardTempleteSelect($bt_ix){
$mdb = new Database;


$mdb->query("select * from ".TBL_BBS_TEMPLETE." where disp = 1 ");

	$mstring ="<select name='bt_ix' class='select_box'  >\n";
	$mstring .="<option value=''>템플릿 선택하기</option>\n";
	for($i=0;$i<$mdb->total;$i++){
		$mdb->fetch($i);
		if($mdb->dt[bt_ix] == $bt_ix){
			$mstring .="<option value='".$mdb->dt[bt_ix]."' selected>".$mdb->dt[board_tmp_name]."</option>\n";
		}else{
			$mstring .="<option value='".$mdb->dt[bt_ix]."'>".$mdb->dt[board_tmp_name]."</option>\n";
		}
	}
	$mstring .="</select>\n";

	return $mstring;

}


function SiteUseTemplete($domain){
	$mdb = new Database;
	$mdb->query("select mall_use_templete from ".TBL_SHOP_SHOPINFO." ");
	$mdb->fetch();
	return $mdb->dt[mall_use_templete];
}

function CompareReturnValue($this_value,$value,$str=' selected'){
	if(is_array($value)){
		for($z=0;$z < count($value);$z++){
			if($value[$z] == $this_value){
				return $str;
			}
		}
	}else{
		if($this_value == $value){
			return $str;
		}else{
			return "";
		}
	}
}

function regProductCnt(){
	$mdb = new Database;
	$mdb->query("select count(*) as product_cnt from ".TBL_SHOP_PRODUCT." ");
	$mdb->fetch();
	return $mdb->dt[product_cnt];
}


function sizeCal( $size )
{
$k      = 1024;         // 킬로
$m      = $k * 1024;    // 메가
$g      = $m * 1024;    // 기가
$t	= $g * 1024;    // 테라

    //if ( $size < 0 ) { $t = 0; $size = abs($size); } else $t = 1;


    if ( $size >= $t )
    {
        $size = intval( $size / $t ) . " TB";
    }
        elseif ( $size >= $g )
        {
                $size = intval( $size / $g ) . " GB";
        }
        elseif ( $size >= $m )
        {
                $size = intval( $size / $m ) . " MB";
        }
        elseif ( $size >= $k )
        {
                $size = intval( $size / $k ) . " KB";
        }
        else
        {
                $size .= " B";
        }

    $size = ( $t ) ? "$size":"- $size";

    return $size;
}

function getMyDBinfo($type="all"){
	$db = new Database;
	if($db->dbms_type == "oracle"){

	}else{
		if($type == "all"){
			$db->query("SHOW TABLE STATUS");
		}else{
			$db->query("SHOW TABLE STATUS where name LIKE 'bbs_%'");
		}
		$first_mb                = "100";
		// 기본값
		$k      = 1024;         // 킬로
		$m      = $k * 1024;    // 메가
		$g      = $m * 1024;    // 기가
		$t    = $g * 1024;    // 테라

		// 나의 디비 용량
		$mySize = $first_mb * $m;

		for($i=0;$i < $db->total;$i++){
		$db->fetch($i);
		$size = $db->dt['Data_length'] + $db->dt['Index_length'];
		$sum += $size;
		$sizeShow = sizeCal( $size );

		}

		$r_mySize = sizeCal( $mySize );
		$r_sum    = sizeCal( $sum );
		$rt       = sizeCal( $mySize - $sum );
	}
	return $r_sum;
}

function returnZeroValue($value){
	if($value == ""){
		return 0;
	}else{
		return $value;
		//return number_format($value,1);// 로그분석에서 숫자표현을 제대로 못해서 주석처리함 kbk 12/04/02
	}
}

//이름2로 변경 이미 ChangeDate는 함수가 존재해서 여기를 타지 않는다. 2015-01-21 박윤완
if (!function_exists('ChangeDate2')) {
	function ChangeDate2($date="", $format="Y년m월d일"){
		if(empty($date)){
			return "";
		}else{
			return date($format,mktime(0,0,0,substr($date,5,2),substr($date,8,2),substr($date,0,4)));
		}
	}
}

function categorySelect($cid, $default_text="카테고리 선택하기"){
	$mdb = new Database;

	$sql = "select cname,cid from shop_category_info where depth = '0' and category_use = 1 order by vlevel1";
	$mdb->query($sql);
	$mstring ="<select name='category_choice' class='select_box' style='font-size:12px;font-family;돋움' >\n";
	$mstring .="<option value=''>".$default_text."</option>\n";
	for($i=0;$i<$mdb->total;$i++){
		$mdb->fetch($i);
		if(substr($mdb->dt[cid],0,3) == $cid){
			$mstring .="<option value='".substr($mdb->dt[cid],0,3)."' selected>".$mdb->dt[cname]."</option>\n";
		}else{
			$mstring .="<option value='".substr($mdb->dt[cid],0,3)."'>".$mdb->dt[cname]."</option>\n";
		}
	}
	$mstring .="</select>\n";

	return $mstring;
}

function user_level($code){
	$mdb = new Database;

	$mdb->query("select gp_ix from common_member_detail where code = '".$code."' ");
	$mdb->fetch();

	return $mdb->dt[gp_ix];
}

function getGlobalInfo(){

	include_once($_SERVER["DOCUMENT_ROOT"]."/admin/logstory/class/sharedmemory.class");

	$shmop = new Shared("global_rule");
	$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$_SESSION["admininfo"]["mall_data_root"]."/_shared/";
	$shmop->SetFilePath();
	$globalInfo = $shmop->getObjectForKey("global_rule");
	$globalInfo = unserialize(urldecode($globalInfo));

	return $globalInfo;
}


function getCurrencyInfo($currency_ix="" , $property="" , $return_type="select"){
	global $admininfo;
	$mdb = new MySQL;
	$mdb->query("SELECT * FROM global_currency where disp = 1 ");
	$currencys = $mdb->fetchall("object");

	if($return_type=="select"){
		$mstring = "<select name='currency_ix' id='currency_ix' $property validation='true' title='화폐단위'>";
		$mstring .= "<option value=''>화폐단위</option>";
		for($i=0;$i < count($currencys);$i++){
		$mstring .= "<option value='".$currencys[$i][currency_ix]."' ".($currencys[$i][currency_ix] == $currency_ix ? "selected":"").">".$currencys[$i][currency_name]."(".$currencys[$i][currency_code].")</option>";
		}
		 
		$mstring .= "</select>";
	}elseif($return_type=="array"){
		$mstring = $currencys;
	}

	return $mstring;
 
}

function getLanguageType($language_ix="" , $property="" , $return_type="select"){
	global $admininfo;
	$mdb = new MySQL;
	$mdb->query("SELECT * FROM global_language where disp = 1 ");
	$languages = $mdb->fetchall("object");

	if($return_type=="select"){
		$mstring = "<select name='language_ix' id='language_ix' $property validation='true' title='언어'>";
		$mstring .= "<option value=''>언어선택</option>";
		for($i=0;$i < count($languages);$i++){
		$mstring .= "<option value='".$languages[$i][language_ix]."' ".($languages[$i][language_ix] == $language_ix ? "selected":"").">".$languages[$i][language_name]."(".$languages[$i][language_code].")</option>";
		}
		if(strpos($_SERVER['SCRIPT_FILENAME'], 'excel_input') == false){
			if(strpos($_SERVER['SCRIPT_FILENAME'], 'translation_pop') == false){
			//	$mstring .= "<option value='only_searchtexts' ".($trans_type == 'only_searchtexts' ? "selected":"").">검색어만 검색</option>";
			}
		}
		$mstring .= "</select>";
	}elseif($return_type=="array"){
		$mstring = $languages;
	}

	return $mstring;
 
}

function getTranslationType($trans_type="" , $property="" , $return_type="select"){
	global $admininfo;
	$mdb = new MySQL;
	$mdb->query("SELECT * FROM global_language where disp = 1 ");
	$languages = $mdb->fetchall("object");

	if($return_type=="select"){
		$mstring = "<select name='trans_type' $property validation='true' title='".(strpos($_SERVER['SCRIPT_FILENAME'], 'excel_input') !== false ? "사용자 언어":"검색 타입")."'>";
		$mstring .= "<option value=''>".(strpos($_SERVER['SCRIPT_FILENAME'], 'excel_input') !== false ? "랭귀지선택":"타입 선택")."</option>";
		for($i=0;$i < count($languages);$i++){
		$mstring .= "<option value='".$languages[$i][language_code]."' ".($languages[$i][language_code] == $trans_type ? "selected":"").">".$languages[$i][language_name]."(".$languages[$i][language_code].")</option>";
		}
		if(strpos($_SERVER['SCRIPT_FILENAME'], 'excel_input') == false){
			if(strpos($_SERVER['SCRIPT_FILENAME'], 'translation_pop') == false){
				$mstring .= "<option value='only_searchtexts' ".($trans_type == 'only_searchtexts' ? "selected":"").">검색어만 검색</option>";
			}
		}
		$mstring .= "</select>";
	}elseif($return_type=="array"){
		$mstring = $languages;
	}

	return $mstring;
 
}


function getGroupinfo($code){
	$mdb = new Database;

	$mdb->query("select gp_name from shop_groupinfo gp , common_member_detail m where m.code = '".$code."' and gp.gp_ix = m.gp_ix");
	$mdb->fetch();

	return $mdb->dt[gp_name].";".$mdb->dt[organization_name];
}

/* 상품등록 페이지에서 옮긴 함수*/


function rmdirr($target,$verbose=false)
// removes a directory and everything within it
{
$exceptions=array('.','..');
if (!$sourcedir=@opendir($target))
   {
   if ($verbose)
       echo '<strong>Couldn&#146;t open '.$target."</strong><br />\n";
   return false;
   }
while(false!==($sibling=readdir($sourcedir)))
   {
   if(!in_array($sibling,$exceptions))
       {
       $object=str_replace('//','/',$target.'/'.$sibling);
       if($verbose)
           echo 'Processing: <strong>'.$object."</strong><br />\n";
       if(is_dir($object))
           rmdirr($object);
       if(is_file($object))
           {
           $result=@unlink($object);
           if ($verbose&&$result)
               echo "File has been removed<br />\n";
           if ($verbose&&(!$result))
               echo "<strong>Couldn&#146;t remove file</strong>";
           }
       }
   }
closedir($sourcedir);
if($result=@rmdir($target))
   {
   if ($verbose)
       echo "Target directory has been removed<br />\n";
   return true;
   }
if ($verbose)
   echo "<strong>Couldn&#146;t remove target directory</strong>";
return false;
}


function ClearText($str){
	return str_replace(">","",$str);
}


function returnFileName($filestr){
	$strfile = split("/",$filestr);
	$file_name = str_replace("%20","",$strfile[count($strfile)-1]);
	$file_name = str_replace("?\$PRODMAIN\$",".jpg",$file_name);
	$file_name = str_replace("?\$anfProduct\$",".png",$file_name);
	$file_name = str_replace("?$mainlarge$",".jpg",$file_name);

	return $file_name;
	//return count($strfile);

}

function returnImagePath($str){
	$IMG = split(" ",$str);

	for($i=0;$i<count($IMG);$i++){
		//echo substr_count($IMG[$i],"src");
			if(substr_count($IMG[$i],"src=") > 0){
				$mstring = str_replace("src=","",$IMG[$i]);
				return str_replace("\"","",$mstring);
			}
	}
}

function imageExists($image,$dir) {

    $i=1; $probeer=$image;

    while(file_exists($dir.$probeer)) {
        $punt=strrpos($image,".");
        if(substr($image,($punt-3),1)!==("[") && substr($image,($punt-1),1)!==("]")) {
            $probeer=substr($image,0,$punt)."[".$i."]".
            substr($image,($punt),strlen($image)-$punt);
        } else {
            $probeer=substr($image,0,($punt-3))."[".$i."]".
            substr($image,($punt),strlen($image)-$punt);
        }
        $i++;
    }
    return $probeer;
}

/*구매대행 필터에서 옮긴 함수*/



function strip_javascript($filter){

    // realign javascript href to onclick
    $filter = preg_replace("/href=(['\"]).*?javascript:(.*)? \\1/i", "onclick=' $2 '", $filter);

    //remove javascript from tags
    while( preg_match("/<(.*)?javascript.*?\(.*?((?>[^()]+) |(?R)).*?\)?\)(.*)?>/i", $filter))
        $filter = preg_replace("/<(.*)?javascript.*?\(.*?((?> [^()]+)|(?R)).*?\)?\)(.*)?>/i", "<$1$3$4$5>", $filter);

    // dump expressions from contibuted content
    if(0) $filter = preg_replace("/:expression\(.*?((?>[^ (.*?)]+)|(?R)).*?\)\)/i", "", $filter);

    while( preg_match("/<(.*)?:expr.*?\(.*?((?>[^()]+)|(? R)).*?\)?\)(.*)?>/i", $filter))
        $filter = preg_replace("/<(.*)?:expr.*?\(.*?((?>[^()] +)|(?R)).*?\)?\)(.*)?>/i", "<$1$3$4$5>", $filter);

    // remove all on* events
    while( preg_match("/<(.*)?\s?on.+?=?\s?.+?(['\"]).*?\\2 \s?(.*)?>/i", $filter) )
       $filter = preg_replace("/<(.*)?\s?on.+?=?\s?.+? (['\"]).*?\\2\s?(.*)?>/i", "<$1$3>", $filter);

    return $filter;
}

function getAdminName(){
	$mdb = new Database;

	$mdb->query("select com_name from ".TBL_COMMON_COMPANY_DETAIL."  where com_type in ('S','A') ");
	$mdb->fetch();

	return $mdb->dt[com_name];
}


function getDeliveryPrice2($oid){
	$mdb = new Database;
	$sql1 = "select sum(delivery_price) from shop_order_delivery where oid = '$oid' and delivery_pay_type in ('1','3') group by oid";
    $mdb->query($sql1);
	$result = $mdb->fetch();
	$delivery_price1 = $result[0];

    $mdb2 = new Database;
	$sql2 = "select sum(delivery_price) from shop_order_delivery where oid = '$oid' and delivery_pay_type = 2 group by oid";
	$mdb2->query($sql2);
    $result = $mdb2->fetch();
    $delivery_price2 = $result[0];

	return "선불배송비 : ".number_format($delivery_price1)."&nbsp;&nbsp; 착불배송비 : ".number_format($delivery_price2);
}

function admin_log($crud_div,$charger_id,$company_id)
{
	global $admininfo;

	$mdb = new Database;
	/*
	$sql = "select ccd.com_name, AES_DECRYPT(UNHEX(cu.name),'".$mdb->ase_encrypt_key."') as name
			from common_company_detail ccd join  common_user cu on ccd.company_id = cu.company_id and cu.company_id = '$company_id' and cu.id = '$charger_id'";
	*/
	if($mdb->dbms_type == "oracle"){
		$sql = "select ccd.com_name, AES_DECRYPT(cmd.name,'".$mdb->ase_encrypt_key."') as name
			from common_user cu, common_member_detail cmd ,  common_company_detail ccd
			where cu.code = cmd.code and cu.company_id = ccd.company_id
			and cu.company_id = '$company_id'
			and cu.id = '$charger_id'";
			//echo $sql;
	}else{
		$sql = "select ccd.com_name, AES_DECRYPT(UNHEX(cmd.name),'".$mdb->ase_encrypt_key."') as name
			from common_user cu, common_member_detail cmd ,  common_company_detail ccd
			where cu.code = cmd.code and cu.company_id = ccd.company_id
			and cu.company_id = '$company_id'
			and cu.id = '$charger_id'";
	}

	$mdb->query($sql);
	$mdb->fetch();


	$sql = "insert into admin_log(log_ix,accept_com_name,accept_m_name,admin_id,admin_name,crud_div,ip,regdate) values('','".$mdb->dt[com_name]."','".$mdb->dt[name]."','".$_SESSION["admininfo"]['charger_id']."','".$_SESSION["admininfo"]['charger']."','$crud_div','".$_SERVER["REMOTE_ADDR"]."',NOW())";
	$mdb->sequences = "ADMIN_LOG_SEQ";
	$mdb->query($sql);


}

function con_log($act,$id,$company_name)
{
	$mdb = new Database;

	if($act == "login")
	{
		$sql = "insert into con_log(con_ix, con_id,con_name,ip,log_date,log_div) values('','$id','$company_name','".$_SERVER["REMOTE_ADDR"]."',NOW(),'I')";
		$mdb->sequences = "CON_LOG_SEQ";
		$mdb->query($sql);
	}
	else if($act == "logout")
	{
		$sql = "insert into con_log(con_ix,con_id,con_name,ip,log_date,log_div) values('','$id','$company_name','".$_SERVER["REMOTE_ADDR"]."',NOW(),'O')";
		$mdb->sequences = "CON_LOG_SEQ";
		$mdb->query($sql);
	}
}

$arr_couponList = array();


function Black_list_check($code,$word){
	$mdb = new Database;

		$sql = "select black_list from common_member_detail where code='".$code."' and black_list ='Y' ";
		$mdb->query($sql);
		if($mdb->total)	{
			$mdb->fetch();
			$mstring = "<span style='color:red'>".$word."</span>";
		}	else	{
			$mstring = $word;
		}

	return $mstring;

}

function bbs_response_templet_selectbox($target,$templet_div=""){
	$mdb = new Database;

	$sql = "SELECT * from bbs_response_templet where disp='1' and templet_div in ('','".$templet_div."') ";
	$mdb->query($sql);

	$massage="<select onChange=\"bbs_response_templet($(this),'".$target."')\">";
	$massage.="<option value=''>탬플릿을 선택해 주세요.</option>";

	if($mdb->total){
		for($i=0;$i < $mdb->total; $i++){
			$mdb->fetch($i);
			$massage.="<option value='".$mdb->dt[templet_text]."'>".$mdb->dt[templet_name]."</option>";
		}
	}

	$massage.="</select >";

	return $massage;
}

function common_response_templet_selectbox($type){
	$mdb = new Database;

	$sql = "SELECT * from common_support_response where disp='1' and type = '".$type."' ";
	$mdb->query($sql);

	$massage="<select onChange=\"common_response_templet($(this),'reply')\">";
	$massage.="<option value=''>탬플릿을 선택해 주세요.</option>";

	if($mdb->total){
		for($i=0;$i < $mdb->total; $i++){
			$mdb->fetch($i);
			$massage.="<option value='".$mdb->dt[templet_text]."'>".$mdb->dt[templet_name]."</option>";
		}
	}

	$massage.="</select >";

	return $massage;
}



function CouponPublishSelectBox($publish_ix,$select_name,$property=""){
	global $arr_couponList;
	$mdb = new Database;

	if($arr_couponList !== false && !count($arr_couponList))	{
		/*
		$sql = "select cp.*,c.cupon_kind
					from ".TBL_SHOP_CUPON."  c inner join ".TBL_SHOP_CUPON_PUBLISH." cp on c.cupon_ix = cp.cupon_ix
					where  c.cupon_ix > 0 and cp.use_date_type = '3' and '".date("Ymd")."' between date_format(cp.use_sdate,'%Y%m%d') and  date_format(cp.use_edate,'%Y%m%d') order by cp.regdate desc";
		*/

		$sql = "select cp.*,c.cupon_kind
					from ".TBL_SHOP_CUPON."  c inner join ".TBL_SHOP_CUPON_PUBLISH." cp on c.cupon_ix = cp.cupon_ix
					where  c.cupon_ix > 0 and ((cp.use_date_type!='9' AND '".date("Ymd")."' between cp.use_sdate and cp.use_edate) OR cp.use_date_type=9 OR cp.use_date_type=2) order by cp.regdate desc";


		//echo $sql;
		$mdb->query($sql);
		if($mdb->total)	{


			for($i = 0; $i < $mdb->total; $i++)	{
				$mdb->fetch($i);
				$arr_couponList[] = $mdb->dt;
			}
		}	else	{
			$arr_couponList = false;
		}
	}
	//print_r($arr_couponList);
	$arr_dateType = array(1=>'년','개월','일');
	$mstring = "<select name='$select_name' id='$select_name' style='width:210px;font-size:11px;' $property>";
	$mstring .= "<option value=''>발행쿠폰 전체 목록</option>";
	if(is_array($arr_couponList)){
	foreach($arr_couponList as $_key=>$_val)	{
		switch($_val['use_date_type'] == 1)	{
			case 1:
				$use_date_type = '발행일';
				$priod_str = $use_date_type."로부터 ".$_val['publish_date_differ']." ".$arr_dateType[$_val['publish_date_type']]."간";
			break;
			case 2:
				$use_date_type = '등록일';
				$priod_str = $use_date_type."로부터 ".$_val['regist_date_differ']." ".$arr_dateType[$_val['regist_date_type']]."간";
			break;
			case 3:
				$use_date_type = '사용기간';
				$priod_str = $use_date_type." : ".ChangeDate($_val['use_sdate'],"Y-m-d")." ~ ".ChangeDate($_val['use_edate'],"Y-m-d")." ";
			case 9:
				$priod_str = "";
			break;
		}
		$mstring .= "<option value='".$_val['publish_ix']."'".($_val['publish_ix'] == $publish_ix ? " selected":"")." title='".$priod_str."'>".$_val['cupon_kind']." ".$_val['cupon_no']."</option>";
	}
	}
	if(!$arr_couponList)	{
		$mstring .= "<option value=''>".$msg."</option>";
	}
	$mstring .= "</select>";
	return $mstring;
}

function CouponRuleSelectBox($publish_ix,$select_name,$property="",$select_id=''){
	global $arr_couponList;
	$mdb = new Database;

	if($select_id == ''){
		$select_id = $select_name;
	}
	//if($arr_couponList !== false && !count($arr_couponList))	{
	/********** 회원전용 쿠폰을 위해 수정함 ***********/
	// mobile_member_publish_ix부분 추가 2015-01-20박윤완
	if($select_name=="member_publish_ix" || $select_id=="member_publish_ix"|| $select_name=="mobile_member_publish_ix" || $select_id=="mobile_member_publish_ix") $publish_type="3";
	else if ($select_name=="appoint_publish_ix") $publish_type="1";
	else $publish_type="2";
	$arr_couponList=array();
	/********** 회원전용 쿠폰을 위해 수정함 kbk 12/06/13 ***********/ //use_sdate 에 date_format 한 이유는 오라클때문에~
	//date("Ymd") 에서 date("Y-m-d H:i:s") 로 변경 2015-01-21 박윤완
	//아마도 cp.use_date_type=1 조건을 줘야 할거 같다. use_date_type= 3(발행일) 일때만 use_sdate에 자료가 들어가 있다.
		$sql = "select cp.*,c.cupon_kind
					from ".TBL_SHOP_CUPON."  c inner join ".TBL_SHOP_CUPON_PUBLISH." cp on c.cupon_ix = cp.cupon_ix
					where  c.cupon_ix > 0 and ((cp.use_date_type!='9' AND '".date("Y-m-d H:i:s")."' between cp.use_sdate and cp.use_edate) OR cp.use_date_type=9 OR cp.use_date_type=2) and cp.publish_type= '".$publish_type."'  order by cp.regdate desc";// and cp.use_date_type = 3
					//특정조건만 나오게 하는 원인을 몰라서 일단 다 나오게 수정함 with 신실장님 kbk 12/06/11
		//echo $sql;
		$mdb->query($sql);
		if($mdb->total)	{


			for($i = 0; $i < $mdb->total; $i++)	{
				$mdb->fetch($i);
				$arr_couponList[] = $mdb->dt;
			}
		}	else	{
			$arr_couponList = false;
		}
	//}
	//print_r($arr_couponList);
	$arr_dateType = array(1=>'년','개월','일');
	$mstring = "<select name='$select_name' id='$select_id' style='width:290px;font-size:12px;' $property>";
	$mstring .= "<option value=''>발행쿠폰 전체 목록</option>";
	if(is_array($arr_couponList)){
		foreach($arr_couponList as $_key=>$_val)	{
			switch($_val['use_date_type'])	{
				case 1:
					$use_date_type = '발행일';
					$priod_str = $use_date_type."로부터 ".$_val['publish_date_differ']." ".$arr_dateType[$_val['publish_date_type']]."간";
				break;
				case 2:
					$use_date_type = '등록일';
					$priod_str = $use_date_type."로부터 ".$_val['regist_date_differ']." ".$arr_dateType[$_val['regist_date_type']]."간";
				break;
				case 3:
					$use_date_type = '사용기간';
					//$priod_str = $use_date_type." : ".substr($_val['use_sdate'],0,10)." ~ ".substr($_val['use_edate'],0,10)." ";
					$priod_str = $use_date_type." : ".ChangeDate2($_val['use_sdate'],"Y-m-d")." ~ ".ChangeDate2($_val['use_edate'],"Y-m-d")." ";
				break;
				case 9://case 9 무기한 설정해줌 2015-01-21 박윤완
					$priod_str = "";
				break;
			}
			$mstring .= "<option value='".$_val['publish_ix']."'".($_val['publish_ix'] == $publish_ix ? " selected":"")." title='".$priod_str."'>".$_val['cupon_kind']." ".$priod_str."</option>";
		}
	}
	if(!$arr_couponList)	{
		$mstring .= "<option value=''>".$msg."</option>";
	}
	$mstring .= "</select>";
	return $mstring;
}


function InputFileHtmlControl($name,$url,$stlye,$width="69",$height="21"){
	global $admininfo;

	return "<div style=\"display:inline;width:".$width."px;height:".$height."px;background-image:url('".$url."');$stlye\"><input type='file' name='".$name."' style='width:".$width."px;height:".$height."px;cursor:pointer;opacity:0;filter:alpha(opacity=0);'></div>";
}





function DateBySchedule($date, $is_schedule = "1", $group_ix = ""){
	global $script_time, $week_i, $x, $admininfo, $list_type;

	$mdb = new Database;
	if($_SESSION["admininfo"]["charger_id"] == "sigi1074"){
		//$db->debug = true;
		//$mdb->debug = true;
	}
	if($is_schedule != "all"){
		$is_schedule_str = "and is_schedule = '".$is_schedule."'";
	}
	//echo $_COOKIE[view_complete_job];
	if($_COOKIE[view_complete_job] != '1' && $list_type != "search"){
		$where .= " and (wl.status not in ('WC','WD') ) ";
		$union_where .= " and (wl.status not in ('WC','WD') ) ";
	}
	/*
	if($group_ix != ""){
		$group_ix_str = "and wl.group_ix = '".$group_ix."'";
	}
	*/
	//$where = " where ";
	//echo $_COOKIE["dynatree-work_group-select"];
	if($_COOKIE["dynatree-work_group-select"]){
		$group_ix_str = " and (wg.group_ix in ('".str_replace(",","','",$_COOKIE["dynatree-work_group-select"])."') )  ";
		$union_group_ix_str = " and (wg.group_ix in ('".str_replace(",","','",$_COOKIE["dynatree-work_group-select"])."') )  ";
	}else{
		if($group_ix != ""){
			$group_ix_str = " and (wg.group_ix in(".$group_ix.") or wg.parent_group_ix in (".$group_ix.") )  ";
			$union_group_ix_str = " and (wg.group_ix in(".$group_ix.") or wg.parent_group_ix in (".$group_ix.") )  ";
		}
		if(!$_COOKIE["dynatree-user-select"]){
			$group_ix_str .= " and wl.charger_ix =  '".$_SESSION['admininfo']['charger_ix']."' ";
			$union_group_ix_str .= "and  cr.charger_ix = '".$_SESSION['admininfo']['charger_ix']."' ";
		}
	}

	if($charger_ix != "" && false){
		//echo "aaa";
		$where .= " and (wl.charger_ix in ('".str_replace(",","','",$charger_ix)."'))  ";
		$union_where .= " and (cr.charger_ix in ('".str_replace(",","','",$charger_ix)."'))  ";
	}else if($_COOKIE["dynatree-user-select"]){
		$where .= " and (wl.charger_ix in ('".str_replace(",","','",$_COOKIE["dynatree-user-select"])."'))  ";
		$union_where .= " and (cr.charger_ix in ('".str_replace(",","','",$_COOKIE["dynatree-user-select"])."'))";
	}else{
		$where .= " and (wl.charger_ix = '".$_SESSION["admininfo"][charger_ix]."' or  (wl.charger_ix !=  '".$_SESSION["admininfo"][charger_ix]."' and is_hidden = '0'))";
		$union_where .= " and (cr.charger_ix = '".$_SESSION["admininfo"][charger_ix]."')";
	}
	//echo $mdb->dbms_type;
	//exit;
	if($mdb->dbms_type == "oracle"){
		///*+ INDEX(wl IDX_WL_SDATE_STIME) */
				$sql = "select * from
				(SELECT wl.*, wg.group_name, wg.group_depth, wg.parent_group_ix, cmd.name as name, charger_ix as co_charger_ix
				FROM work_list wl  , work_group wg, common_member_detail cmd
				where wl.company_id = '".$_SESSION['admininfo']['company_id']."'
				and wl.group_ix = wg.group_ix and wl.charger_ix = cmd.code
				 ".$is_schedule_str." ".$group_ix_str."
				and ".$date." between sdate and dday  ".$where."
				union
				SELECT  wl.* , wg.group_name, wg.group_depth, wg.parent_group_ix, cmd.name as name, cr.charger_ix as co_charger_ix
				FROM work_list wl
				left join work_charger_relation cr on wl.wl_ix = cr.wl_ix
				left join work_group wg on wl.group_ix = wg.group_ix , common_member_detail cmd
				where cr.charger_ix = cmd.code
				and ".$date." between sdate and dday  ".$is_schedule_str." ".$union_group_ix_str." ".$union_where."
				) a
				GROUP BY a.wl_ix";

				//echo $sql;
				//exit;

	}else{
		$sql = "select * from
				(SELECT wl.*, wg.group_name, wg.group_depth, wg.parent_group_ix, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, charger_ix as co_charger_ix
				FROM work_list wl force index(IDX_WL_SDATE_STIME) , work_group wg, common_member_detail cmd
				where wl.company_id = '".$_SESSION['admininfo']['company_id']."'
				and wl.group_ix = wg.group_ix and wl.charger_ix = cmd.code
				 ".$is_schedule_str." ".$group_ix_str."
				and ".$date." between sdate and dday  ".$where."
				union
				SELECT wl.* , wg.group_name, wg.group_depth, wg.parent_group_ix, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, cr.charger_ix as co_charger_ix
				FROM work_list wl force index(IDX_WL_SDATE_STIME)
				left join work_charger_relation cr on wl.wl_ix = cr.wl_ix
				left join work_group wg on wl.group_ix = wg.group_ix , common_member_detail cmd
				where cr.charger_ix = cmd.code
				and ".$date." between sdate and dday  ".$is_schedule_str." ".$union_group_ix_str." ".$union_where."
				) a
				GROUP BY a.wl_ix";
	}
			 //order by sdate desc, stime asc
	//echo nl2br($sql);
	$script_time["weekly_query_start_".$week_i."_".$x] = time();
	$mdb->query($sql);
	$script_time["weekly_query_end_".$week_i."_".$x] = time();
	if($mdb->total){
		$_schedules = $mdb->fetchall();
	}
	//print_r($_schedules);
	return $_schedules;
}




function DateBySchedulePriod($sdate, $edate, $is_schedule = "1", $group_ix = ""){
	global $script_time, $week_i, $x, $admininfo, $list_type;

	$mdb = new Database;
	if($_SESSION["admininfo"]["charger_id"] == "sigi1074"){
		//$db->debug = true;
		//$mdb->debug = true;
	}
	if($is_schedule != "all"){
		$is_schedule_str = "and is_schedule = '".$is_schedule."'";
	}
	//echo $_COOKIE[view_complete_job];
	if($_COOKIE[view_complete_job] != '1' && $list_type != "search"){
		$where .= " and (wl.status not in ('WC','WD') ) ";
		$union_where .= " and (wl.status not in ('WC','WD') ) ";
	}
	/*
	if($group_ix != ""){
		$group_ix_str = "and wl.group_ix = '".$group_ix."'";
	}
	*/
	//$where = " where ";
	//echo $_COOKIE["dynatree-work_group-select"];
	if($_COOKIE["dynatree-work_group-select"]){
		$group_ix_str = " and (wg.group_ix in ('".str_replace(",","','",$_COOKIE["dynatree-work_group-select"])."') )  ";
		$union_group_ix_str = " and (wg.group_ix in ('".str_replace(",","','",$_COOKIE["dynatree-work_group-select"])."') )  ";
	}else{
		if($group_ix != ""){
			$group_ix_str = " and (wg.group_ix in(".$group_ix.") or wg.parent_group_ix in (".$group_ix.") )  ";
			$union_group_ix_str = " and (wg.group_ix in(".$group_ix.") or wg.parent_group_ix in (".$group_ix.") )  ";
		}
		if(!$_COOKIE["dynatree-user-select"]){
			$group_ix_str .= " and wl.charger_ix =  '".$_SESSION['admininfo']['charger_ix']."' ";
			$union_group_ix_str .= "and  cr.charger_ix = '".$_SESSION['admininfo']['charger_ix']."' ";
		}
	}

	if($charger_ix != "" && false){
		//echo "aaa";
		$where .= " and (wl.charger_ix in ('".str_replace(",","','",$charger_ix)."'))  ";
		$union_where .= " and (cr.charger_ix in ('".str_replace(",","','",$charger_ix)."'))  ";
	}else if($_COOKIE["dynatree-user-select"]){
		$where .= " and (wl.charger_ix in ('".str_replace(",","','",$_COOKIE["dynatree-user-select"])."'))  ";
		$union_where .= " and (cr.charger_ix in ('".str_replace(",","','",$_COOKIE["dynatree-user-select"])."'))";
	}else{
		$where .= " and (wl.charger_ix = '".$_SESSION["admininfo"][charger_ix]."' or  (wl.charger_ix !=  '".$_SESSION["admininfo"][charger_ix]."' and is_hidden = '0'))";
		$union_where .= " and (cr.charger_ix = '".$_SESSION["admininfo"][charger_ix]."')";
	}

	if($mdb->dbms_type == "oralce"){
		$sql = "select * from
				(SELECT /*+ INDEX(wl IDX_WL_SDATE_STIME) */ wl.*, wg.group_name, wg.group_depth, wg.parent_group_ix, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, charger_ix as co_charger_ix
				FROM work_list wl  , work_group wg, common_member_detail cmd
				where wl.company_id = '".$_SESSION['admininfo']['company_id']."'
				and wl.group_ix = wg.group_ix and wl.charger_ix = cmd.code
				 ".$is_schedule_str." ".$group_ix_str."
				and (sdate between ".$sdate."  and ".$edate." or dday between ".$sdate."  and ".$edate.")  ".$where."
				union
				SELECT /*+ INDEX(wl IDX_WL_SDATE_STIME) */  wl.* , wg.group_name, wg.group_depth, wg.parent_group_ix, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, cr.charger_ix as co_charger_ix
				FROM work_list wl
				left join work_charger_relation cr on wl.wl_ix = cr.wl_ix
				left join work_group wg on wl.group_ix = wg.group_ix , common_member_detail cmd
				where cr.charger_ix = cmd.code
				and (sdate between ".$sdate."  and ".$edate." or dday between ".$sdate."  and ".$edate.")  ".$is_schedule_str." ".$union_group_ix_str." ".$union_where."
				) a
				GROUP BY a.wl_ix";
	}else{
				$sql = "select * from
				(SELECT wl.*, wg.group_name, wg.group_depth, wg.parent_group_ix, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, charger_ix as co_charger_ix
				FROM work_list wl force index(IDX_WL_SDATE_STIME) , work_group wg, common_member_detail cmd
				where wl.company_id = '".$_SESSION['admininfo']['company_id']."'
				and wl.group_ix = wg.group_ix and wl.charger_ix = cmd.code
				 ".$is_schedule_str." ".$group_ix_str."
				and (sdate between ".$sdate."  and ".$edate." or dday between ".$sdate."  and ".$edate.")  ".$where."
				union
				SELECT wl.* , wg.group_name, wg.group_depth, wg.parent_group_ix, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, cr.charger_ix as co_charger_ix
				FROM work_list wl force index(IDX_WL_SDATE_STIME)
				left join work_charger_relation cr on wl.wl_ix = cr.wl_ix
				left join work_group wg on wl.group_ix = wg.group_ix , common_member_detail cmd
				where cr.charger_ix = cmd.code
				and (sdate between ".$sdate."  and ".$edate." or dday between ".$sdate."  and ".$edate.")  ".$is_schedule_str." ".$union_group_ix_str." ".$union_where."
				) a
				GROUP BY a.wl_ix";

	}
			 //order by sdate desc, stime asc
	//echo nl2br($sql);
	$script_time["weekly_query_start_".$week_i."_".$x] = time();
	$mdb->query($sql);
	$script_time["weekly_query_end_".$week_i."_".$x] = time();
	if($mdb->total){
		$_schedules = $mdb->fetchall();
	}
	if($week_i == 1){
	//print_r($_schedules);
	}
	return $_schedules;
}


function __DateBySchedulePriod($sdate, $edate, $is_schedule = "1", $group_ix = ""){
	global $script_time, $week_i, $x;

	$mdb = new Database;

	if($is_schedule != "all"){
		$is_schedule_str = "and is_schedule = '".$is_schedule."'";
	}



	/*
	if($group_ix != ""){
		$group_ix_str = "and wl.group_ix = '".$group_ix."'";
	}
	*/
	//$where = " where ";
	if($group_ix != ""){
		$group_ix_str = " and (wg.group_ix in(".$group_ix.") or wg.parent_group_ix in (".$group_ix.") )  ";
	}else if($_COOKIE["dynatree-work_group-select"]){
		$group_ix_str = " and (wg.group_ix in ('".str_replace(",","','",$_COOKIE["dynatree-work_group-select"])."') )  ";
	}

	$sql = "SELECT wl.*, wg.group_name, wg.group_depth, wg.parent_group_ix, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, charger_ix as co_charger_ix
			FROM work_list wl , work_group wg, common_member_detail cmd
			where wl.company_id = '".$_SESSION['admininfo']['company_id']."' and wl.wl_ix != ''
			and wl.group_ix = wg.group_ix and wl.charger_ix = cmd.code
			and wl.charger_ix =  '".$_SESSION['admininfo']['charger_ix']."'  ".$is_schedule_st." ".$group_ix_str."
			and (sdate between ".$sdate."  and ".$edate." or dday between ".$sdate."  and ".$edate.")

			";
	$sql .= "
			union
			SELECT wl.* , wg.group_name, wg.group_depth, wg.parent_group_ix, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, cr.charger_ix as co_charger_ix
			FROM work_list wl
			left join work_charger_relation cr on wl.wl_ix = cr.wl_ix
			left join work_group wg on wl.group_ix = wg.group_ix , common_member_detail cmd
			where cr.charger_ix = '".$_SESSION['admininfo']['charger_ix']."' and wl.charger_ix != cmd.code
			and (sdate between ".$sdate."  and ".$edate." or dday between ".$sdate."  and ".$edate.")
			".$is_schedule_st." ".$group_ix_str." ".$where."
			GROUP BY wl.wl_ix
			order by sdate desc, stime asc
			 ";
	//echo nl2br($sql);
	$script_time["weekly_query_start_".$week_i."_".$x] = time();
	$mdb->query($sql);
	$script_time["weekly_query_end_".$week_i."_".$x] = time();
	if($mdb->total){
		$_schedules = $mdb->fetchall();
	}
	//print_r($_schedules);
	return $_schedules;
}


function DateByWork($date){
	$mdb = new Database;

	$sql = "SELECT wl.*, wg.group_name, wg.group_depth, wg.parent_group_ix, cmd.name
			FROM work_list wl , work_group wg, common_member_detail cmd
			where wl.company_id = '".$_SESSION['admininfo']['company_id']."' and wl.wl_ix != ''
			and wl.group_ix = wg.group_ix and wl.charger_ix = cmd.code
			and wl.charger_ix =  '".$_SESSION['admininfo']['charger_ix']."' and is_schedule = '0'
			and ".$date." between sdate and dday
			order by sdate desc, stime asc
			limit 5";
	//echo nl2br($sql);
	$mdb->query($sql);
	return $mdb->fetchall();
}

function BankInfo($bank_ix, $validation=true, $return_type = "select"){
	$db = new Database;

	if($return_type == "select"){

		$db->query("SELECT * FROM ".TBL_SHOP_BANKINFO." where disp = '1' "); //

		$mstring = "<select name='bank_ix' id='bank_ix' style='min-width:100px;' validation='".$validation."' title='무통장 계좌'  >";
		$mstring .= "<option value=''>무통장 계좌 </option>";
			if($db->total){
				for($i=0;$i < $db->total;$i++){
					$db->fetch($i);
					$mstring .= "<option value='".$db->dt[bank_ix]."' ".($db->dt[bank_ix] == $bank_ix ? "selected":"").">".$db->dt[bank_name]." ".$db->dt[bank_number]." ( ".$db->dt[bank_owner]." )</option>";
				}
			}else{
			}
			$mstring .= "</select>";
	}else{
		$sql = "SELECT * FROM ".TBL_SHOP_BANKINFO." where disp = '1' and bank_ix = '".$bank_ix."' ";
		//echo $sql;
		$db->query($sql);
		$db->fetch();

		return $db->dt[bank_name]." <input type=hidden name='bank_ix' id='bank_ix' value='".$db->dt[bank_ix]."'>";
	}

	return $mstring;
}

function service_date_diff($start, $end="NOW")
{
        $sdate = strtotime($start);
        $edate = strtotime($end);

        $time = $edate - $sdate;
		if($time == 0){
			return ;
		}else if($time < 0 && $start && $end){
			return "기간만료";
		}
        if($time>=0 && $time<=59) {
                // Seconds
				$timeshift = 0;
                $timeshift = $time.' 초 ';

        } elseif($time>=60 && $time<=3599) {
                // Minutes + Seconds
                $pmin = ($edate - $sdate) / 60;
                $premin = explode('.', $pmin);

                $presec = $pmin-$premin[0];
                $sec = $presec*60;

                $timeshift = $premin[0].' 분 '.round($sec,0).' 초 ';

        } elseif($time>=3600 && $time<=86399) {
                // Hours + Minutes
                $phour = ($edate - $sdate) / 3600;
                $prehour = explode('.',$phour);

                $premin = $phour-$prehour[0];
                $min = explode('.',$premin*60);

                $presec = '0.'.$min[1];
                $sec = $presec*60;

                $timeshift = $prehour[0].' 시간 '.$min[0].' min '.round($sec,0).' 초 ';

        } elseif($time>=86400) {
                // Days + Hours + Minutes
                $pday = ($edate - $sdate) / 86400;
                $preday = explode('.',$pday);

                $phour = $pday-$preday[0];
                $prehour = explode('.',$phour*24);

                $premin = ($phour*24)-$prehour[0];
                $min = explode('.',$premin*60);

                $presec = '0.'.$min[1];
                $sec = $presec*60;

				if($time % 86400 == 0){
					$timeshift = $preday[0]. ' 일';
				}else{
					$timeshift = $preday[0].' 일 '.$prehour[0].' 시간 '.$min[0].' 분 '.round($sec,0).' 초 ';
				}

        }
        return $timeshift;
}

function date_diff2($start, $end="NOW")
{
        $sdate = strtotime($start);
        $edate = strtotime($end);

        $time = $edate - $sdate;
		//echo $time;
        if($time>=0 && $time<=59) {
                // Seconds
				$timeshift = 0;
                //$timeshift = $time.' seconds ';

        } elseif($time>=60 && $time<=3599) {
                // Minutes + Seconds
                $pmin = ($edate - $sdate) / 60;
                $premin = explode('.', $pmin);

                $presec = $pmin-$premin[0];
                $sec = $presec*60;

                $timeshift = $premin[0].' min '.round($sec,0).' sec ';

        } elseif($time>=3600 && $time<=86399) {
                // Hours + Minutes
                $phour = ($edate - $sdate) / 3600;
                $prehour = explode('.',$phour);

                $premin = $phour-$prehour[0];
                $min = explode('.',$premin*60);

                $presec = '0.'.$min[1];
                $sec = $presec*60;

                $timeshift = $prehour[0].' hrs '.$min[0].' min '.round($sec,0).' sec ';

        } elseif($time>=86400) {
                // Days + Hours + Minutes
                $pday = ($edate - $sdate) / 86400;
                $preday = explode('.',$pday);

                $phour = $pday-$preday[0];
                $prehour = explode('.',$phour*24);

                $premin = ($phour*24)-$prehour[0];
                $min = explode('.',$premin*60);

                $presec = '0.'.$min[1];
                $sec = $presec*60;

				if($time % 86400 == 0){
					$timeshift = $preday[0];
				}else{
					$timeshift = $preday[0].' days '.$prehour[0].' hrs '.$min[0].' min '.round($sec,0).' sec ';
				}

        }
        return $timeshift;
}


function __date_diff($start, $end="NOW")
{
        $sdate = strtotime($start);
        $edate = strtotime($end);

        $time = $edate - $sdate;
        if($time>=0 && $time<=59) {
                // Seconds
				//$timeshift = 0;
                $timeshift = $time.' seconds ';

        } elseif($time>=60 && $time<=3599) {
                // Minutes + Seconds
                $pmin = ($edate - $sdate) / 60;
                $premin = explode('.', $pmin);

                $presec = $pmin-$premin[0];
                $sec = $presec*60;

                $timeshift = $premin[0].' min '.round($sec,0).' sec ';

        } elseif($time>=3600 && $time<=86399) {
                // Hours + Minutes
                $phour = ($edate - $sdate) / 3600;
                $prehour = explode('.',$phour);

                $premin = $phour-$prehour[0];
                $min = explode('.',$premin*60);

                $presec = '0.'.$min[1];
                $sec = $presec*60;

                $timeshift = $prehour[0].' hrs '.$min[0].' min '.round($sec,0).' sec ';

        } elseif($time>=86400) {
                // Days + Hours + Minutes
                $pday = ($edate - $sdate) / 86400;
                $preday = explode('.',$pday);

                $phour = $pday-$preday[0];
                $prehour = explode('.',$phour*24);

                $premin = ($phour*24)-$prehour[0];
                $min = explode('.',$premin*60);

                $presec = '0.'.$min[1];
                $sec = $presec*60;

				if($time % 86400 == 0){
					$timeshift = $preday[0];
				}else{
					$timeshift = $preday[0].' days '.$prehour[0].' hrs '.$min[0].' min '.round($sec,0).' sec ';
				}

        }
        return $timeshift;
}


function get_vip_member($level_ix){
	$mdb = new Database;

	$sql = "select
				u.code,
				AES_DECRYPT(UNHEX(cmd.name),'".$mdb->ase_encrypt_key."') as name,
				u.id
			from
				common_user as u
				inner join common_member_detail as cmd on (u.code = cmd.code)
			where
				cmd.level_ix = '".$level_ix."'";
		$mdb->query($sql);
		$vip_array = $mdb->fetchall();

	return $vip_array;

}

function get_member_name($code){		//회원코드로 회원명 불러오기 2014-04-11 이학봉
	$mdb = new Database;

	$sql = "select u.code,
				 AES_DECRYPT(UNHEX(cmd.name),'".$mdb->ase_encrypt_key."') as name
				from
					common_user as u
					inner join common_member_detail as cmd on (u.code = cmd.code)
				where
					u.code = '".$code."'";
		$mdb->query($sql);
		$mdb->fetch();

	return $mdb->dt[name];

}

function get_member_id($code){
	$mdb = new Database;

	$sql = "select u.id
				from
					common_user as u
				where
					u.code = '".$code."'";
		$mdb->query($sql);
		$mdb->fetch();

	return $mdb->dt[id];
}


function getOrderFromName($order_from){
	$mdb = new Database;

	switch($order_from){
		case 'self':
			$return = '자체쇼핑몰';
		break;
		case 'offline':
			$return = '오프라인 영업';
		break;
		case 'pos':
			$return = 'POS';
		break;
		default :

			$mdb->query("select * from sellertool_site_info where site_code='".$order_from."' ");
			$sellertool=$mdb->fetch();
			$return = $mdb->dt[site_name];

		break;

		/*
		case '11st':
			$return = '11번가';
		break;
		case 'goodss':
			$return = '굿스';
		break;
		case 'auction':
			$return = '옥션';
		break;
		default :
			$return = $order_from;
		break;
		*/
	}

	return $return;
}



function getRefererCategoryPath2($cid, $depth='-1'){

$mdb = new Database;
	if($depth == '0'){
		$sql = "select * from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." where  depth = 0 and cid LIKE '".substr($cid,0,3)."%' order by depth asc";
	}else if($depth == '1'){
		$sql = "select * from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." where  depth <= 1 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%'))  order by depth asc";
	}else if($depth == '2'){
		$sql = "select * from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." where  depth <= 2 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%'))  order by depth asc";
	}else if($depth == '3'){
		$sql = "select * from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." where  depth <= 3 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0,12)."%'))  order by depth asc";
	}else if($depth == '4'){
		$sql = "select * from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." where  depth <= 4 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0,12)."%') or (depth = 4 and cid LIKE '".substr($cid,0,15)."%'))  order by depth asc";

	}else{
		return "";
	}

	$mdb->query($sql);

	for($i=0;$i < $mdb->total;$i++){
		$mdb->fetch($i);

		if($i == 0){
			$mstring .= $mdb->dt[cname];
		}else{
			$mstring .= " > ".$mdb->dt[cname];
		}
	}
	return $mstring;
}

function getPaymentAgentType($payment_agent_type,$viewtype=""){
	global $admininfo;
	switch($payment_agent_type){
		case 'W':
			if($viewtype=="img")		$return = "<label class='helpcloud' help_width='90' help_height='15' help_html='PC(웹)결제'><img src='../images/".$admininfo[language]."/s_payment_agent_type_w.gif' align='absmiddle'></label>";
			else									$return = '웹결제';
		break;
		case 'M':
			if($viewtype=="img")		$return = "<label class='helpcloud' help_width='80' help_height='15' help_html='모바일결제'><img src='../images/".$admininfo[language]."/s_payment_agent_type_m.gif' align='absmiddle'></label>";
			else									$return = '모바일';
		break;
		case 'O':
			if($viewtype=="img")		$return = "<label class='helpcloud' help_width='90' help_height='15' help_html='오프라인주문'><img src='../images/".$admininfo[language]."/s_payment_agent_type_o.gif' align='absmiddle'></label>";
			else									$return = '오프라인주문';
		break;
		default :
			$return = $payment_agent_type;
		break;
	}

	return $return;
}

function getMemberLevel($level_ix,$validation="false")
{
	$mdb = new Database;

	$sql = "SELECT * FROM shop_level where 1 order by level_ix asc";

	$mdb->query($sql);

	if ($mdb->total){
		$mstring = "<Select name='level_ix' id='level_ix'  style='min-width:110px;font-size:12px;' validation='$validation' title='회원 레벨'>\n";
		$mstring = $mstring."<option value=''>회원레벨 선택</option>\n";
		for($i=0; $i < $mdb->total; $i++){
			$mdb->fetch($i);
			//if(substr_count($cid,substr($mdb->dt[cid],0,$mdb->dt[depth]+1))){
			if($mdb->dt[level_ix] == $level_ix){
				$mstring = $mstring."<option value='".$mdb->dt[level_ix]."' selected>".$mdb->dt[lv_name]."</option>\n";
				//$mstring = $mstring."<option value='".$mdb->dt[cid]."' selected>".substr($cid,0,($depth+1)*3)." == ".substr($mdb->dt[cid],0,($depth+1)*3)."</option>\n";
			}else{
				$mstring = $mstring."<option value='".$mdb->dt[level_ix]."' >".$mdb->dt[lv_name]."</option>\n";
				//$mstring = $mstring."<option value='".$mdb->dt[cid]."' >".substr($cid,0,($depth+1)*3)." == ".substr($mdb->dt[cid],0,($depth+1)*3)."</option>\n";
			}
		}
	}else{
		$mstring = "<Select name='level_ix' id='level_ix'  style='width:110px;font-size:12px;' validation='$validation' title='회원 레벨'>\n";
		$mstring = $mstring."<option value=''> 회원레벨 선택</option>\n";
	}

	$mstring = $mstring."</Select>\n";

	return $mstring;
}

function DeliveryMethod($name,$value,$property,$view_type="select"){		//수정필요한 부분 2014-05-21 이학봉
	$mdb = new Database;

	if($view_type=="select"){
		$mdb->query("select * from ".TBL_SHOP_SHOPINFO." limit 0,1 ");
		$mdb->fetch();
		$mdb->dt[mall_send_tekbae_use];
		$mdb->dt[mall_send_quick_use];
		$mdb->dt[mall_send_truck_use];
		$mdb->dt[mall_send_self_use];
		$mdb->dt[mall_send_direct_use];

		$mstring="<select name='".$name."' ".$property." > ";
		$mstring.="<option value='' >배송방법</option>";

		/*
		if($mdb->dt[mall_send_tekbae_use]==1)		$mstring.="<option value='TE' ".($value=='TE' ? "selected" : "").">택배</option>";
		if($mdb->dt[mall_send_quick_use]==1)		$mstring.="<option value='QU' ".($value=='QU' ? "selected" : "").">퀵서비스</option>";
		if($mdb->dt[mall_send_truck_use]==1)		$mstring.="<option value='TR' ".($value=='TR' ? "selected" : "").">용달</option>";
		if($mdb->dt[mall_send_self_use]==1)			$mstring.="<option value='SE' ".($value=='SE' ? "selected" : "").">직접방문</option>";
		if($mdb->dt[mall_send_direct_use]==1)		$mstring.="<option value='DI' ".($value=='DI' ? "selected" : "").">직배송</option>";
		*/

		if($mdb->dt[mall_send_tekbae_use]==1)		$mstring.="<option value='1' ".($value=='1' ? "selected" : "").">택배</option>";
		if($mdb->dt[mall_send_truck_use]==1)		$mstring.="<option value='2' ".($value=='2' ? "selected" : "").">화물</option>";
		if($mdb->dt[mall_send_direct_use]==1)		$mstring.="<option value='3' ".($value=='3' ? "selected" : "").">직배송</option>";
		if($mdb->dt[mall_send_self_use]==1)			$mstring.="<option value='4' ".($value=='4' ? "selected" : "").">방문수령</option>";
		//if($mdb->dt[mall_send_quick_use]==1)		$mstring.="<option value='QU' ".($value=='QU' ? "selected" : "").">퀵서비스</option>";


		$mstring.="</select> ";

	}elseif($view_type="text"){
		if($value=='1'){
			$mstring="택배";
		}elseif($value=='2'){
			$mstring="용달";
		}elseif($value=='3'){
			$mstring="직배송";
		}elseif($value=='4'){
			$mstring="방문수령";
		}else{
			$mstring="-";
		}
	}

	return $mstring;
}


function DisplayStyleSetup($select_name='',$select_id=''){

	$font_style_family = array('굴림','돋움','바탕','궁서','Arial','Sans Serif','Tahoma','Verdana','Courier New','Georgia','Impact',);
	$font_style_size = array('1','2','3','4','5','6','7');
	$font_style_weight = array('bold'=>'굵게','thin'=>'얇게','nomal'=>'기본');
	$font_style_decoration = array('underline'=>'아래에 줄긋기','overline'=>'위에 줄긋기','line-through'=>'가운데 줄긋기');

	$content = "<select name='family_".$select_name."' id='family_".$select_id."' style='margin:0px 2px;'>";
	$content .= "<option value='0'>=글씨체=</option>";
	for($i=0;$i<count($font_style_family);$i++){
	$content .= "<option value='".$font_style_family[$i]."'><span style='font-family:".$font_style_family[$i].";'>".$font_style_family[$i]."</span></option>";
	}
	$content .= "</select>";

	$content .= "<select name='size_".$select_name."' id='size_".$select_id."' style='margin:0px 2px;'>";
	$content .= "<option value='0'>=크기=</option>";
	for($i=0;$i<count($font_style_size);$i++){
	$content .= "<option value='".$font_style_size[$i]."'><span style='font-size:".$font_style_size[$i]."px;'>".$font_style_size[$i]."</span></option>";
	}
	$content .= "</select>";

	$content .= "<select name='weight_".$select_name."' id='weight_".$select_id."' style='margin:0px 2px;'>";
	$content .= "<option value='0'>=굵기=</option>";
	foreach($font_style_weight as $key =>$value){
	$content .= "<option value='".$key."'><span style='font-weight:".$key.";'>".$value."</span></option>";
	}
	$content .= "</select>";

	$content .= "<select name='decoration_".$select_name."' id='decoration_".$select_id."' style='margin:0px 2px;'>";
	$content .= "<option value='0'>=줄긋기=</option>";
	foreach($font_style_weight as $key =>$value){
	$content .= "<option value='".$key."'><span style='font-decoration:".$key.";'>".$value."</span></option>";
	}
	$content .= "</select>";

	return $content;

}

function search_date_arry($sdate_name,$edate_name,$basic_sdate='',$basic_edate='',$use_time='',$search_type='D', $property="", $num){

	global $admininfo;
	//echo "search_type:".$search_type;


	$vdate = date("Y-m-d", time());
	$today = date("Y-m-d", time());

	$vyesterday = date("Y-m-d", time()+86400);
	$voneweekago = date("Y-m-d", time()+86400*7);
	$v15ago = date("Y-m-d", time()+86400*15);
	$vonemonthago = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2)+1,substr($vdate,8,2)+1,substr($vdate,0,4)));
	$v2monthago = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2)+2,substr($vdate,8,2)+1,substr($vdate,0,4)));
	$v3monthago = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2)+3,substr($vdate,8,2)+1,substr($vdate,0,4)));

	$basic_sdate_array = explode(" ",$basic_sdate);	//넘어온값에 시간이 붙어잇을경우 짤라서 시간을 따로 변수에 담아둠
	$basic_sdate_ymd = $basic_sdate_array[0];

	$basic_edate_array = explode(" ",$basic_edate);	//넘어온값에 시간이 붙어잇을경우 짤라서 시간을 따로 변수에 담아둠
	$basic_edate_ymd = $basic_edate_array[0];
	//print_r($basic_edate_array[0]);

	if($use_time == 'Y'){	//시 까지 사용할경우 처리
		if($basic_sdate_array[1]){
			$start_time_h = strftime('%H',strtotime($basic_sdate));
			$start_time_i = strftime('%M',strtotime($basic_sdate));
			$start_time_s = strftime('%S',strtotime($basic_sdate));
		}

		if($basic_edate_array[1]){

			$end_time_h = strftime('%H',strtotime($basic_edate));
			$end_time_i = strftime('%M',strtotime($basic_edate));
			$end_time_s = strftime('%S',strtotime($basic_edate));

		}

		$start_time_select = "<select name='".$sdate_name."_h[".$num."]' id='".$sdate_name."_h_'.$num >";
		for($i=0;$i<24;$i++){
			$start_time_select .="<option value='".$i."' ".($start_time_h == $i?'selected':'').">".$i."</option>";
		}
		$start_time_select .= "</select> 시";

		$start_time_select .= "<select name='".$sdate_name."_i[".$num."]' id='".$sdate_name."_i_'.$num>";
		for($i=0;$i<60;$i++){
			$start_time_select .="<option value='".$i."' ".($start_time_i == $i?'selected':'').">".$i."</option>";
		}
		$start_time_select .= "</select> 분";

		$start_time_select .= "<select name='".$sdate_name."_s[".$num."]' id='".$sdate_name."_s_'.$num>";
		for($i=0;$i<60;$i++){
			$start_time_select .="<option value='".$i."' ".($start_time_s == $i?'selected':'').">".$i."</option>";
		}
		$start_time_select .= "</select> 초";


		$end_time_select = "<select name='".$edate_name."_h[".$num."]' id='".$edate_name."_h_'.$num>";
		for($i=0;$i<24;$i++){
			$end_time_select .="<option value='".$i."' ".($end_time_h == $i?'selected':'').">".$i."</option>";
		}
		$end_time_select .= "</select> 시";

		$end_time_select .= "<select name='".$edate_name."_i[".$num."]' id='".$edate_name."_i_'.$num>";
		for($i=0;$i<60;$i++){
			$end_time_select .="<option value='".$i."' ".($end_time_i == $i?'selected':'').">".$i."</option>";
		}
		$end_time_select .= "</select> 분";

		$end_time_select .= "<select name='".$edate_name."_s[".$num."]' id='".$edate_name."_s_'.$num>";
		for($i=0;$i<60;$i++){
			$end_time_select .="<option value='".$i."' ".($end_time_s == $i?'selected':'').">".$i."</option>";
		}
		$end_time_select .= "</select> 초";
	}

	$Contents .= "
	<table cellpadding=3  cellspacing=1 border=0 bgcolor=#ffffff style='float:left;'>
		<tr>
			<td>
				<img src='../images/".$admininfo["language"]."/calendar_icon.gif'>
			</td>
			<TD nowrap>
				<input type='text' name='".$sdate_name."[".$num."]' class='textbox point_color' value='".$basic_sdate_ymd."' style='height:20px;width:80px;text-align:center;' id='".$sdate_name."_".$num."' ".$property."> 일
				".$start_time_select."
			</TD>
			<TD style='padding:0 5px;' align=center> ~ </TD>
			<td>
				<img src='../images/".$admininfo["language"]."/calendar_icon.gif'>
			</td>
			<TD nowrap>
				<input type='text' name='".$edate_name."[".$num."]' class='textbox point_color' value='".$basic_edate_ymd."' style='height:20px;width:80px;text-align:center;' id='".$edate_name."_".$num."' ".$property."> 일
				".$end_time_select."
			</TD>
		</tr>
	</table>";

	$Contents .= "
	<div style='float:left;padding:7px 10px;'>
		<a href=\"javascript:".$sdate_name."_".$num."('$today','$today',1);\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
		<a href=\"javascript:".$sdate_name."_".$num."('$today','$voneweekago',1);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
		<a href=\"javascript:".$sdate_name."_".$num."('$today','$v15ago',1);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
		<a href=\"javascript:".$sdate_name."_".$num."('$today','$vonemonthago',1);\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
		<a href=\"javascript:".$sdate_name."_".$num."('$today','$v2monthago',1);\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
		<a href=\"javascript:".$sdate_name."_".$num."('$today','$v3monthago',1);\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>
	</div>";

	$Contents .= "
	<script type='text/javascript'>
	<!--
	function ".$sdate_name."_".$num."(FromDate,ToDate,dType) {
//alert($('#".$sdate_name."_".$num."').attr('disabled'));
		if($('#".$sdate_name."_".$num."').attr('disabled') == 'disabled'){
			alert('비활성화 상태에서는 날짜 선택이 불가합니다.');
		}else{
			var frm = document.search_frm;
			$('#".$sdate_name."_".$num."').val(FromDate);
			$('#".$edate_name."_".$num."').val(ToDate);
		}
	}

	$(document).ready(function (){
		$('#".$sdate_name."_".$num."').datepicker({
			//changeMonth: true,
			//changeYear: true,
			monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
			dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
			showMonthAfterYear:true,
			dateFormat: 'yy-mm-dd',
			buttonImageOnly: true,
			buttonText: '달력',
		
			onSelect: function(dateText, inst){
				if($('#".$edate_name."_".$num."').val() != '' && $('#".$edate_name."_".$num."').val() <= dateText){
					$('#".$edate_name."_".$num."').val(dateText);
				}else{
					$('#".$edate_name."_".$num."').datepicker('setDate','+0d');
				}
			}
		});

		$('#".$edate_name."_".$num."').datepicker({
			//changeMonth: true,
			//changeYear: true,
			monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
			dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
			showMonthAfterYear:true,
			dateFormat: 'yy-mm-dd',
			buttonImageOnly: true,
			buttonText: '달력',
			onSelect: function(dateText, inst){";
	$Contents .= "
			}
		
		});
	});
	//-->
	</script>
";

	return $Contents;
}

//날짜검색 공용함수 이학봉 추가
/*
$sdate_name : 시작날짜 input name	이 값을 기준으로 시,분,초 name 과 id 생성
$edate_name : 마감날짜 input name
$basic_sdate : 시작날짜 기본값
$basic_edate : 마감날짜 기본값
$use_time	: 시간사용시 Y 값을 넘김 
$search_type : D: 종료일전 부터 1주일 값 , A:시작일로부터 1주일
*/
function search_date($sdate_name,$edate_name,$basic_sdate='',$basic_edate='',$use_time='',$search_type='D', $property=""){

	global $admininfo;
	//echo "search_type:".$search_type;
	

	$vdate = date("Y-m-d", time());
	$today = date("Y-m-d", time());

	if($search_type == 'D'){
		
		$vyesterday = date("Y-m-d", time()-86400);
		$voneweekago = date("Y-m-d", time()-86400*7);
		$v15ago = date("Y-m-d", time()-86400*15);
		$vonemonthago = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2)-1,substr($vdate,8,2)+1,substr($vdate,0,4)));
		$v2monthago = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2)-2,substr($vdate,8,2)+1,substr($vdate,0,4)));
		$v3monthago = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2)-3,substr($vdate,8,2)+1,substr($vdate,0,4)));
	}else if($search_type == 'tax'){
		
		$vyesterday = date("Y-m-d", time()-86400);
		$voneweekago = date("Y-m-d", time()-86400*7);
		$v15ago = date("Y-m-d", time()-86400*15);
		$vonemonthago = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2)-1,substr($vdate,8,2)+1,substr($vdate,0,4)));
		$v2monthago = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2)-2,substr($vdate,8,2)+1,substr($vdate,0,4)));
		$v3monthago = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2)-3,substr($vdate,8,2)+1,substr($vdate,0,4)));
				
		$first_quarter_1 = date("Y-01-01", time());
		$first_quarter_2 = date("Y-03-31", time());
		$second_quarter_1 = date("Y-04-01", time());
		$second_quarter_2 = date("Y-06-30", time());
		$third_quarter_1 = date("Y-07-01", time());
		$third_quarter_2 = date("Y-09-30", time());
		$fourth_quarter_1 = date("Y-10-01", time());
		$fourth_quarter_2 = date("Y-12-31", time());
    }else if($search_type == 'L'){
        $vyesterday = date("Y-m-d", time()-86400);
        $voneweekago = date("Y-m-d", time()-86400*7);
        $v15ago = date("Y-m-d", time()-86400*15);
	}else{
		
			$vyesterday = date("Y-m-d", time()+86400);
		$voneweekago = date("Y-m-d", time()+86400*7);
		$v15ago = date("Y-m-d", time()+86400*15);
		$vonemonthago = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2)+1,substr($vdate,8,2)+1,substr($vdate,0,4)));
		$v2monthago = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2)+2,substr($vdate,8,2)+1,substr($vdate,0,4)));
		$v3monthago = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2)+3,substr($vdate,8,2)+1,substr($vdate,0,4)));
	}

	$basic_sdate_array = explode(" ",$basic_sdate);	//넘어온값에 시간이 붙어잇을경우 짤라서 시간을 따로 변수에 담아둠
	$basic_sdate_ymd = $basic_sdate_array[0];

	$basic_edate_array = explode(" ",$basic_edate);	//넘어온값에 시간이 붙어잇을경우 짤라서 시간을 따로 변수에 담아둠
	$basic_edate_ymd = $basic_edate_array[0];
	//print_r($basic_edate_array[0]);

	if($use_time == 'Y'){	//시 까지 사용할경우 처리 
		if($basic_sdate_array[1]){
			$start_time_h = strftime('%H',strtotime($basic_sdate));
			$start_time_i = strftime('%M',strtotime($basic_sdate));
			$start_time_s = strftime('%S',strtotime($basic_sdate));
		}

		if($basic_edate_array[1]){ 
			
			$end_time_h = strftime('%H',strtotime($basic_edate));
			$end_time_i = strftime('%M',strtotime($basic_edate));
			$end_time_s = strftime('%S',strtotime($basic_edate));
			
		}

		$start_time_select = "<select name='".$sdate_name."_h' id='".$sdate_name."_h' >";
		for($i=0;$i<24;$i++){
			$start_time_select .="<option value='".$i."' ".($start_time_h == $i?'selected':'').">".$i."</option>";
		}
		$start_time_select .= "</select> 시";

		$start_time_select .= "<select name='".$sdate_name."_i' id='".$sdate_name."_i'>";
		for($i=0;$i<60;$i++){
			$start_time_select .="<option value='".$i."' ".($start_time_i == $i?'selected':'').">".$i."</option>";
		}
		$start_time_select .= "</select> 분";

		$start_time_select .= "<select name='".$sdate_name."_s' id='".$sdate_name."_s'>";
		for($i=0;$i<60;$i++){
			$start_time_select .="<option value='".$i."' ".($start_time_s == $i?'selected':'').">".$i."</option>";
		}
		$start_time_select .= "</select> 초";


		$end_time_select = "<select name='".$edate_name."_h' id='".$edate_name."_h'>";
		for($i=0;$i<24;$i++){
			$end_time_select .="<option value='".$i."' ".($end_time_h == $i?'selected':'').">".$i."</option>";
		}
		$end_time_select .= "</select> 시";

		$end_time_select .= "<select name='".$edate_name."_i' id='".$edate_name."_i'>";
		for($i=0;$i<60;$i++){
			$end_time_select .="<option value='".$i."' ".($end_time_i == $i?'selected':'').">".$i."</option>";
		}
		$end_time_select .= "</select> 분";

		$end_time_select .= "<select name='".$edate_name."_s' id='".$edate_name."_s'>";
		for($i=0;$i<60;$i++){
			$end_time_select .="<option value='".$i."' ".($end_time_s == $i?'selected':'').">".$i."</option>";
		}
		$end_time_select .= "</select> 초";
	}

$Contents .= "
	<table cellpadding=3  cellspacing=1 border=0 bgcolor=#ffffff style='float:left;'>
		<tr>
			<td>
				<img src='../images/".$admininfo["language"]."/calendar_icon.gif'>
			</td>
			<TD nowrap>
				<input type='text' name='".$sdate_name."' class='textbox point_color' value='".$basic_sdate_ymd."' style='height:20px;width:80px;text-align:center;' id='".$sdate_name."' ".$property."> 일
				".$start_time_select."
			</TD>
			<TD style='padding:0 5px;' align=center> ~ </TD>
			<td>
				<img src='../images/".$admininfo["language"]."/calendar_icon.gif'>
			</td>
			<TD nowrap>
				<input type='text' name='".$edate_name."' class='textbox point_color' value='".$basic_edate_ymd."' style='height:20px;width:80px;text-align:center;' id='".$edate_name."' ".$property."> 일
				".$end_time_select."
			</TD>
		</tr>
	</table>";

if($search_type == 'D'){
$Contents .= "
	<div style='float:left;padding:7px 10px;'>
		<a href=\"javascript:".$sdate_name."('$today','$today',1);\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
		<a href=\"javascript:".$sdate_name."('$vyesterday','$vyesterday',1);\"><img src='../images/".$admininfo[language]."/btn_yesterday.gif'></a>
		<a href=\"javascript:".$sdate_name."('$voneweekago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
		<a href=\"javascript:".$sdate_name."('$v15ago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
		<a href=\"javascript:".$sdate_name."('$vonemonthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
		<a href=\"javascript:".$sdate_name."('$v2monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
		<a href=\"javascript:".$sdate_name."('$v3monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>
	</div>";
}else if($search_type == ''){

}else if($search_type == 'tax'){
$Contents .= "
	<div style='float:left;padding:7px 10px;'>
		<a href=\"javascript:".$sdate_name."('$today','$today',1);\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
		<a href=\"javascript:".$sdate_name."('$vyesterday','$vyesterday',1);\"><img src='../images/".$admininfo[language]."/btn_yesterday.gif'></a>
		<a href=\"javascript:".$sdate_name."('$voneweekago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
		<a href=\"javascript:".$sdate_name."('$v15ago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
		<a href=\"javascript:".$sdate_name."('$vonemonthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
		<a href=\"javascript:".$sdate_name."('$v2monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
		<a href=\"javascript:".$sdate_name."('$v3monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>
			
		<a href=\"javascript:".$sdate_name."('$first_quarter_1','$first_quarter_2',1);\"><img src='../images/".$admininfo[language]."/btn_first_quarter.gif'></a>
		<a href=\"javascript:".$sdate_name."('$second_quarter_1','$second_quarter_2',1);\"><img src='../images/".$admininfo[language]."/btn_second_quarter.gif'></a>
		<a href=\"javascript:".$sdate_name."('$third_quarter_1','$third_quarter_2',1);\"><img src='../images/".$admininfo[language]."/btn_third_quarter.gif'></a>
		<a href=\"javascript:".$sdate_name."('$fourth_quarter_1','$fourth_quarter_2',1);\"><img src='../images/".$admininfo[language]."/btn_fourth_quarter.gif'></a>
	</div>";

}else if($search_type == 'L'){
$Contents .= "
	<div style='float:left;padding:7px 10px;'>
		<a href=\"javascript:".$sdate_name."('$today','$today',1);\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
		<a href=\"javascript:".$sdate_name."('$vyesterday','$vyesterday',1);\"><img src='../images/".$admininfo[language]."/btn_yesterday.gif'></a>
		<a href=\"javascript:".$sdate_name."('$voneweekago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
		<a href=\"javascript:".$sdate_name."('$v15ago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
	</div>";

}else{

$Contents .= "
	<div style='float:left;padding:7px 10px;'>
		<a href=\"javascript:".$sdate_name."('$today','$today',1);\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
		<a href=\"javascript:".$sdate_name."('$today','$voneweekago',1);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
		<a href=\"javascript:".$sdate_name."('$today','$v15ago',1);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
		<a href=\"javascript:".$sdate_name."('$today','$vonemonthago',1);\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
		<a href=\"javascript:".$sdate_name."('$today','$v2monthago',1);\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
		<a href=\"javascript:".$sdate_name."('$today','$v3monthago',1);\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>
	</div>";
}

$Contents .= "
	<script type='text/javascript'>
	<!--
	function ".$sdate_name."(FromDate,ToDate,dType) {
//alert($('#".$sdate_name."').attr('disabled'));
		if($('#".$sdate_name."').attr('disabled') == 'disabled'){
			alert('비활성화 상태에서는 날짜 선택이 불가합니다.');
		}else{
			var frm = document.search_frm;
			$('#".$sdate_name."').val(FromDate);
			$('#".$edate_name."').val(ToDate);
		}
	}

	$(document).ready(function (){
		$('#".$sdate_name."').datepicker({
			//changeMonth: true,
			//changeYear: true,
			monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
			dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
			showMonthAfterYear:true,
			dateFormat: 'yy-mm-dd',
			buttonImageOnly: true,
			buttonText: '달력',
		
			onSelect: function(dateText, inst){
				if($('#".$edate_name."').val() != '' && $('#".$edate_name."').val() <= dateText){
					$('#".$edate_name."').val(dateText);
				}else{
					$('#".$edate_name."').datepicker('setDate','+0d');
				}
			}
		});

		$('#".$edate_name."').datepicker({
			//changeMonth: true,
			//changeYear: true,
			monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
			dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
			showMonthAfterYear:true,
			dateFormat: 'yy-mm-dd',
			buttonImageOnly: true,
			buttonText: '달력',
			onSelect: function(dateText, inst){";
			if($search_type != 'D'){
				$Contents .= "
				/*
				var s_d = s_date.getDate();
				var s_m = s_date.getMonth();
				var s_y = s_date.getFullYear();
				
				//ralert(s_h);
				var sdate = new Date(s_y,s_m,s_d,s_h, s_i);

				alert(sdate);
				*/
				if($('#".$sdate_name."').val() != '' && $('#".$sdate_name."').val() > dateText){
					$('#".$sdate_name."').val(dateText);
				}";
			}
			$Contents .= "
			}
		
		});
	});
	//-->
	</script>
";

return $Contents;
}

function search_date2($sdate_name,$edate_name,$basic_sdate='',$basic_edate='',$use_time='',$search_type='D', $property=""){

	global $admininfo;
	//echo "search_type:".$search_type;
	

	$vdate = date("Y-m-d", time());
	$today = date("Y-m-d", time());

	if($search_type == 'D'){
		
		$vyesterday = date("Y-m-d", time()-86400);
		$voneweekago = date("Y-m-d", time()-86400*7);
		$v15ago = date("Y-m-d", time()-86400*15);
		$vonemonthago = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2)-1,substr($vdate,8,2)+1,substr($vdate,0,4)));
		$v2monthago = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2)-2,substr($vdate,8,2)+1,substr($vdate,0,4)));
		$v3monthago = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2)-3,substr($vdate,8,2)+1,substr($vdate,0,4)));
	}else if($search_type == 'tax'){
		
		$vyesterday = date("Y-m-d", time()-86400);
		$voneweekago = date("Y-m-d", time()-86400*7);
		$v15ago = date("Y-m-d", time()-86400*15);
		$vonemonthago = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2)-1,substr($vdate,8,2)+1,substr($vdate,0,4)));
		$v2monthago = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2)-2,substr($vdate,8,2)+1,substr($vdate,0,4)));
		$v3monthago = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2)-3,substr($vdate,8,2)+1,substr($vdate,0,4)));
				
		$first_quarter_1 = date("Y-01-01", time());
		$first_quarter_2 = date("Y-03-31", time());
		$second_quarter_1 = date("Y-04-01", time());
		$second_quarter_2 = date("Y-06-30", time());
		$third_quarter_1 = date("Y-07-01", time());
		$third_quarter_2 = date("Y-09-30", time());
		$fourth_quarter_1 = date("Y-10-01", time());
		$fourth_quarter_2 = date("Y-12-31", time());
    }else if($search_type == 'L'){
        $vyesterday = date("Y-m-d", time()-86400);
        $voneweekago = date("Y-m-d", time()-86400*7);
        $v15ago = date("Y-m-d", time()-86400*15);
	}else{
		
			$vyesterday = date("Y-m-d", time()+86400);
		$voneweekago = date("Y-m-d", time()+86400*7);
		$v15ago = date("Y-m-d", time()+86400*15);
		$vonemonthago = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2)+1,substr($vdate,8,2)+1,substr($vdate,0,4)));
		$v2monthago = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2)+2,substr($vdate,8,2)+1,substr($vdate,0,4)));
		$v3monthago = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2)+3,substr($vdate,8,2)+1,substr($vdate,0,4)));
	}

	$basic_sdate_array = explode(" ",$basic_sdate);	//넘어온값에 시간이 붙어잇을경우 짤라서 시간을 따로 변수에 담아둠
	$basic_sdate_ymd = $basic_sdate_array[0];

	$basic_edate_array = explode(" ",$basic_edate);	//넘어온값에 시간이 붙어잇을경우 짤라서 시간을 따로 변수에 담아둠
	$basic_edate_ymd = $basic_edate_array[0];
	//print_r($basic_edate_array[0]);

	if($use_time == 'Y'){	//시 까지 사용할경우 처리 
		if($basic_sdate_array[1]){
			$start_time_h = strftime('%H',strtotime($basic_sdate));
			$start_time_i = strftime('%M',strtotime($basic_sdate));
			$start_time_s = strftime('%S',strtotime($basic_sdate));
		}

		if($basic_edate_array[1]){ 
			
			$end_time_h = strftime('%H',strtotime($basic_edate));
			$end_time_i = strftime('%M',strtotime($basic_edate));
			$end_time_s = strftime('%S',strtotime($basic_edate));
			
		}

		$start_time_select = "<select name='".$sdate_name."_h' id='".$sdate_name."_h' >";
		for($i=0;$i<24;$i++){
			$start_time_select .="<option value='".$i."' ".($start_time_h == $i?'selected':'').">".$i."</option>";
		}
		$start_time_select .= "</select> 시";

		$start_time_select .= "<select name='".$sdate_name."_i' id='".$sdate_name."_i'>";
		for($i=0;$i<60;$i++){
			$start_time_select .="<option value='".$i."' ".($start_time_i == $i?'selected':'').">".$i."</option>";
		}
		$start_time_select .= "</select> 분";

		$start_time_select .= "<select name='".$sdate_name."_s' id='".$sdate_name."_s'>";
		for($i=0;$i<60;$i++){
			$start_time_select .="<option value='".$i."' ".($start_time_s == $i?'selected':'').">".$i."</option>";
		}
		$start_time_select .= "</select> 초";


		$end_time_select = "<select name='".$edate_name."_h' id='".$edate_name."_h'>";
		for($i=0;$i<24;$i++){
			$end_time_select .="<option value='".$i."' ".($end_time_h == $i?'selected':'').">".$i."</option>";
		}
		$end_time_select .= "</select> 시";

		$end_time_select .= "<select name='".$edate_name."_i' id='".$edate_name."_i'>";
		for($i=0;$i<60;$i++){
			$end_time_select .="<option value='".$i."' ".($end_time_i == $i?'selected':'').">".$i."</option>";
		}
		$end_time_select .= "</select> 분";

		$end_time_select .= "<select name='".$edate_name."_s' id='".$edate_name."_s'>";
		for($i=0;$i<60;$i++){
			$end_time_select .="<option value='".$i."' ".($end_time_s == $i?'selected':'').">".$i."</option>";
		}
		$end_time_select .= "</select> 초";
	}

$Contents .= "
	<table cellpadding=3  cellspacing=1 border=0 bgcolor=#ffffff style='float:left;'>
		<tr>
			<td>
				<img src='../../images/".$admininfo["language"]."/calendar_icon.gif'>
			</td>
			<TD nowrap>
				<input type='text' name='".$sdate_name."' class='textbox point_color' value='".$basic_sdate_ymd."' style='height:20px;width:80px;text-align:center;' id='".$sdate_name."' ".$property."> 일
				".$start_time_select."
			</TD>
			<TD style='padding:0 5px;' align=center> ~ </TD>
			<td>
				<img src='../../images/".$admininfo["language"]."/calendar_icon.gif'>
			</td>
			<TD nowrap>
				<input type='text' name='".$edate_name."' class='textbox point_color' value='".$basic_edate_ymd."' style='height:20px;width:80px;text-align:center;' id='".$edate_name."' ".$property."> 일
				".$end_time_select."
			</TD>
		</tr>
	</table>";

if($search_type == 'D'){
$Contents .= "
	<div style='float:left;padding:7px 10px;'>
		<a href=\"javascript:".$sdate_name."('$today','$today',1);\"><img src='../../images/".$admininfo[language]."/btn_today.gif'></a>
		<a href=\"javascript:".$sdate_name."('$vyesterday','$vyesterday',1);\"><img src='../../images/".$admininfo[language]."/btn_yesterday.gif'></a>
		<a href=\"javascript:".$sdate_name."('$voneweekago','$today',1);\"><img src='../../images/".$admininfo[language]."/btn_1week.gif'></a>
		<a href=\"javascript:".$sdate_name."('$v15ago','$today',1);\"><img src='../../images/".$admininfo[language]."/btn_15days.gif'></a>
		<a href=\"javascript:".$sdate_name."('$vonemonthago','$today',1);\"><img src='../../images/".$admininfo[language]."/btn_1month.gif'></a>
		<a href=\"javascript:".$sdate_name."('$v2monthago','$today',1);\"><img src='../../images/".$admininfo[language]."/btn_2months.gif'></a>
		<a href=\"javascript:".$sdate_name."('$v3monthago','$today',1);\"><img src='../../images/".$admininfo[language]."/btn_3months.gif'></a>
	</div>";
}else if($search_type == ''){

}else if($search_type == 'tax'){
$Contents .= "
	<div style='float:left;padding:7px 10px;'>
		<a href=\"javascript:".$sdate_name."('$today','$today',1);\"><img src='../../images/".$admininfo[language]."/btn_today.gif'></a>
		<a href=\"javascript:".$sdate_name."('$vyesterday','$vyesterday',1);\"><img src='../../images/".$admininfo[language]."/btn_yesterday.gif'></a>
		<a href=\"javascript:".$sdate_name."('$voneweekago','$today',1);\"><img src='../../images/".$admininfo[language]."/btn_1week.gif'></a>
		<a href=\"javascript:".$sdate_name."('$v15ago','$today',1);\"><img src='../../images/".$admininfo[language]."/btn_15days.gif'></a>
		<a href=\"javascript:".$sdate_name."('$vonemonthago','$today',1);\"><img src='../../images/".$admininfo[language]."/btn_1month.gif'></a>
		<a href=\"javascript:".$sdate_name."('$v2monthago','$today',1);\"><img src='../../images/".$admininfo[language]."/btn_2months.gif'></a>
		<a href=\"javascript:".$sdate_name."('$v3monthago','$today',1);\"><img src='../../images/".$admininfo[language]."/btn_3months.gif'></a>
			
		<a href=\"javascript:".$sdate_name."('$first_quarter_1','$first_quarter_2',1);\"><img src='../../images/".$admininfo[language]."/btn_first_quarter.gif'></a>
		<a href=\"javascript:".$sdate_name."('$second_quarter_1','$second_quarter_2',1);\"><img src='../../images/".$admininfo[language]."/btn_second_quarter.gif'></a>
		<a href=\"javascript:".$sdate_name."('$third_quarter_1','$third_quarter_2',1);\"><img src='../../images/".$admininfo[language]."/btn_third_quarter.gif'></a>
		<a href=\"javascript:".$sdate_name."('$fourth_quarter_1','$fourth_quarter_2',1);\"><img src='../../images/".$admininfo[language]."/btn_fourth_quarter.gif'></a>
	</div>";

}else if($search_type == 'L'){
$Contents .= "
	<div style='float:left;padding:7px 10px;'>
		<a href=\"javascript:".$sdate_name."('$today','$today',1);\"><img src='../../images/".$admininfo[language]."/btn_today.gif'></a>
		<a href=\"javascript:".$sdate_name."('$vyesterday','$vyesterday',1);\"><img src='../../images/".$admininfo[language]."/btn_yesterday.gif'></a>
		<a href=\"javascript:".$sdate_name."('$voneweekago','$today',1);\"><img src='../../images/".$admininfo[language]."/btn_1week.gif'></a>
		<a href=\"javascript:".$sdate_name."('$v15ago','$today',1);\"><img src='../../images/".$admininfo[language]."/btn_15days.gif'></a>
	</div>";

}else{

$Contents .= "
	<div style='float:left;padding:7px 10px;'>
		<a href=\"javascript:".$sdate_name."('$today','$today',1);\"><img src='../../images/".$admininfo[language]."/btn_today.gif'></a>
		<a href=\"javascript:".$sdate_name."('$today','$voneweekago',1);\"><img src='../../images/".$admininfo[language]."/btn_1week.gif'></a>
		<a href=\"javascript:".$sdate_name."('$today','$v15ago',1);\"><img src='../../images/".$admininfo[language]."/btn_15days.gif'></a>
		<a href=\"javascript:".$sdate_name."('$today','$vonemonthago',1);\"><img src='../../images/".$admininfo[language]."/btn_1month.gif'></a>
		<a href=\"javascript:".$sdate_name."('$today','$v2monthago',1);\"><img src='../../images/".$admininfo[language]."/btn_2months.gif'></a>
		<a href=\"javascript:".$sdate_name."('$today','$v3monthago',1);\"><img src='../../images/".$admininfo[language]."/btn_3months.gif'></a>
	</div>";
}

$Contents .= "
	<script type='text/javascript'>
	<!--
	function ".$sdate_name."(FromDate,ToDate,dType) {
//alert($('#".$sdate_name."').attr('disabled'));
		if($('#".$sdate_name."').attr('disabled') == 'disabled'){
			alert('비활성화 상태에서는 날짜 선택이 불가합니다.');
		}else{
			var frm = document.search_frm;
			$('#".$sdate_name."').val(FromDate);
			$('#".$edate_name."').val(ToDate);
		}
	}

	$(document).ready(function (){
		$('#".$sdate_name."').datepicker({
			//changeMonth: true,
			//changeYear: true,
			monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
			dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
			showMonthAfterYear:true,
			dateFormat: 'yy-mm-dd',
			buttonImageOnly: true,
			buttonText: '달력',
		
			onSelect: function(dateText, inst){
				if($('#".$edate_name."').val() != '' && $('#".$edate_name."').val() <= dateText){
					$('#".$edate_name."').val(dateText);
				}else{
					$('#".$edate_name."').datepicker('setDate','+0d');
				}
			}
		});

		$('#".$edate_name."').datepicker({
			//changeMonth: true,
			//changeYear: true,
			monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
			dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
			showMonthAfterYear:true,
			dateFormat: 'yy-mm-dd',
			buttonImageOnly: true,
			buttonText: '달력',
			onSelect: function(dateText, inst){";
			if($search_type != 'D'){
				$Contents .= "
				/*
				var s_d = s_date.getDate();
				var s_m = s_date.getMonth();
				var s_y = s_date.getFullYear();
				
				//ralert(s_h);
				var sdate = new Date(s_y,s_m,s_d,s_h, s_i);

				alert(sdate);
				*/
				if($('#".$sdate_name."').val() != '' && $('#".$sdate_name."').val() > dateText){
					$('#".$sdate_name."').val(dateText);
				}";
			}
			$Contents .= "
			}
		
		});
	});
	//-->
	</script>
";

return $Contents;
}

function select_date($sdate_name,$basic_sdate=''){

	global $admininfo;

	$basic_sdate	=	substr($basic_sdate,0,10);

$Contents .= "
			<table cellpadding=3  cellspacing=1 border=0 bgcolor=#ffffff style='float:left;'>
				<tr>
					<td>
						<img src='../images/".$admininfo["language"]."/calendar_icon.gif'>
					</td>
					<TD nowrap>
						<input type='text' name='".$sdate_name."' value='".$basic_sdate."' class='textbox point_color $sdate_name' style='height:20px;width:70px;text-align:center;'> 일
					</TD>
				</tr>
			</table>

	<script type='text/javascript'>
	<!--

	$(document).ready(function (){
		$('.".$sdate_name."').datepicker({
			//changeMonth: true,
			//changeYear: true,
			dayNamesMin: ['일','월', '화', '수', '목', '금', '토' ],
			monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
			changeYear: true ,
			changeMonth: true ,
			showMonthAfterYear:true,
			yearRange: 'c-80:c+10',
			dateFormat: 'yy-mm-dd',
			buttonImageOnly: true,
			buttonText: '달력',

		});
	});
	//-->
	</script>
";

return $Contents;
}

function select_date2($sdate_name,$basic_sdate=''){

	global $admininfo;

	$basic_sdate	=	substr($basic_sdate,0,10);

$Contents .= "<input type='text' name='".$sdate_name."' value='".$basic_sdate."' />


	<script type='text/javascript'>
	<!--

	$(document).ready(function (){
		$('.".$sdate_name."').datepicker({
			//changeMonth: true,
			//changeYear: true,
			dayNamesMin: ['월', '화', '수', '목', '금', '토', '일'],
			monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
			changeYear: true ,
			changeMonth: true ,
			showMonthAfterYear:true,
			yearRange: 'c-80:c+10',
			dateFormat: 'yy-mm-dd',
			buttonImageOnly: true,
			buttonText: '달력',

		});
	});
	//-->
	</script>
";

return $Contents;
}

function get_delivery_policy_text($template_array,$i='0'){

	$db = new Database;

	if($template_array[$i][delivery_package] == 'N'){
		$use_bundle_text = '묶음배송';
	}else{
		$use_bundle_text = '개별배송';
	}

	if($template_array[$i][is_basic_template]=='1'){
		$is_basic_text = "<span style=color:Red>( 기본 배송정책 )</span>";
	}

	switch($template_array[$i][delivery_policy]){
		case '1':
			if($template_array[$i][delivery_basic_policy] == '2'){
				$template_text = $template_array[$i][template_name]." ".$use_bundle_text." ";
			}else{
				$template_text = $template_array[$i][template_name]." (".$use_bundle_text.") : 무료 ";
			}
			break;
		case '2':
			$template_text = $template_array[$i][template_name]." (".$use_bundle_text.") : 고정배송비 ".number_format($template_array[$i][delivery_price])." 원 ";
			break;
		case '3':
			$sql = "select * from shop_delivery_terms where dt_ix = '".$template_array[$i][dt_ix]."' and delivery_policy_type = '3' order by seq ASC limit 0,1";
			$db->query($sql);
			$db->fetch();
			$template_text =  $template_array[$i][template_name]." (".$use_bundle_text.") / 주문금액 ".number_format($db->dt[delivery_basic_terms])." 원 미만일경우 ".number_format($db->dt[delivery_price])." 원 ";
			break;
		case '4':
			$sql = "select * from shop_delivery_terms where dt_ix = '".$template_array[$i][dt_ix]."'  and delivery_policy_type = '4' order by seq ASC limit 0,1";
			$db->query($sql);
			$db->fetch();
			$template_text = $template_array[$i][template_name]." (".$use_bundle_text.") : 수량별 할인 / 기본배송비 ".number_format($template_array[$i][delivery_cnt_price])." 원 ".number_format($db->dt[delivery_price])." 개 이상시 ".number_format($db->dt[delivery_basic_terms])." 원 배송비 적용 ";
			break;
		case '5':
			$sql = "select * from shop_delivery_address where addr_ix = '".$template_array[$i][factory_info_addr_ix]."'";
			$db->query($sql);
			$db->fetch();

			$template_text = $template_array[$i][template_name]." (".$use_bundle_text.") : 출고지별 배송비 ( ".$db->dt[addr_name]." ) ";
			break;
		case '6':
			$template_text = $template_array[$i][template_name]." (".$use_bundle_text.") : 상품 1개단위 배송비 ".number_format($template_array[$i][delivery_unit_price])." 원 ";
			break;
	}

	return $template_text."".$is_basic_text;

}


function product_list_policy_text($dt_ix){	//배송템플릿 키별로 정책 가져오기

	$db = new Database;

	$sql = "select
			*
			from
				shop_delivery_template
			where
				dt_ix = '".$dt_ix."'";

	$db->query($sql);
	$template_array = $db->fetchall();

	$template_text = get_delivery_policy_text($template_array,'0');

	return $template_text;

}

function GetProductDliveryPolicyText($pid,$is_whole='R'){	//상품 아이디별로 정책 불러오기

	$db = new Database;

	$sql = "select
			*
			from
				shop_product_delivery
			where
				pid = '".$pid."'
				and is_wholesale = '".$is_whole."'
				order by delivery_div limit 0,1";
	$db->query($sql);
	$db->fetch();
	$dt_ix = $db->dt[dt_ix];

	$template_text = product_list_policy_text($dt_ix);

	return $template_text;

}

function GetDeliveryName($pid,$is_whole='R'){

	$db = new Database;

	$sql = "select
				dt.template_name
			from
				shop_product_delivery as pd
				left join shop_delivery_template as dt on (pd.dt_ix = dt.dt_ix)
			where
				pd.pid = '".$pid."'
				and pd.is_wholesale =  '".$is_whole."'
				order by pd.delivery_div limit 0,1";

	$db->query($sql);
	$db->fetch();

	$template_name = $db->dt[template_name];

	return $template_name;

}

// 상품 수정 히스토리 쌓기 함수 2014-04-09 이학봉
function product_edit_history_insert($pid,$column_name,$column_text,$b_data,$after_data,$chager_ix,$chager_name){	//회원정보 수정 히스토리 쌓기 2013-11-28 이학봉

	if(!$pid){
		return false;
	}

	$db = new Database;

	$sql = "insert into shop_product_edit_history set
				pid = '".$pid."',
				b_data = '".$b_data."',
				after_data = '".$after_data."',
				column_name = '".$column_name."',
				column_text = '".$column_text."',
				chager_ix = '".$chager_ix."',
				chager_name = '".$chager_name."',
				regdate = NOW()";

	$db->query($sql);

}

// 상품 수정 히스토리 쌓기 함수 2014-04-09 이학봉
function product_edit_history($_POST,$_FILES,$pid){

	global $_SESSION;

	if(!$pid){
		return false;
	}

	$db = new Database;

	$compare_value[0] = array("input_name"=>"pname", "column_name"=>"pname", "name_text"=>"상품명");
	$compare_value[1] = array("input_name"=>"company_id", "column_name"=>"admin", "name_text"=>"셀러업체");
	$compare_value[2] = array("input_name"=>"trade_admin", "column_name"=>"trade_admin", "name_text"=>"매입업체");
	$compare_value[3] = array("input_name"=>"surtax_yorn", "column_name"=>"surtax_yorn", "name_text"=>"면세제품");
	$compare_value[4] = array("input_name"=>"state", "column_name"=>"state", "name_text"=>"판매상태");
	$compare_value[5] = array("input_name"=>"disp", "column_name"=>"disp", "name_text"=>"노출여부");
	$compare_value[6] = array("input_name"=>"mandatory_type_1", "column_name"=>"mandatory_type", "name_text"=>"상품정보고시");
	$compare_value[7] = array("input_name"=>"reserve_yn", "column_name"=>"reserve_yn", "name_text"=>"소매적립금 적용여부");
	$compare_value[8] = array("input_name"=>"reserve", "column_name"=>"reserve", "name_text"=>"소매 적립금");
	$compare_value[9] = array("input_name"=>"wholesale_reserve_yn", "column_name"=>"wholesale_reserve_yn", "name_text"=>"도매적립금 적용여부");
	$compare_value[10] = array("input_name"=>"wholesale_reserve", "column_name"=>"wholesale_reserve", "name_text"=>"도매 적립금");
	$compare_value[11] = array("input_name"=>"stock_options", "column_name"=>"stock_options", "name_text"=>"가격+재고관리 옵션");	//처리필요
	$compare_value[12] = array("input_name"=>"box_options", "column_name"=>"box_options", "name_text"=>"박스 옵션");				//처리필요
	$compare_value[13] = array("input_name"=>"codi_options", "column_name"=>"codi_options", "name_text"=>"코디옵션");				//처리필요
	$compare_value[14] = array("input_name"=>"set2options", "column_name"=>"set2options", "name_text"=>"세트옵션");					//처리필요
	//$compare_value[15] = array("input_name"=>"basicinfo", "column_name"=>"basicinfo", "name_text"=>"상세페이지");					//처리필요
	$compare_value[16] = array("input_name"=>"image", "column_name"=>"image", "name_text"=>"상품이미지");							//처리필요
	//$compare_value[17] = array("input_name"=>"dt_ix_retail", "column_name"=>"dt_ix_retail", "name_text"=>"소매 배송정책");
	//$compare_value[18] = array("input_name"=>"dt_ix_whole", "column_name"=>"dt_ix_whole", "name_text"=>"도매 배송정책");

	$sql = "select
				*
			from
				shop_product
			where
				id = '".$pid."'";
	$db->query($sql);
	$db->fetch();
	$db_value = $db->dt;

	for($i=0;$i<count($compare_value);$i++){

		if($compare_value[$i][input_name] == 'mandatory_type_1'){
			if($_POST[mandatory_type_1]."|".$_POST[mandatory_type_2] != $db_value[$compare_value[$i][column_name]]){
				product_edit_history_insert($pid,$compare_value[$i][column_name],$compare_value[$i][name_text],$db_value[$compare_value[$i][column_name]],$_POST[mandatory_type_1]."|".$_POST[mandatory_type_2],$_SESSION[admininfo][charger_ix],$_SESSION[admininfo][charger]);
			}
		}else if($compare_value[$i][input_name] == 'stock_options'){	//가격재고 옵션 비교
			if($_POST[stock_options][0][details][0][option_div] != ""){
				get_setoption_detail($pid,'stock_options','가격+재고관리 옵션',$_POST);		//옵션 수정사항 체크 처리
			}
		}else if($compare_value[$i][input_name] == 'box_options'){	//박스옵션 비교
			if($_POST[box_options][0][details][0][option_div] != ""){
				get_setoption_detail($pid,'box_options','박스옵션',$_POST);		//옵션 수정사항 체크 처리
			}
		}else if($compare_value[$i][input_name] == 'codi_options'){	//코디옵션 비교
			if($_POST[codi_options][0][details][0][option_div] != ""){
				get_setoption_detail($pid,'codi_options','코디옵션',$_POST);		//옵션 수정사항 체크 처리
			}
		}else if($compare_value[$i][input_name] == 'set2options'){	//세트(묶음상품)옵션 비교

			if($_POST[set2options][0][details][0][option_div] != ""){
				get_setoption_detail($pid,'set2options','세트(묶음상품)옵션',$_POST);		//옵션 수정사항 체크 처리
			}

		}else if($compare_value[$i][input_name] == 'image'){	//이미지수정 비교
			if($_FILES[allimg][name]){
				product_edit_history_insert($pid,$compare_value[$i][column_name],$compare_value[$i][name_text],'상품이미지','상품이미지',$_SESSION[admininfo][charger_ix],$_SESSION[admininfo][charger]);
			}
		}else{
			if($_POST[$compare_value[$i][input_name]] != $db_value[$compare_value[$i][column_name]]){
				product_edit_history_insert($pid,$compare_value[$i][column_name],$compare_value[$i][name_text],$db_value[$compare_value[$i][column_name]],$_POST[$compare_value[$i][input_name]],$_SESSION[admininfo][charger_ix],$_SESSION[admininfo][charger]);
			}
		}
	}

}
// 상품 수정 히스토리 쌓기 함수 2014-04-09 이학봉
function get_setoption_detail($pid,$option_type,$option_name='',$_POST){

	global $_SESSION;

	$db = new Database;
	if(!$pid){
		return false;
	}

	$sql = "select
			*
			from
				shop_product_options as po
				inner join shop_product_options_detail as pod on (po.opn_ix = pod.opn_ix)
			where
				po.pid = '".$pid."'";
	$db->query($sql);
	$data_array = $db->fetchall();

	for($i=0;$i<count($data_array);$i++){
		if($i == '0'){
			$option_details[$data_array[$i][set_group]][option_kind] = $data_array[$i][option_kind];
			$option_details[$data_array[$i][set_group]][option_name] = $data_array[$i][option_name];
		}
		$option_details[$data_array[$i][set_group]][details][$data_array[$i][set_group_seq]][option_div] = $data_array[$i][option_div];
		$option_details[$data_array[$i][set_group]][details][$data_array[$i][set_group_seq]][coprice] = $data_array[$i][coprice];
		$option_details[$data_array[$i][set_group]][details][$data_array[$i][set_group_seq]][wholesale_listprice] = $data_array[$i][wholesale_listprice];
		$option_details[$data_array[$i][set_group]][details][$data_array[$i][set_group_seq]][wholesale_price] = $data_array[$i][wholesale_price];
		$option_details[$data_array[$i][set_group]][details][$data_array[$i][set_group_seq]][listprice] = $data_array[$i][listprice];
		$option_details[$data_array[$i][set_group]][details][$data_array[$i][set_group_seq]][sellprice] = $data_array[$i][sellprice];
		$option_details[$data_array[$i][set_group]][details][$data_array[$i][set_group_seq]][sell_ing_cnt] = $data_array[$i][sell_ing_cnt];
		$option_details[$data_array[$i][set_group]][details][$data_array[$i][set_group_seq]][stock] = $data_array[$i][stock];
		$option_details[$data_array[$i][set_group]][details][$data_array[$i][set_group_seq]][safestock] = $data_array[$i][safestock];
		$option_details[$data_array[$i][set_group]][details][$data_array[$i][set_group_seq]][code] = $data_array[$i][code];
		$option_details[$data_array[$i][set_group]][details][$data_array[$i][set_group_seq]][barcode] = $data_array[$i][barcode];
	}

	foreach($option_details as $set_group => $options){

		if($options[option_kind] != $_POST[$option_type][$set_group][option_kind]){
			product_edit_history_insert($pid,$option_type,$option_name." 옵션종류",$options[option_kind],$_POST[$option_type][$set_group][option_kind],$_SESSION[admininfo][charger_ix],$_SESSION[admininfo][charger]);
		}

		foreach($options as $set_group_seq => $details){
			/* 비교대상
			$options[option_kind] == $_POST[set2options][$set_group][option_kind]	옵션종류
			$details[option_div] == $_POST[set2options][$set_group][details][$set_group_seq][option_div]		옵션구분
			$details[wholesale_listprice] == $_POST[set2options][$set_group][details][$set_group_seq][wholesale_listprice]	도매판매가
			$details[wholesale_price] == $_POST[set2options][$set_group][details][$set_group_seq][wholesale_price]		도매할인가
			$details[listprice] == $_POST[set2options][$set_group][details][$set_group_seq][listprice]		소매 판매가
			$details[sellprice] == $_POST[set2options][$set_group][details][$set_group_seq][sellprice]		소매할인가
			$details[stock] == $_POST[set2options][$set_group][details][$set_group_seq][stock]				재고수량
			$details[option_code] != $_POST[set2options][$set_group][details][$set_group_seq][code]		옵션코드(gu_ix)
			*/

			if($options[option_div] != $_POST[$option_type][$set_group][option_div]){
				product_edit_history_insert($pid,$option_type,$option_name." 옵션구분",$options[option_div],$_POST[$option_type][$set_group][option_div],$_SESSION[admininfo][charger_ix],$_SESSION[admininfo][charger]);
			}else if($options[wholesale_listprice] != $_POST[$option_type][$set_group][wholesale_listprice]){
				product_edit_history_insert($pid,$option_type,$option_name." 도매판매가",$options[wholesale_listprice],$_POST[$option_type][$set_group][wholesale_listprice],$_SESSION[admininfo][charger_ix],$_SESSION[admininfo][charger]);
			}else if($options[wholesale_price] != $_POST[$option_type][$set_group][wholesale_price]){
				product_edit_history_insert($pid,$option_type,$option_name." 도매할인가",$options[wholesale_price],$_POST[$option_type][$set_group][wholesale_price],$_SESSION[admininfo][charger_ix],$_SESSION[admininfo][charger]);
			}else if($options[listprice] != $_POST[$option_type][$set_group][listprice]){
				product_edit_history_insert($pid,$option_type,$option_name." 소매판매가",$options[listprice],$_POST[$option_type][$set_group][listprice],$_SESSION[admininfo][charger_ix],$_SESSION[admininfo][charger]);
			}else if($options[sellprice] != $_POST[$option_type][$set_group][sellprice]){
				product_edit_history_insert($pid,$option_type,$option_name." 소매할인가",$options[sellprice],$_POST[$option_type][$set_group][sellprice],$_SESSION[admininfo][charger_ix],$_SESSION[admininfo][charger]);
			}else if($options[stock] != $_POST[$option_type][$set_group][stock]){
				product_edit_history_insert($pid,$option_type,$option_name." 재고수량",$options[stock],$_POST[$option_type][$set_group][stock],$_SESSION[admininfo][charger_ix],$_SESSION[admininfo][charger]);
			}else if($options[option_code] != $_POST[$option_type][$set_group][code]){
				product_edit_history_insert($pid,$option_type,$option_name." 옵션코드(gu_ix)",$options[option_code],$_POST[$option_type][$set_group][code],$_SESSION[admininfo][charger_ix],$_SESSION[admininfo][charger]);
			}

		}
	}

}
// 상품 수정 페이지 히스토리 노출 함수 2014-04-09 이학봉
function Product_edit_history_Text($pid){

	if(!$pid){
		return false;
	}

	$db = new Database;

	$sql = "select
			*
			from
				shop_product_edit_history
			where
				pid = '".$pid."'
				order by peh_ix ASC";
	$db->query($sql);
	$data_array = $db->fetchall();

	for($i=0;$i<count($data_array);$i++){
		/*
			$compare_value[0] = array("input_name"=>"pname", "column_name"=>"pname", "name_text"=>"상품명");
			$compare_value[1] = array("input_name"=>"company_id", "column_name"=>"admin", "name_text"=>"셀러업체");
			$compare_value[2] = array("input_name"=>"trade_admin", "column_name"=>"trade_admin", "name_text"=>"매입업체");
			$compare_value[3] = array("input_name"=>"surtax_yorn", "column_name"=>"surtax_yorn", "name_text"=>"면세제품");
			$compare_value[4] = array("input_name"=>"state", "column_name"=>"state", "name_text"=>"판매상태");
			$compare_value[5] = array("input_name"=>"disp", "column_name"=>"disp", "name_text"=>"노출여부");
			$compare_value[6] = array("input_name"=>"mandatory_type_1", "column_name"=>"mandatory_type", "name_text"=>"상품정보고시");
			$compare_value[7] = array("input_name"=>"reserve_yn", "column_name"=>"reserve_yn", "name_text"=>"소매적립금 적용여부");
			$compare_value[8] = array("input_name"=>"reserve", "column_name"=>"reserve", "name_text"=>"소매 적립금");
			$compare_value[9] = array("input_name"=>"wholesale_reserve_yn", "column_name"=>"wholesale_reserve_yn", "name_text"=>"도매적립금 적용여부");
			$compare_value[10] = array("input_name"=>"wholesale_reserve", "column_name"=>"wholesale_reserve", "name_text"=>"도매 적립금");
			$compare_value[11] = array("input_name"=>"stock_options", "column_name"=>"stock_options", "name_text"=>"가격+재고관리 옵션");	//처리필요
			$compare_value[12] = array("input_name"=>"box_options", "column_name"=>"box_options", "name_text"=>"박스 옵션");				//처리필요
			$compare_value[13] = array("input_name"=>"codi_options", "column_name"=>"codi_options", "name_text"=>"코디옵션");				//처리필요
			$compare_value[14] = array("input_name"=>"set2options", "column_name"=>"set2options", "name_text"=>"세트옵션");					//처리필요
			$compare_value[15] = array("input_name"=>"basicinfo", "column_name"=>"basicinfo", "name_text"=>"상세페이지");					//처리필요
			$compare_value[16] = array("input_name"=>"image", "column_name"=>"image", "name_text"=>"상품이미지");							//처리필요
			$compare_value[17] = array("input_name"=>"dt_ix_retail", "column_name"=>"dt_ix_retail", "name_text"=>"소매 배송정책");
			$compare_value[18] = array("input_name"=>"dt_ix_whole", "column_name"=>"dt_ix_whole", "name_text"=>"도매 배송정책");
		*/

		if($data_array[$i][column_name] == 'basicinfo'){
			$history_text .= $data_array[$i][regdate]." 상세페이지 변경 : ".$data_array[$i][chager_name]."\n";
		}else if( $data_array[$i][column_name] == 'image'){
			$history_text .= $data_array[$i][regdate]." 상품 변경 : ".$data_array[$i][chager_name]."\n";
		}else if( $data_array[$i][column_name] == 'admin' || $data_array[$i][column_name] == 'trade_admin'){	//셀러업체 와 매입업체
			$b_admin = get_com_name($data_array[$i][b_data]);
			$admin = get_com_name($data_array[$i][after_data]);
			$history_text .= $data_array[$i][regdate]." ".$data_array[$i][column_text]." 변경 : ".$b_admin." -> ".$admin." ( ".$data_array[$i][chager_name]." )\n";
		}else if($data_array[$i][column_name] == 'surtax_yorn'){

			switch($data_array[$i][b_data]){
				case 'N':
					$b_data = "과세";
				break;
				case 'Y':
					$b_data = "면세";
				break;
				case 'P':
					$b_data = "영세";
				break;
			}

			switch($data_array[$i][after_data]){
				case 'N':
					$after_data = "과세";
				break;
				case 'Y':
					$after_data = "면세";
				break;
				case 'P':
					$after_data = "영세";
				break;
			}

			$history_text .= $data_array[$i][regdate]." ".$data_array[$i][column_text]." 변경 : ".$b_data." -> ".$after_data." ( ".$data_array[$i][chager_name]." )\n";
		}else if($data_array[$i][column_name] == 'state'){

			switch($data_array[$i][b_data]){
				case '1':
					$b_data = "판매중";
				break;
				case '0':
					$b_data = "일시품절";
				break;
				case '2':
					$b_data = "판매중지";
				break;
				case '6':
					$b_data = "승인대기";
				break;
				case '8':
					$b_data = "승인거부";
				break;
				case '9':
					$b_data = "판매금지";
				break;
				case '7':
					$b_data = "본사대기상품";
				break;

			}

			switch($data_array[$i][after_data]){
						case '1':
					$after_data = "판매중";
				break;
				case '0':
					$after_data = "일시품절";
				break;
				case '2':
					$after_data = "판매중지";
				break;
				case '6':
					$after_data = "승인대기";
				break;
				case '8':
					$after_data = "승인거부";
				break;
				case '9':
					$after_data = "판매금지";
				break;
				case '7':
					$after_data = "본사대기상품";
				break;
			}

			$history_text .= $data_array[$i][regdate]." ".$data_array[$i][column_text]." 변경 : ".$b_data." -> ".$after_data." ( ".$data_array[$i][chager_name]." )\n";
		}else if($data_array[$i][column_name] == 'disp'){

			switch($data_array[$i][b_data]){
				case '1':
					$b_data = "노출함";
				break;
				case '0':
					$b_data = "노출안함";
				break;
			}

			switch($data_array[$i][after_data]){
				case '1':
					$after_data = "노출함";
				break;
				case '0':
					$after_data = "면세노출안함";
				break;
			}

			$history_text .= $data_array[$i][regdate]." ".$data_array[$i][column_text]." 변경 : ".$b_data." -> ".$after_data." ( ".$data_array[$i][chager_name]." )\n";
		}else{
			$history_text .= $data_array[$i][regdate]." ".$data_array[$i][column_text]." 변경 : ".$data_array[$i][b_data]." -> ".$data_array[$i][after_data]." ( ".$data_array[$i][chager_name]." )\n";
		}
	}

	return $history_text;
}
// 상품 수정 페이지 히스토리 노출 함수 2014-04-09 이학봉

function get_com_name($company_id){	//거래처 코드로 거래처명 불러오기 2014-04-11 이학봉

	if(!$company_id){
		return false;
	}

	$db = new Database;

	$sql = "select
			com_name
			from
				common_company_detail
			where
				company_id = '".$company_id."'";
	$db->query($sql);
	$db->fetch();
	return $db->dt[com_name];
}

function AvergeDliveryDate($pid){

	if(!$pid){
		return false;
	}

	$db = new Database;

	$sql = "select
		AVG(to_days(di_date) - to_days(ic_date)) as avg_date
	from
		shop_order_detail
	where
		pid = '".$pid."'
		and status in ('DI','DC','BF')
		group by pid";

	$db->query($sql);
	$db->fetch();

	return floor($db->dt[avg_date]);
}

function GET_SELLER_INFO($company_id){

	if(!$company_id){
		return false;
	}

	$db = new Database;

	$sql = "select
				ccd.*,
				csd.charge_code,
				AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
				AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail,
				AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as pcs
			from
				common_company_detail as ccd
				left join common_seller_detail as csd on (ccd.company_id = csd.company_id)
				left join common_member_detail as cmd on (csd.charge_code = cmd.code)
			where
				ccd.company_id = '".$company_id."'";
	$db->query($sql);
	$db->fetch();

	$data = "셀러명 : ".$db->dt[com_name]."<br>";
	$data .= "사업자번호 : ".$db->dt[com_number]."<br>";
	//$data .= "셀러등급/패널티 : ".$db->dt[com_name]."<br>";
	$data .= "담당자명 : ".($db->dt[name]?$db->dt[name]:'담당자 미지정')."<br>";
	if($db->dt[name]){
		$data .= "연락처 : ".$db->dt[pcs]."<br>";
		$data .= "이메일 : ".$db->dt[mail]."<br>";
	}
	return $data;
}

function get_state_info($pid,$sate){

	if(!$pid){ return false;}
	$db = new Database;

	$sql = "select
				psh.state,
				psh.state_div,
				psh.state_msg
			from
				shop_product as p

				left join shop_product_state_history as psh on (p.id = psh.pid and p.state = psh.state)
			where
				p.id ='".$pid."'";
	$db->query($sql);
	$db->fetch();

	switch($sate){
		case '1':
			$state_text = "판매중";
		break;
		case '0':
			$state_text = "임시품절";
		break;
		case '2':
			$state_text = "판매중지";
		break;
		case '6':
			$state_text = "승인대기";
		break;
		case '8':
			$state_text = "승인거부";
		break;
		case '9':
			$state_text = "판매거부";
		break;
		case '7':
			$state_text = "본사대기상품";
		break;

	}

	if($db->dt[state_div]){
		switch($db->dt[state_div]){
			case '1':
				$state_div = "상품품절";
			break;
			case '2':
				$state_div = "재고부족";
			break;
			case '3':
				$state_div = "등록오류";
			break;
			case '4':
				$state_div = "판매불가상품";
			break;
			case '5':
				$state_div = "정보미흡";
			break;
			case '6':
				$state_div = "이미지미흡";
			break;
			case '7':
				$state_div = "거래상품아님";
			break;
			case '8':
				$state_div = "저작권위배요청상품";
			break;
			case '9':
				$state_div = "이미테이션";
			break;
			case '10':
				$state_div = "기타";
			break;
		}
	}

	$data = $state_text."<br>";
	$data .= "분류 : ".$state_div."<br>";
	//$data .= "셀러등급/패널티 : ".$db->dt[com_name]."<br>";
	$data .= "사유 : ".$db->dt[state_msg]."<br>";

	return $data;

}


function getDeliveryPayType($delivery_pay_type){

	if($delivery_pay_type == "1"){
		$return = "선불";
	}elseif($delivery_pay_type == "2"){
		$return = "착불";
	}else{
		$return = "무료";
	}

	return $return;
}

function getDeliveryMethod($delivery_method){
	//배송방법(1:택배,2:화물,3:직배송,4:방문수령)
	if($delivery_method == '1'){
		return "택배";
	}else if ($delivery_method == '2'){
		return "화물";
	}else if ($delivery_method == '3'){
		return "직배송";
	}else if ($delivery_method == '4'){
		return "방문수령";
	}
}

function getFactoryAddrName($addr_ix){

	if($addr_ix == ""){
		return false;
	}

	$db = new Database;
	$sql = "select * from shop_delivery_address where addr_ix = '".$addr_ix."'";
	$db->query($sql);
	$db->fetch();

	return $db->dt[addr_name];
}

function getOrderDetailDeliveryType($order_type){
	if($order_type=="1"){
		$return="정상";
	}elseif($order_type=="2"){
		$return="교환";
	}elseif($order_type=="3"){
		$return="반품(역배송)";
	}elseif($order_type=="4"){
		$return="수거";
	}

	return $return;
}

function MakerList($company, $cid, $return_type ="",$c_ix='',$select_name='company',$select_id='company',$input_name='c_ix')
{
//global $db;

	$mdb = new Database;
	$db = new Database;

	$sql = "SELECT * FROM ".TBL_SHOP_COMPANY." where disp=1";
	$db->query($sql);
	$db->fetch();
	$total = $db->total;

	if($total < constant("MAX_COMPANY_VIEW_CNT") && false){

		if($cid){
			$mdb->query("SELECT * FROM ".TBL_SHOP_COMPANY." where disp=1 and cid = '$cid' order by company_name asc");
		}else{
			$mdb->query("SELECT * FROM ".TBL_SHOP_COMPANY." where disp=1 order by company_name asc");
		}

		$bl = "<Select name='".$select_name."' id='".$select_name."' style='height:23px;'>";

		if($mdb->total == 0){
			$bl = $bl."<Option>등록된 제조사가 없습니다.</Option>";
		}else{
			if($return_type == ""){
				$bl = $bl."<Option value=''>제조사 선택</Option>";
				for($i=0 ; $i <$mdb->total ; $i++)
				{
					$mdb->fetch($i);
					if ($company == $mdb->dt[company_name]){
						$strSelected = "Selected";
					}else{
						$strSelected = "";
					}
					$bl = $bl."<Option value='".$mdb->dt[company_name]."' $strSelected>".$mdb->dt[company_name]."</Option>";
				}
			}else{
				for($i=0 ; $i <$mdb->total ; $i++)
				{
					$mdb->fetch($i);
					if ($brand == $mdb->dt[c_ix]){
						return $mdb->dt[comapny_name];
					}
				}
			}
		}

		$bl = $bl."</Select>";
		$bl .= "
			<script>
			$(function() {
				$('#".$select_name."').combobox();
			});
			</script>";

			$mstring = "
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td>
						<div id='company_select_area'>".$bl."</div>
					</td>
					<td style='padding:0px 3px 0px 32px; '>
						<a href=\"javascript:PoPWindow3('../product/origin_list.php?mmode=pop',960,700,'company')\"'><img src='../images/".$_SESSION["admininfo"]["language"]."/btn_pop_manage.gif' align=absmiddle border=0></a>
					</td>
				</tr>
			</table>";

	}else{

		$sql = "SELECT * FROM ".TBL_SHOP_COMPANY." where c_ix = '".$c_ix."' ";
		//echo $sql;
		$mdb->query($sql);
		$mdb->fetch();

		$bl =	"	<table cellpadding=0 cellspacing=0>
						<tr>
							<td><input type=hidden class='textbox' name='".$input_name."' id='".$input_name."'  value='".$mdb->dt[c_ix]."' ></td>
							<td><input type=text class='textbox point_color' name='".$select_name."' id='".$select_name."' value='".$mdb->dt[company_name]."' style='width:140px;' readonly onclick=\"PoPWindow('../company_search.php?input_id=".$input_name."&select_name=".$select_name."',600,380,'company_search')\"></td>
							<td style='padding-left:5px;'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"PoPWindow('../company_search.php?input_id=".$input_name."&select_name=".$select_name."',600,380,'company_search')\"  style='cursor:pointer;'> </td>
							<td style='padding-left:3px;'> <img src='../images/btn_x.gif' style='cursor:pointer' onclick=\"$(this).parent().parent().find('#".$input_name."').val('');$(this).parent().parent().find('#".$select_name."').val('');\"></td>
						</tr>
					</table>";
	}

	return $bl;
}

//상품 배송정책 입력 함수	2014-05-13 이학봉
function Insert_product_delivery($dt_ix = array(),$company_id,$pid,$delivery_policy){
	//global $db ,$admininfo;

	$db = new Database;

	if($pid == ""){
		return false;
	}

	if(is_array($dt_ix)){
		//$sql = "delete from shop_product_delivery where pid = '".$pid."'";
		//$db->query($sql);

		foreach($dt_ix as $is_wholesale => $infos){
			foreach($infos as $delivery_div => $details){
				if($details[template_check] == '1'){

					$sql = "select * from shop_product_delivery where pid = '".$pid."' and is_wholesale = '".$is_wholesale."'";
					$db->query($sql);

					if($db->total > 0){
						if($details[dt_ix] > '0'){
							$sql = "update shop_product_delivery set
										company_id = '".$company_id."',
										delivery_div = '".$delivery_div."',
										dt_ix = '".$details[dt_ix]."'
									where
										pid = '".$pid."'
										and is_wholesale = '".$is_wholesale."'";
							$db->query($sql);
						}

					}else{
						if($details[dt_ix] > '0'){
							$sql = "insert into shop_product_delivery set
										pid = '".$pid."',
										company_id = '".$company_id."',
										delivery_div = '".$delivery_div."',
										is_wholesale = '".$is_wholesale."',
										dt_ix = '".$details[dt_ix]."',
										regdate = NOW()";
										//echo nl2br($sql)."<br><br>";
							$db->query($sql);
						}
					}
				}else{
					$sql = "delete from shop_product_delivery where pid = '".$pid."' and is_wholesale = '".$is_wholesale."'";
					$db->query($sql);
				}
			}

		}
		$db->query("update shop_product set delivery_policy = '".$delivery_policy."' where id = '".$pid."'");
	}


}


function PorudctBasicDeliveryPrice($pid){	//상품당 기본 배송비 (외부 쇼핑몰 연동시 주요 사용 2014-07-31 이학봉)

	global $layout_config,$user,$shop_product_type,$sns_product_type;
	global $master_db;

	if($pid == ""){
		return 0;
	}else{

		$sql = "select
					dt.*,
					p.delivery_type,
					p.sellprice as totalprice

				from
					shop_product as p
					inner join shop_product_delivery as pd on (p.id = pd.pid)
					inner join shop_delivery_template as dt on (pd.dt_ix = dt.dt_ix)
				where
					p.id = '".$pid."' and pd.pid = '".$pid."'
					and dt.product_sell_type = 'R'";

		$master_db->query($sql);
		$rows = $master_db->fetchall();

	for($i=0;$i<count($rows);$i++){	//총주문수량과 총주문금액을 가져오기 2014-07-24 이학봉
		$total_totalprice += $rows[$i][totalprice];	//총주문금액
	}

	for($i=0;$i<count($rows);$i++){	//장바구니 분리별로 배송비 구하기 시작

		$dt_ix = $rows[$i][dt_ix];
		$delivery_policy = $rows[$i][delivery_policy];	//배송비 조건타입
		$delivery_method = $rows[$i][delivery_method];	//배송방법
		$pcount = '1';	//주문수량
		$totalprice = $rows[$i][totalprice];	//총주문금액
		$delivery_package = $rows[$i][delivery_package];	//Y:묶음배송 N:개별배송

		$template_infos = delivery_template_info($dt_ix);

		//조건배송 타입(배송정책)	1:무료배송 2:고정배송비 3:주문결제금액 할인 4:수량별할인 5:출고지별 배송비 6: 상품1개단위 배송비
		if($delivery_policy == "1"){	//1:무료배송

			$delivery_price_array[99] = '0';	//무료배송이 잇을경우 키값을 99 로 넣어서 구분하게 처리함

		}else if($delivery_policy == "2"){	//2:고정배송비

			$delivery_price = $template_infos[delivery_price];

		}else if($delivery_policy == "3"){	//3:주문결제금액 할인

			$sql = "select * from shop_delivery_terms where dt_ix = '".$dt_ix."' and product_sell_type = '".$template_infos[product_sell_type]."' and delivery_policy_type = '".$delivery_policy."' order by delivery_basic_terms ASC";
			$master_db->query($sql);
			$delivery_terms_array = $master_db->fetchall();
			$delivery_price = '0';
			for($k=0;$k<count($delivery_terms_array);$k++){
				if($total_totalprice < $delivery_terms_array[$k][delivery_basic_terms]){
					$delivery_price = $delivery_terms_array[$k][delivery_price];
					break;
				}
			}
			/*
			$sql = "select * from shop_delivery_terms where dt_ix = '".$dt_ix."' and product_sell_type = '".$template_infos[product_sell_type]."' and delivery_policy_type = '".$delivery_policy."' order by delivery_basic_terms DESC";
			$master_db->query($sql);
			$delivery_terms_array = $master_db->fetchall();

			$delivery_price = '0';	//최초 시작을 0으로 설정하고 주문할인별, 수량별 체크시 사용 2014-08-01 이학봉
			for($k=0;$k<count($delivery_terms_array);$k++){
				if($total_totalprice <= $delivery_terms_array[$k][delivery_basic_terms]){
					//echo "$total_totalprice"." == ".$delivery_terms_array[$k][delivery_basic_terms]."<br>";
					$delivery_price = $delivery_terms_array[$k][delivery_price];
				}

				if($delivery_price == '0'){
					//$delivery_price = $delivery_terms_array[0][delivery_price];
				}

			}
			*/

		}else if($delivery_policy == "4"){	//4:수량별할인

			if($template_infos[extra_charge] == '1'){	//할인율 적용	이부분은 필요없을거 같음
				//$asc = 'DESC';
			}else{										//할증율 적용
				//$asc = 'ASC';
			}

			$sql = "select * from shop_delivery_terms where dt_ix = '".$dt_ix."' and product_sell_type = '".$template_infos[product_sell_type]."' and delivery_policy_type = '".$delivery_policy."' order by delivery_price ASC";

			$master_db->query($sql);
			$delivery_terms_array = $master_db->fetchall();

			$delivery_price = '0';	//최초 시작을 0으로 설정하고 주문할인별, 수량별 체크시 사용 2014-08-01 이학봉
			for($k=0;$k<count($delivery_terms_array);$k++){
				if($pcount >= $delivery_terms_array[$k][delivery_price]){
					$delivery_price = $delivery_terms_array[$k][delivery_basic_terms];
				}

				if($delivery_price == '0'){
					$delivery_price = $template_infos[delivery_cnt_price];
				}
			}

		}else if($delivery_policy == "5"){	//5:출고지별 배송비 //잠시 보류

			$delivery_price = '0';
		}else if($delivery_policy == "6"){	//6:상품1개단위 배송비
			//echo "$delivery_policy"."<br>";
			$delivery_price = $pcount * $template_infos[delivery_unit_price];
		}

		if($delivery_method == '4'){	//배송방법이 방문수령일 경우 무조건 0원
			$delivery_price = '0';
		}else{
			$delivery_price = $delivery_price;
		}

		//도서산간 배송비 관련 시작 2014-05-19 이학봉
		//$addr = "서울시 서초구 양재동";
		if($addr !=""){

			$delivery_region_use = $template_infos[delivery_region_use]; //도서산간 배송정책 사용유무

			if( $delivery_region_use == '1'){	//무료배소상품정책 = 2 이고 도서산간배송비 정책 = 1 일경우 추가배송비 부여한다.
				$sql = "select * from shop_product_region_delivery where region_delivery_type = 1 and dt_ix='".$dt_ix."' and product_sell_type = '".$template_infos[product_sell_type]."' order by prd_ix ASC";
				//echo nl2br($sql)."<br><br>";
				$master_db->query($sql);
				$data_array = $master_db->fetchall();

				for($j=0;$j<count($data_array);$j++){
					if(strpos($addr,$data_array[$j][region_name_text]) !== false){
						$delivery_region_price = $data_array[$j][region_name_price];
						break;
					}
				}
			}

		}
		//도서산간 배송비 관련 끝 2014-05-19 이학봉

		$delivery_price_array[$i] = $delivery_price + $delivery_region_price;

		unset($delivery_price);
	}

	//echo "$delivery_product_policy"."<br>";
	if(is_array($delivery_price_array)){	//임시처리
		/*무료배송 배송비 조건이 잇을경우 해당 배송비는 전체 무료로 처리함 시작 2014-05-19 이학봉*/
		if(array_key_exists('99',$delivery_price_array)){
			unset($delivery_price_array); //배열 초기화후 0으로 다시 넣어준다.
			$delivery_price_array[0] = '0';
		}
		/*무료배송 배송비 조건이 잇을경우 해당 배송비는 전체 무료로 처리함 끝 2014-05-19 이학봉*/

		if($delivery_product_policy == '1'){	// 1: 큰배송비 2:적은배송비
			$pos = array_search(max($delivery_price_array),$delivery_price_array);	//max
		}else{
			$pos = array_search(min($delivery_price_array),$delivery_price_array);	//min
		}
	}

		return $delivery_price_array[$pos];
	}

}

function PorudctDeliveryPayMethod($pid){

	global $layout_config,$user,$shop_product_type,$sns_product_type;
	global $master_db;

	$sql = "select
			dt.*,
			p.delivery_type
		from
			shop_product as p
			inner join shop_product_delivery as pd on (p.id = pd.pid)
			inner join shop_delivery_template as dt on (pd.dt_ix = dt.dt_ix)
		where
			p.id = '".$pid."' and pd.pid = '".$pid."'
			and dt.product_sell_type = 'R'
			limit 0,1";

	$master_db->query($sql);
	$master_db->fetch();

	if($master_db->dt[delivery_basic_policy] == '2'){
		$delivery_basic_policy = '2';
	}else{
		$delivery_basic_policy = '1';
	}

	return $delivery_basic_policy;
}

function getProductCategoryRate($pid,$cid,$is_whole='R'){

	$db = new Database;

	if($pid){	//카테고리가 잇을경우 실행

		$sql = "select
					cd.*,
					c.cid,
					c.depth
				from
					shop_product as p
					inner join shop_product_relation as pr on (p.id = pr.pid and basic = '1')
					inner join shop_category_info as c on (pr.cid = c.cid)
					left join shop_category_discount as cd on (c.cid = cd.cid)
				where
					p.id = '".$pid."'";

		$db->query($sql);
		$discount_info =$db->fetch();

		if($discount_info[is_use] == '3'){	//개별
			$cid = $discount_info[cid];
		}else{	//상위
			//$cid = substr($discount_info[cid],0,3)."000000000000";
			for($i=$discount_info[depth]-1;$i>=0;$i--){

				$cid = substr($discount_info[cid],0,($i=='0'?'3':($i+1)*3));
				switch(strlen($cid)){
					case '3':
						$ori_cid = $cid."000000000000";
					break;
					case '6':
						$ori_cid = $cid."000000000";
					break;
					case '9':
						$ori_cid = $cid."000000";
					break;
					case '12':
						$ori_cid = $cid."000";
					break;
					case '15':
						$ori_cid = $cid;
					break;
				}

				$sql = "select
							*
						from
							shop_category_discount
						where
							cid = '".$ori_cid."'
							order by cd_ix ASC limit 0,1";

				$db->query($sql);
				$db->fetch();

				if($db->dt[is_use] == '3'){		//개별로 지정되엇을경우 현재 분류코드로 지정
					$cid = $ori_cid;
					break;
				}else{	//상위		-> 상위로 지정되엇을경우 상위카테고리로 넘겨줌
					continue;
				}
			}
		}

		$sql = "select
				*
			from
				shop_category_discount
			where
				cid = '".$cid."'
				order by cd_ix ASC limit 0,1";

		$db->query($sql);
		$db->fetch();

		if($db->total > 0){
			if($is_whole == 'W'){
				return $db->dt[wholesale_dc_rate];
			}else{
				return $db->dt[dc_rate];
			}
		}else{
			return '0';
		}

	}

}

function GetProductIcons($icon_list){	//해당상품의 선택한 아이콘 불러오기 2014-05-27 이학봉

	$icon_list = explode(";",$icon_list);

	if(count($icon_list) >0 ){
		for($i=0;$i<count($icon_list);$i++){
			$Contents .=	"<img src=\"".$_SESSION["admin_config"][mall_data_root]."/images/icon/".$icon_list[$i][idx].".gif\" align=absmiddle style=vertical-align:middle>&nbsp;&nbsp;";
		}
	}

	return $Contents;
}

function GetProductSns($sns_btn){	//해당상품의 선택한 SNS 불러오기 2014-05-27 이학봉

	if(!is_array($sns_btn)){
		return false;
	}

	$sns_btn = unserialize($sns_btn);//

	if(in_array('facebook',$sns_btn)){
		$Contents .= " 페이스북 &nbsp;";
	}

	if(in_array('twitter',$sns_btn)){
		$Contents .= " 트위터 &nbsp;";
	}
	if(in_array('me2day',$sns_btn)){
		$Contents .= " 미투데이 &nbsp;";
	}
	if(in_array('yozm',$sns_btn)){
		$Contents .= " 요즘 &nbsp;";
	}

	return $Contents;
}

function GetProductViralInfo($pid){
	$db = new Database;

	$sql = "select * from shop_product_viralinfo where pid = '".$pid."' order by vi_ix asc ";
	$db->query($sql);
	$virals = $db->fetchall();

	for($i=0;$i < count($virals);$i++){

		$Contents .= $virals[$i][viral_name]." : ".$virals[$i][viral_url]." : ".$virals[$i][viral_desc]."<br>";
	}

	return $Contents;
}


function orderExcelTemplateSelect($oet_type="",$property=""){
	$db = new Database;

	if($oet_type!=""){
		$where = " and oet_type in ('A','".$oet_type."') ";
	}

	$sql = "select * from shop_order_excel_template where  (company_id = '".$_SESSION["admininfo"]["company_id"]."' and charger_ix='' ) or charger_ix ='".$_SESSION["admininfo"]["charger_ix"]."'  ";
	$db->query($sql);
	$info = $db->fetchall();

	$return ="
	<select name='oet_ix' id='oet_ix' ".$property." title='엑셀양식' >";

	if($oet_type!=""){
		$return .="<option value=''>엑셀양식선택</option>";
	}else{
		$return .="<option value=''>--새로운 양식 생성--</option>";
	}

	for($i=0;$i < count($info);$i++){
		$return .= "<option value='".$info[$i][oet_ix]."' >".$info[$i][oet_name]."</option>";
	}

	$return .="
	</select> ";

	return $return;
}

function ctiSuccessType($type){
	switch ($type){
		case "A" : $stype = "성공";
			break;
		case "B" : $stype = "통화중";
			break;
		case "C" : $stype = "연결포기";
			break;
		case "N" : $stype = "부재중";
			break;
		case ""  : $stype = "-";
			break;

	}
	return $stype;
}

function ctiInOutType($type){
	switch ($type){
		case "10" : $ctype = "인입";
			break;
		case "11" : $ctype = "인입돌려받음";
			break;
		case "20" : $ctype = "발신";
			break;
		case "21" : $ctype = "발신돌려받음";
			break;
		case ""  : $stype = "-";
			break;

	}
	return $ctype;
}

function align_tel($telNo) {

	$telNo = preg_replace('/[^\d\n]+/', '', $telNo);
	if(substr($telNo,0,1)!="0" && strlen($telNo)>8) $telNo = "0".$telNo;
	$Pn3 = substr($telNo,-4);
	if(substr($telNo,0,2)=="01") $Pn1 =  substr($telNo,0,3);
	elseif(substr($telNo,0,2)=="02") $Pn1 =  substr($telNo,0,2);
	//elseif(substr($telNo,0,1)=="0") $Pn1 =  substr($telNo,0,3);
	$Pn2 = substr($telNo,strlen($Pn1),-4);
	if(!$Pn1) return $Pn2."-".$Pn3;
	else return $Pn1."-".$Pn2."-".$Pn3;

}

function SetLikeCategory($cid){
	$db = new Database;
	$sql = "select depth from shop_category_info where cid = '".$cid."' ";
	$db->query($sql);
	$db->fetch();

	switch ($db->dt[depth]){
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
			$cut_num = 12;
			break;
	}

	$this_cid = substr($cid,0,$cut_num);

	return $this_cid;

}


function ProductMandtory($company_id,$mi_ix,$select_id='mi_ix',$input_id='mi_ix',$input_name='mandatory_name',$type='list'){		//상품정보고시 팝업 2014-07-13 이학봉

	global $admininfo, $HTTP_URL;
	global $admin_config;

	$mdb = new Database;
	$db = new Database;

	$sql = "select
				*
			from
				shop_mandatory_info
			where
				1
				and mi_ix = '".$mi_ix."'
				order by mi_ix ASC";
	$mdb->query($sql);
	$mdb->fetch();
	$mandatory_name = $db->dt[mandatory_name];

	$mstring =	"
			<table cellpadding=0 cellspacing=0>
			<tr>
				<td>
					<input type=hidden class='textbox' name='".$input_id."' id='".$input_id."'  value='".$mi_ix."' title='상품고시정보코드'></td>
				<td>
					<input type=text class='textbox point_color' name='".$input_name."' id='".$input_name."' value='".$mandatory_name."' onclick=\"ShowModalWindow('../product/search_mandatory.php?code=".$db->dt[code]."&select_id=$select_id&input_id=$input_id&input_name=$input_name&type=$type',600,550,'seller_search')\"   style='width:140px;' readonly>
				</td>
				<td style='padding-left:5px;'>
					<img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"ShowModalWindow('../product/search_mandatory.php?code=".$db->dt[code]."&select_id=$select_id&input_id=$input_id&input_name=$input_name&type=$type',600,550,'seller_search')\"  style='cursor:pointer;'>
				</td>
				<td>
					<img src='../images/btn_x.gif' style='cursor:pointer;margin:0px 3px;' onclick=\"$(this).parent().parent().find('#".$input_id."').val('');$(this).parent().parent().find('#".$input_name."').val('');\">
				</td>
			</tr>
			</table>";

	return $mstring;

}

function getTaxStatusText($com_id, $publish_type, $idx, $data = ""){
	//발행 상태 정보 가져오기
	include($_SERVER["DOCUMENT_ROOT"]."/admin/tax/popbill/common.php");
	//$Taxinvoice = new Taxinvoice();

	$company_number = str_replace('-','',$com_id);
	//$company_number = '2148868761';
	//echo $idx;
	//echo $company_number;
	//echo $publish_type;
	if($publish_type == '1'){
		$type = ENumMgtKeyType::SELL;
	}else if($publish_type == '2'){
		$type = ENumMgtKeyType::BUY;
	}else if($publish_type == '3'){
		$type = ENumMgtKeyType::TRUSTEE;
	}


	try {
		$result = $TaxinvoiceService->GetInfo($company_number,$type,$idx);
		//$result = $TaxinvoiceService->GetInfo($company_number,ENumMgtKeyType::SELL,$idx);
		//print_r($result);
		//echo $result->stateCode;  1: 전송전 2: 전송대기 3: 전송중 4: 전송완료 5: 전송실패
		if($data == 'con_num'){
			$con_num = $result->ntsconfirmNum."|".$result->itemKey;
		}else if($data == 'mail'){
			switch($result->openYN){
				case '1':
				$status_text = 'O';
				break;
				default :
				$status_text = 'X';
				break;
			}
		}else{
			switch($result->stateCode){
				case '100':
					$status_text = '임시저장';
					$data_value = $result->stateCode;
					break;
				case '200':
					$status_text = '승인대기';
					$data_value = $result->stateCode;
					break;
				case '210':
					$status_text = '발행대기';
					$data_value = $result->stateCode;
					break;
				case '300':
				case '310':
					$status_text = '발행완료';
					$data_value = $result->stateCode;
					break;
				case '301':
				case '302':
				case '303':
				case '311':
				case '312':
				case '313':
					$status_text = '국세청 전송중';
					$data_value = $result->stateCode;
					break;
				case '304':
				case '314':
					$status_text = '국세청 전송완료';
					$data_value = $result->stateCode;
					break;
				case '305':
				case '315':
					$status_text = '국세청 전송실패';
					$data_value = $result->stateCode;
					break;
				case '400':
					$status_text = '거부';
					$data_value = $result->stateCode;
					break;
				case '500':
				case '510':
					$status_text = '취소';
					$data_value = $result->stateCode;
					break;
				case '600':
					$status_text = '발행취소';
					$data_value = $result->stateCode;
					break;
				default :
					$status_text = $result->stateCode;
					$data_value = $result->stateCode;
					break;
			}
		}
		if($data == 'index'){
			return $data_value;
		}else if($data == 'con_num'){
			return $con_num;
		}else{
			return $status_text;
		}
	}
	catch(PopbillException $pe) {
		//echo '['.$pe->getCode().'] '.$pe->getMessage();
	}
}
function getPopbillStatusUdate(){
	global $db, $db2;

	$sql = "select * from tax_sales where status = '1' and (send_status = '1' or national_tax_no = '' or popbill_tax_no = '') ";
	$db->query($sql);

	for($i=0 ; $i<$db->total ; $i++){
		$db->fetch($i);

		$com_id = $db->dt[s_company_number];
		$publish_type = $db->dt[publish_type];
		$idx = $db->dt[idx];

		$result = getTaxStatusText($com_id, $publish_type, $idx, 'index');
		//1: 전송전 2: 전송대기 3: 전송중 4: 전송완료 5: 전송실패
		switch($result){
			case '301':
			case '302':
			case '303':
			case '311':
			case '312':
			case '313':
				$sql = "update tax_sales set send_status = '3' where idx = '$idx' ";
				$db2->query($sql);
				break;
			case '304':
			case '314':
				$sql = "update tax_sales set send_status = '4' where idx = '$idx' ";
				$db2->query($sql);
				break;
			case '305':
			case '315':
				$sql = "update tax_sales set send_status = '5' where idx = '$idx' ";
				$db2->query($sql);
				break;
			default :

				break;
		}

		$ntscon_num = getTaxStatusText($com_id, $publish_type, $idx, 'con_num');
		//print_r($ntscon_num);

		$ntscon_num = explode('|',$ntscon_num);

		$sql = "update tax_sales set popbill_status = '$result' , national_tax_no = '$ntscon_num[0]' , popbill_tax_no = '$ntscon_num[1]'  where idx = '$idx' ";
		$db2->query($sql);

	}
}
function getPopbillJoinMember($code){
	global $db;
	$sql = "select
				ccd.com_number,
				ccd.com_ceo,
				ccd.com_name,
				ccd.com_addr1,
				ccd.com_addr2,
				ccd.com_zip,
				ccd.com_business_status,
				ccd.com_business_category,
				ccd.tax_person_name,
				ccd.tax_person_mail,
				ccd.tax_person_phone,
				cu.id

			from
				common_company_detail as ccd
				inner join common_user as cu on (ccd.company_id = cu.company_id)
			where
				cu.code = '".$code."' ";

	$db->query($sql);
	$db->fetch();

	include_once($_SERVER["DOCUMENT_ROOT"]."/admin/tax/popbill/common.php");

	$joinForm = new JoinForm ();

	$com_number = str_replace('-','',$db->dt[com_number]);
	$addr = $db->dt[com_addr1].$db->dt[com_addr2];
	$tax_person_phone = str_replace('-','',$db->dt[tax_person_phone]);
	$pw = $db->dt[id].'!@#$';

	$joinForm->LinkID 		= $LinkID;
	$joinForm->CorpNum 		= $com_number;
	$joinForm->CEOName 		= $db->dt[com_ceo];
	$joinForm->CorpName 	= $db->dt[com_name];
	$joinForm->Addr			= $addr;
	$joinForm->ZipCode		= $db->dt[com_zip];
	$joinForm->BizType		= $db->dt[com_business_status];
	$joinForm->BizClass		= $db->dt[com_business_category];
	$joinForm->ContactName	= $db->dt[tax_person_name];
	$joinForm->ContactEmail	= $db->dt[tax_person_mail];
	$joinForm->ContactTEL	= $tax_person_phone;
	$joinForm->ID			= $db->dt[id];
	$joinForm->PWD			= $pw;

	try
	{
		$result = $TaxinvoiceService->JoinMember($joinForm);
		return  $result;
		//return true;
	}
	catch(PopbillException $pe) {
		echo '['.$pe->getCode().'] '.$pe->getMessage();
		return true;
	}

}

function InsertDpsDelivery($param_details = array(),$send_count){	//dps 연동 함수 2014-07-18 이학봉

	$date = date("Y-m-d");

	$dps_db = new Database;
	$dps_db->set_change_db("daiso_dps");

	/*
	$date = date('Y-m-d');
	$sql = "select max(send_count) as send_count from shop_dps_info where regdate between '".$date." 00:00:00' and '".$date." 23:59:59'";
	$dps_db->query($sql);
	$dps_db->fetch();
	$send_count = ($dps_db->dt[send_count] + 1);
	*/
	$sql = "select * from shop_dps_info where od_ix = '".$param_details[od_ix]."'";
	$dps_db->query($sql);
	$dps_db->fetch();


	if($dps_db->total > 0){


	}else{
		$sql = "insert into shop_dps_info set
					regdate = NOW(),
					send_count = '".$send_count."',
					oid = '".$param_details[oid]."',
					ori_oid = '".$param_details[ori_oid]."',
					od_ix = '".$param_details[od_ix]."',
					order_from = '".$param_details[order_from]."',
					gcode = '".$param_details[gcode]."',
					gid = '".$param_details[gid]."',
					unit = '".$param_details[unit]."',
					pname = '".$param_details[pname]."',
					standard = '".$param_details[standard]."',
					options = '".$param_details[options]."',
					stock = '".$param_details[stock]."',
					pcnt = '".$param_details[pcnt]."',
					account_price = '".$param_details[account_price]."',
					delivery_price = '".$param_details[delivery_price]."',
					delivery_company = '".$param_details[delivery_company]."',
					deliverycode = '".$param_details[delivery_code]."',
					rname = '".$param_details[rname]."',
					rtel = '".$param_details[rtel]."',
					rmobile = '".$param_details[rmobile]."',
					rzipcode = '".$param_details[rzipcode]."',
					raddr = '".$param_details[raddr]."',
					order_date = '".$param_details[order_date]."',
					da_date = '".$param_details[da_date]."',
					dc_date = '".$param_details[dc_date]."',
					df_date = '".$param_details[df_date]."',
					dps_send_date = '".$param_details[dps_send_date]."',
					dps_status = '".$param_details[dp_status]."',
					dps_is_use = '".$param_details[dps_is_use]."',
					pid = '".$param_details[pid]."',
					user_code = '".$param_details[user_code]."',
					charger = '".$param_details[charger]."',
					charger_ix = '".$param_details[charger_ix]."',
					ps_ix = '".$param_details[ps_ix]."',

					now_company_id = '".$param_details[now_company_id]."',
					now_pi_ix = '".$param_details[now_pi_ix]."',
					now_ps_ix = '".$param_details[now_ps_ix]."',

					move_company_id = '".$param_details[move_company_id]."',
					move_pi_ix = '".$param_details[move_pi_ix]."',
					move_ps_ix = '".$param_details[move_ps_ix]."',

					gname = '".$param_details[gname]."',
					delivery_msg = '".$param_details[delivery_msg]."',
					dps_send_type = 'I',
					dcprice = '".$param_details[dcprice]."',
					pt_dcprice = '".$param_details[pt_dcprice]."'
					";
			//echo nl2br($sql)."<br><br>";

			$dps_db->query($sql);
	}

}
function GetCertificateExpireDate($com_number){

	$com_number = str_replace('-','',$com_number);
	//$com_number = '2148868761';

	include_once($_SERVER["DOCUMENT_ROOT"]."/admin/tax/popbill/common.php");
try {
	$result = $TaxinvoiceService->GetCertificateExpireDate($com_number);
	if($result){
		$year = substr($result,0,4);
		$month = substr($result,4,2);
		$day = substr($result,6,2);


		$date = substr($result,0,8);
		$todate = date("Ymd", time());

		$ddy = ( strtotime($date) - strtotime($todate) ) / 86400;

		$ExpireDate = "공인인증서 만료일 : ".$year." 년 ".$month." 월 ".$day." 일 <span style='color:blue'>(".$ddy.")</span> 일 남음";
	}else{
		$ExpireDate = "<b class='red'>공인인증서를 등록해 주세요 -> </b>";
	}
	//echo $result;
	return $ExpireDate;
}
catch(PopbillException $pe) {
	return $pe->getMessage();
}
}


/**
 * 변수 기본값 할당
 * @param mixed $value
 * @param mixed $empty_return
 *
 * @return mixed
 */
function element($value,$empty_return){
	if(empty($value)){
		$value = $empty_return;
	}
	return $value;
}


//품목별 재고수량 불러오기 (출고보관장소 제외) 2014-07-30 이학봉
function GetGoodsStock($gid){
	global $db;

	$sql = "select
				sum(stock) as stock
			from
				inventory_product_stockinfo
			where
				ps_ix != '2'
				and gid = '".$gid."'";
	$db->query($sql);
	$db->fetch();

	$stock = $db->dt[stock];

	return $stock;

}

//상품리스트 (승인대기) 상태별 메세지 불러오기 2014-10-21 이학봉
function GetProductStateMsg($state,$pid){

	if(!$pid){
		return false;
	}

	$db = new Database;
	$sql = "select state_msg from shop_product_state_history where pid = '".$pid."' and state = '".$state."' order by  regdate DESC limit 0,1";

	$db->query($sql);
	$db->fetch();

	return ($db->dt[state_msg] !=""?"( ".$db->dt[state_msg]." )":"");

}

//상품리스트 (승인대기) 상태별 메세지 불러오기 2014-10-21 이학봉
function customOptionDivDivision($option_div){
	$rt = array();
	list($rt['color'],$rt['size']) = explode(":",$option_div);
	return $rt;
}

/*제휴사 연동 히스토리 정보 2015.12.24 JK*/
function sellertool_regist_history_Text($pid){

	if(!$pid){
		return false;
	}
	$pid = str_pad($pid,"10","0",STR_PAD_LEFT);
	$db = new Database;

	$sql = "select 
			sl.*
			from
				sellertool_log sl
			where
				sl.pid = '".$pid."'
				order by sl.regist_date ASC";
	$db->query($sql);
	$data_array = $db->fetchall();

	for($i=0;$i<count($data_array);$i++){
		$history_text .= $data_array[$i][site_code]." ".$data_array[$i][regist_date]." 제휴사코드 : ".$data_array[$i][result_pno]." 연동메시지 : ".$data_array[$i][result_msg]." \n";
	}

	return $history_text;
}

/*제휴사 연동 single update 처리 관련 2016.02.18 JK*/
function sellertool_single_update($site_code, $id, $type){
	include_once($_SERVER["DOCUMENT_ROOT"]."/admin/openapi/openapi.lib.php");
	include_once($_SERVER["DOCUMENT_ROOT"]."/admin/sellertool/sellertool.lib.php");
	include_once($_SERVER["DOCUMENT_ROOT"]."/include/global_util.php");
	include_once($_SERVER["DOCUMENT_ROOT"]."/include/lib.function.php");
	include_once($_SERVER["DOCUMENT_ROOT"]."/admin/include/admin.util.php");

	//30분마다!


	$OAL = new OpenAPI($site_code);

	if($site_code == 'interpark_api'){
		switch ($type) {
			case 'regist':
				$resulte = $OAL->lib->registGoods($id,'');
				break;
			case 'sold_out':
				$resulte = $OAL->lib->registGoods($id,'','sold_out');
				break;
			case 'stock':
				$resulte = $OAL->lib->modifyStock($id);
				break;
		}
	}else{
		if(method_exists($OAL->lib,'registGoods')){
			$resulte = $OAL->lib->registGoods($id);
		}
	}
	
}


if(!function_exists('getStandardCategoryPathByAdmin')){
        function getStandardCategoryPathByAdmin($cid, $depth='-1'){
                global $user;
                $tb = " standard_category_info ";
                if($cid == "" || strlen($cid) != '15'){
                        return "전체";
                }
                $mdb = new Database;

                if($depth == '0'){
                        $sql = "select * from ".$tb." where depth = 0 and cid LIKE '".substr($cid,0,3)."%' order by depth asc";
                }else if($depth == '1'){
                        $sql = "select * from ".$tb." where depth <= 1 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%'))  order by depth asc";
                }else if($depth == '2'){
                        $sql = "select * from ".$tb." where depth <= 2 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%'))  order by depth asc";
                }else if($depth == '3'){
                        $sql = "select * from ".$tb." where depth <= 3 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0
,12)."%'))  order by depth asc";
                }else if($depth == '4'){
                        $sql = "select * from ".$tb." where depth <= 4 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0
,12)."%') or (depth = 4 and cid LIKE '".substr($cid,0,15)."%'))  order by depth asc";
                }else{
                        $sql = "select * from ".$tb." where depth <= 4 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0
,12)."%') or (depth = 4 and cid LIKE '".substr($cid,0,15)."%'))  order by depth asc";
                        return "전체";
                }
                //echo "<!-- {$sql} -->";
                $mdb->query($sql);

                for($i=0;$i < $mdb->total;$i++){
                        $mdb->fetch($i);

                        if($i == 0){
                                $mstring .= $mdb->dt[cname];
                        }else{
                                $mstring .= " > ".$mdb->dt[cname];
                        }
                }
                return $mstring;
        }
}

function getNationInfo($nation_code="" , $property="" , $return_type="select"){
	global $admininfo;
	$mdb = new MySQL;
	$mdb->query("SELECT * FROM global_nation where disp = 1 ");
	$nations = $mdb->fetchall("object");

	if($return_type=="select"){
		$mstring = "<select name='nation_code' id='nation_code' $property validation='true' title='국가선택'>";
		$mstring .= "<option value=''>국가선택</option>";
		for($i=0;$i < count($nations);$i++){
		$mstring .= "<option value='".$nations[$i][nation_code]."' ".($nations[$i][nation_code] == $nation_code ? "selected":"").">".$nations[$i][nation_name]."</option>";
		}
		 
		$mstring .= "</select>";
	}elseif($return_type=="array"){
		$mstring = $nations;
	}

	return $mstring;
 
}

function getStrpos($check_text="", $str_text="", $checked=""){
	if(strpos($str_text, $check_text) === false){
		$checked = '';
	}
	return $checked;
}

function SearchArea($sido_name,$gugun_name,$basic_sido='',$basic_gugun=''){
	$db = new database();

    $sql = "select * from sido_data order by ix asc ";
    $db->query($sql);
    $sido_array = $db->fetchall();

    $sql = "select * from gugun_data order by gugun asc ";
    $db->query($sql);
    $gugun_array = $db->fetchall();

	$html = "
    <select name='".$sido_name."' id='".$sido_name."' onchange=\"change_gugun()\">";
	$html .= "<option value=''>시/도</option>";
	foreach($sido_array as $sido){
        $html .= "
        <option value='".$sido['sido_shot']."' sido='".$sido['code']."' ".($sido[sido_shot] == $basic_sido ? "selected":"").">".$sido['sido']."</option>
        ";
	}
    $html .= "		
	</select>
	<select name='".$gugun_name."' id='".$gugun_name."'>
		<option value=''>구/군</option>";
	foreach($gugun_array as $gugun){
        $html .= "
        <option class='gugun_area ".$gugun['code']."' value='".$gugun['gugun']."' gugun='".$gugun['code']."' ".($gugun['gugun'] == $basic_gugun ? "selected":"")." style='display:none;'>".$gugun['gugun']."</option>
        ";
	}
    $html .= "		
	</select>";

	$html .="
	<script> 
	$(document).ready(function(){
	    change_gugun();
	});
		function change_gugun(){
		    var sido = $('#$sido_name option:selected').attr('sido');
		    $('#$gugun_name').val('');
		   	$('.gugun_area').hide();
		  	$('.'+sido).show();
		}
	</script>
	";

	return $html;
}

function getAuthTemplet_list($selected="" , $admin_level=9){
    global $admininfo;
    $mdb = new Database;

    if($_SESSION["admininfo"][admin_id] != "forbiz"){
        $where = " and auth_templet_ix != '1' ";
    }

    if($_SESSION["admininfo"][mall_type] == "F" || $_SESSION["admininfo"][mall_type] == "R"){
        $sql = 	"SELECT *
			FROM admin_auth_templet
			where disp=1 and auth_templet_level <= $admin_level and use_soho = 'Y' $where ";
    }else if($_SESSION["admininfo"][mall_type] == "O"){
        $sql = 	"SELECT *
			FROM admin_auth_templet
			where disp=1 and auth_templet_level <= $admin_level and use_openmarket = 'Y' $where ";
    }else if($_SESSION["admininfo"][mall_type] == "B"){
        $sql = 	"SELECT *
			FROM admin_auth_templet
			where disp=1 and auth_templet_level <= $admin_level and use_biz = 'Y' $where ";
    }else{
        $sql = 	"SELECT *
				FROM admin_auth_templet
				where disp=1 and auth_templet_level <= $admin_level $where  ";
    }

    //echo $sql;
    $mdb->query($sql);
    if($mdb->total){


        for($i=0;$i < $mdb->total;$i++){
            $mdb->fetch($i);
            if($mdb->dt[auth_templet_ix] == $selected){
                $mstring = $mdb->dt[auth_templet_name];
            }
        }

    }

    return $mstring;
}

function getQnaDivName($bbs_div){
	global $mdb;

	$sql = "select div_name from shop_product_qna_div where ix='".$bbs_div."'";
	$mdb->query($sql);
	$mdb->fetch();

	return $mdb->dt[div_name];
}

function check_currency_unit($mall_ix){
	$db = new database;

	$sql = "select currency_unit from ".TBL_SHOP_SHOPINFO." where mall_div in ('B') and mall_ix = '".$mall_ix."' ";
	$db->query($sql);
	$db->fetch();
	$currency_unit = $db->dt['currency_unit'];
	if($currency_unit){
		return $currency_unit;
	}else{
		return "KRW";
	}
}

function fbEncrypt($string){
    include $_SERVER["DOCUMENT_ROOT"]."/admin/class/FbEncrypt.class.php";
    $encrypt = new FbEncrypt('180d7d18b8b53e9dba898cc5a692dbb3');

    $encode =  $encrypt->encode($string);

    return $encode;
}

function getFileExtension($file_name){

    $filename = strtolower($file_name['name']);
    $fileInfo = pathinfo($filename);

    return $fileInfo['extension'];
}
?>
