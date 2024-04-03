<?php
/**
 * Created by PhpStorm.
 * User: moon
 * Date: 2019-08-28
 * Time: 오후 2:10
 */
include("../class/layout.class");

if(empty($_GET['gp_ix'])){
    echo "<script>alert('회원그룹 정보가 누락되었습니다.'); self.close();</script>";
    exit;
}

$sql = "select * from shop_groupinfo where gp_ix = '".$_GET['gp_ix']."' ";
$db->query($sql);
if($db->total == 0){
    echo "<script>alert('존재하지 않는 회원 그룹입니다.'); self.close();</script>";
    exit;
}
$db->fetch();
$gp_name = $db->dt['gp_name'];
$langType = GetDisplayDivision($db->dt[mall_ix], "text");

$sql = "select * from shop_group_benefits where gp_ix = '".$_GET['gp_ix']."' and benefit_type = 'M' ";
$db->query($sql);
$db->fetch();
$benefit_mileage = $db->dt['benefit_value'];

$sql = "select * from shop_group_benefits where gp_ix = '".$_GET['gp_ix']."' and benefit_type = 'C' ";
$db->query($sql);
$benefits_coupon = $db->fetchall();

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 align='left'>
	  <tr >
		<td align='left' colspan=2> ".GetTitleNavigation("회원등급별 혜택관리", "회원그룹/레벨설정 ")."</td>
	  </tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 align='left'>
	 <tr>
	    <td align='left'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle> <b>(".$langType.") ".$gp_name." 등급</b></div>")."</td>
	  </tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 align='left' class='input_table_box' style='margin-top:3px;'>
	<tr>
	    <td class='input_box_title' style='width:150px;'> <b>적립금 </b> </td>
		<td class='input_box_item'>
		    <input type='text' class='textbox' name='mileage' value='".$benefit_mileage."' size='4' /> P
	    </td>
	</tr>
	<tr>
	    <td class='input_box_title' style='width:150px;'> <b>쿠폰 </b> </td>
		<td class='input_box_item'>
            <table width='500px' cellpadding=0 cellspacing=3 border='0'  >
                <col width=25%>
                <col width=*>
                <tr>
                    <td>
                        <table border='0' cellpadding=3 width='100%' cellspacing=0 id='delivery_policy_3_terms'>
                        <col width='23%' />
                        <col width='*' />";

                        if(count($benefits_coupon) > 0){

                            for($i=0;$i<count($benefits_coupon);$i++){
                                $Contents01 .= "
                                <tr bgcolor='#ffffff' id='add_table_price'>
                                    <td>
                                        <input type='hidden' name='option_length' id='option_length' value='".$i."'>
                                        ".CouponPublishSelectBox($benefits_coupon[$i][benefit_value],"publish_ix[]")."
                                    </td>
                                    <td>
                                        <input type='button' id='delivery_price_add' value='추가' title='추가' style='cursor:pointer;' onclick=\"AddCopyRow('delivery_policy_3_terms','member_publish_ix','4')\">
                                        <input type='button' id='delivery_price_del' value='삭제' title='삭제' style='cursor:pointer;' >
                                    </td>
                                </tr>";

                                }
                            }else{

                                $Contents01 .= "
                                <tr bgcolor='#ffffff' id='add_table_price'>
                                    <td>
                                        <input type='hidden' name='option_length' id='option_length' value='0'>
                                        ".CouponPublishSelectBox('',"publish_ix[]")."
                                    </td>
                                    <td>
                                        <input type='button' id='delivery_price_add' value='추가' title='추가' style='cursor:pointer;' onclick=\"AddCopyRow('delivery_policy_3_terms','member_publish_ix','4')\">
                                        <input type='button' id='delivery_price_del' value='삭제' title='삭제' style='cursor:pointer;' >
                                    </td>
                                </tr>";
                            }

                        $Contents01 .= "
                        </table>
                    </td>
                </tr>
            </table>
        </td>
	</tr>
	</table>";

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
    $ButtonString = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$_SESSION["admininfo"]["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
	</table>
	";
}else{
    $ButtonString = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	<tr bgcolor=#ffffff ><td colspan=4 align=center><a href=\"".$auth_update_msg."\"><img src='../images/".$_SESSION["admininfo"]["language"]."/b_save.gif' border=0 align=absmiddle ></a></td></tr>
	</table>
	";

}

$Contents = "<form name='filter_form' action='group_benefits.act.php' method='post' onsubmit='return CheckFormValue(this)' target='act' enctype='multipart/form-data'>
<input name='act' type='hidden' value='update'>
<input name='gp_ix' type='hidden' value='".$_GET['gp_ix']."'>";
$Contents = $Contents."<table width='100%' border=0>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$Contents01."<br>";
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."</table >";
$Contents = $Contents."</form>";


$Script ="
<script type='text/javascript'>
    function AddCopyRow(target_id, option_var_name, seq){
    
        var table_target_obj = $('table[id='+target_id+']');
        var option_obj = $('#'+target_id);
        
        /*배열값이 중복땜에 제일마지막 tr의 value 값을 가져온다 2014-02-07 이학봉 */
        var option_length = 0;
        table_target_obj.find('tr:last').each(function(){
             option_length = $(this).find('#option_length').val();
        });
        rows_total = parseInt(option_length) + 1;
        /*배열값이 중복땜에 제일마지막 tr의 value 값을 가져온다 2014-02-07 이학봉 */
    
        var newRow = option_obj.find('tr:first').clone(true).wrapAll('<table/>').appendTo('#'+option_obj.attr('id'));  //
    
        newRow.find('input[id=option_length]').val(rows_total);
        newRow.find('select[id=member_publish_ix]').attr('name',option_var_name+'['+rows_total+'][publish_ix]');
    
    }
    
    $(document).ready(function(){		
		$('#delivery_price_del').live('click',function() {
			if($('#delivery_policy_3_terms tr').size() > 1) $(this).parents('#add_table_price').remove();
		});
	});
</script>
";

$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "회원관리 > 회원그룹/레벨설정 > 회원등급별 혜택관리";
$P->NaviTitle = "회원등급별 혜택관리";
$P->strLeftMenu = member_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();