<?php

include("../class/layout.class");

$db = new Database;
$mdb = new Database;

$championship_path = '/data/barrel_data/championship';

$Script = "<script language='javascript'>

$(function(){
    $('#btn_submit').on('click', function(){
        $('#cfrom').submit(); 
    });
    
    //mail select box
    $('.select_mail').on('change', function() {
        $(this).siblings('input[name=email2]').val($(this).val());
    });
    
    //멤버 삭제
    $('.delete_member').on('click', function() {
        if(confirm('실제 데이터가 삭제됩니다. 해당 선수를 정말 삭제 하시겠습니까?')){
            $.ajax({
            url : '/admin/display/championship.act.php',
            type : 'post',
            cache : false,
            dataType : 'json',
            data : {
                    'act' : 'delete_group_member',
                    'cm_ix' : $(this).data('id')
            },
            success : function(data) {
                if(data) {
                    alert('선수 삭제 성공하였습니다.');
                    location.reload();
                }
            },
            error : function(err) {
                alert('선수 삭제 실패하였습니다.');
            }    
            });
        } 
    });
    
    //파일삭제 Ajax
    $('.delete_file').on('click', function() {
        $.ajax({
            url : '/admin/display/championship.act.php',
            type : 'post',
            cache : false,
            dataType : 'json',
            data : {
                    'act' : 'delete_file',
                    'cm_ix' : $(this).attr('data-id')
            },
            success : function(data) {
                if(data) {
                    alert('파일삭제 성공하였습니다.');
                    location.reload();
                }
            },
            error : function(err) {
                alert('파일삭제에 실패하였습니다.');
            }    
        });
    });
    
    //지도자 파일삭제 Ajax
    $('#master_delete_file').on('click', function() {
        $.ajax({
            url : '/admin/display/championship.act.php',
            type : 'post',
            cache : false,
            dataType : 'json',
            data : {
                    'act' : 'delete_file',
                    'type' : 'master',
                    'gp_ix' : $('input[name=gp_ix]').val()
            },
            success : function(data) {
                if(data) {
                    alert('파일삭제 성공하였습니다.');
                    location.reload();
                }
            },
            error : function(err) {
                alert('파일삭제에 실패하였습니다.');
            }    
        });
    });
});

</script>
";
$gtype = "개인";

$sql = "select cm.cm_ix, cm.sex, cm.attend_div,cm.gp_ix,cm.postnum, cm.size,cm.class_name,cm.attend_group,cm.attend_event1,cm.attend_event2,
                cm.image_url,cm.image_url_path,cm.regdate,cm.editdate,
                AES_DECRYPT(UNHEX(cm.name),'".$db->ase_encrypt_key."') as name,
                AES_DECRYPT(UNHEX(cm.birthday),'".$db->ase_encrypt_key."') as birthday,
                AES_DECRYPT(UNHEX(cm.handphone),'".$db->ase_encrypt_key."') as handphone,
                AES_DECRYPT(UNHEX(cm.email),'".$db->ase_encrypt_key."') as email,
                AES_DECRYPT(UNHEX(cm.address1),'".$db->ase_encrypt_key."') as address1,
                AES_DECRYPT(UNHEX(cm.address2),'".$db->ase_encrypt_key."') as address2,
                AES_DECRYPT(UNHEX(cm.depositor),'".$db->ase_encrypt_key."') as depositor,
                cg.group_name,
                AES_DECRYPT(UNHEX(cg.group_master),'".$db->ase_encrypt_key."') as group_master,
                AES_DECRYPT(UNHEX(cg.handphone),'".$db->ase_encrypt_key."') as group_handphone,
                AES_DECRYPT(UNHEX(cg.email),'".$db->ase_encrypt_key."') as group_email,
                cg.group_master_image_url,cg.regdate as group_regdate, cg.postnum as group_postnum,
                AES_DECRYPT(UNHEX(cg.address1),'".$db->ase_encrypt_key."') as group_address1,
                AES_DECRYPT(UNHEX(cg.address2),'".$db->ase_encrypt_key."') as group_address2,
                 cg.member_cnt, cg.attend_event, cg.group_master_image_url_path,
                  AES_DECRYPT(UNHEX(cg.depositor),'".$db->ase_encrypt_key."') as master_depositor
        from championship_member cm 
        left join championship_group cg 
        on (cm.gp_ix = cg.gp_ix) 
        where cm_ix = $cm_ix";

$db->query($sql);
$member = $db->fetch();

$handphone = explode('-', $member['handphone']);
$email = explode('@', $member['email']);

if($gp_ix != 1) {
    //그룹
    $gtype = "그룹";

    $sql = "select cm.cm_ix, cm.sex, cm.attend_div,cm.gp_ix,cm.postnum, cm.size,cm.class_name,cm.attend_group,cm.attend_event1,cm.attend_event2,
                cm.image_url,cm.image_url_path,cm.regdate,cm.editdate,
                AES_DECRYPT(UNHEX(cm.name),'".$db->ase_encrypt_key."') as name,
                AES_DECRYPT(UNHEX(cm.birthday),'".$db->ase_encrypt_key."') as birthday,
                AES_DECRYPT(UNHEX(cm.handphone),'".$db->ase_encrypt_key."') as handphone,
                AES_DECRYPT(UNHEX(cm.email),'".$db->ase_encrypt_key."') as email,
                AES_DECRYPT(UNHEX(cm.address1),'".$db->ase_encrypt_key."') as address1,
                AES_DECRYPT(UNHEX(cm.address2),'".$db->ase_encrypt_key."') as address2,
                AES_DECRYPT(UNHEX(cm.depositor),'".$db->ase_encrypt_key."') as depositor,
                cg.group_name,
                AES_DECRYPT(UNHEX(cg.group_master),'".$db->ase_encrypt_key."') as group_master,
                AES_DECRYPT(UNHEX(cg.handphone),'".$db->ase_encrypt_key."') as group_handphone,
                AES_DECRYPT(UNHEX(cg.email),'".$db->ase_encrypt_key."') as group_email,
                cg.group_master_image_url,cg.regdate as group_regdate, cg.postnum as group_postnum,
                AES_DECRYPT(UNHEX(cg.address1),'".$db->ase_encrypt_key."') as group_address1,
                AES_DECRYPT(UNHEX(cg.address2),'".$db->ase_encrypt_key."') as group_address2,
                 cg.member_cnt, cg.attend_event, cg.group_master_image_url_path,
                  AES_DECRYPT(UNHEX(cg.depositor),'".$db->ase_encrypt_key."') as master_depositor
        from championship_member cm 
        left join championship_group cg 
        on (cm.gp_ix = cg.gp_ix) 
        where cg.gp_ix = $gp_ix";

    $db->query($sql);
    $member = $db->fetch();

    //그룹 참가종목
    $sql = "select co_ix, option_value from championship_options where option_key = 'attend' order by option_order;";
    $db->query($sql);
    $attend = $db->fetchall();

    //그룹소속원
    $sql = "select 
              cm.cm_ix, cm.sex, cm.attend_div,cm.gp_ix,cm.postnum, cm.size,cm.class_name,cm.attend_group,cm.attend_event1,cm.attend_event2,
                cm.image_url,cm.image_url_path,cm.regdate,cm.editdate,
                AES_DECRYPT(UNHEX(cm.name),'".$db->ase_encrypt_key."') as name,
                AES_DECRYPT(UNHEX(cm.birthday),'".$db->ase_encrypt_key."') as birthday,
                AES_DECRYPT(UNHEX(cm.handphone),'".$db->ase_encrypt_key."') as handphone,
                AES_DECRYPT(UNHEX(cm.email),'".$db->ase_encrypt_key."') as email,
                AES_DECRYPT(UNHEX(cm.address1),'".$db->ase_encrypt_key."') as address1,
                AES_DECRYPT(UNHEX(cm.address2),'".$db->ase_encrypt_key."') as address2,
                AES_DECRYPT(UNHEX(cm.depositor),'".$db->ase_encrypt_key."') as depositor 
            from championship_member cm where cm.gp_ix = $gp_ix order by cm.cm_ix";
    $db->query($sql);
    $group_member = $db->fetchall();

    //그룹의 정보로 대체
    $handphone = explode('-', $member['group_handphone']);
    $email = explode('@', $member['group_email']);
}

//참가그룹
$sql = "select co_ix, option_value from championship_options where option_key = 'group' order by option_order;";
$db->query($sql);
$group = $db->fetchall();

//참가종목
$sql = "select co_ix, option_value from championship_options where option_key = 'event' order by option_order;";
$db->query($sql);
$event = $db->fetchall();

$Contents = "<form id='cfrom' action='championship.act.php' enctype='multipart/form-data' method='post'>
                <input type='hidden' name='act' value='update'/>
                <input type='hidden' name='gp_ix' value='".$gp_ix."'/>
                <input type='hidden' name='cm_ix' value='".$cm_ix."'/>
                <table> 
                    <tr><td colspan='4' style='padding-bottom:10px;font-size:15px;'><b>".($gp_ix == 1?'참가자 기본 정보':'팀 기본 정보')."</b></td></tr>
                </table>
            <table width='100%' cellpadding=0 cellspacing=0 border='0' class='search_table_box'>";
$Contents .= "<colgroup>
                <col width='10%'>
                <col width='35%'>
                </colgroup>";
$Contents .= "<tbody>";
$Contents .= "<tr><td class='input_box_title'>소속명</td><td class='input_box_item'>$gtype</td></tr>";
if($gp_ix != 1) {
    $Contents .= "<tr><td class='input_box_title'>단체명</td><td class='input_box_item'><input type='text' name='group_name' value='".($member['group_name'])."' /></td></tr>";
}
$Contents .= "<tr><td class='input_box_title'>".($gp_ix == 1?'이름(실명)':'감독자(대표자)')."</td><td class='input_box_item'><input type='text' name='name' value='".($gp_ix == 1 ? $member['name'] : $member['group_master'])."' /></td></tr>";
if($gp_ix == 1){
    $Contents .= "<tr>
                    <td class='input_box_title'>성별</td>
                    <td class='input_box_item'>
                        <input type='radio' id='male' name='sex' value='M' ".($member['sex']=='M' ? 'checked' : '')."/><label for='male'>남자</label>
                        <input type='radio' id='female' name='sex' value='F' ".($member['sex']=='F' ? 'checked' : '')."/><label for='female'>여자</label>
                    </td>
                  </tr>";
    $Contents .= "<tr><td class='input_box_title'>생년월일</td><td class='input_box_item'><input type='text' name='birthday' value='".($member['birthday'])."' /></td></tr>";
}

$Contents .= "<tr>
                <td class='input_box_title'>".($gp_ix == 1?'핸드폰번호':'감독자 핸드폰번호')."</td>
                <td class='input_box_item'>
                    <input type='text' name='handphone[]' value='".($handphone[0])."' /> -
                    <input type='text' name='handphone[]' value='".($handphone[1])."' /> -
                    <input type='text' name='handphone[]' value='".($handphone[2])."' />
                </td>
              </tr>";
$Contents .= "<tr>
                <td class='input_box_title'>".($gp_ix == 1?'이메일 주소':'감독자 이메일 주소')."</td>
                <td class='input_box_item'>
                    <input type='text' name='email1' value='".($email[0])."' /> @
                    <input type='text' name='email2' value='".($email[1])."' />
                    <select class='select_mail' name='select_email'>
                        <option value='naver.com' ".($email[1]=='naver.com'?'selected':'').">naver.com</option>
                        <option value='gmail.com' ".($email[1]=='gmail.com'?'selected':'').">gmail.com</option>
                        <option value='hotmail.com' ".($email[1]=='hotmail.com'?'selected':'').">hotmail.com</option>
                        <option value='hanmail.net' ".($email[1]=='hanmail.net'?'selected':'').">hanmail.net</option>
                        <option value='daum.net' ".($email[1]=='daum.net'?'selected':'').">daum.net</option>
                        <option value='nate.com' ".($email[1]=='nate.com'?'selected':'').">nate.com</option>
                        <option value='direct'>직접입력</option>
                    </select>
                </td>
              </tr>";
if($gp_ix == 1) {
    $Contents .= "<tr>
                <td class='input_box_title'>참가기념티셔츠<br/>사이즈</td>
                <td class='input_box_item'>
                    <input type='radio' id='sSize' name='size' value='S' " . ($member['size'] == 'S' ? 'checked' : '') . "/><label for='sSize'>S</label>
                    <input type='radio' id='mSize' name='size' value='M' " . ($member['size'] == 'M' ? 'checked' : '') . "/><label for='mSize'>M</label>
                    <input type='radio' id='lSize' name='size' value='L' " . ($member['size'] == 'L' ? 'checked' : '') . "/><label for='lSize'>L</label>
                    <input type='radio' id='xlSize' name='size' value='XL' " . ($member['size'] == 'XL' ? 'checked' : '') . "/><label for='xlSize'>XL</label>
                    <input type='radio' id='xxlSize' name='size' value='XXL' " . ($member['size'] == 'XXL' ? 'checked' : '') . "/><label for='xxlSize'>XXL</label>
                </td>
              </tr>";
}
$Contents .= "<tr>
                <td class='input_box_title'>사전 참가기념품<br/>수령 주소</td>
                <td class='input_box_item' id='zip_td'>
                    <input type='text' name='postnum' id='zipcode1' value='".($gp_ix == 1 ? $member['postnum'] : $member['group_postnum'])."' readonly/> <img src='../images/".$admininfo["language"]."/btn_search_address.gif' align=absmiddle style='cursor:pointer;' onClick=\"zipcode('9','zip_td')\"><br/>
                    <input type='text' name='address1' id='addr1' value='".($gp_ix == 1 ? $member['address1'] : $member['group_address1'])."' style='width:400px;' readonly /> <br/>
                    <input type='text' name='address2' id='addr2' value='".($gp_ix == 1 ? $member['address2'] : $member['group_address2'])."' style='width:400px;' />
                </td>
              </tr>";
$Contents .= "<tr>
                <td class='input_box_title'>".($gp_ix == 1?'':'감독자')." 사진첨부</td>
                <td class='input_box_item'>";
                if($gp_ix == 1) {
                    if(!empty($member['image_url_path'])){
                        $Contents .= "<img src='".$championship_path.'/'.$member['image_url_path']."' style='width:300px;' /> <button type='button' class='delete_file' data-id='".$cm_ix."'>파일삭제</button>";
                    }else {
                        $Contents .= "<input type='file' name='image_file[]' />";
                    }
                }else {
                    if(!empty($member['group_master_image_url_path'])){
                        $Contents .= "<img src='".$championship_path.'/'.$member['group_master_image_url_path']."' style='width: 300px;' /> <button type='button' id='master_delete_file'>파일삭제</button>";
                    }else {
                        $Contents .= "<input type='file' name='master_image_file[]' />";
                    }
                }
$Contents .= "  </td>
              </tr>";
$Contents .= "</tbody>";
$Contents .= "</table><br/>";
$Contents .= "<table> 
           <tr><td colspan='4' style='padding-bottom:10px;font-size:15px;'><b>대회 참가 정보</b></td></tr>
              </table>";
$Contents .= "<table width='100%' cellpadding=0 cellspacing=0 border='0' class='search_table_box'>";
$Contents .= "<colgroup>
                <col width='10%'>
                <col width='35%'>
                </colgroup>";
$Contents .= "<tbody>";



if($gp_ix == 1) {
    $Contents .= "<tr><td class='input_box_title'>소속명</td><td class='input_box_item'><input type='text' name='class_name' value='" . ($member['class_name']) . "' /></td></tr>";
    $Contents .= "<tr>
                <td class='input_box_title'>참가그룹</td>
                <td class='input_box_item'>
                    <select name='attend_group'>";
    for ($i = 0; $i < count($group); $i++) {
        $Contents .= "<option value='" . $group[$i]['co_ix'] . "' " . ($member['attend_group'] == $group[$i]['co_ix'] ? 'selected' : '') . ">" . $group[$i]['option_value'] . "</option>";
    }
    $Contents .= "    </select>
                </td>
              </tr>";
    $Contents .= "<tr>
                <td class='input_box_title'>참가종목1</td>
                <td class='input_box_item'>
                    <select name='attend_event1'>
                    <option value=''>선택</option>
                    ";
    for ($i = 0; $i < count($event); $i++) {
        $Contents .= "<option value='" . $event[$i]['co_ix'] . "' " . ($member['attend_event1'] == $event[$i]['co_ix'] ? 'selected' : '') . ">" . $event[$i]['option_value'] . "</option>";
    }
    $Contents .= "    </select>
                </td>
              </tr>";
    $Contents .= "<tr>
                <td class='input_box_title'>참가종목2</td>
                <td class='input_box_item'>
                    <select name='attend_event2'>
                    <option value=''>선택</option>
                    ";
    for ($i = 0; $i < count($event); $i++) {
        $Contents .= "<option value='" . $event[$i]['co_ix'] . "' " . ($member['attend_event2'] == $event[$i]['co_ix'] ? 'selected' : '') . ">" . $event[$i]['option_value'] . "</option>";
    }
    $Contents .= "    </select>
                </td>
              </tr>";
}else {
    $Contents .= "<tr>
                <td class='input_box_title'>단체전 참가여부</td>
                <td class='input_box_item'>";
    //
    $attend_event = explode(',', $member['attend_event']);
    if($member['attend_event']){
        $attend_event_count = count($attend_event);
    }else{
        $attend_event_count = 0;
    }
    for ($i = 0; $i < count($attend); $i++) {
        $Contents .= "<input type='checkbox' id='attend".($attend[$i]['co_ix'])."' name='attend[]' value='" . $attend[$i]['co_ix'] . "' " . (in_array($attend[$i]['co_ix'], $attend_event) ? 'checked' : '') . ">
        <label for='attend".($attend[$i]['co_ix'])."'>" . $attend[$i]['option_value']."</label>";
        if($i % 2 == 1 && $i != 0) {
            $Contents .= "<br/>";
        }
    }

    $Contents .= "</td>
              </tr>";
    $Contents .= "<tr><td class='input_box_title'>신청 인원수</td><td class='input_box_item'><input type='text' name='member_cnt' value='".($member['member_cnt'])."' /></td></tr>";



    for($j = 0; $j < count($group_member); $j++) {
        $gmember = $group_member[$j];
        $ghandphone = explode('-',$gmember['handphone']);
        $gemail = explode('@', $gmember['email']);

        $Contents .= "<tr><td class='input_box_title' style='text-align:center'>".($j+1)."<button type='button' class='delete_member' data-id='".($gmember['cm_ix'])."' style='margin-left:20px;'>삭제</button></td><td><table width='100%' cellpadding=0 cellspacing=0 border='0' class='search_table_box'><colgroup><col width='20%'><col width='30%'><col width='20%'><col width='30%'></colgroup><tbody>
                        <tr>
                        <input type='hidden' name='g_cm_ix[]' value='".($gmember['cm_ix'])."' />
                        <td class='input_box_title'>그룹</td>
                        <td class='input_box_item' colspan='3'>
                            <select name='g_attend_group[]'>";
                            for ($i = 0; $i < count($group); $i++) {
                                $Contents .= "<option value='" . $group[$i]['co_ix'] . "' " . ($gmember['attend_group'] == $group[$i]['co_ix'] ? 'selected' : '') . ">" . $group[$i]['option_value'] . "</option>";
                            }
        $Contents .= "    </select>
                      </td></tr>";

        $Contents .= "<tr>
                        <td class='input_box_title'>이름(실명)</td>
                        <td  class='input_box_item'><input type='text' name='g_name[]' value='".($gmember['name'])."' /></td>
                        <td class='input_box_title'>참가종목1</td>
                        <td class='input_box_item'>
                            <select name='g_attend_event1[]'>
                                <option value=''> 선택</option>
                            ";
                            for ($i = 0; $i < count($event); $i++) {
                                $Contents .= "<option value='" . $event[$i]['co_ix'] . "' " . ($gmember['attend_event1'] == $event[$i]['co_ix'] ? 'selected' : '') . ">" . $event[$i]['option_value'] . "</option>";
                            }
        $Contents .= "    </select>
                        </td>
                      </tr>";

        $Contents .= "<tr>
                        <td class='input_box_title'>성별</td>
                        <td class='input_box_item'>
                            <input type='radio' id='male' name='g_sex[".$j."]' value='M' ".($gmember['sex']=='M' ? 'checked' : '')."/><label for='male'>남자</label>
                            <input type='radio' id='female' name='g_sex[".$j."]' value='F' ".($gmember['sex']=='F' ? 'checked' : '')."/><label for='female'>여자</label>
                        </td>
                        <td class='input_box_title'>참가종목2</td>
                        <td class='input_box_item'>
                            <select name='g_attend_event2[]'>
                            <option value=''> 선택</option>
                            ";
                            for ($i = 0; $i < count($event); $i++) {
                                $Contents .= "<option value='" . $event[$i]['co_ix'] . "' " . ($gmember['attend_event2'] == $event[$i]['co_ix'] ? 'selected' : '') . ">" . $event[$i]['option_value'] . "</option>";
                            }
        $Contents .= "    </select>
                        </td>
                      </tr>";

        $Contents .= "<tr>
                        <td class='input_box_title'>생년월일</td><td class='input_box_item'><input type='text' name='g_birthday[]' value='".($gmember['birthday'])."' /></td>
                        <td class='input_box_title'>참가기념 티셔츠 사이즈</td>
                        <td class='input_box_item'>
                            <input type='radio' id='sSize' name='g_size[".$j."]' value='S' " . ($gmember['size'] == 'S' ? 'checked' : '') . "/><label for='sSize'>S</label>
                            <input type='radio' id='mSize' name='g_size[".$j."]' value='M' " . ($gmember['size'] == 'M' ? 'checked' : '') . "/><label for='mSize'>M</label>
                            <input type='radio' id='lSize' name='g_size[".$j."]' value='L' " . ($gmember['size'] == 'L' ? 'checked' : '') . "/><label for='lSize'>L</label>
                            <input type='radio' id='xlSize' name='g_size[".$j."]' value='XL' " . ($gmember['size'] == 'XL' ? 'checked' : '') . "/><label for='xlSize'>XL</label>
                            <input type='radio' id='xxlSize' name='g_size[".$j."]' value='XXL' " . ($gmember['size'] == 'XXL' ? 'checked' : '') . "/><label for='xxlSize'>XXL</label>
                        </td>
                      </tr>";

        $Contents .= "<tr>
                        <td class='input_box_title'>핸드폰번호</td>
                        <td class='input_box_item' colspan='3'>
                            <input type='text' name='g_handphone1[]' value='".($ghandphone[0])."' /> - <input type='text' name='g_handphone2[]' value='".($ghandphone[1])."' /> - <input type='text' name='g_handphone3[]' value='".($ghandphone[2])."' />
                        </td>
                      </tr>";
        if(false){
        $Contents .= "<tr>
                        <td class='input_box_title'>이메일 주소</td>
                        <td class='input_box_item' colspan='3'>
                            <input type='text' name='g_email1[]' value='".($gemail[0])."' /> @
                            <input type='text' name='g_email2[]' value='".($gemail[1])."' />
                            <select class='select_mail' name='g_select_email[]'>
                                <option value='naver.com' ".($gemail[1]=='naver.com'?'selected':'').">naver.com</option>
                                <option value='gmail.com' ".($gemail[1]=='gmail.com'?'selected':'').">gmail.com</option>
                                <option value='hotmail.com' ".($gemail[1]=='hotmail.com'?'selected':'').">hotmail.com</option>
                                <option value='hanmail.net' ".($gemail[1]=='hanmail.net'?'selected':'').">hanmail.net</option>
                                <option value='daum.net' ".($gemail[1]=='daum.net'?'selected':'').">daum.net</option>
                                <option value='nate.com' ".($gemail[1]=='nate.com'?'selected':'').">nate.com</option>
                                <option value='direct'>직접입력</option>
                            </select>
                        </td>
                      </tr>";
        }
        $Contents .= "<tr>
                <td class='input_box_title'>사진첨부</td>
                <td class='input_box_item' colspan='3'>";

        if(!empty($gmember['image_url_path'])){
            $Contents .= "<img src='".$championship_path.'/'.$gmember['image_url_path']."' style='width:300px;'/> <button type='button' class='delete_file' data-id='".$gmember['cm_ix']."'>파일삭제</button>";
        }else {
            $Contents .= "<input type='file' name='image_file[$j]' />";
        }
        $Contents .= "  </td>
              </tr>";
        $Contents .= "</tbody></table></td></tr>";
    }
}

$price = 40000;
if($gp_ix != 1) {
    $price = (20000 * $attend_event_count) + (40000 * $member['member_cnt']);
}

$Contents .= "<tr><td class='input_box_title'>입금계좌</td><td class='input_box_item'><b>신한 140-010-165862 / 예금주 : (주)배럴</b></td></tr>";
$Contents .= "<tr><td class='input_box_title'>참가비</td><td class='input_box_item'><b>".number_format($price)."원</b></td></tr>";
$Contents .= "<tr><td class='input_box_title'>입금자명</td><td class='input_box_item'><input type='text' name='depositor' value='".($gp_ix == '1' ? $member['depositor'] : $member['master_depositor'])."' /></td></tr>";
$Contents .= "<tr><td class='input_box_title'>신청서 비밀번호</td><td class='input_box_item'><input type='password' name='password' value='' /> <input type='checkbox' name='modify_pw' value='1' id='modify_pw'><label for='modify_pw'>비밀번호수정</label> </td></tr>";
$Contents .= "</tbody>";
$Contents .= "</table></form>";
$Contents .= "<div style='text-align:center'><input id='btn_submit' type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></div>";

$P = new LayOut();
$P->addScript = $Script."<script language='javascript' src='../order/orders.js'></script>";
$P->Navigation = "프로모션 전시관리 > 챔피언십관리 > 스프린트 챔피언십 참가신청(수정)";
$P->title = "스프린트 챔피언십 스프린트 챔피언십 참가신청(수정)";
$P->strLeftMenu = display_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();