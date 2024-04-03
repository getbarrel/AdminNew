<?
include("../class/layout.class");
include("../logstory/class/sharedmemory.class");
if($admininfo[admin_level] < 9){
	header("Location:/admin/store/company.add.php");
}

if($admininfo[admin_id] == "forbiz"){
	//print_r($admininfo);
//	echo $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
}

$shmop = new Shared("coupon_rule");
$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
$shmop->SetFilePath();
$coupon_data = $shmop->getObjectForKey("coupon_rule");
$coupon_data = unserialize(urldecode($coupon_data));

//echo md5("wooho".$db->dt[mall_domain].$db->dt[mall_domain_id]);


$Contents01 = "
	<table width='100%' cellpadding=3 cellspacing=0 border='0'>
	  <tr>
			<td align='left' style='padding-bottom:10px;'> ".GetTitleNavigation("쿠폰 정책", "상점관리 > 쿠폰 정책")."</td>
	  </tr>
	</table>";
$Contents01 .="<div style='width:100%;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b>쿠폰 지급 정책</b></div>")."</div>";
$Contents01 .="
	<table width='100%' cellpadding=3 cellspacing=0 border='0' style='margin-top:10px;'>
	  <col width=200>
	  <col width=*>
	  <tr bgcolor=#ffffff >
	    <td><img src='../image/ico_dot2.gif' align=absmiddle> <b>쿠폰 사용 여부 <img src='".$required_path."'></b></td>
	    <td>
			<input type='radio' name='coupon_use_yn' value='Y' id='coupon_use_yn_y'  ".($coupon_data[coupon_use_yn] == "Y" ? "checked":"")."><label for='coupon_use_yn_y' >사용</label>
			<input type='radio' name='coupon_use_yn' value='N' id='coupon_use_yn_n'  ".($coupon_data[coupon_use_yn] =="N" ? "checked":"")."><label for='coupon_use_yn_n' >사용안함</label>
		</td>
	  </tr>
	  <tr height=1><td colspan=2 class=dot-x></td></tr>
	  <tr bgcolor=#ffffff height=50>
	    <td><img src='../image/ico_dot2.gif' align=absmiddle> <b>상품쿠폰 기본 설정 <img src='".$required_path."'></b></td>
	    <td style='line-height:150%'>
	    상품 구매 금액이 <input type=text class='textbox' name='goods_coupon_rate' value='".$coupon_data[goods_coupon_rate]."' style='width:60px;' validation='true' title='상품쿠폰 기본설정'> 일경우
		".CouponRuleSelectBox($coupon_data[goods_publish_ix],"goods_publish_ix")." 을 발급합니다.
		<br><span class=blue>* 신규 상품 등록시 기본으로 적용되며, 상품별로 개별 설정시 본 설정은 적용되지 않습니다.</span>
	    </td>
	  </tr>
	  <tr height=1><td colspan=2 class=dot-x></td></tr>
	  <tr bgcolor=#ffffff height=50 style='line-height:170%'>
	    <td><img src='../image/ico_dot2.gif' align=absmiddle> <b>회원가입 쿠폰 설정 <img src='".$required_path."'></b></td>
	    <td>
	    신규 회원 가입시 ".CouponRuleSelectBox($coupon_data[member_publish_ix],"member_publish_ix")." 쿠폰을 발급합니다<br>
	    <span class='blue small'>* 회원가입 쿠폰을 지급하시지 않을 경우 아무것도 선택하지 않으시면 됩니다.</span>
	    </td>
	  </tr>
	  <tr height=1><td colspan=2 class=dot-x></td></tr>
	  <!--tr bgcolor=#ffffff height=50 style='line-height:150%'>
	   <td rowspan=1><img src='../image/ico_dot2.gif' align=absmiddle> <b>쿠폰 사용 제한 설정 <img src='".$required_path."'></b></td>
	    <td>
		상품 구매 합계액이 <input type=text class='textbox' name='total_order_price' value='".$coupon_data[total_order_price]."' style='width:60px;' validation='true' title='쿠폰 사용제한 설정'> 원 이상 상품 구매시
	      ".CouponRuleSelectBox($mdb,"publish_ix2")." 쿠폰을 발급합니다.
	    </td>
	  </tr>

	  <tr height=1><td colspan=4 class=dot-x></td></tr-->
	  <!--tr bgcolor=#ffffff height=50 style='line-height:150%'>
	    <td rowspan=3><img src='../image/ico_dot2.gif' align=absmiddle> <b>쿠폰 1회 사용 한도 <img src='".$required_path."'></b></td>
	    <td colspan=2>
	    <input type=radio name='coupon_one_use_type' value='1' ".($coupon_data[coupon_one_use_type] == "1" ? "checked":"")." onclick=\"document.getElementById('max_goods_sum_rate').validation=false;document.getElementById('use_reseve_max').validation=true\"> 최대  <input type=text class='textbox' name='use_reseve_max' value='".$coupon_data[use_reseve_max]."' style='width:60px;' id='use_reseve_max' ".($coupon_data[coupon_one_use_type] == "1" ? "validation='true'":"validation='false'")." title='쿠폰 1회 사용 한도'>원 까지만 사용 가능
	    <span class='blue small'>* 쿠폰으로 전액 결제 가능하게 하시려면 0원을 입력하시면 됩니다.</span>
	    </td>
	    <td align=left colspan=1></td>
	  </tr>
	  <tr height=1><td colspan=4 class=dot-x></td></tr>
	  <tr bgcolor=#ffffff height=50 style='line-height:150%'>
	    <td colspan=2>
	    <input type=radio name='coupon_one_use_type' value='2' ".($coupon_data[coupon_one_use_type] == "2" ? "checked":"")." onclick=\"document.getElementById('max_goods_sum_rate').validation=true;document.getElementById('use_reseve_max').validation=false\">  상품 구매 합계액의  <input type=text class='textbox' name='max_goods_sum_rate' value='".$coupon_data[max_goods_sum_rate]."' style='width:60px;' id='max_goods_sum_rate' ".($coupon_data[coupon_one_use_type] == "2" ? "validation='true'":"validation='false'")." title='쿠폰 1회 사용 한도'>% 까지만 사용가능(최대 100%)<br>
	    </td>
	  </tr>
	  <tr height=1><td colspan=2 class=dot-x></td></tr-->
	  </table>";
/*
$ContentsDesc01 = "
<table cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td><img src='../image/emo_3_15.gif' align=absmiddle ></td>
	<td align=left style='padding:10px;' class=small>
		  도메인, 도메인 아이디, 도메인 key 등은 몰스토리에서 발급해드리는 사항이므로 변경이 불가능합니다.<br>
		  <u>상업적인 목적으로 상점을 운영</u>하기 위해서는 정식 <b>도메인 key</b>를 발급 받아 사용하셔야만 상점을 정상적으로 운영하실수 있습니다.
	</td>
</tr>
</table>
";
*/

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff height=70><td colspan=4 align=center><input type='image' src='../image/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";
}

$Contents = "<table width='100%' height='200%'  border=0>";
$Contents = $Contents."<form name='edit_form' action='coupon_rule.act.php' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data' target='act'>";
$Contents = $Contents."<tr><td>".$Contents01."<br></td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";

$Contents = $Contents."<tr><td>";
$Contents = $Contents.$ButtonString;
$Contents = $Contents."</td></tr>";
$Contents = $Contents."</form>";
$Contents = $Contents."</table >";


$help_text = "
<table cellpadding=2 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td ><img src='/admin/image/icon_list.gif' ></td><td class='small' >사이트 쿠폰 정책을 관리합니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >쿠폰 정책 수정 즉시 반영되게 됩니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >상품의 구매 금액에 따른 쿠폰을 발급 할 수 있습니다. </td></tr>
	<tr><td valign=top></td><td class='small' style='line-height:120%' >예) 상품 금액이 30000만원 이상일때 쿠폰 발급 설정을 하시면 30000만원 이상인 상품들에 한해서 미리 설정한 쿠폰이 나열 됩니다. 상품 개별로 설정 시에는 마케팅지원 쿠폰발행 페이지를 참고 하시면 됩니다. </td></tr>
</table>
";


$Contents .=  HelpBox("쿠폰 정책 관리", $help_text, 100);

//$Contents = "<div style=height:1000px;'></div>";


$Script = "<script language='javascript' src='/admin/store/basicinfo.js'></script>";
$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = display_menu();
$P->Navigation = "HOME > 상점관리 > 쿠폰정책";
$P->strContents = $Contents;
echo $P->PrintLayOut();



function CouponRuleSelectBox($publish_ix,$select_name,$property=""){
	global $arr_couponList;
	$mdb = new Database;

	if($arr_couponList !== false && !count($arr_couponList))	{
		$sql = "select cp.*,c.cupon_kind
					from ".TBL_SHOP_CUPON."  c inner join ".TBL_SHOP_CUPON_PUBLISH." cp on c.cupon_ix = cp.cupon_ix
					where  c.cupon_ix > 0 and cp.use_date_type = 3 and ".date("Ymd")." between cp.use_sdate and  cp.use_edate and cp.publish_type= '2'  order by cp.regdate desc";
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
	$mstring = "<select name='$select_name' id='$select_name' style='width:290px;font-size:11px;' $property>";
	$mstring .= "<option value=''>발행쿠폰 전체 목록</option>";
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
				$priod_str = $use_date_type." : ".ChangeDate($_val['use_sdate'],"Y-m-d")." ~ ".ChangeDate($_val['use_edate'],"Y-m-d")." ";
			break;
		}
		$mstring .= "<option value='".$_val['publish_ix']."'".($_val['publish_ix'] == $publish_ix ? " selected":"")." title='".$priod_str."'>".$_val['cupon_kind']." ".$priod_str."</option>";
	}
	if(!$arr_couponList)	{
		$mstring .= "<option value=''>".$msg."</option>";
	}
	$mstring .= "</select>";
	return $mstring;
}

?>