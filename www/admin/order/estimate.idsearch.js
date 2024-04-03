function idsearch_in(formname,a,b,c,d,e,f,g,h,i,j,k,l,m,n){
	var obj = formname;
	var cArr = c.split('-');
	var fArr = f.split('-');
	var gArr = g.split('-');
	var kArr = k.split('-');

	obj.ucode.value = a;
	obj.name_a.value = b;
	obj.zipcode1.value = cArr[0];
	obj.zipcode2.value = cArr[1];
	obj.addr1.value = d;
	obj.addr2.value = e;
	obj.tel1_a.value = fArr[0];
	obj.tel2_a.value = fArr[1];
	obj.tel3_a.value = fArr[2];
	obj.pcs1_a.value = gArr[0];
	obj.pcs2_a.value = gArr[1];
	obj.pcs3_a.value = gArr[2];
	obj.mail_a.value = h;
	obj.sc_name2.value = i;
	obj.sc_name.value = i;
	obj.sc_number.value = j;
	obj.sc_zip1.value = kArr[0];
	obj.sc_zip2.value = kArr[1];
	obj.sc_addr1.value = l;
	obj.sc_addr2.value = m;
	obj.sc_ceo.value = n;
	
	opener.isEQ_sc();
	self.close();
}
