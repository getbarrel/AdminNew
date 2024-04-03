<?
// 기간검색 변수
$vdate = date("Ymd", time());
$today = date("Y-m-d", time());
$vyesterday = date("Y-m-d", time()+84600);
$voneweeklater = date("Y-m-d", time()+84600*7);
$vtwoweeklater = date("Y-m-d", time()+84600*14);
$vfourweeklater = date("Y-m-d", time()+84600*28);
$vyesterday = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24);
//$voneweeklater = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*7);
$v15later = date("Y-m-d", time()+84600*15);;//date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*15);
//$vfourweeklater = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*28);
$vonemonthlater = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2)+1,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v2monthlater = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2)+2,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v3monthlater = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2)+3,substr($vdate,6,2)+1,substr($vdate,0,4)));
// 전시구분
$__arr_display_div = array("0"=>""
									, "1"=>"메인페이지"
									, "2"=>"분류"
									, "3"=>"상품리스트"
									, "4"=>"프로모션"
									, "5"=>"이벤트/기획전"
									, "6"=>"배너"
									);
$__arr_display_div_code = array(
									  "0"=>""
									, "1"=>"main"
									, "2"=>"cate"
									, "3"=>"goodsview"
									, "4"=>"promo"
									, "5"=>"event"
									, "6"=>"banner"
									);
$__arr_display_div_banner = array(
									  "0"=>""
									, "1"=>"일반 배너"
									, "2"=>"플래쉬 배너"
									, "3"=>"슬라이드 배너"
									);
// radio button 사전설정
$arr_display_status		= array("0"=>"전체", "1"=>"진행예약", "2"=>"진행중", "3"=>"진행완료");	// 진행상태
$arr_display_disp		= array("0"=>"전체","1"=>"전시", "2"=>"미전시");	// 전시유무
$arr_display_div_disp  = array("1"=>"사용", "0"=>"미사용");	// 분류사용 여부

if (!function_exists('makeDisplayDivSelectBox')) {
	// 전시분류목록 select box 생성
	function makeDisplayDivSelectBox($mdb,$display_div, $select_name, $div_ix='', $depth=0,  $display_name = '전체보기', $onchange=''){
		global $admininfo;
		$mdb = new Database;

		if ($div_ix || $depth==0){
			$sql = "SELECT div_ix, div_name FROM ".TBL_SHOP_DISPLAY_DIV." WHERE depth=".$depth." AND disp='1'  AND display_div=".$display_div." order by div_ix asc ";		
			//echo $sql;
			$mdb->query($sql);

			$mstring = "<select name='$select_name' id='$select_name' style='width:110px;font-size:12px;' $onchange>";
			$mstring .= "<option value=''>".$display_name."</option>";
			if($mdb->total){
				for($i=0;$i < $mdb->total;$i++){
					$mdb->fetch($i);
					$mstring .= "<option value='".$mdb->dt[div_ix]."' ".($mdb->dt[div_ix] == $div_ix ? "selected":"").">".$mdb->dt[div_name]."</option>";
				}
			}else{
				$mstring .= "<option value=''>".$msg."</option>";
			}
			$mstring .= "</select>";
		}else{
			$mstring = "<select name='$select_name' id='$select_name' style='width:110px;font-size:11px;'>";
			$mstring .= "<option value=''>".$display_name."</option>";
			$mstring .= "</select>";

		}
		return $mstring;
	}
}

if (!function_exists('makeRadioTag')) {
	// radio tag 생성
	function makeRadioTag($array, $name, $select_value=false, $onclick=false, $key_is_value = false){
		if (is_array($array)){
			foreach ($array as $key => $val){
				if ($key_is_value == "Y"){
					if ($select_value == $val)		$str.="<input name='".$name."' type='radio' value='".$val."' id='".$name."_".$val."' checked><label for='".$name."_".$val."'>&nbsp;".$val."</label>";
					else									$str.="<input name='".$name."' type='radio' value='".$val."' id='".$name."_".$val."' ><label for='".$name."_".$val."'>&nbsp;".$val."</label>";
				}else{
					if ($select_value == $key)		$str.="<input name='".$name."' type='radio' value='".$key."' id='".$name."_".$val."' checked ><label for='".$name."_".$val."'>&nbsp;".$val."</label>";
					else									$str.="<input name='".$name."' type='radio' value='".$key."' id='".$name."_".$val."'  ><label for='".$name."_".$val."'>&nbsp;".$val."</label>";
				}
				$str.="&nbsp;";
			}		
			return $str;
		}
	}
}
if (!function_exists('makeMDSelectBox')) {
	// MD 목록 select box 생성
	function makeMDSelectBox($mdb, $select_name, $md_id='', $load_js=''){
		global $admininfo;
		$mdb = new Database;

		$sql = 	"
					SELECT cmd.code, 
							AES_DECRYPT(UNHEX(cmd.name),'".$mdb->ase_encrypt_key."') as name
						FROM  
							".TBL_COMMON_MEMBER_DETAIL." as cmd  
							inner join ".TBL_SHOP_COMPANY_DUTY." as cd on (cd.cu_ix = cmd.duty)
						WHERE 
							cd.duty_name like '%MD%'
						";
		$mdb->query($sql);
		$total = mysql_num_rows($mdb->result);
		
		
		$mstring = "<select name='".$select_name."' id='".$select_name."' ".$load_js."  style='width:140px;font-size:12px;'>";
		$mstring .= "<option value=''>담당MD</option>";
		if($total){

			for($i=0;$i < $total; $i++){
				$mdb->fetch($i);
				if($mdb->dt[code] == $md_id){
					$mstring .= "<option value='".$mdb->dt[code]."' selected>".$mdb->dt[name]."</option>";
				}else{
					$mstring .= "<option value='".$mdb->dt[code]."'>".$mdb->dt[name]."</option>";
				}
			}

		}
		$mstring .= "</select>";
		
		return $mstring;
	}
}
if (!function_exists('getDisplayDivInfo')) {
	// 전시분류명 조회
	function getDisplayDivInfo($db,$div_ix){
		$mdb = new Database;
		$sql = "SELECT a.*, (select div_name from shop_display_div where div_ix=a.parent_div_ix) as parent_name FROM ".TBL_SHOP_DISPLAY_DIV." a WHERE div_ix='$div_ix' ";
		$mdb->query($sql);
		$mdb->fetch();
		$div_name = $mdb->dt["div_name"];
		if ($mdb->dt["parent_name"]){
			$div_name = $mdb->dt["parent_name"]." > ".$div_name;
		}
		return $div_name;	 
	}
}

if (!function_exists('getBannerDivInfo')) {
// 전시 배너분류명 조회
	function getBannerDivInfo($db,$div_ix){
		$mdb = new Database;
		$sql = "SELECT a.*, (select div_name from shop_banner_div where div_ix=a.parent_div_ix) as parent_name FROM ".TBL_SHOP_BANNER_DIV." a WHERE div_ix='$div_ix' ";
		$mdb->query($sql);
		$mdb->fetch();
		$div_name = $mdb->dt["div_name"];
		if ($mdb->dt["parent_name"]){
			$div_name = $mdb->dt["parent_name"]." > ".$div_name;
		}
		return $div_name;	 
	}
}

if (!function_exists('getNameForCode')) {
// code 로 이름 조회
	function getNameForCode($mdb, $code){
		$mdb = new Database;
		$sql = "SELECT AES_DECRYPT(UNHEX(name),'".$mdb->ase_encrypt_key."') as name
						FROM ".TBL_COMMON_MEMBER_DETAIL." WHERE code='$code' ";
		$mdb->query($sql);
		$mdb->fetch();
		return $mdb->dt["name"];	 
	}
}

if (!function_exists('getStatusForDate')) {
	// 날짜기준 진행상태 체크
	function getStatusForDate($sdate, $edate){
		$sdate = substr($sdate,0,8);
		$edate = substr($edate,0,8);
		$today = date("Ymd");
		if ($today >= $sdate && $today <= $edate){
			$status = "<font color='red'>진행중</font>";
		}else if ($today < $sdate){
			$status = "<font color='blue'>예약</font>";
		}else if ($today > $sdate){
			$status = "완료";
		}else{
			$status = "";
		}
		return $status;
	}
}

if (!function_exists('getDateList')) {
	// 12자리 날짜,시간,분 목록표시용 처리
	function getDateList($date){
		$date = substr($date,0,4)."-".substr($date,4,2)."-".substr($date,6,2)." ".substr($date,8,2).":".substr($date,10,2);
		return $date;
	}
}

if (!function_exists('getMenuPath')) {
	// admin_menus 테이블의 menu_path 조회
	function getMenuPath($mdb, $url){
		$mdb = new Database;
		$sql = "SELECT menu_path FROM admin_menus WHERE menu_link='$url' ";
		$mdb->query($sql);
		$mdb->fetch();
		return $mdb->dt["menu_path"];	 
	}
}

if (!function_exists('makeBannerDivSelectBox')) {
	// 배너분류목록 select box 생성
	function makeBannerDivSelectBox($mdb, $select_name, $div_ix='', $depth=0,  $display_name = '전체보기', $onchange=''){
		global $admininfo;
		$mdb = new Database;

		if ($depth==0){
			$sql = "SELECT div_ix, div_name FROM ".TBL_SHOP_BANNER_DIV." WHERE depth=".$depth." AND disp='1'   order by div_ix asc ";		
			//echo $sql;
			$mdb->query($sql);

			$mstring = "<select name='$select_name' id='$select_name' style='width:110px;font-size:12px;' $onchange>";
			$mstring .= "<option value=''>".$display_name."</option>";
			if($mdb->total){
				for($i=0;$i < $mdb->total;$i++){
					$mdb->fetch($i);
					$mstring .= "<option value='".$mdb->dt[div_ix]."' ".($mdb->dt[div_ix] == $div_ix ? "selected":"").">".$mdb->dt[div_name]."</option>";
				}
			}else{
				$mstring .= "<option value=''>".$msg."</option>";
			}
			$mstring .= "</select>";
		}else{
			$mstring = "<select name='$select_name' id='$select_name' style='width:110px;font-size:12px;'>";
			$mstring .= "<option value=''>".$display_name."</option>";
			$mstring .= "</select>";

		}
		return $mstring;
	}
}


function getBannerFirstDIV($selected=""){
	global $agent_type;
	$mdb = new Database;

	$sql = 	"SELECT *
			FROM shop_banner_div
			where disp=1 and agent_type = '".$agent_type."'
			ORDER BY div_ix ASC ";

	$mdb->query($sql);

	$mstring = "<select name='banner_page' id='banner_page' onchange=\"loadBannerPosition(this,'banner_position')\">";
	$mstring .= "<option value=''>배너 분류</option>";
	if($mdb->total){


		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			if($mdb->dt[div_ix] == $selected){
				$mstring .= "<option value='".$mdb->dt[div_ix]."' selected>".$mdb->dt[div_name]."</option>";
			}else{
				$mstring .= "<option value='".$mdb->dt[div_ix]."'>".$mdb->dt[div_name]."</option>";
			}
		}

	}
	$mstring .= "</select>";

	return $mstring;
}




function bannerPosition($div_ix, $bp_ix){
	global $admininfo;

	$mdb = new Database;
	$sql = "SELECT * FROM shop_banner_position where disp=1 and div_ix = '$div_ix' ORDER BY vieworder ASC  ";

	$mdb->query($sql);

	$mstring = "<select name='banner_position' id='banner_position'  $property>";

	if($mdb->total){
        $mstring .= "<option value='0'>배너위치 선택</option>";
		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			$mstring .= "<option value='".$mdb->dt[bp_ix]."' ".($mdb->dt[bp_ix] == $bp_ix ? "selected":"").">".htmlspecialchars($mdb->dt[bp_name])."</option>";
		}
	}else{
		$mstring .= "<option value='0'>배너위치 선택</option>";
	}
	$mstring .= "</select>";

	return $mstring;
}
function bannerPositionTab($div_ix, $bp_ix,$cid2){
    global $banner_page,$depth,$mall_ix;
    if(empty($div_ix)){
        return;
    }

    $mdb = new Database;

    $sql = "select view_category from shop_banner_div where div_ix = '".$div_ix."' ";
    $mdb->query($sql);
    $mdb->fetch();
    $view_category = $mdb->dt['view_category'];

    if($view_category == 'Y'){
        $sql = "SELECT * FROM ".TBL_SHOP_CATEGORY_INFO." where depth ='0' and category_type = 'C' and category_use != '0' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 ";
    }else{
        $sql = "SELECT * FROM shop_banner_position where disp=1 and div_ix = '$div_ix' ORDER BY vieworder ASC  ";
    }

//   .getCategoryPathByAdmin($banner_infos[$i][display_cid],4)
    $mdb->query($sql);
    $positions = $mdb->fetchall();

    $tab_position = "";
    if(is_array($positions)){
        foreach($positions as $key=>$val){
            if($view_category == 'Y'){
                /*
                $tab_position .= "
                <table  id='tab_" . ($key) . "' " . ( substr($cid2,0,3) == substr($val[cid],0,3) ? "class='on'" : "") . " >
                    <tr>
                        <th class='box_01'></th>
                        <td class='box_02'><a href='banner.php?banner_page=".$div_ix."&cid2=".$val[cid]."&depth=0'>" . $val[cname] . "</a></td>
                        <th class='box_03'></th>
                    </tr>
                </table>";
                */
            }else {
                $tab_position .= "
                <table  id='tab_" . ($key) . "' " . ($bp_ix == $val[bp_ix] ? "class='on'" : "") . " >
                    <tr>
                        <th class='box_01'></th>
                        <td class='box_02'><a href='banner.php?banner_page=" . $div_ix . "&banner_position=" . $val[bp_ix] . "'>" . $val[bp_name] . "</a></td>
                        <th class='box_03'></th>
                    </tr>
                </table>";
            }
        }
    }

    $mstring= "
    <tr>
		<td align='left' colspan='7' style='padding-bottom:15px;'>";
    if($view_category == 'Y'){
        $mstring .= "
            <form name='search_category'> 
            <input type='hidden' name='cid2' value='".$cid2."'/>
            <input type='hidden' name='depth' value='".$depth."' />
            <input type='hidden' name='banner_page' value='".$banner_page."' />
            <input type='hidden' name='mall_ix' value='".$mall_ix."' />
            <table border=0 cellpadding=0 cellspacing=0>
                <tr>
                    <td style='padding-right:5px;'>".getCategoryList3("대분류", "cid0_1", "onChange=\"loadCategory(this,'cid1_1',2)\" title='대분류' ", 0, $cid2)."</td>
                    <td style='padding-right:5px;'>".getCategoryList3("중분류", "cid1_1", "onChange=\"loadCategory(this,'cid2_1',2)\" title='중분류'", 1, $cid2)."</td>
                    <td style='padding-right:5px;'>".getCategoryList3("소분류", "cid2_1", "onChange=\"loadCategory(this,'cid3_1',2)\" title='소분류'", 2, $cid2)."</td>
                    <td style='padding-right:5px;'>".getCategoryList3("세분류", "cid3_1", "onChange=\"loadCategory(this,'cid2',2)\" title='세분류'", 3, $cid2)."</td>
                    <td><input type='submit' value='검색' /></td>
                </tr>
            </table>
            </form>
        ";
    }else {
        $mstring .= "
			<div class='tab'>
				<table class='s_org_tab' width=100%>
				<col width='*'>
				<col width='100'>
				<tr>
					<td class='tab'>
						" . $tab_position . "
					</td>
				</tr>
				</table>
			</div>";
    }
    $mstring .= "
		</td>
	</tr>
	<script> 
	    function loadCategory(sel,target) {
            var trigger = sel.options[sel.selectedIndex].value;
            var form = sel.form.name;
            var depth = $('select[name='+sel.name+']').attr('depth');      
            if(trigger == ''){
                depth = depth-1;
                trigger = $('select[name=cid'+depth+'_1] :selected').val();
            }
//            alert(trigger)
//            alert(depth)
            window.frames['act'].location.href = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
        
        }
	</script>
    ";

    return $mstring;
}
?>