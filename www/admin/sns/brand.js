function BrandInput(frm,mode)
{
	frm.mode.value = mode;
	//frm.brandimg.style.display="block";
	//frm.top_design.value = iView.document.body.innerHTML;
	frm.top_design.value = document.getElementById("iView").contentWindow.document.body.innerHTML;//kbk
	return CheckFormValue(frm);
	//frm.submit();
}

function BrandSubmit(frm,mode)
{
	frm.mode.value = mode;
	//frm.brandimg.style.display="block";
	//frm.top_design.value = iView.document.body.innerHTML;
	frm.top_design.value = document.getElementById("iView").contentWindow.document.body.innerHTML;//kbk
	if(CheckFormValue(frm)){
		frm.submit();		
	}
	
}

function ViewBrandImage(b_ix)
{
	//document.frames["extand"].location.href="brand.act.php?mode=change&b_ix="+b_ix;
	document.getElementById("extand").src="brand.act.php?mode=change&b_ix="+b_ix;//kbk
	
}