var xmlHttp;
        var completeDiv;
        var inputField;
        var searchType;
        var nameTable;
        var nameTableBody;
        var focusOutBool = false;
		var s_i=-1;
		var selected_standard_cid = "";

        function createXMLHttpRequest() {
            if (window.ActiveXObject) {
                xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            else if (window.XMLHttpRequest) {
                xmlHttp = new XMLHttpRequest();
            }
        }

        function initVars() {
			
            inputField = document.getElementById("search_texts");
            searchType = document.getElementById("search_type");
            nameTable = document.getElementById("search_table");
            completeDiv = document.getElementById("popup");
            nameTableBody = document.getElementById("search_table_body");
        //    pidField = document.getElementById("pid_");
        }

		/*
		* GET 방식
		*/
        function findNames() {
			
            initVars();
			
            if (true) {//inputField.value.length > 1 && inputField.clickbool == 'true'
				var obj = $('.list_url');//document.all.slist;
                if(obj.length){//event.keyCode == 40 || event.keyCode == 38 || event.keyCode == 39 || event.keyCode == 25
					
					

					if(event.keyCode == 40){
						if(obj.length-1 > s_i && s_i >= -1){
							s_i++;
						}
					}else if(event.keyCode == 38){
						if(obj.length-1 >= s_i && s_i >= 1){
							s_i--;
						}
					}
					
					//alert(s_i+":::"+obj.length);
					if(obj.length){
						
						for(i=0;i<obj.length;i++){
							if(i == s_i){
								obj[i].className = 'SearchMouseOut';
								inputField.value = obj[i].innerText.replace('[검색어]','');
								//pidField.value = obj[i].pid;
							}else{
								obj[i].className = 'SearchMouseOver';
							}
						}
					}else{
						
						obj.className = 'SearchMouseOut';
						inputField.value = obj.innerText;
						//pidField.value = obj.pid;
					}
					
					
				}else{
					
					createXMLHttpRequest();     
					/*
					if($('#bs_site').val() == ""){
						alert('구매대행 사이트를 선택해주세요');
						return false;
					}
					*/
					//alert(searchType);			
					var url = "./buyingService.xml.php?search_type="+searchType.value+"&search_text=" + inputField.value+"&bs_site="+$('#bs_site').val()+"&cid="+$('#cid2').val();
					
					//var url = "/shop/ms_search.xml.php?search_text=" + encodeURI(inputField.value);
					//var url = "AutoCompleteServlet?names=" + encodeURI(inputField.value);
					//encodeURI 대신에 encodeURIComponent 를 사용해도 결과는 동일하다.
					xmlHttp.open("GET", url, true);
					xmlHttp.onreadystatechange = callback;
					xmlHttp.send(null);
				   // inputField.clickbool = 'false';

				}

                //alert(inputField.clickbool);
            } else {
                clearNames();
                inputField.clickbool = 'true';
                //alert(inputField.clickbool);
            }

        }

		/**
		* POST 방식
		*/
		/*function findNames() {
            initVars();
            if (inputField.value.length > 0) {
                createXMLHttpRequest();
				var url = "AutoCompleteServlet";
                xmlHttp.open("POST", url, true);
                xmlHttp.onreadystatechange = callback;
				xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xmlHttp.send("names=" + encodeURI(inputField.value));
				//encodeURI 대신에 encodeURIComponent 를 사용해도 결과는 동일하다.
            } else {
                clearNames();
            }
        }*/

        function callback() {
	 
            if (xmlHttp.readyState == 4) {
                if (xmlHttp.status == 200) {
                    //setNames(xmlHttp.responseXML.getElementsByTagName("name"));
                   // alert(xmlHttp.responseText);
					setNames(xmlHttp.responseXML);

                } else if (xmlHttp.status == 204){
                    clearNames();
                }
            }
        }

        function setNames(obj) {
			//the_names = $(xml).find('name');
			
			the_names = obj.getElementsByTagName("name");
			
			the_search_texts = obj.getElementsByTagName("search_text");
			the_pids = obj.getElementsByTagName("pid");
			the_cids = obj.getElementsByTagName("cid");
			the_depths = obj.getElementsByTagName("depth");
			the_bs_sites = obj.getElementsByTagName("bs_site");
			the_currency_ix = obj.getElementsByTagName("currency_ix");
			the_last_working_date = obj.getElementsByTagName("last_working_date");
			
            clearNames();
            var size = the_names.length;
			//alert(size);
            setOffsets();
            var row, cell, txtNode;
			//alert(size);
            if(size != 0){

            	completeDiv.style.display = "block";
	            for (var i = 0; i < size; i++) {

	                var nextNode = the_names[i].firstChild.data;
					var bs_list_url = the_search_texts[i].firstChild.data;
					var pid = the_pids[i].firstChild.data;
					var cid = the_cids[i].firstChild.data;
					var depth = the_depths[i].firstChild.data;
					var bs_site = the_bs_sites[i].firstChild.data;
					var currency_ix = the_currency_ix[i].firstChild.data;
					//var last_working_date = the_last_working_date[i].firstChild.data;

	                row = document.createElement("tr");
	                cell = document.createElement("td");

	                cell.onmouseout = function() {this.className='SearchMouseOver';};
	                cell.onmouseover = function() {this.className='SearchMouseOut';};
	                cell.setAttribute("bgcolor", "#ffffff");
	                cell.setAttribute("border", "0");
	                cell.style.paddingLeft= 5;
					cell.style.height= '20px';
					//cell.style.color= '#000000';
					cell.category_path = "["+bs_site+"] "+nextNode;
					cell.pid = pid;
					cell.bsui_ix  = pid;
					//cell.last_working_date = last_working_date
					cell.bs_list_url = bs_list_url;
					cell.cid = cid;
					cell.depth = depth;
					cell.bs_site = bs_site;
					cell.currency_ix = currency_ix;
					cell.id = "slist";
					cell.className = "list_url";
	                cell.onblur = function() { populateName(this); focusOutBool=true;clearNames();} ;
					cell.onclick = function() { populateName(this); focusOutBool=true;clearNames();} ;
					cell.onkeyup= function() {if(event.keycode == 13) populateName(this);};
					//alert(nextNode);
					//$(cell).html(nextNode);
	                txtNode = document.createTextNode("["+bs_site+"] "+nextNode);// + " - " + cell.last_working_date
	                cell.appendChild(txtNode);
					//cell.innerHTML(txtNode);
	                row.appendChild(cell);
	                nameTableBody.appendChild(row);
					//alert(i);
	            }
	            //alert(1);
	            size = 0;
	     }else{
	     	 completeDiv.style.border = "none";
	     	 completeDiv.style.display = "none";
	    }
     }

	function setOffsets() {
		var end = inputField.offsetWidth;
		var left = calculateOffsetLeft(inputField);
		var top = calculateOffsetTop(inputField) + inputField.offsetHeight;

		completeDiv.style.border = "silver 1px solid";
		completeDiv.style.borderTop = "silver 0px solid";
		//completeDiv.style.left = left + "px";
		//completeDiv.style.top = top + "px";
		//nameTable.style.width = end-10 + "px";
	}

	function calculateOffsetLeft(field) {
	  return calculateOffset(field, "offsetLeft");
	}

	function calculateOffsetTop(field) {
	  return calculateOffset(field, "offsetTop");
	}

	function calculateOffset(field, attr) {
	  var offset = 0;
	  while(field) {
		offset += field[attr];
		field = field.offsetParent;
	  }
	  return offset;
	}

	function populateName(cell) {
		//alert(cell.currency_ix);
		
		$("#bs_site > option[value="+cell.bs_site+"]").attr("selected", "true");
		$("#currency_ix > option[value="+cell.currency_ix+"]").attr("selected", "true");
		$("#cid > option[value="+cell.cid+"]").attr("selected", "true");
//alert(cell.cid.substring(0,3));
		$("#this_pagenum").val("");
		$("#this_url").val("");
		$("#start").val("");
		$("#end").val("");
		selected_standard_cid = cell.cid;
		
		//setStandardCategory("scid0_1",cell.cid, 0);
		$('select[name=scid0_1]').find('option[value='+cell.cid.substring(0,3)+'000000000000]').attr('selected', 'true');	
		$('select[name=scid0_1]').trigger('change');
		$('input[name=cid2]').val(selected_standard_cid);
		
		//inputField.value = cell.firstChild.nodeValue;
		//alert(cell.name);
		$("div#selected_category_path").html(cell.category_path);
		$("input#bsui_ix").val(cell.bsui_ix);
		
		inputField.value = cell.bs_list_url;
		//pidField.value = cell.pid;
		clearNames();
	}

	function clearNames() {
	//if(focusOutBool){
		var ind = nameTableBody.childNodes.length;

		for (var i = ind - 1; i >= 0 ; i--) {

			 nameTableBody.removeChild(nameTableBody.childNodes[i]);

		}
		completeDiv.style.border = "none";
		completeDiv.style.display = "none";
	//}


	}

	function setStandardCategory(obj_id, cid, depth){
		
		//alert(depth);
		if(depth == 0){
			$("select[name="+obj_id+"]").find("option[value="+cid.substring(0,3)+"000000000000]").attr("selected", "true");	
			//alert(1);
			$("select[name="+obj_id+"]").trigger("change");
			//$("select[name="+obj_id+"]").change();
			setStandardCategory("scid1_1",cid, 1);	
			
			
			//alert(2);
		}else if(depth == 1){
			//alert(obj_id+":::"+cid.substring(0,6));
			$("select[name="+obj_id+"]").find("option[value="+cid.substring(0,6)+"000000000]").attr("selected", "true");
			alert($("select[name="+obj_id+"]").val());
			$("select[name="+obj_id+"]").trigger("change");
			setStandardCategory("scid2_1",cid, 2);			
			
		}else if(depth == 2){
			$("select[name="+obj_id+"]").find("option[value="+cid.substring(0,9)+"000000]").attr("selected", "true");
			$("select[name="+obj_id+"]").trigger("change");
			setStandardCategory("scid3_1",cid, 3);				
		}else if(depth == 3){
			$("select[name="+obj_id+"]").find("option[value="+cid.substring(0,12)+"000000]").attr("selected", "true");
			$("select[name="+obj_id+"]").trigger("change");
		}
		
		/*
		$(obj).each(function(){
			
		});
		*/
	}

	function sleep(milliseconds) {
	  var start = new Date().getTime();
	  for (var i = 0; i < 1e7; i++) {
		if ((new Date().getTime() - start) > milliseconds){
		  break;
		}
	  }
	}