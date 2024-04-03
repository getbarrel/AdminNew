<?php
/**
 * Created by PhpStorm.
 * User: moon
 * Date: 2017-07-27
 * Time: 오후 8:04
 */
include $_SERVER['DOCUMENT_ROOT']."/admin/class/layout.class";

$db = new database();


if($_GET['ho_ix']){
    $act = "update";
    $sql = "select * from bienview_hospital_info where ho_ix = '".$_GET['ho_ix']."' ";
    $db->query($sql);
    $db->fetch();
    $ho_ix = $db->dt['ho_ix'];
    $info_type = $db->dt['info_type'];
    $com_name = $db->dt['com_name'];
    $com_zip = $db->dt['com_zip'];
    $com_addr1 = $db->dt['com_addr1'];
    $com_addr2 = $db->dt['com_addr2'];
    $homepage = $db->dt['homepage'];
    $phone = $db->dt['phone'];
    $x_code = $db->dt['x_code'];
    $y_code = $db->dt['y_code'];
    $latitude = $db->dt['latitude'];
    $longitude = $db->dt['longitude'];
    $disp = $db->dt['disp'];
    $editdate = $db->dt['editdate'];
    $regdate = $db->dt['regdate'];

}else{
    $act = "insert";
}


$Contents .= "
	<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
			<tr >
				<td align='left' colspan=2> ".GetTitleNavigation("병원 & 약국 등록/수정", "병원 & 약국 관리 > 병원 & 약국 등록/수정", false)."</td>
			</tr>
	</table>
";


$Contents .= "
<form name='hosptal_frm' method='POST' action='hospital_input.act.php' target='act'>
<input type='hidden' name='act' value='".$act."' />
<input type='hidden' name='ho_ix' value='".$ho_ix."' />
    <TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
        <TR>
            <td align=center colspan=2 valign=top>		
                <table border='0' width='100%' cellspacing='1' cellpadding='0'>
                    <tr>
                        <td >
                            <table border='0' width='100%' cellspacing='0' cellpadding='0' class='member_table input_table_box' style='text-align:left;'>
                                <col width=30%>
                                <col width=*>
                                <tr>
                                    <td class='input_box_title' nowrap> 분류</td>
                                    <td class='input_box_item'>
                                        <select name='info_type'>
                                            <option value='H' ".CompareReturnValue('H',$info_type,'selected').">병원</option>
                                            <option value='F'".CompareReturnValue('F',$info_type,'selected').">약국</option>
                                        </select>       
                                    </td>
                                </tr>
                                <tr >
                                    <td class='input_box_title' nowrap> 업체명</td>
                                    <td class='input_box_item'>
                                        <input type='text' class='textbox' name='com_name' value='".$com_name."'/>
                                    </td>
                                </tr>
                                <tr>
                                    <td class='input_box_title' nowrap> 주소</td>
                                    <td class='input_box_item'>
                                        <div id='input_address_area' style='padding:5px 0px 5px 0px;' >
                                            <table border='0' cellpadding='0' cellspacing='0' style='table-layout:fixed;width:100%'>
                                                <col width='80px'>
                                                <col width='*'>
                                                <tr>
                                                    <td height=26>
                                                        <input type=text name='com_zip' id='zipcode1' value='".$com_zip."'  maxlength='7' style='width:60px' class='textbox' validation='true' title='우편코드' readonly>
                                                    </td>
                                                    <td style='padding:1px 0 0 5px;'>
                                                        <img src='../images/".$admininfo["language"]."/btn_search_address.gif' onclick=\"zipcode('1')\" style='cursor:pointer;'>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan=2 height=26>
                                                        <input type=text name='com_addr1'  id='addr1' value='".$com_addr1."' size=50 class='textbox'  style='width:300px' readonly>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan=2 height=26>
                                                        <input type=text name='com_addr2'  id='addr2'  value='".$com_addr2."' size=70 class='textbox'  style='width:300px' > (상세주소)
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </td>									
                                </tr>
                                <tr>
                                    <td class='input_box_title' nowrap> 홈페이지 주소</td>
                                    <td class='input_box_item'>
                                        <input type='text' class='textbox' name='homepage' value='".$homepage."' />
                                    </td>									
                                </tr>
                                <tr>
                                    <td class='input_box_title' nowrap> 전화번호</td>
                                    <td class='input_box_item'>
                                        <input type='text' class='textbox' name='phone' value='".$phone."' />
                                    </td>									
                                </tr>
                                <tr>
                                    <td class='input_box_title' nowrap> 좌표</td>
                                    <td class='input_box_item'>
                                        <div>
                                            <table>
                                                <col width='50%' />
                                                <col width='*' />
                                                <tr> 
                                                    <td align='right'> x: <input type='text' class='textbox' name='x_code' value='".$x_code."'/> </td>
                                                    <td align='right'> y: <input type='text' class='textbox' name='y_code' value='".$y_code."'/> </td>
                                                </tr>
                                                <tr> 
                                                    <td align='right'> 위도: <input type='text' class='textbox' name='latitude' value='".$latitude."'/> </td>
                                                    <td align='right'> 경도: <input type='text' class='textbox' name='longitude' value='".$longitude."'/> </td>
                                                </tr>
                                            </table>
                                        </div>									
                                    </td>									
                                </tr>
                                <tr>
                                    <td class='input_box_title' nowrap> 전시상태</td>
                                    <td class='input_box_item'>
                                        <input type='radio' name='disp' value='1' id='disp_1' ".CompareReturnValue('1',$disp,'checked')." checked/><label for='disp_1'>전시</label>
                                        <input type='radio' name='disp' value='0' id='disp_0' ".CompareReturnValue('0',$disp,'checked')."/><label for='disp_0'>미 전시</label>
                                    </td>									
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td> 
                <div style='text-align:center; padding-top:5px;'> 
                    <input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' align=absmiddle>
                </div>            
            </td>
        </tr>
    </TABLE>
</form>
";

$Script="
<script>
function zipcode(type){
	var zip = window.open('../member/zipcode.php?zip_type='+type+'&obj_id=input_address_area','','width=440,height=300,scrollbars=yes,status=no');
}
</script>
";

$P = new ManagePopLayOut();
$P->addScript = "".$Script;
$P->Navigation = "병원&약국 관리 > 병원&약국 등록/수정";
$P->NaviTitle = "병원&약국 등록/수정";
$P->title = "병원&약국 등록/수정";
$P->strContents = $Contents;
echo $P->PrintLayOut();