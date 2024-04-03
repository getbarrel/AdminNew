<?
include("../class/layout.class");


$db = new Database;

if($db->dbms_type == "oracle"){
	$db->query("SELECT uid_,to_char(start_date,'YYYY-MM-DD') as start_date ,to_char(end_date,'YYYY-MM-DD') as end_date,amount,limit_amount,prod_name FROM shop_order_gift where uid_= '$uid'");
}else{
	$db->query("SELECT * FROM shop_order_gift where uid= '$uid'");
}

$db->fetch();

if($db->total){


	if($db->dbms_type == "oracle"){
		$uid = $db->dt[uid_];
	}else{
		$uid = $db->dt[uid];
	}

	$prod_name = $db->dt[prod_name];
	$amount = $db->dt[amount];
	$limit_amount = $db->dt[limit_amount];
	$start_date = $db->dt[start_date];
	$end_date = $db->dt[end_date];

	$act = "update";

	$sDate = date("Y/m/d", mktime(0, 0, 0, substr($db->dt[start_date],5,2)  , substr($db->dt[start_date],8,2), substr($db->dt[start_date],0,4)));
	$eDate = date("Y/m/d",mktime(0, 0, 0, substr($db->dt[end_date],5,2)  , substr($db->dt[end_date],8,2), substr($db->dt[end_date],0,4)));

	$startDate = $start_date;
	$endDate = $end_date;

}else{
	$act = "insert";
	$start_date = "";
	$end_date = "";


	$next10day = mktime(0, 0, 0, date("m")  , date("d")+10, date("Y"));

//	$sDate = date("Y/m/d");
	$sDate = date("Y/m/d");
	$eDate = date("Y/m/d",$next10day);

	$startDate = date("Ymd");
	$endDate = date("Ymd",$next10day);
}


$Script = "
<Script Language='JavaScript'>
function SubmitX(frm){
	return true;
}




function init(){
	var frm = document.INPUT_FORM;


	onLoad('$sDate','$eDate');
}
</Script>";



$Contents = "
<form name='INPUT_FORM' method='post' onSubmit=\"return SubmitX(this)\" action='order_gift.act.php'><input type='hidden' name=act value='$act'><input type='hidden' name=uid value='$uid'>
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
 <tr>
	<td align='left' colspan=6 > ".GetTitleNavigation("구매금액별 사은품관리", "프로모션/전시 > 구매금액별 사은품관리 ")."</td>
</tr>
  <tr>
    <td>
      <div id='TG_INPUT' style='padding:5px 0px 5px 0px ;position: relative; display: block;'>
        <table class='box_shadow' style='width:100%;height:100%;' cellpadding=0 cellspacing=0>
			<tr>
				<th class='box_01'></th>
				<td class='box_02'></td>
				<th class='box_03'></th>
			</tr>
			<tr>
				<th class='box_04'></th>
				<td class='box_05'  valign=top style='padding:2px 5px 2px 2px'>
                    <table border='0' cellpadding=3 cellspacing=0 width='100%' class='search_table_box'>
                      <col width='20%'>
					  <col width='*'>
					  <tr height=28>
                        <td  bgcolor=#efefef align=left style='padding-left:10px;' class='search_box_title' nowrap> 사은품명</td>
                        <td class='search_box_item'><input class='textbox' type='text' name='prod_name' value='".$db->dt[prod_name]."' maxlength='50' style='width:70%'></td>
                      </tr>
                      <tr height=27 >
						  <td class='search_box_title' bgcolor='#efefef' align=left style='padding-left:10px;' nowrap> 적용기간</td>
						  <td class='search_box_item' align=left >
							<table cellpadding=0 cellspacing=0 border=0 bgcolor=#ffffff>
								<tr>
									<TD width=220 nowrap><SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromYY ></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromMM></SELECT> 월 <SELECT name=FromDD></SELECT> 일 </TD>
									<TD width=30 align=center> ~ </TD>
									<TD width=230 nowrap><SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToYY></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToMM></SELECT> 월 <SELECT name=ToDD></SELECT> 일</TD>
								</tr>
							</table>
						  </td>
						</tr>
                      <tr height=27>
                      	<td class='search_box_title' bgcolor=#efefef align=left style='padding-left:10px;' nowrap> 증정금액범위 </td>
                        <td class='search_box_item'  >
                       최소금액 <input type='text' class='textbox number' name='amount' value='$amount' size=10 > ~ 최대금액 <input type='text' class='textbox number' name='limit_amount' value='$limit_amount' size=10> * 최대금액의 제한이 없을시 0 을 입력하시기 바랍니다.
						</td>
                      </tr>
                  </table>
                  </td>
				<th class='box_06'></th>
			</tr>
			<tr>
				<th class='box_07'></th>
				<td class='box_08'></td>
				<th class='box_09'></th>
			</tr>
		</table>
      </div>
    </td>
  </tr>
  <tr>
	<td colspan=3 align=center>
		 <table border='0' cellpadding=0 cellspacing=0 >
		<col width='50%'>
		<col width='50%'>
		 <tr>
			<td align='right'>
				<input type=image src='../image/b_save.gif' border=0>
			</td>
			<td align='left' style='padding-left:5px;'>
				<a href='order_gift.list.php'><img src='../image/b_cancel.gif' border=0></a>
			</td>
		</tr>
		</table>
	</td>
  ";
 $help_text = " - 구매금액별 사은품 정보를 입력해주세요";


$help_text = HelpBox("구매금액별 사은품 관리", $help_text);
$Contents .= "
  <tr>
    <td align='left'>

  $help_text

    </td>
  </tr>
</table>
</form>

<form name='lyrstat'><input type='hidden' name='opend' value=''></form>
<Script Language='JavaScript'>
init()
</Script>";




$Script = "<script language='javascript' src='order_gift.js'></script>\n<script language='javascript' src='../include/DateSelect.js'></script>\n$Script";
$P = new LayOut();
$P->addScript = $Script;
$P->Navigation = "프로모션/전시 > 사은품관리";
$P->title = "사은품관리";
$P->strLeftMenu = display_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>