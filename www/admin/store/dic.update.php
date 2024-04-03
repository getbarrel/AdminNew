<?

//echo $this_menu_code.":::".md5(str_replace("/promotion/","/display/",$_SERVER["PHP_SELF"]));
if($this_menu_code && $before_menu_code){
	//exit;
	$sql = "SELECT * FROM admin_dic where menu_div = 'display' and menu_code = '".$before_menu_code."' order by dic_code asc, regdate desc   ";
	//echo $sql;
	$db->query($sql);
	if($db->total){
	$Contents = "
		<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
		  <tr>
			<td align='left' colspan=8 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b>사전 목록</b></div>")."</td>
		  </tr>
		  </table>
		<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='list_table_box'>
			<col style='width:70px;'>
			<col style='width:70px;'>
			<col style='width:80px;'>
			<col style='width:160px;'>
			<col style='width:*;'>
			<col style='width:100px;'>
			<col style='width:130px;'>
			<col style='width:100px;'>
		  <tr height=30 bgcolor=#efefef align=center style='font-weight:bold'>
			<td class='s_td'> 메뉴구분</td>
			<td class='m_td'> 사전구분</td>
			<td class='m_td'> 언어구분</td>
			<td class='m_td'> 한글문구</td>
			<td class='m_td'> 번역문구</td>
			<td class='m_td'> 사용유무</td>
			<td class='m_td'> 등록일자</td>
			<td class='e_td'> 관리</td>
		  </tr>";

		$datas = $db->fetchall();
		for($i=0;$i < count($datas);$i++){
			//$db->fetch($i);
			$sql = "update admin_dic set menu_div = 'promotion' , menu_code = '".$this_menu_code."'  where menu_code = '".$before_menu_code."' and dic_ix ='".$datas[$i][dic_ix]."'   ";
			//echo $sql."<br>";
			$db->query($sql);

			$Contents .= "
			  <tr bgcolor=#ffffff height=30 align=center>
				<td class='list_box_td list_bg_gray'>".$datas[$i][menu_div]." ".$datas[$i][menu_code]."</td>
				<td class='list_box_td '>".$datas[$i][dic_type]."</td>
				<td class='list_box_td list_bg_gray'>".$datas[$i][language_type]."</td>
				<td class='list_box_td point' style='padding:5px;'>".($datas[$i][dic_type] == "WORD" ? $datas[$i][text_korea]:"")."</td>
				<td class='list_box_td list_bg_gray' style='padding:5px;'>".($datas[$i][dic_type] == "WORD" ? $datas[$i][text_trans]:"")."</td>

				<td class='list_box_td '>".($datas[$i][disp] == "1" ?  "사용":"사용하지않음")."</td>
				<td class='list_box_td list_bg_gray'>".$datas[$i][regdate]."</td>
				<td class='list_box_td '>";
				if(checkMenuAuth($this_menu_code,"U")){
					//$Contents .= "<a href=\"javascript:updateLanguageInfo('".$datas[$i][dic_ix]."','".$datas[$i][text_div]."','".$datas[$i][text_korea]."','".$datas[$i][text_english]."','".$datas[$i][disp]."')\"><img src='../image/btc_modify.gif' border=0></a>";
					$Contents .= "<a href=\"?dic_ix=".$datas[$i][dic_ix]."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a> ";
				}else{
					$Contents .= "<a href=\"javascript:alert('수정권한이 없습니다.');\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a> ";
				}

				if(checkMenuAuth($this_menu_code,"D")){
					$Contents .= "<a href=\"javascript:deleteDicInfo('delete','".$datas[$i][dic_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
				}else{
					$Contents .= "<a href=\"javascript:alert('삭제권한이 없습니다.')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
				}
				$Contents .= "
				</td>
			  </tr>";
		}

		$Contents .= "</table>";
	}

}
?>