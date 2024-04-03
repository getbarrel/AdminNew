<?
include("../class/layout.class");

$Script = "
<script language='JavaScript' src='../member/member.js'></Script>
<style>
	input {border:1px solid #c6c6c6;padding:3px;}
	.member_table td {text-align:left;}
</style>";

$db = new Database;
$mdb = new Database;

if($place_ix == ''){
	$act = "insert";
}else{
	$act = "update";

	$sql = "select 
				* 
			from 
				shop_event_place ep 
			where 
				place_ix = '".$place_ix."' ";
	//echo $sql ;

	$db->query($sql);
	$db->fetch();
}


$Contents .= "
	<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
			<tr >
				<td align='left' colspan=2> ".GetTitleNavigation("플레이스관리", "이벤트관리 > 플레이스관리", false)."</td>
			</tr>

			";
			if($act == "update"){
				$Contents .= " <tr height=30><td class='p11 ls1' style='padding:0 0 0 0px;text-align:left;' > <b>".Black_list_check($db->dt[code],$db->dt[name])."</b> 님의 회원정보 입니다.</td></tr>";
			}else{
				$Contents .= " <tr height=30><td class='p11 ls1' style='padding:0 0 0 0px;text-align:left;' > 플레이스 정보를 등록후 오프라인 이벤트 설정시 플레이스를 지정하실수 있습니다.</td></tr>";
			}
$Contents .= "
	</table>
";
  

$Contents .= "
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<TR>
		<td align=center colspan=2 valign=top>
		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
			<tr >
				<td align='left' colspan=2> ".GetTitleNavigation("플레이스관리", "이벤트관리 > 플레이스관리", false)."</td>
			</tr>
			<tr>
				<td align=center style='padding: 0 0px 0 0px;height:569px;vertical-align:top'>
				<form name='event_place_form' method='post' onsubmit='return CheckFormValue(this)' action='event_place.act.php'  target='act'>
				<input type='hidden' name='act' value='".$act."'>
				<input type='hidden' name='place_ix' value='".$db->dt[place_ix]."'> 
				<input type='hidden' name='place_latitude' id='place_latitude' value='".$db->dt[place_latitude]."'> 
				<input type='hidden' name='place_longitude' id='place_longitude' value='".$db->dt[place_longitude]."'> 
				<table border='0' width='100%' cellspacing='1' cellpadding='0'>
					<tr>
						<td >
							<table border='0' width='100%' cellspacing='0' cellpadding='0' class='member_table input_table_box' style='text-align:left;'>
								<col width=15%>
								<col width=35%>
								<col width=15%>
								<col width=35%>";
$Contents .= "					 
								<tr>
									<td class='input_box_title' nowrap> 플레이스 이름 <img src='".$required3_path."'></td>
									<td class='input_box_item' colspan=3> <input type='text' class='textbox' name='place_name' size='27' maxlength='20' value='".$db->dt[place_name]."' validation=true title='플레이스 이름'></td>
								</tr>
								<tr>
									<td class='input_box_title' nowrap> 우편번호</td>
									<td class='input_box_item' colspan=3>

										<table border='0' cellpadding='0' cellspacing='0' >";
						if($db->dt[mem_type] == "F"){
						  $Contents .= "<tr>
											<td style='border:0px;'>
												<input type='text' class='textbox' name='place_zip' id='zipcode1' size='10' maxlength='10' value='".$db->dt[place_zip]."'>
											</td>
										</tr>";
						}else{
						  $Contents .= "<tr>
											<td style='border:0px;'>
												<input type='text' class='textbox' name='place_zip' id='zipcode1' size='7' maxlength='7' value='".$db->dt[place_zip]."' readonly>
											</td>
											<td style='border:0px;padding:0px 0 0 5px;'>
												<img src='../images/".$admininfo["language"]."/btn_search_address.gif' onclick=\"search_Postcode();\" style='cursor:pointer;' align=absmiddle>
											</td>
										</tr>";
						}
						  $Contents .= "</table>
									</td> 
								</tr>
								<tr height=50>
									<td class='input_box_title'2 nowrap> 주소</td>
									<td bgcolor='#ffffff' colspan=3 style='padding:10px;'>";
						if($db->dt[mem_type] == "F"){
							$Contents .= "<input type='text' class='textbox' name='place_addr1' id='addr1' size='66' maxlength='80' value='".$db->dt[place_addr1]."' style='margin:2px 0px'>";

						}else{
							$Contents .= "<input type='text' class='textbox' name='place_addr1' id='addr1' size='66' maxlength='80' value='".$db->dt[place_addr1]."' style='margin:2px 0px' readonly><br>
										<input type='text' class='textbox' name='place_addr2' id='addr2' size='66' maxlength='80' value='".$db->dt[place_addr2]."' style='margin:2px 0px'> 세부주소

										<input type='text' name='doro_addr' id = 'doro_addr' value='".$db->dt[doro_addr1]."' style='width:391px; color:red; border:0px;padding:0px; margin:0px;' readonly>
										<input type='hidden' name='doro_addr1' id='doro_addr1' value='".$db->dt[doro_addr1]."' >
										<input type='hidden' name='doro_addr2' id='doro_addr2' value='".$db->dt[doro_addr2]."' >";
						}
						$Contents .= "</td>
								</tr>
								";

 
				$Contents .= "
								<tr>
									<td class='input_box_title' nowrap> 펜스반경 <img src='".$required3_path."'></td>
									<td class='input_box_item' colspan=3>
										<select id='place_radius' name='place_radius' validation=true title='펜스반경'>
											<option value='' ".($db->dt[place_radius] == "" ? "selected":"").">펜스반경</option>
											<option value='100' ".($db->dt[place_radius] == "100" ? "selected":"").">반경 100m</option>
											<option value='200' ".($db->dt[place_radius] == "200" ? "selected":"").">반경 200m</option> 
											<option value='300' ".($db->dt[place_radius] == "300" ? "selected":"").">반경 300m</option> 
											<option value='400' ".($db->dt[place_radius] == "400" ? "selected":"").">반경 400m</option> 
											<option value='500' ".($db->dt[place_radius] == "500" ? "selected":"").">반경 500m</option> 
										</select>
									</td> 
								</tr>";
			 
 

$Contents .= "		<tr>
								<td class='input_box_title' ></td>
								<td class='input_box_item' colspan=3>
									<input type='text' class='textbox' placeholder='' style='width:95%;' id='disabledinput' class='form-control mt10'  disabled='' />
								</td>
							</tr>
							</table>	 
							<table>
							<tr>
								<th></th>
								<td>
									<div id='map' style='width:1200px;height:350px;margin-top:10px;'></div>
									<script type='text/javascript' src='//apis.daum.net/maps/maps3.js?apikey=8ac1bf69d5e0023d65448b9b87a21799&libraries=services'></script>
									<script type='text/javascript'>

										var radius = $('#place_radius').val();
										var place_latitude = $('#place_latitude').val();
										var place_longitude = $('#place_longitude').val();

										if (place_latitude == '' || place_longitude == ''){
											place_latitude = '37.482674';
											place_longitude = '127.039310';
										}

										var mapContainer = document.getElementById('map'), // 지도를 표시할 div 
										mapOption = {
											center: new daum.maps.LatLng(place_latitude, place_longitude), // 지도의 중심좌표
											level: 4, // 지도의 확대 레벨
											mapTypeId : daum.maps.MapTypeId.ROADMAP // 지도종류
										}; 
										
										// 지도를 생성한다 

										var map = new daum.maps.Map(mapContainer, mapOption); 

										// 지도에 표시할 원을 생성합니다
										var circle = new daum.maps.Circle({
											map: map, // 원을 표시할 지도 객체
											center : new daum.maps.LatLng(place_latitude, place_longitude), // 지도의 중심 좌표
											radius : radius, // 원의 반지름 (단위 : m)
											fillColor: '#42c4f4', // 채움 색
											fillOpacity: 0.3, // 채움 불투명도
											strokeWeight: 1, // 선의 두께
											strokeColor: '#f9f3f3', // 선 색
											strokeOpacity: 0.5, // 선 투명도 
											strokeStyle: 'solid' // 선 스타일
										});

										// 지도에 원을 표시합니다 
										circle.setMap(map); 

										// 지도에 마커를 생성하고 표시한다
										var marker = new daum.maps.Marker({
											position: new daum.maps.LatLng(place_latitude, place_longitude), // 마커의 좌표
											draggable : true, // 마커를 드래그 가능하도록 설정한다
											map: map // 마커를 표시할 지도 객체
										});
										
										$('#place_radius').change(function(){
											circle.setRadius( $(this).val() );
										});
										
										makerMap(place_latitude, place_longitude);
										
										// 출발 마커에 dragend 이벤트를 등록합니다
										daum.maps.event.addListener(marker, 'dragend', function() {
											 // 출발 마커의 드래그가 종료될 때 마커 이미지를 원래 이미지로 변경합니다
											//startMarker.setImage(startImage);
											var position = marker.getPosition();

											//console.log(position);
											$('#place_latitude').val(position.Ab);
											$('#place_longitude').val(position.zb);

											var geocoder = new daum.maps.services.Geocoder();

											// 현재 지도 중심좌표로 주소를 검색해서 지도 좌측 상단에 표시합니다
											searchAddrFromCoords(position, displayCenterInfo);

											function searchAddrFromCoords(coords, callback) {
												// 좌표로 행정동 주소 정보를 요청합니다
												geocoder.coord2addr(coords, callback);         
											}

											// 지도 좌측상단에 지도 중심좌표에 대한 주소정보를 표출하는 함수입니다
											function displayCenterInfo(status, result) {
												if (status === daum.maps.services.Status.OK) {
													$('#disabledinput').attr('placeholder', result[0].fullName);
												}    
											}
											
											makerMap(position.Ab, position.zb);

										});

										function makerMap(place_latitude, place_longitude){
											var position = new daum.maps.LatLng(place_latitude, place_longitude);

											map.setCenter(position);
											marker.setPosition(position);
											circle.setPosition(position);
										}
									</script>
									<p class='text-primary mt5'>! 주소의 위치 표시가 실제와 다르다면 지도상의 핀을 움직여 변경 할 수 있습니다.</p>
								</td>
							</tr>
							</table>	 
								<!-- 항목 설정에 따라서 항목을 뿌려줌 시작 kbk --> 

							<table width='100%' border='0' padding=10>
								<tr>
									<td align='left'>
										 
									</td>
									<td align='right'>";
									if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
										$Contents.="
										<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' align=absmiddle>";
									}else{
										$Contents.="
										<a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' align=absmiddle></a>";
									}
									$Contents.="
									</td>
								</tr>
							</table>
				<!-- 수정마침 -->
						</td>
					</tr>
				</table>
			</td>
		</tr>
		</table>
		</form>
	</td>
</tr>
</TABLE>"; 

$Script .="<script src='http://dmaps.daum.net/map_js_init/postcode.v2.js'></script>
<script src='http://ajax.aspnetcdn.com/ajax/jquery.validate/1.14.0/jquery.validate.min.js' ></script>
<SCRIPT type='text/javascript'>

//우편번호 검색
	function search_Postcode() {
        new daum.Postcode({
            oncomplete: function(data) {
                // 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

                // 각 주소의 노출 규칙에 따라 주소를 조합한다.
                // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
                var fullAddr = ''; // 최종 주소 변수
                var extraAddr = ''; // 조합형 주소 변수

                // 사용자가 선택한 주소 타입에 따라 해당 주소 값을 가져온다.
                if (data.userSelectedType === 'R') { // 사용자가 도로명 주소를 선택했을 경우
                    fullAddr = data.roadAddress;

                } else { // 사용자가 지번 주소를 선택했을 경우(J)
                    fullAddr = data.jibunAddress;
                }

                // 사용자가 선택한 주소가 도로명 타입일때 조합한다.
                if(data.userSelectedType === 'R'){
                    //법정동명이 있을 경우 추가한다.
                    if(data.bname !== ''){
                        extraAddr += data.bname;
                    }
                    // 건물명이 있을 경우 추가한다.
                    if(data.buildingName !== ''){
                        extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                    }
                    // 조합형주소의 유무에 따라 양쪽에 괄호를 추가하여 최종 주소를 만든다.
                    fullAddr += (extraAddr !== '' ? ' ('+ extraAddr +')' : '');
                }

                //document.getElementById('sido').value = data.sido;
                //document.getElementById('sigungu').value = data.sigungu;

                // 우편번호와 주소 정보를 해당 필드에 넣는다.
                document.getElementById('addr1').value = fullAddr;
				document.getElementById('zipcode1').value = data.postcode;

                document.getElementById('disabledinput').setAttribute('placeholder', fullAddr);


				var geocoder = new daum.maps.services.Geocoder();

				// 주소로 좌표를 검색
                geocoder.addr2coord(data.address, function(status, result) {
                    // 정상적으로 검색이 완료됐으면
                    if (status === daum.maps.services.Status.OK) {
                        // 해당 주소에 대한 좌표를 받아서
                        var coords = new daum.maps.LatLng(result.addr[0].lat, result.addr[0].lng);

						$('#place_latitude').val(result.addr[0].lat);
						$('#place_longitude').val(result.addr[0].lng);
                        
						makerMap(result.addr[0].lat, result.addr[0].lng);
                    }
                });

                // 커서를 상세주소 필드로 이동한다.
                document.getElementById('address2').focus();
            }
        }).open();
    }
	
	

	function oneSubmit(act, ix, admin_type) {
		if(act == 'update') {
			window.open('/admin/member/addressbook_add_pop.php?act='+act+'&ix='+ix, 'actpop', 'width=700,height=470,resizeble=yes');
		} else if(act == 'delete') {
			if(confirm('삭제하시면 복구할 수 없습니다. 정말로 삭제하시겠습니까?')) {
				window.location.href='/mypage/addressbook.act.php?act='+act+'&ix='+ix+'&admin_type='+admin_type;
			}
		}
	}
</SCRIPT>
";

if($mmode == "pop"){
    $P = new ManagePopLayOut();
    $P->addScript = "<script language='javascript' src='../basic/company.add.js'></script>".$Script;
    $P->Navigation = "이벤트관리 > 플레이스관리";
    $P->NaviTitle = "플레이스관리";
    $P->title = "플레이스관리";
    $P->strContents = $Contents;
    echo $P->PrintLayOut();
}else if($mmode == "personalization"){
	$P = new ManagePopLayOut();
	$P->addScript = "<script language='javascript' src='../basic/company.add.js'></script>".$Script;
    $P->Navigation = "이벤트관리 > 플레이스관리";
	if($info_type == "shipping_addr"){
	$P->NaviTitle = "배송지관리";
	 $P->title = "배송지관리";
	}else{
    $P->NaviTitle = "플레이스관리";
	 $P->title = "플레이스관리";
	}

	$P->strContents = $Contents;
	$P->layout_display = false;
	$P->view_type = "personalization";
	echo $P->PrintLayOut();
}else{
    $P = new LayOut();
    $P->addScript = "<script language='javascript' src='../basic/company.add.js'></script>".$Script;
    $P->Navigation = "이벤트관리 > 플레이스관리";
    $P->NaviTitle = "플레이스관리";
    $P->title = "플레이스관리";
    $P->strLeftMenu = mshop_menu();
    $P->strContents = $Contents;
    echo $P->PrintLayOut();
}

?>
