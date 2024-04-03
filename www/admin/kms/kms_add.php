<?
include("../class/layout.work.class");
include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
include("kms.lib.php");

$db = new Database;
$mdb = new Database;

$sql = 	"SELECT wl.*, wg.group_depth , case when wg.group_depth = 1 then wg.group_ix else parent_group_ix end as parent_group_ix , cmd.department
				FROM work_list wl, work_group wg , common_member_detail cmd
				where wl_ix ='$wl_ix' and wl.group_ix = wg.group_ix and wl.charger_ix = cmd.code ";

//echo $sql;
$db->query($sql);
$db->fetch();

if($db->total){
	$act = "update";
	$sdate = $db->dt[sdate];
	$dday = $db->dt[dday];
	$parent_group_ix = $db->dt[parent_group_ix];

	$department = $db->dt["department"];
	$charger_ix = $db->dt[charger_ix];
	$is_schedule = $db->dt[is_schedule];
	$is_hidden = $db->dt[is_hidden];

	$stime = $db->dt[stime];
	$dtime = $db->dt[dtime];

	$sql = 	"SELECT charger_ix FROM work_charger_relation where wl_ix ='$wl_ix'  ";

	//echo $sql;
	$db->query($sql);
	$co_charger_ix_rows = $db->getrows();
	$co_charger_ix = $co_charger_ix_rows[0];
	//print_r($co_charger_ix);

	if($charger_ix != $admininfo[charger_ix]){
		//$readonly_str = " disabled ";
	}
	//if(in_array($admininfo[charger_ix] , $co_charger_ix)){

	//}
	WorkHistory($mdb, $wl_ix, $admininfo[charger_ix], "R", "지식 변경 화면 ");

}else{
	//WorkHistory($mdb, $wl_ix, $admininfo[charger_ix], "R", "지식 등록 화면 ");

	$act = "insert";
	$sdate = $sdate;
	if($dday){
		$dday = $dday;
	}else{
		$dday = $sdate;
	}
	$parent_group_ix = "11";
	$charger_ix = $admininfo[charger_ix];
	$department = $admininfo["department"];
}

//print_r($admininfo);
//".round(get_folder_size('../file/'.$user[code].'/', false)/1024/1024,2)."MB / 100MB 
//".getGlobalCategoryPath($ssgcid, getDepth($ssgcid))."
//".getCategoryPath($ssmycid, getDepth($ssmycid,'mytree'))."
//.getDepth($ssmycid,'mytree')."

$Contents01 = "<table cellpadding=0 cellspacing=0 width=100% height=100%>
<tr>
	<td align=center valign=top><form name=kms_input action='write.act.php' method='post' enctype='multipart/form-data' onsubmit='return SubmitX(this);'><input type='hidden' name=act value='insert'>

			<table width='100%' cellpadding=0 cellspacing=0><tr height=30><td style='padding:5px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle style='position:relative;'><b class=blk> 지식 저장하기</b> </div>")."</td></tr></table>
		
	<table cellpadding=3 cellspacing=0 border=0 width=100% class='input_table_box'>
		
		<tr>
			<td class='input_box_title'>전체 지식분류</td>
			<td class='input_box_item'><input type=hidden name=gcid size=23 value='".$ssgcid."'><input type=text class='textbox' name=gcname size=35 value='' onclick='parent.document.frames['tree'].location.href='../tree.php'' readonly> <img src=../img/01.gif align=absmiddle> 내지식을 공유할때 </td>
		</tr>
		<tr>
			<td class='input_box_title' nowrap>내지식분류</td>
			<td class='input_box_item' align=left ><input type=hidden name=mycid value='".$ssmycid."'><input type=hidden name=depth value=''><input type=text class='textbox' name=mycname value='' size=35 onclick=\"parent.document.frames['tree'].location.href='../mytree.php'\" readonly> <img src=../img/01.gif align=absmiddle> 내지식을 저장할 카테고리</td>
		</tr>
		<tr><td class='input_box_title'>자료제목</td><td class='input_box_item' ><input type=text class='textbox'  name=data_name size=70></td></tr>
		<tr>
			<td colspan=2 class='input_box_item' >
				<input type=hidden name=data_text value=''>
				<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25'>
                    <col width='120' >
					<col width='*' >
					<tr>                       
                      <td height='30' colspan='2'>
						".miniWebEdit("/admin")."
                      </td>
                    </tr>
                    <tr  >
          	          <td height='25' align='center' ></td>
          		      <td align='right' style='text-align:right;'>&nbsp; 
						<a href='javascript:doToggleText(document.product_input);' onMouseOver=\"MM_swapImage('editImage15','','../images/".$admininfo["language"]."/webedit/bt_html_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../images/".$admininfo["language"]."/webedit/bt_html.gif' name='editImage15' width='93' height='23' border='0' id='editImage15'></a>
						<a href='javascript:doToggleHtml(document.product_input);' onMouseOver=\"MM_swapImage('editImage16','','../images/".$admininfo["language"]."/webedit/bt_source_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../images/".$admininfo["language"]."/webedit/bt_source.gif' name='editImage16' width='93' height='23' border='0' id='editImage16'></a>
                    </tr>
                  </table>
			</td>
		</tr>
		<tr>
			<td class='input_box_title'>공개여부</td>
			<td valign=middle class='input_box_item'><input type=radio name=open value=1 checked> 공개 <input type=radio name=open value=0> 비공개  지식공유시에 공개유무를 선택합니다.</td>
		<tr><td class='input_box_title'>관련링크</td><td class='input_box_item'><input type=text class='textbox'  name='data_link' size=70><!--a href='JavaScript:stil(document.kms_input.data_link.value);'>훔쳐오기</a--></td></tr>
		<tr><td class='input_box_title'>검색어</td><td class='input_box_item'><input type=text class='textbox' name=keyword size=70></td></tr>
		<tr>
			<td class='input_box_title'>파일종류</td>
			<td class='input_box_item'>
			<input type=radio name=data_type onclick='file_select(this.value, '파일없음')' value=0 checked> 파일없음 
			<input type=radio name=data_type onclick='file_select(this.value, '텍스트')' value=1> 텍스트 
			<input type=radio onclick='file_select(this.value, '이미지')' name=data_type value=2> 이미지 
			<input type=radio name=data_type onclick='file_select(this.value, '음악(오디오)파일')' value=3> 음악(오디오)파일 
			<input type=radio name=data_type onclick='file_select(this.value, '영상파일')' value=4> 영상파일
			<input type=radio name=data_type onclick='file_select(this.value, '압축파일')' value=5 > 압축파일
			<input type=radio name=data_type onclick='file_select(this.value, '전자결제')' value=6 > 전자결제
			</td>
		</tr>
		<tr id=file_tr style='display:none;'>
			<td class='input_box_title' id='file_text' style='padding-left:30px;'> 파일</td>
			<td class='input_box_item'>
				<input type=file name=file size=55 style='font-size:11px;'><br>
				<input type=file name=file2 size=55 style='font-size:11px;'>
			</td>
		</tr>		
		<tr ><td class='input_box_item' colspan=2 height='6' id='file_exp'> 파일을 입력하기 위해서 파일 종류를 선택해 주세요</td></tr>
	</table>
	<div style='padding-top:5px;text-align:right'><input type=image src='../images/".$admininfo["language"]."/b_save.gif'> <!--a href='list.php'><image src=./img/b_cancel.gif border=0></a--></div>
	</form>
	</td>
</tr>
</table>";



$help_text = "
	<table cellpadding=1 cellspacing=0 class='small' >
		<col width=8>
		<col width=*>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >지식을 원활히 관리하기 위해서는 그룹을 선택하셔야 합니다.</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><u>비공개</u>지식는  본인 이외의 리스트에 노출되지 않습니다.</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >부가적으로 회사정보등을 입력해서 관리 하실 수 있습니다.</td></tr>
	</table>
	";


$help_text = HelpBox("지식 등록 관리", $help_text);

$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$Contents01."<br>";
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."<tr><td>".$help_text."</td></tr>";
$Contents = $Contents."</table >";



 $Script = "<script language='JavaScript'>
<!--
top.document.getElementById('leftmenu_table').style.display = 'block';


function file_select(vdata_type, data_typename){

	document.getElementById('file_text').innerHTML = data_typename;


	switch(vdata_type){
		case '0':		
			document.getElementById('file_tr').style.display = 'none';
			document.getElementById('file_exp').innerHTML = '<img src=../img/01.gif align=absmiddle> 파일을 입력하기 위해서 파일 종류를 선택해 주세요';
			break;
		case '1':
			document.getElementById('file_tr').style.display = 'block';
			document.getElementById('file_exp').innerHTML = '<img src=../img/01.gif align=absmiddle> .txt, .doc, .hwp 등의 데이타 파일';
			break;
		case '2':
			document.getElementById('file_tr').style.display = 'block';
			document.getElementById('file_exp').innerHTML = '<img src=../img/01.gif align=absmiddle> .gif, .jpg, .psd, .ai ... 등 이미지 파일';
			break;
		case '3':
			document.getElementById('file_tr').style.display = 'block';
			document.getElementById('file_exp').innerHTML = '<img src=../img/01.gif align=absmiddle> .mp3, .asf, .ogg';
			break;
		case '4':
			document.getElementById('file_tr').style.display = 'block';
			document.getElementById('file_exp').innerHTML = '<img src=../img/01.gif align=absmiddle> .asf, .avi, .wmv, .mpg ... ';
			break;
		case '5':
			document.getElementById('file_tr').style.display = 'block';
			document.getElementById('file_exp').innerHTML = '<img src=../img/01.gif align=absmiddle> .zip, .rar, .tar, .cab ... ';
			break;
		case '6':
			document.getElementById('file_tr').style.display = 'block';
			document.getElementById('file_exp').innerHTML = '<img src=../img/01.gif align=absmiddle> 전자결제 참고 문서입니다... ';
			break;
	}
		
}

function MySetCategory(cname,cid,depth){	
	//document.kms_input.mycname.value = cname;	
	document.frames['act'].location.href='./kms.bbs/write.act.php?act=category&mycid='+cid+'&depth='+depth;
	document.kms_input.mycid.value = cid;	
	document.kms_input.depth.value = depth;
}


function GlobalSetCategory(cname,cid,depth){
	//document.kms_input.gcname.value = cname;	
	document.frames['act'].location.href='./kms.bbs/write.act.php?act=category&gcid='+cid+'&depth='+depth;
	document.kms_input.gcid.value = cid;	
}

function SubmitX(frm){
	
	if(frm.gcid.value.length < 1){
		alert('왼쪽 카테고리에서 지식을 등록할 전체 지식 카테고리를 선택해 주세요');
		return false;	
	}
	
	if(frm.mycid.value.length < 1){
		alert('왼쪽 카테고리에서 지식을 등록할 내 지식 카테고리를 선택해 주세요');
		return false;	
	}
	
	if(frm.data_name.value.length < 1){
		alert('지식 제목을 선택해 주세요');
		return false;	
	}
	frm.data_text.value = iView.document.body.innerHTML;
	//alert(frm.data_text.value);
	if(frm.data_text.value.length < 1){
		alert('지식 내용을 선택해 주세요');
		return false;	
	}
}

function stil(sURL){
		
		alert(GetSource(sURL));
		
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf('#')!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf('?'))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
//-->
</script>
 ";

if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = "<!--script type='text/javascript' src='./js/jquery-1.4.2.min.js'></script-->
<script type='text/javascript' src='./js/ui/ui.core.js'></script>
<script type='text/javascript' src='./js/ui/ui.datepicker.js'></script>
<!--script type='text/javascript' src='./js/ui/jquery-ui-timepicker-addon-0.5.js'></script-->
<script type='text/javascript' src='work.js'></script>".$Script;
	$P->strLeftMenu = kms_menu();
	$P->Navigation = "KMS 관리 > 지식 등록";
	$P->title = "지식 등록";
	$P->strContents = $Contents;
	$P->NaviTitle = "지식 등록관리";
	$P->prototype_use = false;

	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = "<!--script type='text/javascript' src='./js/jquery-1.4.2.min.js'></script-->
<script type='text/javascript' src='./js/ui/ui.core.js'></script>
<script type='text/javascript' src='./js/ui/ui.datepicker.js'></script>
<!--script type='text/javascript' src='./js/ui/jquery-ui-timepicker-addon-0.5.js'></script-->
<script type='text/javascript' src='work.js'></script>".$Script;
	$P->strLeftMenu = kms_menu();
	$P->Navigation = "KMS 관리 > 지식 등록";
	$P->title = "지식 등록";
	$P->strContents = $Contents;
	$P->footer_menu = footMenu()."".footAddContents();
	$P->prototype_use = false;

	echo $P->PrintLayOut();
}

?>