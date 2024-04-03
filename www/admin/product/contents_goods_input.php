<?php
	include("../class/layout.class");
	$db = new Database;
	$db2 = new Database;

	if ($ci_ix != ""){

		$sql = "SELECT 	* 	FROM contents_info ci where 	ci.ci_ix = '".$ci_ix."' ";
		$db->query($sql);
		if($db->total){
			$db->fetch();
			$title_img_path = $admin_config[mall_data_root]."/images/contents/";
			$act = "update";
			$sql = "select * from contents_data where ci_ix = '".$ci_ix."' ";
			$db2->query($sql);
			$up_data = $db2->fetchall();
		}else{
			$act = "insert";
		}
	}else{
		$act = "insert";
	}




	$c_file_type = array(); 
	if($act == "update"){
		$c_file_type = unserialize($db->dt[c_file_type]);
	}
	$Contents = "
		<table cellpadding=0 width=100%>
			<tr>
				<td align='left' colspan=4 > ".GetTitleNavigation("단위 컨텐츠 등록/수정", "상품관리 > 컨텐츠 상품관리 > 단위 컨텐츠 등록/수정")."</td>
			</tr>
		</table>";


	// 고정시켜야 하는 항목들 아래에서 지정함.

	$db->dt[c_type] = 'video';
	$c_file_type    = array('mp4');
	$db->dt[up_file_type] = 'U';

	$Contents .= "
		<form name='contents' action='./contents_goods_input.act.php' method='post' enctype='multipart/form-data' onsubmit='return SubmitX(this);' target='iframe_act'><!-- target='iframe_act'-->
		<input type='hidden' name=act value='".$act."'>
		<input type='hidden' name=b_up_file_type value='".$db->dt[up_file_type]."'>
		<input type='hidden' name=ci_ix value='".$ci_ix."'>
		
		<table width='100%' cellpadding=0 cellspacing=0>
			<tr height=30>
				<td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<table cellpadding=0 width=100%>
			</tr>
			<tr>
				<td style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle style='position:relative;'><b class=blk> 기본정보 </b><span class=small></td></tr></table>")."</td>
			</tr>
		</table>
		<table cellpadding=0 cellspacing=0 width='100%' class='input_table_box'>
			<col width=15%>
			<col width=35%>
			<col width=15%>
			<col width=35%>
			<tr>
				<td class='input_box_title' nowrap> <b>단위 컨텐츠 명</b> <img src='".$required3_path."'></td>
				<td class='input_box_item'>
					<input type=text class='textbox' name=c_name size=28 style='width:90%' value='".str_replace("'","&#39;",trim($db->dt[c_name]))."' validation=true title='단위 컨텐츠 명'>
				</td>
				<td class='input_box_title'> 사용여부 <img src='".$required3_path."'></td>
				<td class='input_box_item' style='line-height:150%'>
					<input type=radio name='state' id='state_1'  value='1' ".($db->dt[state] == "1" || $db->dt[state] == "" ? "checked":"")." ><label for='state_1'> 사용</label>
					<input type=radio name='state' id='state_0'  value='0' ".($db->dt[state] == "0"  ? "checked":"")."><label for='state_0'> 사용하지 않음</label>
				</td>
			</tr>
			<tr style='display:none'>
				<td class='input_box_title' nowrap> <b>컨텐츠 타입</b> <img src='".$required3_path."'></td>
				<td class='input_box_item'>
					<input type=radio name='c_type' class='c_type' id='c_type_document'  value='document' ".($db->dt[c_type] == "document" || $db->dt[c_type] == "" ? "checked":"")." ><label for='c_type_document'> 문서</label>
					<input type=radio name='c_type' class='c_type' id='c_type_image'  value='image' ".($db->dt[c_type] == "image"  ? "checked":"")."><label for='c_type_image'> 이미지</label>
					<input type=radio name='c_type' class='c_type' id='c_type_music'  value='music' ".($db->dt[c_type] == "music"  ? "checked":"")." ><label for='c_type_music'> 음원</label>
					<input type=radio name='c_type' class='c_type' id='c_type_video'  value='video' ".($db->dt[c_type] == "video"  ? "checked":"")."><label for='c_type_video'> 영상</label>
				</td>
				<td class='input_box_title'> 셀러 구분 <img src='".$required3_path."'></td>
				<td class='input_box_item' style='line-height:150%'>
					<input type=radio name='company_type' id='company_type_a'  value='a' ".($db->dt[company_id] ==  $_SESSION['admininfo']['company_id'] || $db->dt[company_id] == "" ? "checked":"")." ><label for='company_type_a'> 본사품목</label>
					<input type=radio name='company_type' id='company_type_b'  value='b' ".($db->dt[company_id] !=  $_SESSION['admininfo']['company_id'] &&  $db->dt[company_id] != "" ? "checked":"")."><label for='company_type_b'> 위탁품목</label>
					<div style='float:left;'>".companyAuthList($db->dt[company_id] , "title='위탁업체' ",'company_id','company_id','com_name','input')."</div>
				</td>
			</tr>
			<tr style='display:none'>
				<td class='input_box_title' nowrap> <b>파일 타입</b> <img src='".$required3_path."'></td>
				<td class='input_box_item'>
					<div class='c_file_type document' ".($db->dt[c_type] == "document" || $db->dt[c_type] == "" ? "":"style='display:none;'").">
						<input type=checkbox name='c_file_type[]' class='document' id='c_file_type_doc' value='DOC' ".(in_array( "DOC" ,$c_file_type) ? "checked":"")."  title='파일 타입'><label for='c_file_type_doc'> DOC</label>
						<input type=checkbox name='c_file_type[]' class='document' id='c_file_type_hwp' value='HWP' ".(in_array("HWP" ,$c_file_type ) ? "checked":"")."  title='파일 타입'><label for='c_file_type_hwp'> HWP</label>
						<input type=checkbox name='c_file_type[]' class='document' id='c_file_type_ppt' value='PPT' ".(in_array( "PPT" ,$c_file_type ) ? "checked":"")."  title='파일 타입'><label for='c_file_type_ppt'> PPT</label>
						<input type=checkbox name='c_file_type[]' class='document' id='c_file_type_pdf' value='PDF' ".(in_array( "PDF" ,$c_file_type ) ? "checked":"")."  title='파일 타입'><label for='c_file_type_pdf'> PDF</label>
					</div>
					<div class='c_file_type image' ".($db->dt[c_type] == "image"  ? "":"style='display:none;'").">
						<input type=checkbox name='c_file_type[]' class='image' id='c_file_type_jpg' value='jpg' ".(in_array( "jpg" ,$c_file_type) ? "checked":"")."  title='파일 타입'><label for='c_file_type_jpg' > jpg</label>
						<input type=checkbox name='c_file_type[]' class='image' id='c_file_type_png' value='png' ".(in_array("png" ,$c_file_type ) ? "checked":"")."  title='파일 타입'><label for='c_file_type_png'> png</label>
						<input type=checkbox name='c_file_type[]' class='image' id='c_file_type_gif' value='gif' ".(in_array( "gif" ,$c_file_type ) ? "checked":"")."  title='파일 타입'><label for='c_file_type_gif'> gif</label>
						<input type=checkbox name='c_file_type[]' class='image' id='c_file_type_eps' value='eps' ".(in_array( "eps" ,$c_file_type ) ? "checked":"")."  title='파일 타입'><label for='c_file_type_eps'> eps</label>
					</div>
					<div class='c_file_type music' ".($db->dt[c_type] == "music"  ? "":"style='display:none;'").">
						<input type=checkbox name='c_file_type[]' class='music' id='c_file_type_mp3' value='mp3' ".(in_array( "mp3" ,$c_file_type) ? "checked":"")."  title='파일 타입'><label for='c_file_type_mp3'> mp3</label>
						<input type=checkbox name='c_file_type[]' class='music' id='c_file_type_wav' value='wav' ".(in_array("wav" ,$c_file_type ) ? "checked":"")."  title='파일 타입'><label for='c_file_type_wav'> wav</label>
					</div>	
					<div class='c_file_type video' ".($db->dt[c_type] == "video"  ? "":"style='display:none;'").">
						<input type=checkbox name='c_file_type[]' class='video' id='c_file_type_mp4' value='mp4' ".(in_array( "mp4" ,$c_file_type) ? "checked":"")."  title='파일 타입'><label for='c_file_type_mp4'> mp4</label>
						<input type=checkbox name='c_file_type[]' class='video' id='c_file_type_avi' value='avi' ".(in_array("avi" ,$c_file_type ) ? "checked":"")."  title='파일 타입'><label for='c_file_type_avi'> avi</label>
					</div>
				</td>
				<td class='input_box_title'> 연령대 </td>
				<td class='input_box_item' style='line-height:150%'>
					<input type=text class='textbox' name=age size=28 style='width:90%' value='".str_replace("'","&#39;",trim($db->dt[age]))."' title='연령대'>
				</td>
			</tr>
			<tr style='display:none'>
				<td class='input_box_title' nowrap> <b>이용기간</b><img src='".$required3_path."'></td>
				<td class='input_box_item' >
					구매일로부터 <input type=text class='textbox' name=end_day  style='width:30px;'  value='".str_replace("'","&#39;",trim($db->dt[end_day]))."' title='이용기간'> 일
				</td>
				<td class='input_box_title'><b>타이틀이미지</b></td>
				<td class='input_box_item' style='line-height:150%'>
					<input type='file' name='title_img'  style='border:1px solid #cccccc; vertical-align:middle;' />";
					if($db->dt[title_img]){
						$Contents .= "
					<div style='float:left; vertical-align:middle; padding: 3px 0;' >
						<img src='".$title_img_path."/".$db->dt[ci_ix]."/".$db->dt[title_img]."'  style='max-width:50px;'/>				
					</div>";
					}
					$Contents .= "
				</td>
			</tr>
			<tr>
				<td class='input_box_title' nowrap> <b>상단전시</b></td>
				<td class='input_box_item' colspan=3>
					<input type=checkbox name='displayTopYN' id='displayTopYN' class='image' value='Y' title='전시여부' " . ($db->dt[displaytopyn] == "Y" ? "checked" : "") . " ><label for='displayTopYN' > 전시 </label>
				</td>
			</tr>
			<tr>
				<td class='input_box_title' nowrap> <b>관련링크</b><img src='".$required3_path."'></td>
				<td class='input_box_item' colspan=3>
					<input type=text class='textbox' name=targetUrl  style='width:964px;' validation=true title='관련링크' value='" . $db->dt[targeturl] . "'> 
				</td>
			</tr>
			<tr class='c_file_type music' ".($db->dt[c_type] == "music"  ? "":"style='display:none;'").">
				<td class='input_box_title' nowrap> <b>작사/작곡</b></td>
				<td class='input_box_item' colspan=3>
					<input type=text class='textbox' name='composer'  style='width:200px;'  value='".str_replace("'","&#39;",trim($db->dt[composer]))."'  title='이용기간'> 
				</td>
			</tr>
		</table><br>";
	$Contents .="
		<table width='100%' cellpadding=0 cellspacing=0 style='display:none'>
			<tr height=30>
				<td style='padding-bottom:10px;'>
						".colorCirCleBox("#efefef","100%","<table cellpadding=0 width=100%><tr><td style='padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle style='position:relative;'><b class=blk> 가격정보  </b></td></tr></table>")."
				</td>
			</tr>
		</table>
		<table cellpadding=0 cellspacing=0 width='100%' class='list_table_box' id='unit_table' style='display:none'>
			<col width=15%>
			<col width=35%>
			<col width=15%>
			<col width=35%>
			<tr>
				<td class='input_box_title' nowrap> <b>공급가</b> <img src='".$required3_path."'></td>
				<td class='input_box_item' >
					<input type=text class='textbox' name=c_coprice  style='width:150px;'  value='".$db->dt[c_coprice]."' title='공급가'>
				</td>
				<td class='input_box_title'>판매가 <img src='".$required3_path."'> </td>
				<td class='input_box_item' style='line-height:150%'>
					<input type=text class='textbox' name=c_sellprice  style='width:150px;'  value='".$db->dt[c_sellprice]."' title='판매가'>	
				</td>
			</tr>

		</table>	<br>";

	$Contents .="
		<table width='100%' cellpadding=0 cellspacing=0>
			<tr height=30>
				<td style='padding-bottom:10px;'>
						".colorCirCleBox("#efefef","100%","<table cellpadding=0 width=100%><tr><td style='padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle style='position:relative;'><b class=blk> 컨텐츠 업로드  </b></td></tr></table>")."
				</td>
			</tr>
		</table>
		<table cellpadding=0 cellspacing=0 width='100%' class='list_table_box' id='unit_table'>
			<col width=15%>
			<col width=35%>
			<col width=15%>
			<col width=35%>
			<tr style='display:none'>
				<td class='input_box_title' nowrap> <b>업로드 타입</b> <img src='".$required3_path."'></td>
				<td class='input_box_item' colspan='3'>
					<input type=radio name='up_file_type' class='up_file_type' id='up_file_type_f'  value='F' ".($db->dt[up_file_type] == "F" || $db->dt[up_file_type] == "" ? "checked":"")." ><label for='up_file_type_f'> 파일 업로드</label>
					<input type=radio name='up_file_type' class='up_file_type' id='up_file_type_u'  value='U' ".($db->dt[up_file_type] == "U"  ? "checked":"")."><label for='up_file_type_u'> URL 링크</label>
					<input type=radio name='up_file_type' class='up_file_type' id='up_file_type_s'  value='S' ".($db->dt[up_file_type] == "S"  ? "checked":"")." ><label for='up_file_type_s'> 소스코드</label>
				</td>
			</tr>
			<tr height=70px class='type_file' ".($db->dt[up_file_type] == "F" || $db->dt[up_file_type] == "" ? "":"style='display:none;'").">
				
				<td class='input_box_title' nowrap> <b>파일업로드</b> <img src='".$required3_path."'></td>
				<td class='input_box_item' colspan='3'>
					<input type=file name='up_file' id='up_file' style='border:1px solid #cccccc;' > <input type='button' value='저장' onclick=\"tmp_file_upload();\" />   <input type='button' value='임시파일삭제' onclick=\"delete_tmp_all();\" /> <span style='color:blue; size:10px;'>* 선택한 파일을 임시 저장 합니다. (임시 저장된 파일이 최종 등록 됩니다.)</span>
					
					<div style='padding:8px;0;' class='file_area'>
						<input type='hidden' id='file_area_num' value='0' />
						";
						if($act == "insert"){
						$Contents .="";
						}else{
							for($i=0; $i < count($up_data); $i++){
								if($up_data[$i][up_file_type] == 'F'){
								$Contents .="
								<div class='floatL file_data_".$up_data[$i][cd_ix]."'>
									<span class='vm Pgap_L10'>".$up_data[$i][data_info]."</span>
									<img src='../images/btn_x.gif' class='vm' style='cursor:pointer;margin:0px 3px;' onclick=\"delete_data_img('file_data_".$up_data[$i][cd_ix]."','".$up_data[$i][data_info]."','".$ci_ix."')\" >
								</div> ";
								}
							}
						}
						$Contents .="
					</div>
				</td>

			</tr>
			<tr class='type_url' ".($db->dt[up_file_type] == "U"  ? "":"style='display:none;'").">
				<td class='input_box_title' nowrap> <b>URL 링크</b> <img src='".$required3_path."'></td>
				<td class='input_box_item' colspan='3'>";
				if($db->dt[up_file_type] == "U"){
					$Contents .="
					<input type='hidden' name = 'cd_ix' value='".$up_data[0][cd_ix]."' />";
				}
				$Contents .="
					<textarea name='data_info' class='data_info_url' style='width:90%' title='URL 링크' ".($db->dt[up_file_type] == "U"  ? "":"disabled")." validation='true'>".($up_data[0][up_file_type] == 'U' ? $up_data[0][data_info] : "" )."</textarea>
				</td>
			</tr>	
			<tr height=70px class='type_sauce'".($db->dt[up_file_type] == "S"  ? "":"style='display:none;'").">
				<td class='input_box_title' nowrap> <b>소스코드</b> <img src='".$required3_path."'></td>
				<td class='input_box_item' colspan='3'>";
				if($db->dt[up_file_type] == "S"){
					$Contents .="
					<input type='hidden' name = 'cd_ix' value='".$up_data[0][cd_ix]."' />";
				}
				$Contents .="
					<textarea name='data_info' class='data_info_sauce'  style='width:90%' title='소스코드' ".($db->dt[up_file_type] == "S"  ? "":"disabled").">".($up_data[0][up_file_type] == 'S' ? $up_data[0][data_info] : "" )."</textarea>
				</td>
			</tr>	
		</table>	<br>";


	$Contents .="
		<table width='100%' cellpadding=0 cellspacing=0 style='display:none'>
			<tr height=30>
				<td style='padding-bottom:10px;'>
						".colorCirCleBox("#efefef","100%","<table cellpadding=0 width=100%><tr><td style='padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle style='position:relative;'><b class=blk> 변경 히스토리 </b></td></tr></table>")."
				</td>
			</tr>
		</table>
		<table cellpadding=0 cellspacing=0 width='100%' class='list_table_box' id='unit_table' style='display:none'>
			<col width=15%>
			<col width=35%>
			<col width=15%>
			<col width=35%>
			<tr>
				<td class='input_box_title' nowrap> <b>변경히스토리</b></td>
				<td class='input_box_item' colspan='3'>
					<div style='overflow:auto; width:98%; height:100px; padding:10px;'>
						".history_data($ci_ix)."
					</div>
				</td>
			</tr>
		</table>
		<table border=0 cellpadding=0 cellspacing=0 width='100%'>
		<tr>
			<td height='50' colspan=2 align=center style='padding:10px 0px'>
				<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle>
			</td>
		</table>
		</form>
		";

	$help_text = "
	<table cellpadding=2 cellspacing=0 class='small' >
		<col width=8>
		<col width=*>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td > 설명1 </td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td > 설명2  </td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td > 설명3 </td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td > 설명4  </td></tr>
	</table>
	";


	$help_text = HelpBox("단위 컨텐츠 등록/수정", $help_text);
	$Contents .= $help_text;

	$Script = "
	<script type='text/javascript' src='./contents_goods_input.js'></script>

	";


		$P = new LayOut();
		$P->addScript = $Script;
		$P->strLeftMenu = product_menu();
		$P->Navigation = "상품관리 > 컨텐츠 상품 관리 > 단위 컨텐츠 등록/수정";
		$P->title = "단위 컨텐츠 등록/수정";
		$P->strContents = $Contents;
		echo $P->PrintLayOut();

	function history_data($ci_ix){
		$db = new database;
		if(!$ci_ix){
			return false;
		}
		
		$sql = "select * from contents_history where ci_ix  = '".$ci_ix."' ";
		$db->query($sql);
		$html = "";
		if($db->total){
			for($i=0; $i < $db->total; $i++){
				$db->fetch($i);
				if($db->dt[column_name] == "c_file_type"){
					$db->dt[b_data]  =  implode('|',unserialize($db->dt[b_data]));
					$db->dt[after_data]  =  implode('|',unserialize($db->dt[after_data]));
				}
				$html .= "<div>[수정자] : ".$db->dt[charger_name]." [수정항목] : ".$db->dt[column_text]."  [이전데이터] : ".$db->dt[b_data]." [변경데이터] : ".$db->dt[after_data]." [수정일자] : ".$db->dt[regdate]." </div>";
			}

		}
		return $html;
	}
