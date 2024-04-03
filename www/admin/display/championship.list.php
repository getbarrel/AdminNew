<?
include("../class/layout.class");

if(empty($type)){
    $type = 'A';
}

$db = new Database;
$mdb = new Database;

$Script = "<script language='javascript'>
    $(function() {
        
        $('#all_fix').on('click', function() {
            if(!$(this).is(':checked')) {
                $('.code').each(function(){
                   $(this).prop('checked', false);
                });    
            }else {
                $('.code').each(function(){
                   $(this).prop('checked', true);
                });    
            }
        });
        
        $('.delete_member').on('click', function() {
            if(confirm('정말 삭제하시겠습니까?')) {
                deleteAjax($(this), '');
            }
        });
        
        $('.delete_all').on('click', function() {
            
            var checked = '';
            $('.code').each(function() {
                if($(this).is(':checked')) {
                   checked += $(this).val()+',';
                } 
            });
            
            checked = checked.slice(0,-1);
            
            if(checked == '') {
                alert('체크 된 값이 없습니다.');
                return;
            }
            
            if(confirm('정말 삭제하시겠습니까?')) {                
                $('.code').each(function() {
                    if($(this).is(':checked')) {
                       deleteAjax($(this), 'all');
                    } 
                });
                alert('삭제에 성공하였습니다.');
                location.reload();
            }
        });
        
        function deleteAjax(obj, type) {
            $.ajax({
                    url : '/admin/display/championship.act.php',
                    type : 'post',
                    cache : false,
                    dataType : 'json',
                    data : {
                            'act' : 'delete_member',
                            'id' : obj.attr('data-id'),
                            'type' : obj.attr('data-type')
                    },
                    success : function(data) {
                        if(data) {
                            if(type != 'all') {
                                alert('삭제에 성공하였습니다.');
                                location.reload();
                            }
                        }
                    },
                    error : function(err) {
                        alert('삭제에 실패하였습니다.');
                    }    
                });
        }
    });

</script>";

$mstring = "<meta http-equiv='Cache-Controll' content='no-cache'>";
$mstring .="<form name=serchform >
		<table cellpadding=5 cellspacing=0 width=100% border=0 align=center style=''>
		<tr>
			<td align='left' colspan=6 > ".GetTitleNavigation("챔피언십관리", "프로모션/전시 > 챔피언십 관리 > 스프린트 챔피언십 참가신청 ")."</td>
		</tr>";


$mstring .= "
		<tr>
			<td>
				<table cellpadding=0 cellspacing=1 border=0 width=100% align='center' class='search_table_box'>
					<col width=10%>
					<col width=25%>
					<col width=10%>
					<col width=25%>";
$mstring .= "
                    <tr>
                        <td class='search_box_title' >참가구분</td>
						<td class='search_box_item' colspan=3>
						    <input type='radio' id='aType' name='type' value='A' ".($type == 'A'?'checked':'')."/><label for='aType'>전체</label>
						    <input type='radio' id='iType' name='type' value='I' ".($type == 'I'?'checked':'')."/><label for='iType'>개인</label>
						    <input type='radio' id='gType' name='type' value='G' ".($type == 'G'?'checked':'')."/><label for='gType'>단체</label>
                        </td>
                    </tr>
                    <tr>
                        <td class='search_box_title' >
							<label for='search_date'><b>기간</b></label><input type='checkbox' name='search_date' id='search_date' value='1' onclick='ChangeRegistDate(document.search_banner);' ".(($search_date==1)?'checked':'').">
						  </td>
						  <td class='search_box_item'  colspan=3>
						  ".search_date('use_sdate','use_edate',$use_sdate,$use_edate,'N','D')."
						  </td>
                    </tr>
					<tr>
						<td class='search_box_title' > 조건검색</td>
						<td class='search_box_item' colspan=3>
						    <select name='search_type'>
						        <option value='' selected>선택해주세요</option>
						        <option value='name' ".($search_type == 'name' ? 'selected' : '').">이름/단체명</option>
						        <option value='email' ".($search_type == 'email' ? 'selected' : '').">이메일</option>
						        <option value='handphone' ".($search_type == 'handphone' ? 'selected' : '').">핸드폰</option>
                            </select>
						    <input type='text' class='textbox' style='width: 210px; ' name='search_text' value='".($search_text)."'  title='분류명'> 
						
						</td>					
					</tr>
				</table>
			</td>
		</tr>
		<tr>
				<td align ='center'><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' border=0 align=absmiddle></td>
		";
$mstring .= "
		<tr>
			<td>
			".PrintPromotionGoods($type)."
			</td>
		</tr>
		</form>";
$mstring .="</table>";


$Contents = $mstring;
$Contents .= "<div style='height:120px;'></div>";

$P = new LayOut();
$P->addScript = $Script;
$P->Navigation = "프로모션 전시관리 > 챔피언십관리 > 스프린트 챔피언십 참가목록";
$P->title = "스프린트 챔피언십 참가목록";
$P->strLeftMenu = display_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();



function PrintPromotionGoods(){
    global $db, $mdb, $admin_config, $div_ix ,$admininfo;
    global $page, $type, $search_date, $search_text, $search_type, $use_sdate, $use_edate, $start;

    $max = 10;

    if ($page == ''){
        $start = 0;
        $page  = 1;
    }else{
        $start = ($page - 1) * $max;
    }

    $where = '';

    if($type == 'I') {
        $where .= 'and cm.gp_ix = 1';
    }else if($type == 'G') {
        $where .= 'and cm.gp_ix != 1';
    }

    if($search_date == 1) {
        $where .= ' and cm.regdate between "'.$use_sdate.' 00:00:00" and "'.$use_edate.' 23:59:59"';
    }

    if(!empty($search_text)) {

        if($search_type == 'name') {
            $where .= ' and (AES_DECRYPT(UNHEX(cm.name),"' . $db->ase_encrypt_key . '") like "%'.$search_text.'%" or cg.group_name like "%'.$search_text.'%")';
        }else if($search_type == 'email'){
            $where .= ' and (AES_DECRYPT(UNHEX(cm.email),"' . $db->ase_encrypt_key . '") like "%'.$search_text.'%" or AES_DECRYPT(UNHEX(cg.email), "' . $db->ase_encrypt_key . '") like "%'.$search_text.'%")';
        }else if($search_type == 'handphone') {

            $search_text = str_replace('-', '', $search_text);

            $where .= ' and (REPLACE(AES_DECRYPT(UNHEX(cm.handphone),"' . $db->ase_encrypt_key . '"), "-", "") like "%'.$search_text.'%" or REPLACE(AES_DECRYPT(UNHEX(cg.handphone),"' . $db->ase_encrypt_key . '"), "-", "") like "%'.$search_text.'%")';
        }
    }

    $sql = "select un.* 
            from(
                select cm.cm_ix, cm.name, cm.gp_ix, cm.email, cm.image_url, cm.regdate, cg.group_name, cg.group_master, cg.email as group_email, cg.group_master_image_url, cg.regdate as group_regdate
                from championship_member cm 
                left join championship_group cg 
                on (cm.gp_ix = cg.gp_ix) 
                where cm.gp_ix = 1 $where union all select cm.cm_ix, cm.name, cm.gp_ix, cm.email, cm.image_url, cm.regdate, cg.group_name, cg.group_master, cg.email as group_email, cg.group_master_image_url, cg.regdate as group_regdate
                from championship_member cm 
                left join championship_group cg 
                on (cm.gp_ix = cg.gp_ix) 
                where cm.gp_ix != 1 $where group by cm.gp_ix
            ) un 
            order by un.cm_ix ";
    $db->query($sql);
    $total = $db->total;

    $sql = "select count(*) as cnt from championship_member";
    $mdb->query($sql);
    $mtotal = $mdb->fetch();

    $sql = "select * from championship_set";
    $mdb->query($sql);
    $mdb->fetch();
    $recruitment = $mdb->dt['max'];

    $mString = "<table width=100%>
                    <tr>
                        <td><b>( 목록 : 총 $total )  현재 신청자 수 : ".($mtotal['cnt'])." / $recruitment</b></td>
                        <td align='right'><a href='championship.act.php?act=excel_down&type=I'><button type='button'>개인 참가신청서 엑셀 다운로드</button></a></td>
                    </tr>
                </table>";
    $mString .= "<table cellpadding=4 cellspacing=0 border=0 width=100% class='list_table_box' >
                    <colgroup>
                        <col width=4%>
                        <col width=4%>
                        <col width='7%'>
                        <col width='10%'>						
                        <col width='7%'>						
                        <col width='15%'>
                        <col width='7%'>
                        <col width='15%'>
                        <col width='14%'>
                    </colgroup>";

    $mString .= "<tr height=30 align=center>
                    <td align='center' class=s_td><input type=checkbox name='all_fix' id='all_fix'></td>
                    <td align='center' class='m_td'><font color='#000000'><b>번호</b></font></td>
                    <td class=m_td >참가구분</td>
                    <td class=m_td >이름/단체명</td>
                    <td class=m_td >참가신청자 수</td>
                    <td class=m_td >개인/감독 이메일주소</td>
                    <td class=m_td >첨부</td>
                    <td class=m_td >등록일</td>
                    <td class=e_td >관리</td>
                </tr>";

    $sql = "select un.* 
            from(
                select cm.cm_ix, 
                    AES_DECRYPT(UNHEX(cm.name),'".$db->ase_encrypt_key."') as name, 
                    AES_DECRYPT(UNHEX(cm.email),'".$db->ase_encrypt_key."') as email, 
                    cm.gp_ix, cm.image_url, cm.regdate, cg.group_name, cg.group_master, 
                    AES_DECRYPT(UNHEX(cg.email),'".$db->ase_encrypt_key."') as group_email, 
                    
                    cg.group_master_image_url, cg.regdate as group_regdate
                    from championship_member cm 
                    left join championship_group cg 
                    on (cm.gp_ix = cg.gp_ix) 
                    where cm.gp_ix = 1 $where 
                union all 
                select cm.cm_ix, 
                    AES_DECRYPT(UNHEX(cm.name),'".$db->ase_encrypt_key."') as name, 
                    AES_DECRYPT(UNHEX(cm.email),'".$db->ase_encrypt_key."') as email, 
                    cm.gp_ix, cm.image_url, cm.regdate, cg.group_name, cg.group_master,                    
                    AES_DECRYPT(UNHEX(cg.email),'".$db->ase_encrypt_key."') as group_email, 
                    
                    cg.group_master_image_url, cg.regdate as group_regdate
                from championship_member cm 
                left join championship_group cg 
                on (cm.gp_ix = cg.gp_ix) 
                where cm.gp_ix != 1 $where group by cm.gp_ix
            ) un 
            order by un.cm_ix desc limit $start, $max";
    $db->query($sql);
    $members = $db->fetchall();


    if ($total == 0){
        $mString .= "<tr bgcolor=#ffffff><td height=70 colspan=11 align=center>챔피언십 등록자가 없습니다.</td></tr>";
    }else{

        for($i=0;$i < count($members); $i++){
            $member = $members[$i];
            $no = $total - ($page - 1) * $max - $i;

            //기본 개인세팅
            $cm_ix = $member['cm_ix'];
            $gp_ix = $member['gp_ix'];
            $name = $member['name'];
            $email = $member['email'];
            $regdate = $member['regdate'];
            $img_url = $member['image_url'];
            $gtype = '개인';

            $member_cnt = 1;

            //그룹일때
            if($gp_ix != 1) {
                $sql = "select count(*) as total from championship_member where gp_ix = '".$gp_ix."' ";
                $mdb->query($sql);
                $mdb->fetch();
                $member_cnt = $mdb->dt['total'];

                $name = $member['group_name'];
                $email = $member['group_email'];
                $regdate = $member['group_regdate'];
                $img_url = $member['group_master_image_url'];
                $gtype = '그룹';
            }

            $mString .= "<tr height=27>
			<td class='list_box_td'><input type=checkbox name=code[] class='code' data-id='".($gp_ix == 1 ? $cm_ix : $gp_ix)."' data-type='".($gp_ix == 1 ? 'I' : 'G')."' value='".$cm_ix."'></td>
			<td class='list_box_td'>".$no."</td>
			<td class='list_box_td list_bg_gray' style='line-height:150%;'>" .$gtype. "</td>
            <td class='list_box_td' style='padding:5px;' nowrap>".$name."</td>
			<td class='list_box_td' >".$member_cnt."</td>
			<td class='list_box_td' >".$email."</td>
			<td class='list_box_td' >".$img_url."</td>";

            $mString .= "
			<td class='list_box_td list_bg_gray'>".$regdate."</td>
			<td class='list_box_td'>";
            if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
                if($gp_ix != 1){
                    $mString .= "<a href='championship.act.php?act=excel_down&gp_ix=$gp_ix&type=G'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0 style='cursor:pointer' /></a>";
                }
                $mString .= "<a href=\"/admin/display/championship.detail.php?gp_ix=$gp_ix&cm_ix=$cm_ix\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a> ";
            }

            if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
                $mString .= "<img class='delete_member' data-id='".($gp_ix == 1 ? $cm_ix : $gp_ix)."' data-type='".($gp_ix == 1 ? 'I' : 'G')."'  src='../images/".$admininfo["language"]."/btc_del.gif' border=0>";
            }
            $mString .= "
			</td>
			</tr>
			";
        }

    }

    $mString .= "</table>";
    $mString .= "<table cellpadding=0 cellspacing=0 border=0 width=100% >
				<tr height=50 bgcolor=#ffffff>
				    <td><button class='delete_all'>선택삭제</button></td>
					<td colspan=3 align=center>".page_bar($total, $page, $max,  "&max=$max&type=$type&use_sdate=$use_sdate&use_edate=$use_edate&search_type=$search_type&search_text=$search_text","")."</td>";
    $mString .= "</tr>";
    $mString .= "</table>";

    return $mString;
}


?>
