/////////       insertAdjacentHTML 를 Firefox 에서 사용하기 위한 스크립트 시작        /////////////
if(typeof HTMLElement!="undefined" && !HTMLElement.prototype.insertAdjacentElement){
    HTMLElement.prototype.insertAdjacentElement = function (where,parsedNode)
    {
        switch (where){
        case 'beforeBegin':
            this.parentNode.insertBefore(parsedNode,this)
            break;
        case 'afterBegin':
            this.insertBefore(parsedNode,this.firstChild);
            break;
        case 'beforeEnd':
            this.appendChild(parsedNode);
            break;
        case 'afterEnd':
            if (this.nextSibling) 
this.parentNode.insertBefore(parsedNode,this.nextSibling);
            else this.parentNode.appendChild(parsedNode);
            break;
        }
    }

    HTMLElement.prototype.insertAdjacentHTML = function (where,htmlStr)
    {
        var r = this.ownerDocument.createRange();
        r.setStartBefore(this);
        var parsedHTML = r.createContextualFragment(htmlStr);
        this.insertAdjacentElement(where,parsedHTML)
    }


    HTMLElement.prototype.insertAdjacentText = function (where,txtStr)
    {
        var parsedText = document.createTextNode(txtStr)
        this.insertAdjacentElement(where,parsedText)
    }
}
/////////       insertAdjacentHTML 를 Firefox 에서 사용하기 위한 스크립트 끝        /////////////


var idx;
var obj_table;
var select_gorup_code = 1;
var group_idx=0;
function my_init(group_total) {
  idx=group_total;
  obj_table = $('#group_info_area0').clone(true);//document.getElementById('group_info_area0').cloneNode();
}

function callConetne(num){
	var popupWidth = 850;
	var popupHeight = 550;

	var popupX = (window.screen.width / 2) - (popupWidth / 2);
// 만들 팝업창 width 크기의 1/2 만큼 보정값으로 빼주었음

	var popupY= (window.screen.height / 2) - (popupHeight / 2);
// 만들 팝업창 height 크기의 1/2 만큼 보정값으로 빼주었음

	window.open('./content_pop.php?gubun='+num,num,'status=no, height=' + popupHeight  + ', width=' + popupWidth  + ', left='+ popupX + ', top='+ popupY);
}

function callGroupConetne(num, groupNum){
	var popupWidth = 850;
	var popupHeight = 550;

	var popupX = (window.screen.width / 2) - (popupWidth / 2);
// 만들 팝업창 width 크기의 1/2 만큼 보정값으로 빼주었음

	var popupY= (window.screen.height / 2) - (popupHeight / 2);
// 만들 팝업창 height 크기의 1/2 만큼 보정값으로 빼주었음

	window.open('./content_pop.php?gubun='+num+'&group_num='+groupNum,num,'status=no, height=' + popupHeight  + ', width=' + popupWidth  + ', left='+ popupX + ', top='+ popupY);
}

function colorPopUp(id){
	var popupWidth = 440;
	var popupHeight = 206;

	var popupX = (window.screen.width / 2) - (popupWidth / 2);
// 만들 팝업창 width 크기의 1/2 만큼 보정값으로 빼주었음

	var popupY= (window.screen.height / 2) - (popupHeight / 2);
// 만들 팝업창 height 크기의 1/2 만큼 보정값으로 빼주었음

	window.open('./colorpop.php?id='+id,id,'status=no, height=' + popupHeight  + ', width=' + popupWidth  + ', left='+ popupX + ', top='+ popupY);
}

function add_table_main(){
	idx = $("div[id^=group_info_area]").length;

	idx = parseInt(idx);

	if (idx<10) {
		obj_table_text = "";
		obj_table_text += "<div id='group_info_area"+idx+"' data-id='group_info_area"+idx+"' class='group_info_area_wrapper' group_code='"+(idx+1)+"'>";
		obj_table_text += "<div class='group_info_header' style='position:relative;padding:10px 10px;width:100%;'><img src='/admin/images/dot_org.gif' > <b style='font-family:굴림;font-size:13px;font-weight:bold;letter-spacing:-1px;word-spacing:0px;'>상품그룹  (GROUP "+(idx+1)+")</b> ";
		obj_table_text += "<a onclick='add_table_main()'><img src='../images/korean/btn_goods_group_add.gif' border=0 align=absmiddle style='position:relative;top:-2px;cursor:pointer;'></a> ";
		obj_table_text += "<a onClick=del_table('group_info_area"+idx+"','"+idx+"');><img src='../images/korean/btn_goods_group_del.gif' border=0 align=absmiddle style='position:relative;top:-2px;'></a>";
		obj_table_text += "<a href='javascript:void(0);' class='slide-up-down-link'>";
		obj_table_text += "<span class='plus'><img src='/admin/images/btn_group_close.png' alt='Plus'></span>";
		obj_table_text += "<span class='minus'><img src='/admin/images/btn_group_open.png' alt='Minus'></span>";
		obj_table_text += "</a>";
		obj_table_text += "<input type='hidden'  name='cmgr_ix["+(idx+1)+"]' value=''>";
		obj_table_text += "<input type='hidden' class='input-order' name='group_order["+(idx+1)+"]' value='"+(idx+1)+"'>";
		obj_table_text += "</div>";
		obj_table_text += "<table width='100%' border='0' cellpadding='10' cellspacing='1' class='input_table_box'>";
		obj_table_text += "<col width='12%'>";
		obj_table_text += "<col width='30%'>";
		obj_table_text += "<col width='12%'>";
		obj_table_text += "<col width='38%'>";
		obj_table_text += "<input type='hidden' class='input-number' value='"+(idx+1)+"'>";
		obj_table_text += "<tr>";
		obj_table_text += "<td class='input_box_title'> <b>그룹명</b></td>";
		obj_table_text += "<td class='input_box_item'>";
		obj_table_text += "<table border=0 width=100%>";
		obj_table_text += "<col width=50px><col width=*>";
		obj_table_text += "<tr height=28 id='tableTitleK_"+(idx+1)+"'><td>국문</td><td><textarea name='group_title["+(idx+1)+"]' id='group_title_"+(idx+1)+"' style='width:85%;height:15px'></textarea></td></tr>";
		obj_table_text += "<tr height=28 id='tableTitleE_"+(idx+1)+"'><td>영문</td><td><textarea name='group_title_en["+(idx+1)+"]' id='group_title_en_"+(idx+1)+"' style='width:85%;height:15px'></textarea></td></tr>";
		obj_table_text += "</table>";
		obj_table_text += "</td>";
		obj_table_text += "<td class='input_box_title'> <b>그룹명 설정</b></td>";
		obj_table_text += "<td class='input_box_item'>";
		obj_table_text += "<input type='radio' name='s_group_title["+(idx+1)+"]' id='s_group_title_L_"+(idx+1)+"' value='L' checked><label for='s_group_title_L_"+(idx+1)+"'> 좌측정렬</label>";
		obj_table_text += "<input type='radio' name='s_group_title["+(idx+1)+"]' id='s_group_title_C_"+(idx+1)+"' value='C'><label for='s_group_title_C_"+(idx+1)+"'> 가운데정렬</label>";
		obj_table_text += "<input type='radio' name='s_group_title["+(idx+1)+"]' id='s_group_title_R_"+(idx+1)+"' value='R'><label for='s_group_title_R_"+(idx+1)+"'> 우측정렬</label><br><br>";
		obj_table_text += "진하게<input type='checkbox' name='b_group_title["+(idx+1)+"]' id='b_group_title_"+(idx+1)+"'>";
		obj_table_text += "기울기<input type='checkbox' name='i_group_title["+(idx+1)+"]' id='i_group_title_"+(idx+1)+"'>";
		obj_table_text += "밑줄<input type='checkbox' name='u_group_title["+(idx+1)+"]' id='u_group_title_"+(idx+1)+"'><br>";
		obj_table_text += "글자색 <input type='text' name='c_group_title["+(idx+1)+"]' id='c_group_title_"+(idx+1)+"' style='width:50px' maxlength='7'><a href='javascript:void(0);' onclick=colorPopUp('c_group_title_"+(idx+1)+"')> 색상팝업창열기</a>";
		obj_table_text += "<div style='clear:both;width:100%;'><span class=small>* 글자색 미선택시 검정색(#000000)으로 자동저장 됩니다.<br /><font style='color:red'>* 추가그룹의 색상은 팝업으로 등록 가능합니다.</font></span></div>";
		obj_table_text += "</td>";
		obj_table_text += "</tr>";
		obj_table_text += "<tr>";
		obj_table_text += "<td class='input_box_title'> <b>그룹간단설명</b></td>";
		obj_table_text += "<td class='input_box_item'>";
		obj_table_text += "<table border=0 width=100%>";
		obj_table_text += "<col width=50px><col width=*>";
		obj_table_text += "<tr height=28><td>국문</td><td><textarea name='group_explanation["+(idx+1)+"]' id='group_explanation_"+(idx+1)+"' style='width:85%;height:15px'></textarea></td></tr>";
		obj_table_text += "<tr height=28><td>영문</td><td><textarea name='group_explanation_en["+(idx+1)+"]' id='group_explanation_en_"+(idx+1)+"' style='width:85%;height:15px'></textarea></td></tr>";
		obj_table_text += "</table>";
		obj_table_text += "</td>";
		obj_table_text += "<td class='input_box_title'> <b>그룹간단설명 설정</b></td>";
		obj_table_text += "<td class='input_box_item'>";
		obj_table_text += "진하게<input type='checkbox' name='b_group_explanation["+(idx+1)+"]' id='b_group_explanation_"+(idx+1)+"'>";
		obj_table_text += "기울기<input type='checkbox' name='i_group_explanation["+(idx+1)+"]' id='i_group_explanation_"+(idx+1)+"'>";
		obj_table_text += "밑줄<input type='checkbox' name='u_group_explanation["+(idx+1)+"]' id='u_group_explanation_"+(idx+1)+"'><br>";
		obj_table_text += "글자색 <input type='text' name='c_group_explanation["+(idx+1)+"]' id='c_group_explanation_"+(idx+1)+"' style='width:50px' maxlength='7'><a href='javascript:void(0);' onclick=colorPopUp('c_group_explanation_"+(idx+1)+"')> 색상팝업창열기</a>";
		obj_table_text += "<div style='clear:both;width:100%;'><span class=small>* 글자색 미선택시 검정색(#000000)으로 자동저장 됩니다.<br /><font style='color:red'>* 추가그룹의 색상은 팝업으로 등록 가능합니다.</font></span></div>";
		obj_table_text += "</td>";
		obj_table_text += "</tr>";
		obj_table_text += "<tr>";
		obj_table_text += "<td class='input_box_title'><b>컨텐츠 불러오기</b></td>";
		obj_table_text += "<td class='search_box_item' colspan=3>";
		obj_table_text += "<table border=0 style=width:100%;>";
		obj_table_text += "<col width='225px'>";
		obj_table_text += "<col width='*'>";
		obj_table_text += "<tr>";
		obj_table_text += "<td class='search_box_item' style='padding:10px 10px;' colspan=2>";
		obj_table_text += "<div id='goods_manual_area_1'>";
		obj_table_text += "<div style='width:100%;padding:5px;' id='group_product_area_1'>";
		obj_table_text += "<ui id='choiceGorupContent_"+(idx+1)+"'></ui>";
		obj_table_text += "</div>";
		obj_table_text += "</div>";
		obj_table_text += "</td>";
		obj_table_text += "</tr>";
		obj_table_text += "<tr>";
		obj_table_text += "<td class='search_box_item' style='padding:5px 5px;' colspan=2>";
		obj_table_text += "<button type='button' onclick='callGroupConetne(2, "+(idx+1)+");'>스타일 불러오기</button> ";
		obj_table_text += "<button type='button' onclick='callGroupConetne(4, "+(idx+1)+");'>배너 불러오기</button>";
		obj_table_text += "</td>";
		obj_table_text += "</tr>";
		obj_table_text += "</table>";
		obj_table_text += "</td>";
		obj_table_text += "</tr>";
		obj_table_text += "<tr>";
		obj_table_text += "<td class='input_box_title'><b>사용여부</b></td>";
		obj_table_text += "<td class='input_box_item' colSpan='3'>";
		obj_table_text += "<input type='radio' name='group_use["+(idx+1)+"]' id='group_use_"+(idx+1)+"_y' size=50 value='Y' checked><label For='group_use_"+(idx+1)+"_y'>사용</label>";
		obj_table_text += "<input type='radio' name='group_use["+(idx+1)+"]' id='group_use_"+(idx+1)+"_n' size=50 value='N'><label For='group_use_"+(idx+1)+"_n'>미사용</label>";
		obj_table_text += "</td>";
		obj_table_text += "</tr>";
		obj_table_text += "<tr>";
		obj_table_text += "<td class='input_box_title'><b>전시기간</b></td>";
		obj_table_text += "<td class='input_box_item' colSpan='3'>";
		obj_table_text += "<table cellpadding=3  cellspacing=1 border=0 bgcolor=#ffffff style='float:left;'>";
		obj_table_text += "<tr>";
		obj_table_text += "<td>";
		obj_table_text += "<img src='../images/korean/calendar_icon.gif'>";
		obj_table_text += "</td>";
		obj_table_text += "<TD nowrap>";
		obj_table_text += "<input type='text' name='group_display_start["+(idx+1)+"]' class='startDate' value='' style='height:20px;width:80px;text-align:center;' id='group_display_start_"+(idx+1)+"'> 일 ";
		obj_table_text += "<select name='group_display_start_h["+(idx+1)+"]' id='group_display_start_h_"+(idx+1)+"'><option value='0' selected=''>0</option><option value='1'>1</option><option value='2'>2</option><option value='3'>3</option><option value='4'>4</option><option value='5'>5</option><option value='6'>6</option><option value='7'>7</option><option value='8'>8</option><option value='9'>9</option><option value='10'>10</option><option value='11'>11</option><option value='12'>12</option><option value='13'>13</option><option value='14'>14</option><option value='15'>15</option><option value='16'>16</option><option value='17'>17</option><option value='18'>18</option><option value='19'>19</option><option value='20'>20</option><option value='21'>21</option><option value='22'>22</option><option value='23'>23</option></select> 시 ";
		obj_table_text += "<select name='group_display_start_i["+(idx+1)+"]' id='group_display_start_i_"+(idx+1)+"'><option value='0' selected=''>0</option><option value='1'>1</option><option value='2'>2</option><option value='3'>3</option><option value='4'>4</option><option value='5'>5</option><option value='6'>6</option><option value='7'>7</option><option value='8'>8</option><option value='9'>9</option><option value='10'>10</option><option value='11'>11</option><option value='12'>12</option><option value='13'>13</option><option value='14'>14</option><option value='15'>15</option><option value='16'>16</option><option value='17'>17</option><option value='18'>18</option><option value='19'>19</option><option value='20'>20</option><option value='21'>21</option><option value='22'>22</option><option value='23'>23</option><option value='24'>24</option><option value='25'>25</option><option value='26'>26</option><option value='27'>27</option><option value='28'>28</option><option value='29'>29</option><option value='30'>30</option><option value='31'>31</option><option value='32'>32</option><option value='33'>33</option><option value='34'>34</option><option value='35'>35</option><option value='36'>36</option><option value='37'>37</option><option value='38'>38</option><option value='39'>39</option><option value='40'>40</option><option value='41'>41</option><option value='42'>42</option><option value='43'>43</option><option value='44'>44</option><option value='45'>45</option><option value='46'>46</option><option value='47'>47</option><option value='48'>48</option><option value='49'>49</option><option value='50'>50</option><option value='51'>51</option><option value='52'>52</option><option value='53'>53</option><option value='54'>54</option><option value='55'>55</option><option value='56'>56</option><option value='57'>57</option><option value='58'>58</option><option value='59'>59</option></select> 분 ";
		obj_table_text += "<select name='group_display_start_s["+(idx+1)+"]' id='group_display_start_s_"+(idx+1)+"'><option value='0' selected=''>0</option><option value='1'>1</option><option value='2'>2</option><option value='3'>3</option><option value='4'>4</option><option value='5'>5</option><option value='6'>6</option><option value='7'>7</option><option value='8'>8</option><option value='9'>9</option><option value='10'>10</option><option value='11'>11</option><option value='12'>12</option><option value='13'>13</option><option value='14'>14</option><option value='15'>15</option><option value='16'>16</option><option value='17'>17</option><option value='18'>18</option><option value='19'>19</option><option value='20'>20</option><option value='21'>21</option><option value='22'>22</option><option value='23'>23</option><option value='24'>24</option><option value='25'>25</option><option value='26'>26</option><option value='27'>27</option><option value='28'>28</option><option value='29'>29</option><option value='30'>30</option><option value='31'>31</option><option value='32'>32</option><option value='33'>33</option><option value='34'>34</option><option value='35'>35</option><option value='36'>36</option><option value='37'>37</option><option value='38'>38</option><option value='39'>39</option><option value='40'>40</option><option value='41'>41</option><option value='42'>42</option><option value='43'>43</option><option value='44'>44</option><option value='45'>45</option><option value='46'>46</option><option value='47'>47</option><option value='48'>48</option><option value='49'>49</option><option value='50'>50</option><option value='51'>51</option><option value='52'>52</option><option value='53'>53</option><option value='54'>54</option><option value='55'>55</option><option value='56'>56</option><option value='57'>57</option><option value='58'>58</option><option value='59'>59</option></select> 분 ";
		obj_table_text += "</TD>";
		obj_table_text += "<TD style='padding:0 5px;' align=center> ~ </TD>";
		obj_table_text += "<td>";
		obj_table_text += "<img src='../images/korean/calendar_icon.gif'>";
		obj_table_text += "</td>";
		obj_table_text += "<TD nowrap>";
		obj_table_text += "<input type='text' name='group_display_end["+(idx+1)+"]' class='endDate' value='' style='height:20px;width:80px;text-align:center;' id='group_display_end_"+(idx+1)+"'> 일 ";
		obj_table_text += "<select name='group_display_end_h["+(idx+1)+"]' id='group_display_end_h_"+(idx+1)+"'><option value='0' selected=''>0</option><option value='1'>1</option><option value='2'>2</option><option value='3'>3</option><option value='4'>4</option><option value='5'>5</option><option value='6'>6</option><option value='7'>7</option><option value='8'>8</option><option value='9'>9</option><option value='10'>10</option><option value='11'>11</option><option value='12'>12</option><option value='13'>13</option><option value='14'>14</option><option value='15'>15</option><option value='16'>16</option><option value='17'>17</option><option value='18'>18</option><option value='19'>19</option><option value='20'>20</option><option value='21'>21</option><option value='22'>22</option><option value='23'>23</option></select> 시 ";
		obj_table_text += "<select name='group_display_end_i["+(idx+1)+"]' id='group_display_end_i_"+(idx+1)+"'><option value='0' selected=''>0</option><option value='1'>1</option><option value='2'>2</option><option value='3'>3</option><option value='4'>4</option><option value='5'>5</option><option value='6'>6</option><option value='7'>7</option><option value='8'>8</option><option value='9'>9</option><option value='10'>10</option><option value='11'>11</option><option value='12'>12</option><option value='13'>13</option><option value='14'>14</option><option value='15'>15</option><option value='16'>16</option><option value='17'>17</option><option value='18'>18</option><option value='19'>19</option><option value='20'>20</option><option value='21'>21</option><option value='22'>22</option><option value='23'>23</option><option value='24'>24</option><option value='25'>25</option><option value='26'>26</option><option value='27'>27</option><option value='28'>28</option><option value='29'>29</option><option value='30'>30</option><option value='31'>31</option><option value='32'>32</option><option value='33'>33</option><option value='34'>34</option><option value='35'>35</option><option value='36'>36</option><option value='37'>37</option><option value='38'>38</option><option value='39'>39</option><option value='40'>40</option><option value='41'>41</option><option value='42'>42</option><option value='43'>43</option><option value='44'>44</option><option value='45'>45</option><option value='46'>46</option><option value='47'>47</option><option value='48'>48</option><option value='49'>49</option><option value='50'>50</option><option value='51'>51</option><option value='52'>52</option><option value='53'>53</option><option value='54'>54</option><option value='55'>55</option><option value='56'>56</option><option value='57'>57</option><option value='58'>58</option><option value='59'>59</option></select> 분 ";
		obj_table_text += "<select name='group_display_end_s["+(idx+1)+"]' id='group_display_end_s_"+(idx+1)+"'><option value='0' selected=''>0</option><option value='1'>1</option><option value='2'>2</option><option value='3'>3</option><option value='4'>4</option><option value='5'>5</option><option value='6'>6</option><option value='7'>7</option><option value='8'>8</option><option value='9'>9</option><option value='10'>10</option><option value='11'>11</option><option value='12'>12</option><option value='13'>13</option><option value='14'>14</option><option value='15'>15</option><option value='16'>16</option><option value='17'>17</option><option value='18'>18</option><option value='19'>19</option><option value='20'>20</option><option value='21'>21</option><option value='22'>22</option><option value='23'>23</option><option value='24'>24</option><option value='25'>25</option><option value='26'>26</option><option value='27'>27</option><option value='28'>28</option><option value='29'>29</option><option value='30'>30</option><option value='31'>31</option><option value='32'>32</option><option value='33'>33</option><option value='34'>34</option><option value='35'>35</option><option value='36'>36</option><option value='37'>37</option><option value='38'>38</option><option value='39'>39</option><option value='40'>40</option><option value='41'>41</option><option value='42'>42</option><option value='43'>43</option><option value='44'>44</option><option value='45'>45</option><option value='46'>46</option><option value='47'>47</option><option value='48'>48</option><option value='49'>49</option><option value='50'>50</option><option value='51'>51</option><option value='52'>52</option><option value='53'>53</option><option value='54'>54</option><option value='55'>55</option><option value='56'>56</option><option value='57'>57</option><option value='58'>58</option><option value='59'>59</option></select> 분 ";
		obj_table_text += "</TD>";
		obj_table_text += "</tr>";
		obj_table_text += "</table>";
		obj_table_text += "</td>";
		obj_table_text += "</tr>";
		obj_table_text += "</table>";
		obj_table_text += "</div>";

		newObj = $(obj_table_text);

		newObj.appendTo($('#group_area_parent'));

		counter = (idx+1);

		$(`#group_display_start_${counter}`).datepicker({
			dateFormat: "yy-mm-dd",
			onSelect: function(selectedDate) {
				const index = this.id.replace("group_display_start_", "");
				$(`#group_display_end_${index}`).datepicker("option", "minDate", selectedDate);
			}
		});

		$(`#group_display_end_${counter}`).datepicker({
			dateFormat: "yy-mm-dd",
			onSelect: function(selectedDate) {
				const index = this.id.replace("group_display_end_", "");
				$(`#group_display_start_${index}`).datepicker("option", "maxDate", selectedDate);
			}
		});

		sortGroup();

	}else{
		alert('10개까지만 가능합니다.');//'상품그룹은 10개까지만 가능합니다.'
	}
}

function sortGroup(init) {
	var groupAreaWrapper = $('#group_area_parent'),
		childrenSelector = '.group_info_area_wrapper';

	if (init) {
		/**	var sortFunction = function($data, customOptions) {
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

		 var filteredData = groupAreaWrapper.find(childrenSelector).clone();
		 sortedData = sortFunction(filteredData, {
			'by' : function(v) {
				return $(v).find('.input-order').val();
			}
		});
		 groupAreaWrapper.quicksand(sortedData, {
			'duration': 10
		});*/

		/**	var groupChildren = groupAreaWrapper.find(childrenSelector);
		 groupChildren.sort(function(a, b) {
			var aVal = $(a).find('.input-order').val();
			var bVal = $(b).find('.input-order').val();
			if(aVal > bVal) { return 1; }
			if(aVal < bVal) { return -1; }
			return 0;
		});
		 groupChildren.detach().appendTo(groupAreaWrapper);*/
	}
	if(groupAreaWrapper.data("sortable")) {
		groupAreaWrapper.sortable('destroy');
		groupAreaWrapper.sortable({
			'cursor': 'move',
			'items': childrenSelector,
			'handle': '.drag-link',
			'stop': function (event, ui) {
				groupAreaWrapper.find(childrenSelector).each(function (i, elt) {
					$(this).find('.input-order').val((i + 1));
				});
			}
		});
	}

	groupAreaWrapper.find(childrenSelector).each(function(i, elt) {
		var tableBox = $(elt).find('.input_table_box').data('isOpened', true);

		$(elt).find('.slide-up-down-link').unbind('click').bind('click', function(e) {
			e.preventDefault();

			inputNum = tableBox.find('.input-number').val();

			isOpened = tableBox.data('isOpened');

			isOpened ? tableBox.find('tr:not(:first)').hide() : tableBox.find('tr:not(:first)').show();
			$('#tableTitleK_'+inputNum).css('display', '');
			$('#tableTitleE_'+inputNum).css('display', '');

			isOpened ? $(this).addClass('closed') : $(this).removeClass('closed');
			tableBox.data('isOpened', !isOpened);

			return false;
		});
	});
}

function slideUpDownAll() {
	var allLinkDOM = $('.slide-up-down-all .slide-up-down-link'),
		isOpened = !allLinkDOM.hasClass('closed'),
		groupAreaWrapper = $('#group_area_parent'),
		childrenSelector = '.group_info_area_wrapper';


	groupAreaWrapper.find('.input_table_box').data('isOpened', isOpened);
	groupAreaWrapper.find('.slide-up-down-link').trigger('click');
	isOpened ? allLinkDOM.addClass('closed') : allLinkDOM.removeClass('closed');
}

function SearchGoods(jq_obj, group_code){
	$("div#goods_manual_area_"+group_code+" div.goods_search_text").closest('li').css("font-weight","normal").css('border','1px solid #efefef');
	$("div#goods_manual_area_"+group_code+" div.goods_search_text:contains('"+jq_obj.parent().find("input#search_goods").val()+"')").closest('li').css("font-weight","bold").css('border','1px solid #000000');
}


function SearchGoodsDelete(jq_obj){
	jq_obj.closest('DIV[id^=goods_manual_area]').find('ul[name=productList] li').remove();	
}

function del_table(obj,gCode) {
	//alert(obj);
	//var tg=obj.target?obj.target:obj.srcElement;
	//var tbl = tg.parentElement.parentElement;//.parentElement.parentElement.parentElement;
	//var tbl = tg.parentNode.parentNode.parentNode.parentNode.parentNode;//.parentElement.parentElement.parentElement;
	//var tbl_code=tbl.getAttribute("group_code");
	//alert(tbl.getAttribute("id"));
	//alert(idx+":::"+obj_table.group_code);
	//alert(gCode);
	
	//var tbl=document.getElementById(obj);
	if((idx+1)==gCode || true){
		$('#'+obj).remove();
		//document.getElementById("group_area_parent").removeChild(tbl);
		idx--;
	}else{
		alert(language_data['event.write.js']['B'][language]);//'상품 그룹을 삭제하실려면 마지막 상품그룹 부터 삭제하셔야 합니다.'
	}
}

var gpCode = null;
/*
function showLayer(obj_id,group_code,evt){
	var tg=evt.target?evt.target:evt.srcElement;
	var tg_top=getOffsetTop(tg);// /admin/js/dd.js 에 getOffsetTop() 있음 kbk

	if(gpCode != group_code)	{
		deleteWhole(false);	
		gpCode = group_code;
		
		select_gorup_code = group_code;
		$('#'+obj_id).css('top', parseInt(tg_top)+30+"px");
		$('#'+obj_id).show();
		selectedGoodsView("selected");
	}	else	{
		if($('#'+obj_id).css('display') == 'none')	{
			select_gorup_code = group_code;
			$('#'+obj_id).css('top', parseInt(tg_top)+30+"px");
			$('#'+obj_id).show();
			selectedGoodsView("selected");
		}else{
			$('#'+obj_id).hide();
			preRow = null;
			deleteWhole(false);		
		}
	}
	
}
 
function UpdateOrder(pid,thisorder,changeorder){
	//alert(pid+'::'+changeorder+'::"'+thisorder)
	document.vieworderform.vieworder.value = changeorder;
	document.vieworderform._vieworder.value = thisorder;
	document.vieworderform.pid.value = pid;
	
	document.vieworderform.submit();
}
*/   