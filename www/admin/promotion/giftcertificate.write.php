<?
include("../class/layout.class");


$db = new Database;

$db->query("SELECT * FROM shop_gift_certificate where gc_ix= '$gc_ix'");
$db->fetch();

if($db->total){
	$gc_ix = $db->dt[gc_ix];
	$gift_certificate_name = $db->dt[gift_certificate_name];
	$gift_amount = $db->dt[gift_amount];
	$gift_prefix_code = $db->dt[gift_prefix_code];
	$gift_type = $db->dt[gift_type];

	$create_cnt = $db->dt[create_cnt];
	$gift_start_date = $db->dt[gift_start_date];
	$gift_end_date = $db->dt[gift_end_date];
	$gift_way = $db->dt[gift_way];

	$act = "update";

	$loadScript = "
	$(document).ready(function(){
		$('select[name=gift_way]').prop('disabled', true);
		$('input[name=create_cnt]').prop('disabled', true);
		$('input[name=gift_prefix_code]').prop('disabled', true);
	})
	";

}else{
	$act = "insert";
	$start_date = "";
	$end_date = "";
	$gift_type = "R";

	$next10day = mktime(0, 0, 0, date("m")  , date("d")+10, date("Y"));

	$gift_start_date = date("Y-m-d");
	$gift_end_date = date("Y-m-d",$next10day);
}

$Script = "
<Script Language='JavaScript'>

".$loadScript."

function SubmitX(frm){

	if(!CheckFormValue(frm)){
		return false;
	}

	/*
	if(frm.event_gift.value.length < 1){
		alert('상품권 타입을 선택해주세요');
		frm.event_gift.focus();
		return false;
	}

	if(frm.event_gift_num.value.length < 1){
		alert('상품권 발행회차를 선택해주세요');
		frm.event_gift_num.focus();
		return false;
	}
	*/

	return true;
}

function changeGiftType(){
//alert($('#gift_type_r').attr('checked'));
	if($('#gift_type_r').attr('checked')){
		$('#gifttype_r').show();
		$('#gifttype_c').hide();

		$('.gift_code_area').hide();
		
		if($('select[name=gift_way]').val() == 1){
			$('input[name=gift_prefix_code]').attr('title','Pre Fix');
			$('input[name=gift_prefix_code]').attr('size','4');
			$('input[name=gift_prefix_code]').attr('maxlength','4');
			$('input[name=gift_prefix_code]').attr('validation','true');
			$('input[name=gift_prefix_code]').show();
			$('.gift_prefix_code_area').show();
		}else{
			$('input[name=gift_prefix_code]').attr('validation','false');
			$('input[name=gift_prefix_code]').hide();
			$('.gift_prefix_code_area').hide();
		}

		$('#gifttype_cnt').show();
		$('input[name=create_cnt]').attr('validation','true');
		$('.appoint_publish_ix').attr('validation','false');
	}else if($('#gift_type_c').attr('checked')){
		$('#gifttype_c').show();
		$('#gifttype_r').hide();

		$('.gift_code_area').hide();
		
		if($('select[name=gift_way]').val() == 1){
			$('input[name=gift_prefix_code]').attr('title','Pre Fix');
			$('input[name=gift_prefix_code]').attr('size','4');
			$('input[name=gift_prefix_code]').attr('maxlength','4');
			$('input[name=gift_prefix_code]').attr('validation','true');
			$('input[name=gift_prefix_code]').show();
			$('.gift_prefix_code_area').show();
		}else{
			$('input[name=gift_prefix_code]').attr('validation','false');
			$('input[name=gift_prefix_code]').hide();
			$('.gift_prefix_code_area').hide();
		}

		$('#gifttype_cnt').show();
		$('input[name=create_cnt]').attr('validation','true');
		$('.appoint_publish_ix').attr('validation','true');
	}else if($('#gift_type_u').attr('checked')){
		$('#gifttype_c').show();
		$('#gifttype_r').hide();

		$('.gift_code_area').show();
		$('.gift_prefix_code_area').hide();
		
		if($('select[name=gift_way]').val() == 1){
			$('input[name=gift_prefix_code]').attr('title','Gift Code');
			$('input[name=gift_prefix_code]').attr('size','20');
			$('input[name=gift_prefix_code]').attr('maxlength','20');
			$('input[name=gift_prefix_code]').attr('validation','true');
			$('input[name=gift_prefix_code]').show();
		}else{
			$('input[name=gift_prefix_code]').attr('validation','false');
			$('input[name=gift_prefix_code]').hide();
		}

		$('#gifttype_cnt').hide();
		$('input[name=create_cnt]').attr('validation','false');
		$('.appoint_publish_ix').attr('validation','true');
	}
}
 
function copycupon(){
	var tbody = $('#cupon_table tbody');  

	var newRow = tbody.find('tr.cupon_tr:last').clone(true).appendTo(tbody);  
	newRow.find('.appoint_publish_ix').val('');
}

function removecupon(this_obj){
	if($('#cupon_table tbody').find('tr.cupon_tr').length > 1){
		this_obj.parent().parent().remove();
	}else{
		this_obj.parent().parent().find('select').val('');
	}
}

function change_gift_check(obj){
	var bool=false;
	var _index = $('select[name^=appoint_publish_ix]').index(obj);
	$('select[name^=appoint_publish_ix]').each(function(i){
		//if(($(this).attr('name')!=obj.attr('name')) && ($(this).val()==obj.val()) && (obj.val() !='')){
		if((_index != i) && ($(this).val()==obj.val()) && (obj.val() !='')){
			//alert($(this).attr('name')+' : '+obj.attr('name'));
			bool = true;
		}
	})
	
	if(bool){
		alert('같은 쿠폰은 선택할수 없습니다.');
		obj.val('');
	}
}

function filterNum(str) {
	if(str){
		return str.replace(/[^0-9a-zA-Z]/g, '');
	}else{
		return '';
	}
}

function checkCreateAmount(obj){
	if(obj.val() > 10000){
		alert('오프라인 상품권의 1회 최대 발행은 10,000장 까지 입니다');
		obj.val('10000');
		return false;
	} 
}

function showSelfArea(obj){
	if($(obj).val() == 1){
		$('.self_make').show();
		if($('input[name=gift_type]:checked').val() == 'U'){
			$('input[name=gift_prefix_code]').attr('title','Gift Code');
			$('input[name=gift_prefix_code]').attr('size','20');
			$('input[name=gift_prefix_code]').attr('maxlength','20');
			$('input[name=gift_prefix_code]').attr('validation','true');
			$('input[name=gift_prefix_code]').show();
			$('.gift_prefix_code_area').hide();
		}else{
			$('input[name=gift_prefix_code]').attr('title','Pre Fix');
			$('input[name=gift_prefix_code]').attr('size','4');
			$('input[name=gift_prefix_code]').attr('maxlength','4');
			$('input[name=gift_prefix_code]').attr('validation','true');
		}
	}else{
		$('.self_make').hide();
		$('input[name=gift_prefix_code]').attr('validation','false');
	}
}

</Script>";



$Contents = "
<table width='100%' border='0' align='left'>
 <tr>
	<td align='left' colspan=6 > ".GetTitleNavigation("오프라인 상품권 생성", "프로모션(마케팅) > 오프라인 상품권 생성 ")."</td>
</tr>
  <tr>
    <td>
      <div id='TG_INPUT' style='position: relative; display: block;'>
        <form name='INPUT_FORM' method='post' onSubmit=\"return SubmitX(this)\" action='giftcertificate.act.php' target='iframe_act'><input type='hidden' name=act value='$act'><input type='hidden' name=gc_ix value='$gc_ix'>
        <table border='0' width='100%' cellspacing='1' cellpadding='0'>
          <tr>
            <td bgcolor='#6783A8'>
              <table border='0' cellspacing='0' cellpadding='0' width='100%'>
                <tr>
                  <td bgcolor='#ffffff'>
                    <table border='0' cellpadding=2 cellspacing=0 width='100%' class='input_table_box'>
						<col width='15%' />
						<col width='*' />
                      <tr height=27>
                      	<td class='input_box_title' nowrap>상품권 이름 <img src='".$required3_path."'></td>
                        <td class='input_box_item'>
                        <input type='text' name='gift_certificate_name' class='textbox' value='$gift_certificate_name' validation='true' title='상품권 이름' style='width:400px;' >
						<span class='red'>* 고객에게 노출되는 명칭입니다. (단, 쿠폰 지급 상품권의 경우에는 쿠폰 이름이 노출됨)</span>
						</td>
                      </tr>
                      <tr height=27>
                      	<td class='input_box_title' nowrap>상품권 설명 </td>
                        <td class='input_box_item'>
							<input type='text' name='memo' class='textbox' value='".$db->dt[memo]."' validation='true' title='상품권 설명' style='width:400px;' >
							<span class='blue'>* 관리자에게만 노출되는 명칭입니다</span>
						</td>
                      </tr>
					  <tr height=27>
                      	<td class='input_box_title'>상품권 유형 <img src='".$required3_path."'> </td>
                        <td class='input_box_item' style='padding: 10px;'>
							<div stlye='padding-top: 10px;'>";
						  foreach($_GIFT_TYPE as $key => $value){
							$Contents .= "<input type='radio' name='gift_type' id='gift_type_".strtolower($key)."' class='gift_type' onclick=\"changeGiftType()\" value='".$key."' ".($gift_type == $key ? "checked":"")." validation=true title='상품권 유형' ".($act == "update" ? "disabled":"")."> <label for='gift_type_".strtolower($key)."' >".$value."</label> ";
						  }
				$Contents .= "
							</div>
							<table cellpadding=0 cellspacing=0 class='input_table_box' style='margin: 5px;'>
								  <tr height=27>
									<td class='input_box_title' nowrap style='padding-right: 15px;'>시리얼넘버 생성 방법 </td>
									<td class='input_box_item'>
										<select name='gift_way' onchange='showSelfArea(this);'><option value='1' ".($gift_way == 1 ? "selected" : "").">수동</option><option value='2' ".($gift_way == 2 ? "selected" : "").">자동</option></select>

										<input type='text' name='gift_prefix_code' class='textbox self_make' value='$gift_prefix_code' validation='true' title='Pre Fix' size='4' maxlength='4' onkeyup=\"this.value=filterNum(this.value)\"  ".($gift_way == 1 ? "" : "style='display:none;'")."> <span class ='gift_prefix_code_area self_make'>(Pre Fix)</span>

										<span class='blue'>* 수동 선택 시, 시리얼 넘버 중 앞의 4자리를 직접 설정할 수 있습니다</span>
									</td>
								  </tr> 
								  <tr height=27 id='gifttype_cnt'>
									<td class='input_box_title'>생성 개수 </td>
									<td class='input_box_item' style='padding-right: 15px;'>
									<input type='text' name='create_cnt' class='textbox numeric' value='$create_cnt' validation='true' title='생성갯수' onkeyup=\"checkCreateAmount($(this))\" size=10 maxlength=5 >
									 장 생성 (1회 최대 10,000장 생성 가능)
									 <span class='blue'>* 10,000장 이상 생성이 필요한 경우, 여러 번 나누어서 생성해주세요</span>
									</td>
								  </tr>
								  <tr height=27 id='gifttype_r'>
									<td class='input_box_title'>마일리지 지급 금액</td>
									<td class='input_box_item'  >
									<input type='text' name='gift_amount' class='textbox number' value='".$gift_amount."' size=10> 마일리지
									</td>
								  </tr>
								  <tr height=27 id='gifttype_c'>
									<td class='input_box_title'>지급 쿠폰 목록</td>
									<td class='input_box_item' style='padding:10px 10px;'>
										<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff id='cupon_table'>
											<tbody>";

												if($act == "insert"){
													$Contents .= "
													<tr class='cupon_tr'>
														<td nowrap>".giftCouponRuleSelectBox('',"appoint_publish_ix[]","validation=true title='지급쿠폰'")."</td>
														<td style='padding-left:10px;'>
															<img src='../images/".$_SESSION["admininfo"]["language"]."/btn_add.gif' style='cursor:pointer;' onclick=\"copycupon()\" />
															<img src='../images/".$_SESSION["admininfo"]["language"]."/btc_del.gif' style='cursor:pointer;' onclick=\"removecupon($(this))\"/>
														</td>
													</tr>";
												}else{
													$sql = "SELECT * FROM shop_gift_certificate_cupon where gc_ix= '$gc_ix'";
													//echo $sql;
													$db->query($sql);
													if($db->total){
														for($i=0;$i<$db->total;$i++){
															$db->fetch($i);
															$Contents .= "
															<tr class='cupon_tr'>
																<td nowrap>".giftCouponRuleSelectBox($db->dt[gift_cupon_ix],"appoint_publish_ix[]","validation=true title='지급쿠폰'")."</td>
																<td style='padding-left:10px;'>
																	<img src='../images/".$_SESSION["admininfo"]["language"]."/btn_add.gif' style='cursor:pointer;' onclick=\"copycupon()\" />
																	<img src='../images/".$_SESSION["admininfo"]["language"]."/btc_del.gif' style='cursor:pointer;' onclick=\"removecupon($(this))\"/>
																</td>
																<td></td>
															</tr>";
														}
													}else{
														$Contents .= "
													<tr class='cupon_tr'>
														<td nowrap>".giftCouponRuleSelectBox('',"appoint_publish_ix[]","validation=true title='지급쿠폰'")."</td>
														<td style='padding-left:10px;'>
															<img src='../images/".$_SESSION["admininfo"]["language"]."/btn_add.gif' style='cursor:pointer;' onclick=\"copycupon()\" />
															<img src='../images/".$_SESSION["admininfo"]["language"]."/btc_del.gif' style='cursor:pointer;' onclick=\"removecupon($(this))\"/>
														</td>
													</tr>";
													}
												}
											$Contents .= "
											</tbody>
										</table>
										<span class='blue'>* 쿠폰 생성 메뉴에서 ‘오프라인 상품권 발급’으로 등록한 쿠폰이 노출됩니다</span>
										";
										
										

									$Contents .= "

									</td>
								  </tr>
							</table>

							</br>
							<div style='background-color: #efefef;padding: 10px;'>
								<img src='../image/emo_3_15.gif' align=absmiddle > <b>상품권 유형별 설정 가이드</b></br>
									<span>-마일리지 지급 상품권의 경우, 고객이 발급받은 시리얼 넘버를 등록하는 즉시 마일리지가 적립됩니다.</span></br>
									<span>-쿠폰 지급 상품권의 경우, 고객이 발급받은 시리얼 넘버를 등록하는 즉시 쿠폰이 발급됩니다. </span></br>
									<span>-쿠폰 지급 상품권을 생성하시려면, 먼저 ‘프로모션(마케팅)>쿠폰 생성’ 메뉴에서 쿠폰 발급 방식을 ‘오프라인 쿠폰 발급’으로 하여 쿠폰을 생성하신 후, 지급할 쿠폰 리스트에서 해당 쿠폰을 선택해주세요. </span></br>
							</div>
							</br>
						</td>
                      </tr>
					  <tr>
						  <td class='input_box_title' nowrap > <b>상품권 사용기간 <img src='".$required3_path."'></b></td>
						  <td class='input_box_item' style='padding:10px;' >
							".search_date('gift_start_date','gift_end_date',$gift_start_date,$gift_end_date,'N','A')."
						</td> 
					</tr>";



$vdate = date("Ymd", time());
$today = date("Y/m/d", time());
$vyesterday = date("Y/m/d", time()+84600);
$voneweeklater = date("Y/m/d", time()+84600*7);
$vtwoweeklater = date("Y/m/d", time()+84600*14);
$vfourweeklater = date("Y/m/d", time()+84600*28);
$vyesterday = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24);
$voneweeklater = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*7);
$v15later = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*15);
$vfourweeklater = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*28);
$vonemonthlater = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)+1,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v2monthlater = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)+2,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v3monthlater = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)+3,substr($vdate,6,2)+1,substr($vdate,0,4)));


$Contents .= "
					  </table>
					  <table border='0' cellspacing='0' cellpadding='0' width='100%' style='margin-top:5px;'>
						  <tr>
							<td style='text-align:left;'></td>
							<td style='text-align:right;' >
								<table align=right>
									<tr>
										<!--td><input type=checkbox id='next_mode' name='next_mode' value='goon'><label for='next_mode'>계속등록하기</label> </td>
										<td><input type=checkbox id='check_mode' name='check_mode' value='test'><label for='check_mode'>중복확인(등록제외)</label> </td-->
										<td><input type=image src='../image/b_save.gif' border=0> <a href='giftcertificate.php'><img src='../image/b_cancel.gif' border=0 align=absmiddle></a></td>
									</tr>
								</table>
							</td>
						</tr>
					  </table>

                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
        </form>
      </div>
    </td>
  </tr>";

 $help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >생성하고자 하는 상품권명을 입력하신 후 저장 버튼을 누르시면 쿠폰이 생성됩니다. </td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >생성하고자 하는 코드번호는 프리픽스 문자를 입력해서 생성 하실 수 있습니다</td></tr>
	<!--tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >삭제를 원하시는 상품권 내역을 선택하신후 일괄정보 삭제를 클릭하시면 상품권이 삭제됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >상품권를 직접 지급 하고자 하실 경우 회원 이름을 클릭하여 입력하시면 됩니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >적립내역이나 적립금 사용내역이 주문취소 시 적립금 산출에 적용이 되지 않게 됩니다.</td></tr-->
</table>
";


$help_text = HelpBox("상품권등록하기", $help_text);
$Contents .= "
  <tr>
    <td align='left'>

  $help_text

    </td>
  </tr>
</table>

<form name='lyrstat'><input type='hidden' name='opend' value=''></form>
<Script Language='JavaScript'>
 
changeGiftType();
</Script>";




$Script = "<script language='javascript' src='giftcertificate.js'></script>\n<script language='javascript' src='../include/DateSelect.js'></script>\n$Script";
$P = new LayOut();
$P->addScript = $Script;
$P->jquery_use = true;
$P->prototype_use = false;

$P->Navigation = "프로모션(마케팅) > 오프라인 상품권 생성";
$P->title = "오프라인 상품권 생성";
$P->strLeftMenu = promotion_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();

//어라운지용 차후 삭제!
function giftCouponRuleSelectBox($publish_ix,$select_name,$property=""){
	global $arr_couponList;
	$mdb = new Database;

	//if($arr_couponList !== false && !count($arr_couponList))	{
	/********** 회원전용 쿠폰을 위해 수정함 ***********/
	if($select_name=="member_publish_ix") $publish_type="3";
	else if (substr_count($select_name,"appoint_publish_ix") > 0) $publish_type="1";
	else $publish_type="2";


	//2014-07-22 차후 조건 받아서(강태웅주임)  진행 HONG
	//and ((cp.use_date_type!='9' AND '".date("Ymd")."' between cp.use_sdate and cp.use_edate) OR cp.use_date_type=9 OR cp.use_date_type=2)

	$arr_couponList=array();

	//and cp.publish_type= '".$publish_type."' 2014-08-14 차후 조건 받아서(강태웅주임)  진행 HONG
	/********** 회원전용 쿠폰을 위해 수정함 kbk 12/06/13 ***********/ //use_sdate 에 date_format 한 이유는 오라클때문에~
		$sql = "select cp.*,c.cupon_kind
					from ".TBL_SHOP_CUPON."  c inner join ".TBL_SHOP_CUPON_PUBLISH." cp on c.cupon_ix = cp.cupon_ix
					where  issue_type='5' and c.cupon_ix > 0 and cp.is_use='1' order by cp.regdate desc";// and cp.use_date_type = 3 //특정조건만 나오게 하는 원인을 몰라서 일단 다 나오게 수정함 with 신실장님 kbk 12/06/11
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
	$mstring = "<select name='$select_name' class='appoint_publish_ix' style='font-size:12px;width:300px;' $property onchange=\"change_gift_check($(this))\">";
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
					$priod_str = $use_date_type." : ".date("Y-m-d", strtotime($_val['use_sdate']))." ~ ".date("Y-m-d", strtotime($_val['use_edate']))." ";
				break;
			}
			$mstring .= "<option value='".$_val['publish_ix']."'".($_val['publish_ix'] == $publish_ix ? " selected":"")." title='".$priod_str."'>[".$_val['cupon_no']."] ".$_val['publish_name']."  ".$priod_str."</option>";
		}
	}
	if(!$arr_couponList)	{
		$mstring .= "<option value=''>".$msg."</option>";
	}
	$mstring .= "</select>";
	return $mstring;
}

?>