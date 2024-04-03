function XMLHttp()
{
	function Result(status, responseText)
	{
		this.status = status;
		this.responseText = responseText;
	}
	
	this.id = XMLHttp.Objects.length;
	XMLHttp.Objects[this.id] = this;
	
	this.xmlHttp = null;
	this.responseText = "";
	this.status = 0;
	
	this.results = new Array();
	
	this.request = function(method, url, async, data, onSuccessHandler, onFailHandler, user, password)
	{
		if(this.xmlHttp==null)
			throw new Error("xmlHttp is null.");
		
		if(user==null)
			this.xmlHttp.open(method, url, async);
		else
			this.xmlHttp.open(method, url, async, user, password);
		
		if(async)
		{
			this.onSuccess = onSuccessHandler;
			this.onFail = onFailHandler;
		}
		
		switch(method.toLowerCase())
		{
			case "get":
			case "head":
				this.xmlHttp.send(null);
				break;
			case "post":
				this.xmlHttp.send(data);
				break;
		}
		
		if(async)
			return null;
		else
		{
			this.status = this.xmlHttp.status;
			this.responseText = this.xmlHttp.responseText;
			return new Result(this.status, this.responseText);
		}
	}
	
	try
	{
		if(window.XMLHttpRequest)
		{
			this.xmlHttp = new XMLHttpRequest();
			// 일부의 모질라 버전을은 readyState property, 
			// onreadystate event를 지원하지 않으므로. - from xmlextrs
			if(this.xmlHttp.readyState == null)
			{
				this.xmlHttp.readyState = 1;
				this.xmlHttp.addEventListener("load", function() {
						this.xmlHttp.readyState = 4;
						this.xmlHttp.onreadystatechange();
					}, false);
			}
		}
		
		if(this.xmlHttp==null)
		{
			try
			{
				this.xmlXhttp = new ActiveXObject("MSXML2.XMLHTTP");
			}
			catch(e)
			{
			}
		}
		
		if(this.xmlHttp==null)
		{
			try
			{
				this.xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch(e)
			{
			}
		}

		
		if(this.xmlHttp==null)
		{
			if(window.createRequest)
			{
				try
				{
					this.xmlHttp = window.createRequest();
				}
				catch(e)
				{
				}
			}
		}
		
		
		if(this.xmlHttp==null)
			throw new Error("Can't create XMLHTTP object.");
		
	}
	catch(e)
	{
		throw new Error("Can't create XMLHTTP object.");
	}
	
	this.onSuccess = function(result) {};
	this.onFail = function(result) {}
	
	eval("this.xmlHttp.onreadystatechange = function ()\n"
		+"{\n"
		+"	var obj = XMLHttp.Objects["+this.id+"];\n"
		+"	if(obj.xmlHttp.readyState == 4)\n"
		+"	{\n"
		+"		if(obj.xmlHttp.status == 200)\n"
		+"		{\n"
		+"			obj.status = obj.xmlHttp.status;\n"
		+"			obj.responseText = obj.xmlHttp.responseText;\n"
		+"			obj.onSuccess(new Result(obj.status, obj.responseText));\n"
		+"		}\n"
		+"		else\n"
		+"		{\n"
		+"			obj.status = obj.xmlHttp.status;\n"
		+"			obj.responseText = obj.xmlHttp.responseText;\n"
		+"			obj.onFail(new Result(obj.status, obj.responseText));\n"
		+"		}\n"
		+"	}\n"
		+"}\n");
	
}
XMLHttp.Objects = new Array();



/*




// 호출 방법
// method, url, async, data, onSuccessHandler, onFailHandler, user, password
// method : "GET", "POST", "HEAD"
// url : 호출 주소
// async : 비동기 호출 여부. true 일 때 비동기 호출, false 일 때 동기 호출
// onSuccessHandler : 비동기 호출 시 성공 이벤트 수신 핸들러
// onFailHandler : 비동기 호출 시 실패 이벤트 수신 핸들러
// user : 인증시 사용자 아이디.
// password : 인증시 사용자 암호

var ret = null;




// 비동기 호출
function onSuccess(result)
{
	alert("status="+result.status+"\n\nresponseText="+result.responseText);
}

function onFail(result)
{
	alert("status="+result.status+"\n\nresponseText="+result.responseText);
}

var xmlHttp = new XMLHttp();
XMLHttp.request("get", "ajax_service.asp", true, null, onSuccess, onFail, null, null);

// 약식 호출 : XMLHttp.request("get", "ajx_service.asp", true, null, onSuccess, onFail); (인증이 필요하지 않으므로 user, password 인자에서 제외)





// 동기 호출 : return 값으로 데이터가 돌아온다.
ret = XMLHttp.request("get", "ajax_service.asp", false, null, onSuccess, onFail, null, null);
if(ret.status == 200)
	; // 성공
else
	; // 실패

// 약식 호출 : ret = XMLHttp.request("get", "ajax_service.asp", false); (인증 필요없음. 이벤트 수신 핸들러 필요없음)





*/
