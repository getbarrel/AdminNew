<?
include("../class/layout.class");
$db = new Database;
$db2 = new Database;

if($mall_ix){
    $where = " and gi.mall_ix = '".$mall_ix."' ";
}

$sql = "select * from shop_groupinfo gi where 1 $where";

$db->query($sql);
$db->fetch();
$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
		<col width='15%' />
		<col width='30%' />
		<col width='*' />
	  <tr>
		<td align='left' colspan=3> ".GetTitleNavigation("회원그룹관리", "회원관리 > 회원그룹관리 ")."</td>
	  </tr>
	</table>";

$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr >
		<td align='left' colspan=2 style='padding-bottom:0px;'> ".GetTitleNavigation("회원그룹/레벨설정", "회원관리 > 회원그룹/레벨자동설정 ")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=2 style='padding:3px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b>회원그룹/레벨자동설정</b></div>")."</td>
	  </tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='search_table_box'>
	<col width='18%'>
	<col width='*'>
	    <!--
	   <tr bgcolor=#ffffff height='30'>
	    <td class='search_box_title'> <b>자동설정사용여부 <img src='".$required3_path."'></b></td>
	    <td class='search_box_item'>
	    	<input type=radio name='all_disp' id='disp_1' value='1' ".($db->dt[all_disp]=="1" || $db->dt[all_disp]=="" ? 'checked' : '' )."><label for='disp_1'>사용</label>
	    	<input type=radio name='all_disp' id='disp_0' value='0' ".($db->dt[all_disp]=="0" ? 'checked' : '' )."><label for='disp_0'>미사용</label>
	    </td>
	  </tr>
	  -->
	  <tr bgcolor=#ffffff height='30'>
	    <td class='search_box_title'> <b>회원그룹 갱신일 <img src='".$required3_path."'></b> </td>
	    <td class='search_box_item'>
	        <select name='gp_type' validation='true' title='회원그룹 갱신일'>
	            <option value='' ".($db->dt[gp_type]=="" ? 'selected' : '' ).">선택해주세요</option>
	            <!--<option value='1' ".($db->dt[gp_type]=="1" ? 'selected' : '' ).">매월 1일</option>-->
	            <option value='5' ".($db->dt[gp_type]=="5" ? 'selected' : '' ).">매월 5일</option>
	            <!--<option value='2' ".($db->dt[gp_type]=="2" ? 'selected' : '' ).">매일</option>-->
	            <!--<option value='3' ".($db->dt[gp_type]=="3" ? 'selected' : '' ).">매주 월요일</option>-->
	            <!--<option value='4' ".($db->dt[gp_type]=="4" ? 'selected' : '' ).">매년 1월 1일</option>-->
            </select>
	    </td>
	  </tr>
	  </table>";
/*
$ContentsDesc01 = "
<table cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td><img src='../image/emo_3_15.gif' align=absmiddle ></td>
	<td align=left style='padding:10px;' class=small>
		  <u>회원그룹</u>으로 이용하실 그룹를 입력해주세요
	</td>
</tr>
</table>
";
*/

$langTypeArr = GetDisplayDivision('','array');
$langTab = "";
if(is_array($langTypeArr) && count($langTypeArr) > 0){
    foreach($langTypeArr as $key=>$val){
        $langTab .="
        <table id='tab_'$key ".($mall_ix == $val['mall_ix'] ? "class='on' ":"").">
            <tr>
                <th class='box_01'></th>
                <td class='box_02' ><a href='?mall_ix=".$val['mall_ix']."'>".$val['mall_templete_type']."</a></td>                                    
                <th class='box_03'></th>
            </tr>
        </table>
        ";
    }
}

$ContentsDesc01 = getTransDiscription(md5($_SERVER["PHP_SELF"]),'B');

$Contents02 =colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b>그룹목록</b></div>");
$Contents02 .="
<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<col width='15%' />
	<col width='35%' />
	<col width='15%' />
	<col width='35%' />
    <tr>
        <td align='left' colspan=4 style='padding-bottom:20px;'>
            <div class='tab'>
                <table class='s_org_tab'>
                <col width='550px'>
                <col width='*'>
                    <tr>
                        <td class='tab'>
                            <table id='tab_0' ".(($mall_ix == "") ? "class='on' ":"").">
                                <tr>
                                    <th class='box_01'></th>
                                    <td class='box_02'><a href='?mall_ix='>전체</a></td>
                                    <th class='box_03'></th>
                                </tr>
                            </table>                           
                            ".$langTab."
                        </td>
                    <td style='text-align:right;vertical-align:bottom;padding:0 0 10px 0'>
                    
                    </td>
                    </tr>
                </table>
            </div>
        </td>
    </tr>
</table>
";
$Contents02 .= "
<form name='group_frm' action='group.act.php' method='post' enctype='multipart/form-data' onsubmit='return CheckFormValue(this)'>
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box' style='margin-top:3px;'>
	  <tr bgcolor=#efefef style='font-weight:bold' align='center' height=30>
	    <td class='s_td' width='30' height=25>번호</td>";
if($_SESSION["admin_config"]["front_multiview"] == "Y"){
    $Contents02 .= "
			<td class='m_td' width='40'>국내<br>해외</td>";
}
$Contents02 .= "
		<td class='m_td' width='70'>그룹등급</td>
	    <td class='m_td' width='120'>그룹명</td>
	    <td class='m_td' width='150'>주문금액</td>
	    <td class='e_td' width='50'>사용여부</td>
	    <td class='e_td' width='100'>혜택정보</td>
	    <td class='e_td' width='50'>혜택</td>
	  </tr>";
$db = new Database;
$cdb = new Database;

//오라클은 group by 할때 컬럼을 명시해줘야함
//$db->query("SELECT gi.*,COUNT(md.gp_ix) AS cnt FROM ".TBL_SHOP_GROUPINFO." gi LEFT JOIN ".TBL_COMMON_MEMBER_DETAIL." md ON gi.gp_ix=md.gp_ix GROUP BY gi.gp_ix order by gi.gp_level asc ");
if($db->dbms_type == "oracle"){
	$db->query("SELECT distinct gi.gp_ix, gi.gp_name, gi.organization_img, gi.sale_rate, gi.gp_level,period,keep_period,order_price,keep_order_cnt,give_coupon, gi.disp, gi.basic, gi.regdate FROM ".TBL_SHOP_GROUPINFO." gi LEFT JOIN ".TBL_COMMON_MEMBER_DETAIL." md ON gi.gp_ix=md.gp_ix order by gi.gp_level asc");
}else{
    $qq = "SELECT gi.gp_ix, gi.ed_order_price,gi.st_reserve,gi.ed_reserve, gi.gp_name, gi.organization_img, gi.sale_rate, gi.gp_level,period,keep_period,order_price,keep_order_cnt,give_coupon, gi.disp, gi.basic, gi.regdate, COUNT(md.gp_ix) AS cnt,
                  gi.mall_ix,gi.all_disp
            FROM shop_groupinfo gi
            LEFT JOIN common_member_detail md ON gi.gp_ix=md.gp_ix
            WHERE (gi.gp_ix != 8 OR gi.gp_name != '직원회원')
            $where
            GROUP BY gi.gp_ix
            ORDER BY gi.mall_ix ASC, gi.gp_level ASC";
	$db->query($qq);
}
if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);

	if($cdb->dbms_type == "oracle"){
		$cdb->query("SELECT COUNT(md.gp_ix) AS cnt  FROM  ".TBL_COMMON_MEMBER_DETAIL." md where md.gp_ix = '".$db->dt[gp_ix]."'");
		$cdb->fetch(0);
		$cnt = $cdb->dt[cnt];
	}else{
		$cnt = $db->dt[cnt];
	}


    $benefit_info = "";
    $sql = "select * from shop_group_benefits where gp_ix = '".$db->dt['gp_ix']."' and benefit_type = 'M'";
    $db2->query($sql);
    $benefit_mileage = 0;
    if($db2->total){
        $db2->fetch();
        $benefit_mileage = $db2->dt['benefit_value'];

        $benefit_info .="M: ".$benefit_mileage." ";
    }

    $sql = "select count(*) cnt from shop_group_benefits where gp_ix = '".$db->dt['gp_ix']."' and benefit_type = 'C'";
    $db2->query($sql);
    $db2->fetch();
    $benefit_coupon = 0;
    if($db2->dt['cnt'] > 0){
        $benefit_coupon = $db2->dt['cnt'];
        $benefit_info .="C: ".$benefit_coupon."개 ";
    }

    if($db->dt['mall_ix'] == '20bd04dac38084b2bafdd6d78cd596b2'){
        $unitF = '$';
        $unitB = '';
    } else {
        $unitF = '';
        $unitB = '원';
    }


	$Contents02 .= "
		  <tr bgcolor=#ffffff align='center' height=30>
			<input type='hidden' name='data[".$db->dt[gp_ix]."][gp_ix]' value='".$db->dt[gp_ix]."'>
			<input name='act' type='hidden' value='setup_update'>
		    <td class='list_box_td'>".($i+1)."</td>";
        if($_SESSION["admin_config"]["front_multiview"] == "Y"){
            $Contents02 .= "
			<td class='list_box_td list_bg_gray'>".GetDisplayDivision($db->dt[mall_ix], "text")." </td>";
        }
        $Contents02 .= "
			<td class='list_box_td'>".$db->dt[gp_level]." </td>
		    <td class='list_box_td point'>".$db->dt[gp_name]."</td>
			<td class='list_box_td'>
			$unitF<input type='text' class='textbox number' name='data[".$db->dt[gp_ix]."][order_price]' id='st_order_price' value='".$db->dt[order_price]."' style='width:70px;'>$unitB ~ 
			$unitF<input type='text' class='textbox number' name='data[".$db->dt[gp_ix]."][ed_order_price]' id='ed_order_price' value='".$db->dt[ed_order_price]."' style='width:70px;'>$unitB
			</td>
		    <td class='list_box_td'>".($db->dt[all_disp] == "1" ?  "사용":"미사용")."</td>
		    <td class='list_box_td'>".$benefit_info."</td>
		    <td class='list_box_td'><input type='button' value='혜택등록' onclick=\"groupBenefits('".$db->dt['gp_ix']."')\" ></td>
			
			  </tr>";

	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td class='list_box_td' align=center colspan=8>등록된 그룹이 없습니다. </td>
		  </tr>";
}
$Contents02 .= "
	  <!--tr height=1><td colspan=8 ><img src='../image/emo_3_15.gif' align=absmiddle > <span class=small>사용을 원하시는 PG 모듈을 선택해 주세요</span></td></tr-->
	  </table>";
$Contents02 .= "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr height=20><td></td></tr>
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ><!--img src='../images/".$admininfo["language"]."/b_save.gif' border='0'  style='cursor:pointer;border:0px;' onClick='CheckFormValue(document.group_frm)' /--> </td></tr>
</table>
";
$Contents02 .= "
</form>";

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ><!--img src='../images/".$admininfo["language"]."/b_save.gif' border='0'  style='cursor:pointer;border:0px;' onClick='CheckFormValue(document.group_frm)' /--> </td></tr>
</table>
";
}else{
$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff >
    <td colspan=4 align=center>
        <a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></a>
    </td>
</tr>
</table>
";
}

$Contents = "<table width='100%' border=0 cellpadding='0' cellspacing='0'>";
$Contents = $Contents."<form name='group_frm' action='group.act.php' method='post' enctype='multipart/form-data' onsubmit='return CheckFormValue(this)'>
<input name='act' type='hidden' value='all_update'>
<input name='gp_ix' type='hidden' value=''>
<input name='basic' type='hidden' value=''>";
$Contents = $Contents."<tr><td width='100%'>";
$Contents = $Contents.$Contents01;
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."</form>";
$Contents = $Contents."<tr height=30><td></td></tr>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";

$Contents = $Contents."</table >";
/*
$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td><img src='/admin/image/icon_list.gif' align='absmiddle'></td><td class='small' >회원 그룹정보는 위와 같이 9단계의 등급으로 이루어져 있으며 '그룹등급' 은 수정이 불가능하며 각 등급에 맞는 명칭을 변경해서 사용하셔야 합니다</td></tr>
	<tr><td><img src='/admin/image/icon_list.gif' align='absmiddle'></td><td class='small' >사용하지 않으실 회원그룹정보는 수정버튼을 클릭하신후 사용하지 않음으로 선택하신후 저장 버튼을 누르시면 됩니다</td></tr>
	<tr><td><img src='/admin/image/icon_list.gif' align='absmiddle'></td><td class='small' >사용유무가 사용으로 되어 있는 회원그룹만 사용하실수 있게 됩니다</td></tr>
</table>
";
*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');


$Contents .= HelpBox("회원그룹관리", $help_text,'100');

 $Script = "
 <script language='javascript'>
function updateGroupInfo(gp_ix,sale_rate,gp_name,organization_name, organization_id,organization_img, gp_level,mem_cnt,sale_rate,memberreg_baymoney,use_mall_yn, disp, basic,period,keep_period,order_price,keep_order_cnt,give_coupon){
 	var frm = document.group_frm;

 	frm.act.value = 'update';
 	frm.gp_ix.value = gp_ix;
 	frm.sale_rate.value = sale_rate;
 	frm.gp_name.value = gp_name;
 	frm.basic.value = basic;
 	frm.gp_level.value = gp_level;
	//frm.period.value = period;
 	//frm.keep_period.value = keep_period;
 	frm.order_price.value = order_price;
 	frm.keep_order_cnt.value = keep_order_cnt;
 	frm.give_coupon.value = give_coupon;


/*
 	if(organization_img != ''){
 		document.getElementById('organization_img_area').innerHTML =\"<img src='".$admin_config[mall_data_root]."/images/member_group/\"+organization_img+\"' width='109'>\";
 	}else{
 		document.getElementById('organization_img_area').innerHTML =\"\";
 	}
*/
//	alert(document.getElementById('organization_img_area').innerHTML);

	//alert(frm.period.length);

 	for(i=0;i < frm.period.length;i++){
 		if(frm.period[i].value == period){
 			frm.period[i].checked = true;
 		}
 	}

	for(i=0;i < frm.keep_period.length;i++){
 		if(frm.keep_period[i].value == keep_period){
 			frm.keep_period[i].checked = true;
 		}
 	}

    if(mem_cnt > 0){
        frm.disp[0].disabled = true;
        frm.disp[1].disabled = true;

        if(disp == '1'){
     		frm.disp[0].checked = true;
     	}else{
     		frm.disp[1].checked = true;
     	}
    }else{
        frm.disp[0].disabled = false;
        frm.disp[1].disabled = false;

     	if(disp == '1'){
     		frm.disp[0].checked = true;
     	}else{
     		frm.disp[1].checked = true;
     	}
    }
}

 function deleteGroupInfo(act, gp_ix){
 	if(confirm(language_data['group.php']['A'][language])){
		//'해당그룹 정보를 정말로 삭제하시겠습니까?'
 		var frm = document.group_frm;
 		frm.act.value = act;
 		frm.gp_ix.value = gp_ix;
 		frm.submit();
 	}
}

function groupBenefits(gp_ix){
     PoPWindow('./group_benefits.php?gp_ix='+gp_ix,960,700,'group_benefits');    
}

$(document).ready(function(){
	$('#gp_st_date').datepicker({
		dateFormat: 'yy-mm-dd',
		buttonImageOnly: true,
		buttonText: 'Kalender',
	});

});
 </script>
 ";

$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = member_menu();
$P->Navigation = "회원관리 > 회원그룹/레벨설정";
$P->title = "회원그룹관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();

/*

create table ".TBL_SHOP_GROUPINFO." (
gp_ix int(4) unsigned not null auto_increment  ,
gp_name varchar(20) null default null,
gp_level int(2)  default '9' ,
sale_rate varchar(20) null default null,

disp char(1) default '1' ,
regdate datetime not null,
primary key(gp_ix));
*/
?>