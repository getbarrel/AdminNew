<?
//include($_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class");
include_once($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include("../display/content.lib.php");

$db = new Database;
$master_db = new Database;
$master_db->master_db_setting();
?>
<link rel="stylesheet" href="/admin/v3/include/admin.css?912648106" type="text/css">
<link rel="stylesheet" href="/admin/css/design.css" type="text/css">
<link rel="stylesheet" href="/admin/common/css/design.css" type="text/css">
<link href="/admin/css/facebox2.css" type="text/css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="/admin/v3/css/class.css">
<link rel="stylesheet" type="text/css" href="/admin/v3/css/common.css">
<link rel="stylesheet" type="text/css" href="/admin/v3/css/jquery-ui.css">
<script src="/admin/js/jquery-1.8.3.js"></script>
<script src="/admin/js/jquery-ui.js"></script>
<script type='text/javascript'>
    function choiceChk(val, gubun){
        var str = '';

        if($('#choiceContentCon_'+val).is(':checked')){
            var title = $('#title_'+val).val();
            str += "<li id=li_contentImage_"+gubun+"_"+val+" vieworder=" + val + " viewcnt=" + val + " style=float:left;width:110px;>";
            str += "<table width=95% cellspacing=1 cellpadding=0 style=table-layout:fixed;height:180px;>";
            str += "<tr><td align=center><img src="+$('#img_'+val).val()+" width=100px height=100px>";
            str += "<br>"+title.replace(/\n/g, "<br />")+"";
            str += "<input type=hidden name=con_ix_"+gubun+"[] id=con_ix_"+val+" value="+val+" />";
            str += "</td></tr>";
            str += "<tr><td align=center><button type=button onclick=imgDel('"+val+"',"+gubun+")>삭제</td></tr>";
            str += "</table>";
            str += "</li>";

            if(gubun == 1){
                opener.$('#choiceSpecial').append(str);
            }else if(gubun == 2){
                opener.$('#choiceStyle').append(str);
            }else if(gubun == 3){
                opener.$('#choiceContent').append(str);
            }
        }else{
            opener.$('#li_contentImage_'+gubun+'_'+val).remove();
        }
    }

    function groupChoiceChk(val, gubun, groupNum){
        var str = '';

        if($('#choiceContentCon_'+val).is(':checked')){
            if(gubun == 2){
                name = '스타일';
                groupGubun = 'S';
            }else if(gubun == 3) {
                name = '컨텐츠';
                groupGubun = 'C';
            }else{
                name = '배너';
                groupGubun = 'B';
            }
            var title = $('#title_'+val).val();
            str += "<li id=li_contentImage_"+groupNum+"_"+gubun+"_"+val+" vieworder=" + val + " viewcnt=" + val + " style=float:left;width:110px;>";
            str += "<table width=95% cellspacing=1 cellpadding=0 style=table-layout:fixed;height:180px;>";
            str += "<tr>";
            //if(gubun == 3){
                str += "<td align=center>";
            //}else{
            //    str += "<td align=center>"+name+"<br>";
            //}
            str += "<img src="+$('#img_'+val).val()+" width=100px height=100px>";
            str += "<br>"+title.replace(/\n/g, "<br />")+"";
            str += "<input type=hidden name=group_con_ix["+groupNum+"][] id=group_con_ix_"+val+" value="+val+" />";
            str += "<input type=hidden name=group_con_gubun["+groupNum+"][] id=group_con_gubun_"+val+" value="+groupGubun+" />";
            str += "</td></tr>";
            str += "<tr><td align=center><button type=button onclick=imgGroupDel('"+val+"',"+gubun+","+groupNum+")>삭제</td></tr>";
            str += "</table>";
            str += "</li>";

            opener.$('#choiceGorupContent_'+groupNum).append(str);

            opener.$('#choiceGorupContent_'+groupNum ).sortable();
        }else{
            opener.$('#li_contentImage_'+groupNum+'_'+gubun+'_'+val).remove();
        }
    }


</script>
<?

if($gubun == 1){
    //$queryCid   = '001001';
    //$queryCid2  = '002';
    $content_type = 1;
    $title      = '기획전';
}else if($gubun == 2){
    //$queryCid   = '001002';
    $content_type = 2;
    $title      = '스타일';
}else if($gubun == 3){
    //$queryCid   = '001001';
    $content_type = 1;
    $title      = '컨텐츠';
}else if($gubun == 4){
    //$queryCid   = '001002';
    $title      = '배너';
}

if($gubun == 4){
    $sql = "SELECT 
            banner_ix as con_ix, banner_name as title, banner_img as list_img 
        FROM
            shop_bannerinfo 
        WHERE
            disp = '1' AND
            banner_position = '65' AND
            '".date("Y-m-d H:i:s" , time())."' BETWEEN use_sdate AND use_edate
        ORDER BY shot_title ASC
    ";
/*}else if($gubun == 1){
    $sql = "SELECT
            con_ix, title, list_img, cid 
        FROM
            shop_content 
        WHERE
            (cid LIKE '$queryCid%' OR cid LIKE '$queryCid2%') AND
            display_use = 'Y' AND
            display_state = 'D' AND
            display_start <= '".time()."' AND
            display_end >= '".time()."'
        ORDER BY title ASC
    ";*/
}else{
    /*$sql = "SELECT
            con_ix, title, list_img 
        FROM
            shop_content 
        WHERE
            cid LIKE '$queryCid%' AND
            display_use = 'Y' AND
            display_state = 'D' AND
            display_start <= '".time()."' AND
            display_end >= '".time()."'
        ORDER BY title ASC
    ";*/
    $sql = "SELECT 
            c.con_ix, c.title, c.list_img 
        FROM
            shop_content AS c LEFT JOIN shop_content_class AS cs ON c.cid = cs.cid  
        WHERE
            cs.content_type = $content_type AND
            c.display_use = 'Y' AND
            c.display_state = 'D' AND
            c.display_start <= '".time()."' AND
            c.display_end >= '".time()."'
        ORDER BY c.title ASC
    ";
}

$db->query($sql);
$content_info = $db->fetchall("object");

$quotient = (count($content_info) - (count($content_info) % 4)) / 4;
$remainder = count($content_info) % 4;
?>
<table border="0" width="100%" cellpadding="0" cellspacing="1" align="center">
    <tr>
        <td align=center style="padding: 0 10 0 10">
            <table border="0" width="100%" cellspacing="1" cellpadding="0" >
                <tr>
                    <td>
                        <div style="padding:5px"><img src="../images/dot_org.gif" align="absmiddle"> <b><?=$title?></b></div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table border="0" width="100%" cellspacing="1" cellpadding="3" class="input_table_box" style="table-layout:fixed;background:<?if(count($content_info) != 0){?>#FFFFFF<?}?>;" >
                            <col width="25%">
                            <col width="25%">
                            <col width="25%">
                            <col width="25%">
<?
                            if(count($content_info) == 0){
?>
                            <tr>
                                <td class="input_box_item" style="text-align:center;" colspan="4">적용가능한 <?=$title?>이 없습니다.</td>
                            </tr>
<?
                            }else{

                            $i = 0;
                            for($t=0;$t <= $quotient;$t++){
?>
                            <tr>
<?
                                for($n=0;$n < 4;$n++){
                                    if($t == $quotient && $n == $remainder){
                                        break;
                                    }
                                    if($gubun == 4){
                                        $img = $_SESSION["admin_config"]["mall_data_root"] . "/images/banner/".$content_info[$i][con_ix]."/".$content_info[$i][list_img];
                                    }else{
                                        $img = $_SESSION["admin_config"]["mall_data_root"] . "/images/content/".$content_info[$i][con_ix]."/".$content_info[$i][list_img];
                                    }
?>
                                <td class="input_box_item" style="text-align:center;">
                                    <input type="hidden" name="title" id="title_<?=$content_info[$i][con_ix]?>" value="<?=$content_info[$i][title]?>">
                                    <input type="hidden" name="img" id="img_<?=$content_info[$i][con_ix]?>" value="<?=$img?>">
                                    <img src="<?=$img?>" style="width:100px;height:130px;"><br>
<?
                                    if($group_num == ''){
?>
                                        <input type="checkbox" name="choiceContentCon" id="choiceContentCon_<?=$content_info[$i][con_ix]?>" value="<?=$content_info[$i][con_ix]?>" onclick="choiceChk(this.value, <?=$gubun?>)"><?=nl2br($content_info[$i][title])?>
<?
                                    }else{
?>
                                        <input type="checkbox" name="choiceContentCon" id="choiceContentCon_<?=$content_info[$i][con_ix]?>" value="<?=$content_info[$i][con_ix]?>" onclick="groupChoiceChk(this.value, <?=$gubun?>, <?=$group_num?>)"><?=nl2br($content_info[$i][title])?>
<?
                                    }
?>
                                </td>

<?
                                $i++;
                                }
?>
                            </tr>
<?
                            }
                            }
?>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan=2 align=center style='padding:10px 0px;'>
            <button type='button' onclick='window.close();'>닫기</button>
        </td>
    </tr>
</table>

