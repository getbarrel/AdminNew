<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include($_SERVER["DOCUMENT_ROOT"]."/include/global_util.php");

$db = new Database;
$db->query("SELECT pname FROM ".TBL_SHOP_PRODUCT." where id ='$pid'");

if($db->total){
	$db->fetch();
	$pname = $db->dt[pname];	
}

$db->query("SELECT * FROM ".TBL_SHOP_PRODUCT_OPTIONS." where pid ='$pid' and option_kind ='b' ");
if($db->total){
	$basic_option_bool = true;	
}else{
	$basic_option_bool = false;	
}
?>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
<LINK REL='stylesheet' HREF='/admin/include/admin.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/common/css/design.css' TYPE='text/css'>
<script Language="JavaScript">

function CheckValue(frm){
	
	if (frm.option_name.value.length < 1){
		alert(language_data['option.pop.php']['A'][language]);
		//'옵션이름을 입력해주세요'
		return false;
	}
	
	
	return true;
}

function updateOption(opn_ix, option_name, option_use, option_kind){
	var frm = document.option_regist;
	
	frm.act.value = "update";	
	frm.opn_ix.value = opn_ix;	
	frm.option_name.value = option_name;	
	if(option_use == "1")
		frm.option_use.checked = true;
	else
		frm.option_use.checked = false;
		
	for(i=0;i<frm.option_kind.length;i++){
		if(frm.option_kind[i].value == option_kind){
			frm.option_kind[i].selected = true;
		}
	}
}	

</script>
</head>
<body topmargin=0 leftmargin=0 ><!--onload="Init(document.send_mail);"-->
<TABLE cellSpacing=0 cellPadding=0 width="100%" align=center border=0>
	<TR>		
		<td align=center colspan=2>
		<table border="0" width="100%" cellpadding="0" cellspacing="1" align="center">	
			<tr><td  align=left class='top_orange'  ></td></tr>
			<tr height=35 bgcolor=#efefef>
				<td  style='padding:0 0 0 0;'> 
					<table width='100%' border='0' cellspacing='0' cellpadding='0' >
						<tr> 
							<td width='10%' height='31' valign='middle' style='font-family:굴림;font-size:16px;font-weight:bold;letter-spacing:-2px;word-spacing:0px;padding-left:10px;' nowrap><!--border-bottom:2px solid #ff9b00;-->
								<img src='/admin/images/icon/sub_title_dot.gif' align=absmiddle> 옵션등록하기
							</td>
							<td width='90%' align='right' valign='top' ><!--style='border-bottom:2px solid #efefef;'-->
								&nbsp;
							</td>
						</tr>
						<!--tr height=10><td colspan=2></td></tr-->
					</table>
				</td>
			</tr>
			<tr height=30><td class="p11 ls1" style='padding:0 0 0 20;' > <span class="org str"><?=$pname?></span> 제품의 옵션을 등록합니다.</td></tr>	
			<tr>				
				<td align=center style='padding: 0 20 0 20'>
				<form name="option_regist" action="option.pop.act.php" onsubmit="return CheckValue(this);" method="get">
				<input type="hidden" name="opn_ix" value="">
				<input type="hidden" name="act" value="insert">
				<input type="hidden" name="pid" value="<?=$pid?>">
					<table cellpadding=0 cellspacing=0 width=100% border=0>					
					<tr><td colspan=2 bgcolor=silver height=1></td></tr>
					<tr height=50>
						<th width=30%>옵션구분</th>
						<td align=left>
							<select name=option_kind>
								<?if(!$basic_option_bool){?>
								<option value=b>가격/재고 관리 옵션</option>
								<?}?>
								<option value=p>가격추가옵션</option>
								<option value=s>선택옵션</option>
							</select>
							<input type="checkbox" name="option_use" value=1 class='no' checked> 사용<br>
							<span class='p11 ls1'>가격/재고 관리 옵션은 한번만 적용이 가능합니다.</span>
						</td>
					</tr>
					<tr><td colspan=2 bgcolor=silver height=1></td></tr>
					<tr height=40>
						<th>옵션이름</th>
						<td align=left>
							<input type="text" name="option_name" style='width:200px;'>				
						</td>
					</tr>
					<tr><td colspan=2 bgcolor=silver height=1></td></tr>
					<tr height=150>			
						<td colspan=2 style="vertical-align:top;padding:10 0 0 0 ">
			<?
			$db->query("SELECT * FROM ".TBL_SHOP_PRODUCT_OPTIONS." where pid ='$pid'");
			
			if($db->total){
			
					echo"<table cellpadding=0 cellspacing=0 width='100%' border=0 >
						<tr height=23 bgcolor=#efefef ><td class='p11 ls1'>옵션이름</td><td class='p11 ls1'>표시유무</td><td class='p11 ls1'>옵션종류</td><td class='p11 ls1'>삭제</td></tr>";
				for($i=0;$i < $db->total;$i++){
				$db->fetch($i);
			?>
						<tr height=23>
							<td class='p11 ls1'><a href="javascript:updateOption('<?=$db->dt[opn_ix]?>','<?=$db->dt[option_name]?>','<?=$db->dt[option_use]?>','<?=$db->dt[option_kind]?>')"><b><?=$db->dt[option_name]?></b></a></td>
							<td class='p11 ls1'><!--input type=checkbox name='opn_ix' value='<?=$db->dt[opn_ix]?>' class=no--><?//=($i+1)?> 
			<?
							if($db->dt[option_use] == "1"){
								echo "표시";				
							}else if($db->dt[option_use] == "0"){
								echo "표시하지않음";
							}
			?>                           					
							</td>
			                                <td class='p11 ls1'>
			<?
							if($db->dt[option_kind] == "b"){
								echo "가격/재고 관리 옵션";
							}else if($db->dt[option_kind] == "p"){
								echo "가격추가옵션";
							}else if($db->dt[option_kind] == "s"){
								echo "선택옵션";
							}
			?>                           
			                               	</td>
							
							<td class='p11 ls1'><a href='option.pop.act.php?act=delete&pid=<?=$pid?>&opn_ix=<?=$db->dt[opn_ix]?>'>삭제</a></td>
						</tr>
			<?		
				}
			echo"</table>";	
			}else{
			?>
						<table width=100%>
						<tr><td height=150 vailgn=middle align=center>등록된 옵션이 없습니다.</td></tr>
						</table>
			<?	
			}
			?>				
						</td>
					</tr>
					<tr><td colspan=2 bgcolor=silver height=1></td></tr>
					</table>				
				</td>
			</tr>
		</table>
		</td>
	</tr>	
	<tr height=50>
		<td align=center style='padding:10 0 0 0' colspan=2>
			<input type="image" src="../images/btn/ok.gif" style="border:0px;">
			<a href="javascript:self.close();"><img src="../images/btn/cancel.gif" border=0></a>
		</td>
	</tr></form>
</TABLE>

<IFRAME id=act name=act src="" frameBorder=0 width=0 height=0 scrolling=no ></IFRAME>	
</body>
</html>




<?
function PrintSelect($pid, $select_opn_ix)
{
	$mdb = new Database;
	$mdb->query("SELECT * FROM ".TBL_SHOP_PRODUCT_OPTIONS." where pid ='$pid'");
	
	$SelectString = "<Select name='option_name'>";
	
	if ($size == 0){
		$SelectString = $SelectString."<option>옵션이 없습니다.</option>";
	}else{
		
		for($i=0; $i < $mdb->total; $i++){
			$SelectString = $SelectString."<option value='".$mdb->dt[opn_ix]."'>".$mdb->dt[option_name]."</option>";
		}
		
	}
	
	$SelectString = $SelectString."</Select>";
	
	return $SelectString;
}
?>

