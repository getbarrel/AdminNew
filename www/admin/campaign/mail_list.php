<?
include("../class/layout.class");

$db = new Database;
$mdb = new Database;

$Script = "<script language='javascript'>
function mailDelete(mail_ix){
	if(confirm('해당 메일를 정말로 삭제하시겠습니까? 삭제하시면 관련된 모든 이미지가 삭제됩니다.'))
	{
		window.frames['act'].location.href= 'mail.act.php?act=delete&mail_ix='+mail_ix;
	}
}
</script>";


$mstring ="<form name=poll action='board.manage.act.php'><input type=hidden name=act value=insert>
		<table cellpadding=5 cellspacing=0 width=100% border=0 align=center>
		<tr>
			<td align='left' colspan=6 > ".GetTitleNavigation("메일목록", "메일링/SMS > 메일목록 ")."</td>
		</tr>
		<tr>
			<td>
			".PrintMailList()."
			</td>
		</tr>
		</form>";
$mstring .="</table>";


$Contents = $mstring;

/*
$help_text = "
	<table cellpadding=1 cellspacing=0 class='small' >
		<col width=8>
		<col width=*>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >메일링 하기위한 이메일을 미리 등록 관리 하실 수 있습니다. </td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >또한 메일 발송시 저장버튼을 클릭하면 메일 목록에 저장됩니다.</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >저장된 메일들은 메일발송시 선택하여 발송 하실수 있습니다.</td></tr>
	</table>
	";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');


$help_text = HelpBox("메일목록", $help_text);
$Contents = $Contents.$help_text;



$P = new LayOut();
$P->addScript = $Script;
$P->Navigation = "메일링/SMS > 메일목록";
$P->title = "메일목록";
$P->strLeftMenu = campaign_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();

function PrintMailList(){
	global $db, $mdb, $admininfo, $start, $max ,$page,$auth_write_msg,$auth_delete_msg;

	$sql = "select count(*) as total from shop_mail_box ";
	$mdb->query($sql);
	$mdb->fetch();
	$total = $mdb->dt[total];

	$max = 10;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}



	$mString = "<table cellpadding=4 cellspacing=0 border=0 width=100% class='list_table_box' >";
	$mString = $mString."<tr align=center bgcolor=#efefef height=25>
									<td class=s_td width='36%'>메일 제목</td>
									<td class=m_td width='10%'>표시</td>
									<td class=m_td width='8%'>발송건수</td>
									<td class=m_td width='8%'>오픈건수</td>
									<td class=m_td width='8%'>click 건수</td>
									<td class=m_td width='10%'>등록일</td>
									<td class=e_td width='10%'>관리</td>
									</tr>";
	if ($total == 0){
		$mString .= "<tr bgcolor=#ffffff height=150><td colspan=7 style='border-bottom:1px solid silver;' align=center>메일 내역이 존재 하지 않습니다.</td></tr>";
		$mString .= "<tr bgcolor=#ffffff height=60><td colspan=7 align=right><a href='mail_write.php'><img src='../images/".$admininfo["language"]."/btn_mailadd.gif' border=0 ></a></td></tr>";
	}else{

		//$mdb->query("select * from shop_mail_box  order by  regdate desc limit $start , $max");
		if($mdb->dbms_type == "oracle"){
			$sql = "select m.mail_ix, m.mail_title, m.regdate, m.disp, count(mh.mail_ix) as send_cnt,
				sum(case when mail_open = '1' then 1 else 0 end) as mail_open, sum(case when mail_click = '1' then 1 else 0 end) as mail_click
				from shop_mail_box m
				left join shop_mailling_history mh on m.mail_ix = mh.mail_ix
				group by m.mail_ix, m.mail_title, m.regdate, m.disp order by m.regdate desc limit $start, $max ";
		}else{
			$sql = "select m.mail_ix, m.mail_title, m.regdate, m.disp, count(mh.mail_ix) as send_cnt,
				sum(case when mail_open = '1' then 1 else 0 end) as mail_open, sum(case when mail_click = '1' then 1 else 0 end) as mail_click
				from shop_mail_box m
				left join shop_mailling_history mh on m.mail_ix = mh.mail_ix
				group by mail_ix order by m.regdate desc limit $start, $max ";
		}
		$mdb->query($sql);
		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);

			$mString = $mString."<tr height=30 bgcolor=#ffffff align=center>
			<td class='list_box_td point' style='text-align:left;padding-left:10px;'><a href='mail_write.php?mail_ix=".$mdb->dt[mail_ix]."'>".$mdb->dt[mail_title]."</a></td>
			<td class='list_box_td list_bg_gray'>".($mdb->dt[disp] == "1" ? "표시":"표시하지 않음")."</td>
			<td class='list_box_td'><a href='mail_sended_history.php?mail_ix=".$mdb->dt[mail_ix]."'>".$mdb->dt[send_cnt]."</a></td>
			<td class='list_box_td list_bg_gray'><a href='mail_sended_history.php?mail_ix=".$mdb->dt[mail_ix]."&mail_result=o'>".$mdb->dt[mail_open]."</a></td>
			<td class='list_box_td'><a href='mail_sended_history.php?mail_ix=".$mdb->dt[mail_ix]."&mail_result=c'>".$mdb->dt[mail_click]."</a></td>
			<td class='list_box_td list_bg_gray'>".str_replace("-",".",substr($mdb->dt[regdate],0,10))."</td>
			<td  align=center>";
            if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
                $mString.="
				<a href=\"JavaScript:mailDelete('".$mdb->dt[mail_ix]."')\"><img  src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
            }else{
                $mString.="
				<a href=\"".$auth_delete_msg."\"><img  src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
            }
            $mString.="
			</td>
			</tr>
			";
		}
		$mString .= "</table>";
		$mString .= "<table cellpadding=0 cellspacing=0 border=0 width=100% style='padding:10px 0px;'>";
		$mString .= "<tr>
					<td colspan=0 align=left>".page_bar($total, $page, $max,  "&max=$max","")."</td>
					<td colspan=0 align=right>";
                    if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
                        $mString.="
                        <a href='mail_write.php'><img src='../images/".$admininfo["language"]."/btn_mailadd.gif' border=0 ></a>";
                    }else{
                        $mString.="
                        <a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_mailadd.gif' border=0 ></a>";
                    }
                    $mString.="
                    </td>
				</tr>";
	}


	$mString .= "</table>";

	return $mString;
}


?>
