<?
include("../class/layout.class");
include("../logstory/class/sharedmemory.class");


$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr >
		<td align='left' colspan=6> ".GetTitleNavigation("키워드 관리", "프로모션(마케팅) > 키워드 관리 ")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=3 style='padding-bottom:15px; position:relative;'>
	    	<div class='tab'>
				<table class='s_org_tab'>
					<tr>
						<td class='tab'>
							<table id='tab_01' ".($recommend == '' ? "class='on'":"")." >
							<tr>
								<th class='box_01'></th>
								<td class='box_02'  ><a href='keyword_list.php'>전체</a></td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_02' ".($recommend == '1' ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' ><a href='keyword_list.php?recommend=1'>추천검색어</a></td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_02' ".($recommend == '0' ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' ><a href='keyword_list.php?recommend=0'>인기검색어</a></td>
								<th class='box_03'></th>
							</tr>
							</table>
						</td>
					</tr>
				</table>
			</div>
			<div style='position:absolute; margin-top: -15px; right:0; ".($recommend == '1' || $recommend == '0'?'display:none;':'')."'><a href='./keyword_excel_down.php'><button>엑셀다운로드</button></a></div>
	    </td>
	</tr>
	  </table>";


$shmop = new Shared("mobile_search_keyword");
$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
$shmop->SetFilePath();
$ms_data = $shmop->getObjectForKey($recommend);
$ms_data = unserialize(urldecode($ms_data));


if($recommend != ''){


$Contents01 .= "
<form name='keyword_frm' method='POST' action='keyword.act.php' target='act'/>
<input type='hidden' name='act' value='save'/>
<input type='hidden' name='recommend' value='".$recommend."'/>
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box'>
		<col width='5%'>
		<col width='30%'>
		<col width='10%'>
		<col width='5%'>
		<col width='30%'>
		<col width='10%'>
		<tr height=25 align=center style='font-weight:bold'>
			<td bgcolor='#efefef'>순위</td>
			<td>키워드</td>
			<td>검색횟수</td>
			<td bgcolor='#efefef'>순위</td>
			<td>키워드</td>
			<td>검색횟수</td>
		</tr>";

		$scIxArray=array();
		for($i=1;$i<=20;$i++){
			if( ! empty($ms_data['code'.($i)])){
				$scIxArray[] = $ms_data['code'.($i)];
			}
		}

		if(count($scIxArray)>0){
			$sql="select * from shop_search_keyword where k_ix in ('".implode("','",$scIxArray)."') ";
			//$sql="select * from shop_search_keyword where ranking>=1 and ranking<=20";
			$db->query($sql);
			$skData = $db->fetchall("object");

			$scData = array();
			if(count($skData)>0){
				foreach($skData as $sc){
					$scData[$sc['k_ix']]['keyword']=$sc['keyword'];
					$scData[$sc['k_ix']]['searchcnt']=$sc['searchcnt'];
				}
			}
		}

		for($i=1;$i<=10;$i++){

			$Contents01 .= "
			<tr height=25 align=center style='font-weight:bold'>
				<td bgcolor='#efefef'>".($i)."</td>
				<td align=left style='padding-left:10px'>
					<input type='text' size='5' style='text-align:center' name='code".($i)."' value='".$ms_data['code'.($i)]."'>  ".$scData[$ms_data['code'.($i)]]['keyword']."
				</td>
				<td>".$scData[$ms_data['code'.($i)]]['searchcnt']."</td>
				<td bgcolor='#efefef'>".($i+10)."</td>
				<td align=left style='padding-left:10px'>
					<input type='text' size='5' style='text-align:center' name='code".($i+10)."' value='".$ms_data['code'.($i+10)]."'> 
					".$scData[$ms_data['code'.($i+10)]]['keyword']."
				</td>
				<td>".$scData[$ms_data['code'.($i+10)]]['searchcnt']."</td>
			</tr>";

		}



	$Contents01 .= "
	</table>
	<table>
		<tr height='30'>
			<td></td>
		</tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
		<tr bgcolor=#ffffff ><td colspan=4 align=center>
			<input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px; margin:10px 0px; ' >
		</td></tr>
	</table>
	<table>
		<tr height='30'>
			<td></td>
		</tr>
	</table>
	</form>
	";
}


$Contents02 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'  class='list_table_box'>
	  <col width=70>
	  <col width=50>
	  <col width=*>
	  <col width=50>
	  <col width=50>
	  <col width=50>
	  <col width=100>
	  <col width=100>
	  <col width=150>
	  <col width=100>
	  <tr height=25 bgcolor=#efefef align=center style='font-weight:bold'>
	    <td class=s_td rowspan=2> 번호</td>
		<td class=m_td rowspan=2> 코드</td>
	    <td class=m_td rowspan=2> 키워드</td>
	    <td class=m_td colspan=3> 검색횟수</td>
	    <td class=m_td rowspan=2> 분류</td>
	    <td class=m_td rowspan=2> 사용유무</td>
	    <td class=m_td rowspan=2> 등록일자</td>
	    <td class=e_td rowspan=2> 관리</td>
	  </tr>
	  <tr height=25 bgcolor=#efefef align=center style='font-weight:bold'>
	    <td class=m_td> 전체</td>
		<td class=m_td> 웹</td>
		<td class=m_td> 모바일</td>
	  </tr>";



$db = new Database;


$max = 20; //페이지당 갯수

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

if($recommend != ''){
	$recommend_str = "WHERE recommend = '$recommend' ";
}

$db->query("SELECT count(*) as total FROM shop_search_keyword $recommend_str ");
//echo "SELECT count(*) as total FROM shop_search_keyword $recommend_str ";
$db->fetch();
$total = $db->dt[total];
//echo $total;

$db->query("SELECT * FROM shop_search_keyword $recommend_str order by searchcnt desc limit $start, $max ");
//echo "SELECT * FROM shop_search_keyword $recommend_str order by searchcnt desc limit $start , $max ";


if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
	$no = $total - ($page - 1) * $max - $i;
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=30 align=center>
		    <td class='list_box_td list_bg_gray' >".$no."</td>
			<td class='list_box_td'>".$db->dt[k_ix]."</td>
		    <td class='list_box_td point' style='text-align:left; padding-left:10px;' >".$db->dt[keyword]."</td>
		    <td class='list_box_td'>".$db->dt[searchcnt]."</td>
			<td class='list_box_td'>".$db->dt[searchcnt_web]."</td>
			<td class='list_box_td'>".$db->dt[searchcnt_mobile]."</td>
		    <td class='list_box_td ' >".($db->dt[recommend]==1 ? "추천검색어":"인기검색어")."</td>
		    <td class='list_box_td list_bg_gray' >".($db->dt[disp] ? "사용":"사용 안함")."</td>
		    <td class='list_box_td ' >".$db->dt[regdate]."</td>
		    <td class='list_box_td list_bg_gray' >";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				$Contents02 .= "
		    	<a href=\"keyword.php?k_ix=".$db->dt[k_ix]."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
			}else{
				$Contents02 .= "
		    	<a href=\"javascript:alert('수정권한이 없습니다.');\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
			}
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
			$Contents02 .= "
	    		<a href=\"javascript:deleteKeywordInfo('delete','".$db->dt[k_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
			}else{
			$Contents02 .= "
	    		<a href=\"javascript:alert('삭제권한이 없습니다.');\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
			}
			$Contents02 .= "
		    </td>
		  </tr> ";
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=100>
		    <td align=center colspan=10>등록된 키워드가 없습니다. </td>
		  </tr>";
}

$Contents02 .= " </table>";
$Contents02 .= "<ul class='paging_area' >
						<li class='front'>".page_bar($total, $page, $max,$query_string,"&recommend=$recommend")."</li>
						<li class='back'></li>
					  </ul>";

$Contents = "<table width='100%' border=0>";
$Contents .= "<tr><td>".$Contents01."</td></tr>";
$Contents .= "<tr><td>".$Contents02."</td></tr>";
$Contents .= "</table>";
$Contents .= "<form name='keywordlist_form' method='POST' action='keyword.act.php' target='act'><input type=hidden name=act value=''><input type=hidden name=k_ix value=''></form>";
/*
$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >해당목록은 사용자가 실제 검색한 검색어 목록입니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >검색횟수는 실제 검색된 횟수입니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >수정하기를 통해 추천검색어로 설정 가능합니다.</td></tr>
</table>
";*/

$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$help_text = HelpBox("키워드 목록", $help_text, 80);
$Contents .= $help_text;
 $Script = "
 <script language='javascript'>


 function deleteKeywordInfo(act, k_ix){
 	if(confirm('해당계좌 키워드를 정말로 삭제하시겠습니까?')){
 		var frm = document.keywordlist_form;
 		frm.act.value = act;
 		frm.k_ix.value = k_ix;
 		frm.submit();
 	}
}
function etcBank(etc){
	if(etc == 'etc'){
		document.getElementById('etc').disabled = false;
	}else{
		document.getElementById('etc').disabled = true;
	}
}
 </script>
 ";


$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = promotion_menu();
$P->Navigation = "프로모션(마케팅) > 검색관리 > 키워드관리";
$P->title = "키워드관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();



/*

create table ".TBL_SHOP_BANKINFO." (
bank_ix int(4) unsigned not null auto_increment  ,
bank_name varchar(20) null default null,
bank_number varchar(20) null default null,
bank_owner varchar(20) null default null,
disp char(1) default '1' ,
regdate datetime not null,
primary key(bank_ix));
*/
?>

