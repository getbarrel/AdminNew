<?
include("../class/layout.class");

if ($max == "") {
    $max = 20; //페이지당 갯수
}

if ($page == '') {
    $start = 0;
    $page = 1;
} else {
    $start = ($page - 1) * $max;
}

$db = new MySQL;

$sql = "SHOW COLUMNS  FROM global_translation LIKE 'file_short_content'";
$db->query($sql);

if ($db->total == 0) {
    $sql = "ALTER TABLE `omnichannel_db`.`global_translation` ADD COLUMN `file_short_content` LONGTEXT NULL  COMMENT '파일내용' AFTER `file_name`;";
    $db->query($sql);
}

$language_list = getTranslationType("", "", "array");
$globalInfo = getGlobalInfo();

$db->query("SELECT * FROM global_translation al WHERE  al.trans_ix = '" . $trans_ix . "'");
$db->fetch();

if ($db->total) {
    $trans_div = $db->dt['trans_div'];
    $trans_type = $db->dt['trans_type'];
    $act = "update";
} else {
    $act = "insert";
}


//$db = new MySQL;
/*
if($list_type == "grid"){

}else{
$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;'>
	<col width='25%' />
	<col width='*' />
	  <tr >
		<td align='left' colspan=2> ".GetTitleNavigation("번역관리", "상점관리 > 번역관리 ")."</td>
	  </tr>";

	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C") || checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U") || true){
	$Contents01 .= "
		  <tr>
			<td align='left' colspan=2 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b>추가하기</b></div>")."</td>
		  </tr>
		  </table>
		  <table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='input_table_box'>
			<col width='20%' />
			<col width='*' />

		  <tr bgcolor=#ffffff height='34'>
			<td class='input_box_title'> <b>번역언어 <img src='".$required3_path."'></b> </td>
			<td class='input_box_item'>
			".getTranslationType($trans_type,"")."
			<!--input type=text class='textbox' name='trans_div' value='".$db->dt['trans_div']."' style='width:430px;' validation=true title='메뉴구분'> <span class=small></span-->
			</td>
			<td class='input_box_title'> <b>구분 <img src='".$required3_path."'></b> </td>
			<td class='input_box_item'>
			<input type=text class='textbox' name='trans_div' validation=true title='구분' value='".$db->dt['trans_div']."'>
			<span class=small></span>
			</td>
		  </tr>
		  <tr bgcolor=#ffffff height='34'>
			<td class='input_box_title'> <b>파일경로 <img src='".$required3_path."'></b> </td>
			<td class='input_box_item'>
			<input type=text class='textbox' name='file_path' validation=true title='파일경로' value='".$db->dt['file_path']."'>
			<span class=small></span>
			</td>
			<td class='input_box_title'> <b>파일명 <img src='".$required3_path."'></b> </td>
			<td class='input_box_item'>
			<input type=text class='textbox' name='file_name' validation=true title='파일명' value='".$db->dt['file_name']."'>
			<span class=small></span>
			</td>
		  </tr>
		  <tr bgcolor=#ffffff height='34'>
			<td class='input_box_title'> <b>한글번역 <img src='".$required3_path."'></b> </td>
			<td class='input_box_item' colspan=3>
			<textarea type=text class='textbox' name='text_korea' style='padding:4px;width:97%;height:40px;margin:4px 0px' validation=true title='한글번역'>".$db->dt['text_korea']."</textarea>
			<span class=small></span>
			</td>
		  </tr>
		  <tr bgcolor=#ffffff height='34'>
			<td class='input_box_title' > <b>번역문구 <img src='".$required3_path."'></b> </td>
			<td class='input_box_item' colspan=3>
			<textarea type=text class='textbox' name='trans_text' style='padding:4px;width:97%;height:40px;margin:4px 0px' validation=true title='번역문구'>".$db->dt['trans_text']."</textarea>
			<span class=small></span></td>
		  </tr>
		  <!--tr bgcolor=#ffffff height='34'>
			<td class='input_box_title'> <b>인도네시아어 번역</b> </td>
			<td class='input_box_item'>
			<textarea type=text class='textbox' name='text_indomesian' style='padding:4px;width:700px;height:40px;margin:4px 0px' validation=false title='인도네시아어번역'>".$db->dt['text_indomesian']."</textarea>
			<span class=small></span></td>
		  </tr>
		  <tr bgcolor=#ffffff height='34'>
			<td class='input_box_title'> <b>중국어번역 </b> </td>
			<td class='input_box_item' colspan=3>
			<textarea type=text class='textbox' name='text_chinese' style='padding:4px;width:700px;height:40px;margin:4px 0px' validation=false title='중국어번역'>".$db->dt['text_chinese']."</textarea>
			<span class=small></span></td>
		  </tr-->
		  <tr bgcolor=#ffffff height='34'>
			<td class='input_box_title'> <b>사용유무 <img src='".$required3_path."'></b> </td>
			<td class='input_box_item' colspan=3>
				<input type=radio name='disp' value='1' checked><label for='disp_1'>사용</label>
				<input type=radio name='disp' value='0' ><label for='disp_0'>사용하지않음</label>
			</td>
		  </tr>";
	}
	$Contents01 .= "
		  </table>";

	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C") || checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
	$ButtonString = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	<tr bgcolor=#ffffff ><td colspan=4 align=center style='padding:10px 0px'><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
	</table>
	";
	}
}

*/

if ($language_type) {
    $join_where = " and td.trans_type = '" . $language_type . "'";
}

$where = " where t.trans_ix is not null ";
if ($trans_div) //$where .= " and trans_div = '$trans_div' ";

    if ($trans_type) {
        if ($trans_type != 'only_searchtexts') {
            $where .= " and td.trans_type = '$trans_type' ";
        }
    }

if ($search_text != "") {
    $bak_search_text = $search_text;
//    $search_text = mysqli_real_escape_string($db->dblink(), $search_text);
    if ($trans_type == 'only_searchtexts') {
        $where .= " and (trans_div = '" . $search_text . "' or text_korea = '" . $search_text . "' or td.trans_text = '" . $search_text . "'  or td.trans_key = '" . $search_text . "' )"; //d29dad6039ffb8ca418efb0bd3c892c3
    } else {
        if ($is_exact) {
            $where .= " and (trans_div LIKE '" . $search_text . "' or text_korea LIKE '" . $search_text . "' or td.trans_text LIKE '" . $search_text . "'  or td.trans_key LIKE '" . $search_text . "' )";
        } else {
            $where .= " and (trans_div LIKE '%" . $search_text . "%' or text_korea LIKE '%" . $search_text . "%' or td.trans_text LIKE '%" . $search_text . "%'  or td.trans_key LIKE '%" . $search_text . "%' or t.trans_key LIKE '%" . $search_text . "%' )";
        }//d29dad6039ffb8ca418efb0bd3c892c3
    }
    $search_text = $bak_search_text;
    $search_text = htmlspecialchars($search_text, ENT_QUOTES);
    $search_text = htmlspecialchars_decode($search_text, ENT_NOQUOTES);


}

/*
$sql = "SELECT count(*) as total
          FROM global_translation t
	 LEFT JOIN global_translation_detail td ON t.trans_ix = td.trans_ix $join_where
			$where
		   AND call_cnt > 0 ";
*/
$sql = "SELECT COUNT(A.trans_ix) AS total
          FROM (
                SELECT t.trans_ix
                  FROM global_translation t
             LEFT JOIN global_translation_detail td ON t.trans_ix = td.trans_ix $join_where
                $where
                   AND call_cnt > 0 
              GROUP BY t.trans_ix 
          ) AS A ";
$db->query($sql);
$db->fetch();
$use_total = $db->dt['total'];

/*
$sql = "SELECT COUNT(*) as total
          FROM global_translation t
     LEFT JOIN global_translation_detail td ON t.trans_ix = td.trans_ix $join_where
            $where   ";
*/
$sql = "SELECT COUNT(A.trans_ix) AS total
          FROM (
                SELECT t.trans_ix
                  FROM global_translation t
             LEFT JOIN global_translation_detail td ON t.trans_ix = td.trans_ix $join_where
                $where
              GROUP BY t.trans_ix 
          ) AS A ";
$db->query($sql);
$db->fetch();
$total = $db->dt['total'];

$str_page_bar = page_bar($total, $page, $max, "&view=innerview&max=$max&search_text=$search_text&trans_type=$trans_type", "");
if (false) {
    $sql = "SELECT t.trans_ix, t.trans_key, trans_div, td.trans_type, file_path, file_name, text_name, text_korea, is_check, disp, t.viewdate, td.regdate, td.trans_text 
		  FROM global_translation t 
	 LEFT JOIN global_translation_detail td ON t.trans_ix = td.trans_ix $join_where
			$where 
	  ORDER BY viewdate desc, regdate DESC 
		 LIMIT $start, $max ";
} else {
    $sql = "SELECT t.trans_ix, t.trans_key, t.trans_div, t.file_path, t.file_name, t.text_name, t.text_korea, t.is_check, t.disp , t.regdate
          FROM global_translation t
     LEFT JOIN global_translation_detail td ON t.trans_ix = td.trans_ix $join_where
        $where 
      GROUP BY t.trans_ix, t.trans_key, t.trans_div, t.file_path, t.file_name, t.text_name, t.text_korea, t.is_check, t.disp 
      ORDER BY t.regdate DESC
		 LIMIT $start, $max ";
}
//echo nl2br($sql);
$db->query($sql);

$trans_datas = $db->fetchall();


$SearchForm = "<form name='search_frm' method='GET'><input type='hidden' name='trans_div' value='$trans_div'>
				<table>
					<tr>
						<td><img src='../image/title_head.gif' align=absmiddle> <b><!--랭귀지 목록--> 검색 타입 선택</b></td>
						<td>" . getTranslationType($trans_type, "") . "</td>
						
						<td><input type='text' class=textbox name='search_text' value='$search_text' style='width:400px;'></td>
						<td><input type='image' src='../images/" . $admininfo['language'] . "/btn_search.gif' align=absmiddle></td>
						<td><input type='checkbox' class=textbox name='is_exact' id='is_exact' value='1' ><label for='is_exact'>동일키워드</label></td>
						<td>" . number_format($total) . " 개 </td>
						<td>	
							<a onclick=\"PoPWindow('./translation_pop.php','1000','500','trans_pop')\">
								<img src='../images/" . $admininfo["language"] . "/b_add.png' border=0 style='padding-left: 20px;border:0px;' style='cursor:pointer;'>
							</a>
						</td>
					</tr>
				</table>
				* 키값으로도 검색이 가능합니다 </form>";
//href='./translation_pop.php' target='_blank'
if ($list_type == "grid") {
    $Contents02 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0'  >
		 
		<col style='width:*;'>
		<col style='width:80px;'>
		<col style='width:120px;'>
		<col style='width:110px;'>
	  <tr>
	    <td align='left' colspan=7 style='padding:4px 0px;'> " . colorCirCleBox("#efefef", "100%", "<div style='padding:0px 3px 0px 13px;'> $SearchForm </div>") . "</td>
	  </tr>
	  <tr>
	    <td align='right' colspan=7 style='padding:10px 0px;'>
			<a href='translation.act.php?act=excel'><img src='../images/korean/btn_excel_save.gif' border='0'></a>
	    </td>
		
	</tr>
	</table>
	<table width='100%' cellpadding=3 cellspacing=0 border='0'  class='list_table_box' style='table-layout:fixed;word-wrap:break-word;'>
		 
		<col style='width:10%;'>
		<col style='width:*;'>
		";
    if (count((array)$language_list) > 0 && $globalInfo['global_use'] == 'Y' && $globalInfo['global_pname_type'] == 'D') {
        foreach ($language_list as $key => $li) {

            $Contents02 .= "<col style='width:15%;' >";
        }
    }
    $Contents02 .= "
		<!--col style='width:100px;'>
		<col style='width:140px;'>
		<col style='width:140px;'-->
	  <tr height=45 bgcolor=#efefef align=center style='font-weight:bold'>
	     		
	    <td class='m_td' rowspan=2> 파일경로</td>
		<!--td class='m_td' rowspan=2> 파일명</td-->
		<td class='m_td' rowspan=2> 한글문구</td>
		<td class='m_td' colspan='" . count((array)$language_list) . "' style='width:*;'> 번역문구</td>
	    <!--td class='m_td' rowspan=2> 사용유무</td>
	    <td class='m_td' rowspan=2> 등록일자<br>수정일자</td>
	    <td class='e_td' rowspan=2> 관리</td-->
	  </tr>
	  <tr>";
    if (count((array)$language_list) > 0 && $globalInfo['global_use'] == 'Y' && $globalInfo['global_pname_type'] == 'D') {
        foreach ($language_list as $key => $li) {

            $Contents02 .= "<td style='text-align:center;' >" . $li['language_name'] . "</td>";
        }
    }
    $Contents02 .= "</tr>";

    if (count((array)$trans_datas) > 0) {
        for ($i = 0; $i < count((array)$trans_datas); $i++) {
            $db->fetch($i);
            $Contents02 .= "
			  <tr bgcolor=#ffffff height=30 align=center>
				<!--td class='list_box_td'>" . ($i + 1) . "</td>
				<td class='list_box_td list_bg_gray'>" . $trans_datas[$i]['trans_type'] . "</td>
				<td class='list_box_td '>" . $trans_datas[$i]['trans_div'] . "</td-->			
				<td class='list_box_td '>" . $trans_datas[$i]['file_path'] . " - " . $trans_datas[$i]['file_name'] . "</td>
				<!--td class='list_box_td '></td-->
				<td class='list_box_td point' style='padding-left:10px;text-align:left;'>" . $trans_datas[$i]['text_korea'] . "</td>
				   ";
            if (count((array)$language_list) > 0 && $globalInfo['global_use'] == 'Y' && $globalInfo['global_pname_type'] == 'D') {
                foreach ($language_list as $key => $li) {
                    if ($key != 0) $Contents02 .= "";

                    $_global_pname = urldecode($global_pinfo['pname'][$li['language_code']]);
                    $sql = "SELECT trans_text FROM global_translation_detail td where td.trans_ix = '" . $trans_datas[$i]['trans_ix'] . "' and trans_type = '" . $li['language_code'] . "' ";
                    //echo $sql."<br>";
                    $db->query($sql);
                    if ($db->total) {
                        $db->fetch();
                        $trans_text = $db->dt['trans_text'];
                        $trans_text = htmlspecialchars($trans_text, ENT_QUOTES);
                        $trans_text = htmlspecialchars_decode($trans_text, ENT_NOQUOTES);
                    } else {
                        $trans_text = "";
                    }
                    $Contents02 .= "<td class='list_box_td' style='padding:10px; text-align:left;line-height:150%;' >
								<input type=text class='textbox trans_text' name=\"global_pinfo['" . $trans_datas[$i]['trans_ix'] . "'][" . $li['language_code'] . "]\" size=68 style='width:90%;margin-top:3px;' value='" . $trans_text . "' validation=false title='글로벌 제품명(" . $li['language_name'] . ")' placeholder='" . $li['language_name'] . "' trans_key='" . $trans_datas[$i]['trans_key'] . "' trans_ix='" . $trans_datas[$i]['trans_ix'] . "' trans_type='" . $li['language_code'] . "'></li>
									<li style='float:left;padding-top:3px;'><img src='../images/loading_large.gif' width=20 style='display:none;' id='loading_img'></li>
									</td>";

                }
            }
            $Contents02 .= "
				<!--/a-->
				
				<td class='list_box_td list_bg_gray' style='display:none;'>" . ($trans_datas[$i]['disp'] == "1" ? "사용" : "사용하지않음") . "</td>
				<td class='list_box_td ' style='display:none;'>" . $trans_datas[$i]['regdate'] . "<br>" . $trans_datas[$i]['viewdate'] . "</td>
				<td class='list_box_td list_bg_gray' style='display:none;'>";
            if (checkMenuAuth(md5($_SERVER["PHP_SELF"]), "U")) {
                $Contents02 .= "<a onclick=\"PoPWindow('./translation_pop.php?trans_div=" . $_GET["trans_div"] . "&trans_type=" . $_GET["trans_type"] . "&search_text=" . $_GET["search_text"] . "&trans_ix=" . $trans_datas[$i]['trans_ix'] . "','1000','500','trans_pop')\"><img src='../images/" . $admininfo["language"] . "/btc_modify.gif' border=0></a> ";


                //$Contents02 .= "<a href=\"javascript:updateTranslationInfo('".$trans_datas[$i]['trans_ix']."','".$trans_datas[$i]['trans_type']."','".$trans_datas[$i]['trans_div']."','".$trans_datas[$i]['text_korea']."','".$trans_datas[$i]['text_english']."','".$trans_datas[$i]['disp']."')\"><img src='../image/btc_modify.gif' border=0></a>";
            } else {
                $Contents02 .= "<a href=\"javascript:alert('수정권한이 없습니다.');\"><img src='../images/" . $admininfo["language"] . "/btc_modify.gif' border=0></a> ";
            }

            if (checkMenuAuth(md5($_SERVER["PHP_SELF"]), "D")) {
                $Contents02 .= "<a href=\"javascript:deleteTranslationInfo('delete','" . $trans_datas[$i]['trans_ix'] . "')\"><img src='../images/" . $admininfo["language"] . "/btc_del.gif' border=0></a>";
            } else {
                $Contents02 .= "<a href=\"javascript:alert('삭제권한이 없습니다.')\"><img src='../images/" . $admininfo["language"] . "/btc_del.gif' border=0></a>";
            }

            $Contents02 .= "
				</td>
			  </tr> ";
        }
    } else {
        $Contents02 .= "
			  <tr bgcolor=#ffffff height=50>
				<td align=center colspan=10>등록된 랭귀지 목록이 없습니다. </td>
			  </tr> ";
    }
    $Contents02 .= "</table>";
} else {
    $Contents02 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >
		<col style='width:80px;'>
		<col style='width:70px;'>
		<col style='width:150px;'>
		<col style='width:*;'>
		<col style='width:80px;'>
		<col style='width:120px;'>
		<col style='width:110px;'>
	  <tr>
	    <td align='left' colspan=7 style='padding:4px 0px;'> " . colorCirCleBox("#efefef", "100%", "<div style='padding:0px 3px 0px 13px;'> $SearchForm </div>") . "</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=7 style='padding:10px 0px;'>
			<table class='s_org_tab' style='width:100%;'>
			<tr>
				<td class='tab'>
					<a href='translation.act.php?act=excel'><img src='../images/korean/btn_excel_save.gif' border='0'></a>
					<a href='javascript:void()' onclick='shortContents()'><img src='../images/korean/btn_short_contents.gif' border='0'></a>

				</td>
				<td style='width:145px;text-align:right;vertical-align:bottom;padding:0px 0px 10px 0'>
					 
				</td>
				<td style='text-align:right'><b>사용 번역항목 갯수 : " . number_format($use_total) . " 개</b></td>
			</tr>
			</table>
	    </td>
	</tr>
	</table>
	
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='list_table_box' style='table-layout:fixed;word-wrap:break-word;'>
		<col style='width:50px;'>
		<!--<col style='width:80px;'>-->
		<col style='width:100px;'>
		<col style='width:100px;'>
		<col style='width:100px;'>
		<col style='width:250px;'>
		<col style='width:*;'>
		<col style='width:100px;'>
		<col style='width:80px;'>
		<col style='width:150px;'>
	  <tr height=25 bgcolor=#efefef align=center style='font-weight:bold'>
	    <td class='s_td'> 순</td>
		<!--<td class='m_td'> 언어구분</td>-->
		<td class='m_td'> 구분</td>		
	    <td class='m_td'> 파일경로</td>
		<td class='m_td'> 파일명</td>
		<td class='m_td'> 한글번역</td>
		<td class='m_td' style='width:*;'> 번역문구</td>
	    <td class='m_td'> 사용유무</td>
	    <td class='m_td'> 등록일자<br>수정일자</td>
	    <td class='e_td'> 관리</td>
	  </tr>";


    if (count((array)$trans_datas) > 0) {
        for ($i = 0; $i < count((array)$trans_datas); $i++) {
            $db->fetch($i);

            //$trans_text = htmlspecialchars($trans_text, ENT_QUOTES);

            $Contents02 .= "
			  <tr bgcolor=#ffffff height=30 align=center>
				<td class='list_box_td'>" . ($i + 1) . "</td>
				<!--<td class='list_box_td list_bg_gray'>" . $trans_datas[$i]['trans_type'] . "</td>-->
				<td class='list_box_td '>" . $trans_datas[$i]['trans_div'] . "</td>			
				<td class='list_box_td '>" . $trans_datas[$i]['file_path'] . "</td>
				<td class='list_box_td '>" . $trans_datas[$i]['file_name'] . "</td>
				<td class='list_box_td point'>" . $trans_datas[$i]['text_korea'] . "</td>
				<td class='list_box_td' style='padding:10px; text-align:left;line-height:150%;' > ";
            if (count((array)$language_list) > 0 && $globalInfo['global_use'] == 'Y' && $globalInfo['global_pname_type'] == 'D') {
                foreach ($language_list as $key => $li) {
                    if ($key != 0) $Contents02 .= "";

                    $_global_pname = urldecode($global_pinfo['pname'][$li['language_code']]);
                    $sql = "SELECT trans_text FROM global_translation_detail td where td.trans_ix = '" . $trans_datas[$i]['trans_ix'] . "' and trans_type = '" . $li['language_code'] . "' ";
                    //echo $sql."<br>";
                    $db->query($sql);
                    if ($db->total) {
                        $db->fetch();
                        $trans_text = $db->dt['trans_text'];
                        $trans_text = htmlspecialchars($trans_text, ENT_QUOTES);
                        $trans_text = htmlspecialchars_decode($trans_text, ENT_NOQUOTES);

                    } else {
                        $trans_text = "";
                    }
                    $Contents02 .= "<ul>
									<li style='float:left;'><input type=text class='textbox trans_text' name=\"global_pinfo['" . $trans_datas[$i]['trans_ix'] . "'][" . $li['language_code'] . "]\" size=68 style='width:90%;margin-top:3px;' value='" . $trans_text . "' validation=false title='글로벌 제품명(" . $li['language_name'] . ")' placeholder='" . $li['language_name'] . "' trans_key='" . $trans_datas[$i]['trans_key'] . "' trans_ix='" . $trans_datas[$i]['trans_ix'] . "' trans_type='" . $li['language_code'] . "'></li>
									<li style='float:left;padding-top:3px;'><img src='../images/loading_large.gif' width=20 style='display:none;' id='loading_img'></li>
									</ul>";
                }
            }
            $Contents02 .= "
				<!--/a-->
				</td>
				<td class='list_box_td list_bg_gray'>" . ($trans_datas[$i]['disp'] == "1" ? "사용" : "사용하지않음") . "</td>
				<td class='list_box_td '>" . $trans_datas[$i]['regdate'] . "</td>
				<td class='list_box_td list_bg_gray'>";
            if (checkMenuAuth(md5($_SERVER["PHP_SELF"]), "U")) {
                $Contents02 .= "<a onclick=\"PoPWindow('./translation_pop.php?trans_div=" . $_GET["trans_div"] . "&trans_type=" . $_GET["trans_type"] . "&search_text=" . $_GET["search_text"] . "&trans_ix=" . $trans_datas[$i]['trans_ix'] . "','1000','500','trans_pop')\" style='cursor:pointer'><img src='../images/" . $admininfo["language"] . "/btc_modify.gif' border=0></a> ";

                $Contents02 .= "<a onclick=\"PoPWindow('./translation_pop.php?mode=copy&trans_div=" . $_GET["trans_div"] . "&trans_type=" . $_GET["trans_type"] . "&search_text=" . $_GET["search_text"] . "&trans_ix=" . $trans_datas[$i]['trans_ix'] . "','1000','500','trans_pop')\" style='cursor:pointer'>복사</a> ";

                //$Contents02 .= "<a href=\"javascript:updateTranslationInfo('".$trans_datas[$i]['trans_ix']."','".$trans_datas[$i]['trans_type']."','".$trans_datas[$i]['trans_div']."','".$trans_datas[$i]['text_korea']."','".$trans_datas[$i]['text_english']."','".$trans_datas[$i]['disp']."')\"><img src='../image/btc_modify.gif' border=0></a>";
            } else {
                $Contents02 .= "<a href=\"javascript:alert('수정권한이 없습니다.');\"><img src='../images/" . $admininfo["language"] . "/btc_modify.gif' border=0></a> ";
            }

            if (checkMenuAuth(md5($_SERVER["PHP_SELF"]), "D")) {
                $Contents02 .= "<a href=\"javascript:deleteTranslationInfo('delete','" . $trans_datas[$i]['trans_ix'] . "')\"><img src='../images/" . $admininfo["language"] . "/btc_del.gif' border=0></a>";
            } else {
                $Contents02 .= "<a href=\"javascript:alert('삭제권한이 없습니다.')\"><img src='../images/" . $admininfo["language"] . "/btc_del.gif' border=0></a>";
            }
            $Contents02 .= "
				</td>
			  </tr> ";
        }
    } else {
        $Contents02 .= "
			  <tr bgcolor=#ffffff height=50>
				<td align=center colspan=9>등록된 랭귀지 목록이 없습니다. </td>
			  </tr> ";
    }
    $Contents02 .= "</table>";
}

$Contents02 .= "
<table width='100%' cellpadding=0 cellspacing=0 border='0'  >
<tr>
	<td align=left style='padding:10px;'>
		" . $str_page_bar . "
	</td>
</tr>
</table>
";


$Contents = "<table width='100%' border=0>";

$Contents = $Contents . "<tr><td>" . $Contents01 . "</td></tr>";
$Contents = $Contents . "<tr><td>" . $ContentsDesc01 . "</td></tr>";
$Contents = $Contents . "<tr><td>" . $ButtonString . "</td></tr>";

$Contents = $Contents . "<tr><td>" . $Contents02 . "</td></tr>";
$Contents = $Contents . "<tr height=30><td></td></tr>";

$Contents = $Contents . "</table >";
$Contents = $Contents . "<form name='translation_list_frm' action='translation.act.php' method='post' onsubmit='return CheckFormValue(this)' style='display:inline;' target='act'><input name='act' type='hidden' value='" . $act . "'><input name='trans_ix' type='hidden' value='$trans_ix'>";
$Contents = $Contents . "</form>";


$Script = "
 <script language='javascript'>
 function shortContents(){
	var warn = confirm('사이트내에서 사용중인 치환코드의 경로/내용을 저장하는 기능입니다.\\n데이터 저장은 최대 30분 이상 소요됩니다.\\n(저장하는 시간동안 사이트 접속이 원활하지 않을 수 있습니다.)');
	if(warn == true){
		location.href='translation_sort_contents.php';
	}
 }

 function updateTranslationInfo(trans_ix,trans_type,trans_div,text_korea, text_english ,disp){
 	var frm = document.translation_list_frm;

 	frm.act.value = 'update';
 	frm.trans_ix.value = trans_ix;
 	frm.trans_type.value = trans_type;
 	frm.trans_div.value = trans_div;
 	frm.text_korea.value = text_korea;
	frm.text_english.value = text_english;
 	if(disp == '1'){
 		frm.disp['0'].checked = true;
 	}else{
 		frm.disp['1'].checked = true;
 	}
	//frm.bank_name.focus();

}

 function deleteTranslationInfo(act, trans_ix){
 	if(confirm('해당랭귀지 목록을 정말로 삭제하시겠습니까?')){
 		var frm = document.translation_list_frm;
 		frm.act.value = act;
 		frm.trans_ix.value = trans_ix;
 		frm.submit();
 	}
}
function etcBank(etc){
	if(etc == 'etc'){
		document.getElementById('etc').disabled = false;
	}else{
		document.getElementById('etc').disabled = true;
	}
}

function copyToClipboard(text)
{
    if (window.clipboardData) // Internet Explorer
    {  
        window.clipboardData.setData('Text', text);
    }
    else
    {  
        unsafeWindow.netscape.security.PrivilegeManager.enablePrivilege(\"UniversalXPConnect\");  
        const clipboardHelper = Components.classes[\"@mozilla.org/widget/clipboardhelper;1\"].getService(Components.interfaces.nsIClipboardHelper);  
        clipboardHelper.copyString(text);
    }
}

function copyLanguageFunction(key, text)
{
	var copy_text = '__LANGUAGE(\"'+text+'\");//'+key;
	copyToClipboard(copy_text);
}
$(document).ready(function() {
	$('.trans_text').keyup(function(evt){
		//alert(evt.keyCode);
		var selected_obj = $(this);
		//alert($(this).attr('trans_ix'));
		$.ajax({ 
			type: 'GET', 
			data: {'act': 'trans_text_reg', 'trans_type':selected_obj.attr('trans_type'), 'trans_key':selected_obj.attr('trans_key'), 'trans_ix':selected_obj.attr('trans_ix') , 'trans_text':$(this).val()},
			url: './translation.act.php',  
			dataType: 'text', 
			async: false, 
			beforeSend: function(){ 
					//alert(selected_obj.attr('trans_ix'));
					selected_obj.closest('ul').find('#loading_img').css('display','');
					//alert(selected_obj.closest('ul').html());//find('#loading_img').
			},  
			success: function(result){ 
				//alert(result);
				selected_obj.closest('ul').find('#loading_img').css('display','none');
			}
		});
	});
 

});

 </script>
 ";


$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = global_menu();
$P->Navigation = "상점관리 > 다국어지원 > 번역관리";
$P->title = "번역관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();


/*

CREATE TABLE IF NOT EXISTS `global_translation` (
  `trans_ix` int(4) unsigned NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `trans_div` varchar(255) DEFAULT NULL COMMENT '문자열구분',
  `trans_type` varchar(20) DEFAULT 'english' COMMENT '언어',
  `text_name` varchar(255) DEFAULT NULL COMMENT '문자열 명칭',
  `text_korea` mediumtext COMMENT '한글 문자열',
  `trans_text` mediumtext COMMENT '번역한 문자열',
  `disp` char(1) DEFAULT '1' COMMENT '사용여부',
  `regdate` datetime NOT NULL COMMENT '등록일',
  PRIMARY KEY (`trans_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='프론트 번역 사전'



CREATE TABLE IF NOT EXISTS `global_translation_use_history` (
  `trans_use_ix` int(4) unsigned NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `trans_key` varchar(50) DEFAULT NULL COMMENT '문자열구분',
  `file_name` varchar(255) DEFAULT 'english' COMMENT '언어',
  `file_path` varchar(255) DEFAULT NULL COMMENT '문자열 명칭',
  `regdate` datetime NOT NULL COMMENT '등록일',
  PRIMARY KEY (`trans_use_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='랭귀지팩 사용 내역'
*/
?>