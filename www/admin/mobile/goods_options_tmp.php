<?
//include("../class/layout.class");
include("../class/mobilelayout.class");
include("../../class/database.class");
include("../include/admin.util.php");

$db = new Database; 

$Script = "
<script type='text/javascript' src='../product/goods_input.js'></script>
<script language='JavaScript'>
	$(document).ready(function(){
		$('.footer_btns02').hide();
	})
	
	function mobileAllPossibleCases(arr) {
		if (arr.length === 0) {
			return [];
		}else if (arr.length ===1){
			return arr[0];
		}else {
			var result = [];
			var allCasesOfRest = mobileAllPossibleCases(arr.slice(1));
			var item_id = 0;
			//alert($('#options_basic_item_input_0').html())
			for (var c in allCasesOfRest) {
			  for (var i = 0; i < arr[0].length; i++) {
				result.push(arr[0][i] + '_'+ allCasesOfRest[c]);

				before_option_cnt = i;
				item_id++;
			  }		 
			}
			
			
			return result;
		}
	}

	function MobileMakeStockOption(){
		var make_option = new Array();
		var mk_i = 0;
		$('.make_option').each(function(){
			//alert($(this).attr('option_selected') + '::'+$(this).html());

			if($(this).attr('option_selected') == '1'){
				make_option[mk_i] = new Array();
				$('.make_option_detail_'+$(this).attr('opnt_ix')).each(function(){
					//alert(mk_i+':::'+$(this).html());
					if($(this).attr('option_detail_selected') == '1'){
						make_option[mk_i].push($(this).html());
						//alert($(this).attr('option_detail_selected') + '::'+$(this).html());
					}
				});
				//alert(mk_i+'<==');
				mk_i++;
			}
			
		});
		
		var result=mobileAllPossibleCases(make_option);
		window.CALLBACK_OPTION.setMessage(result);
	}


</script>
<style type='text/css'>
	.goods_input_header{padding:0 0 0 8px ;background:#ebebeb;border-bottom:2px solid #d3d3d3;}
	.goods_input_header:after{content:'';clear:both;display:block;} 
	.goods_input_header span{padding-left:9px;background:url('./images/li_bg.gif') 0 center no-repeat;background-size:3px 3px;font-size:15px;font-weight:bold;}
	.goods_input_header tr td {height:46px;}
</style>
";

$Contents01 = "
<div class='goods_input_header'>
	<table border='0' cellpadding='0' cellspacing='0' width=100%'>
	<col width='50%' />
	<col width='50%' />
		<tr>
			<td><span>자주쓰는옵션</span></td>
			<td align='right'>
				<a href='./goods_options_tmp_list.php'><img src='../images/".$admininfo["language"]."/btn_favorite_option_manage.gif' id='btn_favorite_option_use' style='cursor:pointer;margin:4px 3px 0px 4px;' ></a>
			</td>
		</tr>
	</table>
</div>
";

$Contents01 .= getMobileOptionTmpTitle();


$Contents = $Contents01;


$P = new MobileLayOut();
$P->addScript = $Script;
//$P->strLeftMenu = store_menu();
$P->strContents = $Contents;
$P->Navigation = "";
$P->layout_display = false;
echo $P->PrintLayOut();


function getMobileOptionTmpTitle(){
	global $admininfo, $layout_config, $goods_options_tmp_type;

//	if($_SESSION["layout_config"]["mall_use_inventory"] != "Y"){
			//$where ="and ".$goods_options_tmp_type." in ('".$admininfo[$goods_options_tmp_type]."','') ";
			$where =" and (basic='Y' or charger_ix='".$_SESSION["admininfo"]["charger_ix"]."' )";

			$mdb = new Database;
			$sql = "SELECT * FROM ".TBL_SHOP_PRODUCT_OPTIONS_TMP." where disp ='1' $where ";
			//echo $sql;
			$mdb->query($sql);
			$option_tmp_infos = $mdb->fetchall();
		//echo count($option_tmp_infos);
			if (count($option_tmp_infos) > 0){
				//exit;
				$SelectString .= "
				<div style='clear:both;height:10px;'></div>
				<div style='width:90%;margin:0 auto;clear:both;'>
				";
				for($i=0; $i < count($option_tmp_infos); $i++){
					//$mdb->fetch($i);
					//print_r($option_tmp_infos[$i]);
					$SelectString .= "<div id='opnt_ix_".$option_tmp_infos[$i][opnt_ix]."' opnt_ix='".$option_tmp_infos[$i][opnt_ix]."' class='make_option' option_selected='0' style='float:left;min-width:90px;border:1px solid silver;background-color:#efefef;padding:10px 5px;margin:4px 3px;cursor:pointer;text-align:center;' onclick=\"selectTmpOption('".$option_tmp_infos[$i][opnt_ix]."')\"> ".$option_tmp_infos[$i][option_name]." ".($option_tmp_infos[$i][basic]=="Y" ? "(기본)" : "")."</div>";
				}

				//$SelectString .= "<div style='float:left;' class='make_option_btn' ><a href='./goods_options_tmp.php' target=_blank><img src='../images/".$admininfo["language"]."/btn_favorite_option_manage.gif' id='btn_favorite_option_use'   style='cursor:pointer;margin:4px 0px 0px 4px;' ></a></div>";

				$SelectString .= "
				</div>
				<div style='clear:both;height:10px;'></div>
				<div class='goods_input_header' style='clear:both;'>
					<table border='0' cellpadding='0' cellspacing='0' width=100%'>
					<col width='50%' />
					<col width='50%' />
						<tr>
							<td><span>옵션선택</span></td>
							<td align='right'><!--a href='./goods_options_tmp.php' target=_blank><img src='../images/".$admininfo["language"]."/btn_favorite_option_manage.gif' id='btn_favorite_option_use'   style='cursor:pointer;margin:4px 3px 0px 4px;' ></a--></td>
						</tr>
					</table>
				</div>
				<div style='clear:both;height:10px;'></div>
				";
				//$SelectString .= "<div style='width:100%;border:1px solid blue;'>";
				for($i=0; $i < count($option_tmp_infos) ; $i++){
					//$mdb->fetch($i);
					$SelectString .= "<div id='opnt_ix_".$option_tmp_infos[$i][opnt_ix]."_box' style='clear:both;width:90%;margin:0 auto;border:0px solid blue;display:none;'>";
					$SelectString .= "<div title='".$option_tmp_infos[$i][opnt_ix]."' style='float:left;width:90px;border:1px solid silver;background-color:#efefef;padding:10px 5px;margin:3px'>".$option_tmp_infos[$i][option_name]."</div>";

					$sql = "select * from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL_TMP." where opnt_ix = '".$option_tmp_infos[$i][opnt_ix]."' order by opndt_ix asc ";
					//echo $sql;
					$mdb->query($sql);
					$option_detail_tmp_infos = $mdb->fetchall();

					for($j=0; $j < count($option_detail_tmp_infos) ; $j++){
						$SelectString .= "<div id='opndt_ix_".$option_detail_tmp_infos[$j][opndt_ix]."' opnt_ix='".$option_tmp_infos[$i][opnt_ix]."'  opndt_ix='".$option_detail_tmp_infos[$j][opndt_ix]."' class='make_option_detail_".$option_tmp_infos[$i][opnt_ix]."' option_detail_selected='0' style='float:left;width:90px;border:1px solid silver;background-color:#ffffff;padding:10px 5px;margin:3px;cursor:pointer;' onclick=\"selectTmpOptionDetail('".$option_detail_tmp_infos[$j][opndt_ix]."')\">".$option_detail_tmp_infos[$j][option_div]."</div>";
					}
					//$SelectString .= "<br><br><br> ";
					$SelectString .= "</div>";

				}

				$SelectString .= "
				<div style='clear:both;height:30px;'></div>
				<div class='auto_option_create' style='margin:0 auto;clear:both;width:90px;border:1px solid silver;background-color:#efefef;padding:10px 5px;cursor:pointer;text-align:center;' onclick='MobileMakeStockOption()'>옵션자동생성</div>";
			}else{
				$SelectString .= "
				<div style='width:100%;text-align:center;padding:20px 0;border-bottom:1px solid silver; '><b>등록된 자주쓰는 옵션이 없습니다.</b></div>";
			}
//	}
/*
	$SelectString .= "<div class='stockinfo_loade' style='".(($_SESSION["layout_config"]["mall_use_inventory"] == "Y" && $_SESSION["admininfo"]["admin_level"] == 9) ? "display:block;":"display:none;")."float:left;margin:3px 3px 3px 0px;cursor:pointer;text-align:center;' onclick=\"PoPWindow('../inventory/inventory_search.php',950,480,'inventory_search')\"><img src='../images/korea/stock.gif' alt='재고정보불러오기' title='재고정보불러오기' /></div>";
*/
	return $SelectString;
}

$script_time[end] = time();
if($admininfo[charger_id] == "forbiz"){
	//print_r($script_time);
}

?>
