<?
include("../class/layout.class");
include_once("sellertool.lib.php");

$db = new Database;

if( ! empty($site_code) ){
	$where = " and site_code = '".$site_code."' ";
}

if( empty($etc_div) ){
	$etc_div = "B";
}

$sql = "SELECT 
			si.*
		FROM
			sellertool_site_info si
		WHERE
			disp = 1 AND use_mapping_div like '%|".$etc_div."|%'
		".$where."
		GROUP BY
			si_ix
		LIMIT 1 ";
$db->query($sql);
$db->fetch();
$site_name = $db->dt[site_name];
$site_code = $db->dt[site_code];


$max = 100000; //페이지당 갯수

if ($page == '')
{
	$start = 0;
	$page  = 1;
}
else
{
	$start = ($page - 1) * $max;
}


switch($etc_div){
	case "B":
		$etc_div_name = "브랜드";
		$etc_table = "shop_brand";
		$etc_code_column = "b_ix";
		$etc_search_code_column = "e.brand_code";
		$etc_search_name_column = "e.brand_name";
		$etc_select_txt = "e.b_ix as shop_code, e.brand_name as shop_name";
		break;
	case "C";
		$etc_div_name = "제조사";
		$etc_table = "shop_company";
		$etc_code_column = "c_ix";
		$etc_search_code_column = "e.cp_code";
		$etc_search_name_column = "e.company_name";
		$etc_select_txt = "e.c_ix as shop_code, e.company_name as shop_name";
		break;
	case "N";
		$etc_div_name = "원산지(제조국)";
		$etc_table = "common_origin";
		$etc_code_column = "og_ix";
		$etc_search_code_column = "e.origin_code";
		$etc_search_name_column = "e.origin_name";
		$etc_select_txt = "e.og_ix as shop_code, e.origin_name as shop_name";
		break;
	default:
		echo "잘못된 접근입니다.";
		exit;
		break;
	
}


$where = "";

if(!empty($search_text)){
	if( ! empty($search_type) ){
		$where .= " AND ".$search_type." LIKE '%".trim($search_text)."%' ";
	}else{
		$where .= " AND ( ".$etc_search_code_column." LIKE '%".trim($search_text)."%' or ".$etc_search_name_column." LIKE '".trim($search_text)."%')";
	}
}

if(!empty($relation_yn)){
	if($relation_yn == 'Y'){
		$where .= " AND er.elr_ix is not NULL ";
	}else{
		$where .= " AND er.elr_ix is NULL ";
	}
}

$sql = "SELECT 
			SQL_CALC_FOUND_ROWS
			er.elr_ix, e.regdate, er.rel_date, er.target_name, er.target_code,
			".$etc_select_txt."
		FROM 
			".$etc_table." AS e 
		LEFT JOIN 
			sellertool_etc_linked_relation AS er 
		ON 
			(e.".$etc_code_column." = er.origin_code and er.etc_div='".$etc_div."' and site_code = '".trim($site_code)."')
		where
			1=1
		$where
		LIMIT
			$start, $max";

$db->query($sql);
$linkeds = $db->fetchall("object");

$db->query("select FOUND_ROWS() as total ");
$db->fetch();
$total = $db->dt[total];


$Contents01 = "
<div style='width:100%;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk>키타코드 연동 제휴사 목록 </b></div>")."</div>
<div class='tab' style='width:100%;height:38px;margin:20px 0 0 0;'>
	<table class='s_org_tab' cellpadding='0' cellspacing='0' border='0'>
	<tr>
		<td class='tab'>
			<table id='tab_01' ".( $etc_div == "B" ? "class='on'" : "" )." >
			<tr>
				<th class='box_01'></th>
				<td class='box_02' onclick=\"location.href='?etc_div=B'\" style='padding-left:20px;padding-right:20px;'>
					브랜드
				</td>
				<th class='box_03'></th>
			</tr>
			</table>
			<table id='tab_02' ".( $etc_div == "C" ? "class='on'" : "" ).">
			<tr>
				<th class='box_01'></th>
				<td class='box_02' onclick=\"location.href='?etc_div=C'\" style='padding-left:20px;padding-right:20px;'>
					제조사
				</td>
				<th class='box_03'></th>
			</tr>
			</table>
			<table id='tab_03' ".( $etc_div == "N" ? "class='on'" : "" ).">
			<tr>
				<th class='box_01'></th>
				<td class='box_02' onclick=\"location.href='?etc_div=N'\" style='padding-left:20px;padding-right:20px;'>
					제조국
				</td>
				<th class='box_03'></th>
			</tr>
			</table>
		</td>
		<td class='btn'>
		</td>
	</tr>
	</table>
</div>
";

$Contents01 .= "
<form name='search_form' method='get' action='./etc.linked.php' onsubmit='return CheckFormValue(this);' style='display:inline;'>
<input type='hidden' name='etc_div' value='".$etc_div."' />
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >
		<tr>
			<td>
			<table cellpadding=0 cellspacing=0 border=0 width=100% align='center' class='input_table_box'>
				<col width='20%' />
				<col width='30%' />
				<col width='20%' />
				<col width='30%' />
				<tr>
					<td class='input_box_title'>제휴사 선택</td>
					<td class='input_box_item' >
						<table border=0 cellpadding=0 cellspacing=0>
							<tr>
								<td style='padding-right:5px;'>
									".getSellerToolSiteInfo($site_code , "" ,"selectbox" ,"AND use_mapping_div like '%|".$etc_div."|%'")."
								</td>
							</tr>
						</table>
					</td>
					<td class='input_box_title'>".$etc_div_name." 매핑여부</td>
					<td class='input_box_item'  >
						<input type='radio' name='relation_yn'  id='relation_yn_' value='' ".ReturnStringAfterCompare($relation_yn, "", " checked")."><label for='relation_yn_'>전체</label>
						<input type='radio' name='relation_yn'  id='relation_yn_Y' value='Y' ".ReturnStringAfterCompare($relation_yn, "Y", " checked")."><label for='relation_yn_Y'>연결됨</label>
						<input type='radio' name='relation_yn'  id='relation_yn_N' value='N' ".ReturnStringAfterCompare($relation_yn, "N", " checked")."><label for='relation_yn_N'>연결안됨</label>
					</td>
					
				</tr>
				<tr>
					<td class='input_box_title'>  검색어  </td>
					<td class='input_box_item' >
						<table cellpadding=2 cellspacing=0 width=100%>
							<col width='20%'>
							<col width='*'>
							<tr>
								<td>
								<select name='search_type'>
									<option value=''>통합검색</option>
									<option value='".$etc_search_name_column."' ".($search_type == $etc_search_name_column ? "selected":"").">".$etc_div_name."명</option>
									<option value='".$etc_search_code_column."' ".($search_type == $etc_search_code_column ? "selected":"").">".$etc_div_name."코드</option>
								</select>
								</td>
								<td >
								<INPUT id=search_texts  class='textbox' value='".$search_text."' style=' FONT-SIZE: 12px; WIDTH: 90%; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'><br>
								
								</td>
								<td colspan=2 style='padding-left:5px;'><span class='p11 ls1'></span></td>
							</tr>
						</table>
					</td>
					<td class='input_box_title'></td>
					<td class='input_box_item'></td>
				</tr>
			</table>
			</td>
		</tr>
		<tr>
		   <td colspan=2 align=center style='padding:10px 0px;'><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' border=0 align=absmiddle><!--btn_inquiry.gif--></td>
		</tr>
	</table>
</form>";


 
$Contents02 = "<div style='width:100%;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk>".$site_name." 품목(상품)분류 연동 목록(".number_format($total)." 개)</b></div>")."</div>";
$Contents02 .= "
    <table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box' style='margin-top:3px;'>
        <col width=12%>
	    <col width=30%>
	    <col width=10%>
		<col width=*>
		 <col width=7%>
	    <col width=12%>
        <col width=10%>
	  <tr height=30 bgcolor=#efefef align=center style='font-weight:bold'>
	    <td class='m_td'> 제휴사</td>
		<td class='m_td'> ".$etc_div_name."</td>
		 <td class='m_td'> 등록일자</td>
        <td class='m_td'> 연동".$etc_div_name."</td>
        <td class='m_td'> 연동여부</td>
        <td class='m_td'> 매핑일자</td>
	    <td class='m_td'> 관리</td>
	  </tr>";

if(count($linkeds) > 0){
	foreach( $linkeds as $linked ){

		$Contents02 .= "
		  <tr bgcolor=#ffffff height=30 align=center>
			<td class='list_box_td point'>
				<table cellpadding='0' cellspacing='0' border='0' width='100%'>
					<tr>
						<td align='center'>".$site_name."</td>
					</tr>
				</table>
			</td>
            <td class='list_box_td list_bg_gray' style='padding:5px;line-height:130%;'>
				".$linked['shop_name']."
            </td>
			<td class='list_box_td'>".$linked['regdate']."</td>";

		if( ! empty($linked['elr_ix']) ){            
			$Contents02 .="		
            <td class='list_box_td list_bg_gray' style='padding:0px 5px;'>".$linked['target_name']."</td>";
		}else{
			$Contents02 .="		
            <td class='list_box_td list_bg_gray' style='padding:5px;'><ul><li style='text-align:left;cusor:pointer;'><u>없음</u></li></ul></td>";
		}

		$Contents02 .="
            <td class='list_box_td' >".($linked['elr_ix'] != "" ?  "연결됨":"연결안함")."</td>
            <td class='list_box_td list_bg_gray'>".$linked['rel_date']."</td>
		    <td class='list_box_td ' style='padding:0px 5px;' nowrap>";

            if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
                $Contents02.="
		    	<a href=\"javascript:PoPWindow3('./etc.linked.edit.php?mmode=pop&elr_ix=".$linked['elr_ix']."&site_code=".$site_code."&etc_div=".$etc_div."&shop_code=".$linked['shop_code']."&shop_name=".$linked['shop_name']."',900,600,'category_linked_edit')\"'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
            }else{
                $Contents02.="
		    	<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
            }

            if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
                $Contents02.="    
                <a href=\"javascript:delete_linked('".$linked['elr_ix']."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a></td>";
            }else{
                $Contents02.="    
                <a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a></td>";
            }
            $Contents02.="
		    </td>
		  </tr> ";
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=8>등록된 ".$etc_div_name." 연동 정보가 없습니다. </td>
		  </tr>";
}

$Contents02 .= "
	  </table>";

$Contents02 .= "
<table width=100%>
	<tr height=50>
		<td>".page_bar($total, $page, $max,"&etc_div=$etc_div&search_type=$search_type&search_text=$search_text&site_code=$site_code&relation_yn=$relation_yn","")."</td>
	</tr>
</table>";



$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$Contents01."<br>";
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";
$Contents = $Contents."</table >";

$Script = "
<script>
    function delete_linked(elr_ix){
		if(elr_ix == ''){
			alert('매핑된 ".$etc_div_name." 정보가 없습니다.');
			return false;
		}
        var select = confirm('제휴 ".$etc_div_name." 매핑정보를 정말로 삭제하시겠습니까?');
        if(select){
            $.ajax({
                type:'POST',
                data: {'act': 'delete','elr_ix': elr_ix},
                url:'etc.linked.act.php',
                dataType: 'html',
                error: function(data,error){// 실패시 실행
    		        alert(error);},
                success: function(transport){
                    if(transport == 'SUCCESS'){
                        alert('삭제되었습니다'),
                        location.reload();
                    }
                }
            });
        }
    }
</script>

";
      
if($mmode == "pop"){

	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = sellertool_menu();
	$P->NaviTitle = "기타코드 맵핑관리";
	$P->Navigation = "제휴사연동 > 기본정보설정 > 기타코드 맵핑관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();

}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = sellertool_menu();
	$P->title = "기타코드 맵핑관리";
	$P->Navigation = "제휴사연동 > 기본정보설정 > 기타코드 맵핑관리";
	$P->strContents = $Contents;
	$P->ContentsWidth = "90%";
	echo $P->PrintLayOut();
}      

/*
CREATE TABLE `sellertool_received_etc` (
  `sre_ix` int(10) NOT NULL AUTO_INCREMENT COMMENT 'sequence',
  `etc_div` varchar(1) NOT NULL COMMENT '분류 B:브랜드,C:제조사,N:제조국',
  `site_code` varchar(20) NOT NULL COMMENT '사이트코드',
  `code_name` varchar(100) NOT NULL COMMENT '코드명',
  `code` varchar(20) NOT NULL COMMENT '코드',
  `insert_date` datetime DEFAULT NULL COMMENT '입력일',
  PRIMARY KEY (`sre_ix`),
  INDEX (`etc_div`),
  INDEX (`site_code`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='제휴사기타코드정보';


CREATE TABLE `sellertool_etc_linked_relation` (
  `elr_ix` int(12) NOT NULL AUTO_INCREMENT COMMENT 'sequence',
  `etc_div` varchar(1) NOT NULL COMMENT '분류 B:브랜드,C:제조사,N:제조국',
  `site_code` varchar(20) NOT NULL COMMENT '사이트코드',
  `origin_code` varchar(20) NOT NULL COMMENT '우리쪽 코드',
  `origin_name` varchar(100) DEFAULT NULL COMMENT '우리쪽 코드명',
  `target_code` varchar(20) NOT NULL COMMENT '연계된 코드',
  `target_name` varchar(100) DEFAULT NULL COMMENT '연계된 코드명',
  `editdate` datetime NOT NULL COMMENT '수정일자',
  `rel_date` datetime DEFAULT NULL COMMENT '연동 날짜',
  PRIMARY KEY (`elr_ix`),
  INDEX (`etc_div`),
  INDEX (`site_code`),
  INDEX (`origin_code`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='제휴사기타코드연결';
*/


?>