<? 
include("../class/layout.work.class");
include("gallery.lib.php");

$db = new Database;



$Script = "
<style type='text/css'> 
  div#drop_relation_product { width:100%;height:100%;overflow:auto;padding:1px;border:1px solid red }
  td#drop_relation_product { width:100%;height:100%;overflow:auto;padding:1px;border:0px solid red;vertical-align:top }

  div#drop_relation_product.hover { border:5px dashed #aaa; background:#efefef; }
  table.tb {width:100%;cursor:move;}
  div.draggable {border:0px solid silver;}
</style>
<script type='text/javascript' src='relationAjax.js'></script>
<Script Language='JavaScript'>
function SubmitX(frm){
	//alert(iView.document.body.innerHTML);
	frm.content.value = iView.document.body.innerHTML;	
	return true;
}




function init(){
	var frm = document.main_frm;
	Content_Input();
	Init(frm);
	onLoadDate('$sDate','$eDate');	
}

function onDropAction(mode, main_ix,pid)
{
	//outTip(img3);
	//alert(1);
	parent.document.frames['act'].location.href='./relation.category.act.php?mode='+mode+'&main_ix='+main_ix+'&pid='+pid; 
	
}

</Script>";

$gdb = new Database;
$gdb->query("SELECT * FROM deepzoom_gallery_info where dgi_ix ='".$dgi_ix."'  limit 1");

if($gdb->total){
	$act = "update";
}else{
	$act = "insert";
}

$Contents = "
<table width='100%' border='0' align='left'>
 <tr>
	<td align='left' colspan=6 > ".GetTitleNavigation("갤러리 관리", "이미지 관리 > 갤러리 관리 ")."</td><!--<a onClick=\"PoPWindow('/admin/_manual/manual.php?config=몰스토리동영상메뉴얼_기획전등록(090322)_config.xml',800,517,'manual_view')\"  title='이벤트/기획전 동영상 메뉴얼입니다'><img src='../image/movie_manual.gif' align=absmiddle></a>-->
</tr>
  <tr>
    <td>
      
        <form name='main_frm' method='post' onsubmit='return CheckFormValue(this)'  action='gallery.act.php' style='display:inline;' enctype='multipart/form-data'>
		<input type='hidden' name=act value='".$act."'>
		<input type='hidden' name=dgi_ix value='".$dgi_ix."'>
        <table border='0' width='100%' cellspacing='1' cellpadding='0'>
          <tr>
            <td bgcolor='#6783A8'>
              <table border='0' cellspacing='0' cellpadding='0' width='100%'>
                <tr>
                  <td bgcolor='#ffffff'>
                    <table border='0' cellpadding=3 cellspacing=0 width='100%'>
                      
                   
                    <tr> 
                      <td  colspan='4' style='padding:10px;'>";


if($gdb->total){	
	$gallery_total = $gdb->total-1;
	for($i=0;$i < $gdb->total;$i++){
	$gdb->fetch($i);
$Contents .= "
                      <div id='gallery_info_area".$i."' gallery_code='".($i+1)."'>
                      <div style='padding:10px 10px'><img src='/admin/images/dot_org.gif' > <b style='font-family:굴림;font-size:13px;font-weight:bold;letter-spacing:-1px;word-spacing:0px;'>갤러리 등록</b> </div>
                      <table width='100%' border='0' cellpadding='10' cellspacing='1' bgcolor='#E9E9E9'>        
								        <col width='135px'>
										<col width='*'>
										<tr bgcolor='#ffffff'>          
								          <td  bgcolor='#ffffff'><img src='/admin/image/ico_dot.gif'> <b>갤러리명</b></td>
								          <td>
								          <input type='text' class='textbox' name='gallery_name[".($i+1)."]' id='gallery_name_".($i+1)."' size=50 validation=true title='갤러리명' value='".$gdb->dt[gallery_name]."'>
								          </td>
								        </tr>
								        <tr bgcolor='#ffffff'>          
								          <td bgcolor='white'><img src='/admin/image/ico_dot.gif'> <b>전시여부</b></td>
								          <td>
								          <input type='radio' class='textbox' name='use_yn[".($i+1)."]' id='use_".($i+1)."_y' size=50 value='Y' style='border:0px;' ".($gdb->dt[use_yn] == "Y" ? "checked":"")."><label for='use_".($i+1)."_y'>전시</label>
								          <input type='radio' class='textbox' name='use_yn[".($i+1)."]' id='use_".($i+1)."_n' size=50 value='N' style='border:0px;' ".($gdb->dt[use_yn] == "N" ? "checked":"")."><label for='use_".($i+1)."_n'>전시 하지 않음</label>
								          </td>
								        </tr>
								       
										<tr bgcolor='#ffffff'>          
								          <td  bgcolor='#ffffff'><img src='/admin/image/ico_dot.gif'> <b>갤러리 상세 링크</b></td>
								          <td>
								          <input type='text' class='textbox' name='gallery_link[".($i+1)."]' id='gallery_link_".($i+1)."' size=50 value='".$gdb->dt[gallery_link]."'>
								          </td>
								        </tr>
										<tr bgcolor='#ffffff'>          
								          <td  bgcolor='#ffffff'><img src='/admin/image/ico_dot.gif'> <b>이미지 노출갯수</b></td>
								          <td>
								          <input type='text' class='textbox' name='gallery_disp_cnt[".($i+1)."]' id='gallery_disp_cnt_".($i+1)."' size=10 value='".$gdb->dt[gallery_disp_cnt]."'>
								          </td>
								        </tr>
								        <tr bgcolor='#ffffff'>          
								          <td bgcolor='white'><img src='/admin/image/ico_dot.gif'> <b>전시타입</b></td>
								          <td style='float:left'>
								          <div style='float:left;text-align:center;width:130px;'>
								          <img src='/admin/images/g_5.gif' align=center onclick=\"document.getElementById('display_type_".($i+1)."_0').checked = true;\"><br>
								          <input type='radio' class='textbox' name='display_type[".($i+1)."]' id='display_type_".($i+1)."_0' value='0' style='border:0px;' ".($gdb->dt[display_type] == "0" ? "checked":"")."><label for='display_type_".($i+1)."_0'>기본형(5EA 배열)</label>
								          </div>
								          <div style='float:left;text-align:center;width:130px;'>
								          <img src='/admin/images/g_4.gif' align=center onclick=\"document.getElementById('display_type_".($i+1)."_1').checked = true;\"><br>
								          <input type='radio' class='textbox' name='display_type[".($i+1)."]' id='display_type_".($i+1)."_1' value='1' style='border:0px;' ".($gdb->dt[display_type] == "1" ? "checked":"")."><label for='display_type_".($i+1)."_1'>기본형(4EA 배열)</label>
								          </div>
								          <div style='float:left;text-align:center;width:130px;'>
								          <img src='/admin/images/g_3.gif' align=center onclick=\"document.getElementById('display_type_".($i+1)."_2').checked = true;\"><br>
								          <input type='radio' class='textbox' name='display_type[".($i+1)."]' id='display_type_".($i+1)."_2' value='2' style='border:0px;' ".($gdb->dt[display_type] == "2" ? "checked":"")."><label for='display_type_".($i+1)."_2'>기본형2(3EA 배열)</label>
								          </div>
								          <div style='float:left;text-align:center;width:135px;'>
								          <img src='/admin/images/slide_4.gif' align=center onclick=\"document.getElementById('display_type_".($i+1)."_3').checked = true;\"><br>
								          <input type='radio' class='textbox' name='display_type[".($i+1)."]' id='display_type_".($i+1)."_3' value='3' style='border:0px;' ".($gdb->dt[display_type] == "3" ? "checked":"")."><label for='display_type_".($i+1)."_3'>슬라이드형(4EA 배열)</label>
								          </div>
								          <div style='float:left;text-align:center;width:135px;'>
								          <img src='/admin/images/g_16.gif' align=center onclick=\"document.getElementById('display_type_".($i+1)."_4').checked = true;\"><br>
								          <input type='radio' class='textbox' name='display_type[".($i+1)."]' id='display_type_".($i+1)."_4' value='4' style='border:0px;' ".($gdb->dt[display_type] == "4" ? "checked":"")."><label for='display_type_".($i+1)."_4'>기본형4(1/*EA 배열)</label>
								          </div>
								          ";
								          
//$Contents .= SelectFileList2($DOCUMENT_ROOT.$admin_config[mall_data_root]."/module/main_templet/")."

$Contents .= "
								          </td>
								        </tr>
								        <tr bgcolor='#ffffff'>          
								          <td bgcolor='white'><img src='/admin/image/ico_dot.gif'> <b>선택된 이미지</b></td>
								          <td> <a onclick=\"$('#relation_product_area').toggle();\"><img src='/admin/images/btn_goods_search_add.gif' border=0 align=absmiddle></a><br>
								          <div style='width:100%;height:65px;padding:5px;clear:right;' id='gallery_product_area_".($i+1)."' >".relationGalleryImageList($gdb->dt[dgi_ix], "clipart")."</div>
								          <div class=small>* 이미지를 선택하신 후 방향키로 노출 순서를 조정하실수 있습니다.</div>
								          </td>
								        </tr>
								      </table><br><br>
								      </div>";
	}
}else{
	$gallery_total = 0;
$Contents .= "       <div id='gallery_info_area0' gallery_code='1'>
                      <div style='padding:10px 10px'><img src='/admin/images/dot_org.gif'> <b style='font-family:굴림;font-size:13px;font-weight:bold;letter-spacing:-1px;word-spacing:0px;'>갤러리 등록 </b> <!--삭제버튼--></div>
                      <table width='100%' border='0' cellpadding='10' cellspacing='1' bgcolor='#E9E9E9'>        
								        <tr bgcolor='#ffffff'>          
								          <td width=140 bgcolor='white'><img src='/admin/image/ico_dot.gif'> <b>갤러리명</b></td>
								          <td>
								          <input type='text' class='textbox' name='gallery_name[1]' id='gallery_name_1' size=50 validation=true title='갤러리명'  value=''>
								          </td>
								        </tr>
								        <tr bgcolor='#ffffff'>          
								          <td bgcolor='white'><img src='/admin/image/ico_dot.gif'> <b>전시여부</b></td>
								          <td>
								          <input type='radio' class='textbox' name='use_yn[1]' id='use_1_y' size=50 value='Y' style='border:0px;' validation=false title='전시여부' ><label for='use_1_y'>전시</label>
								          <input type='radio' class='textbox' name='use_yn[1]' id='use_1_n' size=50 value='N' style='border:0px;' validation=false title='전시여부' checked><label for='use_1_n'>전시 하지 않음</label>
								          </td>
								        </tr>
								        <tr bgcolor='#ffffff'>          
								          <td  bgcolor='#ffffff'><img src='/admin/image/ico_dot.gif'> <b>갤러리 상세 링크</b></td>
								          <td>
								          <input type='text' class='textbox' name='gallery_link[1]' id='gallery_link_1' size=50 value=''>
								          </td>
								        </tr>
								        <tr bgcolor='#ffffff'>          
								          <td bgcolor='white'><img src='/admin/image/ico_dot.gif'> <b>갤러리 타입</b></td>
								          <td>
								          <div style='float:left;text-align:center;width:130px;'>
								          <img src='/admin/images/g_5.gif' align=center onclick=\"document.getElementById('display_type_1_0').checked = true;\"><br>
								          <input type='radio' class='textbox' name='display_type[1]' id='display_type_1_0' value='0' style='border:0px;' checked><label for='display_type_1_0'>기본형(5EA 배열)</label>
								          </div>
								          <div style='float:left;text-align:center;width:130px;'>
								          <img src='/admin/images/g_4.gif' align=center onclick=\"document.getElementById('display_type_1_1').checked = true;\"><br>
								          <input type='radio' class='textbox' name='display_type[1]' id='display_type_1_1' value='1' style='border:0px;' checked><label for='display_type_1_1'>기본형(4EA 배열)</label>
								          </div>
								          <div style='float:left;text-align:center;width:130px;'>
								          <img src='/admin/images/g_3.gif' align=center onclick=\"document.getElementById('display_type_1_2').checked = true;\"><br>
								          <input type='radio' class='textbox' name='display_type[1]' id='display_type_1_2' value='2' style='border:0px;' ><label for='display_type_1_2'>기본형2(3EA 배열)</label>
								          </div>
								          <div style='float:left;text-align:center;width:135px;'>
								          <img src='/admin/images/slide_4.gif' align=center onclick=\"document.getElementById('display_type_1_3').checked = true;\"><br>
								          <input type='radio' class='textbox' name='display_type[1]' id='display_type_1_3' value='3' style='border:0px;' ><label for='display_type_1_3'>슬라이드형(4EA 배열)</label>
								          </div>
								          <div style='float:left;text-align:center;width:135px;'>
								          <img src='/admin/images/g_16.gif' align=center onclick=\"document.getElementById('display_type_1_4').checked = true;\"><br>
								          <input type='radio' class='textbox' name='display_type[1]' id='display_type_1_4' value='4' style='border:0px;' ><label for='display_type_1_4'>기본형4(1/*EA 배열)</label>
								          </div>
								          ";
								          
//$Contents .= SelectFileList2($DOCUMENT_ROOT.$admin_config[mall_data_root]."/module/main_templet/")."

$Contents .= "
								          </td>
								        </tr>
								        <tr bgcolor='#ffffff'>          
								          <td width=140 bgcolor='white'><img src='/admin/image/ico_dot.gif'> <b>전시갯수</b></td>
								          <td>
								          <input type='text' class='textbox' name='gallery_disp_cnt[1]' id='gallery_disp_cnt_1' size=10 validation=true title='전시갯수' value=''>
										  노출하시고자 하는 이미지의 갯수를 선택해주세요. 사진이 많이 등록되었더라도 입력된 숫자만큼만 노출되게 됩니다.
								          </td>
								        </tr>
								        <tr bgcolor='#ffffff'>          
								          <td bgcolor='white'><img src='/admin/image/ico_dot.gif'> <b>전시상품</b></td>
								          <td> <a onclick=\"$('#relation_product_area').toggle();\"><img src='/admin/images/btn_goods_search_add.gif' border=0 align=absmiddle></a><br>
								          <div style='height:65px;padding:5px;' id='gallery_product_area_1' >".relationGalleryImageList($gdb->dt[dgi_ix], "clipart")."</div>
								          </td>
								        </tr>
								      </table><br><br>
								      </div>";
}
$Contents .= "	      
	                     <!-- 카테고리 및 상품 검색 인터페이스 -->
							<div class='doong' id='relation_product_area' onselectstart='return false;' style='z-index:10;position:absolute;display:none;vertical-align:top;height:auto;width:800px;border:4px solid silver;padding:5px;background-color:#ffffff'   >
							<table bgcolor=#ffffff border=0 cellpadding=0 cellspacing=0 width=100%' >
							<tr height=25 >
								<td width='15%' style='padding-right:5px;' valign=top>
									<div class='tab' style='margin: 0px 0px ;'>
										<table class='s_org_tab'>
										<tr>
											<td class='tab'>
												<table id='tab_01' class='on' >
												<tr>
													<th class='box_01'></th>
													<td class='box_02 small' onclick=\"showTabContents('category_search','tab_01')\" style='padding-left:5px;padding-right:5px;'>카테고리검색</td>
													<th class='box_03'></th>
												</tr>
												</table>
												<table id='tab_02'>
												<tr>
													<th class='box_01'></th>
													<td class='box_02 small' onclick=\"showTabContents('keyword_search','tab_02')\" style='padding-left:5px;padding-right:5px;'>키워드검색</td>
													<th class='box_03'></th>
												</tr>
												</table>
												
											</td>
											<td class='btn'>						
												
											</td>
										</tr>
										</table>
									</div>
									<div class='t_no' style='margin: 2px 0px ; '>
										<div class='my_box' >
											<div id='category_search' style='overflow:auto;height:370px;width:200px;border:1px solid silver'><iframe  src='image.group.php' width=100% height=100% frameborder=0 ></iframe></div>
											<div id='keyword_search' style='display:none;height:360px;width:200px;border:1px solid silver;padding-top:10px;'>
												
												<table align=center>
													<tr>
														<td bgcolor='#efefef' align=center>입점업체</td>
														<td>
															".CompanyList($company_id,"","")."
														</td>
													</tr>
													<tr>
														<td>
															<select name='search_type' id='search_type'>
																<option value='p.pname'>상품명</option>
																<option value='p.pcode'>상품코드</option>
																<option value='brand_name'>브랜드명</option>
															</select>
														</td>
														<td><input type='text' name='search_text' id='search_text' size='15'onkeypress=\"if(event.keyCode == 13){SearchProduct(document.main_frm);return false;}\" ></td>
													</tr>
													<tr>												
														<td colspan=2 align=right><img src='../image/search01.gif' onclick=\"SearchProduct(document.main_frm);\"></td>
													</tr>											
													</table>
													
											</div>								
										</div>
									</div>
									</td>
									<td colspan=2 width='100%' height=100% valign=top>						
											<table border=0 cellpadding=0 cellspacing=1 bgcolor='silver' width=100% height=100% >
												<tr height=25 bgcolor='#ffffff'>
													<td style='padding:1px;padding-left:10px;padding-right:10px;' align=center >
													<table width='100%' height='100%' >
													<tr>
														<td align=left width='10' ><input type=hidden id='cpid' value=''><input type=checkbox name='all_fix' onclick='fixAll(document.main_frm)' >
														
														</td>
														<td  align=right>
														<img src='../image/btn_selected_reg.gif' border='0' align='left' onclick='selectGoodsList(document.main_frm);' style='cursor:hand;'>																		
														<!--img src='../image/btn_searched_reg.gif' border='0' align='right'-->
														<select name='list_max' id='list_max' align=right onchange='getRelationProduct(_mode,_nset, _page,_cid,_depth);'>
															<option value='3'>3</option>
															<option value='5' >5</option>
															<option value='10' selected>10</option>
															<option value='15'>15</option>
															<option value='20'>20</option>
															<option value='30' >30</option>
															<option value='40'>40</option>
															<option value='50'>50</option>
															<option value='100'>100</option>
														</select>
														</td>
													</tr>
													</table>
													</td>
													<td style='padding:0 0 0 5' rowspan=3></td>
													<td style='padding:1px;padding-left:10px;padding-right:10px;' align=center >
													<b>선택된 상품</b>
													</td>
												</tr>
												<tr height='340px' bgcolor='#ffffff'>
													<td width=50%>
													<div id='reg_product' style='width:100%;height:100%;width:100%;padding:1px;padding-left:0px;padding-right:10px;' align=center >
													<table width=100% height=100% border=0><tr><td align=center class='small'>좌측카테고리를 선택해주세요</td></tr></table>
													</div>
													</td>
													<td width=50% height=100% style='vertical-align:top;padding:0 0 0 0' id='drop_relation_product' >
														<div  >".relationGalleryImageList($dgi_ix)."</div>
													</td>
												</tr>
												<tr height='29px' bgcolor='#ffffff' >
													<td id='view_paging' style='padding:1px;padding-left:0px;padding-right:2px;border-top:0px;' align=center >
													
													</td>																		
													<td style='padding:1px;padding-left:4px;padding-right:2px;border-top:0px;'>
													<img src='../image/btn_whole_del.gif' border='0' align='left' onclick='deleteWhole(true);' style='cursor:hand;margin:0 2px 0 0'>
													<a onclick=\"$('#relation_product_area').toggle();\"><img src='../images/btn_win_close.gif' border='0' align='left' ></a>
													</td>
													</tr>
											</table>
									<!--/div-->
									</td>
							</tr>					
							</table>
							</div>
							<!-- 카테고리 및 상품 검색 인터페이스 -->
                      </td>
                    </tr>
                    
                    <tr><td colspan=3 align=right style='padding:10px;'><input type=image src='../image/b_save.gif' border=0> </td></tr>
                  </table>
                       
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
        </form>
    </td>
  </tr>
    	
  ";
$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >작업시에는 표시하지 않음으로 선택후 작업하시기 바랍니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >갤러리그룹은 원하는 만큼 추가해서 관리 하실 수 있습니다.</td></tr>	
	
	</td></tr>
</table>
";


//$help_text = HelpBox("이벤트/기획전  관리", $help_text);	 
$help_text = "<div style='position:relative;z-index:100px;'>".HelpBox("<div style='position:relative;top:4px;'><table><tr><td valign=bottom><b>갤러리관리</b></td><td></td></tr></table></div>", $help_text,220)."</div>";//<!--a onClick=\"PoPWindow('/admin/_manual/manual.php?config=몰스토리동영상메뉴얼_기획전등록(090322)_config.xml',800,517,'manual_view')\"  title='이벤트/기획전 동영상 메뉴얼입니다'><img src='../image/movie_manual.gif' align=absmiddle></a-->

$Contents .= "
  <tr>
    <td align='left' style='padding-bottom:200px;'>    
    $help_text

    </td>
  </tr>";

$Contents .= "		
	</table>


<form name='lyrstat'><input type='hidden' name='opend' value=''></form>
";



$Script = "<script type='text/javascript' src='../work/js/jquery-ui-1.8.6.custom.min.js'></script>
<script type='text/javascript' src='../work/js/ui/ui.core.js'></script>
<script language='JavaScript' src='../js/dd.js'></script>\n
<!--script language='JavaScript' src='../js/mozInnerHTML.js'></script-->\n
<script type='text/javascript' src='../work/js/ui/jquery.ui.droppable.js'></script>
$Script";
$P = new LayOut();
$P->addScript = $Script;
$P->Navigation = "HOME > 이미지관리 > 이미지 갤러리";
$P->OnloadFunction = "";//showSubMenuLayer('storeleft');
$P->strLeftMenu = deepzoom_menu();
$P->right_menu = "";
$P->strContents = $Contents;
echo $P->PrintLayOut();



function FileList2 ( $path , $maxdepth = -1 , $mode = "FULL" , $d = 0 ){
global $page_name;
   if ( substr ( $path , strlen ( $path ) - 1 ) != '/' ) { $path .= '/' ; }      
   $dirlist = array () ;
   //if ( $mode != "FILES" ) { $dirlist[] = $path ; }
   if(!is_dir($path)){return false;};
   if ( $handle = opendir ( $path ) )
   {
   	
       while ( false !== ( $file = readdir ( $handle ) ) )
       {
           if ( $file != '.' && $file != '..' )
           {
               $only_file = $file;
               $file = $path . $file ;
               if ( ! is_dir ( $file ) || $mode == "FULL"){    
               		if(is_dir ( $file )){
               			//$mstring .=  "<option value='".$only_file ."'>".$only_file ."</option>";
               		}else{
               			if($page_name == $only_file){
               				$mstring .=  "<option value='".$only_file ."' selected>".$only_file ."</option>";
               			}else{
               				$mstring .=  "<option value='".$only_file ."'>".$only_file ."</option>";
               			}
               		}
               		
               }elseif ($d >=0 && ($d < $maxdepth || $maxdepth < 0) ){
                   $mstring .= FileList2 ( $file . '/' , $maxdepth , $mode , $d + 1 ) ;                  
                  $mstring .=  "<option value='".Icon($only_file,"path",filetype($file))."'>".$only_file ."</option>";
               }
           }
       }
       closedir ( $handle ) ;
   }
   if ( $d == 0 ) { natcasesort ( $dirlist ) ; }
   return ( $mstring ) ;
}

function SelectFileList2($path){
	global $DOCUMENT_ROOT, $mod, $SubID, $mmode;
	if($path == ""){
		$path = $_SERVER["DOCUMENT_ROOT"]."/data/sample/templet/basic";	
	}
	
	$mstring =  "<select name='page_name' onchange=\"document.location.href='design.mod.php?SubID=$SubID&mod=$mod&page_name='+this.value+'&mmode=$mmode'\">";
	if(FileList2($path, 0, "FULL")){
		$mstring .= FileList2($path, 0, "FULL");
	}else{
		$mstring .= "<option>파일이 존재하지않습니다.</option>";
	}
	$mstring .=  "</select>";
	
	return $mstring;
}



/*
CREATE TABLE IF NOT EXISTS deepzoom_gallery_info (
  `dgi_ix` int(8) unsigned zerofill NOT NULL auto_increment,
  `gallery_name` varchar(100) NOT NULL default '',
  `gallery_link` varchar(255) default NULL,
  `display_type` int(2) default '1',
  `product_cnt` int(5) default NULL,
  `insert_yn` enum('Y','N') default 'Y',
  `use_yn` enum('Y','N') default 'Y',
  `regdate` datetime default NULL,
  PRIMARY KEY  (`dgi_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='갤러리 정보' ;


CREATE TABLE IF NOT EXISTS `deepzoom_gallery_relation` (
  `dgr_ix` int(8) unsigned zerofill NOT NULL auto_increment,
  `di_ix` int(6) unsigned zerofill NOT NULL default '000000',
  `vieworder` int(5) NOT NULL default '0',
  `insert_yn` enum('Y','N') default 'Y',
  `regdate` datetime default NULL,
  PRIMARY KEY  (`dgr_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='갤러리 이미지 정보' ;


CREATE TABLE `shop_main_product_group` (
  `mpg_ix` int(8) unsigned zerofill NOT NULL auto_increment,  
  `gallery_name` varchar(100) NOT NULL default '',
  `gallery_code` int(2) NOT NULL default '0',
  `display_type` int(2) default '1',
  `insert_yn` enum('Y','N') default 'Y',
  `use_yn` enum('Y','N') default 'Y',
  `regdate` datetime default NULL,
  PRIMARY KEY  (`mpg_ix`)
) TYPE=MyISAM COMMENT='갤러리전시관리_그룹'



CREATE TABLE `shop_main_product_relation` (
  `mpr_ix` int(8) unsigned zerofill NOT NULL auto_increment,
  `pid` int(6) unsigned zerofill NOT NULL default '000000',  
  `gallery_code` int(2) NOT NULL default '1',
  `vieworder` int(5) NOT NULL default '0',
  `insert_yn` enum('Y','N') default 'Y',
  `regdate` datetime default NULL,
  PRIMARY KEY  (`mpr_ix`)
) TYPE=MyISAM COMMENT='갤러리전시관리_상품'

*/
?>