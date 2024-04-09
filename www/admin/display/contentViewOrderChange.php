<?php
/**
 * Created by PhpStorm.
 * User: moon
 * Date: 2019-10-29
 * Time: 오후 5:28
 */
include $_SERVER['DOCUMENT_ROOT']."/admin/class/layout.class";

if($_GET['display_cid']){
    $schDepth           = $_GET['depth'];
    $schCid             = $_GET['display_cid'];

    if($schDepth == 0){
        $queryCid = substr($schCid,0,3);
    }else if($schDepth == 1){
        $queryCid = substr($schCid,0,6);
    }else if($schDepth == 2){
        $queryCid = substr($schCid,0,9);
    }else if($schDepth == 3){
        $queryCid = substr($schCid,0,12);
    }else if($schDepth == 4){
        $queryCid = $schCid;
    }
}
$sql = "select * from shop_content where cid like '$queryCid%' AND depth = '$schDepth' order by sort asc, cid desc, regdate DESC ";
$db->query($sql);
$contentInfos = $db->fetchall();

// $img = "<img src='".$_SESSION["admin_config"]["mall_data_root"] . "/images/content/".$content_infos[$i][con_ix]."/".$content_infos[$i][list_img]."' style='width:75px;'>";

if(is_array($contentInfos)){
    $bannerItem = "";
    foreach($contentInfos as $key=>$val){

            if($val['list_img'] && file_exists($_SESSION["admin_config"]["mall_data_root"] . "/images/content/".$val['con_ix']."/".$val['list_img'])){
                $image_info = getimagesize ($_SESSION["admin_config"]["mall_data_root"] . "/images/content/".$val['con_ix']."/".$val['list_img']);

                if($image_info[0] > 300){
                    $bannerItem .= "
                    <li class='item'>
                        <img src='".$_SESSION["admin_config"]["mall_data_root"] . "/images/content/".$val['con_ix']."/".$val['list_img']."'  style='vertical-align:middle' ".($_COOKIE['banner_image_view'] == 1 ? "":"width=140")." style='cursor:pointer;'>
                        <div>".$val['title']."</div>
                        <input type='hidden' name='con_ix[]' value='".$val['con_ix']."' />
                    </li>";
                }else{
                    $bannerItem .= "
                    <li class='item'>
                        <img src='".$_SESSION["admin_config"]["mall_data_root"] . "/images/content/".$val['con_ix']."/".$val['list_img']."' style='vertical-align:middle'  ".($_COOKIE['banner_image_view'] == 1 ? "":"height=50")."  style='cursor:pointer;'>
                        <div>".$val['title']."</div>
                        <input type='hidden' name='con_ix[]' value='".$val['con_ix']."' />
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
                        <div>".$val['title']."</div>
                        <input type='hidden' name='con_ix[]' value='".$val['con_ix']."' />
                    </li>";
                }else{
                    $bannerItem .= "
                    <li class='item'>
                        <img src='".$imageSrc."' style='vertical-align:middle'  ".($_COOKIE['banner_image_view'] == 1 ? "":"height=50")."  style='cursor:pointer;'>
                        <div>".$val['title']."</div>
                        <input type='hidden' name='con_ix[]' value='".$val['con_ix']."' />
                    </li>";
                }
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
<form name='banner_frm' method='post' action='../display/contentViewOrderChange.act.php' target='act'>
<input type='hidden' name='act' value='update' />
<input type='hidden' name='depth' value='".$schDepth."' />
<input type='hidden' name='display_cid' value='".$schCid."' />
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
$P->Navigation = "프로모션/전시 > 컨텐츠관리 > 컨텐츠노출순서변경";
$P->title = "컨텐츠노출순서변경";
$P->NaviTitle = "컨텐츠노출순서변경";
$P->strContents = $Contents;
echo $P->PrintLayOut();