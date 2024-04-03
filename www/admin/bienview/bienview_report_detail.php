<?php
/**
 * Created by PhpStorm.
 * User: moon
 * Date: 2017-07-27
 * Time: 오후 8:04
 */
include $_SERVER['DOCUMENT_ROOT']."/admin/class/layout.class";

$db = new database();

if(empty($_GET['re_ix'])){
    echo "<script>alert('상세정보가 존재하지 않습니다.');self.close()</script>";
    exit;
}

$sql = "select 
          re_ix,report_type,report_div,report_title,report_contents,homepage,address,editdate,regdate,
          AES_DECRYPT(UNHEX(phone),'".$db->ase_encrypt_key."') as phone,
          AES_DECRYPT(UNHEX(email),'".$db->ase_encrypt_key."') as email
        from 
            bienview_report_info 
        where 
            re_ix = '".$_GET['re_ix']."' ";

$db->query($sql);

if(!$db->total){
    echo "<script>alert('상세정보가 존재하지 않습니다.');self.close()</script>";
    exit;
}
$report = $db->fetch();

switch ($report['report_div']){
    case 'A':
        $report_div_text = "전체";
        break;
    case 'T':
        $report_div_text = "단체";
        break;
    case 'S':
        $report_div_text = "개인";
        break;
    case 'H':
        $report_div_text = "병원";
        break;
    case 'F':
        $report_div_text = "약국";
        break;
    default:
        $report_div_text = "";
        break;
}

$Contents .= "
	<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
			<tr >
				<td align='left' colspan=2> ".GetTitleNavigation("제보 상세보기", "제보관리 > 제보 상세보기", false)."</td>
			</tr>
	</table>
";


$Contents .= "
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<TR>
		<td align=center colspan=2 valign=top>
		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
			<tr >
				<td align='left' colspan=2> ".GetTitleNavigation("회원정보 수정", "회원관리 > 회원정보 수정", false)."</td>
			</tr>
			<tr>
				<td align=center style='padding: 0 0px 0 0px;height:569px;vertical-align:top'>				
				<table border='0' width='100%' cellspacing='1' cellpadding='0'>
					<tr>
						<td >
							<table border='0' width='100%' cellspacing='0' cellpadding='0' class='member_table input_table_box' style='text-align:left;'>
								<col width=30%>
								<col width=*>
                    			<tr>
									<td class='input_box_title' nowrap> 분류</td>
									<td class='input_box_item'>".$report_div_text."</td>
								</tr>
								<tr >
									<td class='input_box_title' nowrap> 단체명</td>
									<td class='input_box_item'>".$report['report_title']."</td>
								</tr>
								<tr>
									<td class='input_box_title' nowrap> 설명</td>
									<td class='input_box_item'>".$report['report_contents']."</td>									
								</tr>
								<tr>
									<td class='input_box_title' nowrap> 홈페이지 주소</td>
									<td class='input_box_item'>http://".$report['homepage']."</td>									
								</tr>
								<tr>
									<td class='input_box_title' nowrap> 주소</td>
									<td class='input_box_item'>".$report['address']."</td>									
								</tr>
								<tr>
									<td class='input_box_title' nowrap> 전화번호</td>
									<td class='input_box_item'>".$report['phone']."</td>									
								</tr>
								<tr>
									<td class='input_box_title' nowrap> 메일주소</td>
									<td class='input_box_item'>".$report['email']."</td>									
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		</table>
	</td>
</tr>
</TABLE>";


$P = new ManagePopLayOut();
$P->addScript = "".$Script;
$P->Navigation = "제보관리 > 제보 상세보기";
$P->NaviTitle = "제보 상세보기";
$P->title = "제보 상세보기";
$P->strContents = $Contents;
echo $P->PrintLayOut();