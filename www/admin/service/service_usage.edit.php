<?
include("../class/layout.class");
include_once("service.lib.php");

$db1 = new MySQL;
$db2 = new MySQL;
$db3 = new MySQL;



$ctgr ="orders";

$Contents = "

<table width='100%'>
<tr>
    <td align='left'> ".GetTitleNavigation("서비스이용정보수정", "서비스관리 > 서비스이용정보수정 ")."</td>
</tr>
</table>  ";

		
		$sql = "SELECT si.*,id,UNIX_TIMESTAMP(si.start_date) AS unix_start_date,UNIX_TIMESTAMP(si.end_date) AS unix_end_date, AES_DECRYPT(UNHEX(cd.mail),'".$db->ase_encrypt_key."') as mail,AES_DECRYPT(UNHEX(tel),'".$db->ase_encrypt_key."') as tel,AES_DECRYPT(UNHEX(pcs),'".$db->ase_encrypt_key."') as pcs 
		FROM service_info si 
		left join ".TBL_COMMON_USER." c on c.code = si.code 
		LEFT JOIN ".TBL_COMMON_MEMBER_DETAIL." cd ON c.code=cd.code 
		WHERE si.si_ix = '".$si_ix."' ";

		//echo $sql;
		$db2->query($sql);
		$db2->fetch();

		$ucode = $db2->dt[code];
		switch($db2->dt[si_status]) {
			case "SI" : $status="사용중";
			break;
			case "CC" : $status="사용취소";
			break;
		}

		$now_time=time();
		if($now_time>$db2->dt["unix_end_date"]) {
			$status="사용만료";
		}
		$delete = "[<a href=\"javascript:act('delete','$Obj');\">삭제</a>]";

		if($db2->dt[unix_start_date]){
			$sDate = date("Y/m/d", $db2->dt[unix_start_date]);
			$sHour=substr($db2->dt[start_date],11,8);
		}else{
			$sDate = date("Y/m/d");
			$sHour=substr($db2->dt[start_date],11,8);

		}

		if($db2->dt[unix_end_date]){
			$eDate = date("Y/m/d", $db2->dt[unix_end_date]);
			$eHour=substr($db2->dt[end_date],11,8);
		}else{
			$eDate = date("Y/m/d");
			$eHour=substr($db2->dt[end_date],11,8);

		}


$Contents = $Contents."

      <div id='TG_order_edit' style='position: relative;width:100%;'>
		<form name='order_info_edit' method='post' onSubmit='return CheckFormValue(this)'  action='service_usage.act.php' target='act'>
		<input type=hidden name=si_ix value='$si_ix'>
		<input type=hidden name=act value='serviceinfo_update'>
		<input type=hidden name=bstatus value='".$db2->dt[si_status]."'>
        <table border='0' width='100%' cellspacing='1' cellpadding='0'>
          <tr>
            <td >
              <table border='0' cellspacing='1' cellpadding='15' width='100%'>
                <tr>
                  <td bgcolor='#F8F9FA'>";

$zipcode = split("-",$db2->dt[zip]);

					$Contents = $Contents."

					<div style='padding:5px'><img src='../images/dot_org.gif' align='absmiddle'> <b class='middle_title'>이용정보</b></div>

					<table border='0' width='100%' cellspacing='1' cellpadding='0'>
						<tr>
							<td >
								<table border='0' width='100%' cellspacing='0' cellpadding='0' class='input_table_box' style='width:100%;'>
								<col width='15%' />
								<col width='35%' />
								<col width='15%' />
								<col width='35%' />
									<tr height=25 bgcolor='#ffffff' >
										<td class='input_box_title'>서비스종류</td>
										<td class='input_box_item'>&nbsp;".print_service_code_name($db2->dt[s_kind])."</td>
										<td class='input_box_title'>패키지명</td>
										<td class='input_box_item'>&nbsp;".$db2->dt[pname]." (".$status.")</td>
									</tr>
									<tr bgcolor='#ffffff' >
										<td class='input_box_title' >이용자이름</td>
										<td class='input_box_item'>&nbsp;".$db2->dt[name]."</td>
										<td class='input_box_title'>이용자아이디</td>
										<td class='input_box_item'>&nbsp;".$db2->dt[id]."</td>
									</tr>
									<tr bgcolor='#ffffff' >
										<td class='input_box_title'>이용자메일</td>
										<td class='input_box_item' colspan='3'>&nbsp;".$db2->dt[mail]."</td>
									</tr>
									<tr bgcolor='#ffffff' >
										<td class='input_box_title'>이용자전화</td>
										<td  class='input_box_item'>&nbsp;".$db2->dt[tel]."</td>
										<td class='input_box_title'>이용자핸드폰</td>
										<td  class='input_box_item'>&nbsp;".$db2->dt[tel]."</td>
									</tr>
									<tr>
										<td class='input_box_title'><b>이용시작일</b></td>
										<td class='input_box_item'>&nbsp;
											<SELECT name=vFromYY ></SELECT> 년 <SELECT name=vFromMM></SELECT> 월 <SELECT name=vFromDD></SELECT> 일
											<input type='hidden' name='s_hour' value='".$sHour."' />
										</td>
										<td class='input_box_title'><b>이용만료일</b></td>
										<td class='input_box_item'>&nbsp;
											<SELECT name=vToYY></SELECT> 년 <SELECT name=vToMM></SELECT> 월 <SELECT name=vToDD></SELECT> 일
											<input type='hidden' name='e_hour' value='".$eHour."' />
										</td>
									</tr>
									<tr >
										<td class='input_box_title'>이용신청내역</td>
										<td class='input_box_item' colspan='3' style='padding:10px 0 10px 10px'>
										<div style='width:100%;height:200px;overflow:auto;'>";
										if($admininfo[admin_level] == 9){
											$sql = "select o.oid, o.method, od.*
															from service_order o left join service_order_detail od on o.oid = od.oid
															where od.si_ix ='$si_ix' and od.status in ('IC') AND od.s_kind='".$db2->dt[s_kind]."' AND od.s_type='".$db2->dt[s_type]."' order by od.regdate asc";
											//echo nl2br($sql);
											$db3->query($sql);
										}else if($admininfo[admin_level] == 8){
											$sql = "select o.oid, o.method, od.*
															from service_order o left join service_order_detail od on o.oid = od.oid
															where od.si_ix ='$si_ix' and od.status in ('IC') AND od.s_kind='".$db2->dt[s_kind]."' AND od.s_type='".$db2->dt[s_type]."' order by od.regdate asc";

											$db3->query($sql);
										}
										for($j = 0; $j < $db3->total; $j++)
										{
											$db3->fetch($j);

											$sattle_method=$db3->dt[method];

											if ($sattle_method == ORDER_METHOD_CARD)
											{
											//	if($db1->dt[bank] == ""){
													$method = "카드결제";
											//	}else{
											//		$method = $db1->dt[bank];
											//	}
											}elseif($sattle_method == ORDER_METHOD_BANK){
												$method = "무통장입금";
											}elseif($sattle_method == ORDER_METHOD_PHONE){
												$method = "전화결제";
											}elseif($sattle_method == ORDER_METHOD_AFTER){
												$method = "후불결제";
											}elseif($sattle_method == ORDER_METHOD_VBANK){
												$method = "가상계좌";
											}elseif($sattle_method == ORDER_METHOD_ICHE){
												$method = "계좌이체";
											}elseif($sattle_method == ORDER_METHOD_ASCROW){
												$method = "가상계좌[에스크로]";
											}elseif($sattle_method == ORDER_METHOD_SAVEPRICE){
												$method = "예치금결제";
											}
											$Contents .= "<span class=small>".$db3->dt[oid]." (".$method.") 시작일 : ".substr($db3->dt[start_date],0,10)." 만료일 : ".substr($db3->dt[end_date],0,10)." 사용기간 : ".$db3->dt[period]."개월 신청일 : ".substr($db3->dt[regdate],0,10)."</span><br>";
										}


					$Contents .= "		</div>
										</td>
									</tr>
								</table>
							</td>
						</tr>";


					if($admininfo[mall_use_multishop] && $admininfo[admin_level] ==  9){
					$Contents .= "
						<tr height=30>
							<td class='small' style='padding:3px;line-height:150%'>
							<table width=100%>
								<tr>
									<td>
										<!-- - 입점업체 상품 모두가 상태변경이 되었을때 상태변경을 하시면 됩니다.-->
										".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."
									</td>
									<td align=right>
									<a href='javascript:history.back();'><img src='../images/".$admininfo["language"]."/btn_back.gif' border='0' align=absmiddle></a> ";
									if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
									$Contents .= "<input type=image src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 style='cursor:pointer;' align=absmiddle> ";
									}
									if($admininfo[admin_level] == 9 && $admininfo["language"] == 'korea' ){
										if($admininfo[sattle_module] == "inicis"){
											$Contents .= " <a href='https://iniweb.inicis.com/' target='_blank'><img src='../images/".$admininfo["language"]."/btn_pg_inisis.gif' align=absmiddle border=0  ></a>";
										}else if($admininfo[sattle_module] == "allthegate"){
											$Contents .= " <a href='https://www.allthegate.com/login/r_login.jsp' target='_blank'><img src='../images//btn_pg_admin.gif' align=absmiddle border=0  ></a>";
										}else if($admininfo[sattle_module] == "lgdacom"){
											$Contents .= " <a href='http://pgweb.lgdacom.net' target='_blank'><img src='../images/".$admininfo["language"]."/btn_pg_lgdacom.gif' align=absmiddle border=0  ></a>";
										}else if($admininfo[sattle_module] == "kcp"){
											$Contents .= " <a href='https://admin.kcp.co.kr' target='_blank'><img src='../images/".$admininfo["language"]."/btn_pg_kcp.gif' align=absmiddle border=0  ></a>";
										}
									}
								$Contents .= "
									</td>
								</tr>
							</table>
							</td>
						</tr>";

					}
					$Contents .= "
					</table>

				</td>
			</tr>
		</table>
		</form>
		</div>
	</td>
</tr>
</table>

";
/*
$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >주문에 대한 정보를 확인 및 수정할수 있는 페이지 입니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >처리상태가 반품완료 및 취소완료시에는 상품을 변경할수 없습니다</td></tr>
</table>
";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');


$help_text = HelpBox("서비스이용정보수정", $help_text);
$Contents .= $help_text;


$Contents = $Contents."
<form name='lyrstat'>
	<input type='hidden' name='opend' value=''>
</form>";

$Script = "
<script language='javascript' >
function memoDelete(oid, om_ix){
	if(confirm(language_data['service_orders.edit.php']['A'][language])){//해당 상담내역을 정말로 삭제 하시겠습니까?
		window.frames['iframe_act'].location.href='service_orders_memo.act.php?act=memo_delete&oid='+oid+'&om_ix='+om_ix;
	}
}


//콤마표현 없는 정수만입력
function onlyEditableNumber(obj){
 var str = obj.value;
 str = new String(str);
 var Re = /[^0-9]/g;
 str = str.replace(Re,'');
 obj.value = str;
}
$(document).ready(function() {
	MessageBoxView();
});
function MessageBoxView(){
	var offsetX = 20;
	var offsetY = 10;
	
	/*$('a.messagebox').hover(function(e){
		//mouse on
		var msgbox = $(this).attr('messagebox_id');
		//alert($(msgbox).parent().html());
		$('#'+msgbox).css('display','block');
		$('#'+msgbox).css('top', e.pageY + offsetY).css('left', e.pageX + offsetX).appendTo('body');
	}, function(){
		//mouse off
		//$('.messagebox_contents').remove();
		$('.messagebox_contents').css('display','none');
	});
	
	$('a.messagebox').mousemove(function(e){
		$('.messagebox_contents').css('top', e.pageY + offsetY).css('left', e.pageX + offsetX);
	});*/
	$('a.messagebox').click(function(e){
		var msgbox = $(this).attr('messagebox_id');
		//alert($(msgbox).parent().html());
		$('#'+msgbox).css('display','block');
		$('#'+msgbox).css('top', e.pageY + offsetY).css('left', e.pageX + offsetX).appendTo('body');
		$('#'+msgbox+' .messagebox_x').click(function() {
			$('#'+msgbox).css('display','none');
		});
	});
		
}
</script>
<style type='text/css'>
a img {
	border: none;
}

.messagebox_contents {
	position: absolute;
	padding: .5em;
	background: #e3e3e3;
	border: 1px solid;
}
</style>
";


$P = new LayOut();
$P->OnloadFunction = "onLoadEdit('".$sDate."','".$eDate."');";//MenuHidden(false);
$P->strLeftMenu = service_menu();
$P->addScript = $Script."<script language='javascript' src='service_usage.js'></script><script language='javascript' src='../include/DateSelect.js'></script>";
$P->Navigation = "서비스관리 > 서비스이용정보수정";
$P->title = "서비스이용정보수정";
$P->strContents = $Contents;


echo $P->PrintLayOut();
?>