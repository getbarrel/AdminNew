<?
include("../class/layout.class");
include_once("sellertool.lib.php");

$db = new Database;


//echo $sql;
if($site_code == ""){
	$sql = 	"SELECT si.* FROM sellertool_site_info si
				where disp = 1 AND use_mapping_div like '%|I|%'
				group by si_ix limit 1 ";

	$db->query($sql);
	$db->fetch();
	$site_name = $db->dt[site_name];
	$site_code = $db->dt[site_code];

}else{
	$sql = 	"SELECT si.* FROM sellertool_site_info si
				where disp = 1 AND use_mapping_div like '%|I|%' and site_code = '".$site_code."'
				group by si_ix limit 1 ";

	$db->query($sql);
	$db->fetch();
	$site_name = $db->dt[site_name];
	$site_code = $db->dt[site_code];

}



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

$where = "where cid is not null  ";

if(!empty($search_text)){
	if($search_type == "cname"){
		$where .= " AND ci.cname LIKE '%".trim($search_text)."%' ";
	}else if($search_type == "cid"){
		$where .= " AND ci.cid LIKE '".trim($search_text)."%' ";
	}else{
		$where .= " AND (ci.cname LIKE '%".trim($search_text)."%' or ci.cid LIKE '".trim($search_text)."%')";
	}
}

if($site_code != ""){
	$join_where .= "and site_code = '".trim($site_code)."' ";
}
 

if(!empty($api_key)){
	$join_where .= " AND api_key = '".trim($api_key)."' ";
}

if(!empty($depth)){
	$where .= " AND depth <= '".trim($depth)."' ";
}

if(!empty($relation_yn)){
	if($relation_yn == 'Y'){
		$where .= " AND clr.ilr_ix is not NULL ";
	}else{
		$where .= " AND clr.ilr_ix is NULL ";
	}
}


$sql = "SELECT count(*) as total 
		FROM ".$category_table." AS ci 
		LEFT JOIN sellertool_itemtype_linked_relation AS clr ON ci.cid = clr.origin_cid $join_where
		$where ";

//$sql = "SELECT count(*) as total FROM ".$category_table." AS ci  $where ";


//echo $sql;
//exit;
$db->query($sql);


$db->fetch();
$total = $db->dt[total];
//$sql = "select count(*) as total from ".$category_table." ";

$Contents01 = "<div style='width:100%;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk>표준카테고리 분류 연동 제휴사 목록 </b></div>")."</div>";
$Contents01 .= "<form name='search_form' method='get' action='./itemtype.linked.php' onsubmit='return CheckFormValue(this);' style='display:inline;'>
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
                                            ".getSellerToolSiteInfo($site_code , "" ,"selectbox" ,"AND use_mapping_div like '%|I|%'")."
                                        </td>
        							</tr>
        						</table>
        					</td>
							<td class='input_box_title'>표준카테고리 분류 매핑여부</td>
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
											<option value='cname' ".($search_type == "cname" ? "selected":"").">카테고리명</option>
											<option value='cid' ".($search_type == "cid" ? "selected":"").">카테고리코드</option> 
										</select>
										</td>
        								<td >
        								<INPUT id=search_texts  class='textbox' value='' style=' FONT-SIZE: 12px; WIDTH: 90%; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'><br>
        								
        								</td>
        								<td colspan=2 style='padding-left:5px;'><span class='p11 ls1'></span></td>
        							</tr>
        						</table>
        					</td>
							<td class='input_box_title'>카테고리 depth</td>
        					<td class='input_box_item'  >
        						<select name='depth'>
									<option value=''>카테고리 depth</option>
									<option value='1' ".($depth == 1 ? "selected":"").">카테고리 1 depth</option>
									<option value='2' ".($depth == 2 ? "selected":"").">카테고리 2 depth</option>
									<option value='3' ".($depth == 3 ? "selected":"").">카테고리 3 depth</option>
									<option value='4' ".($depth == 4 ? "selected":"").">카테고리 4 depth</option>
								</select>
        					</td>
                        </tr>
                    </table>
					</td>
                </tr>
                <tr>
	               <td colspan=2 align=center style='padding:10px 0px;'><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' border=0 align=absmiddle><!--btn_inquiry.gif--></td>
                </tr>
            
        </table></form>";

 
$Contents02 = "<div style='width:100%;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk>".$site_name." 표준카테고리(상품)분류 연동 목록(".number_format($total)." 개)</b></div>")."</div>";
$Contents02 .= "
    <table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box' style='margin-top:3px;'>
        <col width=12%>
        <col width=5%>
	    <col width=30%>
		<col width=*>
	    <col width=7%>
	    <col width=10%>
	    <col width=12%>
        <col width=10%>
	  <tr height=40 bgcolor=#efefef align=center style='font-weight:bold'>
	    <td class='m_td'> 제휴사</td>
        <td class='m_td'> depth</td>
		<td class='m_td'> 카테고리</td>
        <td class='m_td'> 연동카테고리<br>(제휴사 표준카테고리(상품)분류)</td>
        <td class='m_td'> 연동여부</td>
	    <td class='m_td'> 등록일자</td>
        <td class='m_td'> 매핑일자</td>
	    <td class='m_td'> 관리</td>
	  </tr>";

$sql = "SELECT  ci.*, clr.*
			FROM ".$category_table." ci 
			left join sellertool_itemtype_linked_relation clr on ci.cid = clr.origin_cid $join_where		
			$where 
			order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 
			limit $start, $max";

//echo nl2br($sql);
//exit;
$db->query($sql);
$category_linkeds = $db->fetchall();
//print_r($category_linkeds);
if(count($category_linkeds) > 0){
	for($i=0;$i < count($category_linkeds) ;$i++){
	//$db->fetch($i);
	
	$category_linked_info = $category_linkeds[$i];

	$Contents02 .= "
		  <tr bgcolor=#ffffff height=30 align=center>
			<td class='list_box_td point'>
				<table cellpadding='0' cellspacing='0' border='0' width='100%'>
					<tr>
						<td align='center'>".$site_name."</td>
					</tr>
				</table>
			</td>
             <td>".$category_linkeds[$i][depth]."</td>
            <td class='list_box_td list_bg_gray' style='padding:5px;line-height:130%;'>
            <table cellpadding='0' cellspacing='0' border='0' width='100%'>
                <tr>
                    <td width=".(20*$category_linkeds[$i][depth])."></td>
        		    <td class='list_box_td' style='text-align:left;'>".$category_linkeds[$i][cname]." (<a href='?site_code=".$site_code."&search_type=cid&search_text=".substr($category_linkeds[$i][cid],0,($category_linkeds[$i][depth]+1)*3)."'>".$category_linkeds[$i][cid]."</a>)</td>
                </tr>
            </table>
            </td>";
    if($category_linked_info[target_name] != ""){            
        $Contents02 .="		
            <td class='list_box_td list_bg_gray' style='padding:0px 5px;'>".$category_linked_info[target_name]."( ".($category_linked_info[target_depth]+1)." depth )</td>";
        
    }else{
        $Contents02 .="		
            <td class='list_box_td list_bg_gray' style='padding:5px;'><ul id='category_linked_list_".$category_linkeds[$i][cid]."' cid='".$category_linkeds[$i][cid]."' depth='".$category_linkeds[$i][depth]."'  site_code='".$site_code."' ><li style='text-align:left;cusor:pointer;'><u>없음</u></li></ul></td>";
			//onclick=\"SearchLinkedCategory($(this),'".$site_code."','".$category_linkeds[$i][cname]."')\"
    }
        $Contents02 .="
            <td class='list_box_td ' id='link_result'>".($category_linkeds[$i][ilr_ix] != "" ?  "연결됨":"연결안함")."</td>
		    <td class='list_box_td list_bg_gray'>".$category_linkeds[$i][regdate]."</td>
            <td class='list_box_td list_bg_gray' id='link_date'>".$category_linked_info[rel_date]."</td>
		    <td class='list_box_td ' style='padding:0px 5px;' nowrap>";
            if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
                $Contents02.="
		    	<a href=\"javascript:PoPWindow3('./itemtype.linked.edit.php?mmode=pop&ilr_ix=".$category_linkeds[$i][ilr_ix]."&cid=".$category_linkeds[$i][cid]."&depth=".$category_linkeds[$i][depth]."&site_code=".$site_code."',900,600,'category_linked_edit')\"'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
            }else{
                $Contents02.="
		    	<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
            }
            if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
                $Contents02.="    
                <a href=\"javascript:delete_linked('".$category_linkeds[$i][ilr_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a></td>";
            }else{
                $Contents02.="    
                <a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a></td>";
            }
            $Contents02.="
		    </td>
		  </tr> ";
	
	unset($category_linked_info);
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=8>등록된 표준카테고리(상품)분류 연동 정보가 없습니다. </td>
		  </tr>";
}
$Contents02 .= "

	  </table>";

$Contents02 .= "<table width=100%>
						<tr height=50>
						<td>".page_bar($total, $page, $max,"&cid2=$cid2&depth=$depth&search_type=$search_type&search_text=$search_text&site_code=$site_code&relation_yn=$relation_yn","")."</td>
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
    function delete_linked(ilr_ix){
		if(ilr_ix == ''){
			alert('매핑된 제휴표준카테고리(상품)분류 정보가 없습니다.');
			return false;
		}
        var select = confirm('제휴 표준카테고리(상품)분류 매핑정보를 정말로 삭제하시겠습니까?');
        if(select){
            $.ajax({
                type:'POST',
                data: {'act': 'delete','ilr_ix': ilr_ix},
                url:'itemtype.linked.act.php',
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

/*
function saveCategory(obj, trObj){
	var cname = obj.html();
	var origin_cid = obj.parent().attr('cid');
	var target_cid = obj.attr('target_cid');
	var target_depth = obj.attr('target_depth');
	
	var site_code = obj.parent().attr('site_code');
	var origin_depth = obj.parent().attr('depth');
	alert(cname+'::'+target_depth);
	//return;
	$.ajax({ 
		type: 'GET', 
		data: {'act': 'json_insert', 'site_code':site_code, 'origin_cid':origin_cid, 'origin_depth':origin_depth, 'target_cid':target_cid, 'target_depth':target_depth, 'cname':cname},
		url: './category.linked.act.php',  
		dataType: 'json', 
		async: false, 
		beforeSend: function(){ 
				//alert(1);
		},  
		success: function(results){ 
			alert(results.message); 
			var data = results.data;
		 
			obj.parent().html(cname+'( '+(target_depth+1)+' depth) ');
			//alert($(obj).parent().parent().html());
			trObj.find('td[id=link_result]').html('연결됨');
			trObj.find('td[id=link_date]').html(data.rel_date);
			
		} 
	}); 
 
}
	
function SearchLinkedCategory(obj_id, site_code, cname){
	
	$.ajax({ 
		type: 'GET', 
		data: {'act': 'get_category_list', 'site_code':site_code, 'cname':cname},
		url: './category.linked.act.php',  
		dataType: 'json', 
		async: false, 
		beforeSend: function(){ 
				//alert(1);
		},  
		success: function(category_lists){ 
			//alert(category_lists); 
			
			if(category_lists != null){
				//alert(category_lists.length); 
				var selected_obj = $(obj_id);
				$.each(category_lists, function(i,category_info){ 
					//alert(category_info.disp_name);
					
					if(i == 0){
						newRow = selected_obj.find('li:first');
						
					}else{
						newRow = selected_obj.find('li:first').clone(true);
					}
					newRow.html(category_info.category_path);
					newRow.attr('target_cid',category_info.disp_no);
					newRow.attr('target_depth',category_info.depth);

					newRow.unbind('click').click(function(){
						saveCategory($(this), newRow.closest('tr'));
					});
					selected_obj.append(newRow);
				});  
				selected_obj.unbind('click');
				selected_obj.attr('onclick','');
			}else{
				alert('매핑되어 있는 표준카테고리(상품)분류가 없습니다.');
			}
 
		} 
	}); 
 
}
*/
</script>

";
      
if($mmode == "pop"){

	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = sellertool_menu();
	$P->NaviTitle = "표준카테고리 맵핑관리";
	$P->Navigation = "제휴사연동 > 기본정보설정 > 표준카테고리 맵핑관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();

}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = sellertool_menu();
	$P->title = "표준카테고리 맵핑관리";
	$P->Navigation = "제휴사연동 > 기본정보설정 > 표준카테고리 맵핑관리";
	$P->strContents = $Contents;
	$P->ContentsWidth = "90%";
	echo $P->PrintLayOut();
}      


/*
CREATE TABLE `sellertool_itemtype_linked_relation` (
  `ilr_ix` int(12) NOT NULL AUTO_INCREMENT COMMENT 'sequence',
  `ila_ix` int(12) DEFAULT NULL COMMENT '링크카테고리 참조키',
  `site_name` varchar(50) NOT NULL COMMENT '사이트명',
  `site_code` varbinary(20) NOT NULL COMMENT '사이트코드',
  `origin_cid` varchar(20) NOT NULL COMMENT '우리쪽 cid',
  `origin_name` varchar(100) DEFAULT NULL COMMENT '우리쪽 카테고리명',
  `origin_depth` int(2) DEFAULT NULL COMMENT '우리쪽 depth',
  `target_cid` varchar(20) NOT NULL COMMENT '연계된 cid',
  `target_name` varchar(100) DEFAULT NULL COMMENT '연계된 카테고리명',
  `target_depth` int(2) DEFAULT NULL COMMENT '연계된 카테고리depth',
  `editdate` datetime NOT NULL COMMENT '수정일자',
  `rel_date` datetime DEFAULT NULL COMMENT '연동 날짜',
  PRIMARY KEY (`ilr_ix`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='제휴사품목(상품)분류연결';
*/
?>
