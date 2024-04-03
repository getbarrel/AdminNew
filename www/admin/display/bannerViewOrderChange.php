<?php
/**
 * Created by PhpStorm.
 * User: moon
 * Date: 2019-10-29
 * Time: 오후 5:28
 */
include $_SERVER['DOCUMENT_ROOT']."/admin/class/layout.class";

if($_GET['display_cid']){
    $where = " and display_cid = '".$_GET['display_cid']."'";
}
$sql = "select * from shop_bannerinfo where banner_position = '".$_GET['banner_position']."' $where order by banner_page, banner_position, display_cid desc,view_order asc	, regdate DESC ";

$db->query($sql);
$bannerInfos = $db->fetchall();

if(is_array($bannerInfos)){
    $bannerItem = "";
    foreach($bannerInfos as $key=>$val){
        if($val['banner_kind'] == "1" || $val['banner_kind'] == "4" ){
            if($val['banner_img'] && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config['mall_data_root']."/images/banner/".$val['banner_ix']."/".$val['banner_img'])){
                $image_info = getimagesize ($DOCUMENT_ROOT.$admin_config['mall_data_root']."/images/banner/".$val['banner_ix']."/".$val['banner_img']);
                //print_r($image_info);
                if($image_info[0] > 300){
                    $bannerItem .= "
                    <li class='item'>
                        <img src='".$admin_config['mall_data_root']."/images/banner/".$val['banner_ix']."/".$val['banner_img']."'  style='vertical-align:middle' ".($_COOKIE['banner_image_view'] == 1 ? "":"width=140")." style='cursor:pointer;'>
                        <div>".$val['banner_name']."</div>
                        <input type='hidden' name='banner_ix[]' value='".$val['banner_ix']."' />
                    </li>";
                }else{
                    $bannerItem .= "
                    <li class='item'>
                        <img src='".$admin_config['mall_data_root']."/images/banner/".$val['banner_ix']."/".$val['banner_img']."' style='vertical-align:middle'  ".($_COOKIE['banner_image_view'] == 1 ? "":"height=50")."  style='cursor:pointer;'>
                        <div>".$val['banner_name']."</div>
                        <input type='hidden' name='banner_ix[]' value='".$val['banner_ix']."' />
                    </li>";
                }
            }else{
                $imageSrc = "/admin/images/noimage_152_148.gif";
                $image_info = getimagesize ($DOCUMENT_ROOT.$imageSrc);
                //print_r($image_info);
                if($image_info[0] > 300){
                    $bannerItem .= "
                    <li class='item'>
                        <img src='".$imageSrc."'  style='vertical-align:middle' ".($_COOKIE['banner_image_view'] == 1 ? "":"width=140")." style='cursor:pointer;'>
                        <div>".$val['banner_name']."</div>
                        <input type='hidden' name='banner_ix[]' value='".$val['banner_ix']."' />
                    </li>";
                }else{
                    $bannerItem .= "
                    <li class='item'>
                        <img src='".$imageSrc."' style='vertical-align:middle'  ".($_COOKIE['banner_image_view'] == 1 ? "":"height=50")."  style='cursor:pointer;'>
                        <div>".$val['banner_name']."</div>
                        <input type='hidden' name='banner_ix[]' value='".$val['banner_ix']."' />
                    </li>";
                }
            }
        }else{
            $bannerItem .= "<div>순서 변경이 가능한 배너 정보가 존재하지 않습니다.</div>";
        }
    }
}

$Contents = "
<style>
  #sortable {
    list-style-type: none;
    margin: 0;
    padding: 0;
  }

  #sortable li {
    margin: 0 3px 3px;
    padding: .4em;
    font-size: 1.4em;
    line-height: 2.5rem;
    float: left;
   /* width: 140px;
    height: 85px;*/
    border: 1px solid #000;
    background-color: #efefef;
  }

  button {
    margin-top: 10px;
    margin-left: 45%;
    width: 10%;
  }
</style>
<form name='banner_frm' method='post' action='../display/bannerViewOrderChange.act.php' target='act'>
<input type='hidden' name='act' value='update' />
<input type='hidden' name='banner_position' value='".$banner_position."' />
<input type='hidden' name='display_cid' value='".$display_cid."' />
<div>
    <ul id='sortable'>
        ".$bannerItem."
    </ul>
</div>
<div style='clear:both;'>
<button>적용</button>
</form>
</div>
    
";
$Script = "
<script>
    $(function () {
      $('#sortable').sortable();
      $('#sortable').disableSelection();
    });
</script>
";

$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->OnloadFunction = "";
$P->strLeftMenu = display_menu();
$P->Navigation = "프로모션/전시 > 배너관리 > 배너노출순서변경";
$P->title = "배너노출순서변경";
$P->NaviTitle = "배너노출순서변경";
$P->strContents = $Contents;
echo $P->PrintLayOut();