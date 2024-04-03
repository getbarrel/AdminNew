<?
include("../class/layout.class");
include("../econtract/contract.lib.php");

$db = new Database;
$db2 = new Database;


if($_COOKIE[seller_company_list_limit]){
    $max = $_COOKIE[seller_company_list_limit]; //페이지당 갯수
}else{
    $max = 30;
}

if($page == ''){
    $start = 0;
    $page  = 1;
}else{
    $start = ($page - 1) * $max;
}

if($mode == 'search'){

    if($mult_search_use == '1'){	//다중검색 체크시 (검색어 다중검색)

        if($search_text != ""){
            if(strpos($search_text,",") !== false){
                $search_array = explode(",",$search_text);
                $search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
                $search_str .= "and ( ";
                $count_where .= "and ( ";
                for($i=0;$i<count($search_array);$i++){
                    $search_array[$i] = trim($search_array[$i]);
                    if($search_array[$i]){
                        if($i == count($search_array) - 1){
                            $search_str .= $search_type." = '".trim($search_array[$i])."'";
                            $count_where .= $search_type." = '".trim($search_array[$i])."'";
                        }else{
                            $search_str .= $search_type." = '".trim($search_array[$i])."' or ";
                            $count_where .= $search_type." = '".trim($search_array[$i])."' or ";
                        }
                    }
                }
                $search_str .= ")";
                $count_where .= ")";
            }else if(strpos($search_text,"\n") !== false){//\n
                $search_array = explode("\n",$search_text);
                $search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
                $search_str .= "and ( ";
                $count_where .= "and ( ";

                for($i=0;$i<count($search_array);$i++){
                    $search_array[$i] = trim($search_array[$i]);
                    if($search_array[$i]){
                        if($i == count($search_array) - 1){
                            $search_str .= $search_type." = '".trim($search_array[$i])."'";
                            $count_where .= $search_type." = '".trim($search_array[$i])."'";
                        }else{
                            $search_str .= $search_type." = '".trim($search_array[$i])."' or ";
                            $count_where .= $search_type." = '".trim($search_array[$i])."' or ";
                        }
                    }
                }
                $search_str .= ")";
                $count_where .= ")";
            }else{
                $search_str .= " and ".$search_type." = '".trim($search_text)."'";
                $count_where .= " and ".$search_type." = '".trim($search_text)."'";
            }
        }

    }else{	//검색어 단일검색
        if($search_text != ""){
            if(substr_count($search_text,",")){
                $search_str .= " and ".$search_type." in ('".str_replace(",","','",str_replace(" ","",$search_text))."') ";
            }else{
                $search_str .= " and ".$search_type." LIKE '%".trim($search_text)."%' ";
            }
        }
    }
}

if($admininfo[admin_level] == 8){
    $search_str .= " and ccd.company_id = '".$admininfo['company_id']."'";
}

$sql = "SELECT 
			COUNT(*) as total
		FROM 
			common_company_detail as ccd
		where 
			1
			and ccd.com_type = 'S'
			$search_str ";

$db->query($sql);
$db->fetch();
$total = $db->dt[total];

$sql = "select 
        a.*
        ,ifnull((select csp.use_penalty from common_seller_penalty csp where csp.company_id = a.company_id order by csp.penalty_ix desc limit 1),0) as use_penalty
        from 
        (
        select
            ccd.company_id,
            ccd.com_name,
            ci.cname,
            b.brand_name as seller_brand_name
        from
            common_company_detail as ccd
            inner join common_seller_detail as csd on (ccd.company_id = csd.company_id)
            left join shop_category_info as ci on (csd.seller_cid = ci.cid)
            left join shop_brand as b on (csd.seller_brand = b.b_ix)
        where
            ccd.com_type = 'S'
            $search_str
            order by ccd.com_name asc
            LIMIT $start,$max
        ) a ";

$db->query($sql);

if($QUERY_STRING == "nset=$nset&page=$page"){
    $query_string = str_replace("nset=$nset&page=$page","",$QUERY_STRING) ;
}else{
    $query_string = str_replace("nset=$nset&page=$page&","","&".$QUERY_STRING) ;
}
$str_page_bar = page_bar($total, $page,$max, $query_string,"view");

$menu_name = "셀러별 판매 신용점수 리스트";

$mstring = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<col width=6%>
	<col width=*>
	<col width=12%>
	<col width=12%>
	<col width=12%>
	<col width=9%>
	<col width=9%>
	<col width='20%'>
	<tr>
	    <td align='left' colspan=8> ".GetTitleNavigation("$menu_name", "셀러업체 관리 > $menu_name")."</td>
	</tr>
	<tr>
	<td colspan=8>
		<form name='searchmember'>
		<input type='hidden' name='mode' value='search'>
		<input type='hidden' name='list_type' value='".$list_type."'>
		<table border='0' cellpadding='0' cellspacing='0' width='100%'>
		<tr>
			<td style='width:100%;' valign=top colspan=3>
				<table width=100%  cellpadding=0 cellspacing=0 border=0>
				<tr height=22>
					<td ><img src='../images/dot_org.gif' align=absmiddle> <b>셀러업체 검색하기</b></td>
				</tr>
				<tr>
					<td align='left' colspan=2 height=50 width='100%' valign=top style='padding-top:5px;'>
						<TABLE cellSpacing=0 cellPadding=0 style='width:100%;' align=center border=0>
						<TR>
							<TD bgColor=#ffffff style='padding:0 0 0 0;height:30px;'>
								<table cellpadding=0 cellspacing=0 width='100%' class='input_table_box'>
									<col width='18%'>
									<col width='32%'>
									<col width='18%'>
									<col width='32%'>
									<tr height=30>
										<td class='input_box_title'>조건검색
											<span style='padding-left:2px' class='helpcloud' help_width='220' help_height='30' help_html='검색하시고자 하는 값을 ,(콤마) 구분으로 넣어서 검색 하실 수 있습니다'><img src='/admin/images/icon_q.gif' align=absmiddle/></span>
											<input type='checkbox' name='mult_search_use' id='mult_search_use' value='1' ".($mult_search_use == '1'?'checked':'')." title='다중검색 체크'>
											<label for='mult_search_use'>(다중검색 체크)</label>
										</td>
										<td class='input_box_item' colspan='3'>
											<table cellpadding='0' cellspacing='0' border='0' >
												<tr>
													<td valign='top'>
														<div style='padding-top:5px;'>
														<select name='search_type' id='search_type'  style=\"font-size:12px;\">
														<option value='ccd.com_name' ".($search_type == 'ccd.com_name' || $search_type == ''?'selected':'').">업체명</option>
														<option value='ccd.com_ceo' ".($search_type == 'ccd.com_ceo'?'selected':'').">대표자</option>
														
														<option value='csd.shop_name' ".($search_type == 'csd.shop_name'?'selected':'').">상점이름</option>";
if($list_type == "user" || true){
    $mstring .= "
														<option value='cmd.name' ".($search_type == 'cmd.name'?'selected':'').">담당자명</option>
														<option value='cu.id' ".($search_type == 'cu.id'?'selected':'').">사용자 ID</option>";
}
$mstring .= "
														</select>
														</div>
													</td>
													<td style='padding:5px;'>
														<div id='search_text_input_div'>
															<input id=search_texts class='textbox1' value='".$search_text."' autocomplete='off' clickbool='false' style='WIDTH: 210px; height:18px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'>
														</div>
														<div id='search_text_area_div' style='display:none;'>
															<textarea name='search_text' name='search_text_area' id='search_text_area' class='tline textbox' style='padding:3px;height:90px;width:200px;' >".$search_text."</textarea>
														</div>
													</td>
													<td>
														<div>
															<span class='small blu' > * 다중 검색은 다중 아이디로 검색 지원이 가능합니다. 구분값은 ',' 혹은 'Enter'로 사용 가능합니다. </span>
														</div>
													</td>

												</tr>
											</table>
										</td>
									</tr>
								</table>
							</TD>
						</TR>
						</TABLE>
					</td>
				</tr>
				<tr>
					<td colspan=3 align=center  style='padding:30px 0 10px 0'>
						<input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		</table>
		</form>
	</td>
</tr>
</table>";


$mstring .= "
	<table border='0' cellpadding='0' cellspacing='0' width='100%'>
	<col width='*'>
	<col width=10%>
	<tr height='25px;'>
		<td >
			<img src='../images/dot_org.gif' align=absmiddle> <b>셀러업체 리스트</b>
		</td>
	    <td align=right>
	목록수 : <select name='max' id='max'>
				<option value='5' ".($max == '5'?'selected':'').">5</option>
				<option value='10' ".($max == '10'?'selected':'').">10</option>
				<option value='20' ".($max == '20'?'selected':'').">20</option>
				<option value='30' ".($max == '30'?'selected':'').">30</option>
				<option value='50' ".($max == '50'?'selected':'').">50</option>
				<option value='100' ".($max == '100'?'selected':'').">100</option>
				<option value='500' ".($max == '500'?'selected':'').">500</option>
				<option value='1000' ".($max == '1000'?'selected':'').">1000</option>
				<option value='1500' ".($max == '1500'?'selected':'').">1500</option>
				<option value='2000' ".($max == '2000'?'selected':'').">2000</option>
			</select>
	</td>
	</tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' class='list_table_box'>
		<col width=8%>
		<col width='*'>
		<col width=20%>
		<col width=15%>
		<col width=20%>
		<col width=20%>
		<tr bgcolor=#efefef align=center height=27>
		    <td class='s_td'>번호</td>
			<td class='m_td'>업체명</td>
			<td class='m_td'>주요상품군</td>
			<td class='m_td'>주요브랜드</td>
			<td class='m_td'>판매신용점수</td>
			<td class='e_td'>관리</td>
		</tr>";

if($db->total){
    for($i=0;$i<$db->total;$i++){
        $db->fetch($i);

        $no = $total - ($page - 1) * $max - $i;

        $mstring .="<tr height=32 align=center>
					<td class='list_box_td list_bg_gray'>".$no."</td>
					<td class='list_box_td'>".$db->dt[com_name]."</td>
					<td class='list_box_td list_bg_gray'><span style='padding-left:2px;cursor:pointer;' class='helpcloud' help_width='100' help_height='35' help_html='".$db->dt['seller_msg']."'>".$db->dt['cname']."</span></td>
					<td class='list_box_td'><span style='padding-left:2px;cursor:pointer;'>".$db->dt['seller_brand_name']."</span></td>
					<td class='list_box_td point'>".number_format($db->dt['use_penalty'])."</td>
					<td class='list_box_td' align=center style='padding:0px 5px' nowrap>";

        if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
            $mstring .="
				<a href=\"javascript:PoPWindow('seller_penalty.pop.php?company_id=".$db->dt[company_id]."',800,550,'penalty_pop')\">
						<img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 align='absmiddle'>
				</a>";
        }else{
            $mstring .="
				<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 align='absmiddle'></a>";
        }

        $mstring .="
				</td>
			</tr>";
    }
}else{
    $mstring .= "<tr height=50><td colspan=14 align=center style='padding-top:10px;'>등록된 셀러업체가 없습니다.</td></tr>";
}
$mstring .="</table><br>";

$mstring .="<table width='100%' cellpadding=0 cellspacing=0 border='0' align>
			<tr hegiht=30><td colspan=12 align=right style='padding-top:5px 0px;'>".$str_page_bar."</td></tr>
			</table><br>";
$Contents = $mstring;

$Script .= "
<script language='javascript'>

function clearAll(frm){
	for(i=0;i < frm.cpid.length;i++){
			frm.cpid[i].checked = false;
	}
}
function checkAll(frm){
	for(i=0;i < frm.cpid.length;i++){
		frm.cpid[i].checked = true;
	}
}
function fixAll(frm){
	if (!frm.all_fix.checked){
		clearAll(frm);
		frm.all_fix.checked = false;
			
	}else{
		checkAll(frm);
		frm.all_fix.checked = true;
	}
}

function ChangeRegistDate(frm){
	if($('input[name=regdate]').attr('checked') == 'checked'){
		$('input[name=sdate]').attr('disabled',false);
		$('input[name=edate]').attr('disabled',false);
	}else{
		$('input[name=sdate]').attr('disabled',true);
		$('input[name=edate]').attr('disabled',true);
	}
}

function init(){

	var frm = document.search_seller;
//	onLoad('$sDate','$eDate');";

if($regdate != "1"){
    $Script .= "
	frm.sdate.disabled = true;
	frm.edate.disabled = true;";
}

$Script .= "
}";

$Script .= "
$(document).ready(function (){

//다중검색어 시작 2014-04-10 이학봉

	$('input[name=mult_search_use]').click(function (){
		var value = $(this).attr('checked');

		if(value == 'checked'){
			$('#search_text_input_div').css('display','none');
			$('#search_text_area_div').css('display','');
			
			$('#search_text_area').attr('disabled',false);
			$('#search_texts').attr('disabled',true);
		}else{
			$('#search_text_input_div').css('display','');
			$('#search_text_area_div').css('display','none');

			$('#search_text_area').attr('disabled',true);
			$('#search_texts').attr('disabled',false);
		}
	});

	var mult_search_use = $('input[name=mult_search_use]:checked').val();
		
	if(mult_search_use == '1'){
		$('#search_text_input_div').css('display','none');
		$('#search_text_area_div').css('display','');

		$('#search_text_area').attr('disabled',false);
		$('#search_texts').attr('disabled',true);
	}else{
		$('#search_text_input_div').css('display','');
		$('#search_text_area_div').css('display','none');

		$('#search_text_area').attr('disabled',true);
		$('#search_texts').attr('disabled',false);
	}

//다중검색어 끝 2014-04-10 이학봉
	
	$('#max').change(function(){
		var value= $(this).val();
		$.cookie('seller_company_list_limit', value, {expires:1,domain:document.domain, path:'/', secure:0});
		document.location.reload();
	});
});

</script>";

$P = new LayOut;
$P->addScript = "$Script";
$P->strLeftMenu = seller_menu();
$P->Navigation = "셀러관리 > 셀러업체 관리 > $menu_name";
$P->title = "$menu_name";
$P->strContents = $Contents;
$P->PrintLayOut();


?>