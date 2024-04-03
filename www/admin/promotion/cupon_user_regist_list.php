<?
include("../class/layout.class");
include_once("../store/md.lib.php");

$max = 10;

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}


if($cupon_send_type_view == ""){
	$cupon_send_type_view = 2;
}

/*
if($mem_ix){
	$mem_ix_str = " and r.mem_ix = '".$mem_ix."' ";
}
*/
if($mmode == "personalization"){
	$where .= " and cr.mem_ix = '".$mem_ix."' ";
}

if($search_type != "" && $search_text != ""){
	if($search_type=="pid"){
		$where .= " and use_product_type='3' and publish_ix in (select publish_ix from shop_cupon_relation_product where pid='$search_text') ";
	}else{
		$where .= " and $search_type LIKE  '%$search_text%' ";
	}
}

if($b_ix !=""){
	$where .= " and use_product_type='4' and publish_ix in (select publish_ix from shop_cupon_relation_brand where b_ix='$b_ix') ";
}

if($md_code !=""){
	$where .= " and md_code = '".$md_code."' ";
}

if($publish_type !=""){
	$where .= " and publish_type ='".$publish_type."' ";
}

$db = new Database();
//$db->query("Select * from ".TBL_SHOP_CUPON_REGIST."");
/*
	if($cupon_send_type_view == 2){
		 
		$sql = "Select
			count(*) as total
			from (
				select cp.cupon_no, cp.regdate as publish_regdate, cp.publish_ix, r.regdate as regist_regdate, r.open_yn, r.use_yn, c.cupon_kind, r.mem_ix, cp.use_sdate, r.use_date_limit ,use_date_type from ".TBL_SHOP_CUPON_REGIST." r , ".TBL_SHOP_CUPON_PUBLISH." cp , ".TBL_SHOP_CUPON." c  where r.publish_ix = cp.publish_ix  and cp.cupon_ix = c.cupon_ix and open_yn = 1 $where $mem_ix_str order by r.regdate desc
			) r,
			 ".TBL_COMMON_USER." cu , ".TBL_COMMON_MEMBER_DETAIL." cmd
			where r.mem_ix = cu.code and  cu.code = cmd.code $mem_ix_str
			order by r.regist_regdate desc ";

	}else if($cupon_send_type_view == 3){
		 

		$sql = "Select
			count(*) as total
			from (
				select cp.cupon_no, cp.regdate as publish_regdate, cp.publish_ix, r.regdate as regist_regdate, r.open_yn, r.use_yn, c.cupon_kind, r.mem_ix, cp.use_sdate, r.use_date_limit ,use_date_type from ".TBL_SHOP_CUPON_REGIST." r , ".TBL_SHOP_CUPON_PUBLISH." cp , ".TBL_SHOP_CUPON." c  where r.publish_ix = cp.publish_ix  and cp.cupon_ix = c.cupon_ix and use_yn = 1 $where $mem_ix_str order by r.regdate desc
			) r,
			 ".TBL_COMMON_USER." cu , ".TBL_COMMON_MEMBER_DETAIL." cmd
			where r.mem_ix = cu.code and  cu.code = cmd.code $mem_ix_str
			order by r.regist_regdate desc ";

	}else{
	 

		$sql = "Select
			count(*) as total
			from (
				select cp.cupon_no, cp.regdate as publish_regdate, cp.publish_ix, r.regdate as regist_regdate, r.open_yn, r.use_yn, c.cupon_kind, r.mem_ix, cp.use_sdate, r.use_date_limit ,use_date_type from ".TBL_SHOP_CUPON_REGIST." r , ".TBL_SHOP_CUPON_PUBLISH." cp , ".TBL_SHOP_CUPON." c  where r.publish_ix = cp.publish_ix  and cp.cupon_ix = c.cupon_ix and use_yn is not null $where $mem_ix_str order by r.regdate desc
			) r,
			 ".TBL_COMMON_USER." cu , ".TBL_COMMON_MEMBER_DETAIL." cmd
			where r.mem_ix = cu.code and  cu.code = cmd.code $mem_ix_str
			order by r.regist_regdate desc";

	}
*/
/*
$db->query($sql);
$db->fetch();
$total = $db->dt[total];
*/
	$sql = 	"SELECT count(*) as total
			FROM ".TBL_SHOP_CUPON_PUBLISH." cp , ".TBL_SHOP_CUPON_REGIST." cr , ".TBL_COMMON_USER." cu , ".TBL_COMMON_MEMBER_DETAIL." cmd , ".TBL_SHOP_CUPON." c
			where cr.publish_ix = cp.publish_ix 
			and cu.code = cr.mem_ix 
			and cu.code = cmd.code 
			and c.cupon_ix = cp.cupon_ix			 
			$status_str $where
			";

	$db->query($sql);
	$db->fetch();

	$total = $db->dt[total];

	$sql = 	"SELECT cp.regdate as publish_regdate, cp.publish_ix, cr.regdate as regist_regdate, cr.open_yn, cr.use_yn,  cp.cupon_no, cp.cupon_ix, cp.use_date_type, cr.regdate, cr.regist_ix, c.cupon_kind, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as mem_name, cu.id as mem_id, cr.use_sdate,cp.use_edate, cr.use_date_limit as use_date_limit, cr.use_yn , case when use_yn = 1 then '사용완료' else '사용가능' end as use_yn_text , cp.publish_condition_price,cp.publish_limit_price,cp.use_product_type,c.cupon_div,c.cupon_sale_value,c.cupon_sale_type,cp.publish_name ,cr.usedate
			FROM ".TBL_SHOP_CUPON_PUBLISH." cp , ".TBL_SHOP_CUPON_REGIST." cr , ".TBL_COMMON_USER." cu , ".TBL_COMMON_MEMBER_DETAIL." cmd , ".TBL_SHOP_CUPON." c
			where cr.publish_ix = cp.publish_ix and cu.code = cr.mem_ix and cu.code = cmd.code and c.cupon_ix = cp.cupon_ix
			$status_str $where
			order by cr.regdate desc LIMIT $start,$max ";
//	echo $sql;
	$db->query($sql);
//}
//echo $total;
/*
$db->query("Select * from ".TBL_SHOP_CUPON_REGIST." where use_yn = 1 ");
$use_total = $db->total;
*/

$Script = "
<script language=javascript>
function SelectView(cupon_type_view){
	document.location.href = 'cupon_user_regist_list.php?mmode=".$mmode."&mem_ix=".$mem_ix."&cupon_send_type_view='+cupon_type_view
}
function loadSellerManager(sel,target) {

	var trigger = sel.options[sel.selectedIndex].value;
	//alert(sel.form.name);
	var form = sel.form.name;

	var depth = sel.getAttribute('depth');

	window.frames['act'].location.href = '../store/sellermanager.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2';

}
</script>";

$Contents = "
<table width='100%' border='0' cellpadding='0' cellspacing='0'>
  <tr>
		<td align='left' colspan=6 style='padding:0 0 10px 0;'> ".GetTitleNavigation("쿠폰 사용자 발급 리스트", "전시관리 > 쿠폰 사용자 발급 리스트")."</td>
  </tr>
  <tr>
    <td valign='top'>
      <table width='100%' border='0' cellpadding='2' cellspacing='0'>
        <tr>
				    <td align='left' colspan=4 style='padding-bottom:15px;'>
				    	<div class='tab'>
								<table class='s_org_tab' style='width:100%'>
								<col width=320>
								<col width=*>
								<tr>
									<td class='tab'>
										<table id='tab_03'  class='on' >
										<tr>
											<th class='box_01'></th>
											<td class='box_02' onclick='SelectView(3)'>보유쿠폰</td>
											<th class='box_03'></th>
										</tr>
										</table>
										<table id='tab_02' >
										<tr>
											<th class='box_01'></th>
											<td class='box_02' ><a href='../promotion/cupon_publish_list.php?mmode=personalization&mem_ix=".$mem_ix."'>발급 목록 보기</a></td>
											<th class='box_03'></th>
										</tr>
										</table>
										
									</td>
								</tr>
								</table>
							</div>
				    </td>
					</tr>
      </table>
    </td>
  </tr>
  <tr>
    <td colspan=7>
        <form name='search_coupon'>
		<input type='hidden' name='mmode' value='$mmode'>
		<input type='hidden' name='mem_ix' value='$mem_ix'>
		<input type='hidden' name='cupon_send_type_view' value='$cupon_send_type_view' />
        
                    <table width=100%  border=0>
                        <tr>
                            <td align='left' colspan=2 width='100%' valign=top style='padding-top:0px;'>
                                 
                                            <TABLE   cellSpacing=0 cellPadding=0 style='width:100%;' align=center border=0>
                                                <TR>
                                                    <TD bgColor=#ffffff style='padding:0 0 0 0;'>
                                                        <table cellpadding=2 cellspacing=1 width='100%' class='search_table_box'>
															<col width='150'>
															<col width='*'>
                                                            <tr>
                                                                <th class='search_box_title' >조건검색 : </th>
                                                                <td class='search_box_left' colspan='3'>
                                                                    <table>
                                                                        <tr>
                                                                            <td>
                                                                                <select name=search_type>
                                                                                <option value='cupon_no' ".CompareReturnValue("cupon_no",$search_type,"selected").">쿠폰코드</option>
																				<option value='cupon_kind' ".CompareReturnValue("cupon_kind",$search_type,"selected").">쿠폰명</option>
																				<option value='pid' ".CompareReturnValue("pid",$search_type,"selected").">상품코드</option>
                                                                                </select>
                                                                            </td>
                                                                            <td><input type=text class=textbox name='search_text' value='".$search_text."' style='width:200px;' ></td>
                                                                        </tr>
                                                                    </table>
                                                                </td>                                                                
                                                            </tr>
															<tr>
																<th class='search_box_title' >발행형태</th>
                                                                <td class='search_box_left' colspan='3'>
                                                                    <input type='radio' name='publish_type' id='publish_type_1' onFocus='this.blur();' align='middle' value='' ".CompareReturnValue($publish_type,'',' checked')."><label for='publish_type_1'>모두보기</label>&nbsp;
                                                                    <input type='radio' name='publish_type' id='publish_type_2' onFocus='this.blur();' align='middle' value=1 ".CompareReturnValue($publish_type,'1',' checked')."><label for='publish_type_2'>고객지정 발행 보기</label>&nbsp;
                                                                    <input type='radio' name='publish_type' id='publish_type_3' onFocus='this.blur();' align='middle' value=2 ".CompareReturnValue($publish_type,'2',' checked')."><label for='publish_type_3'>일반 발행 보기</label>
                                                                </td>
															</tr>
                                                        </table>
                                                    </TD>
                                                </TR>

                                            </TABLE>
                                         

                            </td>
                        </tr>
                        <tr >
                            <td colspan=3 align=center  style='padding:10px 0 0 0'>
                                <input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0>
                            </td>
                        </tr>
                    </table>
                 
        </form>
    </td>
</tr>
  <tr>
    <td valign='top'>
      <table width='100%' border='0' cellpadding='0' cellspacing='0'>
        <tr>
          <td valign='top'>
			<table width='100%' border='0' cellpadding='0' cellspacing='0'>
				<tr>
					<td style='text-align:left;vertical-align:bottom;padding:0 0 10px 0'>
						총건수 :&nbsp;<b>".$total."</b>
					</td>
				</tr>
			</table>
            <table width='100%'  border='0' cellpadding='0' cellspacing='1' class='list_table_box'>
              
              <tr bgcolor='#ffffff' align=center>
                <td width='5%' class='s_td' bgcolor='white' nowrap height='25'>번호</td>
                <!--td width='13%' class='m_td' bgcolor='white' nowrap>쿠폰발행일자</td-->
                <td width='8%' class='m_td' bgcolor='white' nowrap>쿠폰종류</td>
				<td width='8%' class='m_td' bgcolor='white' nowrap>쿠폰명</td>
                
                <td width='*' class='m_td' bgcolor='white' nowrap>쿠폰발행</td>
                <td width='15%' class='m_td' bgcolor='white' nowrap>쿠폰번호</td>
                <td width='12%' class='m_td' bgcolor='white' nowrap>아이디 / 이름</td>
				<td width='14%' class='m_td' bgcolor='white' nowrap>사용가능일자</td>
                <td width='10%' class='m_td' bgcolor='white' nowrap>사용여부</td>
              </tr>";

if($total < 1){
	$Contents .= "<tr bgcolor='#ffffff' height=50><td colspan=8  align=center> 등록된 쿠폰 정보가 없습니다. </td></tr>";
}else{
	/*
	if($cupon_send_type_view == 2){ 
		if($db->dbms_type == "oracle"){

			$sql = "Select
			r.* ,cmd.name as mem_name, cu.id as mem_id
			from (
				select b.* from (
					select a.*, ROWNUM rnum from (
						select cp.cupon_no, cp.regdate as publish_regdate, cp.publish_ix, r.regdate as regist_regdate, r.open_yn, r.use_yn, c.cupon_kind, r.mem_ix, cp.use_sdate, r.use_date_limit ,use_date_type from ".TBL_SHOP_CUPON_REGIST." r , ".TBL_SHOP_CUPON_PUBLISH." cp , ".TBL_SHOP_CUPON." c  where r.publish_ix = cp.publish_ix  and cp.cupon_ix = c.cupon_ix and open_yn = 1 $where $mem_ix_str order by r.regdate desc
					) a where ROWNUM <= ".($start+$max)."
				) b where rnum > ".$start."
			) r,
			 ".TBL_COMMON_USER." cu , ".TBL_COMMON_MEMBER_DETAIL." cmd
			where r.mem_ix = cu.code and  cu.code = cmd.code $mem_ix_str
			order by r.regist_regdate desc";

			//echo $sql;
		}else{
			$sql = "Select cp.cupon_no, cp.regdate as publish_regdate, cp.publish_ix, r.regdate as regist_regdate, r.open_yn, r.use_yn,
						c.cupon_kind, cmd.name as mem_name, cu.id as mem_id , r.mem_ix, r.use_sdate, r.use_date_limit ,use_date_type
						from (select * from ".TBL_SHOP_CUPON_REGIST." r where open_yn = 1 $mem_ix_str order by r.regdate desc LIMIT $start, $max) r,
						".TBL_SHOP_CUPON." c , ".TBL_SHOP_CUPON_PUBLISH." cp , ".TBL_COMMON_USER." cu , ".TBL_COMMON_MEMBER_DETAIL." cmd
						where r.mem_ix = cu.code and cp.cupon_ix = c.cupon_ix and r.publish_ix = cp.publish_ix and cu.code = cmd.code $mem_ix_str
						order by r.regdate desc  ";
		}

	}else if($cupon_send_type_view == 3){ 
		if($db->dbms_type == "oracle"){

			$sql = "Select
			r.* ,cmd.name as mem_name, cu.id as mem_id
			from (
				select b.* from (
					select a.*, ROWNUM rnum from (
						select cp.cupon_no, cp.regdate as publish_regdate, cp.publish_ix, r.regdate as regist_regdate, r.open_yn, r.use_yn, c.cupon_kind, r.mem_ix, cp.use_sdate, r.use_date_limit ,use_date_type from ".TBL_SHOP_CUPON_REGIST." r , ".TBL_SHOP_CUPON_PUBLISH." cp , ".TBL_SHOP_CUPON." c  where r.publish_ix = cp.publish_ix  and cp.cupon_ix = c.cupon_ix and use_yn = 1 $where $mem_ix_str order by r.regdate desc
					) a where ROWNUM <= ".($start+$max)."
				) b where rnum > ".$start."
			) r,
			 ".TBL_COMMON_USER." cu , ".TBL_COMMON_MEMBER_DETAIL." cmd
			where r.mem_ix = cu.code and  cu.code = cmd.code $mem_ix_str
			order by r.regist_regdate desc";

		}else{
			$sql = "Select cp.cupon_no, cp.regdate as publish_regdate, cp.publish_ix, r.regdate as regist_regdate, r.open_yn, r.use_yn,
						c.cupon_kind, cmd.name as mem_name, cu.id as mem_id , r.mem_ix, r.use_sdate, r.use_date_limit ,use_date_type
						from (select * from ".TBL_SHOP_CUPON_REGIST." r where use_yn = 1 $mem_ix_str order by r.regdate desc LIMIT $start, $max) r,
									".TBL_SHOP_CUPON." c , ".TBL_SHOP_CUPON_PUBLISH." cp , ".TBL_COMMON_USER." cu , ".TBL_COMMON_MEMBER_DETAIL." cmd
						where r.mem_ix = cu.code and cp.cupon_ix = c.cupon_ix and r.publish_ix = cp.publish_ix and cu.code = cmd.code  $mem_ix_str
						order by r.regdate desc  ";
		}
	}else{ 
		if($db->dbms_type == "oracle"){

			$sql = "Select
			r.* ,cmd.name as mem_name, cu.id as mem_id
			from (
				select b.* from (
					select a.*, ROWNUM rnum from (
						select cp.cupon_no, cp.regdate as publish_regdate, cp.publish_ix, r.regdate as regist_regdate, r.open_yn, r.use_yn, c.cupon_kind, r.mem_ix, cp.use_sdate, r.use_date_limit ,use_date_type from ".TBL_SHOP_CUPON_REGIST." r , ".TBL_SHOP_CUPON_PUBLISH." cp , ".TBL_SHOP_CUPON." c  where r.publish_ix = cp.publish_ix  and cp.cupon_ix = c.cupon_ix and use_yn is not null $where $mem_ix_str order by r.regdate desc
					) a where ROWNUM <= ".($start+$max)."
				) b where rnum > ".$start."
			) r,
			 ".TBL_COMMON_USER." cu , ".TBL_COMMON_MEMBER_DETAIL." cmd
			where r.mem_ix = cu.code and  cu.code = cmd.code $mem_ix_str
			order by r.regist_regdate desc";

		}else{
			$sql = "Select cp.cupon_no, cp.regdate as publish_regdate, cp.publish_ix, r.regdate as regist_regdate, r.open_yn, r.use_yn,
						c.cupon_kind, cmd.name as mem_name, cu.id as mem_id , r.mem_ix, r.use_sdate, r.use_date_limit ,use_date_type
						from (select * from ".TBL_SHOP_CUPON_REGIST." r where use_yn is not null $mem_ix_str order by r.regdate desc LIMIT $start, $max) r,
									".TBL_SHOP_CUPON." c , ".TBL_SHOP_CUPON_PUBLISH." cp , ".TBL_COMMON_USER." cu , ".TBL_COMMON_MEMBER_DETAIL." cmd
						where r.mem_ix = cu.code and cp.cupon_ix = c.cupon_ix and r.publish_ix = cp.publish_ix and cu.code = cmd.code  $mem_ix_str
						order by r.regdate desc  ";
		}
	}
	//echo $sql."<br><br>";

	$db->query($sql);
	*/
	//echo $db->total;
	for($i=0;$i < $db->total;$i++){
		$db->fetch($i);
		$no = $total - ($page - 1) * $max - $i;


		if($db->dt[use_date_type] == 1){
			if($db->dt[publish_date_type] == 1){
				$date_type = '년';
			}else if($db->dt[publish_date_type] == 2){
				$date_type = '개월';
			}else if($db->dt[publish_date_type] == 3){
				$date_type = '일';
			}
			$date_differ = $db->dt[publish_date_differ];
			$use_date_type = '발행일';
		}else{
			if($db->dt[regist_date_type] == 1){
				$date_type = '년';
			}else if($db->dt[regist_date_type] == 2){
				$date_type = '개월';
			}else if($db->dt[regist_date_type] == 3){
				$date_type = '일';
			}
			$date_differ = $db->dt[regist_date_differ];
			$use_date_type = '등록일';
		}



		if($db->dt[use_yn] == 1){
			$use_str = "사용";
		}else{
			$use_str = "미사용";
		}

		if($db->dt[open_yn] == 1){
			$open_str = "등록";
		}else{
			$open_str = "미등록";
		}

		if($db->dt[usedate] == ""){
			$use_date_str = "미사용";
		}else{
			if($db->dt[use_oid]){
				$use_date_str = "".$db->dt[use_oid] ."<br>(".$db->dt[usedate] .")";
			}else{
				$use_date_str = "(".$db->dt[usedate] .")";
			}
		}

		$Contents .= "
	              <!--- // 목록 반복 시작 ---------->
	        	<tr bgcolor='#ffffff' align=center height='30'>
	                <td class='list_box_td' nowrap >".$no."</td>
	                <!--td class='list_box_td' align=center nowrap><font class='gray16'>".$db->dt[publish_regdate] ."</font></td-->
	                <td class='list_box_td' align=center nowrap><font class='gray16'>".$_COUPON_KIND[$db->dt[cupon_div]] ."</font></td>	                
	                <td class='list_box_td ' style='text-align:left;' nowrap><font class='yellow'>".$db->dt[cupon_kind] ."</font></td>
					<td class='list_box_td ' style='text-align:left;' nowrap><font class='yellow'>".$db->dt[publish_name] ."</font></td>
	                <td class='list_box_td' nowrap><font class='yellow'><a href=\"javascript:PopSWindow('cupon_register_user.php?mode=result&publish_ix=".$db->dt[publish_ix]."',850,600,'cupon_detail_pop');\" title='해당 쿠폰에 대한 발급된 사용자를 보여줍니다.' class=blue>".$db->dt[cupon_no] ."</a></font></td>
	                <td class='list_box_td'><a href=\"javascript:PopSWindow('cupon_user_regist_list.php?mmode=pop&mem_ix=".$db->dt[mem_ix]."',900,600,'cupon_detail_pop');\" title='해당 사용자에게 발급된 쿠폰 목록을 보여줍니다.'  class=blue><font class='green'>".($db->dt[mem_id] !="" ? $db->dt[mem_id] ."/".$db->dt[mem_name] : "탈퇴한회원" )."</font></a></td>
					<td class='list_box_td' align=center nowrap><font class='gray16'>".($db->dt[use_date_type]!=9 ? ChangeDate($db->dt[use_sdate],"Y-m-d")."-".ChangeDate($db->dt[use_date_limit],"Y-m-d"):"기간제한없음")."</font></td>
	                <td class='list_box_td' nowrap>".$use_date_str."</td>
	              </tr>
	        			 ";


	$apply_str='';
	}//for
$Contents .= "
		 <tr height=50 bgcolor='#ffffff'>
		    <td align='center' colspan='8'>".page_bar($total, $page, $max,"&mmode=$mmode&mem_ix=$mem_ix&cupon_send_type_view=$cupon_send_type_view",'')."</td>
		  </tr>";

}

$Contents .= "
              <!--- 목록 반복 끝 // ---------->
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td height='20' valign='top'></td>
  </tr>

</table>";

$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td ><img src='/admin/image/icon_list.gif' ></td><td class='small' >쿠폰에 대한 사용자들의 등록 목록 입니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >쿠폰번호를 클릭하시면 해당 쿠폰에 대한 사용자의 발급 목록을 확인 하실 수 있습니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >같은 쿠폰은 중복 발급할 수 없습니다.</td></tr>
</table>
";


$Contents .=  HelpBox("쿠폰 사용자 발급 리스트", $help_text);

if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;

	$P->strLeftMenu = promotion_menu();
	$P->strContents = $Contents;
	$P->Navigation = "HOME > 전시관리 > 쿠폰 사용자 발급 리스트";
	$P->title = "쿠폰 사용자 발급 리스트";
	$P->NaviTitle =  "쿠폰 사용자 발급 리스트";
	echo $P->PrintLayOut();
}else if($mmode == "personalization"){
	$P = new ManagePopLayOut();
	$P->addScript = "<script language='javascript' src='orders.js'></script>\n".$Script;
	$P->OnloadFunction = "";
	$P->strLeftMenu = order_menu();
	$P->Navigation = "HOME > 전시관리 > 쿠폰 사용자 발급 리스트";
	$P->title = "쿠폰 사용자 발급 리스트";
	$P->NaviTitle =  "쿠폰 사용자 발급 리스트";
	$P->strContents =  $Contents;
	$P->layout_display = false;
	$P->view_type = "personalization";
	echo $P->PrintLayOut();

}else{

	$P = new LayOut();
	$P->addScript = $Script;

	$P->strLeftMenu = promotion_menu();
	$P->strContents = $Contents;
	$P->Navigation = "HOME > 전시관리 > 쿠폰 사용자 발급 리스트";
	$P->title = "쿠폰 사용자 발급 리스트";
	echo $P->PrintLayOut();
}
?>
