<?php
/**
 * Created by PhpStorm.
 * User: Forbiz
 * Date: 2019-04-18
 * Time: 오후 1:55
 */

?>
<td class="input_box_item" style="padding:5px;" colspan="3">

    <!-- AREA 1 -->
    <input type="radio" name="use_product_type" id="use_product_type_1"
           onclick="$('#goods_display_sub_area_B').hide();$('#div_productSearchBox').hide();;$('#relation_category_area').hide();$('#goods_display_sub_area_S').hide();"
           onfocus="this.blur();" align="absmiddle" value="1" checked="">
    <label class="blue" for="use_product_type_1">전체 상품에 발행 합니다</label>
    <br>
    <!-- AREA 1 -->


    <!-- AREA 2 -->
    <div class="cupon_cart_hide">
        <input type="radio" name="use_product_type" id="use_product_type_4"
               onclick="$('#goods_display_sub_area_B').show();$('#relation_category_area').hide();$('#goods_display_sub_area_S').hide();$('#div_productSearchBox').hide();"
               onfocus="this.blur();" align="absmiddle" value="4">
        <label class="blue" for="use_product_type_4"> 특정 브랜드(에,를) 속한 상품에 발행 합니다. </label>
        <br>
    </div>
    <div class="goods_auto_area" id="goods_display_sub_area_B" style="padding: 10px 5px; display: none;">
        <table border="0" cellpadding="0" cellspacing="0">
            <tbody>
            <tr>
                <td width="300">
                    <table border="0" cellpadding="0" cellspacing="0" align="center">
                        <tbody>
                        <tr align="left">
                            <td width="100">
                                <input type="text" class="textbox" name="search_text" id="search_text"
                                       style="width:180px;margin-bottom:2px;" value="">
                            </td>
                            <td align="center">
                                <img src="../v3/images/korea/btn_search.gif"
                                     onclick="SearchInfo('B',$('DIV#goods_display_sub_area_B'), 'brand');"
                                     style="cursor:pointer;">
                                <img src="../images/icon/pop_all.gif" alt="전체선택" title="전체선택"
                                     onclick="SelectedAll($('DIV#goods_display_sub_area_B #search_result_brand option'),'selected')"
                                     style="cursor:pointer;">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <select name="search_result[brand]"
                                        style=" width:320px;height:148px;font-size:12px;background:#fff;border-radius:0px;box-shadow:inset 0px 0px 0px 0px #e6e6e6;"
                                        class="search_result" id="search_result_brand" multiple="">
                                </select>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
                <td align="center" width="80">
                    <div class="float01 email_btns01">
                        <ul class="ui-sortable">
                            <li>
                                <a href="javascript:MoveSelectBox($('DIV#goods_display_sub_area_B'), 'B','ADD','brand');"><img
                                            src="../images/icon/pop_plus_btn.gif" alt="추가" title="추가"></a>
                            </li>
                            <li>
                                <a href="javascript:MoveSelectBox($('DIV#goods_display_sub_area_B'), 'B','REMOVE','brand');"><img
                                            src="../images/icon/pop_del_btn.gif" alt="삭제" title="삭제"></a>
                            </li>
                        </ul>
                    </div>
                </td>
                <td width="300" style="vertical-align:bottom;">
                    <table width="100%" border="0" align="center">
                        <tbody>
                        <tr>
                            <td colspan="2">
                                <select name="brand[]"
                                        style="width:300px;height:148px;font-size:12px;background:#fff;padding:5px;border-radius:0px;box-shadow:inset 0px 0px 0px 0px #e6e6e6;"
                                        id="selected_result_brand" validation="false" title="브랜드" multiple="">
                                </select>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <!-- AREA 2 -->


    <div class="cupon_cart_hide">

        <input type="radio" name="use_product_type" id="use_product_type_2"
               onclick="$('#goods_display_sub_area_B').hide();document.getElementById('relation_category_area').style.display='block';$('#div_productSearchBox').hide();$('#goods_display_sub_area_S').hide();"
               onfocus="this.blur();" align="absmiddle" value="2">
        <label class="blue" for="use_product_type_2">카테고리에 등록된 상품에 발행 합니다. (선택한 카테고리 하부 상품에 모두 적용됩니다.)</label>
        <br>

        <!-- AREA 3 -->
        <div class="doong" id="relation_category_area" style="display: block; vertical-align: top; min-height: 60px; padding-left: 20px;">
            <table border="0" cellpadding="0" cellspacing="0" style="margin-top:10px;">
                <tbody>
                <tr bgcolor="#ffffff">
                    <td nowrap=""><b>카테고리 선택 </b>&nbsp;&nbsp;</td>
                    <td>
                        <input type="hidden" name="selected_cid" value="">
                        <input type="hidden" name="selected_depth" value="">
                        <input type="hidden" id="_category">
                        <table border="0" cellpadding="0" cellspacing="0">
                            <tbody>
                            <tr>
                                <td style="padding-right:5px;">
                                    <select name="cid0" id="cid" depth="0" class="cid" onchange="loadCategory($(this),'cid1',2)" title="대분류" style="width:140px;font-size:12px;">
                                        <option value="">대분류</option>
                                        <option value="000000000000000">PRODUCT TYPE</option>
                                        <option value="001000000000000">SKIN SOLUTION</option>
                                        <option value="002000000000000">PRODUCT LINE</option>
                                    </select>
                                </td>
                                <td style="padding-right:5px;">
                                    <select name="cid1" id="cid" depth="1" class="cid" onchange="loadCategory($(this),'cid2',2)" title="중분류" validation="false" style="width:140px;font-size:12px;">
                                        <option value=""> 중분류</option>
                                    </select>
                                </td>
                                <td style="padding-right:5px;">
                                    <select name="cid2" id="cid" depth="2" class="cid" onchange="loadCategory($(this),'cid3',2)" title="소분류" validation="false" style="width:140px;font-size:12px;">
                                        <option value=""> 소분류</option>
                                    </select>
                                </td>
                                <td>
                                    <select name="cid3" id="cid" depth="3" class="cid" onchange="loadCategory($(this),'cid_1',2)" title="세분류" validation="false" style="width:140px;font-size:12px;">
                                        <option value=""> 세분류</option>
                                    </select>
                                </td>
                                <td style="padding-left:10px"><img src="../images/korea/btn_add.gif" align="absmiddle" border="0" onclick="categoryadd()"></td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
            <table width="90%" cellpadding="0" cellspacing="0" border="0" id="objCategory" style="margin-top:5px;">
                <colgroup>
                    <col width="1">
                    <col width="10">
                    <col width="545">
                    <col width="*">
                </colgroup>
            </table>
            <br><br>
        </div>
        <!-- AREA 3 -->



        <!-- AREA 4 -->
        <input type="radio" name="use_product_type" id="use_product_type_5"
               onclick="$('#goods_display_sub_area_B').hide();$('#relation_category_area').hide();$('#div_productSearchBox').hide();$('#goods_display_sub_area_S').show();"
               onfocus="this.blur();" align="absmiddle" value="5">
        <label class="blue" for="use_product_type_5">특정셀러(에,를) 속한 상품에 발행합니다.</label>
        <br>
        <div class="goods_auto_area" id="goods_display_sub_area_S" style="padding: 10px 5px; display: none;">
            <table border="0" cellpadding="0" cellspacing="0">
                <tbody>
                <tr>
                    <td width="300">
                        <table border="0" cellpadding="0" cellspacing="0" align="center">
                            <tbody>
                            <tr align="left">
                                <td width="100">
                                    <input type="text" class="textbox" name="search_text" id="search_text"
                                           style="width:180px;margin-bottom:2px;" value="" "="">
                                    <!--onclick=ShowModalWindow('./charger_search.php?company_id=3444fde7c7d641abc19d5a26f35a12cc&target=4&amp;code=',600,530,'charger_search')-->
                                </td>
                                <td align="center">
                                    <img src="../v3/images/korea/btn_search.gif"
                                         onclick="SearchInfo('S',$('DIV#goods_display_sub_area_S'), 'seller');"
                                         style="cursor:pointer;">
                                    <!--img src='../images/btn_select_seller.gif' onclick="SearchInfo('S',$('DIV#goods_display_sub_area_S'), 'seller');"  style='cursor:pointer;'-->
                                    <img src="../images/icon/pop_all.gif" alt="전체선택" title="전체선택"
                                         onclick="SelectedAll($('DIV#goods_display_sub_area_S #search_result_seller option'),'selected')"
                                         style="cursor:pointer;">
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <!--div style=' width:320px;height:148px;font-size:12px;background:#fff;border:1px solid silver;' id='search_result'>
                                    </div-->
                                    <select name="search_result[seller]"
                                            style=" width:320px;height:148px;font-size:12px;background:#fff;border-radius:0px;box-shadow:inset 0px 0px 0px 0px #e6e6e6;"
                                            class="search_result" id="search_result_seller" multiple="">
                                    </select>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                    <td align="center" width="80">
                        <div class="float01 email_btns01">
                            <ul class="ui-sortable">
                                <li>
                                    <a href="javascript:MoveSelectBox($('DIV#goods_display_sub_area_S'), 'S','ADD','seller');"><img
                                                src="../images/icon/pop_plus_btn.gif" alt="추가" title="추가"></a>
                                </li>
                                <li>
                                    <a href="javascript:MoveSelectBox($('DIV#goods_display_sub_area_S'),'S','REMOVE','seller');"><img
                                                src="../images/icon/pop_del_btn.gif" alt="삭제" title="삭제"></a>
                                </li>
                            </ul>
                        </div>
                    </td>
                    <td width="300" style="vertical-align:bottom;">
                        <table width="100%" border="0" align="center">
                            <tbody>
                            <tr>
                                <td colspan="2">
                                    <!--div style=' width:320px;height:148px;font-size:12px;background:#fff;border:1px solid silver;' id='selected_result'>
                                    </div-->
                                    <select name="seller[]"
                                            style="width:300px;height:148px;font-size:12px;background:#fff;padding:5px;border-radius:0px;box-shadow:inset 0px 0px 0px 0px #e6e6e6;"
                                            id="selected_result_seller" validation="false" title="셀러" multiple="">

                                    </select>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <!-- AREA 4 -->


        <!-- AREA 5 -->
        <input type="radio" name="use_product_type" id="use_product_type_3"
               onclick="$('#goods_display_sub_area_B').hide();$('#relation_category_area').hide();ms_productSearch.show_productSearchBox(event,1,'productList_1','clipart','', 'coupon');$('#goods_display_sub_area_S').hide();"
               onfocus="this.blur();" align="absmiddle" value="3">
        <label class="blue" for="use_product_type_3">특정 상품에 발행 합니다. (트리에서 상품을 검색 후 드래그앤드롭 을 사용해 등록합니다)</label>
        <br>
        <div style="width:100%;padding:5px;" id="group_product_area_1">
            <ul id="productList_1" name="productList" class="productList ui-sortable">
                <li id="li_productList_1_0000000389" state="1" vieworder="327" viewcnt="" regdate=""
                    style="float:left;width:100px;">
                    <table width="100%" cellspacing="1" cellpadding="0" style="table-layout:fixed;height:210px;">
                        <tbody>
                        <tr>
                            <td class="small" style="background-color:gray;color:#ffffff;" nowrap="">판매중</td>
                            <td class="small" style="background-color:gray;color:#ffffff;">노출함</td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <img id="pImage_1_0000000389"
                                     src="http://dewytree-admin.forbiz.co.kr/data/dewytree_data/images/product/00/00/00/03/89/c_0000000389.gif"
                                     title="[0000000389]듀이트리 아쿠아 딥마스크 1매_1" height="60"
                                     onerror="this.src='/admin/images/noimages_50.gif';"
                                     style="margin:5px;">
                                <input type="hidden" name="listPid" class="listPid" value="0000000389">
                                <input type="hidden" name="rpid[1][]" value="0000000389">
                                <input type="hidden" id="dcprice_1_0000000389" value="1000">
                                <span style="display:none;" id="pName_1_0000000389">듀이트리 아쿠아 딥마스크 1매_1</span>
                                <span style="display:none;" id="pPrice_1_0000000389">1000</span>
                                <span style="display:none;" id="listprice_1_0000000389">1000</span>
                                <span style="display:none;" id="prdType_1_0000000389">0</span>
                                <span style="display:none;" id="pGid_1_0000000389"></span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div style="margin-top:4px;" class="goods_search_text">0000000389</div>
                                <div style="display:inline;" class="goods_search_text">듀이트리아쿠아 ..
                                    <div style="margin:3px 0 0 0;">판매가 : 1,000원 &gt; <span class="amount">1,000</span> 원
                                    </div>
                                    <div style="margin:3px 0 0 0;">WMS품목코드 :</div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </li>
                <li id="li_productList_1_0000000391" state="1" vieworder="329" viewcnt="" regdate=""
                    style="float:left;width:100px;">
                    <table width="100%" cellspacing="1" cellpadding="0" style="table-layout:fixed;height:210px;">
                        <tbody>
                        <tr>
                            <td class="small" style="background-color:gray;color:#ffffff;" nowrap="">판매중</td>
                            <td class="small" style="background-color:gray;color:#ffffff;">노출함</td>
                        </tr>
                        <tr>
                            <td colspan="2"><img id="pImage_1_0000000391"
                                                 src="http://dewytree-admin.forbiz.co.kr/data/dewytree_data/images/product/shop/noimg.gif"
                                                 title="[0000000391]CMKIM_2" height="60"
                                                 onerror="this.src='/admin/images/noimages_50.gif';"
                                                 style="margin:5px;"><input type="hidden" name="listPid" class="listPid"
                                                                            value="0000000391"><input type="hidden"
                                                                                                      name="rpid[1][]"
                                                                                                      value="0000000391"><input
                                        type="hidden" id="dcprice_1_0000000391" value="34000"><span
                                        style="display:none;" id="pName_1_0000000391">CMKIM_2</span><span
                                        style="display:none;" id="pPrice_1_0000000391">34000</span><span
                                        style="display:none;" id="listprice_1_0000000391">34000</span><span
                                        style="display:none;" id="prdType_1_0000000391">99</span><span
                                        style="display:none;" id="pGid_1_0000000391"></span></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div style="margin-top:4px;" class="goods_search_text">0000000391</div>
                                <div style="display:inline;" class="goods_search_text">CMKIM_2..
                                    <div style="margin:3px 0 0 0;">판매가 : 34,000원 &gt; <span class="amount">34,000</span>
                                        원
                                    </div>
                                    <div style="margin:3px 0 0 0;">WMS품목코드 :</div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </li>
                <li id="li_productList_1_0000000384" state="1" vieworder="322" viewcnt="" regdate=""
                    style="float:left;width:100px;">
                    <table width="100%" cellspacing="1" cellpadding="0" style="table-layout:fixed;height:210px;">
                        <tbody>
                        <tr>
                            <td class="small" style="background-color:gray;color:#ffffff;" nowrap="">판매중</td>
                            <td class="small" style="background-color:gray;color:#ffffff;">노출함</td>
                        </tr>
                        <tr>
                            <td colspan="2"><img id="pImage_1_0000000384"
                                                 src="http://dewytree-admin.forbiz.co.kr/data/dewytree_data/images/product/00/00/00/03/84/c_0000000384.gif"
                                                 title="[0000000384]듀이트리 미니멀 딥마스크 1매" height="60"
                                                 onerror="this.src='/admin/images/noimages_50.gif';"
                                                 style="margin:5px;"><input type="hidden" name="listPid" class="listPid"
                                                                            value="0000000384"><input type="hidden"
                                                                                                      name="rpid[1][]"
                                                                                                      value="0000000384"><input
                                        type="hidden" id="dcprice_1_0000000384" value="3000"><span style="display:none;"
                                                                                                   id="pName_1_0000000384">듀이트리 미니멀 딥마스크 1매</span><span
                                        style="display:none;" id="pPrice_1_0000000384">3000</span><span
                                        style="display:none;" id="listprice_1_0000000384">3000</span><span
                                        style="display:none;" id="prdType_1_0000000384">0</span><span
                                        style="display:none;" id="pGid_1_0000000384">123456</span></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div style="margin-top:4px;" class="goods_search_text">0000000384</div>
                                <div style="display:inline;" class="goods_search_text">듀이트리미니멀 ..
                                    <div style="margin:3px 0 0 0;">판매가 : 3,000원 &gt; <span class="amount">3,000</span> 원
                                    </div>
                                    <div style="margin:3px 0 0 0;">WMS품목코드 : 123456</div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </li>
            </ul>
        </div>
        <!-- AREA 5 -->


        <input type="radio" name="use_product_type" id="use_product_type_6"
               onclick="$('#goods_display_sub_area_B').hide();$('#relation_category_area').hide();ms_productSearch.show_productSearchBox(event,1,'productList_1','clipart','', 'coupon');$('#goods_display_sub_area_S').hide();"
               onfocus="this.blur();" align="absmiddle" value="6">
        <label class="blue" for="use_product_type_6">전체 상품, 일부 상품 제외 발행 합니다. (트리에서 상품을 검색 후 드래그앤드롭 을 사용해
            등록합니다)</label><br>
        <div style="width:100%;padding:5px;" id="group_product_area_1">
            <ul id="productList_1" name="productList" class="productList ui-sortable"></ul>
        </div>


    </div>
</td>
