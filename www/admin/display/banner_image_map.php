<html>
<head>
<link rel="stylesheet" type="text/css" href="/admin/v3/css/common.css">
<script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>
<script type="text/javascript">
//<![CDATA[
$(window).load(function(){
/*탭메뉴 global*/
	$(".tab_img_list").hide()
	$(".tab_img_list").eq(0).show();
	$(".tab_btn_list").hover(function(){
		var img_list=$(".tab_btn_list").index(this);
		$(".tab_img_list").hide();
        $(".tab_btn_list").removeClass("on");
        $(this).addClass("on");
		$(".tab_img_list").eq(img_list).show();
	})
});
//]]>
</script>
</head>
<body>
    <!--image width 500px-->
    <div class='tab' style="margin:0px;">
		<table class='s_org_tab'>
		<col width='550px'>
		<col width='*'>
		<tr>
			<td class='tab'>
				<table id='tab_01' class="tab_btn_list on">
				<tr>
					<th class='box_01'></th>
					<td class='box_02'  >
                        Beauty
                    </td>
					<th class='box_03'></th>
				</tr>
				</table>
				<table id='tab_02' class='tab_btn_list'>
				<tr>
					<th class='box_01'></th>
					<td class='box_02' >
					   Denim
					</td>
					<th class='box_03'></th>
				</tr>
				</table>
				<table id='tab_03' class="tab_btn_list">
				<tr>
					<th class='box_01'></th>
					<td class='box_02'  >
                        Designer
                    </td>
					<th class='box_03'></th>
				</tr>
				</table>
				<table id='tab_04' class="tab_btn_list">
				<tr>
					<th class='box_01'></th>
					<td class='box_02'  >
                        Men
                    </td>
					<th class='box_03'></th>
				</tr>
				</table>
			</td>
		</tr>
		</table>
	</div>
    <div id="beauty" class="tab_img_list" style="display:none;">
        <img src="../images/banner_image_map/resize_beauty_operate_new.jpg" id="beauty_operate" alt="resize_beauty_operate_new.jpg" usemap="#resize_beauty_operate_new.jpg"/>
        <img src="../images/banner_image_map/resize_beauty_preview_new.jpg" id="beauty_preview" alt="resize_beauty_preview_new.jpg" usemap="#resize_beauty_preview_new.jpg"/>
    </div>
    <div id="denim" class="tab_img_list" style="display:none;">
        <img src="../images/banner_image_map/resize_denim_operate_new.jpg" id="denim_operate" alt="resize_denim_operate_new.jpg" usemap="#resize_denim_operate_new.jpg"/>
        <img src="../images/banner_image_map/resize_denim_preview_new.jpg" id="denim_preview" alt="resize_denim_preview_new.jpg" usemap="#resize_denim_preview_new.jpg"/>
    </div>
    <div id="designer" class="tab_img_list" style="display:none;">
        <img src="../images/banner_image_map/resize_designer_operate_new.jpg" id="designer_operate" alt="resize_designer_operate_new.jpg" usemap="#resize_designer_operate_new.jpg"/>
        <img src="../images/banner_image_map/resize_designer_preview_new.jpg" id="designer_preview" alt="resize_designer_preview_new.jpg" usemap="#resize_designer_preview_new.jpg"/>
    </div>
    <div id="men" class="tab_img_list" style="display:none;">
        <img src="../images/banner_image_map/resize_men_operate_new.jpg" id="men_operate" alt="resize_men_operate_new.jpg" usemap="#resize_men_operate_new.jpg"/>
        <img src="../images/banner_image_map/resize_men_preview_new.jpg" id="men_preview" alt="resize_men_preview_new.jpg" usemap="#resize_men_preview_new.jpg"/>
    </div>
    <map name="resize_beauty_operate_new.jpg">
        <area shape="rect" coords="24,19,455,193" href="/admin/display/banner_write.php?banner_ix=106" target="_parent" alt="top_banner" />
        <area shape="rect" coords="24,752,232,901" href="/admin/display/banner_write.php?banner_ix=105" target="_parent" alt="middle_banner_1" />
        <area shape="rect" coords="245,751,458,901" href="/admin/display/banner_write.php?banner_ix=102" target="_parent" alt="middle_banner_2" />
        <area shape="rect" coords="24,1355,115,1437" href="/admin/display/banner_write.php?banner_ix=103" target="_parent" alt="bottom_banner" />
    </map>
    <map name="resize_beauty_preview_new.jpg">
        <area shape="rect" coords="29,21,300,269" href="/admin/display/banner_write.php?banner_ix=104" target="_parent" alt="" />
        <area shape="rect" coords="312,20,465,269" href="/admin/display/banner_write.php?banner_ix=105" target="_parent" alt="" />
        <area shape="rect" coords="31,431,123,510" href="/admin/display/banner_write.php?banner_ix=106" target="_parent" alt="" />
        <area shape="rect" coords="32,879,241,1025" href="/admin/display/banner_write.php?banner_ix=107" target="_parent" alt="" />
        <area shape="rect" coords="252,877,464,1026" href="/admin/display/banner_write.php?banner_ix=100" target="_parent" alt="" />    
    </map>
    <map name="resize_denim_operate_new.jpg">
        <area shape="rect" coords="32,19,474,190" href="/admin/display/banner_write.php?banner_ix=100" target="_parent" alt="" />
        <area shape="rect" coords="32,748,246,898" href="/admin/display/banner_write.php?banner_ix=100" target="_parent" alt="" />
        <area shape="rect" coords="258,748,474,899" href="/admin/display/banner_write.php?banner_ix=100" target="_parent" alt="" />
        <area shape="rect" coords="32,1351,123,1431" href="/admin/display/banner_write.php?banner_ix=100" target="_parent" alt="" />    
    </map>
    <map name="resize_denim_preview_new.jpg">
        <area shape="rect" coords="35,20,445,196" href="/admin/display/banner_write.php?banner_ix=100" target="_parent" alt="" />
        <area shape="rect" coords="35,762,232,913" href="/admin/display/banner_write.php?banner_ix=100" target="_parent" alt="" />
        <area shape="rect" coords="244,762,444,913" href="/admin/display/banner_write.php?banner_ix=100" target="_parent" alt="" />
        <area shape="rect" coords="33,1372,120,1455" href="/admin/display/banner_write.php?banner_ix=100" target="_parent" alt="" />
    </map>
    <map name="resize_designer_operate_new.jpg">
        <area shape="rect" coords="29,20,301,298" href="/admin/display/banner_write.php?banner_ix=100" target="_parent" alt="" />
        <area shape="rect" coords="29,622,119,709" href="/admin/display/banner_write.php?banner_ix=100" target="_parent" alt="" />
        <area shape="rect" coords="27,322,170,506" href="/admin/display/banner_write.php?banner_ix=100" target="_parent" alt="" />
        <area shape="rect" coords="177,324,319,506" href="/admin/display/banner_write.php?banner_ix=100" target="_parent" alt="" />
        <area shape="rect" coords="325,324,466,507" href="/admin/display/banner_write.php?banner_ix=100" target="_parent" alt="" />
    </map>
    <map name="resize_designer_preview_new.jpg">
        <area shape="rect" coords="26,25,302,310" href="/admin/display/banner_write.php?banner_ix=100" target="_parent" alt="" />
        <area shape="rect" coords="23,333,169,518" href="/admin/display/banner_write.php?banner_ix=100" target="_parent" alt="" />
        <area shape="rect" coords="179,331,321,518" href="/admin/display/banner_write.php?banner_ix=100" target="_parent" alt="" />
        <area shape="rect" coords="330,331,475,517" href="/admin/display/banner_write.php?banner_ix=100" target="_parent" alt="" />
        <area shape="rect" coords="25,633,118,724" href="/admin/display/banner_write.php?banner_ix=100" target="_parent" alt="" />
    </map>
    <map name="resize_men_operate_new.jpg">
        <area shape="rect" coords="23,18,471,192" href="/admin/display/banner_write.php?banner_ix=100" target="_parent" alt="" />
        <area shape="rect" coords="23,748,240,894" href="/admin/display/banner_write.php?banner_ix=100" target="_parent" alt="" />
        <area shape="rect" coords="255,749,472,898" href="/admin/display/banner_write.php?banner_ix=100" target="_parent" alt="" />
        <area shape="rect" coords="23,1352,116,1431" href="/admin/display/banner_write.php?banner_ix=100" target="_parent" alt="" />
    </map>
    <map name="resize_men_preview_new.jpg">
        <area shape="rect" coords="30,20,472,195" href="/admin/display/banner_write.php?banner_ix=100" target="_parent" alt="" />
        <area shape="rect" coords="31,762,246,913" href="/admin/display/banner_write.php?banner_ix=100" target="_parent" alt="" />
        <area shape="rect" coords="256,762,471,912" href="/admin/display/banner_write.php?banner_ix=100" target="_parent" alt="" />
        <area shape="rect" coords="31,1377,121,1457" href="/admin/display/banner_write.php?banner_ix=100" target="_parent" alt="" />
    </map>
</body>
</html>