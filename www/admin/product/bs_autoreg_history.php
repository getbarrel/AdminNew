<?
include("../class/layout.class");
include_once("buyingService.lib.php");

$db = new Database;
/*
$db2 = new Database;

$sql = "select id from shop_product where product_type = 1 ";
$db->query ($sql);

for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
	$db2->query ("update shop_product_buyingservice_priceinfo set bs_use_yn = '1' where pid ='".$db->dt[id]."' limit 1");
}
echo "처리완료";
exit;
*/
$sql = "select * from shop_buyingservice_autoupdate_history order by regdate desc limit 0,1 ";

$db->query ($sql);

if($db->total){
	$db->fetch();

	$exchange_rate = $db->dt[exchange_rate];
	$bs_basic_air_shipping = $db->dt[bs_basic_air_shipping];
	$bs_add_air_shipping = $db->dt[bs_add_air_shipping];

	$bs_duty = $db->dt[bs_duty];
	$bs_supertax_rate = $db->dt[bs_supertax_rate];
	$clearance_fee = $db->dt[clearance_fee];
}


$max = 20; //페이지당 갯수

if ($page == '')
{
	$start = 0;
	$page  = 1;
}
else
{
	$start = ($page - 1) * $max;
}

$where = "where bsah.bsah_ix is not null and bsah.autoupdate_type = 'new_goods_reg' ";
if($cid2 != ""){
	$where .= " and bsah.cid LIKE '".substr($cid2,0,($depth+1)*3)."%' ";
	$join_where .= " and bsah.cid LIKE '".substr($cid2,0,($depth+1)*3)."%' ";
}

if($search_text != ""){
	$where .= "and ".$search_type." LIKE '%".trim($search_text)."%' ";
	$join_where .= "and ".$search_type." LIKE '%".trim($search_text)."%' ";
}

if($bs_site != ""){
	$where .= "and bsah.bs_site = '".trim($bs_site)."' ";
	$join_where .= "and bsah.bs_site = '".trim($bs_site)."' ";
}

$sql = "select count(*) as total from shop_buyingservice_autoupdate_history bsah  $where ";
//echo $sql;
$db->query($sql);

$db->fetch();
$total = $db->dt[total];


$sql = "select bsah.*
			from shop_buyingservice_autoupdate_history bsah 
			left join shop_category_info ci on bsah.cid = ci.cid   $join_where
			$where
			order by bsah.regdate desc limit $start, $max ";// , ci.cid, ci.cname , ci.depth 
//echo $sql;
$db->query("$sql "); //where uid = '$code'



$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr >
			<td align='left' colspan=4> ".GetTitleNavigation("자동신상품등록 정보", "상품관리 > 자동신상품등록 정보 ")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=4 style='padding-bottom:15px;'>
	    	<div class='tab'>
					<table class='s_org_tab'>
					<tr>
						<td class='tab'>
							<table id='tab_01' ".($bsmode=="new_goods_reg" || $bsmode=="" ? "class='on'":"")." >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?bsmode=new_goods_reg'\">구매대행 자동상품 등록</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_02' ".($bsmode=="log" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?bsmode=log'\">신상품 자동등록 내역</td>
								<th class='box_03'></th>
							</tr>
							</table>
							
						</td>
						<td class='btn'>

						</td>
					</tr>
					</table>
				</div>
	    </td>
	</tr>
	</table>";
if($bsmode == "new_goods_reg" || $bsmode==""){
	$Contents01 .= "
	  <form name='autoreg_form' method='get' action='bs_autoreg.act.php'  style='display:inline;'>
		<table cellpadding=0 cellspacing=0 border=0 width=100% align='center' class='input_table_box'>
				<col width='20%' />
				<col width='30%' />
				<col width='20%' />
				<col width='30%' />
				<tr bgcolor=#ffffff >
					<td class='input_box_title'> 구매대행 사이트  </td>
					<td class='input_box_item'>
						".getBuyingServiceSiteInfo($bs_site,"validation=true title='구매대행 사이트' ")."
						
						<span class=small></span>
						<div id='organization_img_area' ></div>
					</td>
				</tr>
		</table>
		<div align=center style='padding:10px;'>
		<img src='../images/".$admininfo["language"]."/btn_buyservice_goods_get.gif' class=vm title=\"상품정보 가져오기\" onclick='AutoReg();' style=\"cursor:pointer;\">
		</div>
	  </form>";
}else{
$Contents01 .= "
	  <form name='search_form' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' style='display:inline;'>
	<input type='hidden' name='mode' value='search'>
	<input type='hidden' name='cid2' value='$cid2'>
	<input type='hidden' name='depth' value='$depth'>
	<input type='hidden' name='bsmode' value='$bsmode'>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >
	<tr>
		<td colspan=2>
			
			<table cellpadding=0 cellspacing=0 border=0 width=100% align='center' class='input_table_box'>
				<col width='20%' />
				<col width='30%' />
				<col width='20%' />
				<col width='30%' />
				<tr>
					<td class='input_box_title'>  선택된 카테고리  </td>
					<td class='input_box_item' colspan=3 ><b id='select_category_path1'>".($search_text == "" ? getCategoryPathByAdmin($cid2, $depth)."(".$total."개)":"<b style='color:red'>'$search_text'</b> <!--로 검색된 결과 입니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'O')." ")."</b></div></td>
				</tr>
				<tr>
					<td class='input_box_title'>카테고리선택</td>
					<td class='input_box_item' colspan=3 >
						<table border=0 cellpadding=0 cellspacing=0>
							<tr>
								<td style='padding-right:5px;'>".getCategoryList3("대분류", "cid0_1", "onChange=\"loadCategory(this,'cid1_1',2)\" title='대분류' ", 0, $cid2)."</td>
								<td style='padding-right:5px;'>".getCategoryList3("중분류", "cid1_1", "onChange=\"loadCategory(this,'cid2_1',2)\" title='중분류'", 1, $cid2)."</td>
								<td style='padding-right:5px;'>".getCategoryList3("소분류", "cid2_1", "onChange=\"loadCategory(this,'cid3_1',2)\" title='소분류'", 2, $cid2)."</td>
								<td>".getCategoryList3("세분류", "cid3_1", "onChange=\"loadCategory(this,'cid2',2)\" title='세분류'", 3, $cid2)."</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr bgcolor=#ffffff >
				<td class='input_box_title'> 구매대행 사이트  </td>
				<td class='input_box_item'>
					".getBuyingServiceSiteInfo($bs_site)."
					
					<span class=small></span>
					<div id='organization_img_area' ></div>
				</td>
				<td class='input_box_title'>목록갯수</td>
					<td class='input_box_item'>
						<select name=max style=\"font-size:12px;height: 20px; width: 50px;\" align=absmiddle><!-- onchange=\"document.frames['act'].location.href='".$HTTP_URL."?cid=$cid&depth=$depth&view=innerview&max='+this.value\"-->
							<option value='5' ".CompareReturnValue(5,$max).">5</option>
							<option value='10' ".CompareReturnValue(10,$max).">10</option>
							<option value='20' ".CompareReturnValue(20,$max).">20</option>
							<option value='50' ".CompareReturnValue(50,$max).">50</option>
							<option value='100' ".CompareReturnValue(100,$max).">100</option>
						</select> <span class='small'><!--한페이지에 보여질 갯수를 선택해주세요.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'C')."</span>
					</td>
			  </tr>
			  <tr>
					<td class='input_box_title'>  검색어  </td>
					<td class='input_box_item' colspan=3>
						<table cellpadding=0 cellspacing=0 width=100%>
							<col width='130px'>
							<col width='*'>
							<tr>
								<td><select name='search_type'  style=\"font-size:12px;height:20px;\">
									<option value='orgin_category_info'>Orgin 카테고리 정보</option>
									<option value='bs_list_url'>상품리스트 URL</option>
									</select>
								</td>
								<td style='padding-left:5px;'>
								<INPUT id=search_texts  class='textbox' value='' style=' FONT-SIZE: 12px; WIDTH: 90%; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'><br>
								
								</td>
								<td colspan=2 style='padding-left:5px;'><span class='p11 ls1'></span></td>
							</tr>
						</table>
					</td>
					
							</tr>
				";
				
$Contents01 .=	"				
				
			</table>
		</td>
	</tr>
	<tr>
		<td colspan=2 align=center style='padding:10px 0px;'><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' border=0 align=absmiddle ><!--btn_inquiry.gif--></td>
	</tr>
	  </table>
	  </form>";
}
//$ContentsDesc01 = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$innerview = "
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left'  class='list_table_box'><!--style='table-layout:fixed;'-->
		<col width=5%>
		<col width=7% >
		<col width=30%>
		<col width=* >
		<col width=9%>
		<col width=7%>
		
		<col width=9%>
	  <tr bgcolor=#efefef align=center height=25>
			<td class='s_td'  nowrap>번호 </td>
			<td class='m_td' >구매대행사이트</td>
			<td class='m_td' >카테고리</td>
			<td class='m_td' >시작시간</td>
			<td class='m_td' >종료시간</td>
			<td class='m_td' align='center' >전체상품수</td>
			<td class='m_td' align='center' nowrap>등록 상품수</td>
			
			<td class='e_td' >관리 </td>
		</tr>";



if($db->total){
	for($i=0;$i  < $db->total; $i++){
		$db->fetch($i);
		$no = $total - ($page - 1) * $max - $i;

		$innerview .= "<tr align=center height=30>
				<td class='list_box_td list_bg_gray' >".$no." </td>
				<td class='list_box_td'>".$db->dt[bs_site]."</td>
				<td class='list_box_td point' style='text-align:left;'>".getCategoryPathByAdmin($db->dt[cid], 4)."</td>
				<td class='list_box_td' align=center>".$db->dt[sdate]."</td>
				<td class='list_box_td list_bg_gray' align=center>".$db->dt[edate]."</td>
				
				<td class='list_box_td'>".$db->dt[goods_update_cnt]."</td>
				<td class='list_box_td list_bg_gray'>".$db->dt[goods_update_complete_cnt]."</td>
				
				<td align=center>".$db->dt[regdate]."</td>
			</tr>";
	}
	$innerview .= "";
}else{
		$innerview .= "
			<tr height=60><td colspan=8 align=center>구매대행 자동신상품등록  정보가 없습니다.</td></tr>";

}


$innerview .= "</table>";
$Contents02 .= $innerview;
$Contents02 .= "<ul class='paging_area' >
						<li class='front'></li>
						<li class='back'>".page_bar($total, $page, $max,"&cid2=$cid2&depth=$depth&search_type=$search_type&search_text=$search_text&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD&bs_site=$bs_site","")."</li>
					  </ul>";



$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$Contents01."<br>";
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";

$Contents = $Contents."</table >";

$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >구매대행 사이트를 지정한 후 상품정보 가져오기 버튼을 클릭시 즐겨찾기에 등록된 목록을 기준으로 신상품 등록이 실행됩니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >신상품 등록의 경우 중복해서 호출시 시스템 부하의 원인이 될수 있으므로 중복해서 실행하시면 안됩니다.</td></tr>
</table>
";
//$help_text =  getTransDiscription(md5($_SERVER["PHP_SELF"]),'B');

$Contents .= HelpBox("자동신상품등록 정보", $help_text)."<br>";

 $Script = "
 <script language='javascript'>
 function AutoReg(){
	//alert(bs_site+':::'+list_url);
	if(CheckFormValue(document.autoreg_form)){
		//alert($('#bs_site').val());
		$.ajax({ 
			type: 'GET', 
			data: {'bs_act': 'new_goods_reg','bs_site': $('#bs_site').val()},
			url: 'bs_autoreg.act.php',  
			dataType: 'html', 
			async: true, 
			beforeSend: function(){ 
				$.blockUI.defaults.css = {}; 
				$.blockUI({ message: $('#loading'), css: { width: '100px' , height: '100px' ,padding:  '10px'} });  
			},  
			success: function(data){ 
				$.unblockUI();
				alert(data);
				
				//alert('카테고리 정보가 정상적으로 처리 되었습니다.');
				//alert($('#'+obj_id).parent().html());
				//alert($('#'+obj_id).clone().wrapAll('<div/>').parent().html());
			} 
		}); 

	}else{
		return false;
	}
}

 </script>
 <script Language='JavaScript' type='text/javascript'>
	function loadCategory(sel,target) {
		//alert(target);
		var trigger = sel.options[sel.selectedIndex].value;
		var form = sel.form.name;
		//var depth = sel.depth;//kbk
		var depth = sel.getAttribute('depth');
		//dynamic.src = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
		//document.getElementById('act').src = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
		window.frames['act'].location.href = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

	}
	function loadChangeCategory(sel,target) {
		//alert(target);
		var trigger = sel.options[sel.selectedIndex].value;
		var form = sel.form.name;
		//var depth = sel.depth;//kbk
		var depth = sel.getAttribute('depth');

		//dynamic.src = '../product/category.load3.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;//kbk
		//document.getElementById('act').src = '../product/category.load3.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
		window.frames['act'].location.href = '../product/category.load3.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

	}
	</script>
 ";

if($mmode == "pop"){

	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = product_menu();
	$P->NaviTitle = "자동신상품등록 정보";
	$P->Navigation = "구매대행 > 자동신상품등록 정보";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();

}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = product_menu();
	$P->title = "자동신상품등록 정보";
	$P->Navigation = "구매대행 > 자동신상품등록 정보";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}

$goods_update_soldout_cnt++;
/*
create table shop_buyingservice_autoupdate_history (
	bsah_ix int(4) unsigned not null auto_increment  ,
	cid varchar(15)  NOT NULL COMMENT '상품카테고리' ,
	cname varchar(100)  NOT NULL COMMENT '상품카테고리 텍스트' ,
	bs_site varchar(100) NOT NULL COMMENT '구매대행사이트코드',
	autoupdate_type varchar(20) NOT NULL COMMENT '자동신상품등록 타입 new_goods_reg , goods_update ',
	bs_list_url varchar(256) null default null COMMENT '구매대행 사이트 리스트 URL ' ,
	bs_list_url_md5 varchar(32) null default null COMMENT '구매대행 사이트 리스트 URL 키값' ,
	orgin_category_info varchar(255)  NULL COMMENT '구매대행사이트 카테고리 정보',
	sdate datetime not null COMMENT '작업 시작시간', 
	edate datetime not null COMMENT '작업 종료시간', 
	goods_update_cnt int(4) unsigned not null COMMENT '업데이트 전체 상품수'  ,
	goods_update_complete_cnt int(4) unsigned not null COMMENT '업데이트 완료상품수'  ,
	goods_update_soldout_cnt int(4) unsigned not null COMMENT '품절 상품수'  ,
	regdate datetime not null,
primary key(bsah_ix));

*/
?>