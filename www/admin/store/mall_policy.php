<?
include("../class/layout.class");

if($admininfo[admin_level] < 9){
	header("Location:../admin.php");
}

$db = new Database;

$sql="CREATE TABLE IF NOT EXISTS `shop_policy_info` (
  `pi_ix` int(3) NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `pi_code` varchar(50) NOT NULL COMMENT '정책구분값',
  `pi_contents` mediumtext NULL COMMENT '정책내용',
  `contents_type` enum('B','U') not null default 'B' COMMENT '정책내용구분(B:기본정책,U:운영자입력)',
  `disp` enum('Y','N') not null default 'Y' COMMENT '정책사용',
  `regdate` datetime null COMMENT '등록일',
  `modidate` datetime null COMMENT '수정일',
  PRIMARY KEY (`pi_ix`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='상점정책정보(이용,개인정보)';";//테이블이 없을 경우 생성 kbk 13/02/17
$db->query($sql);

$sql="SELECT * FROM shop_policy_info ";
$db->query($sql);

if($db->total) {
	$sql_type="update";
	for($i=0;$i < $db->total;$i++){
		$db->fetch($i);
		$mp_config[$db->dt[pi_code]]["pi_code"] = $db->dt[pi_code];
		$mp_config[$db->dt[pi_code]]["pi_contents"] = stripslashes($db->dt[pi_contents]);
		$mp_config[$db->dt[pi_code]]["contents_type"] = $db->dt[contents_type];
		$mp_config[$db->dt[pi_code]]["disp"] = $db->dt[disp];
	}
} else {
	$sql_type="insert";
}

$p_path=$_SERVER["DOCUMENT_ROOT"]."/admin/store/";

$mall_policy_file_path=$p_path."mall_policy.txt";
$mall_policy_handle=fopen($mall_policy_file_path,"r");
$mall_policy_text=fread($mall_policy_handle,filesize($mall_policy_file_path));
fclose($mall_policy_handle);

$mall_policy_eft_file_path=$p_path."mall_policy_eft.txt";		//전자 금융거래 이용약관 electronic financial transaction 2016-06-28 이학봉
$mall_policy_eft_handle=fopen($mall_policy_eft_file_path,"r");	
$mall_policy_eft_text=fread($mall_policy_eft_handle,filesize($mall_policy_eft_file_path));
fclose($mall_policy_eft_handle);


$others_offer_file_path=$p_path."others_offer.txt";
$others_offer_handle=fopen($others_offer_file_path,"r");
$others_offer_text=fread($others_offer_handle,filesize($others_offer_file_path));
fclose($others_offer_handle);

$privacy_items_file_path=$p_path."privacy_items.txt";
$privacy_items_handle=fopen($privacy_items_file_path,"r");
$privacy_items_text=fread($privacy_items_handle,filesize($privacy_items_file_path));
fclose($privacy_items_handle);

$privacy_purpose_file_path=$p_path."privacy_purpose.txt";
$privacy_purpose_handle=fopen($privacy_purpose_file_path,"r");
$privacy_purpose_text=fread($privacy_purpose_handle,filesize($privacy_purpose_file_path));
fclose($privacy_purpose_handle);

$privacy_period_file_path=$p_path."privacy_period.txt";
$privacy_period_handle=fopen($privacy_period_file_path,"r");
$privacy_period_text=fread($privacy_period_handle,filesize($privacy_period_file_path));
fclose($privacy_period_handle);

$privacy_consignment_file_path=$p_path."privacy_consignment.txt";
$privacy_consignment_handle=fopen($privacy_consignment_file_path,"r");
$privacy_consignment_text=fread($privacy_consignment_handle,filesize($privacy_consignment_file_path));
fclose($privacy_consignment_handle);

$Contents01 = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;'>
<col width='*'>
	<tr>
		<td align='left'> ".GetTitleNavigation("약관/개인정보취급방침", "상점관리 > 쇼핑몰 환경설정 > 약관/개인정보취급방침")."</td>
	</tr>

	<tr>
		<td align='left' style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b class='blk'>쇼핑몰 이용약관</b></div>")."</td>
	</tr>
</table>
<form name='policy_form' method='post' action='mall_policy.act.php' target='act'>
<input type='hidden' name='act_value' value='mall_policy_act' />
<input type='hidden' name='type_value' value='".$sql_type."' />
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
	<col width='*'>
	<tr>
		<td class='input_box_item' style='padding:5px 0px;text-align:center;'>
			<input type='hidden' name=\"pi_code[]\" value='mall_policy' />
			<input type='hidden' name=\"disp[mall_policy]\" value='Y' />
			<input type='hidden' name=\"sql_type[mall_policy]\" value='".($mp_config["mall_policy"]["pi_code"]==""?"insert":"update")."' />
			<textarea name=\"pi_contents[mall_policy][B]\" id='mall_policy_pi_contents_b' class='mall_policy_contents' style='width:95%;height:150px;".($mp_config["mall_policy"]["contents_type"]=="B" || $mp_config["mall_policy"]["contents_type"]==""?"":"display:none;")."' readonly>".$mall_policy_text."</textarea>
			<textarea name=\"pi_contents[mall_policy][U]\" id='mall_policy_pi_contents_u' class='mall_policy_contents' style='width:95%;height:150px;".($mp_config["mall_policy"]["contents_type"]=="U"?"":"display:none;")."'>".($mp_config["mall_policy"]["contents_type"]=="" || $mp_config["mall_policy"]["contents_type"]=="B"?$mall_policy_text:$mp_config["mall_policy"]["pi_contents"])."</textarea>
		</td>
	</tr>
	<tr height=30>
		<td class='input_box_item' style='text-align:right;'>
			<div style='padding-right:10px;'>
				<input type='radio' name=\"contents_type[mall_policy]\" id='mall_policy_contents_type_b' value='B' ".($mp_config["mall_policy"]["contents_type"]=="B" || $mp_config["mall_policy"]["contents_type"]==""?"checked":"")." onClick=\"ch_contents('mall_policy_contents',0)\" /> <label for='mall_policy_contents_type_b'>공정위 표준약관(기본형) 적용</label> <input type='radio' name=\"contents_type[mall_policy]\" id='mall_policy_contents_type_u' value='U' ".($mp_config["mall_policy"]["contents_type"]=="U"?"checked":"")." onClick=\"ch_contents('mall_policy_contents',1)\" /> <label for='mall_policy_contents_type_u'>직접 작성한 표준약관 적용</label>
			</div>
		</td>
	</tr>
</table>
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;'>
	<col width='*'>";
	/*if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$Contents01 .= "
	<tr>
		<td class='input_box_item' align='right'>
			<table width='100%' cellpadding=0 cellspacing=0 border='0'>
				<tr bgcolor=#ffffff height=70><td align=center><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' onClick=\"send_policy('mall_policy')\" ></td></tr>
			</table>
			</td>
	</tr>";
	}*/
$Contents01 .= "
	<tr>
		<td>
			<table width='100%' align='left' border='0' cellSpacing='0' cellPadding='0'>
				<col width='20' />
				<col width='*' />
				<tr>
					<td>
						<img align='absMiddle' src='../image/emo_3_15.gif' complete='complete'/>
					</td>
					<td align='left' class='small' style='padding-bottom: 10px; line-height: 120%; padding-left: 10px; padding-right: 10px; padding-top: 10px;'>
						저희 서비스는 공정거래위원회의 표준약관을 제공해 드리고 있습니다.
					</td>
				</tr>
				<tr>
					<td>
						<img align='absMiddle' src='../image/emo_3_15.gif' complete='complete'/>
					</td>
					<td align='left' class='small' style='padding-bottom: 10px; line-height: 120%; padding-left: 10px; padding-right: 10px; padding-top: 10px;'>
						공정위 약관을 사용하지 않거나 수정한 경우, 공정위 표준약관 로고를 사용하시면 안됩니다!
					</td>
				</tr>
				<tr>
					<td>
						<img align='absMiddle' src='../image/emo_3_15.gif' complete='complete'/>
					</td>
					<td align='left' class='small' style='padding-bottom: 10px; line-height: 120%; padding-left: 10px; padding-right: 10px; padding-top: 10px;'>
						공정위 표준약관(기본형)적용 선택 시 {_SESSION[\"shopcfg\"][\"com_name\"]}은 상점명, [DATE]는 약관적용일자가 자동으로 입력됩니다.
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<!--tr>
		<td align='center'>
			<input type='button' value='공정위 표준약관' style='cursor:pointer;font-family:Dotum;font-size:18px;width:200px;height:50px;font-weight:600;' />
		</td>
	</tr-->
</table>";

$Contents01 .= "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;'>
<col width='*'>
	<tr>
		<td align='left'> ".GetTitleNavigation("약관/개인정보취급방침", "상점관리 > 쇼핑몰 환경설정 > 약관/개인정보취급방침")."</td>
	</tr>
	<tr>
		<td align='left' style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b class='blk'>전자금융거래 이용약관</b></div>")."</td>
	</tr>
</table>
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
	<col width='*'>
	<tr>
		<td class='input_box_item' style='padding:5px 0px;text-align:center;'>
			<input type='hidden' name=\"pi_code[]\" value='mall_policy_eft' />
			<input type='hidden' name=\"disp[mall_policy_eft]\" value='Y' />
			<input type='hidden' name=\"sql_type[mall_policy_eft]\" value='".($mp_config["mall_policy_eft"]["pi_code"]==""?"insert":"update")."' />

			<textarea name=\"pi_contents[mall_policy_eft][B]\" id='mall_policy_eft_pi_contents_b' class='mall_policy_eft_contents' style='width:95%;height:150px;".($mp_config["mall_policy_eft"]["contents_type"]=="B" || $mp_config["mall_policy_eft"]["contents_type"]==""?"":"display:none;")."' readonly>".$mall_policy_eft_text."</textarea>
			<textarea name=\"pi_contents[mall_policy_eft][U]\" id='mall_policy_eft_pi_contents_u' class='mall_policy_eft_contents' style='width:95%;height:150px;".($mp_config["mall_policy_eft"]["contents_type"]=="U"?"":"display:none;")."'>".($mp_config["mall_policy_eft"]["contents_type"]=="" || $mp_config["mall_policy_eft"]["contents_type"]=="B"?$mall_policy_text:$mp_config["mall_policy_eft"]["pi_contents"])."</textarea>
		</td>
	</tr>
	<tr height=30>
		<td class='input_box_item' style='text-align:right;'>
			<div style='padding-right:10px;'>
				<input type='radio' name=\"contents_type[mall_policy_eft]\" id='mall_policy_eft_contents_type_b' value='B' ".($mp_config["mall_policy_eft"]["contents_type"]=="B" || $mp_config["mall_policy_eft"]["contents_type"]==""?"checked":"")." onClick=\"ch_contents('mall_policy_eft_contents',0)\" /> <label for='mall_policy_eft_contents_type_b'>공정위 표준약관(기본형) 적용</label> <input type='radio' name=\"contents_type[mall_policy_eft]\" id='mall_policy_eft_contents_type_u' value='U' ".($mp_config["mall_policy_eft"]["contents_type"]=="U"?"checked":"")." onClick=\"ch_contents('mall_policy_eft_contents',1)\" /> <label for='mall_policy_eft_contents_type_u'>직접 작성한 표준약관 적용</label>
			</div>
		</td>
	</tr>
</table>

";

$Contents01 .= "
<!--table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;'>
<col width='*'>
	<tr>
		<td align='left' style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b class='blk'>제 3자 정보제공</b></div>")."</td>
	</tr>
</table>
<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
	<col width='200'>
	<col width='*'>
	<col width='200'>
	<col width='*'>
	<tr bgcolor=#ffffff>
		<td class='input_box_item' colspan='4' style='padding:5px 0px;text-align:center;'>
			<input type='hidden' name=\"pi_code[]\" value='others_offer' />
			<textarea name=\"pi_contents['others_offer']['B']\" id='others_offer_pi_contents_b' class='others_offer_contents' style='width:95%;height:150px;".($mp_config["others_offer"]["contents_type"]=="B" || $mp_config["others_offer"]["contents_type"]==""?"":"display:none;")."'>".$others_offer_text."</textarea>
			<textarea name=\"pi_contents['others_offer']['U']\" id='others_offer_pi_contents_u' class='others_offer_contents' style='width:95%;height:150px;".($mp_config["others_offer"]["contents_type"]=="U"?"":"display:none;")."'>".$mp_config["others_offer"]["pi_contents"]."</textarea>
		</td>
	</tr>
	<tr bgcolor=#ffffff >
		<td class='input_box_title' >제 3자 정보제공 사용</td>
		<td class='input_box_item'>
			<input type='radio' name=\"disp['others_offer']\" id='others_offer_disp_y' value='Y' ".($mp_config["others_offer"]["disp"]=="Y"?"checked":"")." /> <label for='others_offer_disp_y'>사용</label> <input type='radio' name=\"disp['others_offer']\" id='others_offer_disp_n' value='N' ".($mp_config["others_offer"]["disp"]=="N" || $mp_config["others_offer"]["disp"]==""?"checked":"")." /> <label for='others_offer_disp_n'>미사용</label>
		</td>
		<td class='input_box_title' >기본형 사용</td>
		<td class='input_box_item' style='padding:0px 0px;text-align:right;'>
			<div style='padding-right:10px;'>
				<input type='radio' name=\"contents_type['others_offer']\" id='others_offer_contents_type_b' value='B' ".($mp_config["others_offer"]["contents_type"]=="B" || $mp_config["others_offer"]["contents_type"]==""?"checked":"")." onClick=\"ch_contents('others_offer_contents',0)\" /> <label for='others_offer_contents_type_b'>기본형 사용</label> <input type='radio' name=\"contents_type['others_offer']\" id='others_offer_contents_type_u' value='U' ".($mp_config["others_offer"]["contents_type"]=="U"?"checked":"")." onClick=\"ch_contents('others_offer_contents',1)\" /> <label for='others_offer_contents_type_u'>직접 작성해서 적용</label>
			</div>
		</td>
	</tr>
</table>
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;'>
	<col width='*'>";
	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$Contents01 .= "
	<tr>
		<td class='input_box_item' align='right'>
			<table width='100%' cellpadding=0 cellspacing=0 border='0'>
				<tr bgcolor=#ffffff height=70><td align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
			</table>
			</td>
	</tr>";
	}
$Contents01 .= "
	<tr>
		<td>
			<table width='100%' align='left' border='0' cellSpacing='0' cellPadding='0'>
				<col width='20' />
				<col width='*' />
				<tr>
					<td>
						<img align='absMiddle' src='../image/emo_3_15.gif' complete='complete'/>
					</td>
					<td align='left' class='small' style='padding-bottom: 10px; line-height: 120%; padding-left: 10px; padding-right: 10px; padding-top: 10px;'>
						‘제3자 정보제공’ 여부에 따라 개인정보취급방침 동의 형태가 변경 됩니다.
					</td>
				</tr>
				<tr>
					<td>
						<img align='absMiddle' src='../image/emo_3_15.gif' complete='complete'/>
					</td>
					<td align='left' class='small' style='padding-bottom: 10px; line-height: 120%; padding-left: 10px; padding-right: 10px; padding-top: 10px;color:red;'>
						PG사, 택배사 이용시 제공으로 체크 하여 주시기 바랍니다.
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table-->

<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;margin-top:20px;'>
<col width='*'>
	<tr>
		<td align='left' style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b class='blk'>개인정보 취급방침 관리</b></div>")."</td>
	</tr>
</table>
<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
	<col width='200'>
	<col width='*'>
	<col width='200'>
	<col width='*'>
	<tr bgcolor=#ffffff >
		<td class='input_box_item' colspan='4' style='padding:5px 0px;text-align:left;'>
			<div style='padding:0px 10px;line-height:130%;'>
				* 정보통신망법 제 24조의 2제 3항 신설<br />
				* 정보통신 서비스 제공자들은 정보제공에 대한동의와 개인정보 취급위탁에 대한 동의를 받을 때에는 <span style='color:red;'>개인정보 수집 이용에대한 동의와 구분하여 받아야 하고</span>, 이에 동의 하지 아니한다는 이유로<br />&nbsp;&nbsp;서비스 제공을 거부하여서는 아니된다.
			</div>
		</td>
	</tr>
	<tr bgcolor=#ffffff >
		<td class='input_box_title' >수집하는 개인정보 항목</td>
		<td class='input_box_item' colspan='3' style='padding:5px 0px;text-align:center;'>
			<input type='hidden' name=\"pi_code[]\" value='privacy_items' />
			<input type='hidden' name=\"disp[privacy_items]\" value='Y' />
			<input type='hidden' name=\"sql_type[privacy_items]\" value='".($mp_config["privacy_items"]["pi_code"]==""?"insert":"update")."' />
			<textarea name=\"pi_contents[privacy_items][B]\" id='privacy_items_pi_contents_b' class='privacy_items_contents' style='width:95%;height:100px;".($mp_config["privacy_items"]["contents_type"]=="B" || $mp_config["privacy_items"]["contents_type"]==""?"":"display:none;")."'>".$privacy_items_text."</textarea>
			<textarea name=\"pi_contents[privacy_items][U]\" id='privacy_items_pi_contents_u' class='privacy_items_contents' style='width:95%;height:100px;".($mp_config["privacy_items"]["contents_type"]=="U"?"":"display:none;")."'>".($mp_config["privacy_items"]["contents_type"]=="" || $mp_config["privacy_items"]["contents_type"]=="B"?$privacy_items_text:$mp_config["privacy_items"]["pi_contents"])."</textarea>
		</td>
	</tr>
	<tr bgcolor=#ffffff >
		<td class='input_box_item' colspan='4' style='padding:0px 0px;text-align:right;'>
			<div style='padding-right:10px;'>
				<input type='radio' name=\"contents_type[privacy_items]\" id='privacy_items_contents_type_b' value='B' ".($mp_config["privacy_items"]["contents_type"]=="B" || $mp_config["privacy_items"]["contents_type"]==""?"checked":"")." onClick=\"ch_contents('privacy_items_contents',0)\" /> <label for='privacy_items_contents_type_b'>기본형 사용</label> <input type='radio' name=\"contents_type[privacy_items]\" id='privacy_items_contents_type_u' value='U' ".($mp_config["privacy_items"]["contents_type"]=="U"?"checked":"")." onClick=\"ch_contents('privacy_items_contents',1)\" /> <label for='privacy_items_contents_type_u'>직접 작성해서 적용</label>
			</div>
		</td>
	</tr>
	<tr bgcolor=#ffffff >
		<td class='input_box_title' >개인정보의 수집 이용목적</td>
		<td class='input_box_item' colspan='3' style='padding:5px 0px;text-align:center;'>
			<input type='hidden' name=\"pi_code[]\" value='privacy_purpose' />
			<input type='hidden' name=\"disp[privacy_purpose]\" value='Y' />
			<input type='hidden' name=\"sql_type[privacy_purpose]\" value='".($mp_config["privacy_purpose"]["pi_code"]==""?"insert":"update")."' />
			<textarea name=\"pi_contents[privacy_purpose][B]\" id='privacy_purpose_pi_contents_b' class='privacy_purpose_contents' style='width:95%;height:100px;".($mp_config["privacy_purpose"]["contents_type"]=="B" || $mp_config["privacy_purpose"]["contents_type"]==""?"":"display:none;")."'>".$privacy_purpose_text."</textarea>
			<textarea name=\"pi_contents[privacy_purpose][U]\" id='privacy_purpose_pi_contents_u' class='privacy_purpose_contents' style='width:95%;height:100px;".($mp_config["privacy_purpose"]["contents_type"]=="U"?"":"display:none;")."'>".($mp_config["privacy_purpose"]["contents_type"]=="" || $mp_config["privacy_purpose"]["contents_type"]=="B"?$privacy_purpose_text:$mp_config["privacy_purpose"]["pi_contents"])."</textarea>
		</td>
	</tr>
	<tr bgcolor=#ffffff >
		<td class='input_box_item' colspan='4' style='padding:0px 0px;text-align:right;'>
			<div style='padding-right:10px;'>
				<input type='radio' name=\"contents_type[privacy_purpose]\" id='privacy_purpose_contents_type_b' value='B' ".($mp_config["privacy_purpose"]["contents_type"]=="B" || $mp_config["privacy_purpose"]["contents_type"]==""?"checked":"")." onClick=\"ch_contents('privacy_purpose_contents',0)\" /> <label for='privacy_purpose_contents_type_b'>기본형 사용</label> <input type='radio' name=\"contents_type[privacy_purpose]\" id='privacy_purpose_contents_type_u' value='U' ".($mp_config["privacy_purpose"]["contents_type"]=="U"?"checked":"")." onClick=\"ch_contents('privacy_purpose_contents',1)\" /> <label for='privacy_purpose_contents_type_u'>직접 작성해서 적용</label>
			</div>
		</td>
	</tr>
	<tr bgcolor=#ffffff >
		<td class='input_box_title' >개인정보의 보유 및 이용기간</td>
		<td class='input_box_item' colspan='3' style='padding:5px 0px;text-align:center;'>
			<input type='hidden' name=\"pi_code[]\" value='privacy_period' />
			<input type='hidden' name=\"disp[privacy_period]\" value='Y' />
			<input type='hidden' name=\"sql_type[privacy_period]\" value='".($mp_config["privacy_period"]["pi_code"]==""?"insert":"update")."' />
			<textarea name=\"pi_contents[privacy_period][B]\" id='privacy_period_pi_contents_b' class='privacy_period_contents' style='width:95%;height:100px;".($mp_config["privacy_period"]["contents_type"]=="B" || $mp_config["privacy_period"]["contents_type"]==""?"":"display:none;")."'>".$privacy_period_text."</textarea>
			<textarea name=\"pi_contents[privacy_period][U]\" id='privacy_period_pi_contents_u' class='privacy_period_contents' style='width:95%;height:100px;".($mp_config["privacy_period"]["contents_type"]=="U"?"":"display:none;")."'>".($mp_config["privacy_period"]["contents_type"]=="" || $mp_config["privacy_period"]["contents_type"]=="B"?$privacy_period_text:$mp_config["privacy_period"]["pi_contents"])."</textarea>
		</td>
	</tr>
	<tr bgcolor=#ffffff >
		<td class='input_box_item' colspan='4' style='padding:0px 0px;text-align:right;'>
			<div style='padding-right:10px;'>
				<input type='radio' name=\"contents_type[privacy_period]\" id='privacy_period_contents_type_b' value='B' ".($mp_config["privacy_period"]["contents_type"]=="B" || $mp_config["privacy_period"]["contents_type"]==""?"checked":"")." onClick=\"ch_contents('privacy_period_contents',0)\" /> <label for='privacy_period_contents_type_b'>기본형 사용</label> <input type='radio' name=\"contents_type[privacy_period]\" id='privacy_period_contents_type_u' value='U' ".($mp_config["privacy_period"]["contents_type"]=="U"?"checked":"")." onClick=\"ch_contents('privacy_period_contents',1)\" /> <label for='privacy_period_contents_type_u'>직접 작성해서 적용</label>
			</div>
		</td>
	</tr>
	<tr bgcolor=#ffffff>
		<td class='input_box_title' >제 3자 정보제공</td>
		<td class='input_box_item' colspan='3' style='padding:5px 0px;text-align:center;'>
			<input type='hidden' name=\"pi_code[]\" value='others_offer' />
			<input type='hidden' name=\"sql_type[others_offer]\" value='".($mp_config["others_offer"]["pi_code"]==""?"insert":"update")."' />
			<textarea name=\"pi_contents[others_offer][B]\" id='others_offer_pi_contents_b' class='others_offer_contents' style='width:95%;height:150px;".($mp_config["others_offer"]["contents_type"]=="B" || $mp_config["others_offer"]["contents_type"]==""?"":"display:none;")."'>".$others_offer_text."</textarea>
			<textarea name=\"pi_contents[others_offer][U]\" id='others_offer_pi_contents_u' class='others_offer_contents' style='width:95%;height:150px;".($mp_config["others_offer"]["contents_type"]=="U"?"":"display:none;")."'>".($mp_config["others_offer"]["contents_type"]=="" || $mp_config["others_offer"]["contents_type"]=="B"?$others_offer_text:$mp_config["others_offer"]["pi_contents"])."</textarea>
		</td>
	</tr>
	<tr bgcolor=#ffffff >
		<td class='input_box_title' >제 3자 정보제공 사용</td>
		<td class='input_box_item'>
			<input type='radio' name=\"disp[others_offer]\" id='others_offer_disp_y' value='Y' ".($mp_config["others_offer"]["disp"]=="Y"?"checked":"")." /> <label for='others_offer_disp_y'>사용</label> <input type='radio' name=\"disp[others_offer]\" id='others_offer_disp_n' value='N' ".($mp_config["others_offer"]["disp"]=="N" || $mp_config["others_offer"]["disp"]==""?"checked":"")." /> <label for='others_offer_disp_n'>미사용</label>
		</td>
		<td class='input_box_title' >기본형 사용</td>
		<td class='input_box_item' style='padding:0px 0px;text-align:right;'>
			<div style='padding-right:10px;'>
				<input type='radio' name=\"contents_type[others_offer]\" id='others_offer_contents_type_b' value='B' ".($mp_config["others_offer"]["contents_type"]=="B" || $mp_config["others_offer"]["contents_type"]==""?"checked":"")." onClick=\"ch_contents('others_offer_contents',0)\" /> <label for='others_offer_contents_type_b'>기본형 사용</label> <input type='radio' name=\"contents_type[others_offer]\" id='others_offer_contents_type_u' value='U' ".($mp_config["others_offer"]["contents_type"]=="U"?"checked":"")." onClick=\"ch_contents('others_offer_contents',1)\" /> <label for='others_offer_contents_type_u'>직접 작성해서 적용</label>
			</div>
		</td>
	</tr>
	<tr bgcolor=#ffffff >
		<td class='input_box_title' >개인정보 취급위탁안내</td>
		<td class='input_box_item' colspan='3' style='padding:5px 0px;text-align:center;'>
			<input type='hidden' name=\"pi_code[]\" value='privacy_consignment' />
			<input type='hidden' name=\"sql_type[privacy_consignment]\" value='".($mp_config["privacy_consignment"]["pi_code"]==""?"insert":"update")."' />
			<textarea name=\"pi_contents[privacy_consignment][B]\" id='privacy_consignment_pi_contents_b' class='privacy_consignment_contents' style='width:95%;height:100px;".($mp_config["privacy_consignment"]["contents_type"]=="B" || $mp_config["privacy_consignment"]["contents_type"]==""?"":"display:none;")."'>".$privacy_consignment_text."</textarea>
			<textarea name=\"pi_contents[privacy_consignment][U]\" id='privacy_consignment_pi_contents_u' class='privacy_consignment_contents' style='width:95%;height:100px;".($mp_config["privacy_consignment"]["contents_type"]=="U"?"":"display:none;")."'>".($mp_config["privacy_consignment"]["contents_type"]=="" || $mp_config["privacy_consignment"]["contents_type"]=="B"?$privacy_consignment_text:$mp_config["privacy_consignment"]["pi_contents"])."</textarea>
		</td>
	</tr>
	<tr bgcolor=#ffffff >
		<td class='input_box_title' >개인정보 취급위탁안내 사용</td>
		<td class='input_box_item'>
			<input type='radio' name=\"disp[privacy_consignment]\" id='privacy_consignment_disp_y' value='Y' ".($mp_config["privacy_consignment"]["disp"]=="Y"?"checked":"")." /> <label for='privacy_consignment_disp_y'>사용</label> <input type='radio' name=\"disp[privacy_consignment]\" id='privacy_consignment_disp_n' value='N' ".($mp_config["privacy_consignment"]["disp"]=="N" || $mp_config["privacy_consignment"]["disp"]==""?"checked":"")." /> <label for='privacy_consignment_disp_n'>미사용</label>
		</td>
		<td class='input_box_title' >기본형 사용</td>
		<td class='input_box_item' style='padding:0px 0px;text-align:right;'>
			<div style='padding-right:10px;'>
				<input type='radio' name=\"contents_type[privacy_consignment]\" id='privacy_consignment_contents_type_b' value='B' ".($mp_config["privacy_consignment"]["contents_type"]=="B" || $mp_config["privacy_consignment"]["contents_type"]==""?"checked":"")." onClick=\"ch_contents('privacy_consignment_contents',0)\" /> <label for='privacy_consignment_contents_type_b'>기본형 사용</label> <input type='radio' name=\"contents_type[privacy_consignment]\" id='privacy_consignment_contents_type_u' value='U' ".($mp_config["privacy_consignment"]["contents_type"]=="U"?"checked":"")." onClick=\"ch_contents('privacy_consignment_contents',1)\" /> <label for='privacy_consignment_contents_type_u'>직접 작성해서 적용</label>
			</div>
		</td>
	</tr>

	<tr bgcolor=#ffffff >
		<td class='input_box_title' >판매자 이용약관</td>
		<td class='input_box_item' colspan='3' style='padding:5px 0px;text-align:center;'>
			<input type='hidden' name=\"pi_code[]\" value='seller_consignment' />
			<input type='hidden' name=\"sql_type[seller_consignment]\" value='".($mp_config["seller_consignment"]["pi_code"]==""?"insert":"update")."' />
			<textarea name=\"pi_contents[seller_consignment][B]\" id='seller_consignment_pi_contents_b' class='seller_consignment_contents' style='width:95%;height:100px;".($mp_config["seller_consignment"]["contents_type"]=="B" || $mp_config["seller_consignment"]["contents_type"]==""?"":"display:none;")."'>".$privacy_consignment_text."</textarea>
			<textarea name=\"pi_contents[seller_consignment][U]\" id='seller_consignment_pi_contents_u' class='seller_consignment_contents' style='width:95%;height:100px;".($mp_config["seller_consignment"]["contents_type"]=="U"?"":"display:none;")."'>".($mp_config["seller_consignment"]["contents_type"]=="" || $mp_config["seller_consignment"]["contents_type"]=="B"?$privacy_consignment_text:$mp_config["seller_consignment"]["pi_contents"])."</textarea>
		</td>
	</tr>
	<tr bgcolor=#ffffff >
		<td class='input_box_title' >판매자 이용약관 사용</td>
		<td class='input_box_item'>
			<input type='radio' name=\"disp[seller_consignment]\" id='seller_consignment_disp_y' value='Y' ".($mp_config["seller_consignment"]["disp"]=="Y"?"checked":"")." /> <label for='seller_consignment_disp_y'>사용</label> <input type='radio' name=\"disp[seller_consignment]\" id='seller_consignment_disp_n' value='N' ".($mp_config["seller_consignment"]["disp"]=="N" || $mp_config["seller_consignment"]["disp"]==""?"checked":"")." /> <label for='seller_consignment_disp_n'>미사용</label>
		</td>
		<td class='input_box_title' >기본형 사용</td>
		<td class='input_box_item' style='padding:0px 0px;text-align:right;'>
			<div style='padding-right:10px;'>
				<input type='radio' name=\"contents_type[seller_consignment]\" id='seller_consignment_contents_type_b' value='B' ".($mp_config["seller_consignment"]["contents_type"]=="B" || $mp_config["seller_consignment"]["contents_type"]==""?"checked":"")." onClick=\"ch_contents('seller_consignment_contents',0)\" /> <label for='seller_consignment_contents_type_b'>기본형 사용</label> <input type='radio' name=\"contents_type[seller_consignment]\" id='seller_consignment_contents_type_u' value='U' ".($mp_config["seller_consignment"]["contents_type"]=="U"?"checked":"")." onClick=\"ch_contents('seller_consignment_contents',1)\" /> <label for='seller_consignment_contents_type_u'>직접 작성해서 적용</label>
			</div>
		</td>
	</tr>

</table>
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;'>
	<col width='*'>";
	/*if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$Contents01 .= "
	<tr>
		<td class='input_box_item' align='right'>
			<table width='100%' cellpadding=0 cellspacing=0 border='0'>
				<tr bgcolor=#ffffff height=70><td align=center><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' onClick=\"send_policy('privacy')\" ></td></tr>
			</table>
			</td>
	</tr>";
	}*/
$Contents01 .= "
	<tr>
		<td>
			<table width='100%' align='left' border='0' cellSpacing='0' cellPadding='0'>
				<col width='20' />
				<col width='*' />
				<tr>
					<td>
						<img align='absMiddle' src='../image/emo_3_15.gif' complete='complete'/>
					</td>
					<td align='left' class='small' style='padding-bottom: 10px; line-height: 120%; padding-left: 10px; padding-right: 10px; padding-top: 10px;'>
						[NAME], [EMAIL], [SHOP], [TEL]은 <strong>상점관리 &gt; 사업자 정보</strong>에 입력하신 정보가 자동으로 입력 됩니다.
					</td>
				</tr>
				<tr>
					<td>
						<img align='absMiddle' src='../image/emo_3_15.gif' complete='complete'/>
					</td>
					<td align='left' class='small' style='padding-bottom: 10px; line-height: 120%; padding-left: 10px; padding-right: 10px; padding-top: 10px;'>
						‘제3자 정보제공’ 여부에 따라 개인정보취급방침 동의 형태가 변경 됩니다.
					</td>
				</tr>
				<tr>
					<td>
						<img align='absMiddle' src='../image/emo_3_15.gif' complete='complete'/>
					</td>
					<td align='left' class='small' style='padding-bottom: 10px; line-height: 120%; padding-left: 10px; padding-right: 10px; padding-top: 10px;'>
						! 운영 중이신 쇼핑몰에 맞게 수정하여 사용하시기 바랍니다.
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td align='center'>
			<input type='button' value='제3자 정보제공/개인정보 취급위탁 설정안내 가이드' style='cursor:pointer;height:25px;' onClick=\"PoPWindow('mall_privacy_user_guide.php',703,652,'mall_privacy_user_guide')\" />
		</td>
	</tr>
	<tr>
		<td>";
		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$Contents01 .= "
			<table width='100%' cellpadding=0 cellspacing=0 border='0'>
				<!--tr bgcolor=#ffffff height=70><td align=center><input type='button' value='전체저장' border=0 style='cursor:pointer;width:200px;height:80px;font-weight:600;font-size:18px;' onClick=\"send_policy('all')\"></td></tr-->
				<tr bgcolor=#ffffff height=70><td align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' value='전체저장' border=0 style='cursor:pointer;'></td></tr>
			</table>
			";
	}
$Contents01 .= "</td>
	</tr>
</table>
</form>
";

$Contents = "<table width='100%' height='100%'  border=0>";
$Contents = $Contents."<tr><td>".$Contents01."<br></td></tr>";
$Contents = $Contents."<tr><td>";
//$Contents = $Contents.$ButtonString;
$Contents = $Contents."</td></tr>";
$Contents = $Contents."</table >";

//$Contents = "<div style=height:1000px;'></div>";


$Script = "<script language='javascript' src='basicinfo.js'></script>
<script language='javascript'>
function update_zipcode(){
	form = document.edit_form;
	form.action = './zip_act.php';
	form.act.value = 'zipcode';
	form.submit();
}

function ch_contents(ctext,k) {
	$('.'+ctext).each(function() {
		$(this).css('display','none');
	});
	$('.'+ctext+':eq('+k+')').css('display','');
}

function send_policy(stype) {
	var fm=document.policy_form;
	fm.type_value.value=stype;
	fm.submit();
}
</script>
";

$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = store_menu();
$P->Navigation = "상점관리 > 쇼핑몰 환경설정 > 약관/개인정보취급방침";
$P->title = "약관/개인정보취급방침";
$P->strContents = $Contents;
echo $P->PrintLayOut();




/*
create table admin_menus (
menu_code varchar(32) not null ,
menu_name varchar(255) null default null,
menu_path varchar(255) null default null,
auth_read enum('Y','N') null default 'Y',
auth_write enum('Y','N') null default 'Y',
shipping_company varchar(30) null default null,
primary key(menu_code));


CREATE TABLE IF NOT EXISTS `shop_mall_config` (
  `mall_ix` varchar(32) NOT NULL COMMENT '쇼핑몰키',
  `config_name` varchar(100) NOT NULL DEFAULT '' COMMENT 'PG 환경설정 변수이름',
  `config_value` varchar(255) DEFAULT NULL COMMENT 'PG 환경설정 변수값',
  PRIMARY KEY (`mall_ix`,`config_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='쇼핑몰 환경설정 정보';


*/
?>