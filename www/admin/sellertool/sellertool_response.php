<?
include("../class/layout.class");
include_once("sellertool.lib.php");

$db = new Database;
$db2 = new Database;

if(empty($max)){
	$max = 20; //페이지당 갯수
}

if ($page == '')
{
	$start = 0;
	$page  = 1;
}
else
{
	$start = ($page - 1) * $max;
}

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <col width='20%'>
	  <col width='*'>
	  <tr >
		<td align='left' colspan=6 > ".GetTitleNavigation("제휴사 연동키검색", "제휴사연동 > 기본정보 설정 > 제휴사 연동키검색")."</td>
	  </tr>";

$Contents01 .= "
        <tr>
            <td align='left' colspan=4 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk>제휴사 연동키 검색</b></div>")."</td>
        </tr>
    </table>
        <form name='search_form' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' style='display:inline;'>
			<input type=hidden name='mode' value='search'>
            <table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='input_table_box'>
                <col width='15%'>
                <col width='35'>
                <col width='15%'>
                <col width='35%'>
                <tr>
                    <td class='input_box_title'>제휴사 선택</td>
                    <td class='input_box_item' colspan='3'>
                    	<table border=0 cellpadding=0 cellspacing=0>
                    		<tr>
                    			<td style='padding-right:5px;'>
                                    ".getSellerToolSiteInfo($site_code)."
                                </td>
                    		</tr>
                    	</table>
                    </td>
                </tr>
				<tr>
					<td class='search_box_title' >판매상태</td>
					<td class='search_box_item'>
						<table cellpadding=0 cellspacing=0 border='0'  align='left'>
						<col width=100>
						<col width=100>
						<col width=100>
						<col width=100> 
						<tr>
							<td>
							<input type='checkbox' name='state[]' id='search_state_1' value='1' title='판매중' ".(is_array($state)?in_array('1',$state)?'checked':'':'')."/>
							<label for='search_state_1'> 판매중 </label> 
							</td>
							<td>
							<input type='checkbox' name='state[]' id='search_state_0' value='0' title='일시품절' ".(is_array($state)?in_array('0',$state)?'checked':'':'')."/>
							<label for='search_state_0'> 일시품절 </label> 
							</td>
							<td>
							<input type='checkbox' name='state[]' id='search_state_2' value='2' title='판매중지' ".(is_array($state)?in_array('2',$state)?'checked':'':'')."/>
							<label for='search_state_2'> 판매중지</label>
							</td>
							<td></td>
						</tr>
						<tr>
							<td>
							<input type='checkbox' name='state[]' id='search_state_8' value='8' title='승인거부' ".(is_array($state)?in_array('8',$state)?'checked':'':'')."/> 
							<label for='search_state_8'> 승인거부 </label>
							</td>
							
							<td>
							<input type='checkbox' name='state[]' id='search_state_6' value='6' title='승인대기' ".(is_array($state)?in_array('6',$state)?'checked':'':'')."/> 
							<label for='search_state_6'> 승인대기 </label>
							</td>

							<td>
							<input type='checkbox' name='state[]' id='search_state_7' value='7' title='수정대기상품' ".(is_array($state)?in_array('7',$state)?'checked':'':'')."/> 
							<label for='search_state_7'> 수정대기상품 </label>
							</td>

							<td>
							<input type='checkbox' name='state[]' id='search_state_9' value='9' title='판매금지' ".(is_array($state)?in_array('9',$state)?'checked':'':'')."/> 
							<label for='search_state_9'> 판매금지 </label>
							</td>
							";


$Contents01 .=	"
						</tr>
						</table>
					</td>
					<td class='search_box_title'>노출여부</td>
					<td class='search_box_item'>
						<table cellpadding=0 cellspacing=0 border='0'  align='left'>
						<col width=60>
						<col width=100>
						<tr>
							<td>
								<input type='checkbox' name='disp[]' id='disp_1' value='1' title='노출' ".(is_array($disp)?in_array('1',$disp)?'checked':'':'')."/><label for='disp_1'> 노출 </label> 
							</td>
							<td>
								<input type='checkbox' name='disp[]' id='disp_0' value='0' title='미노출' ".(is_array($disp)?in_array('0',$disp)?'checked':'':'')."/><label for='disp_0'> 미노출 </label> 
							</td>
							<td></td>
						</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td class='search_box_title'>브랜드</td>
					<td class='search_box_item'>
					".BrandListSelect($brand, $cid)."
					</td>
					<td class='search_box_title'>셀러업체</td>
					<td class='search_box_item'>
						".companyAuthList($company_id , "validation=false title='셀러업체' ")."
					</td>
				</tr>
				";

				$Contents01 .= "
                <tr>
                	<td class='input_box_title'>  검색어 <input type='checkbox' name='mult_search_use' id='mult_search_use' value='1' ".($mult_search_use == '1'?'checked':'')." title='다중검색 체크'> 
								<label for='mult_search_use'>(다중검색 체크)</label> </td>
                	<td class='input_box_item' >
                		<table cellpadding=0 cellspacing=0 width=400>
                			<col width='100px'>
							<col width='*'>
							<tr>
                				<td >
                				    <select name='search_type'>
										<option value='target.pname' ".($search_type == 'target.pname' ? "selected" : "").">상품명</option>
										<option value='target.pcode' ".($search_type == 'target.pcode' ? "selected" : "").">상품코드</option>
										<option value='target.id' ".($search_type == 'target.id' ? "selected" : "").">상품코드(키)</option>";
									$Contents01 .= "
									</select>
                				</td>
								<td >
                				    <div id='search_text_input_div'>
										<input id=search_texts class='textbox1' value='".$search_text."' autocomplete='off' clickbool='false' style='WIDTH: 210px; height:18px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'>
									</div>
									<div id='search_text_area_div' style='display:none;'>
										<textarea name='search_text' name='search_text_area' id='search_text_area' class='tline textbox' style='padding:0px;height:90px;width:150px;' >".$search_text."</textarea>
									</div>
                				</td>
                				<td colspan=2 style='padding-left:5px;'>
                                </td>
                			</tr>
                		</table>
                    </td>
					<td class='input_box_title'> 검색수 </td>
                	<td class='input_box_item' >
                		<select name='max' style=\"font-size:12px;height: 20px; width: 50px;\" align=absmiddle>
							<option value='5' ".CompareReturnValue(5,$max).">5</option>
							<option value='10' ".CompareReturnValue(10,$max).">10</option>
							<option value='20' ".CompareReturnValue(20,$max).">20</option>
							<option value='40' ".CompareReturnValue(40,$max).">40</option>
							<option value='50' ".CompareReturnValue(50,$max).">50</option>
							<option value='100' ".CompareReturnValue(100,$max).">100</option>
						</select>
                    </td>
                </tr>        
            </table>
            <table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
                <tr bgcolor=#ffffff ><td colspan=4 align=center style='padding:20px 0px;'><input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
            </table>
        </form>";

$Contents02 = "
	<div style='width:100%;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'>
	<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >
		<tr>
			<td align='left'>
				<img src='../image/title_head.gif' align=absmiddle> <b class=blk>제휴사 연동키 결과</b>
			</td>
			<td align='right'>
				<!--span class='helpcloud' help_height='30' help_html='로그 정보를 엑셀로 다운로드 하실 수 있습니다'>
				<img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0 align='absmiddle' style='cursor:pointer' onclick=\"location.href='?mmode=excel&".$QUERY_STRING."';\" ></span-->
			</td>
		</tr>
	</table>
	</div>")."</div>";

$Contents02 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box' style='margin-top:3px;' >
		<col width=10%>
        <col width=20%>
        <col width=15%>
        <col width=10%>
        <col width=25%>
		<col width=13%>
	  <tr height=30 bgcolor=#efefef align=center style='font-weight:bold'>
		<td class='m_td'> 상품코드</td>
		<td class='m_td'> 제휴사</td>
		<td class='m_td'> 상품명</td>
		<td class='m_td'> 연동키타입</td>
		<td class='m_td'> 연동키값</td>
        <td class='m_td'> 등록일자</td> 
	  </tr>";





$where = "where sr.shop_value = target.id and sr.shop_key='pid' ";

if($site_code != ""){
	$where .= "and site_code = '".trim($site_code)."' ";
}

if($result_code !=""){
	if($result_code == 'Y'){
		$where .= "and result_code = '200' ";
	}else{
		$where .= "and result_code != '200' ";
	}
}

if($mult_search_use == '1'){	//다중검색 체크시 (검색어 다중검색)
	//다중검색 시작 2014-04-10 이학봉
	if($search_text != ""){
		if(strpos($search_text,",") !== false){
			$search_array = explode(",",$search_text);
			$search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
			$where .= "and ( ";
			for($i=0;$i<count($search_array);$i++){
				$search_array[$i] = trim($search_array[$i]);
				if($search_array[$i]){
					if($i == count($search_array) - 1){
						$where .= $search_type." = '".trim($search_array[$i])."'";
					}else{
						$where .= $search_type." = '".trim($search_array[$i])."' or ";
					}
				}
			}
			$where .= ")";
		}else if(strpos($search_text,"\n") !== false){//\n
			$search_array = explode("\n",$search_text);
			$search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
			$where .= "and ( ";

			for($i=0;$i<count($search_array);$i++){
				$search_array[$i] = trim($search_array[$i]);
				if($search_array[$i]){
					if($i == count($search_array) - 1){
						$where .= $search_type." = '".trim($search_array[$i])."'";
					}else{
						$where .= $search_type." = '".trim($search_array[$i])."' or ";
					}
				}
			}
			$where .= ")";
		}else{
			$where .= " and ".$search_type." = '".trim($search_text)."'";
		}
	}

}else{	//검색어 단일검색
	if($search_text != ""){
		if($search_type=="target.pname"){
			$where = $where."and ".$search_type." LIKE '%".trim($search_text)."%' ";
		}else{
			$where = $where."and ".$search_type." = '".trim($search_text)."' ";
		}
	}
}

//판매상태 검색관련
if(is_array($state)){		//판매상태 (판매중, 일시품절, 본사대기 ... )
	if(count($state)>0){
		$where.=" AND target.state IN ('".implode("','",$state)."')";
	}
}else{
	if($state != ""){
		$where .= " and target.state = '".$state."'";
	}else{
		$state='';
	}
}

//노출여부 검색관련
if(is_array($disp)){		//노출여부 
	if(count($disp)>0){
		$where.=" AND target.disp IN ('".implode("','",$disp)."')";
	}
}else{
	if($disp != ""){
		$where .= " and target.disp = '".$disp."'";
	}else{
		//$disp=array();
		$disp='';
	}
}

if($b_ix != ""){	//브랜드검색
	$where .= " and target.brand = '".$b_ix."'";
}

if($company_id != ""){	//셀러별 검색
	$where .= " and target.admin = '".$company_id."'";
}

if($mode == 'search'){
	$sql = "select 
		SQL_CALC_FOUND_ROWS
		sr.*
		,concat('[',target.brand_name,']',target.pname) as pname ,target.id
	from
		sellertool_reponse sr , shop_product target
	$where ";

	if($mmode!='excel'){
		$sql .= " limit $start , $max ";
	}

	$db->query($sql);
	$lists = $db->fetchall("object");

	$db->query("select FOUND_ROWS() as total ");
	$db->fetch();
	$total = $db->dt[total];
}

$search_query = "&mode=$mode&site_code=$site_code&result_code=$result_code&mult_search_use=$mult_search_use&search_text=$search_text&search_type=$search_type&max=$max&startDate=$startDate&endDate=$endDate";
$str_page_bar = page_bar($total, $page,$max, $search_query ,'');

if($total){
	for($i=0;$i < count($lists); $i++){

	$point = "<a href=\"../product/goods_input.php?id=".$lists[$i][id]."\" target='_blank'>".$lists[$i][id]."</a>";

	$Contents02 .= "
		  <tr bgcolor=#ffffff height=30 align=center>
			<td class='list_box_td point' >
				".$point."
			</td>
			<td class='list_box_td list_bg_gray'>".$lists[$i][site_code]."</td>
			<td class='list_box_td' style='text-align:left;'>".$lists[$i][pname]."</td>
		    <td class='list_box_td list_bg_gray'>".$lists[$i][shop_key]."</td>
			<td class='list_box_td'>".$lists[$i][sellertool_value]."</td>
            <td class='list_box_td list_bg_gray' style='padding:5px;'>".$lists[$i][regdate]."</td> 
		  </tr> ";
	}
}else{
	if($mode=='mode'){
		$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=6> 제휴사연동 로그정보가 없습니다. </td>
		  </tr>";
	}else{
		$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=6> 먼저 검색을 해주시기 바랍니다. </td>
		  </tr>";
	}
}



$Contents02 .= "<table width=100%>
						<tr>
						<td style='padding:10px 0px;text-align:right;'>".$str_page_bar."</td>
						</tr>
				</table>";



$Contents .= "<table width='100%' border=0 cellpadding='0' cellspacing='0'>";
$Contents .= "<tr><td width='100%'>";
$Contents .= $Contents01."<br>";
$Contents .= "</td></tr>";
$Contents .= "<tr><td>".$ContentsDesc01."</td></tr>";
$Contents .= "<tr height=10><td></td></tr>";
$Contents .= "<tr height=10><td></td></tr>";
$Contents .= "<tr><td>".$Contents02."<br></td></tr>";
$Contents .= "<tr height=30><td></td></tr>";
$Contents .= "</table >";
$Contents .= "</form>";


$help_text = "
	<table cellpadding=1 cellspacing=0  >
		<col width=8>
		<col width=*>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >제휴사 연동시 상품 연동에 따른 키와 값을 조회해 보실수 있습니다. </td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ></td></tr>
	</table>
	";
//$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'D');

	$help_text = HelpBox("제휴사 연동키보기", $help_text);
$Contents = $Contents.$help_text;

 $Script = "
 <script language='javascript'>
	//다중검색어 시작 2014-04-10 이학봉
	$(document).ready(function() {
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
	})
	//다중검색어 끝 2014-04-10 이학봉
 </script>
 ";

if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = sellertool_menu();
	$P->Navigation = "제휴사연동 > 제휴사 연동키검색";
	$P->title = "제휴사 연동키검색";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = sellertool_menu();
	$P->Navigation = "제휴사연동 > 제휴사 연동키검색";
	$P->title = "제휴사 연동키검색";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}

?>