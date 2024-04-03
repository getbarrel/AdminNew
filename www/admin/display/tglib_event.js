<!--

	function showObj(id)
	{
		obj = eval(id+".style");
		obj.display = "block";

		document.lyrstat.opend.value = id;
	}

	function hideObj(id)
	{
		obj = eval(id+".style");
		obj.display = "none";

		document.lyrstat.opend.value = '';
	}

	function swapObj(id, vno)
	{
		EditForm = eval('document.EDIT_'+vno);
		//InputForm = document.INPUT_FORM;
		
		try{
			iStype = "<style>";
		   	iStype += "P {margin-top:0px;margin-bottom:0px;font-family:굴림체;font-size:11px;};";   	
		   	iStype += "TD{font-family:굴림체;font-size:11px;}";    
			iStype += "</style>";
			EditFrame = eval("document.frames['iframeEDIT_"+vno+"']");
			EditFrame.iView.document.body.innerHTML = iStype+EditForm.text.value;			
		}catch(E){}
		
		
		//InputForm.act.value = 'update';
		//InputForm.subj.value = EditForm.subj.value;
		//InputForm.content.value = EditForm.text.value;
		//Init(InputForm);
		
		swapObj_BackUp(id)
		
	}
	
	function swapObj_BackUp(id)
	{
		obj = eval(id+".style");
		stats = obj.display;

		if (stats == "none")
		{
			if (document.lyrstat.opend.value)
				hideObj(document.lyrstat.opend.value);

			showObj(id);
		}
		else
		{
			hideObj(id);
		}
	}

	function EventAct(act, no)
	{
		if (act == "update")
		{
			var form = eval("document.EDIT_"+no);
			
			EditForm = eval('document.EDIT_'+no);
		
		
			try{
				EditFrame = eval("document.frames['iframeEDIT_"+no+"']");
				EditForm.text.value = EditFrame.iView.document.body.innerHTML;			
			}catch(E){}

			form.action = 'event.act.php?act='+act+'&no='+no;
			form.submit();
		}
//?act=insert&div=1&subj=test&main=1&pop=1&html=1&disp=1&content=&x=37&y=15
		if (act == "insert")
		{
			var form = document.INPUT_FORM;
			
			try{
			//alert(iView.document.body.innerHTML);
				form.text.value = iView.document.body.innerHTML;			
			}catch(E){}

			form.action = 'event.act.php?act='+act;
			return true;
			//form.submit();
		}

		if (act == "delete")
		{
			if(confirm('정말로 삭제하시겠습니까?'))
			{
				document.frames("act").location.href= 'event.act.php?act='+act+'&no='+no;
			}
		}
	}

//-->
