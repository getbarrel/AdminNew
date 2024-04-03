/** 
* Author	: MC 심새 (ganer9r@naver.com) 
* Make Date : 2006-09-22
* comment	: ajax의 xml을 javascript 객체형식으로 자동 변환
**/

/* Ajax 사용을 위한 기본 콤포넌트 Start */
function personalSorts(){
	var entry					= new Array();
	var tplString				= "";

	var personalSortsOptions	= "";
	var sortTableOptions		= "";


	personalSorts.prototype.create				= function(entry, personalSortsOptions, sortTableOptions){
		this._setEntry(entry);
		this._setEntryEnabled();

		this.ajaxReturnFunction	= "displayContinent";

		this.personalSortsOptions				= this._setPersonalSortsOptions(personalSortsOptions);

		this.sortTableOptions					= sortTableOptions;
		this.sortTableOptions['containment']	= this.entry;
	}

	personalSorts.prototype._setPersonalSortsOptions			= function(personalSortsOptions){
		personalSortsOptions['nodeId']				= personalSortsOptions['nodeId']		|| "rss";
		personalSortsOptions['nodeCalss']			= personalSortsOptions['nodeCalss']		|| "item";
		personalSortsOptions['tmpBody']				= personalSortsOptions['tmpBody']		|| "";
		personalSortsOptions['tplName']				= personalSortsOptions['tplName']		|| "template";
		personalSortsOptions['tplString']			= personalSortsOptions['tplString']		|| "";
		personalSortsOptions['callBack']			= personalSortsOptions['callBack'];
		personalSortsOptions['cookieExpire']		= personalSortsOptions['cookieExpire']	|| "30";

		personalSortsOptions['nodeId']				= "personalSorts_"+personalSortsOptions['nodeId'];

		if(!personalSortsOptions['tplString']){
			personalSortsOptions['tplString']	= document.getElementById(personalSortsOptions['tplName']).innerHTML;
		}

		if (personalSortsOptions['callBack']){
			personalSortsOptions['callBack'] = eval( personalSortsOptions['callBack'] );
		} 

		return personalSortsOptions;
	}

	personalSorts.prototype._setEntry			= function(entry){
		entry		= entry.split(",");
		this.entry	= entry;
	}
	
	personalSorts.prototype._setEntryEnabled	= function(){
		for(var i=0; i<this.entry.length;i++){
			Sortable.create(this.entry[i], this.sortTableOptions );
		}
	}

	personalSorts.prototype.makeTplString		= function(idx, display){
		tpl					= this.personalSortsOptions['tplString'].replace(/#id#/g, idx);

		if(display == 0){
			tpl				= tpl.replace(/#display#/g, " style='display:none;' " );
			tpl				= tpl.replace(/#display=\"\"/g, " style='display:none;' " );
		}else{
			tpl				= tpl.replace(/#display#/g, "" );
		}

		return tpl;
	}

	personalSorts.prototype._makeSortItem			= function(locate, idx, display){
		var oList			= $(this.entry[locate-1]);
		var tpl				= this.makeTplString(idx, display);
		var oLiNode			= document.createElement("LI");
		//var oLiNode			= document.createElement("DIV");
		var oDivNode		= document.createElement("DIV");


		oDivNode.innerHTML	= tpl;
		oDivNode.className	= this.personalSortsOptions['nodeCalss'];
		oLiNode.id			= this.personalSortsOptions['nodeId']+"_"+idx;
		oLiNode.style.listStyleType = "none";
		oLiNode.style.padding = "0";
		
		oLiNode.appendChild(oDivNode);
		oList.appendChild(oLiNode);

		if(this.personalSortsOptions['callBack']){
			// 콜백 함수를 호출합니다.
			this.personalSortsOptions['callBack'](idx);
		}
	}

	personalSorts.prototype.makeSortItem			= function(locate, idx, display){
		this._makeSortItem(locate, idx, display);
		this._setEntryEnabled();

		this.makeCookies();
	}

	personalSorts.prototype.parseAsync			= function(items){
		if(items){
			items	= items.split(",");

			for(var j=0; j<items.length; j++){
				param	= items[j].split("-");

				this._makeSortItem(param[0], param[1], param[2]);
			}
		}

		this._setEntryEnabled();
	}

	personalSorts.prototype.removeItem	= function (idx){
		var obj				= document.getElementById(this.personalSortsOptions['nodeId']+"_"+idx);
		obj.parentNode.removeChild(obj);
		this.makeCookies();
	}

	personalSorts.prototype.serialize	= function (elementName, loc){
		var result		= new Array();
		var entryNodes	= $(elementName).childNodes;
		//alert(elementName+":::"+$(elementName).childNodes.length);
		for(var i=0; i<entryNodes.length; i++){
			var str_id		= entryNodes[i].id.replace(/^personalSorts_/, "");
			str_id			= str_id.split("_");
			
			var displayChk	= $(this.personalSortsOptions['tmpBody']+"_"+str_id[1]).style.display;
			
			displayChk		= (displayChk == 'none') ? "0" : "1";

			if(loc){
				result.push( loc +"-"+ str_id[1] +"-"+ displayChk );
			}else{
				result.push( str_id[1] +"-"+ displayChk );
			}
		}

		result.join(",");
		return result;
	}

	personalSorts.prototype.serializeAll	= function(){
		var result	= new Array();

		for(var i=0; i<this.entry.length;i++){
			result.push( this.serialize( this.entry[i], (i+1) ) );
		}

		return result;
	}

	personalSorts.prototype.makeCookies		= function(){
		serializeChar				= this.serializeAll();
		for(var i=0; i<this.entry.length; i++){
			$(this.entry[i]+"_debug").innerHTML	= serializeChar[i];

			var todayDate	= new Date();
			todayDate.setDate( todayDate.getDate() + this.personalSortsOptions['cookieExpire'] ); 
			document.cookie = this.entry[i] + "=" + escape( serializeChar[i] ) + "; path=/; expires=" + todayDate.toGMTString() + ";" 
		}
	}

}

/* Ajax에서 리턴받은 XML NODE를 JAVASCRIPT OBJECT 형식으로 변환 End */


/* 사용법 Start*/
//	var aObj	= new AjaxObject;         // AjaxList 선언
//	aObj.getHttpRequest("test.xml", "displayBoardList", [인자값]);		//참조Url, 리턴 함수명
//	리턴 함수에는 obj를 받을 인자 필수!!!
//
//	리턴 함수에, 변환된 데이터 이외의 인자를 받고 싶으면 계속 이어서 쓰세요
//	예] aObj.getHttpRequest("test.xml", "displayBoardList", "test", 1, "all", ... );
/* 사용법 End */