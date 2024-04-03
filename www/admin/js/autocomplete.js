var xmlHttp;
        var completeDiv;
        var inputField;
        var searchType;
        var nameTable;
        var nameTableBody;
        var focusOutBool = false;
		var s_i=-1;

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

            if (inputField.value.length > 1 && inputField.clickbool == 'true') {

                if(event.keyCode == 40 || event.keyCode == 38 || event.keyCode == 39 || event.keyCode == 25){
					var obj = document.all.slist;

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
								inputField.value = obj[i].innerText;
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
					var url = "./search.xml.php?search_type="+searchType.value+"&search_text=" + inputField.value;
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
                    //alert(xmlHttp.responseText);
					setNames(xmlHttp.responseXML);

                } else if (xmlHttp.status == 204){
                    clearNames();
                }
            }
        }

        function setNames(obj) {
			
			the_names = obj.getElementsByTagName("name");
			the_pids = obj.getElementsByTagName("pid");
			//alert(the_pids.length);
            clearNames();
            var size = the_names.length;
            setOffsets();
            var row, cell, txtNode;
			//alert(size);
            if(size != 0){

            	    completeDiv.style.display = "block";
	            for (var i = 0; i < size; i++) {

	                var nextNode = the_names[i].firstChild.data;
									var pid = the_pids[i].firstChild.data;

	                row = document.createElement("tr");
	                row.setAttribute("height", "22");
	                cell = document.createElement("td");

	                //cell.onmouseout = function() {this.className='SearchMouseOver';};
	                //cell.onmouseover = function() {this.className='SearchMouseOut';};
	                cell.setAttribute("bgcolor", "#ffffff");
	                cell.setAttribute("border", "0");
	                cell.style.paddingLeft= 10;
					cell.pid = pid;
					cell.id = "slist";
	                cell.onclick = function() { populateName(this); focusOutBool=true;clearNames();} ;
					cell.onkeyup= function() {if(event.keycode == 13) alert(1);};

	                txtNode = document.createTextNode(nextNode);
	                cell.appendChild(txtNode);
	                row.appendChild(cell);
	                nameTableBody.appendChild(row);
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
            completeDiv.style.left = left + "px";
            completeDiv.style.top = top + "px";
            nameTable.style.width = end-10+ "px";
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
            inputField.value = cell.firstChild.nodeValue;
			pidField.value = cell.pid;
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