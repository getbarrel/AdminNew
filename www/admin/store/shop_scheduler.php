<?
include("../class/layout.class");
include_once("../sellertool/sellertool.lib.php");

if($admininfo[admin_level] < 9){
	header("Location:../admin.php");
}
//print_r($_SESSION["admin_config"]);
$db = new Database;


$sql = "SHOW TABLES LIKE 'shop_schedule_setting'";
$db->query($sql);
if(!$db->total){
	$sql="CREATE TABLE shop_schedule_setting (
			`ss_ix`  int(10) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT COMMENT '쇼핑몰 스케줄 인덱스' ,
			`type`  varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '스케줄 등록 타입 상품: product, 주문:order, 제휴사:sellertool' ,
			`file_type` enum('select','input') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '등록타입' ,
			`action_type` enum('web','shell','self') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '동작위치' ,
			`auto_type` enum('Y','N') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '자동스케줄등록여부 Y , 정보등록 N' ,
			`file`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '제휴사파일위치' ,
			`month`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '월지정 0 : 매월' ,
			`weekday`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '요일지정 0:매요일' ,
			`day`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '일 지정 0:매일' ,
			`hour`  varchar(50) NULL COMMENT '시간지정 0 : 매시간' ,
			`minute`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '분지정 00 : 매분' ,
			schedul_time  text(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '스케줄시간 수동' ,
			`site_code`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '제휴사코드' ,
			`comment`  mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '작업설명',
			`update_date`  datetime NULL COMMENT '수정일자' ,
			`regdate`  datetime NULL COMMENT '등록일자' ,
			PRIMARY KEY (`ss_ix`)
			);
	";
	$db->query($sql);
}

if($page_type == "sellertool"){
	$titel_name = "제휴사";
	$schedule_type = 'sellertool';
}else{
	$titel_name = "쇼핑몰";
	$schedule_type = $_GET['schedule_type'];
	if(empty($schedule_type)){
		$schedule_type = 'product';
	}
}

$week_day = array("SUN" => "일", "MON" => "월", "TUE" => "화", "WED" => "수", "THU" => "목", "FRI" => "토", "SAT" => "일");

if($page_type != "sellertool"){
$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr >
			<td align='left' colspan=4> ".GetTitleNavigation("시스템관리", "".$titel_name." 환경설정 > ".$titel_name." 스케줄설정 ")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=2 style='padding-bottom:15px;'>
	    <div class='tab'>
				<table class='s_org_tab'>
				<tr>
					<td class='tab'>";
			
			$Contents01 .=	"
						
						<table id='tab_02' ".($schedule_type == "product" ||  $schedule_type == ""  ? "class='on'":"")." >
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='?schedule_type=product'\">상품관련</td>
							<th class='box_03'></th>
						</tr>
						</table>
						<table id='tab_03' ".($schedule_type == "order" ? "class='on'":"")." >
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='?schedule_type=order'\">주문관련</td>
							<th class='box_03'></th>
						</tr>
						</table>
						<table id='tab_04' ".($schedule_type == "sellertool" ? "class='on'":"")." >
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='?schedule_type=sellertool'\">제휴사 관련</td>
							<th class='box_03'></th>
						</tr>
						</table>
						<table id='tab_04' ".($schedule_type == "etc" ? "class='on'":"")." >
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='?schedule_type=etc'\">기타</td>
							<th class='box_03'></th>
						</tr>
						</table>";
			
			$Contents01 .=	"
					</td>
					<td style='vertical-align:bottom;padding:0px 0px 10px 4px;'>";

$Contents01 .= "
					</td>
				</tr>
				</table>
			</div>
	    </td>
	</tr>
	 
	</table>";
}
$Contents01 .= "
	<form name='schedule_form' id='schedule_form' method='POST' action='/admin/store/shop_scheduler.act.php' onsubmit='return CheckFormValue(this);' style='display:inline;'  target='iframe_act'>
	<input type='hidden' name='act' id='act' value='insert'>
	<input type='hidden' name='type' id='type' value='".$schedule_type."'>
	<input type='hidden' name='ss_ix' id='ss_ix' value='".$ss_ix."'>
	
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >
	<tr>
		<td colspan=2>
			
			<table cellpadding=0 cellspacing=0 border=0 width=100% align='center' class='input_table_box'>
				<col width='20%' />
				<col width='30%' />
				<col width='20%' />
				<col width='30%' />
				<tr>
					<td class='input_box_title'>등록타입</td>
					<td class='input_box_item' colspan=3>
						<input type='radio' name='file_type' id='file_type1' value='input' checked><label for='file_type1'>직접등록</label>
						<input type='radio' name='file_type' id='file_type2' value='select' ><label for='file_type2'>선택등록</label>
					</td>
				</tr>
				<tr>
					<td class='input_box_title'>Auto 스케줄 설정</td>
					<td class='input_box_item' colspan=3>
						<input type='radio' name='auto_type' id='auto_type_y' value='Y' checked><label for='auto_type_y'>스케줄등록</label>
						<input type='radio' name='auto_type' id='auto_type_n' value='N' ><label for='auto_type_n'>정보만등록</label>
					</td>
				</tr>
				<tr>
					<td class='input_box_title'>동작위치</td>
					<td class='input_box_item' colspan=3>
						<input type='radio' name='action_type' id='action_type_web' value='web' checked><label for='action_type_web'>Web</label>
						<input type='radio' name='action_type' id='action_type_shell' value='shell' ><label for='action_type_shell'>Shell(동작테스트중)</label>
						<input type='radio' name='action_type' id='action_type_self' value='self' ><label for='action_type_self'>직접입력(shell 명령어)</label>
					</td>
				</tr>
				<tr class='file_type_input'>
					<td class='input_box_title'>스케줄 작업파일 위치등록</td>
					<td class='input_box_item' colspan=3>
						<input type='text' name='scheduler_file_name_input' id='scheduler_file_name_input' value='".$_SERVER["DOCUMENT_ROOT"]."' style='width:40%' >
						<span style='font-size:11px; color:blue;'>파일위치가 DOCUMENT_ROOT 하위가 아닌 경우 기본 경로를 제거 후 풀 경로 및 파일명을 입력 해 주세요.</span>
					</td>
				</tr>
				<tr class='file_type_select' style='display:none;'>
					<td class='input_box_title'>스케줄 작업파일 선택</td>
					<td class='input_box_item' colspan=3>
						<table border=0 cellpadding=0 cellspacing=0>
							<tr>
								<td style='padding-right:5px;'>";
								$file_path = $_SERVER['DOCUMENT_ROOT']."/cron";
								$scheduler_file_array = filesInDir($file_path);
								$Contents01 .= "
									<select name='scheduler_file_name' id = 'scheduler_file_name'>
										<option value=''>파일을 선택해 주세요</option>";
										if(is_array($scheduler_file_array)){
											foreach($scheduler_file_array as $val){
												$file_name = explode('cron/',$val);
												$Contents01 .= "
												<option value='".$val."'>".$file_name[1]."</option>";
											}
										}
								$Contents01 .= "
									</select>	
								</td>
							</tr>
						</table>
					</td>
				</tr>
				
				<tr>
					<td class='input_box_title'>스케줄 작업 설명</td>
					<td class='input_box_item' colspan=3>
						<table border=0 cellpadding=0 cellspacing=0>
							<tr>
								<td style='padding-right:5px;'>
									<textarea name='comment' id='comment' rows='4' cols='80'></textarea>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				";
				if($schedule_type == 'sellertool'){
				$Contents01 .= "
				<tr>
					<td class='input_box_title'>제휴사 선택</td>
					<td class='input_box_item' colspan=3>
						<table border=0 cellpadding=0 cellspacing=0>
							<tr>
								<td style='padding-right:5px;'>
									".getSellerToolSiteInfo($site_code)."
								</td>
							</tr>
						</table>
					</td>
				</tr>";
				}
				$Contents01 .= "
				<tr>
					<td class='input_box_title'>  스케줄 시간 등록 타입  </td>
					<td class='input_box_item' colspan=3>
						<input type='radio' name='time_option' id='time_option_basic' value='basic' checked><label for='time_option_basic'>간편등록</label>
						<input type='radio' name='time_option' id='time_option_self' value='self' ><label for='time_option_self'>직접입력</label>
					</td>
				</tr>
				";
				$Contents01 .= "
				<tr class='time_option_basic'>
					<td class='input_box_title'>  스케줄 시간설정  </td>
					<td class='input_box_item' colspan=3 style='padding:5px;' >
					<table cellpadding=3>
						
						<tr>
							<td>
							<select name='minute'  id='minute'  style=\"font-size:12px;width:90px;\" >
								";
								for($i=0; $i < 60;$i++){
								$Contents01 .= "<option value='".($i)."' ".( $set_cron_sellertool[cron_minutes] == $i ? 'selected' : '' ).">".($i)." 분</option>";
								}
								$Contents01 .= "
							</select>
							</td>
							<td>
							<select name='hour' id='hour' style=\"font-size:12px;width:90px;\" >
								<!--option value='00'>00</option-->";
								for($i=0; $i < 24;$i++){
								$Contents01 .= "<option value='".$i."' ".( $set_cron_sellertool[cron_hours] == $i ? 'selected' : '' ).">".$i." 시</option>";
								}
								$Contents01 .= "
							</select>
							</td>
							<td>
							<select name='day' id='day' style=\"font-size:12px;width:90px;\"  >
								<option value='0'>매일</option>";
								for($i=1; $i < 32;$i++){
								$Contents01 .= "<option value='".$i."' ".( $set_cron_sellertool[cron_days] == $i ? 'selected' : '' ).">".$i." 일</option>";
								}
								$Contents01 .= "
							</select>
							</td>
							<td>
							<select name='month' id='month' style=\"font-size:12px;width:90px;\"  >
								<option value='0'>매월</option>";
								for($i=1; $i < 13;$i++){
								$Contents01 .= "<option value='".$i."' ".( $set_cron_sellertool[cron_months] == $i ? 'selected' : '' ).">".$i." 월</option>";
								}
								$Contents01 .= "
							</select>
							</td>
							<td>
							<select name='weekday' id='weekday' style=\"font-size:12px;width:90px;\"  >
								<option value='0'>매요일</option>";
								
								foreach($week_day as $key => $val){
								$Contents01 .= "<option value='".$key."' ".( $set_cron_sellertool[cron_weekdays] == $i ? 'selected' : '' ).">".$val." 요일</option>";
								}
								$Contents01 .= "
							</select>
							</td>
						</tr>
					</table>
					</td>
				</tr>
				<tr class='time_option_self' style='display:none;'>
					<td class='input_box_title'>  스케줄 시간설정  </td>
					<td class='input_box_item' colspan=3 style='padding:5px;' >
						<input type='text' name='schedul_time' id='schedul_time' value='' style='width:20%' >
						<span style='font-size:11px; color:blue;'>ex) */10 * * * *  <a href='javascript:void:0' onClick=\"PopSWindow('/admin/store/cron_sample.gif',900,710,'cron_sample')\">샘플 가이드</a></span>
					</td>
				</tr>
				</table>
		</td>
	</tr>
	<tr>
		<td colspan=2 align=center style='padding:10px 0px;'><input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle><!--btn_inquiry.gif--></td>
	</tr>
	  </table>
	  </form>";


$Contents02 = "
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left'  class='list_table_box'><!--style='table-layout:fixed;'-->
		<col width=5%>";
		if($schedule_type=='sellertool'){
		$Contents02 .= "
		<col width=10%>";
		}
		$Contents02 .= "
		<col width=* >
		<col width=8% >
		<col width=8%>
		<col width=8%>
		<col width=8%>
		<col width=8%>
		<col width=9% >
		<col width=9% > 
		<col width=9%>
		<tr bgcolor=#efefef align=center height=25>
			<td class='s_td'  nowrap rowspan='2'>번호 </td>";
			if($schedule_type=='sellertool'){
			$Contents02 .= "
			<td class='m_td' rowspan='2'>제휴사 업체</td>";
			}
			$Contents02 .= "
			<td class='m_td' nowrap rowspan='2'>작업파일</td>
			<td class='m_td' nowrap rowspan='2'>동작위치</td>
			<td class='m_td' nowrap rowspan='2'>동작여부</td>
			<td class='m_td' colspan='5'>스케줄 시간</td>
			<td class='e_td' nowrap rowspan='2'>관리 </td>
		</tr>
	    <tr bgcolor=#efefef align=center height=25>
			
			<td class='m_td' >분지정</td>
			<td class='m_td' >시간지정</td>
			<td class='m_td' >일지정</td>
			<td class='m_td' >월지정</td>
			<td class='m_td' >요일지정</td>
			
	    </tr>";


if($max == ""){
	$max = 15; //페이지당 갯수
}

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}


$where  = "where ss_ix !='' ";

if($schedule_type){
	$where .= " and type = '".$schedule_type."'";
}

$sql = "select * from shop_schedule_setting  $where ";
$db->query($sql);
$total = $db->total;

if($QUERY_STRING == "nset=$nset&page=$page"){
	$query_string = str_replace("nset=$nset&page=$page","",$QUERY_STRING) ;
}else{
	$query_string = str_replace("nset=$nset&page=$page&","","&".$QUERY_STRING) ;
}

$str_page_bar = page_bar($total, $page,$max, $query_string,"view");

$sql = "select * from shop_schedule_setting  $where limit $start,$max ";
$db->query($sql);
if($schedule_type=='sellertool'){
$colspan= "11";
}else{
$colspan= "10";
}
//print_r($cron_sellertools);
if($db->total > 0){
	for($i=0; $i < $db->total; $i++){
		$data = $db->fetch($i);
		$no = $total - ($page - 1) * $max - $i;
		
		foreach($week_day as $key => $val){
			if($data[weekday] == $key){
				$weekday = $val;
			}
		}
		//print_r($cron_sellertool);
		$Contents02 .= "<tr align=center height=30>
				<td class='list_box_td list_bg_gray' >".$no." </td>";
				if($schedule_type=='sellertool'){
				$Contents02 .= "
				<td class='list_box_td point' style='text-align:left;padding-left:10px;'>".$data[site_code]."</td>";
				}
				$Contents02 .= "
				<td class='list_box_td ' >".$data['file']."</td>
				<td class='list_box_td ' >".$data['action_type']."</td>
				<td class='list_box_td ' >".($data[auto_type] == "Y" ? "동작중":"동작안함")."</td>";
				if(empty($data['schedul_time'])){
				$Contents02 .= "
				<td class='list_box_td list_bg_gray' >".$data[minute]." 분</td>
				<td class='list_box_td ' >".$data[hour]." 시</td>
				<td class='list_box_td list_bg_gray' >".($data[day] == "0" ? "매":$data[day])." 일"." </td>
				<td class='list_box_td ' >".($data[month] == "0" ? "매":$data[month])." 월"."</td>
				<td class='list_box_td ' >".($data[weekday] == "0" ? "매":$weekday)." 요일"."</td>";
				}else{
				$Contents02 .= "
				<td class='list_box_td' colspan='5'>".$data[schedul_time]."</td>";
				}
				$Contents02 .= "
				<td class='list_box_td list_bg_gray' >
				<a href=\"javascript:ModifyScheduleInfo('".$data[ss_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>
				<a href=\"javascript:DeleteScheduleInfo('".$data[ss_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a></td>
			</tr>";
		
	}

	$Contents02 .= "";
}else{
		$Contents02 .= "<tr height=60><td colspan=".$colspan." align=center>".$titel_name." 스케줄 정보가 없습니다.</td></tr>";

}


$Contents02 .= "</table>";



$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$Contents01."<br>";
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";

$Contents = $Contents."</table >
<table width='100%' border='0' cellspacing='0' cellpadding='0'>
  <tr height='40'>
    <td colspan=5 align=left>

    </td>
    <td  colspan='5' align='right' >&nbsp;".$str_page_bar."&nbsp;</td>
  </tr>
</table>";

$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >상품 업데이트를 원하시는 시간대를 설정 하신후 저장 버튼을 클릭하시면 설정이 완료되게 됩니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >등록된 설정정보를 바탕으로 상품정보를 주기적으로 연동하게 됩니다</td></tr>
</table>
";

if($admininfo[mall_type] == "H"){
	$Contents = str_replace("쇼핑몰","사이트",$Contents);
}
$Script = "
<script>
	function ModifyScheduleInfo(ix){
		 $.ajax({
           type:'POST',
           url:'shop_scheduler.act.php',
           dataType:'json', 
		   data:{
			act:'modify',
			ix:ix
		   },
           success : function(trans) {
               //console.log(trans.file_type)
				if(trans.file_type == 'input'){
					$('#scheduler_file_name_input').val(trans.file);
					$('.file_type_input').show();
					$('.file_type_select').hide();
				}else{
					$('#scheduler_file_name').val(trans.file);
					$('.file_type_input').hide();
					$('.file_type_select').show();
				}
				$('#comment').val(trans.comment);
				if(trans.schedul_time == '' || trans.schedul_time == null){
					$('#time_option_basic').trigger('click');
					$('#minute').val(trans.minute);
					$('#hour').val(trans.hour);
					$('#day').val(trans.day);
					$('#month').val(trans.month);
					$('#weekday').val(trans.weekday);
					
				}else{
					$('#time_option_self').trigger('click');
					$('#schedul_time').val(trans.schedul_time);
				}
				$('#act').val('update');
				$('#type').val(trans.type);
				$('input:radio[name=file_type]:radio[value='+trans.file_type+']').attr('checked',true);
				$('input:radio[name=action_type]:radio[value='+trans.action_type+']').attr('checked',true);
				$('input:radio[name=auto_type]:radio[value='+trans.auto_type+']').attr('checked',true);
				$('#ss_ix').val(trans.ss_ix);

           },
           error : function(xhr, status, error) {
                 console.log(xhr);
           }
     });
	}

	function DeleteScheduleInfo(ix){
		if(confirm('스케줄 정보를 삭제하시겠습니까?')){
			$('#act').val('delete');
			$('#ss_ix').val(ix);

			$('#schedule_form').submit();
		}
	}

	$('document').ready(function(){
		$('#file_type1').click(function(){
			$('.file_type_input').show();
			$('.file_type_select').hide();
		})
		$('#file_type2').click(function(){
			$('.file_type_input').hide();
			$('.file_type_select').show();
		})

		$('#time_option_basic').click(function(){
			$('.time_option_self').hide();
			$('#schedul_time').val('');
			$('.time_option_basic').show();
		});
		$('#time_option_self').click(function(){
			$('.time_option_basic').hide();
			$('.time_option_self').show();
		});
	});
</script>
";
$P = new LayOut();
$P->addScript = $Script;
if($page_type == 'sellertool'){
	$P->strLeftMenu = sellertool_menu();
}else{
	$P->strLeftMenu = service_manage_menu();
}
$P->Navigation = "시스템관리 > ".$titel_name." 환경설정 > ".$titel_name." 스케줄설정";
$P->title = "".$titel_name." 스케줄설정";
$P->strContents = $Contents;
echo $P->PrintLayOut();

function filesInDir ($tdir) 
{ 
	if($dh = opendir ($tdir)) { 
	
		$files = Array(); 
		$in_files = Array(); 
		
		while($a_file = readdir ($dh)) { 
			if($a_file[0] != '.') { 
				if(is_dir ($tdir . "/" . $a_file)) { 
					$in_files = filesInDir ($tdir . "/" . $a_file); 
					if(is_array ($in_files)) $files = array_merge ($files , $in_files); 
				} else { 
					array_push ($files , $tdir . "/" . $a_file); 
				} 
			} 
		} 
		
		closedir ($dh); 
		return $files ; 
	} 
} 
?>