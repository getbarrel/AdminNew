function HTTPParameter(name, value)
{
	this.name = (name==null) ? "" : name;
	this.value = (value==null) ? "" : value;
	
	this.toString = function()
	{
		return "name="+name+"\n"+"value="+value;
	}
}

function HTTPParameters()
{
	throw "�� ��ü�� new �۾��� �������� �ʽ��ϴ�.";
}
HTTPParameters.create = function(s)
{
	var obj = new Array();
	
	obj.find = function(name)
	{
		for(var i=0; i<this.length; ++i)
		{
			if(this[i].name==name)
				return i;
		}		
		return -1;
	}

	if(s==null)
		return obj;
			
	if(s.length==0)
		return obj;

	if(s.charAt(0)=="=")
		return obj;
	
	var i=0;
	
	while(true)
	{
		var name="", value="";
			
			
		for(i; (i<s.length) && (s.charAt(i)!="="); ++i)
			name += s.charAt(i);
		if(i>=s.length)
		{
			obj[obj.length] = new HTTPParameter(name, "");
			return obj;
		}
			
		++i;
		for(; (i<s.length) && (s.charAt(i)!="&"); ++i)
			value += s.charAt(i);
		if(i>=s.length)
		{
			obj[obj.length] = new HTTPParameter(name, value);
			return obj;
		}
			
		obj[obj.length] = new HTTPParameter(name, value);
		++i;
	}
	
	return obj;
}

function HTTPURL(URL)
{
	this.host = "";
	this.hostname = "";
	this.port = 80; 
	this.pathname = "";
	this.parameters = HTTPParameters.create();
	
	this.toString = function()
	{
		var s = "host: "+this.host+"\n"
			+"port:"+this.port+"\n"
			+"hostname:"+this.hostname+"\n"
			+"pathname:"+this.pathname+"\n";

		if(this.parameters.length>0)
			s += "parameters="+this.parameters[0].name+"="+this.parameters[0].value;

		for(var i=1; i<this.parameters.length; ++i)
		{
			s += "&"+this.parameters[i].name+":"+this.parameters[i].value;
		}

		return s;
	}
	
	if(URL.substring(0, "http://".length)=="http://")
		URL = URL.substring("http://".length, URL.length);

	for(var i=0; (i<URL.length)&&(URL.charAt(i)!="/"); ++i)
		this.hostname += URL.charAt(i);
		
	var found = this.hostname.indexOf(":");
	if(found==-1)
		this.host = this.hostname;
	else
	{
		this.host = this.hostname.substring(0, found);
		this.port = parseInt(this.hostname.substring(found+1, this.hostname.length), 10);
	}
	
	if(i>=URL.length)
		return;
		
	found = URL.indexOf("?");
	if(found==-1)
	{
		this.pathname = URL.substring(i, URL.length);
		return;
	}

	this.pathname = URL.substring(i, found);
	this.parameters = HTTPParameters.create(URL.substring(found+1, URL.length));
}

var MAX_URL_LEN	= 2043;
var DEBUG_FLAG = false;


function CampaignType()
{
}
CampaignType.Email = 6;
	

function ForbizSalesAnalysisTag(data, campaignType, campaignID)
{
	this.isServiceHost = function(host)
	{
		for(var i=0; i<this.hosts.length; ++i)
		{
			if( host.toLowerCase().indexOf(this.hosts[i].toLowerCase()) != -1)
				return true;
		}
		
		return false;
	}

	// �ּ� ���� : http://, ? ������ �Ķ����ʹ� �����Ѵ�.
	this.analyzeURL = function()
	{
		var index;
		
		// host ���� "www." ������ �����Ѵ�.
		if(this.location.hostname.indexOf("www.")==0)
		{
			this.URL = this.location.hostname.substring("www.".length, this.location.hostname.length).toLowerCase();
		}
		else
		{
			this.URL = this.location.hostname.toLowerCase();
		}
		
		
		// ����Ʈ�� ���ҹ��� ������ �ϸ� pathname�� ��ȯ���� �ʴ´�.
		if(this.caseFlag)
			this.URL += this.location.pathname;
		else
			this.URL += this.location.pathname.toLowerCase();
	}

	// Ÿ��Ʋ ����
	this.analyzeTitle = function()
	{
		// Ÿ��Ʋ�� ���� ���� �߰�
		if(document.title==null)
			return "";
		if(document.title=="")
			return "";
			
		this.title = document.title;
	}

	// ���Ի���Ʈ ���� : �����θ� �����´�.
	this.analyzeReferer = function()
	{
		var i;
		var found = this.location.parameters.find("forbizref");
		if(found>=0)
		{
			this.referer = this.location.parameters[found].value;
			return;
		}
		
		if(document.referrer=="")
			return;
			
		this.referer = document.referrer.substring("http://".length, document.referrer.length);
	}
	
	this.analyzeData = function(data)
	{
		//������ ���̵�|�̸���|�ڵ���|����|�¾ �⵵|����|data0~data9|������Ÿ��(�Ϲ�,���Ŵܰ�,ȸ�����Դܰ�)|����������
		//������Ÿ�� : �Ϲ�, ���Ŵܰ�, ȸ�����Դܰ�
		//���������� : ��ǰī�װ���A>��ǰī�װ���B>...>��ǰ��|����(�ܰ�)|����
	
		var tokens = data.split("|");
		if(tokens.length<17)
			return;
		
		if(tokens[0]!="-")
			this.parameters[this.parameters.length] = new HTTPParameter("loginID", tokens[0]);
		if(tokens[1]!="-")
			this.parameters[this.parameters.length] = new HTTPParameter("email", tokens[1]);
		if(tokens[2]!="-")
			this.parameters[this.parameters.length] = new HTTPParameter("mobile", tokens[2]);
		if(tokens[3]!="-")
			this.parameters[this.parameters.length] = new HTTPParameter("gender", tokens[3]);
		if(tokens[4]!="-")
			this.parameters[this.parameters.length] = new HTTPParameter("birthYear", tokens[4]);
		if(tokens[5]!="-")
			this.parameters[this.parameters.length] = new HTTPParameter("zip", tokens[5]);
			
		for(var i=0; i<10; ++i)
			if(tokens[i+6]!="-")
				this.parameters[this.parameters.length] = new HTTPParameter("data"+i, tokens[i+6]);
		if(tokens[16]!="-")
			this.parameters[this.parameters.length] = new HTTPParameter("pageType", tokens[16]);
		
				
		if(tokens.length>17)
		{
			for(i=17; i<tokens.length; i+=3)
			{
				if(tokens.length<i+3)
					return;
				this.items[this.items.length] = tokens[i]+"|"+tokens[i+1]+"|"+tokens[i+2];
			}
		}
	}
	
	this.analyze = function()
	{
		this.analyzeURL();
		this.analyzeTitle();
		this.analyzeReferer();
		this.analyzeData(data);
	}
	
	this.request = function(URL)
	{
		
		if(DEBUG_FLAG)
		{
			document.write("<textarea rows=50 cols=50>"+URL+"</textarea>");
		}
		else
		{
			var index = this.images.length;
			this.images[index] = new Image();
			this.images[index].src = URL;

			index = URL.indexOf(this.GS);
			if(index==-1)
				return;
			URL = URL.substring(0, index)+"intra.forbiz.co.kr:1277"+URL.substring(index+this.GS.length, URL.length);
			index = this.images.length;
			this.images[index] = new Image();
			this.images[index].src = URL;
		}
	}
	
	this.run = function()
	{
		var value = "";
		var paramIndex = 0;
		var itemIndex = 0;
		var requestURLHead = "";
		var requestURL = "";
		var continueFlag = false;
		
		if(this.campaignType!=null)
		{
			if(this.campaignID==null)
				return;	// ����!
				
			if(this.campaignType!=CampaignType.Email)
				return;	// ����!
			
			requestURLHead = "http://"+this.GS+"/collect.php?siteID="+this.siteID+"&data_root="+this.data_root+"&URL=EMAIL*"+this.campaignID;
		}
		else
		{
			requestURLHead = "http://"+this.GS+"/collect.php?siteID="+this.siteID+"&data_root="+this.data_root+"&URL="+this.URL;
		}
		
		for(var i=0; true; ++i)
		{
			continueFlag = false;
			requestURL = requestURLHead;
			if(i==0)
			{
				if(this.leftURL.length>0)
				{
					value = escape(this.leftURL);
					if(requestURL.length+"&leftURL=".length+value.length>MAX_URL_LEN)
					{
						this.request(requestURL);
						continue;
					}
					requestURL += "&leftURL="+value;
				}
				
				if(this.referer.length>0)
				{
					value = escape(this.referer);
					if(requestURL.length+"&referer=".length+value.length>MAX_URL_LEN)
					{
						this.request(requestURL);
						continue;
					}
					requestURL += "&referer="+value;
				}
				
				if(this.title.length>0)
				{
					value = escape(this.title);
					if(requestURL.length+"&title=".length+value.length>MAX_URL_LEN)
					{
						this.request(requestURL);
						continue;
					}
					requestURL += "&title="+value;
				}
				
				for(paramIndex=0; paramIndex<this.parameters.length; ++paramIndex)
				{
					value = escape(this.parameters[paramIndex].value);
					if(requestURL.length+this.parameters[paramIndex].name.length+"=".length+value.length>MAX_URL_LEN)
					{
						this.request(requestURL);
						continueFlag = true;
						break;
					}
					requestURL += "&"+this.parameters[paramIndex].name+"="+value;
				}
				if(continueFlag)
				{
					continueFlag = false;
					continue;
				}
				

				for(itemIndex=0; itemIndex<this.items.length; ++itemIndex)
				{
					if(itemIndex==0)
					{
						value = escape(this.items[itemIndex]);
						if(requestURL.length+"&items=".length+value.length>MAX_URL_LEN)
						{
							this.request(requestURL);
							continueFlag = true;
							break;
						}
						requestURL += "&items="+value;
					}
					else
					{
						value = escape("|"+this.items[itemIndex]);
						if(requestURL.length+value.length>MAX_URL_LEN)
						{
							this.request(requestURL);
							continueFlag = true;
							break;
						}
						requestURL += value;
					}
				}
				if(continueFlag)
				{
					continueFlag = false;
					continue;
				}
				
				this.request(requestURL);
				break;
			}
			else
			{
				for(; paramIndex<this.parameters.length; ++paramIndex)
				{
					value = escape(this.parameters[paramIndex].value);
					if(requestURL.length+this.parameters[paramIndex].name.length+"=".length+value.length>MAX_URL_LEN)
					{
						this.request(requestURL);
						continueFlag = true;
						break;
					}
					requestURL += "&"+this.parameters[paramIndex].name+"="+value;
				}
				if(continueFlag)
				{
					continueFlag = false;
					continue;
				}
				

				for(; itemIndex<this.items.length; ++itemIndex)
				{
					if(itemIndex==0)
					{
						value = escape(this.items[itemIndex]);
						if(requestURL.length+"&items=".length+value.length>MAX_URL_LEN)
						{
							this.request(requestURL);
							continueFlag = true;
							break;
						}
						requestURL += "&items="+value;
					}
					else
					{
						value = escape("|"+this.items[itemIndex]);
						if(requestURL.length+value.length>MAX_URL_LEN)
						{
							this.request(requestURL);
							continueFlag = true;
							break;
						}
						requestURL += value;
					}
				}
				if(continueFlag)
				{
					continueFlag = false;
					continue;
				}
				
				this.request(requestURL);
				break;
			}
		}
	}
	
	this.checkHost = function()
	{
		var URL = new HTTPURL(location.href);
		// ����Ʈ ȣ��Ʈ �� �˻�	 : Ÿ����Ʈ �� ���� �����Ѵ�. (��������Ʈ ����)
		return this.isServiceHost(URL.host);
	}
	
	if(data==null)
		return;
		
	this.location = new HTTPURL(location.href);
	
	this.campaignType = campaignType;
	this.campaignID = campaignID;
	//alert(document.domain);
	this.GS = document.domain;
	//this.GS = "redsun.forbiz.co.kr
	this.siteID = "0002";
	this.data_root = "";
	this.caseFlag = false;
	this.hosts = new Array(document.domain);
	this.images = new Array();
	this.parameters = HTTPParameters.create("");
	this.items = new Array();
	
	this.URL = "";
	this.leftURL = "";
	this.title = "";
	this.referer = "";
	
	
	
	if(this.checkHost()==false)
		return;
	
	this.analyze();
	
	//this.run();
}

function SetSalesAnalysisTag(data, siteID, data_root)
{
	// ������ü�� ��ü�� �����Ǿ��� ���� ������������ ������ ������ �����ϱ� ���� 
	try
	{
		document.SalesAnalysisTag = new ForbizSalesAnalysisTag(data);
		document.SalesAnalysisTag.data_root = data_root;
		document.SalesAnalysisTag.run();
	}
	catch(e)
	{
		;
	}
}
//DEBUG_FLAG = true;
//�±� ��ġ�� DEBUG_FLAG = false;�� true�� �ٲ��ּ���
