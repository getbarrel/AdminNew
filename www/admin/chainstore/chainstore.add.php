<?
include("../class/layout.class");
include_once("../store/md.lib.php");
include("../webedit/webedit.lib.php");
if( $admininfo[mall_use_multishop] && $admininfo[mall_div]){
	$menu_name = "등록관리";
}else{
	$menu_name = "거래처 관리";
}

if($info_type == ""){
	$info_type = "basic";
}

$db = new Database;
$db2 = new Database;
$cdb = new Database;


if($admininfo[admin_level] == 9){
	if($company_id == ""){
		$act = "insert";
	}else{
		if($info_type == "basic"){
			$sql = "SELECT * FROM common_company_detail ccd where ccd.company_id = '".$company_id."'";
			$db->query($sql);
			$db->fetch();
		}else if($info_type == "seller_info"){
			$sql = "SELECT * FROM common_seller_detail csd where csd.company_id = '".$company_id."'";
			$db->query($sql);
			$db->fetch();
			$team = $db->dt[team];

			$sql = "SELECT ct.branch, cr.rg_ix, cr.parent_rg_ix FROM common_team ct, common_branch cb, common_region cr where ct.ct_ix = '".$db->dt[team]."' and ct.branch = cb.cb_ix and cb.rg_ix = cr.rg_ix ";
			$db2->query($sql);
			$db2->fetch();

			$branch = $db2->dt[branch];
			$rg_ix = $db2->dt[rg_ix];
			$parent_rg_ix = $db2->dt[parent_rg_ix];

			//echo $branch;

		}else if($info_type == "delivery_info"){
			$sql = "SELECT * FROM common_seller_delivery csd where csd.company_id = '".$company_id."'";
			//echo $sql;
			$db->query($sql);
			$db->fetch();
		}
		//echo $sql;

		$act = "update";
	}
}else if($admininfo[admin_level] == 8){
		$company_id = $admininfo[company_id];

		if($info_type == "basic"){
			$sql = "SELECT * FROM common_user cu , common_company_detail ccd where  cu.company_id = ccd.company_id and ccd.company_id = '".$company_id."'";
		}else if($info_type == "seller_info"){
			$sql = "SELECT * FROM common_seller_detail csd where csd.company_id = '".$company_id."'";
		}else if($info_type == "delivery_info"){
			$sql = "SELECT * FROM common_seller_delivery csd where csd.company_id = '".$company_id."'";
		}

		$db->query($sql);
		$db->fetch();
		$act = "update";

}


//$com_phone = explode("-",$db->dt[com_phone]);
//$com_fax = explode("-",$db->dt[com_fax]);



$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<col width='20%' />
	<col width='30%' />
	<col width='20%' />
	<col width='30%' />
	  <tr>
	    <td align='left' colspan=4> ".GetTitleNavigation("$menu_name", "가맹점 관리 > $menu_name")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=4 style='padding-bottom:20px;'>
	    <div class='tab'>
			<table class='s_org_tab'>
			<col width='550px'>
			<col width='*'>
			<tr>
				<td class='tab'>
					<table id='tab_01' ".(($info_type == "basic" || $info_type == "") ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02'  ><a href='?info_type=basic&company_id=".$company_id."&mmode=$mmode'>사업자 정보</a></td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_02' ".($info_type == "seller_info" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";
						if($company_id == ""){
							$Contents01 .= "<a href=\"javascript:alert('".getTransDiscription(md5($_SERVER["PHP_SELF"]),'N','','',false)."');\">상점정보 </a>";
						}else{
							$Contents01 .= "<a href='?info_type=seller_info&company_id=".$company_id."&mmode=$mmode'>상점정보 </a>";
						}
						$Contents01 .= "
						</td>
						<th class='box_03'></th>
					</tr>
					</table>
					<!--table id='tab_03' ".($info_type == "delivery_info" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";
						if($company_id == ""){
							$Contents01 .= "<a href=\"javascript:alert('".getTransDiscription(md5($_SERVER["PHP_SELF"]),'N','','',false)."');\">배송정책</a>";
						}else{
							$Contents01 .= "<a href='?info_type=delivery_info&company_id=".$company_id."&mmode=$mmode'>배송정책</a>";
						}
						$Contents01 .= "

						</td>
						<th class='box_03'></th>
					</tr>
					</table-->
				</td>
				<td style='text-align:right;vertical-align:bottom;padding:0 0 10px 0'>

				</td>
			</tr>
			</table>
		</div>
	    </td>
	  </tr>
	  ";
if($info_type == "basic" || $info_type == ""){
	$com_zip = explode("-",$db->dt[com_zip]);
$Contents01 .= "
	  <tr>
	    <td align='left' colspan=4 style='padding-bottom:5px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 5px;'><img src='../image/title_head.gif' ><b class=blk> 사업자 정보</b></div>")."</td>
	  </tr>
	  </table>
	  <table width='100%' cellpadding=0 cellspacing=0 class='input_table_box' >
	  <colgroup>
		<col width='20%' />
		<col width='30%' style='padding:0px 0px 0px 10px'/>
		<col width='20%' />
		<col width='30%' style='padding:0px 0px 0px 10px'/>
	  </colgroup>
	  <tr>
		<td class='input_box_title'><b>사업자번호</b></td>
		<td class='input_box_item'>
			<input type=text name='com_number' value='".$db->dt[com_number]."' class='textbox'  style='width:80px' validation='false' title='사업자번호'>
			<div style='display:inline;padding:2px;' class=small>예) XXX-XX-XXXXX</div>
		</td>
		<td class='input_box_title'> <b>기업형태 <img src='".$required3_path."'></b>   </td>
		<td class='input_box_item'>
			<input type='radio' name='com_div' id='com_div_p' value='P' validation=true title='기업형태' ".($db->dt[com_div] == "P" ? "checked":"")."><label for='com_div_p'>개인</label> &nbsp;&nbsp;
			<input type='radio' name='com_div' id='com_div_r' value='R' validation=true title='거래처형태' ".($db->dt[com_div] == "R" ? "checked":"")."><label for='com_div_p'>법인</label>
		</td>
	  </tr>
	  <tr>
	    <td class='input_box_title'> <b>사업자명 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
		<input type=text name='com_name' value='".$db->dt[com_name]."' class='textbox'  style='width:200px' validation='true' title='사업자명'>
		</td>
	    <td class='input_box_title'> <b>업태</b>   </td>
		<td class='input_box_item'><input type=text name='com_business_status' value='".$db->dt[com_business_status]."' class='textbox'  style='width:200px' validation='false' title='업태'></td>
	  </tr>
	  <tr>
	    <td class='input_box_title'> <b>대표자명 <img src='".$required3_path."'></b>   </td>
		<td class='input_box_item'><input type=text name='com_ceo' value='".$db->dt[com_ceo]."' class='textbox'  style='width:200px' validation='true' title='대표자명'></td>
	    <td class='input_box_title' > <b>업종</b>   </td>
		<td class='input_box_item'><input type=text name='com_business_category' value='".$db->dt[com_business_category]."' class='textbox'  style='width:200px' validation='false' title='업종'></td>
	  </tr>
	  <tr>
	    <td class='input_box_title'> <b>대표전화 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			<input type=text class='textbox' name='com_phone' value='".$db->dt[com_phone]."'  style='width:200px' validation='true' title='대표전화' com_numeric=true>
		</td>
	    <td class='input_box_title'> <b>대표팩스</b></td>
		<td class='input_box_item'>
			<input type=text class='textbox' name='com_fax' value='".$db->dt[com_fax]."'  style='width:200px'>
		</td>
	  </tr>
		<tr>
		<td class='input_box_title'><b>통신판매업 번호</b></td>
		<td class='input_box_item'><input type=text name='online_business_number' value='".$db->dt[online_business_number]."' class='textbox'  style='width:200px' validation='false' title='통신판매업 번호'></td>
		<td class='input_box_title'> <b>대표이메일 </b></td>
		<td class='input_box_item'><input type=text name='com_email' value='".$db->dt[com_email]."' class='textbox'  style='width:200px' validation='false' title='대표이메일' email=true></td>
	  </tr>
	  <tr>
	    <td class='input_box_title'> <b>회사 주소</b>    </td>
	    <td class='input_box_item' colspan=3>
	    	<!--".($db->dt[com_zip] == "" ? "":"[".$db->dt[com_zip]."]")." ".$db->dt[com_address]." <input type='checkbox' name='change_address' id='change_address' onclick='ChangeAddress(this)'><label for='change_address'>주소변경</label><br>-->
	    	<div id='input_address_area' ><!--style='display:none;'-->
	    	<table border='0' cellpadding='0' cellspacing='0' style='table-layout:fixed;width:100%'>
				<col width='120px'>
				<col width='*'>
				<tr>
					<td height=26>
						<input type='text' class='textbox' name='com_zip' id='com_zip' size='15' maxlength='15' value='".$db->dt[com_zip]."' readonly>
					</td>
					<td style='padding:1px 0 0 5px;'>
						<img src='../images/".$admininfo["language"]."/btn_search_address.gif' onclick=\"zipcode('4');\" style='cursor:pointer;'>
					</td>
				</tr>
				<tr>
					<td colspan=2 height=26>
						<input type=text name='com_addr1'  id='com_addr1' value='".$db->dt[com_addr1]."' size=50 class='textbox'  style='width:75%'>
					</td>
				</tr>
				<tr>
					<td colspan=2 height=26>
						<input type=text name='com_addr2'  id='com_addr2'  value='".$db->dt[com_addr2]."' size=70 class='textbox'  style='width:450px'> (상세주소)
					</td>
				</tr>
				</table>
	    	</div>
	    	</td>
	  </tr>
		<tr>
			<td class='input_box_title'><b>사업자 인감도장 </b></td>
			<td class='input_box_item' colspan=3>
				<table>
					<tr>
						<td><input type=file name='company_stamp' size=70 class='textbox'  style='width:300px' validation='false' title='사업자 인감도장'></td>
				";
				if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/stamps/stamp_".$company_id.".gif")){
					$Contents01 .= "<td><img src='".$admin_config[mall_data_root]."/images/stamps/stamp_".$company_id.".gif' width=50></td>";
				}
$Contents01 .= "
					</tr>
				</table>
			</td>
	  </tr>
	  <tr>
			<td class='input_box_title'> <b>통장사본 </b></td>
			<td class='input_box_item' colspan=3 >	<input type=file name='bank_file' size=70 class='textbox'  style='width:300px'></td>";
				if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/seller/".$company_id."/bank_file_".$company_id.".gif")){
					$Contents01 .= "<td><img src='".$admin_config[mall_data_root]."/images/seller/".$company_id."/bank_file_".$company_id.".gif'></td> ";
				}
$Contents01 .= "
			</tr>
	   <tr>
			<td class='input_box_title'><b>증빙서류 </b></td>
			<td class='input_box_item' colspan=3 ><input type=file name='ktp_file' size=70 class='textbox'  style='width:300px'></td>";
				if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/seller/".$company_id."/ktp_file_".$company_id.".gif")){
					$Contents01 .= "<td><img src='".$admin_config[mall_data_root]."/images/seller/".$company_id."/ktp_file_".$company_id.".gif'></td> ";
				}
$Contents01 .= "
	  </tr>";
	  if( $admininfo[mall_use_multishop] && $admininfo[mall_div]){
$Contents01 .= "
	  <tr>
	    <td class='input_box_title'> <b>가맹점승인</b>    </td>

	    <td class='input_box_item' colspan=3>
	    	<input type=radio name='seller_auth' id='seller_auth_N' value='N' ".CompareReturnValue("N",$db->dt[seller_auth],"checked")." ".CompareReturnValue("",$db->dt[seller_auth],"checked")."><label for='seller_auth_N'>승인대기</label>
	    	<input type=radio name='seller_auth' id='seller_auth_Y' value='Y'  ".CompareReturnValue("Y",$db->dt[seller_auth],"checked")."><label for='seller_auth_Y'>승인</label>
	    	<input type=radio name='seller_auth' id='seller_auth_X' value='X' ".CompareReturnValue("X",$db->dt[seller_auth],"checked")."><label for='seller_auth_X'>승인거부</label>
	    	 &nbsp;&nbsp;&nbsp;&nbsp;<span class=small style='color:gray'><!--가맹점 승인후에 사용자 등록이 가능합니다. --> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')."</span>
	    </td>
	  </tr>";
	}
$Contents01 .= "
	  </table>";

/*
$Contents01 .= "
	  <table>
	  <tr height=40><td colspan=4 style='padding:10px 0px 50px 20px'><img src='../image/emo_3_15.gif' align=absmiddle>  세금 계산서 발급및 견적서 작성시 정보로 이용됩니다.</td></tr></table>";*/

	  $Contents01 .= getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');
}



if($info_type == "seller_info"){
$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0'  >
		<col width='20%' />
		<col width='30%' />
		<col width='20%' />
		<col width='30%' />
	  <tr>
	    <td align='left' colspan=4 style='padding-bottom:5px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 5px;'><img src='../image/title_head.gif' ><b class=blk> 상점 정보</b></div>")."</td>
	  </tr>
	  </table>
	  <table width='100%' cellpadding=0 cellspacing=0 border='0' class='input_table_box' >
		<col width='20%' />
		<col width='*' />
	  <tr bgcolor=#ffffff height=34>
	    <td class='input_box_title' nowrap> <b>상점 이름 <img src='".$required3_path."'></b>    </td>
	    <td class='input_box_item' colspan=3><input type=text name='shop_name' value='".$db->dt[shop_name]."' class='textbox'  style='width:350px' validation=true title='상점 이름'></td>
	  </tr>
	  <tr bgcolor=#ffffff height=34>
	    <td class='input_box_title' > <b>홈페이지(분양주소)</b>   </td>
	    <td class='input_box_item' colspan=3><input type=text name='homepage' value='".$db->dt[homepage]."' class='textbox'  style='width:350px' validation=true title='홈페이지'></td>
	  </tr>
	  <tr bgcolor=#ffffff height=104>
	    <td class='input_box_title' > <b>상점 설명</b>   </td>
	    <td class='input_box_item' colspan=3><textarea name='shop_desc'  style='width:600px;height:70px;' validation=false title='상점 설명'>".$db->dt[shop_desc]."</textarea></td>
	  </tr>
	  
	  <tr bgcolor=#ffffff height=34>
	    <td class='input_box_title'> <b>상점 로고 </b>  </td>
	    <td class='input_box_item' colspan=3>
	    <input type=file name='shop_logo_img' size=30 class='textbox'  style='width:300px;'> <!--권장 사이즈 305 * 264 --> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'I')."<br>";

if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/shopimg/shop_logo_".$company_id.".gif")){
$Contents01 .= "<img src='".$admin_config[mall_data_root]."/images/shopimg/shop_logo_".$company_id.".gif' style='margin:10px;'> ";
		}

$Contents01 .= "
	    </td>
	  </tr>
	  <tr bgcolor=#ffffff height=34>
	    <td class='input_box_title' nowrap> <b>상점 이미지 </b>  </td>
	    <td class='input_box_item' colspan=3>
	    <input type=file name='shop_img' size=30 class='textbox'  style='width:300px'>  <!--권장 사이즈 305 * 264 --> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'J')."<br>";

	if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/shopimg/shop_".$company_id.".gif")){
$Contents01 .= "<img src='".$admin_config[mall_data_root]."/images/shopimg/shop_".$company_id.".gif' style='margin:10px;'> ";
	}

$Contents01 .= "
	    </td>
	  </tr>
	  <tr bgcolor=#ffffff height=34>
	    <td class='input_box_title' nowrap> <b>상점 썸네일이미지 </b>  </td>
	    <td class='input_box_item' colspan=3>
	    <input type=file name='shop_img_thum' size=30 class='textbox'  style='width:200px'>  <!--권장 사이즈 200 * 200 --> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'J')."<br>";

	if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/shopimg/shop_thum_".$company_id.".gif")){
$Contents01 .= "<img src='".$admin_config[mall_data_root]."/images/shopimg/shop_thum_".$company_id.".gif' style='margin:10px;'> ";
	}

$Contents01 .= "
	    </td>
	  </tr>
	  ";

if($admininfo[mall_type] == "O"){
$Contents01 .= "
	  <tr bgcolor=#ffffff height=34 >
	    <td class='input_box_title'> <b>미니샵 템플릿</b>    </td>
	    <td class='input_box_item' colspan=3 style='padding:0px 0px 0px 10px'>
	     ".SelectDirList("minishop_templet", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/minishop_templet", $db->dt[minishop_templet])."
	    </td>
	  </tr>";

if($_SESSION["admininfo"]["mem_type"]=="S" && $_SESSION["admininfo"]["admin_level"]==8) {
	$txt_text="text";
	$Contents01 .= "<input type='hidden' name='team' value='".$team."' /><input type='hidden' name='md_code' value='".$db->dt[md_code]."' />";
} else {
	$txt_text="";
}
$Contents01 .= "
	  <tr bgcolor=#ffffff height=34 >
	    <td class='input_box_title'> <b>담당 MD</b>    </td>
	    <td colspan=3 style='padding:0px 0px 0px 10px'>
	     ".getRegionInfoSelect('parent_rg_ix', '1차 지역',$parent_rg_ix, $parent_rg_ix, 1, " onChange=\"loadRegion(this,'rg_ix')\" ",$txt_text)."
		 ".getRegionInfoSelect('rg_ix', '2차 지역',$parent_rg_ix, $rg_ix, 2, "validation=false title='지역' onChange=\"loadBranch(this,'branch')\" ",$txt_text)."
		 ".makeBranchSelectBox($cdb,'branch', $rg_ix, $branch, '지사', "validation=false title='지사' onChange=\"loadTeam(this,'team')\" ",$txt_text)."
		 ".makeTeamSelectBox($cdb,'team', $branch,$team,  '팀', "".($db->dt[mem_level] == "11" ? "validation=false":"validation=true")." title='팀' onChange=\"loadSellerManager(this,'md_code')\"  ",$txt_text)."
		 ".getSellerManager($branch, $team, $db->dt[md_code],$txt_text)."
	    </td>
	  </tr>";
}
$Contents01 .= "
	  </table>
	  <table width='100%' cellpadding=0 cellspacing=0 border='0'  >
		<col width='20%' />
	<col width='30%' />
	<col width='20%' />
	<col width='30%' />
	  <tr height=40>
		<td colspan=4 style='padding:10px 0px 50px 20px'>
			<img src='../image/emo_3_15.gif' align=absmiddle> <span class='small'><!--미니샵 및 상점소개 페이지에서 노출되는 정보입니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'C')." </span>
		</td>
	</tr>
	  <tr>
	    <td align='left' colspan=4 style='padding-bottom:5px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 5px;'><img src='../image/title_head.gif' align=absmiddle><b class=blk> 거래은행 / 계좌번호</b></div>")."</td>
	  </tr>
	  </table>
  <table width='100%' cellpadding=0 cellspacing=0 border='0' class='input_table_box' >
	<col width='20%' />
	<col width='30%' />
	<col width='20%' />
	<col width='30%' />
	  <tr bgcolor=#ffffff height=34>
	    <td class='input_box_title'> 예금주    </td>
		<td class='input_box_item' width='30%' ><input type=text name='bank_owner' value='".$db->dt[bank_owner]."' class='textbox'  style='width:200px'></td>
	    <td class='input_box_title'> 거래은행    </td>
		<td class='input_box_item' width='30%' ><input type=text name='bank_name' value='".$db->dt[bank_name]."' class='textbox'  style='width:200px'></td>
	  </tr>
	  <tr bgcolor=#ffffff height=34>
	    <td class='input_box_title'> 계좌번호    </td>
		<td class='input_box_item' colspan=3><input type=text name='bank_number' value='".$db->dt[bank_number]."' class='textbox'  style='width:200px' title='계좌번호' onKeyup='ch_banknum(this)'></td>

	  </tr>";

$Contents01 .= "
	  </table>";
}
$ContentsDesc02 = "
<table width='660' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td align=left style='padding:10px;' class=small>
		<img src='../image/emo_3_15.gif' align=absmiddle> 해당거래처와의 기본 계좌 정보를 입력합니다.
	</td>
</tr>
</table>
";

/*
$Contents03 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' style='table-layout:fixed;'>
	  <col width=18%>
	  <col width=32%>
	  <col width=18%>
	  <col width=32%>
	  <tr>
	    <td align='left' colspan=4> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 5px;'><img src='../image/title_head.gif' align=absmiddle><b> 영업정보</b></div>")."</td>
	  </tr>
	  <tr bgcolor=#ffffff height=34 >
	    <td> <b>대표전화 <img src='".$required3_path."'></b></td><td><input type=text name='phone1' value='".$phone[0]."' maxlength=3 size=3  class='textbox' validation='true' title='대표전화' numeric='true'> - <input type=text name='phone2' value='".$phone[1]."' maxlength=4 size=5 class='textbox' validation='true' title='대표전화' numeric='true'> - <input type=text name='phone3' value='".$phone[2]."' maxlength=4 size=5 class='textbox' validation='true' title='대표전화' numeric='true'></td>
	    <td align=left style='padding:0 0 0 30' > 대표팩스</td><td><input type=text name='fax1' value='".$fax[0]."' maxlength=3 size=3 class='textbox' > - <input type=text name='fax2' value='".$fax[1]."' maxlength=4 size=5 class='textbox' > - <input type=text name='fax3' value='".$fax[2]."' maxlength=4 size=5 class='textbox' ></td>
	  </tr>
	  <tr height=1><td colspan=4 background='../image/dot.gif'></td></tr>
	  <tr bgcolor=#ffffff height=34 >
	    <td> <b>담당자명 <img src='".$required3_path."'></b></td><td><input type=text name='charger' value='".$db->dt[charger]."' class='textbox'  style='width:200px' validation='true' title='담당자명' ></td>
	    <td align=left style='padding:0 0 0 30'> <b>이메일 <img src='".$required3_path."'></b></td><td><input type=text name='charger_email' value='".$db->dt[charger_email]."' class='textbox'  style='width:200px' validation='true' title='담당자명' email=true></td>
	  </tr>
	  <tr height=1><td colspan=4 background='../image/dot.gif'></td></tr>
	  <tr bgcolor=#ffffff height=34 >
	    <td> 홈페이지</td><td><input type=text name='homepage' value='".$db->dt[homepage]."' class='textbox'  style='width:200px'></td>
	    <td align=left style='padding:0 0 0 30'> <b>제품문의 메일 <img src='".$required3_path."'></b></td><td><input type=text name='pqmail' value='".$db->dt[pqmail]."' class='textbox'  style='width:200px' validation='true' title='담당자명' email=true></td>
	  </tr>
	  <tr height=1><td colspan=4 background='../image/dot.gif'></td></tr>
	  <tr bgcolor=#ffffff height=34 >
	    <td> AS 전화</td><td><input type=text name='asphone' value='".$db->dt[asphone]."' class='textbox'  style='width:200px'></td>

	  </tr>
	  <tr height=1><td colspan=4 background='../image/dot.gif'></td></tr>";
if( $admininfo[mall_use_multishop] && $admininfo[mall_div]){
$Contents03 .= "
	  <tr bgcolor=#ffffff height=34 >
	    <td  > 가맹점승인    </td>
	    <td colspan=3 >
	    	<input type=radio name='auth' id='auth_N' value='N' ".CompareReturnValue("N",$db->dt[auth],"checked")." ".CompareReturnValue("",$db->dt[auth],"checked")."><label for='auth_N'>승인대기</label>
	    	<input type=radio name='auth' id='auth_Y' value='Y'  ".CompareReturnValue("Y",$db->dt[auth],"checked")."><label for='auth_Y'>승인</label>
	    	<input type=radio name='auth' id='auth_X' value='X' ".CompareReturnValue("X",$db->dt[auth],"checked")."><label for='auth_X'>승인거부</label>
	    	 &nbsp;&nbsp;&nbsp;&nbsp;<span class=small style='color:gray'>가맹점 승인후에 사용자 등록이 가능합니다. </span>
	    </td>
	  </tr>";
}
$Contents03 .= "
	  <tr height=1><td colspan=4 background='../image/dot.gif'></td></tr>
	  </table><br><br>";
*/
if($info_type == "delivery_info"){

$sql = getTopDeliveryPolicy($db2,"sql");
$db2->query($sql);
$db2->fetch();
$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	 <col width='20%' />
	<col width='30%' />
	<col width='20%' />
	<col width='30%' />
	  <tr>
	    <td align='left' colspan=4 style='padding-bottom:5px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 5px;'><img src='../image/title_head.gif' ><b> 가맹점별 기본 배송정책 및 수수료</b></div>")."</td>
	  </tr>
	  </table>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' class='input_table_box'  >
	<col width='20%' />
	<col width='30%' />
	<col width='20%' />
	<col width='30%' />
";

if($admininfo[admin_level] == 8){
	 $Contents01 .= " <tr bgcolor=#ffffff height=34 >
	    <td class='input_box_title'> 업체정산 수수료</td>
		<td class='input_box_item' colspan='3'><input type='hidden' name='commission' value='".$db->dt[commission]."' class='textbox' style='width:30px;'>".$db->dt[commission]." %</td>
		<!--td class='input_box_title'></td>
		<td class='input_box_item'></td-->
	  </tr>";
}else if($admininfo[admin_level] == 9){

	$Contents01 .= " <tr bgcolor=#ffffff height=34 >
	    <td class='input_box_title'> <b>업체정산 수수료 <img src='".$required3_path."'></b></td>
		<td class='input_box_item' colspan='3'><input type='text' name='commission' value='".$db->dt[commission]."' class='textbox'  style='width:30px' validation='true' title='업체정산 수수료' numeric=true> %</td>
		<!--td class='input_box_title'></td>
		<td class='input_box_item'></td-->
	  </tr>";
}
	$Contents01 .= "
	  <tr bgcolor=#ffffff height=34>
	    <td bgcolor='#efefef' >
			<table cellpadding=0 cellspacing=0 border='0'>
				<tr>
					<td></td>
					<td class='input_box_title'> <b>본사정책 적용유무 <img src='".$required3_path."'></b></td>
				</tr>
			</table>
		</td>
	    <td class='input_box_item' colspan=3>";
if($admininfo[admin_level] == 8){
	if($db->dt[delivery_policy] == "1"){
		$Contents01 .= "<input type=hidden name='delivery_policy' value='1' > 본사 기본배송 정책 사용 ";
	}else{
		$Contents01 .= "<input type=hidden name='delivery_policy' value='2' > 가맹점 배송정책 설정 ";
	}

}else{
	$Contents01 .= "
			<input type=radio name='delivery_policy' value='1' id='delivery_policy_1' onclick=\"deliveryTypeView('1')\" ".(($company_id == "" || $db->dt[delivery_policy] == "1") ? "checked":"")."><label for='delivery_policy_1' >본사 기본배송 정책 사용</label>
			<input type=radio name='delivery_policy' value='2' id='delivery_policy_2' onclick=\"deliveryTypeView('2')\" ".(($db->dt[delivery_policy] == "2"  || $db->dt[delivery_policy] == "") ? "checked":"")."><label for='delivery_policy_2'>가맹점 배송정책 설정</label>";
}
$Contents01 .= "
	    </td>
	  </tr>
	  <tr bgcolor=#ffffff height=34 id='policy_input' style='display:none;'>
	    <td bgcolor='#efefef' >
			<table cellpadding=0 cellspacing=0 border='0'>
				<tr>
					<td></td>
					<td class='input_box_title'> <b>가맹점 배송 정책 <img src='".$required3_path."'></b></td>
				</tr>
			</table>
		</td>
	    <td class='input_box_item' colspan=3>
	     	<input type=radio name='delivery_basic_policy' value='1' id='delivery_basic_policy_1' ".(($company_id == "" || $db->dt[delivery_basic_policy] == "1" || $db->dt[delivery_basic_policy] == "") ? "checked":"")."><label for='delivery_basic_policy_1'>선불</label>
			<input type=radio name='delivery_basic_policy' value='2' id='delivery_basic_policy_2' ".($db->dt[delivery_basic_policy] == "2" ? "checked":"")."><label for='delivery_basic_policy_2'>착불</label>
			<input type=radio name='delivery_basic_policy' value='3' id='delivery_basic_policy_3' ".($db->dt[delivery_basic_policy] == "3" ? "checked":"")."><label for='delivery_basic_policy_3'>무료</label>
	    </td>
	  </tr>
	  <tr bgcolor=#ffffff id='policy_text' height=34>
	    <td bgcolor='#efefef' >
			<table cellpadding=0 cellspacing=0 border='0'>
				<tr>
					<td></td>
					<td class='input_box_title'> <b>가맹점 배송 정책 <img src='".$required3_path."'></b></td>
				</tr>
			</table>
		</td>
	    <td class='input_box_item' colspan=3>";
		if($db2->dt[delivery_basic_policy] == "1"){
			$Contents01 .= "선불";
		}else if($db2->dt[delivery_basic_policy] == "2"){
			$Contents01 .= "착불";
		}else{
			$Contents01 .= "무료";
		}
	   $Contents01 .= "</td>
	  </tr>
	  <tr bgcolor=#ffffff id='price_input' height=34 style='display:none'>
	    <td bgcolor='#efefef' >
			<table cellpadding=0 cellspacing=0 border='0'>
				<tr>
					<td></td>
					<td class='input_box_title'> <b>기본택배비 <img src='".$required3_path."'></b></td>
				</tr>
			</table>
		</td>
		<td colspan=3>
			".getTransDiscription(md5($_SERVER["PHP_SELF"]),'K', $db)."
		</td>
	  </tr>
	  <tr bgcolor=#ffffff id='price_text' height=34>
	    <td bgcolor='#efefef' >
			<table cellpadding=0 cellspacing=0 border='0'>
				<tr>
					<td></td>
					<td class='input_box_title'> <b>기본택배비 <img src='".$required3_path."'></b></td>
				</tr>
			</table>
		</td>
		<td class='input_box_item' colspan=3>".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db2->dt[delivery_freeprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]." 미만 일때 ".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db2->dt[delivery_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]." 부과</td>

	  </tr>
	  <tr bgcolor=#ffffff>
	    <td bgcolor='#efefef'>
			<table cellpadding=0 cellspacing=0 border='0'>
				<tr>
					<td></td>
					<td class='input_box_title'> 택배업체 설정 </td>
				</tr>
			</table>
		</td>
	    <td class='input_box_item' colspan=3>
	    	".deliveryCompanyList($db->dt[delivery_company],"SelectbyAll","")." 귀사가 사용하시는 택배 업체를 선택해주세요
	    </td>
	  </tr>
	  </table>
	  <table width='100%' cellpadding=0 cellspacing=0 border='0'>
		<col width='190' />
		<col width='*' />
	  <tr height=30>
		<td colspan='2'></td>
	  </tr>
	  <tr>
	    <td align='left' colspan=2 style='padding-bottom:5px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 5px;'><img src='../image/title_head.gif' ><b> 가맹점별 부가 배송정책</b></div>")."</td>
	  </tr>
	  </table>
	   <table width='100%' cellpadding=0 cellspacing=0 border='0' class='input_table_box'>
		<col width='20%' />
		<col width='*' />
	  <tr bgcolor=#ffffff id='free_input' style='display:none' height=34>
	    <td bgcolor='#efefef' >
			<table width='100%' cellpadding=0 cellspacing=0 border='0'>
				<tr>
					<td></td>
					<td class='input_box_title'> <b>무료배송상품 정책 <img src='".$required3_path."'></b></td>
				</tr>
			</table>
		</td>
	    <td colspan=3>
	     	<input type=radio name='delivery_free_policy' value='1' id='delivery_free_policy_1'  ".(($company_id == "" || $db->dt[delivery_free_policy] == "1" || $db->dt[delivery_free_policy] == "") ? "checked":"")."><label for='delivery_free_policy_1'><!--구매상품중 무료배송 상품이 있을 때 전체 배송비 무료--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'D')."</label><br>
			<input type=radio name='delivery_free_policy' value='2' id='delivery_free_policy_2'  ".($db->dt[delivery_free_policy] == "2" ? "checked":"")."><label for='delivery_free_policy_2'><!--구매상품중 무료배송 상품이 있을 때 무료배송 상품만 배송비 무료--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'E')."</label>
	    </td>
	  </tr>
	  <tr bgcolor=#ffffff id='free_text' height=34>
	    <td bgcolor='#efefef'>
			<table width='100%' cellpadding=0 cellspacing=0 border='0'>
				<tr>
					<td></td>
					<td class='input_box_title'> <b>무료배송상품 정책 <img src='".$required3_path."'></b></td>
				</tr>
			</table>
		</td>
	    <td class='input_box_item' colspan=3>";
		if($db2->dt[delivery_free_policy] == "1"){
			$Contents01 .= "구매상품중 무료배송 상품이 있을 때 전체 배송비 무료";
		}else if($db2->dt[delivery_free_policy] == "2"){
			$Contents01 .= "구매상품중 무료배송 상품이 있을 때 무료배송 상품만 배송비 무료";
		}
	   $Contents01 .= "</td>
	  </tr>
	  <tr bgcolor=#ffffff id='product_input' style='display:none' height=34>
	    <td bgcolor='#efefef'>
			<table width='100%' cellpadding=0 cellspacing=0 border='0'>
				<tr>
					<td></td>
					<td class='input_box_title'> <b>상품별 배송비 정책 <img src='".$required3_path."'></b></td>
				</tr>
			</table>
		</td>
	    <td class='input_box_item' colspan=3 style='padding:5px 0px 5px 5px'>
			<table border=0 cellpadding=0 cellspacing=0>
				<tr>
					<td><input type=radio name='delivery_product_policy' value='1' id='delivery_product_policy_1'  ".(($db->dt[delivery_product_policy] == "1" || $db->dt[delivery_product_policy] == "") ? "checked":"")."><label for='delivery_product_policy_1'><!--상품 2가지이상 주문했을때 기본배송비 + 상품배송비--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'F')." </label></td>
				</tr>
				<tr>
					<td><input type=radio name='delivery_product_policy' value='2' id='delivery_product_policy_2'  ".($db->dt[delivery_product_policy] == "2" ? "checked":"")."><label for='delivery_product_policy_2'><!--상품 2가지이상 주문했을때 기본배송비와 상품배송비중 큰금액 책정--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'G')." </label></td>
				</tr>
				<Tr>
					<td><input type=radio name='delivery_product_policy' value='3' id='delivery_product_policy_3'  ".($company_id == "" || $db->dt[delivery_product_policy] == "3" ? "checked":"")."><label for='delivery_product_policy_3'><!--상품 2가지이상 주문했을때 기본배송비와 상품배송비중 작은금액 책정--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'H')." </label></td>
				</tr>
			</table>
	    </td>
	  </tr>
	  <tr bgcolor=#ffffff>
	    <td bgcolor='#efefef'>
			<table width='100%' cellpadding=0 cellspacing=0 border='0'>
				<tr>
					<td></td>
					<td class='input_box_title'>지역별 배송비용</td>
				</tr>
			</table>
		</td>
		<td class='input_box_item' colspan=3>
			<table border=0 cellpadding=0 cellspacing=0 width='100%'>
				<tr>
					<td>
						<input type='hidden' id='region_delivery_type_1' name='region_delivery_type' value='1' ><!--label for='region_delivery_type_1'>지역명</label-->
					</td>
				</tr>
				<tr>
					 <td>
					<input type='radio' id='delivery_region_use_1' name='delivery_region_use' value='1' ".CompareReturnValue("1",$db->dt[delivery_region_use],"checked")."><label for='delivery_region_use_1'>사용</label> <input type='radio' id='delivery_region_use_0' name='delivery_region_use' value='0' ".CompareReturnValue("0",$db->dt[delivery_region_use],"checked")."><label for='delivery_region_use_0'>사용안함</label>
					</td>
				</tr>
				<tr height=50>
					<td id='region_name' style='padding:5px 5px 5px 0px;'>
						";
						$db2->query("select * from shop_region_delivery where region_delivery_type = 1 and company_id='".$company_id."'  ");
						if(!$db2->total){
						$Contents01 .= "<table border=0 cellpadding=0 cellspacing=2 width='100%' id='region_name_table_add' class='region_name_table_add list_table_box''>
							<col width='70%'>
							<col width='*'>
							<tr align=center height=30><td class='s_td'>배송지역 입력</td><td class='e_td'>추가배송비</td></tr>
							<tr height=30>
								<td class='list_box_td' >
									<input type='text' class='textbox' name='region_name_text[]' style='width:97%;'>
								</td>
								<td class='list_box_td point' align=center>
									".$currency_display[$admin_config["currency_unit"]]["front"]." <input type='text' class='textbox number' name='region_name_price[]' style='width:55%;'> ".$currency_display[$admin_config["currency_unit"]]["back"]."
									<img src='../images/".$admininfo["language"]."/btn_add2.gif' onclick=\"insertInputBox('region_name_table')\" style='cursor:pointer' align=absmiddle>
									</td>

							</tr>
							</table>
							";
						}else{
							$Contents01 .= "<table border=0 cellpadding=0 cellspacing=2 width='100%' id='region_name_table_add' class='region_name_table_add list_table_box'>
									<col width='70%'>
									<col width='*'>";
							for($i=0;$i<$db2->total;$i++){
								$db2->fetch($i);
								if($i==0){
									$Contents01 .= "
									<tr align=center height=30><td class='s_td'>배송지역 입력</td><td class='e_td'>추가배송비</td></tr>
									<tr height=30 >
										<td class='list_box_td'>
											<input type='hidden' class='textbox' name='rd_ix[]' style='width:98%;' value='".$db2->dt[rd_ix]."'>
											<input type='text' class='textbox' name='region_name_text[]' style='width:98%;' value='".$db2->dt[region_name_text]."'>
										</td>
										<td align=center class='list_box_td'>
											".$currency_display[$admin_config["currency_unit"]]["front"]." <input type='text' class='textbox number' name='region_name_price[]' style='width:55%;' value='".$db2->dt[region_name_price]."'> ".$currency_display[$admin_config["currency_unit"]]["back"]."
											<img src='../images/".$admininfo["language"]."/btn_add2.gif' onclick=\"insertInputBox('region_name_table')\" style='cursor:pointer' align=absmiddle>
										</td>
									</tr>";
								}else{
									$Contents01 .= "
									<tr height=30 >
										<td class='list_box_td'>
											<input type='hidden' class='textbox' name='rd_ix[]' style='width:98%;' value='".$db2->dt[rd_ix]."'>
											<input type='text' class='textbox' name='region_name_text[]' style='width:98%;' value='".$db2->dt[region_name_text]."'>
										</td>
										<td align=center class='list_box_td'>
											".$currency_display[$admin_config["currency_unit"]]["front"]." <input type='text' class='textbox number' name='region_name_price[]' value='".$db2->dt[region_name_price]."' style='width:55%;'> ".$currency_display[$admin_config["currency_unit"]]["back"]."
											<img src='../images/".$admininfo["language"]."/btn_del.gif' onclick=\"$(this).parent().parent().remove();/*del_table('region_name',event);this.parentNode.parentNode.parentNode.removeNode(true);*/\" style='cursor:pointer' align=absmiddle>
										</td>
									</tr>
									";
								}
							
							}
							$Contents01 .= "</table>";
						}
						$Contents01 .= "
						<table border=0 cellpadding=0 cellspacing=2 width='100%' id='region_name_table' style='display:none;' class='region_name_table' disabled='disabled'>
							<col width='70%'>
							<col width='*'>
							<tr height=30>
								<td ><input type='text' class='textbox' name='region_name_text[]' style='width:98%;'></td>
								<td align=center>
									".$currency_display[$admin_config["currency_unit"]]["front"]." <input type='text' class='textbox number' name='region_name_price[]' style='width:55%;'> ".$currency_display[$admin_config["currency_unit"]]["back"]."
									<img src='../images/".$admininfo["language"]."/btn_del.gif' onclick=\"$(this).parent().parent().remove();/*del_table('region_name',event);this.parentNode.parentNode.parentNode.removeNode(true);*/\" style='cursor:pointer' align=absmiddle>
								</td>
							</tr>
						</table>
						<table border=0 cellpadding=0 cellspacing=0 width='100%' >
							<tr>
								<td height=25><span class='small'> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'L')."  </span></td>
								<td><span class='small'> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'M')."  </span></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td><span class='small blu'>  ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'O')."  </span></td>
				</tr>
			</table>
		</td>
	  </tr>
	  <tr bgcolor=#ffffff id='product_text' height=34>
	    <td bgcolor='#efefef'>
			<table width='100%' cellpadding=0 cellspacing=0 border='0'>
				<tr>
					<td></td>
					<td class='input_box_title'> <b>상품별 배송비 정책 <img src='".$required3_path."'></b></td>
				</tr>
			</table>

		</td>
	    <td colspan=3>";
		if($db2->dt[delivery_product_policy] == "1"){
			$Contents01 .= "상품 2가지이상 주문했을때 기본배송비 + 상품배송비";
		}else if($db2->dt[delivery_product_policy] == "2"){
			$Contents01 .= "상품 2가지이상 주문했을때 기본배송비와 상품배송비중 큰금액 책정";
		}else if($db2->dt[delivery_product_policy] == "3"){
			$Contents01 .= "상품 2가지이상 주문했을때 기본배송비와 상품배송비중 작은금액 책정";
		}
	   $Contents01 .= "</td>
	  </tr>
	  <tr bgcolor=#ffffff id='product_text' height=34>
	    <td bgcolor='#efefef'>
			<table width='100%' cellpadding=0 cellspacing=0 border='0'>
				<tr>
					<td></td>
					<td class='input_box_title'> <b>배송 정책 <img src='".$required3_path."'></b></td>
				</tr>
			</table>

		</td>
	    <td class='input_box_item' colspan=3>
		<table cellpadding=3 cellspacing=1  bgcolor=#c0c0c0 border=0 width='100%'>
			<tr bgcolor=#ffffff>
				<td colspan=2 style='padding:0px;'>".WebEdit()."</td>
			</tr>
		</table>
		<textarea name=\"delivery_policy_text\"  style='display:none' >".$db->dt[delivery_policy_text]."</textarea>
			<table width='100%' cellpadding=0 cellspacing=0 border=0>
				<tr >
					<td colspan=2 align=right valign=top style='padding:0px;padding-right:0px;'>
					<a href='javascript:doToggleText();' onMouseOver=\"MM_swapImage('editImage15','','../webedit/image/bt_html_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/bt_html.gif' name='editImage15' width='93' height='23' border='0' id='editImage15'></a>
						  <a href='javascript:doToggleHtml();' onMouseOver=\"MM_swapImage('editImage16','','../webedit/image/bt_source_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/bt_source.gif' name='editImage16' width='93' height='23' border='0' id='editImage16'></a>
					</td>
				</tr>
			</table>
		</tr>
		</td>
	  </tr>

	  </table><br><br>";
}
$ButtonString = "
<table cellpadding=0 cellspacing=0 border='0' >
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></td></tr>
</table>
";

if($company_id != "" && $info_type == "delivery_info"){
$ButtonString .= "<script type='text/javascript'>
	window.onload = function(){
		deliveryTypeView(".$db->dt[delivery_policy].");
		Content_Input();
		Init(document.edit_form);
	}
	function Content_Input(){
		document.edit_form.content.value = document.edit_form.delivery_policy_text.value;
	}
	function SubmitX(frm){
		if(!CheckFormValue(frm)){
			return false;
		}
		frm.content.value = iView.document.body.innerHTML;
		frm.content.value = frm.content.value.replace('<P>&nbsp;</P>','')
		//alert(frm.content.value);
		return true;
	}
</script>
";
}

$Contents = "<table width='100%' border=0>";
if($company_id != "" && $info_type == "delivery_info"){
	$Contents = $Contents."<form name='edit_form' action='chainstore.act.php' method='post' onsubmit='return SubmitX(this)' enctype='multipart/form-data' style='display:inline;'>";
} else {
	$Contents = $Contents."<form name='edit_form' action='chainstore.act.php' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data' style='display:inline;'>";
}
$Contents = $Contents."
<input name='act' type='hidden' value='$act'>
<input name='mmode' type='hidden' value='$mmode'>
<input name='info_type' type='hidden' value='$info_type'>
<input name='company_id' type='hidden' value='".$company_id."'>";
//$Contents = $Contents."<tr ><img src='../funny/image/title_basicinfo.gif'><td></td></tr>";
$Contents = $Contents."<tr><td>".$Contents01."</td></tr>";
$Contents = $Contents."<tr><td>".$Contents04."</td></tr>";
$Contents = $Contents."<tr><td align=center>".$ButtonString."</td></tr></form>";
$Contents = $Contents."</table><br><br>";




$Script = "<script language='javascript' src='company.add.js'></script>
<script language='JavaScript' src='../webedit/webedit.js'></script>
<script language='javascript'>
function zipcode(type) {
	var zip = window.open('../member/zipcode.php?type='+type,'','width=440,height=300,scrollbars=yes,status=no');
}

function ChangeAddress(obj){
	if(obj.checked){
		document.getElementById('input_address_area').style.display = '';
	}else{
		document.getElementById('input_address_area').style.display = 'none';
	}
}

function loadRegion(sel,target) {

	var trigger = sel.options[sel.selectedIndex].value;
	//alert(sel.form.name);
	var form = sel.form.name;

	var depth = sel.getAttribute('depth');

	window.frames['act'].location.href = '../store/region.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2';

}

function loadBranch(sel,target) {

	var trigger = sel.options[sel.selectedIndex].value;
	//alert(sel.form.name);
	var form = sel.form.name;

	var depth = sel.getAttribute('depth');

	window.frames['act'].location.href = '../store/branch.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2';

}

function loadTeam(sel,target) {

	var trigger = sel.options[sel.selectedIndex].value;
	//alert(sel.form.name);
	var form = sel.form.name;

	var depth = sel.getAttribute('depth');

	window.frames['act'].location.href = '../store/team.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2';

}

function loadSellerManager(sel,target) {

	var trigger = sel.options[sel.selectedIndex].value;
	//alert(sel.form.name);
	var form = sel.form.name;

	var depth = sel.getAttribute('depth');

	window.frames['act'].location.href = '../store/sellermanager.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2';

}

</script>

";

if($mmode == "pop"){


	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = chainstore_menu();
	$P->strContents = $Contents;
	$P->Navigation = "가맹점관리 > 회원관리 > 가맹점 정보 등록/수정";
	$P->NaviTitle = "가맹점 정보 등록/수정";
	echo $P->PrintLayOut();
}else{

	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = chainstore_menu();
	$P->strContents = $Contents;
	$P->Navigation = "가맹점관리 > 회원관리 > 가맹점 정보 등록/수정";
	$P->title = "가맹점 정보 등록/수정";
	echo $P->PrintLayOut();
}

/*


create table common_seller_detail (
company_id varchar(32) not null ,
shop_name varchar(50) null default null,
shop_desc mediumtext null default null,
homepage varchar(255) null default null,
seller_auth enum('Y','N','X') default 'N',
primary key(company_id));


insert into common_user
select charger_code as code, charger_id as id, charger_pass as pw, regdate as date, 0 as visit, '' as last,
'' as ip , '' as file, case when charger_level = 9 then 'A' else 'S' end as mem_type, company_id ,
charger_auth as seller_auth , charger_ix
from shop_company_---userinfo;


insert into common_member_detail
select charger_code as code, '' as jumin, '' as birthday, '' as birthday_div,  charger as name,
charger_email as mail, '' as zip , '' as addr1, '' as addr2, charger_phone as tel, 'C' as tel_div,
charger_mobile as pcs,  '1' as info, '1' as sms, '' as nick_name,  '' as job,
regdate as date, '' as file,'' as recent_order_date, '' as recom_id , '1' as gp_ix , 'M' as sex_div,
'' as add_etc1, '' as add_etc2, '' as add_etc3, '' as add_etc4,  '' as add_etc5, '' as add_etc6
from shop_company_---userinfo;


insert into common_company_detail
select company_id, company_name as com_name, ceo as com_ceo, business_kind as com_business_status, business_item as com_business_category, business_number as com_number, phone as com_phone, fax as com_fax , company_zip as com_zip ,
company_address as com_addr1, '' as com_addr2
from shop_---companyinfo

*/
?>