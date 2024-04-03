function allCheck(chk, name)
{
    $('input[name="'+name+'"]').attr('checked',((chk)	?	true:false));
}

function allCheck2(chk, name)
{
    $('input[name="'+name+'"]').attr('checked',((chk)	?	true:false));
}

var ms_productSearch = {
    mallixHTML: null,
    companyHTML: null,
    groupCode: null,
    submcode: 'clipart',
    service_type: '',

    // 상품 검색 레이어
    show_productSearchBox: function(evt, groupCode, mObj, submcode, set_product, service_type)
    {

         console.log("evt:"+evt+", groupCode:"+groupCode+", mObj:"+mObj+", submcode:"+submcode+", set_product:"+set_product+", service_type:"+service_type);

        ms_productSearch.groupCode = groupCode;
        ms_productSearch.mObj = mObj;

        if(typeof(submcode) != 'undefined'){
            ms_productSearch.submcode = submcode;
        }

        ms_productSearch.set_product = set_product;

        if(typeof(service_type) != 'undefined'){
            ms_productSearch.service_type = service_type;
        }

        if(set_product == '77'){
            var saveDatasName = 'fpid';
        }else{
            var saveDatasName = 'rpid';
        }


        if(language == "korea"){
            var category_search_text = "카테고리검색";
            var keyword_search_text = "키워드검색";
            var discount_search_text = "기획할인전";
            var product_name = "상품명";
            var product_code = "상품코드";
            var system_code = "시스템코드";
            var item_code = "품목시스템코드";
            var barcode = "바코드";
            var option_code = "옵션코드";
            var brand_name = "브랜드명";
            var select_category = "카테고리를 선택해 주세요.";
            var choice_goods = "선택된 상품";
        }else if(language == "english"){
            var category_search_text = "Category";
            var keyword_search_text = "Keyword";
            var discount_search_text = "Discount";
            var product_name = "Product Name";
            var product_code = "Product Code";
            var system_code = "System Code";
            var item_code = "Item Code";
            var barcode = "Barcode";
            var option_code = "Option Code";
            var brand_name = "Brand Name";
            var select_category = "Please select a category ";
            var choice_goods = "Selected Products";
        }else if(language == "indonesian"){
            var category_search_text = "Category";
            var keyword_search_text = "Keyword";
            var discount_search_text = "Discount";
            var product_name = "Product Name";
            var product_code = "Product Code";
            var system_code = "System Code";
            var item_code = "Item Code";
            var barcode = "Barcode";
            var option_code = "Option Code";
            var brand_name = "Brand Name";
            var select_category = "Please select a category ";
            var choice_goods = "Selected Products";
        }else{
            var category_search_text = "카테고리검색";
            var keyword_search_text = "키워드검색";
            var discount_search_text = "기획할인전";
            var product_name = "상품명";
            var product_code = "상품코드";
            var system_code = "시스템코드";
            var item_code = "품목코드";
            var barcode = "바코드";
            var option_code = "옵션코드";
            var brand_name = "브랜드명";
            var select_category = "카테고리를 선택해 주세요.";
            var choice_goods = "선택된 상품";
        }



        $('div#div_productSearchBox').remove();
        $('<div id="div_productSearchBox" style="position:absolute;width:1020px;height:450px;z-index:1;display:none;background-color:#FFFFFF;border:3px solid silver;padding:5px"></div>').appendTo('div#container');


        // 검색탭
        var str = '<div id="div_productSearch1" style="float:left;width:260px;height:450px;background-color:#ffffff; ">';
        str += '<div class="tab" style="height:30px;margin:0;"><table class="s_org_tab"><tr><td class="tab">';
        str += '<table id="tab_keyword" class="on"><tr><th class="box_01"></th><td class="box_02 small" onclick="ms_productSearch._tabChange(\'keyword\')\" style="padding-left:5px;padding-right:5px;">1'+keyword_search_text+'</td><th class="box_03"></th></tr></table>';
        str += '</td><td class="btn"></td></tr></table></div>';

        // 카테고리 검색폼
        str += '<div id="div_categoryForm" style="overflow:auto;height:400px;width:260px;display:none;border:1px solid #C0C0C0;border-top:none;"><iframe src="/admin/product/relationAjax.category.php?search_type='+ms_productSearch.service_type+'" width="100%" height="100%" frameborder="0"></iframe></div>';

        // 키워드 검색 폼
        str += '<div id="div_keywordForm" style="overflow:auto;height:400px;width:260px;border:1px solid #C0C0C0;border-top:none;">';
        //str += '<div style="padding:5px 5px 3px 5px;">'+ms_productSearch.mallixHTML+'</div>'; //전체 셀렉트
        str += '<div style="padding:5px 5px 3px 5px;">'+ms_productSearch.companyHTML+'</div>';  //전체보기 셀렉트
        str += '<div style="padding:3px 5px;"><select id="search_type">';
        str += '<option value="p.pcode">'+item_code+'</option>';
        str += '<option value="p.id">'+system_code+'</option>';
        //str += '<option value="p.barcode">'+barcode+'</option>';
        str += '<option value="p.pname">'+product_name+'</option>';
        //str += '<option value="p.brand_name">'+brand_name+'</option>';
        str += '</select> </div>';

        str += '<div style="padding:3px 5px;"><textarea type="text" style="width:150px;height:200px;" id="ms_productSearch_search_text"></textarea>';
        str +=' <table><tr><td width=90>엔터키로 <br/>상품코드를 <br/>구분하여 <br/>검색 가능<br/> 합니다.</td><td> ex) <br/>4032<br/>2341<br/>1234</td></tr></table></div>';
        str += '<div style="padding:3px 5px;"><img src="../images/'+language+'/btc_search.gif" onclick="ms_productSearch.searchText();" style="cursor:pointer;" /></div></div>';

        str += '<div id="div_discountForm" style="overflow:auto;height:430px;width:300px;border:1px solid #C0C0C0;border-top:none;display:none;">';
        str += '<div style="padding:10px;"><select name="dc_ix" id="dc_ix"><option value="">기획&특별기획 할인</option></select></div>';
        str += '<div style="padding:10px;"><select name="event_ix" id="event_ix"><option value="">이벤트&기획전</option></select></div>';
        str += '<div id="discount_group_info" style="padding:10px;">상단에 기획할인정보 및 이벤트&기획전 를 선택해주세요</div>';
        str += '</div>';
        str += '</div>';
        // onkeypress="if(event.keyCode == 13){ms_productSearch.searchText();return false;}"

        // 검색 상품 리스트
        str += '<div id="div_productSearch2" style="float:left;width:390px;height:430px;margin:0 0 0 10px;border:1px solid #C0C0C0;">';
        str += '<div id="div_productAction" style="height:50px;border-bottom:1px solid #C0C0C0;"><div style="padding:5px;"><input type="checkbox" id="chk_all" style="vertical-align:middle;" value="Y" onclick="allCheck(this.checked, \'pList\');" /><img src="../images/'+language+'/btn_selected_reg.gif" border="0" onclick="ms_productSearch._selectProduct(\'R\');" style="cursor:pointer;vertical-align:middle;" />';
        str += ' <select id="listMax" onchange="ms_productSearch._getProductList(null, null, 1);" style="vertical-align:middle;"><option value="5">5</option><option value="10">10</option><option value="20">20</option><option value="30">30</option><option value="50">50</option><option value="100" selected>100</option><option value="200">200</option><option value="500">500</option><option value="1000">1000</option><option value="nolimit">제한없음</option></select>';
        str += '<input type="checkbox" name="state" id="state" value="1" onclick="ms_productSearch._getProductList(null, null, 1);"><label for="state">판매중</label>';
        str += '<input type="checkbox" name="disp" id="disp" value="1" onclick="ms_productSearch._getProductList(null, null, 1);"><label for="disp">노출함</label>';

        str += '<input type="hidden" name="sell_type" id="sell_type_basic" value="basic" checked><!-- <label for="sell_type_basic">소매가</label> <input type="radio" name="sell_type" id="sell_type_whoelsale" value="wholesale"> <label for="sell_type_whoelsale">도매가</label>--></div></div>';
        if(set_product != '77'){
            str += '<div id="div_productList" style="overflow:auto;border-bottom:1px solid #C0C0C0;height:390px;"><div style="text-align:center;padding:150px 0 0 0;color:gray;">'+select_category+'</div></div>';
        }else{
            str += '<div id="div_productList" style="overflow:auto;border-bottom:1px solid #C0C0C0;height:390px;"><div style="text-align:center;padding:150px 0 0 0;color:gray;">상품을 검색해주세요</div></div>';
        }
        str += '<div id="div_productPaging" style="height:30px;text-align:center;"></div>';
        str += '</div>';

        // 선택 상품 리스트
        str += '<div id="div_productSearch3" style="float:left;width:340px;height:430px;margin:0 0 0 10px;border:1px solid #C0C0C0;">';
        str += '<div id="div_productSelectT" style="height:30px;border-bottom:1px solid #C0C0C0;"><div style="padding:10px 10px 10px 5px;font-size:12px;font-weight:bold;text-align:left;" class="select_goods" ><input type="checkbox" id="chc_all" style="vertical-align:middle;" value="Y" onclick="allCheck2(this.checked, \'pList2\');" /><label for="chc_all">'+choice_goods+'</label></div></div>';
        str += '<div id="div_productSelect" style="overflow:auto;border-bottom:1px solid #C0C0C0;height:390px;"></div>';
        str += '<div id="div_productSelectB" style="height:30px;"><div style="padding:5px;"><img src="../images/'+language+'/btn_selected_del.gif" border="0" onclick="ms_productSearch._delProductSel();" style="cursor:pointer;vertical-align:middle;" /> <img src="../images/'+language+'/btn_whole_del.gif" border="0" onclick="ms_productSearch._delProductAll();" style="cursor:pointer;vertical-align:middle;" /> <img src="../images/'+language+'/btn_win_close.gif" border="0" onclick="ms_productSearch._closeDiv();" style="cursor:pointer;vertical-align:middle;" /></div></div>';
        str += '</div>';

        $('div#div_productSearchBox').append(str);


        $('div#div_productSelect').html('');
        $('ul#productList_'+ms_productSearch.groupCode+' input[name="'+saveDatasName+'['+ms_productSearch.groupCode+'][]"]').each(function()	{
            pID = $(this).val();
            state = $(this).closest('li').attr('state');
            vieworder = $(this).closest('li').attr('vieworder');
            viewcnt = $(this).closest('li').attr('viewcnt');
            regdate = $(this).closest('li').attr('regdate');
            dcprice = $('#dcprice_'+ms_productSearch.groupCode+'_'+pID).val();
            listprice = $('#listprice_'+ms_productSearch.groupCode+'_'+pID).html();
            ms_productSearch._setProduct('div_productSelect', 'A', pID, $('#pImage_'+ms_productSearch.groupCode+'_'+pID).attr('src'), $('#pName_'+ms_productSearch.groupCode+'_'+pID).html(),
                $('#brandName_'+ms_productSearch.groupCode+'_'+pID).html(), $('#pPrice_'+ms_productSearch.groupCode+'_'+pID).html(),listprice, '','','','', '', state, dcprice,
                vieworder, viewcnt, regdate, '',
                $('#prdType_'+ms_productSearch.groupCode+'_'+pID).html(),
                $('#pGid_'+ms_productSearch.groupCode+'_'+pID).html());
        });

        var tg = (evt.target)	?	evt.target:evt.srcElement;
        $('div#div_productSearchBox').css('left',(parseInt(getOffsetLeft(tg)))+'px');
        $('div#div_productSearchBox').css('top',(parseInt(getOffsetTop(tg))-50)+'px');
        $('div#div_productSearchBox').slideDown();
    },




    searchText: function()
    {
        this._search_type	= '';
        this._search_text	= '';
        //ms_productSearch._getProductList('search_list', 1, 1, $('#search_type').val(), $('#search_text').val(), $('div#div_keywordForm select#company_id option:selected').val());

        ms_productSearch._getProductList('search_list', 1, 1, $('#search_type').val(), $('div#div_keywordForm textarea#ms_productSearch_search_text').val(), $('div#div_keywordForm select#company_id option:selected').val(), ms_productSearch.service_type, $('div#div_keywordForm select[name=mall_ix] option:selected').val());

    },

    // 상품 불러오기
    _getProductList: function()
    {
        this._max			    = $('select#listMax option:selected').val();
        this._mode			    = (arguments[0])	?	arguments[0]:this._mode;
        this._nset			    = (arguments[1])	?	arguments[1]:this._nset;
        this. _page			    = (arguments[2])	?	arguments[2]:this._page;
        this._state			    = ($('input#state').is(":checked") ? $('input#state:checked').val():"");
        this._disp			    = ($('input#disp').is(":checked") ? $('input#disp:checked').val():"");
        this._one_commission	= ($('input#one_commission').is(":checked") ? $('input#one_commission:checked').val():"");
        this._soho			    = ($('input#soho').is(":checked") ? $('input#soho:checked').val():"");
        this._designer			= ($('input#designer').is(":checked") ? $('input#designer:checked').val():"");
        this._mirrorpick		= ($('input#mirrorpick').is(":checked") ? $('input#mirrorpick:checked').val():"");

        if(this._mode == 'list')	{
            this._cid		= (arguments[3])	?	arguments[3]:this._cid;
            this._depth		= (arguments[4] || arguments[4] == '0')	?	arguments[4]:this._depth;//arguments[4];//arguments[4] != '' ||
            this._search_type	= '';
            this._search_text	= '';
            this._company_id	= '';
            this.service_type	= (arguments[6])	?	arguments[6]:this.service_type;
            this._mall_ix	= '';

        }	else	{

            this._cid			= '';
            this._depth			= '';
            //this._search_text	= '';
            this._search_type	= (arguments[3])	?	arguments[3]:this._search_type;
            this._search_text	= (arguments[4])	?	arguments[4]:this._search_text;
            if(this.search_text != ''){
                this._search_text = this._search_text.replace(/\n/g, "<br>");
            }
            this._company_id	= (arguments[5])	?	arguments[5]:this._company_id;
            this.service_type	= (arguments[6])	?	arguments[6]:this.service_type;
            this._mall_ix	= (arguments[7])	?	arguments[7]:this._mall_ix;
        }

        $('div#div_productList').html('<div style="text-align:center;padding:150px 0 0 0;color:gray;"><img src="/admin/images/indicator.gif"><!--상품을 검색 중입니다.--></div>');
        if(!this._mode)	return;

        $.ajax({
            url:'/admin/product/relationAjax.category.act.php?'+(Math.random()*10000000000),
            type: 'post',
            dataType: 'xml',
            data: ({mode: this._mode,
                service_type: this.service_type,
                search_type: this._search_type,
                search_text: this._search_text,
                company_id: this._company_id,
                mall_ix: this._mall_ix,
                page: this._page,
                max: this._max,
                cid: this._cid,
                depth: this._depth,
                state: this._state,
                disp: this._disp,
                one_commission: this._one_commission,
                soho: this._soho,
                //designer: this._designer,
                mirrorpick: this._mirrorpick,
                product_type: ms_productSearch.set_product
            }),
            error: function(xhr){
                //alert(xhr);
                $('div#div_productList').html('<div style="text-align:center;padding:150px 0 0 0;color:gray;">검색된 상품이 없습니다. [e] '+(this.service_type = 'coupon' ? '<br>세트상품은 적용대상이 아닙니다.':'')+'</div>');
                $('#div_productPaging').html("-");
                //alert('XML 문서 해석 실패 - '+xhr.status());
            },
            success: function(data)	{
                var total = $(data).find('relationProducts').attr('total');
                if(total > 0)	{
                    $('#div_productPaging').html(ms_productSearch._getPageString(total));
                    var items = $(data).find('relationProducts').find('products');
                    var str = '';
                    $('#div_productList').html('');
                    items.each(function()	{
                        ms_productSearch._setProduct('div_productList', 'L', $(this).find('pid').text(), $(this).find('img_src').text(), $(this).find('pname').text(),
                            $(this).find('brand_name').text(), $(this).find('sellprice').text(), $(this).find('listprice').text(), $(this).find('reserve').text(),
                            $(this).find('coprice').text(), $(this).find('wholesale_price').text(), $(this).find('wholesale_sellprice').text(), $(this).find('disp').text(),
                            $(this).find('state').text(), $(this).find('dcprice').text(),
                            $(this).find('vieworder').text(), $(this).find('view_cnt').text(), $(this).find('regdate').text(), $(this).find('one_commission').text(), $(this).find('product_type').text(),$(this).find('gid').text());
                    });
                }else{
                    $('div#div_productList').html('<div style="text-align:center;padding:150px 0 0 0;color:gray;">검색된 상품이 없습니다.'+(this.service_type = 'coupon' ? '<br>세트상품은 적용대상이 아닙니다.':'')+'</div>');
                    $('#div_productPaging').html("-");
                }

            }
        });
    },

    _tabChange: function(mode)
    {
        if(mode == 'category')	{
            $('#tab_keyword').removeClass('on');
            $('#div_keywordForm').hide();
            $('#tab_category').addClass('on');
            $('#div_categoryForm').show();
            $('#tab_discount').removeClass('on');
            $('#div_discountForm').hide();

        }else if(mode == 'discount')	{
            $('#tab_category').removeClass('on');
            $('#div_categoryForm').hide();
            $('#tab_keyword').removeClass('on');
            $('#div_keywordForm').hide();
            $('#tab_discount').addClass('on');
            $('#div_discountForm').show();
            ms_productSearch._loadDiscount($("div#div_discountForm select#dc_ix"));
            ms_productSearch._loadEvent($("div#div_discountForm select#event_ix"));

        }else{
            $('#tab_category').removeClass('on');
            $('#div_categoryForm').hide();
            $('#tab_keyword').addClass('on');
            $('#div_keywordForm').show();
            $('#tab_discount').removeClass('on');
            $('#div_discountForm').hide();
        }
    },

    _loadDiscount: function(target, charger_ix) {
        $.ajax({
            type: 'GET',
            data: {'act': 'getDiscountInfo', 'charger_ix':charger_ix},
            url: '/admin/promotion/discount.act.php',
            dataType: 'json',
            async: true,
            beforeSend: function(){

            },
            success: function(datas){
                target.each(function(){
                    $(this).find('option').not(':first').remove();
                });

                if(datas != null){
                    $.each(datas, function(i, data){
                        target.append("<option value='"+data.dc_ix+"'>"+data.discount_sale_title+"</option>");
                    });

                    target.change(function(){
                        //alert($(this).val());
                        ms_productSearch._loadDiscountGroup($(this).val());
                    });
                }
            }
        });
    },

    _loadEvent: function(target, charger_ix) {
        $.ajax({
            type: 'GET',
            data: {'act': 'getEventInfo', 'charger_ix':charger_ix},
            url: '/admin/display/event.act.php',
            dataType: 'json',
            async: true,
            beforeSend: function(){

            },
            success: function(datas){
                target.each(function(){
                    $(this).find('option').not(':first').remove();
                });

                if(datas != null){
                    $.each(datas, function(i, data){
                        var agent_div = '';
                        if(data.agent_type == 'W'){
                            agent_div = '[WEB]';
                        }else{
                            agent_div = '[MOBILE]';
                        }
                        //manage_title
                        target.append("<option value='"+data.event_ix+"'>"+agent_div+data.event_title+"</option>");
                    });

                    target.change(function(){
                        //alert($(this).val());
                        ms_productSearch._loadEvnetGroup($(this).val());
                    });
                }
            }
        });
    },

    _loadDiscountGroup: function(dc_ix) {
        $.ajax({
            type: 'GET',
            data: {'act': 'getDiscountGroupInfo', 'dc_ix':dc_ix},
            url: '/admin/promotion/discount.act.php',
            dataType: 'json',
            async: true,
            beforeSend: function(){

            },
            success: function(datas){
                if(datas != null){
                    //mstring = "<";

                    $("div#div_discountForm div#discount_group_info").html("");
                    var mstring = "";
                    mstring += "<table cellpadding=0 width=100%>";
                    $.each(datas, function(i, data){
                        if(data.discount_sale_type ==1){
                            sale_str = data.sale_rate+"원";
                        }else{
                            sale_str = data.sale_rate+"%";
                        }

                        var onclick_str = "javascript:ms_productSearch._getProductList('search_list', 1, 1, 'dc_ix', data.dc_ix,'');";
                        mstring += "<tr><td style='padding:5px;' ><a href=\""+onclick_str+"\">그룹 "+data.group_code+" ("+sale_str+") "+data.group_name+"</a></td><td nowrap><a href=\"javascript:PoPWindow3('../promotion/discount.php?mmode=pop&dc_ix="+data.dc_ix+"',1200,800,'main_goods')\">보기</a></td></tr>";

                        if(data.goods_display_type == "M"){
                            var onclick_str = "javascript:ms_productSearch._getProductList('search_list', 1, 1, 'p.id', '"+data.r_ix+"','');";
                            mstring += "<tr><td style='padding:5px;padding-left:20px;' colspan='2' align=left ><a href=\""+onclick_str+"\" alt='"+data.r_ix+"'>상품보기</a></td></td></tr>";
                        }else{
                            if(data.display_auto_sub_type	=="B"){
                                //ms_productSearch._getProductList('search_list', 1, 1, $('#search_type').val(), $('div#div_keywordForm textarea#ms_productSearch_search_text').val(), $('div#div_keywordForm select#company_id option:selected').val());
                                var onclick_str = "javascript:ms_productSearch._getProductList('search_list', 1, 1, 'brand', '"+data.r_ix+"','');";
                                mstring += "<tr><td style='padding:5px;padding-left:20px;' colspan='2' align=left ><a href=\""+onclick_str+"\" alt='"+data.r_ix+"'>적용 브랜드 상품보기</a></td></td></tr>";
                            }else if(data.display_auto_sub_type	=="S"){
                                var onclick_str = "javascript:ms_productSearch._getProductList('search_list', 1, 1, 'admin', '"+data.r_ix+"','');"
                                mstring += "<tr><td style='padding:5px;padding-left:20px;' colspan='2' align=left ><a href=\""+onclick_str+"\"  alt='"+data.r_ix+"'>적용 셀러 상품보기</a></td></td></tr>";
                            }else if(data.display_auto_sub_type	=="C"){
                                var onclick_str = "javascript:ms_productSearch._getProductList('list', 1, 1, 'cid', '"+data.r_ix+"','');"
                                mstring += "<tr><td style='padding:5px;padding-left:20px;' colspan='2' align=left ><a href=\""+onclick_str+"\"  alt='"+data.r_ix+"'>적용 카테고리 상품보기</a></td></td></tr>";
                            }
                        }
                    });
                    mstring += "</table>";
                    $("div#div_discountForm div#discount_group_info").append(mstring);
                }else{
                    $("div#div_discountForm div#discount_group_info").html("상단에 기획 할인정보를 선택해주세요");
                }
            }
        });
    },

    _loadEvnetGroup: function(event_ix) {
        $.ajax({
            type: 'GET',
            data: {'act': 'getEventGroupInfo', 'event_ix':event_ix},
            url: '/admin/display/event.act.php',
            dataType: 'json',
            async: true,
            beforeSend: function(){

            },
            success: function(datas){
                if(datas != null){
                    //mstring = "<";

                    $("div#div_discountForm div#discount_group_info").html("");
                    var mstring = "";
                    mstring += "<table cellpadding=0 width=100%>";
                    $.each(datas, function(i, data){

						/*
						 if(data.discount_sale_type ==1){
						 sale_str = data.sale_rate+"원";
						 }else{
						 sale_str = data.sale_rate+"%";
						 }
						 */

                        //var onclick_str = "javascript:ms_productSearch._getProductList('search_list', 1, 1, 'dc_ix', data.dc_ix,'');";
                        //<a href=\""+onclick_str+"\"></a>
                        mstring += "<tr><td style='padding:5px;' >그룹 "+data.group_code+" "+data.group_name+"</td><td nowrap><a href=\"javascript:PoPWindow3('../display/event.write.php?mmode=pop&event_ix="+data.event_ix+"',1200,800,'main_goods')\">보기</a></td></tr>";


                        var onclick_str = "javascript:ms_productSearch._getProductList('search_list', 1, 1, 'p.id', '"+data.r_ix+"','');";
                        mstring += "<tr><td style='padding:5px;padding-left:20px;' colspan='2' align=left ><a href=\""+onclick_str+"\" alt='"+data.r_ix+"'>상품보기</a></td></td></tr>";

                    });
                    mstring += "</table>";
                    $("div#div_discountForm div#discount_group_info").append(mstring);
                }else{
                    $("div#div_discountForm div#discount_group_info").html("상단에 기획 할인정보를 선택해주세요");
                }
            }
        });
    },

    _selectProduct: function(mode)
    {

        $('input:checkbox[name="pList"]:checked').each(function()	{
            var pID = $(this).val();
            ms_productSearch._setProduct('div_productSelect',mode,pID,'',$('#pName_'+ms_productSearch.groupCode+'_'+pID).html(),
                $('#brandName_'+ms_productSearch.groupCode+'_'+pID).html(),$('#pPrice_'+ms_productSearch.groupCode+'_'+pID).html(),
                $('#listprice_'+ms_productSearch.groupCode+'_'+pID).html(),'',$('#coprice_'+ms_productSearch.groupCode+'_'+pID).html(),
                $('#wholesale_price_'+ms_productSearch.groupCode+'_'+pID).html(),$('#wholesale_sellprice_'+ms_productSearch.groupCode+'_'+pID).html(),$('#disp_'+ms_productSearch.groupCode+'_'+pID).html(),$('#state_'+ms_productSearch.groupCode+'_'+pID).html(),$('#dcprice_'+ms_productSearch.groupCode+'_'+pID).val(), '', '', '', '', $('#prdType_'+ms_productSearch.groupCode+'_'+pID).html(),$('#pGid_'+ms_productSearch.groupCode+'_'+pID).val());
        });
    },

    // 상품 디스플레이//,p.coprice,p.wholesale_price,p.wholesale_sellprice,p.listprice
    _setProduct: function(obj, mode, pID, imgSrc, pName, bName, pPrice, listprice, reserve,coprice,wholesale_price,wholesale_sellprice, disp, state, dcprice, vieworder, viewcnt, regdate, one_commission, product_type, gid)
    {
        var str = '';
        var str2 = '';
        var sellprice_type = $('input:radio[name=sell_type]:checked').val();
        if(sellprice_type == "basic"){
            listprice = parseInt(listprice);
            pPrice = parseInt(pPrice);
        }else if(sellprice_type == "wholesale"){
            listprice = parseInt(wholesale_price);
            pPrice = parseInt(wholesale_sellprice);
        }
        dcprice = parseInt(dcprice);
        var state_str = "";
        if(state == '1'){
            state_str = "판매중";
        }else if(state == '0'){
            state_str = "일시품절";
        }else if(state == '2'){
            state_str = "판매중지";
        }else if(state == '7'){
            state_str = "수정대기";
        }else if(state == '6'){
            state_str = "승인대기";
        }else if(state == '8'){
            state_str = "승인거부";
        }else if(state == '9'){
            state_str = "판매금지";
        }else if(state == '88'){
            state_str = "품절";
        }

        if(product_type == '77'){
            var saveDatasName = 'fpid';
        }else{
            var saveDatasName = 'rpid';
        }

        var brandName = (bName)	?	'<b>'+bName+'</b>':'';
        var imgTag = '<img id="pImage_'+ms_productSearch.groupCode+'_'+pID+'" src="'+imgSrc+'" title="['+pID+']'+pName+'"  height=60 onerror="this.src=\'/admin/images/noimages_50.gif\';" style="margin:5px;" />';

        if(mode == 'L')	{
            //var imgTag = '<img id="pImage_'+ms_productSearch.groupCode+'_'+pID+'" src="'+imgSrc+'" title="['+pID+']'+pName+'"  height=50 onerror="this.src=\'/admin/images/noimages_50.gif\';" />';

            str += '<table width="100%" border="0"  '+(state != 1 ? 'class="translucent" ':'')+' >';
            str += '<col width="30" /><col width="80" /><col width="*" />';
            str += '<tr align="center" height=100>';
            str += '<td><input type="checkbox" name="pList" value="'+pID+'" />';
            str += '<input type="hidden" name="goods_infos['+pID+'][listPid]" class="listPid"  value="'+pID+'" />';
            str += '<span style="display:none;" id="pName_'+ms_productSearch.groupCode+'_'+pID+'">'+pName+'</span>';
            str += '<span style="display:none;" id="brandName_'+ms_productSearch.groupCode+'_'+pID+'">'+brandName+'</span>';
            str += '<span style="display:none;" id="pPrice_'+ms_productSearch.groupCode+'_'+pID+'">'+pPrice+'</span>';
            str += '<span style="display:none;" id="coprice_'+ms_productSearch.groupCode+'_'+pID+'">'+coprice+'</span>';
            str += '<span style="display:none;" id="wholesale_price_'+ms_productSearch.groupCode+'_'+pID+'">'+wholesale_price+'</span>';
            str += '<span style="display:none;" id="wholesale_sellprice_'+ms_productSearch.groupCode+'_'+pID+'">'+wholesale_sellprice+'</span>';
            str += '<span style="display:none;" id="listprice_'+ms_productSearch.groupCode+'_'+pID+'">'+listprice+'</span>';
            str += '<span style="display:none;" id="disp_'+ms_productSearch.groupCode+'_'+pID+'">'+disp+'</span>';
            str += '<span style="display:none;" id="state_'+ms_productSearch.groupCode+'_'+pID+'">'+state+'</span>';
            str += '<span style="display:none;" id="prdType_'+ms_productSearch.groupCode+'_'+pID+'">'+product_type+'</span>';
            str += '<span style="display:none;" id="pGid_'+ms_productSearch.groupCode+'_'+pID+'">'+gid+'</span>';

            str += '<input type="hidden" name="sell_type" id="sell_type" value="'+sellprice_type+'"></td>';
            str += '<td id="imgObj_'+pID+'" onclick="ms_productSearch._setProduct(\'div_productSelect\',\'R\',\''+pID+'\',\'\',\''+pName+'\',\''+brandName+'\',\''+pPrice+'\',\''+listprice+'\',\''+reserve+'\',\''+coprice+'\',\''+wholesale_price+'\',\''+wholesale_sellprice+'\',\''+disp+'\',\''+state+'\',\''+dcprice+'\',\''+vieworder+'\',\''+viewcnt+'\',\''+regdate+'\',\'\',\''+product_type+'\',\''+gid+'\');">';
            str += ''+imgTag+'';
            str += '<input type="hidden" name="listPid" class="listPid"  value="'+pID+'" />';

            //str += '<span style="display:none;" id="pName_'+ms_productSearch.groupCode+'_'+pID+'">'+pName+'</span>';

            str += '</td>';
            str += '<td id="textObj_'+pID+'" style="text-align:left;" onclick="ms_productSearch._setProduct(\'div_productSelect\',\'R\',\''+pID+'\',\'\',\''+pName+'\',\''+brandName+'\',\''+pPrice+'\',\''+listprice+'\',\''+reserve+'\',\''+coprice+'\',\''+wholesale_price+'\',\''+wholesale_sellprice+'\',\''+disp+'\',\''+state+'\',\''+dcprice+'\',\''+vieworder+'\',\''+viewcnt+'\',\''+regdate+'\',\'\',\''+product_type+'\',\''+gid+'\');">';
            //str += '<div style="margin:3px 0 0 0;">개별수수료 : '+(one_commission == 'Y' ? '사용':'사용안함')+'</div>';
            str += '<div style="margin:3px 0 0 0;">'+state_str+' -'+(disp == 1 ? '노출함':'노출안함')+'</div>';
            str += brandName;
            str += '<div style="margin:3px 0 0 0;width:170px;text-overflow:ellipsis; overflow:hidden;white-space: nowrap;">'+pName+'</div><!--<div style="margin:3px 0 0 0;">도매가 : '+wholesale_price+'원 > '+wholesale_sellprice+' 원</div>-->';
            // str += '<div class=small style="margin:3px 0 0 0;">판매가 : '+FormatNumber2(listprice)+'원 > '+FormatNumber2(pPrice)+' 원</div>';
            str += '<div class=small style="margin:3px 0 0 0;">판매가 : '+FormatNumber2(listprice)+'원 </div>';
            if(pPrice > dcprice){
                str += '<div style="margin:3px 0 0 0;">혜택가:'+FormatNumber2(dcprice)+'원 ( '+FormatNumber2(Math.round((listprice-dcprice)/listprice*100),1)+'%)</div>';
            }
            //str += '<div style="margin:3px 0 0 0;">WMS품목코드 : '+gid+'</div>';
            str += '</td>';
            str += '</tr>';
            str += '<tr><td colspan=3 class="dot-x"></td></tr>';
            str += '</table>';
            $('#'+obj).append(str);


        }	else if(mode == 'A')	{ // 상품검색창 선택된 오른쪽 상품

            str += '<table id="tb_productList_'+ms_productSearch.groupCode+'_'+pID+'" '+(state != 1 ? 'class="translucent" ':'')+'  width="100%" border="0"><!--style="background:url(/admin/image/dot.gif) repeat-x left bottom;"-->';
            str += '<col width="30" /><col width="60" /><col width="*" />';
            str += '<tr align="center" >';
            str += '<td><input type="checkbox" name="pList2" value="'+pID+'" /></td>';
            str += '<td>'+imgTag+'<input type="hidden" name="listPid" value="'+pID+'" /><span id="brandName_'+ms_productSearch.groupCode+'_'+pID+'" >'+brandName+'</span><span style="display:none;" id="pName_'+ms_productSearch.groupCode+'_'+pID+'">'+pName+'</span><span style="display:none;" id="pPrice_'+ms_productSearch.groupCode+'_'+pID+'">'+pPrice+'</span></td>';
            str += '<td style="text-align:left;" nowrap>'+brandName+'<div style="margin:3px 0 0 0;width:170px;text-overflow:ellipsis; overflow:hidden;white-space: nowrap;">'+pName+'</div>';
            str += '<div class=small style="margin:3px 0 0 0;">판매가:'+pPrice+'원</div>';
            if(pPrice > dcprice){
                str += '<div class=small style="margin:3px 0 0 0;">혜택가:'+FormatNumber2(dcprice)+'원 ( '+FormatNumber2(Math.round((listprice-dcprice)/listprice*100),1)+'%)</div>';
            }
            //str += '<div style="margin:3px 0 0 0;">WMS품목코드 : '+gid+'</div>';
            str += '</td>';

            str += '</tr>';
            str += '<tr><td colspan=3 class="dot-x"></td></tr>';
            str += '</table>';
            $('#'+obj).append(str);

        }	else if(mode == 'R')	{

            isReg = false;
            $('input:hidden[name="'+saveDatasName+'['+ms_productSearch.groupCode+'][]"]').each(function() {

                if(pID == $(this).val())	{
                    isReg = true;
                    return false;
                }
            });

            if(!isReg)	{
                str += '<table id="tb_productList_'+ms_productSearch.groupCode+'_'+pID+'" width="100%" border="0" ><!--style="background:url(/admin/image/dot.gif) repeat-x left bottom;"-->';
                str += '<col width="30" /><col width="60" /><col width="*" />';
                str += '<tr align="center" height=100>';
                str += '<td><input type="checkbox" name="pList2" value="'+pID+'" /></td>';
                str += '<td>'+$('#imgObj_'+pID).html()+'</td>';
                str += '<td style="text-align:left;">'+$('#textObj_'+pID).html()+'</td>';
                str += '</tr>';
                str += '<tr><td colspan=3 class="dot-x"></td></tr>';
                str += '</table>';


                if(ms_productSearch.submcode == "clipart"){

					/*
					 str2 += '<li id="li_productList_'+ms_productSearch.groupCode+'_'+pID+'" style="width:100px;padding:4px 2px 2px 2px;"  '+(state != 1 ? 'class="translucent" ':'')+' >';
					 str2 += '<span class="small">'+state_str+'-'+(disp == 1 ? '노출함':'노출안함')+'</span><br>';
					 str2 += brandName;
					 str2 += $('#imgObj_'+pID).html()+'<br>';
					 str2 += '<div style="margin-top:4px;" class="goods_search_text">'+pID+'</div>';
					 str2 += '<div style="display:block;" class="goods_search_text">'+pName+'..</div>';
					 str2 += '<input type="hidden" name="rpid['+ms_productSearch.groupCode+'][]" value="'+pID+'" />';
					 str2 += '</li>';
					 */
                    str2 += '<li id="li_productList_'+ms_productSearch.groupCode+'_'+pID+'" state="'+state+'" '+(state != 1 ? 'class="translucent" ':'')+' vieworder="' + vieworder + '" viewcnt="' + viewcnt + '" regdate="' + regdate + '" style="float:left;width:100px;">';
                    str2 += '<table width=100% cellspacing=1 cellpadding=0 style="table-layout:fixed;height:220px;">';
                    //str2 += '<tr height=20><td class="small" style="background-color:gray;color:#ffffff;" nowrap>개별수수료</td><td class="small" style="background-color:gray;color:#ffffff;">'+(one_commission == 'Y' ? '사용':'사용안함')+'</td></tr>';
                    str2 += '<tr><td class="small" style="background-color:gray;color:#ffffff;" nowrap>'+state_str+'</td><td class="small" style="background-color:gray;color:#ffffff;">'+(disp == 1 ? '노출함':'노출안함')+'</td></tr>';
                    str2 += '<tr><td colspan=2>'+ $('#imgObj_'+pID).html();
                    str2 += '<input type="hidden" name="'+saveDatasName+'['+ms_productSearch.groupCode+'][]" value="'+pID+'" />';
                    str2 += '<input type="hidden" id="dcprice_'+ms_productSearch.groupCode+'_'+pID+'" value="'+dcprice+'" />';
                    str2 += '<span style="display:none;" id="pName_'+ms_productSearch.groupCode+'_'+pID+'">'+pName+'</span>';
                    str2 += '<span style="display:none;" id="pPrice_'+ms_productSearch.groupCode+'_'+pID+'">'+pPrice+'</span>';
                    str2 += '<span style="display:none;" id="listprice_'+ms_productSearch.groupCode+'_'+pID+'">'+listprice+'</span>';
                    str2 += '<span style="display:none;" id="prdType_'+ms_productSearch.groupCode+'_'+pID+'">'+product_type+'</span>';
                    str2 += '<span style="display:none;" id="pGid_'+ms_productSearch.groupCode+'_'+pID+'">'+gid+'</span>';

                    str2 += '</td></tr>';

                    str2 += '<tr>';
                    str2 += '<td colspan=2>';
                    str2 += '<div style="margin-top:4px;" class="goods_search_text">'+pID+'</div><div style="display:inline;" class="goods_search_text"  >';
                    str2 += pName.replace(' ','').substring(0,8)+'..';
                    // str2 += '<div class=small style="margin:3px 0 0 0;">판매가 : '+FormatNumber2(listprice)+'원 > <span class="amount">'+FormatNumber2(pPrice)+'</span> 원</div>';
                    str2 += '<div class=small style="margin:3px 0 0 0;">판매가 : <span class="amount">'+FormatNumber2(listprice)+'</span>원 </div>';
                    if(pPrice > dcprice){
                        str2 += '<div class=small  style="margin:3px 0 0 0;">혜택가:<span class="discountAmount">'+FormatNumber2(dcprice)+'</span>원 ( '+FormatNumber2(Math.round((listprice-dcprice)/listprice*100),1)+'%)</div>';
                    }
                    //str2 += '<div style="margin:3px 0 0 0;">WMS품목코드 : '+gid+'</div>';
                    str2 += '</div>';
                    str2 += '</td>';
                    str2 += '</tr>';
                    str2 += '</table>';
                    str2 += '</li>';

                }else if(ms_productSearch.submcode == "list"){

                    $("#non_result_area").remove();
                    //str2 += '<li id="li_productList_'+ms_productSearch.groupCode+'_'+pID+'" style="width:70px;">'+$('#imgObj_'+pID).html()+'<br><div style="margin-top:4px;">'+pID+'</div><input type="hidden" name="rpid['+ms_productSearch.groupCode+'][]" value="'+pID+'" /></li>';
                    str2 += '<tr align=center height="27">';
                    //str2 += '<td>'+pName+'</td>';
                    str2 += '<td id="li_productList_'+ms_productSearch.groupCode+'_'+pID+'">'+pName+'<input type="hidden" name="goods_infos['+pID+'][listPid]" class="listPid"  value="'+pID+'" /><span style="display:none;" id="pName_'+ms_productSearch.groupCode+'_'+pID+'">'+pName+'</span><input type="hidden" name="'+saveDatasName+'['+ms_productSearch.groupCode+'][]" value="'+pID+'" /><span style="display:none;" id="brandName_'+ms_productSearch.groupCode+'_'+pID+'">'+brandName+'</span><span style="display:none;" id="pPrice_'+ms_productSearch.groupCode+'_'+pID+'">'+pPrice+'</span></td>';
                    str2 += '<td></td>';
                    str2 += '<td><input type="text" class="textbox number" name="goods_infos['+pID+'][amount]" class="amount"  value="1" style="width:30px;"/></td>';
                    str2 += '<td class=small>판매가 : '+pPrice+'</td>';
                    if(pPrice > dcprice){
                        str2 += '<div class=small  style="margin:3px 0 0 0;">혜택가:'+FormatNumber2(dcprice)+'원 ( '+FormatNumber2(Math.round((listprice-dcprice)/listprice*100),1)+'%)</div>';
                    }
                    str2 += '<td><input type="text" class="textbox number" name="goods_infos['+pID+'][listprice]" class="listprice"  value="'+listprice+'" style="width:80px;"/></td>';
                    str2 += '<td><input type="text" class="textbox number" name="goods_infos['+pID+'][sellprice]" class="sellprice"  value="'+pPrice+'" style="width:80px;" /></td>';
                    str2 += '<td>10%</td>';
                    str2 += '<td>'+pPrice+'</td>';
                    str2 += '<td>'+pPrice+'</td>';
                    str2 += '<td>-</td>';
                    str2 += '<td>-</td>';
                    //str2 += '<td>10%</td>';
                    str2 += '</tr>';
                }else if(ms_productSearch.submcode == "list2"){

                    $("#non_result_area").remove();
                    //str2 += '<li id="li_productList_'+ms_productSearch.groupCode+'_'+pID+'" style="width:70px;">'+$('#imgObj_'+pID).html()+'<br><div style="margin-top:4px;">'+pID+'</div><input type="hidden" name="rpid['+ms_productSearch.groupCode+'][]" value="'+pID+'" /></li>';
                    str2 += '<tr align=center height="27">';
                    //str2 += '<td>'+pName+'</td>';// 상품 디스플레이//,p.coprice,p.wholesale_price,p.wholesale_sellprice,p.listprice
                    str2 += '<td id="li_productList_'+ms_productSearch.groupCode+'_'+pID+'">'+pName+'<input type="hidden" name="goods_infos['+pID+'][listPid]" class="listPid"  value="'+pID+'" /><span style="display:none;" id="pName_'+ms_productSearch.groupCode+'_'+pID+'">'+pName+'</span><input type="hidden" name="'+saveDatasName+'['+ms_productSearch.groupCode+'][]" value="'+pID+'" /><span style="display:none;" id="brandName_'+ms_productSearch.groupCode+'_'+pID+'">'+brandName+'</span><span style="display:none;" id="pPrice_'+ms_productSearch.groupCode+'_'+pID+'">'+pPrice+'</span><span style="display:none;" id="coprice_'+ms_productSearch.groupCode+'_'+pID+'">'+coprice+'</span><span style="display:none;" id="wholesale_price_'+ms_productSearch.groupCode+'_'+pID+'">'+wholesale_price+'</span><span style="display:none;" id="wholesale_sellprice_'+ms_productSearch.groupCode+'_'+pID+'">'+wholesale_sellprice+'</span><input type="hidden" name="sell_type" id="sell_type" value="'+sellprice_type+'"></td>';
                    //str2 += '<td id="td_opn_ix_'+pID+'"><select name="goods_infos['+pID+'][opn_ix]" id="opn_ix_'+pID+'" onchange="product_option(\''+pID+'\');"  style="width:200px;"><option value="">옵션 선택</option></select></td>';

                    str2 += '<td id="td_coprice_'+pID+'">'+ms_productSearch.FormatNumber(coprice)+'</td><input type="hidden" class="textbox number" name="goods_infos['+pID+'][coprice]" id="coprice_'+pID+'"  value="1" style="width:30px;"/>';
                    str2 += '<td id="td_listprice_'+pID+'">'+ms_productSearch.FormatNumber(listprice)+'</td><input type="hidden" class="textbox number" name="goods_infos['+pID+'][listprice]" id="listprice_'+pID+'"  value="'+listprice+'" style="width:80px;"/>';
                    str2 += '<td id="td_sellprice_'+pID+'">'+ms_productSearch.FormatNumber(pPrice)+'</td><input type="hidden" class="textbox number" name="goods_infos['+pID+'][sellprice]" id="sellprice_'+pID+'"  value="'+pPrice+'" style="width:80px;"/>';
                    str2 += '<td id="td_dcprice_'+pID+'"><input type="text" class="textbox number" name="goods_infos['+pID+'][dcprice]"  id="dcprice_'+pID+'" onkeyup="calcurate_maginrate(\''+pID+'\')" value="" style="width:60px;" /></td>';
                    str2 += '<td id="td_unit_price_'+pID+'">'+ms_productSearch.FormatNumber(pPrice)+'</td><input type="hidden" class="textbox number" name="goods_infos['+pID+'][unit_price]" id="unit_price_'+pID+'"  value="'+pPrice+'" style="width:60px;" readonly/>';
                    str2 += '<td id="td_amount_'+pID+'"><input type="text" class="textbox number" name="goods_infos['+pID+'][amount]" id="amount_'+pID+'" onkeyup="calcurate_maginrate(\''+pID+'\')" value="1" style="width:30px;"/></td>';

                    str2 += '<td id="td_dc_unit_price_'+pID+'">'+ms_productSearch.FormatNumber(Math.round(pPrice/11*10))+'</td><input type="hidden" class="textbox number" name="goods_infos['+pID+'][dc_unit_price]" id="dc_unit_price_'+pID+'"  value="'+Math.round(pPrice/11*10)+'" style="width:30px;"/>';
                    str2 += '<td id="td_dc_tax_'+pID+'">'+ms_productSearch.FormatNumber(Math.floor(pPrice/11))+'</td><input type="hidden" class="textbox number" name="goods_infos['+pID+'][dc_tax]" id="dc_tax_'+pID+'"  value="'+Math.floor(pPrice/11)+'" style="width:30px;"/>';
                    str2 += '<td id="td_total_price_'+pID+'">'+ms_productSearch.FormatNumber(pPrice)+'</td><input type="hidden" class="textbox number" name="goods_infos['+pID+'][total_price]" id="total_price_'+pID+'"  value="'+pPrice+'" style="width:30px;"/>';
                    str2 += '<td id="td_discount_rate_'+pID+'">0%</td><input type="hidden" class="textbox number" name="goods_infos['+pID+'][discount_rate]" id="discount_rate_'+pID+'"  value="0" style="width:30px;"/>';
                    //str2 += '<td>10%</td>';
                    str2 += '</tr>';
                }

                //alert(this.mObj+":::"+obj);
                $('#'+this.mObj).append(str2);
                $('#'+obj).append(str);

                if(ms_productSearch.submcode == "list2"){
                    $.ajax({
                        url : '../estimate/estimate.act.php',
                        type : 'POST',
                        data : {pid:pID,
                            act:'search_option',
                            type:'self_estimate'
                        },
                        dataType: 'html',
                        error: function(data,error){// 실패시 실행함수
                            alert(error);},
                        success: function(transport){
                            $('select[id^=opn_ix_'+pID+']').empty();
                            $('select[id^=opn_ix_'+pID+']').append(transport)
                        }
                    });
                }

                $('ul[name=productList]>li').dblclick(function()	{
                    ms_productSearch._delProduct(this);
                });

                $('ul[name=giftList]>li').dblclick(function()	{
                    ms_productSearch._delProduct(this);
                });
            }

        }	else if(mode == 'M')	{

            if(ms_productSearch.submcode == "clipart"){
				/*
				 str += '<li id="li_productList_'+ms_productSearch.groupCode+'_'+pID+'" state="'+state+'" '+(state != 1 ? 'class="translucent" ':'')+' style="float:left;width:100px;padding:4px 2px 2px 2px;">';
				 str += '<span class="small">'+state_str+'-'+(disp == 1 ? '노출함':'노출안함')+'</span><br>';
				 str += ''+imgTag+'<input type="hidden" name="listPid" class="listPid"  value="'+pID+'" /><span style="display:none;" id="pName_'+ms_productSearch.groupCode+'_'+pID+'">'+pName+'</span><input type="hidden" name="rpid['+ms_productSearch.groupCode+'][]" value="'+pID+'" /><span style="display:none;" id="brandName_'+ms_productSearch.groupCode+'_'+pID+'">'+brandName+'</span><span style="display:none;" id="pName_'+ms_productSearch.groupCode+'_'+pID+'">'+pName+'</span><div style="margin-top:4px;" class="goods_search_text">'+pID+'</div><div style="display:inline;" class="goods_search_text" onclick="alert(1);">';
				 str += pName.replace(' ','').substring(0,6)+'..';
				 str += '</div><span style="display:none;" id="pPrice_'+ms_productSearch.groupCode+'_'+pID+'">'+pPrice+'</span></li>';
				 */
                str += '<li id="li_productList_'+ms_productSearch.groupCode+'_'+pID+'" state="'+state+'" '+(state != 1 ? 'class="translucent" ':'')+' vieworder="' + vieworder + '" viewcnt="' + viewcnt + '" regdate="' + regdate + '" style="float:left;width:100px;">';
                str += '<table width=100%  cellspacing=1 cellpadding=0 style="table-layout:fixed;height:220px;">';
                //str += '<tr height=20><td class="small" style="background-color:gray;color:#ffffff;" nowrap>개별수수료</td><td class="small" style="background-color:gray;color:#ffffff;">'+(one_commission == 'Y' ? '사용':'사용안함')+'</td></tr>';
                str += '<tr height=20><td class="small" style="background-color:gray;color:#ffffff;" nowrap>'+state_str+'</td><td class="small" style="background-color:gray;color:#ffffff;">'+(disp == 1 ? '노출함':'노출안함')+'</td></tr>';
                str += '<tr><td colspan=2>'+imgTag+'<input type="hidden" name="listPid" class="listPid"  value="'+pID+'" />';
                str += '<span style="display:none;" id="pName_'+ms_productSearch.groupCode+'_'+pID+'">'+pName+'</span>';
                str += '<input type="hidden" name="'+saveDatasName+'['+ms_productSearch.groupCode+'][]" value="'+pID+'" />';
                str += '<span style="display:none;" id="brandName_'+ms_productSearch.groupCode+'_'+pID+'">'+brandName+'</span>';

                str += '<span style="display:none;" id="pPrice_'+ms_productSearch.groupCode+'_'+pID+'">'+pPrice+'</span>';
                str += '<span style="display:none;" id="listprice_'+ms_productSearch.groupCode+'_'+pID+'">'+listprice+'</span>';
                str += '<input type="hidden" id="dcprice_'+ms_productSearch.groupCode+'_'+pID+'" value="'+dcprice+'" />';
                str += '</td></tr>';
                str += '<tr>';
                str += '<td colspan=2>';
                str += '<div style="margin-top:4px;" class="goods_search_text">'+pID+'</div><div style="display:inline;text-align:left;" class="goods_search_text" >';
                str += pName.replace(' ','').substring(0,6)+'..';
                str += '</div>';
                str += '<div class=small style="margin:3px 0 0 0;">판매가:<span class="amount">'+FormatNumber2(pPrice)+'</span>원  </div>';
                //alert(pPrice +">"+ dcprice);
                if(pPrice > dcprice){
                    str += '<div class=small style="margin:3px 0 0 0;">혜택가:<span class="discountAmount">'+FormatNumber2(dcprice)+'</span>원 ( '+FormatNumber2(Math.round((listprice-dcprice)/listprice*100),1)+'%)</div>';
                }
                //str += '<div style="margin:3px 0 0 0;">WMS품목코드 : '+gid+'</div>';
                str += '<span style="display:none;" id="pGid_'+ms_productSearch.groupCode+'_'+pID+'">'+gid+'</span>';
                str += '</td>';
                str += '</tr>';
                str += '</table>';
                str += '</li>';

            }else{
                str += '<tr align=center height="27">';
                //str += '<td>'+pName+'</td>';
                str += '<td id="li_productList_'+ms_productSearch.groupCode+'_'+pID+'">'+pName+'<input type="hidden" name="goods_infos['+pID+'][listPid]" class="listPid"  value="'+pID+'" /><span style="display:none;" id="pName_'+ms_productSearch.groupCode+'_'+pID+'">'+pName+'</span><input type="hidden" name="'+saveDatasName+'['+ms_productSearch.groupCode+'][]" value="'+pID+'" /><span style="display:none;" id="brandName_'+ms_productSearch.groupCode+'_'+pID+'">'+brandName+'</span><span style="display:none;" id="pPrice_'+ms_productSearch.groupCode+'_'+pID+'">'+pPrice+'</span></td>';
                str += '<td></td>';
                str += '<td><input type="text" class="textbox number" name="goods_infos['+pID+'][amount]" class="amount"  value="1" style="width:30px;"/></td>';
                str += '<td>'+pPrice+'</td>';
                str += '<td><input type="text" class="textbox number" name="goods_infos['+pID+'][listprice]" class="listprice"  value="'+listprice+'" style="width:80px;"/></td>';
                str += '<td><input type="text" class="textbox number" name="goods_infos['+pID+'][sellprice]" class="sellprice"  value="'+pPrice+'" style="width:80px;" /></td>';
                str += '<td>10%</td>';
                str += '<td>'+pPrice+'</td>';
                str += '<td>'+pPrice+'</td>';
                str += '<td>-</td>';
                str += '<td>-</td>';
                //str += '<td>10%</td>';
                str += '</tr>';

            }

            $('#'+obj).append(str);
            $('ul[name=productList]>li').dblclick(function()	{
                ms_productSearch._delProduct(this);
            });

            $('ul[name=giftList]>li').dblclick(function()	{
                ms_productSearch._delProduct(this);
            });
        }

    },

    // 개별 삭제
    _delProduct: function(obj)
    {
        $(obj).html('');
        $(obj).remove();
    },

    // 선택 삭제
    _delProductSel: function()
    {
        $('input[name="pList2"]:checked').each(function()	{
            $('table#tb_productList_'+ms_productSearch.groupCode+'_'+$(this).val()).html('');
            $('table#tb_productList_'+ms_productSearch.groupCode+'_'+$(this).val()).remove();
            $('li#li_productList_'+ms_productSearch.groupCode+'_'+$(this).val()).html('');
            $('li#li_productList_'+ms_productSearch.groupCode+'_'+$(this).val()).remove();
        });
    },

    // 모두 삭제
    _delProductAll: function()
    {
        $('#productList_'+ms_productSearch.groupCode).html('');
        $('#div_productSelect').html('');
    },

    _closeDiv: function()
    {
        $('div#div_productSearchBox').slideUp();
        var successCallback = function() {
                //$( "#productList_2" ).draggable();
                $("ul").sortable({
                    stop: function() {
                        console.log("done");
                    }
                });
                $("ul").disableSelection();
            },
            failCallback = function() {
                // 실패시에는 실행 실패 메시지를 출력한다.
                alert( 'Fail' );
            };
        $.getScript( '/admin/js/ui/jquery-ui-1.8.9.custom.js' ).done( successCallback ).fail( failCallback );
    },

    // 페이지 스트링
    _getPageString: function(total_record)
    {
        //alert(total_record);
        var page = (arguments[1])	?	arguments[1]:this._page;
        var pageString = '';
        var page_count	= 5;	// 표현할 페이지 스트링 수
        var total_page	= Math.ceil(total_record / this._max);	// 총 페이지 수
        var setPage = Math.floor((page - 1) / page_count) * page_count + 1;
        if((prev = page - page_count) > 0)	pageString += '<img src="/admin/image/pre_pageset.gif" onclick=\'ms_productSearch._getProductList(null, null,'+prev+',"'+this._cid+'","'+this._depth+'");\' style="cursor:pointer;vertical-align:middle;" /> ';
        var i = 1;
        var tmp = new Array();
        //alert(this._cid);
        while(i <= page_count && total_page >= setPage)	{
            if(setPage == page)	tmp[i] = '<span style="color:#FF0000;font-weight:bold;">'+setPage+'</span>';
            else	tmp[i] = '<a href=\'javascript:ms_productSearch._getProductList(null, null,'+setPage+',"'+this._cid+'","'+this._depth+'");\' style="font-weight:bold;color:gray;">'+setPage+'</a>';
            setPage++;
            i++;
        }
        pageString += tmp.join(' ');

        if(page + page_count < total_page)	{
            next = page + page_count;
        }	else	{
            if(setPage < total_page)	{
                next = total_page;
            }	else	{
                next = '';
            }
        }
        if(next)	pageString += ' <img src="/admin/image/next_pageset.gif" onclick=\'ms_productSearch._getProductList(null, null,'+next+',"'+this._cid+'","'+this._depth+'");\' style="cursor:pointer;vertical-align:middle;" />';

        if(setPage > page_count + 1)	{
            pageString = '<font style="color:gray;"><a href=\'javascript:ms_productSearch._getProductList(null, null, 1,"'+this._cid+'","'+this._depth+'");\' style="font-weight:bold;color:gray;">1</a>...</font> ' + pageString;
        }

        if(setPage <= total_page)	{
            pageString += ' <font style="color:gray;">...<a href=\'javascript:ms_productSearch._getProductList(null, null,'+(total_page)+',"'+this._cid+'","'+this._depth+'");\' style="font-weight:bold;color:gray;">'+total_page+'</a></font>';
        }
        // pageSearchString = "&nbsp;<input type='number' id='pageSearchText' name='pageSearchText' min=1 max="+total_page+" size='1' onkeyup='if(this.value > "+total_page+" || this.value < 1) this.value="+total_page+"' onkeyPress=\"if (event.keyCode==13){ms_productSearch._getProductList(null, null, this.value,\'"+this._cid+"\',\'"+this._depth+"\')}\">"
        // pageSearchString += "<a href=\"javascript:ms_productSearch._getProductList(null, null, pageSearchText.value,\'"+this._cid+"\',\'"+this._depth+"\')\" style=\"font-weight:bold;color:gray;\">이동</a>";

        return '<div style="padding:10px 0 0 0;">'+pageString+'</div>';

    },

    getProduct: function(type)	{
        //$.data('pid',

    },

    FormatNumber2: function(num)
    {

        fl=''
        if(isNaN(num)) { /*alert('문자는 사용할 수 없습니다.');*/return 0}
        if(num==0) return num

        if(num<0){
            num=num*(-1)
            fl='-'
        }else{
            num=num*1 //처음 입력값이 0부터 시작할때 이것을 제거한다.
        }
        num = new String(num)
        temp=''
        co=3
        num_len=num.length
        while (num_len>0){
            num_len=num_len-co
            if(num_len<0){co=num_len+co;num_len=0}
            temp=','+num.substr(num_len,co)+temp
        }
        return fl+temp.substr(1)
    },

    FormatNumber: function(num)
    {

        num=new String(num)
        num=num.replace(/,/gi,'')
        //  pricecheckmode = false;

        return FormatNumber2(num)
    },

    FilterProducts : function() {
        var productsWrapper = $('.goods_manual_area');
        if (productsWrapper.length > 0) {
            var formatPrice = function(value, mode) {
                var output;
                if (!value) { return 0; }
                switch(mode) {
                    case 'toNumber' :
                        output = parseFloat(value.replace(/,/g, ''));
                        break;
                    case 'toString' :
                        output = Math.ceil(value).toString().replace(/,/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                        break;
                    default:
                        break;
                }
                return output;
            };
            // Custom sorting plugin
            var sortProducts = function($data, customOptions) {
                var options = {
                    reversed: false,
                    by: function(a) { return a.text(); }
                };
                $.extend(options, customOptions);
                arr = $data.get();
                arr.sort(function(a, b) {
                    var valA = options.by($(a));
                    var valB = options.by($(b));
                    if (options.reversed) {
                        return (valA < valB) ? 1 : (valA > valB) ? -1 : 0;
                    } else {
                        return (valA < valB) ? -1 : (valA > valB) ? 1 : 0;
                    }
                });
                return $(arr);
            };

            productsWrapper.each(function(i, elt) {
                $(elt).find('.sortingFilter select').bind('change', function() {
                    var sortedData = null,
                        products = $(elt).find('ul[name=productList]>li'),
                        filteredData;

                    products.each(function(j) { $(this).attr('data-id', 'product-id-' + j); });
                    filteredData = products.clone();

                    switch($(this).val()) {
                        case 'lowestPrice':
                            sortedData = sortProducts(filteredData, {
                                'by' : function(v) {
                                    var amount = $(v).find('.discountAmount').length ? $(v).find('.discountAmount').html() : $(v).find('.amount').html();
                                    return formatPrice(amount, 'toNumber');
                                }
                            });
                            break;

                        case 'highestPrice':
                            sortedData = sortProducts(filteredData, {
                                'reversed' : true,
                                'by' : function(v) {
                                    var amount = $(v).find('.discountAmount').length ? $(v).find('.discountAmount').html() : $(v).find('.amount').html();
                                    return formatPrice(amount, 'toNumber');
                                }
                            });
                            break;

                        case 'vieworder':
                            sortedData = sortProducts(filteredData, {
                                'by' : function(v) {
                                    return parseFloat($(v).attr('vieworder'));
                                }
                            });
                            break;

                        case 'viewcnt':
                            sortedData = sortProducts(filteredData, {
                                'by' : function(v) {
                                    return parseFloat($(v).attr('viewcnt') ? $(v).attr('viewcnt') : 0);
                                }
                            });
                            break;

                        case 'regdate':
                            sortedData = sortProducts(filteredData, {
                                'by' : function(v) {
                                    return $(v).attr('regdate');
                                }
                            });
                            break;

                        default:
                            break;
                    }
                    if (sortedData != null) {
                        $(elt).find('ul[name=productList]').quicksand(sortedData, {
                            'duration': 600,
                            'easing' : 'easeInOutQuad'
                        });
                    }
                });
            });
        }
    }
};

$(function()	{
    $.ajax({
        url:'/admin/getHTML.php?act=companyList',
        type: 'get',
        data: ({act: 'companyList',
            company_id: '',
            display_type: '',
            onchange: ''
        }),
        success: function(data)	{
            ms_productSearch.companyHTML = data;
        }
    });

    //키워드검색 전체 리스트
    $.ajax({
        url:'/admin/getHTML.php?act=mallixList',
        type: 'get',
        data: ({act: 'mallixList'}),
        success: function(data)	{
            ms_productSearch.mallixHTML = data;
        }
    });

    var productListDOM = $('ul[name=productList]');
    if(productListDOM.length >= 1){
        var parentDOM = productListDOM.parents('.goods_manual_area');
        //alert($('ul[name=productList]').length +":::"+$('ul[name=productList]'));
        /**$('ul[name=productList]').sortable({
			'stop' : function (e, ui) {
				$(this).parents('.goods_manual_area').find('.sortingFilter select').blur()[0].selectedIndex = 0;
			}
		});*/
        productListDOM.multisortable({
            'start' : function(e, ui) {
                if (ui.item.hasClass($.fn.multisortable.defaults.selectedClass)) {
                    var parent = ui.item.parent();


                    // adjust placeholder size to be size of items
                    var width = parent.find('.' + $.fn.multisortable.defaults.selectedClass).length * ui.item.outerWidth();
                    ui.placeholder.width(width);
                    ui.placeholder.height(ui.item.height());
                }
            },
            'sort' : function(e, ui) {
                var parent = ui.item.parent(),
                    myIndex = ui.item.data('i'),
                    top = parseInt(ui.item.css('top').replace('px', '')),
                    left = parseInt(ui.item.css('left').replace('px', ''));

                var width = ui.item.outerWidth();
                $('.' + $.fn.multisortable.defaults.selectedClass, parent).filter(function() { return $(this).data('i') > myIndex; }).each(function() {
                    var item = $(this);
                    item.css({
                        left: left + width,
                        top: top,
                        position: 'absolute',
                        zIndex: 1000,
                        width: ui.item.width()
                    });

                    width += item.outerWidth();
                });
            },
            'stop' : function (e, ui) {
                parentDOM.find('.sortingFilter select').blur()[0].selectedIndex = 0;
            }
        });

        productListDOM.disableSelection();
        /**if($('ul[name=productList]>li').length>0) $('ul[name=productList]').sortable({
			'stop' : function (e, ui) {
				$(this).parents('.goods_manual_area').find('.sortingFilter select').blur()[0].selectedIndex = 0;
			}
		});*/
        if($('ul[name=productList]>li').length>0) {
            productListDOM.multisortable({
                'start' : function(e, ui) {
                    if (ui.item.hasClass($.fn.multisortable.defaults.selectedClass)) {
                        var parent = ui.item.parent();


                        // adjust placeholder size to be size of items
                        var width = parent.find('.' + $.fn.multisortable.defaults.selectedClass).length * ui.item.outerWidth();
                        ui.placeholder.width(width);
                        ui.placeholder.height(ui.item.height());
                    }
                },
                'sort' : function(e, ui) {
                    var parent = ui.item.parent(),
                        myIndex = ui.item.data('i'),
                        top = parseInt(ui.item.css('top').replace('px', '')),
                        left = parseInt(ui.item.css('left').replace('px', ''));

                    var width = ui.item.outerWidth();
                    $('.' + $.fn.multisortable.defaults.selectedClass, parent).filter(function() { return $(this).data('i') > myIndex; }).each(function() {
                        var item = $(this);
                        item.css({
                            left: left + width,
                            top: top,
                            position: 'absolute',
                            zIndex: 1000,
                            width: ui.item.width()
                        });

                        width += item.outerWidth();
                    });
                },
                'stop' : function (e, ui) {
                    parentDOM.find('.sortingFilter select').blur()[0].selectedIndex = 0;
                }
            });
        }

        if($('ul[name=productList]>li').length>0) productListDOM.disableSelection();
    }

    $('ul[name=productList]>li').dblclick(function() {
        ms_productSearch._delProduct(this);
    });

    $('ul[name=giftList]>li').dblclick(function()	{
        ms_productSearch._delProduct(this);
    });
    ms_productSearch.FilterProducts();
});


