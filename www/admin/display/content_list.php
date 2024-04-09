<?
include("../class/layout.class");
//include("../webedit/webedit.lib.php");
include($_SERVER["DOCUMENT_ROOT"]."/include/menu.tmp.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/menuline.class");

$db = new Database;
$sql = "select            
			  config_value as front_url
			from
			  shop_mall_config
		    where
			mall_ix = '".$_SESSION['admininfo']['mall_ix']."'
			and config_name = 'front_url'";

$db->query($sql);
$db->fetch();
$front_url = $db->dt['front_url'];

$language_list = getTranslationType("","","array");

$display_yn_hidden = "display:none;";
$content = "
<script  id='dynamic'></script>
<script>
/*	 Create Tree		*/
	var tree = new Tree();
	tree.color = \"black\";
	tree.bgColor = \"white\";
	tree.borderWidth = 0;

	var rootnode = new TreeNode(\"배럴컨텐츠\", \"../resources/ServerMag_Etc_Root.gif\",\"../resources/ServerMag_Etc_Root.gif\");
	rootnode.action = \"setContentList('','000000000000000',-1,'')\";
	rootnode.expanded = true;
";

if($_GET['cid']){
    $schDepth           = $_GET['depth'];
    $schCid             = $_GET['cid'];
    $schCid1            = (int)substr($schCid,0,3);
    $schCid2            = (int)substr($schCid,3,3);
    $schCid3            = (int)substr($schCid,6,3);
    $schCid4            = (int)substr($schCid,9,3);
    $schCid5            = (int)substr($schCid,12,3);
    if($schDepth == 0){
        $queryCid = substr($schCid,0,3);
    }else if($schDepth == 1){
        $queryCid = substr($schCid,0,6);
    }else if($schDepth == 2){
        $queryCid = substr($schCid,0,9);
    }else if($schDepth == 3){
        $queryCid = substr($schCid,0,12);
    }else if($schDepth == 4){
        $queryCid = $schCid;
    }
    $schContentType     = $_GET['content_type'];
}else{
    $schDepth           = 0;
    $schCid             = 0;
    $schCid1            = 0;
    $schCid2            = 0;
    $schCid3            = 0;
    $schCid4            = 0;
    $schCid5            = 0;
    $queryCid           = 0;
    $schContentType     = 0;
}

$db->query("SELECT * FROM shop_content_class where depth in(0,1,2,3,4)  order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5");

$total = $db->total;
for ($i = 0; $i < $db->total; $i++)
{

	$db->fetch($i);

	if ($db->dt["depth"] == 0){
		$content = $content.PrintNode($db);
	}else if($db->dt["depth"] == 1){
		$content = $content.PrintGroupNode($db);
	}else if($db->dt["depth"] == 2){
		$content = $content.PrintGroupNode($db);
	}else if($db->dt["depth"] == 3){
		$content = $content.PrintGroupNode($db);
	}else if($db->dt["depth"] == 4){
		$content = $content.PrintGroupNode($db);
	}else if($db->dt["depth"] == 5){
		$content = $content.PrintGroupNode($db);
	}
}

$content = $content."
	tree.addNode(rootnode);
	tree.draw();
	if($schCid > 0){
        if($schCid5 > 0) {
            tree.nodes[0].nodes[$schCid1-1].nodes[$schCid2-1].nodes[$schCid3-1].nodes[$schCid4-1].nodes[$schCid5-1].select();
        }else{
            if($schCid4 > 0) {
                tree.nodes[0].nodes[$schCid1-1].nodes[$schCid2-1].nodes[$schCid3-1].nodes[$schCid4-1].select();
            }else{
                if($schCid3 > 0) {
                    tree.nodes[0].nodes[$schCid1-1].nodes[$schCid2-1].nodes[$schCid3-1].select();
                }else{
                    if($schCid2 > 0) {
                        tree.nodes[0].nodes[$schCid1-1].nodes[$schCid2-1].select();
                    }else{
                        if($schCid1 > 0) {
                            tree.nodes[0].nodes[$schCid1-1].select();
                        }
                    }
                }
            }    
        }
    }else{
        tree.nodes[0].select();
    }
	
	function contentDelete(conIx, contentType){
	    // 사용자 ID(문자열)와 체크박스 값들(배열)을 name/value 형태로 담는다.
        var allData = { 'mode': 'Del', 'con_ix': conIx, 'content_type': contentType};

        if(confirm('삭제 하시겠습니까?')) {
            $.ajax({
                url:'./content.save.php',
                type:'POST',
                data: allData,
                success:function(data){
                    alert('삭제 되었습니다.');
                    location.reload();
                },error:function(jqXHR, textStatus, errorThrown){
                    alert('에러 발생~~' + textStatus + ' : ' + errorThrown);
                }
            });
        }
	}
	
</script>";

$Contents = "
<script language='JavaScript' src='../include/cTree.js'></script>
<script language='JavaScript' src='content_class.js'></script>
<script language='JavaScript' src='../include/manager.js'></script>
<script type='text/javascript' src='../colorpicker/farbtastic.js'></script>
<link rel='stylesheet' href='../colorpicker/farbtastic.css' type='text/css' />

<table cellpadding=0 cellspacing=0 border=0 width='100%'>
    <tr>
        <td align='left' colspan=3> ".GetTitleNavigation("컨텐츠관리", "컨텐츠관리 > ")."</td>
    </tr>
    <tr>
        <td valign=top width='100%' align='left' style=''>
            <table cellpadding=0 cellspacing=0 width=100% >
                <tr>
				    <td width='15%' align='left'  valign='top'>
                        <table cellpadding=0 cellspacing=0 width=100% class='input_table_box'>
                            <tr>
                                <td valign=top width=236>
                                    <div>
                                        <table cellpadding=0 cellspacing=0 border=0 bgcolor=#e2e2e2 width=100% style='border:3px solid #d8d8d8'>
                                        <tr>
                                            <td style='padding-left:10px;padding-top:5px;padding-bottom:3px;' valign=middle nowrap></td>
                                        </tr>
                                        <tr>
                                            <td colspan=2 width=200 height=400 valign=top style='overflow:auto;padding:0 10px 10px 10px;'>
                                                <div style=\"width:200px;height:418px;padding:5px;overflow:auto;margin:1;background-color:#ffffff\" >
                                                    $content
                                                </div>
                                            </td>
                                        </tr>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
				    <td style='padding-left:13px;'></td>
                    <td width='82%' align='right' valign='top'>
                        <form name='sForm' method='get' action='content_list.php' style='display:inline;'>
                        <input type='hidden' name='depth' value=''>
                        <input type='hidden' name='cid' value=''>
                        <input type='hidden' name='content_type' value=''>
                        <table cellpadding=0 cellspacing=1 border=0 width=100% align='center' class='search_table_box'>
                            <col width=20%>
                            <col width=30%>
                            <col width=20%>
                            <col width=30%>
                            <tr>
                                <td class='search_box_title'> 전시구분</td>
                                <td class='search_box_item' colspan=3>".GetDisplayDivision($mall_ix, "select")." </td>
                            </tr>
                            <tr>
                                <td class='search_box_title'> 전시위치</td>
                                <td class='search_box_item' colspan=3><div id='selected_content_1' >미선택 --> 왼쪽 분류에서 컨텐츠를 선택해주세요. </div></td>
                            </tr>
                            <tr>
                                <td class='search_box_title' > 조건검색</td>
                                <td class='search_box_item' colspan=3>
                                    <select name='search_type'>
                                        <option value='' ".(($search_type == '')?"selected":"").">전체</option>
                                        <option value='title' ".(($search_type == 'title')?"selected":"").">제목</option>
                                    </select>
                                    <input type='text' class=textbox style='width: 800px; ' name=search_text value='$search_text'> 
                                </td>
                            </tr>
                            <tr>
                                <td class='search_box_title' > 사용여부</td>
                                <td class='search_box_item'>
                                    <input type='radio' name='sch_display_use'  id='sch_display_use_' value='' ".ReturnStringAfterCompare($sch_display_use, "", " checked")."><label for='sch_display_use_'>전체</label>
                                    <input type='radio' name='sch_display_use'  id='sch_display_use_Y' value='Y' ".ReturnStringAfterCompare($sch_display_use, "Y", " checked")."><label for='sch_display_use_Y'>사용중</label>
                                    <input type='radio' name='sch_display_use'  id='sch_display_use_N' value='N' ".ReturnStringAfterCompare($sch_display_use, "N", " checked")."><label for='sch_display_use_N'>종료</label>
                                </td>
                                <td class='search_box_title' >  전시여부</td>
                                <td class='search_box_item'>
                                    <input type='radio' name='sch_display_state'  id='sch_display_state_' value='' ".ReturnStringAfterCompare($sch_display_state, "", " checked")."><label for='sch_display_state_'>전체</label>
                                    <input type='radio' name='sch_display_state'  id='sch_display_state_D' value='D' ".ReturnStringAfterCompare($sch_display_state, "D", " checked")."><label for='sch_display_state_D'>전시중</label>
                                    <input type='radio' name='sch_display_state'  id='sch_display_state_E' value='E' ".ReturnStringAfterCompare($sch_display_state, "E", " checked")."><label for='sch_display_state_E'>전시대기</label>
                                    <input type='radio' name='sch_display_state'  id='sch_display_state_W' value='W' ".ReturnStringAfterCompare($sch_display_state, "W", " checked")."><label for='sch_display_state_W'>종료</label>
                                </td>
                            </tr>
                            <tr>
                                <td class='search_box_title' nowrap>
                                    <label for='search_start_date'><b>시작일자</b></label><input type='checkbox' name='search_start_date' id='search_start_date' value='1' onclick='ChangeRegistDate(document.search_banner);' ".(($search_start_date==1)?"checked":"").">
                                  </td>
                                  <td class='search_box_item'  colspan=3>
                                  ".search_date('start_sdate','start_edate',$start_sdate,$start_edate,'N','D')."
                                  </td>
                            </tr>
                            <tr>
                                <td class='search_box_title' nowrap>
                                    <label for='search_end_date'><b>종료일자</b></label><input type='checkbox' name='search_end_date' id='search_end_date' value='1' onclick='ChangeRegistDate(document.search_banner);' ".(($search_end_date==1)?"checked":"").">
                                  </td>
                                  <td class='search_box_item'  colspan=3>
                                  ".search_date('end_sdate','end_edate',$end_sdate,$end_edate,'N','D')."
                                  </td>
                            </tr>
                        </table>
                        <table cellpadding=5 cellspacing=0 width=100% border=0 align=center style=''>
                            <col width=20%>
                            <col width=50%>
                            <col width=30%>
                            <tr>
                                <td align ='center'></td>
";
	if($schCid == "") {
$Contents .= " 
								
									<td align ='center'><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' border=0 align=absmiddle></td>
									<td align ='right'></td>
";
	}else{
$Contents .= " 
								
									<td align ='center'><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' border=0 align=absmiddle></td>
									<td align ='right'><input type='button' value='컨텐츠변경' onclick=\"changeContentViewOrder('".$schDepth."', '".$schCid."')\" /></td>
";
	}
$Contents .= " 
                                
                            </tr>
                        </table>
                        </form>
";
                        $where = " WHERE c.cid like '$queryCid%' ";
                        //$where = " where c.cid = '$schCid' and c.depth = $schDepth ";

                        if($mall_ix!=""){
                            $where .= " AND c.mall_ix ='".$mall_ix."' ";
                        }

                        if($search_type != "" && $search_text != ""){
                            $where .= " AND $search_type LIKE  '%$search_text%' ";
                        }

                        if($sch_display_use != ""){
                            $where .= " AND c.display_use =  '$sch_display_use' ";
                        }

                        if($sch_display_state != ""){
                            $where .= " AND c.display_state =  '$sch_display_state' ";
                        }

                        if($search_start_date == "1"){
                            $start_sdate_where = mktime(0,0,0,substr($start_sdate,5,2),substr($start_sdate,8,2),substr($start_sdate,0,4));
                            $start_edate_where = mktime(0,0,0,substr($start_edate,5,2),substr($start_edate,8,2),substr($start_edate,0,4));
                            $where .= " AND  c.display_start BETWEEN  $start_sdate_where AND $start_edate_where ";
                        }

                        if($search_end_date == "1"){
                            $end_sdate_where = mktime(0,0,0,substr($end_sdate,5,2),substr($end_sdate,8,2),substr($end_sdate,0,4));
                            $end_edate_where = mktime(0,0,0,substr($end_edate,5,2),substr($end_edate,8,2),substr($end_edate,0,4));
                            $where .= " AND  c.display_end BETWEEN  $end_sdate_where AND $end_edate_where ";
                        }

                        $sql = "SELECT * FROM shop_content c $where";
                        $mdb->query($sql);
                        $total = $mdb->total;

                        $max = 10;

                        if ($page == ''){
                            $start = 0;
                            $page  = 1;
                        }else{
                            $start = ($page - 1) * $max;
                        }
$Contents .= "
                        <table cellpadding=4 cellspacing=0 border=0 width=100% class='list_table_box' >
                            <col width='4%'>
                            <col width='5%'>
                            <col width='7%'>						
                            <col width='*'>
                            <col width='13%'>
                            <col width='13%'>
                            <col width='6%'>
                            <col width='6%'>
                            <col width='7%'>
                            <col width='7%'>
                            <col width='8%'>
                            <tr height=30 align=center>
                                <td align='center' class='m_td'><font color='#000000'><b>번호</b></font></td>
                                <td class=m_td >전시<br>구분</td>
                                <td class=m_td >분류</td>
                                <td class=m_td id='titleName'>제목</td>
                                <td class=m_td >대표이미지</td>
                                <td class=m_td >전시기간</td>
                                <td class=e_td >전시<br>상태</td>
                                <td class=e_td >사용<br>여부</td>
                                <td class=e_td >작업자</td>
                                <td class=e_td >등록일</td>
                                <td class=e_td >관리</td>
                            </tr>
";
                        if ($mdb->total == 0) {
$Contents .= "           
                            <tr bgcolor=#ffffff height=70><td colspan=12 align=center>내역이 존재 하지 않습니다.</td></tr>
";
                        }else{
                            $sql = "SELECT cc.cname, cc.content_type, c.con_ix, c.cid, c.mall_ix, c.depth, c.title, c.list_img, c.display_use, c.display_state, c.display_start, c.display_end, c.worker_ix, c.regdate, c.sort   
                                    FROM shop_content_class cc LEFT JOIN shop_content c ON cc.cid = c.cid 
                                    $where 
                                    ORDER BY c.sort asc, c.regdate DESC 
                                    LIMIT $start, $max
                            ";

                            $db->query($sql);
                            $content_infos = $db->fetchall("object");

                            for($i=0;$i < count($content_infos);$i++){
                                $no = $total - ($page - 1) * $max - $i;

                                $sql = "SELECT AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') AS name FROM ".TBL_COMMON_MEMBER_DETAIL." cmd WHERE code= '".$content_infos[$i][worker_ix]."' ";
                                $db->query($sql);
                                $db->fetch();
                                $worker_name = $db->dt[name];

                                if($content_infos[$i][display_use] == "Y"){
                                    $display_use = "사용";
                                }else if($content_infos[$i][display_use] == "N"){
                                    $display_use = "미사용";
                                }

                                if($content_infos[$i][display_state] == "D"){
                                    $display_state = "전시중";
                                }else if($content_infos[$i][display_state] == "W"){
                                    $display_state = "전시대기";
                                }else if($content_infos[$i][display_state] == "E"){
                                    $display_state = "종료";
                                }

                                $img = "";
                                if($content_infos[$i][list_img] != ""){
                                    $img = "<img src='".$_SESSION["admin_config"]["mall_data_root"] . "/images/content/".$content_infos[$i][con_ix]."/".$content_infos[$i][list_img]."' style='width:75px;'>";
                                }
$Contents .= "           
                                <tr height=30 bgcolor=#ffffff align=center>
                                    <td class='list_box_td '>".$no." </td>
                                    <td class='list_box_td list_bg_gray'>".GetDisplayDivision($content_infos[$i][mall_ix], "text")."</td>
                                    <td class='list_box_td '>".$content_infos[$i][cname]."</td>
                                    <td class='list_box_td point' style='text-align:left;padding:5px 5px 5px 10px;font-weight:normal;line-height:150%;'>".nl2br($content_infos[$i][title])."</td>
                                    <td class='list_box_td '>".$img."</td>
                                    <td class='list_box_td '>".date("Y.m.d",$content_infos[$i][display_start])." ~ ".date("Y.m.d",$content_infos[$i][display_end])."</td>
                                    <td class='list_box_td '>".$display_state."</td>
                                    <td class='list_box_td '>".$display_use."</td>
                                    <td class='list_box_td list_bg_gray ' style='line-height:140%;'>".($content_infos[$i][worker_ix] == "" ? "없음":$worker_name)."</td>
                                    <td class='list_box_td '>".str_replace("-",".",substr($content_infos[$i][regdate],0,10))."</td>
                                    <td class='list_box_td list_bg_gray' nowrap>
                                        <a href='content_edit.php?con_ix=".$content_infos[$i][con_ix]."&cid=".$content_infos[$i][cid]."&content_type=".$content_infos[$i][content_type]."&depth=".$content_infos[$i][depth]."&mode=Upd'><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle alt='수정' title='수정'></a>
                                        <a href=\"JavaScript:contentDelete('".$content_infos[$i][con_ix]."', '".$content_infos[$i][content_type]."')\"><img  src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle style='margin:0px 5px;' alt='삭제' title='삭제'></a>
                                    </td>
                                </tr>
";
                            }
                        }
$Contents .= "           
                        </table>
";

$Contents .= "
                        <table cellpadding=5 cellspacing=0 width=100% border=0 align=center style=''>
                            <tr>
                                <td colspan=3 align=left>".page_bar($total, $page, $max,  "&max=$max&depth=$depth&cid=$cid&content_type=$content_type&mall_ix=$mall_ix&search_type=$search_type&search_text=$search_text&display_use=$sch_display_use&display_state=$sch_display_state&start_sdate=$start_sdate&start_edate=$start_edate&end_sdate=$end_sdate&end_edate=$end_edate","")."</td>
                                <td align ='right'><a href='javascript:void(0)' onclick='editMove();'><input type=image src='../images/btm_reg.gif' border=0 align=absmiddle></a></td>
                            </tr>
                        </table>";
$Contents .= "
			        </td>
		        </tr>
		    </table>
	    </td>
    </tr>
</table>
";

$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');

$Contents .= "
		<iframe name='calcufrm' id='calcufrm' src='' width=0 height=0></iframe>
<script>
if($schCid > 0){
    setContentList('','$schCid',$schDepth,$schContentType);
}
function changeContentViewOrder(depth, display_cid){
    PoPWindow3('contentViewOrderChange.php?mmode=pop&depth='+depth+'&display_cid='+display_cid+'',1100,800,'contentViewOrderChange');
    
}
</script>		
";

$P = new LayOut;
$addScript = "
<script language='JavaScript' src='../ckeditor/ckeditor.js'></script>\n
<script src='../include/rightmenu.js'></script>\n
<SCRIPT type='text/javascript'>

function setcolorpicker(div_id,input_id){
	
	$('#'+div_id).farbtastic('#'+input_id);		//색상표선택
	$('#'+div_id).css('display','');

}

function department_del(dp_ix){
	$('#department_row_'+dp_ix).remove();
}

function person_del(code){
	$('#row_'+code).remove();
}

function contentProduct(frm){
    var cid = frm.cid.value; 
    var depth = frm.this_depth.value;
    if(cid){
        PoPWindow('content_product.php?cid='+cid+'&depth='+depth,850,550,'content_product');
    }else{
        alert('카테고리를 선택해 주세요')
    }
}

function editMove(){
	if(document.sForm.cid.value == ''){
		alert('등록할 분류 타입을 선택하세요.');
		return true;
	}
    
    if(document.sForm.depth.value <= 0){
		alert('등록할 분류 타입을 선택하세요.');
		return true;
	}

	location.href = './content_edit.php?cid='+document.sForm.cid.value+'&content_type='+document.sForm.content_type.value+'&depth='+document.sForm.depth.value+'&mode=Ins';
}

</SCRIPT>
".$Script;
$P->addScript = $addScript; /**/
$P->OnloadFunction = ""; //showSubMenuLayer('storeleft'); MenuHidden(false);
$P->title = "";//text_button('#', " ♣ 분류 구성"); 
$P->strLeftMenu = display_menu();


$P->strContents = $Contents;
$P->Navigation = "컨텐츠관리 > 컨텐츠관리 > 컨텐츠 목록";
$P->title = "컨텐츠 목록";
$P->PrintLayOut();


function PrintRootNode($cname){

	$cname = str_replace("\"","&quot;",$cname);
	$cname = str_replace("'","&#39;",$cname);

	$vPrintRootNode = "var rootnode = new TreeNode('$cname', '../resources/ServerMag_Etc_Root.gif','../resources/ServerMag_Etc_Root.gif');/n
			rootnode.expanded = true;\n\n";

	return $vPrintRootNode;
}

function PrintNode($mdb){
	
	$cdb = new Database;

	$cid			= $mdb->dt[cid];
	$depth			= $mdb->dt[depth];
	$cname			= $mdb->dt[cname];
	$content_type	= $mdb->dt[content_type];

	$cid1 = substr($cid,0,3);
	$cid2 = substr($cid,3,3);
	$cid3 = substr($cid,6,3);
	$cid4 = substr($cid,9,3);
	$cid5 = substr($cid,12,3);

	$cname			= str_replace("\"","&quot;",$cname);
	$cname			= str_replace("'","&#39;",$cname);

	return "	var node$cid = new TreeNode('$cname', '../resources/Common_TreeNode_CodeManage.gif', '../resources/Common_TreeNode_CodeManage.gif');
	node$cid.expanded = true;
	node$cid.action = \"setContentList('$cname', '$cid', $depth, $content_type)\";
	rootnode.addNode(node$cid);\n\n";
}

function PrintGroupNode($mdb)
{
	global $cid;

	$mcid			= $mdb->dt[cid];
	$depth			= $mdb->dt[depth];
	$cname			= $mdb->dt[cname];
	$content_type	= $mdb->dt[content_type];

	$cid1 = substr($mcid,0,3);
	$cid2 = substr($mcid,3,3);
	$cid3 = substr($mcid,6,3);
	$cid4 = substr($mcid,9,3);
	$cid5 = substr($mcid,12,3);

	$Parentdepth = $depth - 1;

	if ($depth+1 == 1){
		$cid1 = "000";
	}else if($depth+1 == 2){
		$cid2 = "000";
	}else if($depth+1 == 3){
		$cid3 = "000";
	}else if($depth+1 == 4){
		$cid4 = "000";
	}else if($depth+1 == 5){
		$cid5 = "000";
	}

	$parent_cid = "$cid1$cid2$cid3$cid4$cid5";

	if ($depth ==1){
		$ParentNodeCode = "node$parent_cid";
	}else if($depth ==2){
		$ParentNodeCode = "groupnode$parent_cid";
	}else if($depth ==3){
		$ParentNodeCode = "groupnode$parent_cid";
	}else if($depth ==4){
		$ParentNodeCode = "groupnode$parent_cid";
	}else if($depth ==5){
		$ParentNodeCode = "groupnode$parent_cid";
	}

	$cname			= str_replace("\"","&quot;",$cname);
	$cname			= str_replace("'","&#39;",$cname);

	$mstring =  "		var groupnode$mcid = new TreeNode('$cname ',TREE_FOLDER_CLOSED_IMG,TREE_FOLDER_OPEN_IMG);\n";
	if ($mcid == $cid || (substr($mcid,0,($depth)*3) == substr($_GET["cid"],0,($depth)*3) && $_GET["depth"] > $depth )  || (substr($mcid,0,($depth+1)*3) == substr($_GET["cid"],0,($depth+1)*3)) ){//
		$mstring .=  "	groupnode$mcid.expanded = true;\n";
	}


	$mstring .=  "	groupnode$mcid.tooltip = '$cname';
		groupnode$mcid.id ='nodeid$mcid';
		groupnode$mcid.action = \"setContentList('$cname', '$mcid', $depth, $content_type)\";
		$ParentNodeCode.addNode(groupnode$mcid);\n\n";

	return $mstring;
}


?>