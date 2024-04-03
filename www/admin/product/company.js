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

function CompanyInput_list(frm)
{
	//frm.mode.value = mode;
	//frm.companyimg.style.display="block";
	return CheckFormValue(frm);
}

function ViewCompanyImage(c_ix)
{
	//document.frames["extand"].location.href="company.act.php?mode=change&c_ix="+c_ix;
	document.getElementById("extand").src="company.act.php?mode=change&c_ix="+c_ix;//kbk
	
}

function loadCategory(sel,target) {
	//alert(target);
	var trigger = sel.options[sel.selectedIndex].value;	
	var form = sel.form.name;
	//var depth = sel.depth;
	//var depth = sel.getAttribute("depth");//kbk
	var depth = $("select[name="+sel.name+"]").attr('depth');//sel.depth; 크롬도 되도록 (홍진영)
	//if(depth == 2){
	//document.write('category.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target);
	//}
	//alert(target);
	//dynamic.src = 'category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;//kbk
	
	window.frames["act"].location.href = 'category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

	//document.getElementById("act").src = 'category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
	
}

function deleteCompanyInfo(mode, og_ix){
	if(confirm('해당 제조사 정보를 정말로 삭제하시겠습니까?')){
		window.frames['act'].location.href = './company.act.php?mode=delete&c_ix='+og_ix;
	}
}
