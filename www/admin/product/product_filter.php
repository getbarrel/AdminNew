<?php
/**
 * Created by PhpStorm.
 * User: moon
 * Date: 2019-08-09
 * Time: 오전 11:14
 */
include("../class/layout.class");
include("category.lib.php");

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 align='left'>
	  <tr >
		<td align='left' colspan=2> ".GetTitleNavigation("필터관리", "상품등록 > 필터관리 ")."</td>
	  </tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 align='left'>
	 <tr>
	    <td align='left'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle> <b>아이콘추가하기</b></div>")."</td>
	  </tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 align='left' class='input_table_box' style='margin-top:3px;'>
	<tr>
	    <td class='input_box_title' style='width:150px;'> <b>필터타입 <img src='".$required3_path."'></b> </td>
		<td class='input_box_item'>
			<label><input type='radio' name='filter_type' value='CLOTHING' ".CompareReturnValue("CLOTHING",$sch_filter_type,"checked")." checked> 의류 </label>
			<label><input type='radio' name='filter_type' value='SHOES' ".CompareReturnValue("SHOES",$sch_filter_type,"checked")."> 슈즈 </label>
			<label><input type='radio' name='filter_type' value='ACC' ".CompareReturnValue("ACC",$sch_filter_type,"checked")."> ACC </label>
			<label><input type='radio' name='filter_type' value='COLOR' ".CompareReturnValue("COLOR",$sch_filter_type,"checked")."> 색상 </label>
	     </td>
		 <td class='input_box_title'> <b>사용유무 <img src='".$required3_path."'></b> </td>
         <td class='input_box_item'>
            <input type=radio name='disp' value='Y' id='disp_1' ".CompareReturnValue("Y",$sch_disp,"checked")." checked><label for='disp_1'>사용</label>
            <input type=radio name='disp' value='N' id='disp_0' ".CompareReturnValue("N",$sch_disp,"checked")."><label for='disp_0'>미사용</label>
         </td>
	</tr>
	<tr>
	    <td class='input_box_title' style='width:150px;'> <b>필터코드 <img src='".$required3_path."'></b> </td>
		<td class='input_box_item'>
			<input type='text' class=textbox name='filter_code' style='width:200px' id='icon_name' value='".$sch_filter_code."' validation='true' title='필터코드'>
	    </td>
		<td class='input_box_title' style='width:150px;'> <b>필터명 <img src='".$required3_path."'></b> </td>
		<td class='input_box_item'>
			<input type='text' class=textbox name='filter_name' style='width:200px' id='icon_name' value='".$sch_filter_name."' validation='true' title='필터명'>
	    </td>
	</tr>
	<tr>
	    <td class='input_box_title' style='width:150px;'> <b>정렬순서 <img src='".$required3_path."'></b> </td>
		<td class='input_box_item' colspan='3'>
			<input type='text' class=textbox name='filter_sort' style='width:200px' id='icon_name' value='' validation='true' title='정렬순서'>
	    </td>
	</tr>
    <tr class='devFileUpArea' style='display: none;'>
        <td class='input_box_title'> <b>필터 이미지(PC) <img src='".$required3_path."'></b> </td>
        <td class='input_box_item'><input type=file class='textbox' name='filter_img_pc' value='' style='width:230px;' validation=false title='필터 이미지(PC)'> </td>
    </tr>
    <tr class='devFileUpArea' style='display: none;'>
        <td class='input_box_title'> <b>필터 이미지(Mobile) <img src='".$required3_path."'></b> </td>
        <td class='input_box_item'><input type=file class='textbox' name='filter_img_mobile' value='' style='width:230px;' validation=false title='필터 이미지(Mobile)'> </td>
    </tr>
	</table>";

$Contents02 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr>
	    <td align='left'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;' ><img src='../image/title_head.gif' align=absmiddle> <b>아이콘목록</b></div>")."</td>
	  </tr>
	</table>
	<div style='width:100%;height:350px;'>
	<table width='100%' cellpadding=0 cellspacing=0 align='left' class='list_table_box' style='margin-top:3px;'>
	  <tr height=27 bgcolor=#efefef align=center style='font-weight:bold'>
		<td class='s_td' style='width:5%; padding:0; text-align:center;' class='s_td'> 순번</td>
	    <td class='m_td' style='width:12%; padding:0; text-align:center;' class='s_td'> 필터타입</td>
	    <td class='m_td' style='width:12%; padding:0; text-align:center;' class='m_td'> 필터코드</td>
	    <td class='m_td' style='width:12%; padding:0; text-align:center;' class='m_td'> 필터명</td>
		<td class='m_td' style='width:5%; padding:0; text-align:center;' class='m_td'> 순서</td>
	    <td class='m_td' style='width:10%; padding:0; text-align:center;' class='m_td'> 사용여부</td>
	    <td class='m_td' style='width:15%; padding:0; text-align:center;' class='m_td'> 등록자/ID</td>
	    <td class='m_td' style='width:15%; padding:0; text-align:center;' class='m_td'> 등록일</td>
	    <td class='e_td' style='width:*; padding:0; text-align:center;' class='e_td'> 관리</td>
	  </tr>";
$max = 9; //페이지당 갯수

if ($page == '')
{
    $start = 0;
    $page  = 1;
}
else
{
    $start = ($page - 1) * $max;
}
$db = new Database;

$where = " where 1=1 ";

if($sch_mode == "S"){

	$where .= " and filter_type = '".$sch_filter_type."' and disp = '".$sch_disp."' ";
	
	if($sch_filter_code != ""){
		$where .= " and filter_code like '%".($sch_filter_code)."%'";
	}
	if($sch_filter_name != ""){
		$where .= " and filter_name like '%".($sch_filter_name)."%'";
	}
}

$db->query("SELECT * FROM shop_product_filter $where ");
$total = $db->total;
$str_page_bar = page_bar($total, $page,$max, "&max=$max&mmode=pop&sch_mode=S&sch_filter_type=$sch_filter_type&sch_disp=$sch_disp&sch_filter_code=$sch_filter_code&sch_filter_name=$sch_filter_name","");

if($total){
    $db->query("SELECT * FROM shop_product_filter $where order by regdate desc limit $start , $max ");
    for($i=0;$i < $db->total;$i++){
        $db->fetch($i);
        $no = $total - ($page - 1) * $max - $i;
        switch($db->dt['filter_type']){
            case 'CLOTHING':
                $filter_type_text = '의류';
                break;
            case 'SHOES':
                $filter_type_text = '슈즈';
                break;
            case 'ACC':
                $filter_type_text = 'ACC';
                break;
            case 'COLOR':
                $filter_type_text = '색상';
                break;
        }
        $Contents02 .= "
		  <tr bgcolor=#ffffff height=30 align=center>
			<td class='list_box_td list_bg_gray'>".$no."</td>
			<td class='list_box_td list_bg_gray'>".$filter_type_text."</td>
		    <td class='list_box_td list_bg_gray'>".$db->dt['filter_code']."</td>
		    <td class='list_box_td'>".$db->dt['filter_name']."</td>
			<td class='list_box_td'>".$db->dt['filter_sort']."</td>
		    <td class='list_box_td list_bg_gray'>".($db->dt['disp'] == "Y" ?  "사용":"미사용")."</td>
		    <td class='list_box_td'>".$db->dt['reg_name']."/".$db->dt['reg_id']."</td>
		    <td class='list_box_td'>".$db->dt['regdate']."</td>
		    
		    <td class='list_box_td list_bg_gray'>
			";

        if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
            $Contents02 .= "<a href=\"javascript:updateFilterInfo('".$db->dt['idx']."','".$db->dt['filter_type']."','".$db->dt['filter_code']."','".$db->dt['filter_name']."','".$db->dt['filter_img_pc']."','".$db->dt['filter_img_mobile']."','".$db->dt['disp']."','".$db->dt['filter_sort']."')\"><img src='../images/".$_SESSION["admininfo"]["language"]."/btc_modify.gif' border=0 align=absmiddle></a> ";
        }else{
            $Contents02 .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$_SESSION["admininfo"]["language"]."/btc_modify.gif' border=0 align=absmiddle ></a> ";
        }

        //$mString .= "<img  src='../image/btn_view_source.gif'  border=0 align=absmiddle>";
        if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
            $Contents02 .= "<a href=\"javascript:deleteFilterInfo('delete','".$db->dt['idx']."')\"><img src='../images/".$_SESSION["admininfo"]["language"]."/btc_del.gif' border=0 align=absmiddle></a> ";
        }else{
            $Contents02 .= "<a href=\"".$auth_delete_msg."\"><img src='../images/".$_SESSION["admininfo"]["language"]."/btc_del.gif' border=0 align=absmiddle ></a> ";
        }
        $Contents02 .= "


		    </td>
		  </tr>	  ";
    }
}else{
    $Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=9>등록된 필터정보가 없습니다. </td>
		  </tr>	  ";
}
$Contents02 .= "
	  <!--tr height=1><td colspan=5><img src='../image/emo_3_15.gif' align=absmiddle > <span class=small>사용을 원하시는 PG 모듈을 선택해 주세요</span></td></tr-->
	  </table>
	  <div style='width:100%;text-align:center;padding-top:10px;clear:both;'>".$str_page_bar."</div>
	  </div>";


if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
    $ButtonString = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	<tr bgcolor=#ffffff ><td colspan=4 align=center>
		<a href=\"./product_filter.php\">검색초기화</a>
		<a href=\"javascript:search()\"><img src='../images/".$_SESSION["admininfo"]["language"]."/bt_search.gif' border=0 align=absmiddle></a> 
		<input type='image' src='../images/".$_SESSION["admininfo"]["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' >
	</td></tr>
	</table>
	";
}else{
    $ButtonString = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	<tr bgcolor=#ffffff ><td colspan=4 align=center><a href=\"".$auth_update_msg."\"><img src='../images/".$_SESSION["admininfo"]["language"]."/b_save.gif' border=0 align=absmiddle ></a></td></tr>
	</table>
	";

}



$Contents = "<form name='filter_form' action='product_filter.act.php' method='post' onsubmit='return CheckFormValue(this)' target='act' enctype='multipart/form-data'>
<input name='act' type='hidden' value='insert'>
<input name='idx' type='hidden' value=''>";
$Contents = $Contents."<table width='100%' border=0>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$Contents01."<br>";
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."</table >";
$Contents = $Contents."</form>";
$Contents = $Contents."<table width='100%' border=0>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."</table >";
$Contents = $Contents."<form name='filter_search' action='product_filter.php' method='get'>
<input name='sch_mode' type='hidden' value='S'>
<input name='sch_filter_type' type='hidden' value=''>
<input name='sch_disp' type='hidden' value=''>
<input name='sch_filter_code' type='hidden' value=''>
<input name='sch_filter_name' type='hidden' value=''>
"; 
$Contents = $Contents."</table >";

$Script = "
 <script language='javascript'>
 $(document).ready(function(){
    $('input[name=filter_type]').click(function(){
       var filter_type = $(this).val();
       if(filter_type == 'COLOR'){
           $('.devFileUpArea').show();
           $('input:file[name=filter_img_pc]').attr('validation',true);
           $('input:file[name=filter_img_mobile]').attr('validation',true);
       }else{
           $('.devFileUpArea').hide();
           $('input:file[name=filter_img_pc]').attr('validation',false);
           $('input:file[name=filter_img_mobile]').attr('validation',false);
       }
    });
 });
 function updateFilterInfo(idx,filter_type,filter_code,filter_name,filter_img_pc,filter_img_mobile,disp,filter_sort){
 	var frm = document.filter_form;

 	frm.act.value = 'update';
 	frm.idx.value = idx;
 	frm.filter_code.value = filter_code;
 	frm.filter_name.value = filter_name;
	frm.filter_sort.value = filter_sort;
 	//frm.filter_img_pc.value = filter_img_pc;
 	//frm.filter_img_mobile.value = filter_img_mobile;

	$('input[name=filter_type][value='+filter_type+']').attr('checked',true);
	
	if(filter_type == 'COLOR'){
        $('.devFileUpArea').show();
        $('input:file[name=filter_img_pc]').attr('validation',true);
        $('input:file[name=filter_img_mobile]').attr('validation',true);
	}else{
        $('.devFileUpArea').hide();
        $('input:file[name=filter_img_pc]').attr('validation',false);
        $('input:file[name=filter_img_mobile]').attr('validation',false);
	}

 	if(disp == 'Y'){
 		frm.disp[0].checked = true;
 	}else{
 		frm.disp[1].checked = true;
 	}

}

function deleteFilterInfo(act, idx){
 	if(confirm('해당 필터 정보를 정말로 삭제하시겠습니까?')){
 		var frm = document.filter_form;
 		frm.act.value = act;
 		frm.idx.value = idx;
 		frm.submit();
 	}
}

function search(){
	var frm  = document.filter_form;
	var sfrm = document.filter_search;

	sfrm.sch_filter_type.value	= frm.filter_type.value;
	sfrm.sch_disp.value			= frm.disp.value;
	sfrm.sch_filter_code.value	= frm.filter_code.value;
	sfrm.sch_filter_name.value	= frm.filter_name.value;

	sfrm.submit();
}

 </script>
 ";


$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "상품관리 > 상품등록 > 필터관리";
$P->NaviTitle = "필터관리";
$P->strLeftMenu = product_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();
