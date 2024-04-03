<?
include("../class/layout.class");
$db = new MySQL;


//게시판 정보를 가져온다.
$sql = "SELECT bmc.* FROM bbs_manage_config bmc , bbs_group bg WHERE bmc.board_group = bg.div_ix AND disp = 1";
$db->query($sql);


//bbs 이름을 셀렉트 
$mstring = "<select name='board_name' id='board_name'>";
for($i=0; $i<$db->total; $i++)
{
	$db->fetch($i);
	$mstring .= "<option value='".$db->dt[board_ename]."|".$db->dt[bm_ix]."' ".($board == $db->dt[board_ename] ? "selected" : "")." >".$db->dt[board_name]."</option>";
}
$mstring .= "</select>";


?>

<html>
<head>
<title><?=$db->dt["popup_title"]?></title>
</head>
<LINK href="{template_dir}/bbs.css" type=text/css rel=stylesheet>
<script type='text/javascript' src='/admin/js/jquery-1.7.1.min.js'></script>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<div class="coopy_pop_wrap">
	<h2>게시물 복사</h2>
	<form name="bbs_form" id='bbs_form' method="POST" action='board.manage.act.php'>
	<input type="hidden" name="act" value="copy_bbs_list">
	<input type="hidden" name="idx_list" id="idx_list" value="">
	<input type="hidden" name="origin_board" value="<?=$board?>">
		<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25' class='search_table_box'>
			<tr>
				<td class='search_box_item' style="padding-right:20px;">
					게시판 : <?=$mstring ?>
				</td>
				<td class='search_box_item board_div_area' style="display:none;">
					<div id='board_div_area'></div>
				</td>
			</tr>
			
			<?if($board == "after" || $board == "premium_after"){?>
			<tr>
				<td class='search_box_item' style="padding-right:20px;">
					상품시스템코드 : <input type="text" name="pid" value="">
				</td>
			</tr>
			<?}?>

		</table>
		<div class="btn_wrap">
			<a class="copy" href="javascript:copyBBS();">복사</a>
			<a href="javascript:closeWin();">취소</a> 
		</div>

		<style type="text/css">
			.coopy_pop_wrap { padding:20px; }
			.coopy_pop_wrap h2 { margin:0; font-size:18px; }
			.coopy_pop_wrap .search_table_box { margin-top:30px; }
			.coopy_pop_wrap select { font-size:14px; line-height:18px; }
			.coopy_pop_wrap .btn_wrap { margin-top:35px; text-align:center; }
			.coopy_pop_wrap .btn_wrap a { margin:0 2px; padding:5px 20px; border:1px solid #1b1b1b; font-size:14px; line-height:14px; color:#1b1b1b; text-decoration:none; display:inline-block; }
			.coopy_pop_wrap .btn_wrap a.copy { background:#eee; }

		</style>

	</form>
</div>

<script type='text/javascript'>
	window.onload = function(){
		if( (parseInt(document.getElementById('body').offsetHeight)- parseInt(window.document.body.clientHeight)) > 20 ) {
			window.document.body.scroll = "auto";
			window.resizeBy(18,0);
		}else{
			//window.resizeTo(body.offsetWidth, body.offsetHeight); 
		}
	}
	function closeWin()
	{
		$("#copy_bbs", top.document).hide();
		//top.document.getElementById('copy_bbs').style.visibility = "hidden";
	}

	function copyBBS()
	{
		var idx_list = "";
		$('input[name^=bbs_ix]',top.document).each(function(){
			if($(this).is(":checked"))
			{
				idx_list += $(this).val() + "|";
			}
		});


		$("#idx_list").val(idx_list);

		$("#bbs_form").submit();
	}
</script>
<script type='text/javascript'>
	$(document).ready(function(){

		$("#board_name").change(function(){
			var value = $(this).val().split("|");

			$.ajax ({
				data: ({
						act : "get_board_div",
						board : value[0],
						bm_ix : value[1],
				}),
				type: 'GET', // POST 로 전송
				dataType: 'text', 
				url: './board.manage.act.php', // 호출 URL		
				success:function(data){
					if(data != "X")
					{
						$(".board_div_area").show();
						$("#board_div_area").html("분류 : "+data);
					}
					else
					{
						$(".board_div_area").hide();
					}

				},
				error:function(e) {
				}
			});
		});

		//최초 로드시 div 가지고 와야함
        $("#board_name").trigger('change');

	});
</script>
</body>

</html>
