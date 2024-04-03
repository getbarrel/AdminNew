function CompanyInput(frm,mode)
{
	if(mode == "insert" || mode=="update"){
		for(i=0;i < frm.elements.length;i++){
			if(!CheckForm(frm.elements[i])){
				return false;
			}
		}
	}
	frm.mode.value = mode;
	//frm.companyimg.style.display="block";
	frm.submit();
}
function ViewCompanyImage(bc_ix)
{
	//document.frames["extand"].location.href="company.act.php?mode=change&c_ix="+c_ix;
	document.getElementById("extand").src="buying_company.act.php?mode=change&bc_ix="+bc_ix;//kbk
	
}