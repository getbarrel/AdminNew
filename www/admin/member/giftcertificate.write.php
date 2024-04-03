<? 
include("../class/layout.class");


$db = new Database;
$db->query("SELECT * FROM shop_gift_certificate where uid= '$uid'");
$db->fetch();

if($db->total){
	$uid = $db->dt[uid];
	$prod_name = $db->dt[prod_name];
	$amount = $db->dt[amount];
	$limit_amount = $db->dt[limit_amount];
	$start_date = $db->dt[gift_start_date];
	$end_date = $db->dt[gift_end_date];
	
	$act = "update";
	
	$sDate = date("Y/m/d", mktime(0, 0, 0, substr($db->dt[gift_start_date],5,2)  , substr($db->dt[gift_start_date],8,2), substr($db->dt[gift_start_date],0,4)));
	$eDate = date("Y/m/d",mktime(0, 0, 0, substr($db->dt[gift_end_date],5,2)  , substr($db->dt[gift_end_date],8,2), substr($db->dt[gift_end_date],0,4)));
	
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
<table width='100%' border='0' align='left'>
 <tr>
	<td align='left' colspan=6 > ".GetTitleNavigation("상품권등록하기", "회원관리 > 상품권등록하기 ")."</td>
</tr>
  <tr>
    <td>
      <div id='TG_INPUT' style='position: relative; display: block;'>
        <form name='INPUT_FORM' method='post' onSubmit=\"return SubmitX(this)\" action='giftcertificate.act.php' target='act'><input type='hidden' name=act value='$act'><input type='hidden' name=uid value='$uid'>
        <table border='0' width='100%' cellspacing='1' cellpadding='0'>
          <tr>
            <td bgcolor='#6783A8'>
              <table border='0' cellspacing='0' cellpadding='0' width='100%'>
                <tr>
                  <td bgcolor='#ffffff'>
                    <table border='0' cellpadding=3 cellspacing=0 width='100%'>
                      <!-- tr height=1><td colspan=2 background='../image/dot.gif'></td></tr>
                      <tr height=28>
                        <td width='20%' bgcolor=#efefef align=left style='padding-left:10px;' nowrap><img src='../image/ico_dot.gif' align=absmiddle> 상품권번호</td>
                        <td>
                        <textarea class='input' type='text' name='gift_code' style='padding:3px;height:100px;width:200'>".$db->dt[gift_code]."</textarea>
                        </td>
                      </tr //-->
                      <tr height=1><td colspan=2 background='../image/dot.gif'></td></tr>
                      <tr height=27>
                      	<td bgcolor=#efefef align=left style='padding-left:10px;' nowrap><img src='../image/ico_dot.gif' align=absmiddle> 시리얼자릿수 </td>
                        <td  >
                        <input type='text' name='length' value='16' size=2 maxlength=2 readonly>
												</td>
                      </tr>
                      <tr height=1><td colspan=2 background='../image/dot.gif'></td></tr>
                      <tr height=27>
                      	<td bgcolor=#efefef align=left style='padding-left:10px;' nowrap><img src='../image/ico_dot.gif' align=absmiddle> 이벤트/상품권 </td>
                        <td  >
                          <select name='gift_type' onChange='javascript:Event_or_Gift();' class='form'>
                            <option value=''>선택하세요</option>
                            <option value='E'>이벤트</option>
                            <option value='G'>상품권</option>
                          </select>
                        </td>
                      </tr>
                      <tr height=1><td colspan=2 background='../image/dot.gif'></td></tr>
                      <tr height=27>
                      	<td bgcolor=#efefef align=left style='padding-left:10px;' nowrap><img src='../image/ico_dot.gif' align=absmiddle> 회차 </td>
                        <td  >
                        <input type='text' name='event_gift_num' value='' size=5 maxlength=5 disabled>
                        (이벤트일때만 활성화 됩니다. 중복되는 회차가 없을경우 공백으로 두면은 자동으로 회차번호가 생성됩니다.)
												</td>
                      </tr>
                      <tr height=1><td colspan=2 background='../image/dot.gif'></td></tr>
                      <tr height=27>
                      	<td bgcolor=#efefef align=left style='padding-left:10px;' nowrap><img src='../image/ico_dot.gif' align=absmiddle> 생성갯수 </td>
                        <td  >
                        <input type='text' name='endLoop' value='' size=4 maxlength=4>
                        (5천장 이상은 나누어서 생성하세요)
												</td>
                      </tr>
                      <tr height=1><td colspan=2 background='../image/dot.gif'></td></tr>
                      <tr height=27 >
									      <td bgcolor='#efefef' align=left style='padding-left:10px;' nowrap><img src='../image/ico_dot.gif' align=absmiddle> 사용가능 기간</td>
									      <td align=left >
									      	<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff>		
														<tr>					
															<TD width=210 nowrap><SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromYY ></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromMM></SELECT> 월 <SELECT name=FromDD></SELECT> 일 </TD>
															<TD width=20 align=center> ~ </TD>
															<TD width=210 nowrap><SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToYY></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToMM></SELECT> 월 <SELECT name=ToDD></SELECT> 일</TD>					
														</tr>		
													</table>	
									      </td>			
									    </tr>		    
		   
                      <tr height=1><td colspan=2 background='../image/dot.gif'></td></tr>
                      <tr height=27>
                      	<td bgcolor=#efefef align=left style='padding-left:10px;' nowrap><img src='../image/ico_dot.gif' align=absmiddle> 상품권금액 </td>
                        <td  >
                        <input type='text' name='gift_amount' value='".$db->dt[gift_amount]."' size=10>원
												</td>
                      </tr>
                      <tr height=1><td colspan=2 background='../image/dot.gif'></td></tr>
                      <tr height=28>
                        <td width='20%' bgcolor=#efefef align=left style='padding-left:10px;' nowrap><img src='../image/ico_dot.gif' align=absmiddle> 메모</td>
                        <td><textarea name='memo' style='padding:3px;width:100%;height:50px;'>".$db->dt[memo]."</textarea></td>
                      </tr>
                    <tr> 
                      <td bgcolor='D0D0D0' height='1' colspan='4'></td>
                    </tr>
                    <tr>
                    	<td><a href='giftcertificate.php'>목록</a></td>
                    	<td colspan=2 align=right style='padding-top:10px;'> 
                    		<table>
	                    		<tr>
	                    			<td><input type=checkbox id='next_mode' name='next_mode' value='goon'><label for='next_mode'>계속등록하기</label> </td>
	                    			<td><input type=checkbox id='check_mode' name='check_mode' value='test'><label for='check_mode'>중복확인(등록제외)</label> </td>
	                    			<td><input type=image src='../image/b_save.gif' border=0> <a href='giftcertificate.php'><img src='../image/b_cancel.gif' border=0></a></td>
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
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >상품등록을 계속 등록하기 위해서는 계속등록하기 체크박스를 클릭한 후 저장을 누르신후 상품권번호만 바꾸시면서 등록하시면 됩니다. </td></tr>
	<!--tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >회원명을 클릭하시면 해당 회원에 대한 상품권을 확인하실수 있습니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >삭제를 원하시는 상품권 내역을 선택하신후 일괄정보 삭제를 클릭하시면 상품권이 삭제됩니다</td></tr>
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
init()
</Script>";




$Script = "<script language='javascript' src='giftcertificate.js'></script>\n<script language='javascript' src='../include/DateSelect.js'></script>\n$Script";
$P = new LayOut();
$P->addScript = $Script;
$P->Navigation = "HOME > 회원관리 > 상품권등록하기";
$P->strLeftMenu = member_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>