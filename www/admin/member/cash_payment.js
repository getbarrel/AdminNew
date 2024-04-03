function goPay2(frm){
		//alert(frm.gopaymethod.value);
		//alert(frm.pg_com.value);
		if(frm.pg_com.value == "allthegate"){
			/*
			if(frm.Job.value == "bank" || frm.Job.value == "VBank"){
				if(frm.escrow_use.value == "1"){
					frm.Job.value = "onlyiche";
					//alert(frm.Job.value);
					frm.action='/shop/securepay_card.php'			
					Pay(frmAGS_pay);
				}else{
					frm.action='/shop/securepay_bank.php'			
					frm.submit();
				}
			}else if(frm.Job.value == "after"){
				frm.action='/shop/securepay_after.php'
				frm.submit();
			}else if(frm.Job.value == "Card"){
				frm.Job.value = "onlycard";
				Pay(frmAGS_pay);
			}else if(frm.Job.value == "virtual"){
				frm.Job.value = "onlyvirtual";
				Pay(frmAGS_pay);
			}else{
				alert('결제방법을 선택해주세요');
				return false;
			}
			*/
			if(frm.Job.value == "bank"){
				frm.action='/shop/securepay_bank.php'			
				frm.submit();
			}else if(frm.Job.value == "after"){
				frm.action='/shop/securepay_after.php'
				frm.submit();
			}else if(frm.Job.value == "Card"){
				frm.Job.value = "onlycard";
				Pay(frmAGS_pay);
			}else if(frm.Job.value == "virtual"){
				frm.Job.value = "onlyvirtual";
				Pay(frmAGS_pay);
			}else if(frm.Job.value == "iche"){
				frm.Job.value = "onlyiche";
				Pay(frmAGS_pay);
			}else{
				alert('결제방법을 선택해주세요');
				return false;
			}
		}else if(frm.pg_com.value == "inicis"){		
			if(frm.gopaymethod.value == "Card" || frm.gopaymethod.value == "vCard"){
				frm.gopaymethod.value = 'Card';
					if(frm.escrow_use.value == "1" && frm.escrow_apply.value == 1){
						frm.action='/shop/ascrow.php';
						if(MakePayMessage(frm)){
							frm.submit();
						}
					}else if(frm.escrow_use.value == "1" && frm.escrow_apply.value == 0){
						if(frm.price.value >= 100000){
							frm.action='/shop/ascrow.php';
							if(MakePayMessage(frm)){
								frm.submit();
							}
						}else{
							frm.action='/shop/securepay_card.php';
							card(frm);		
						}
					}else{
						frm.action='/shop/securepay_card.php';
						card(frm);						
					}

			}else if(frm.gopaymethod.value == "bank"){
				frm.action='/shop/securepay_bank.php'			
				frm.submit();
			}else if(frm.gopaymethod.value == "virtual"){
				frm.action='/shop/securepay_card.php'			
				frm.submit();	
			}else if(frm.gopaymethod.value == "after"){
				frm.action='/shop/securepay_after.php'
				frm.submit();
			}else if(frm.gopaymethod.value == "VBank"){
				frm.paymethod.value = 'VBank';
				frm.action='/shop/ascrow.php'
				
				if(MakePayMessage(frm)){
					frm.submit();
				}
				
			}else{
				alert('결제방법을 선택해주세요');
				return false;
			}
		}else if(frm.pg_com.value == "lgdacom"){	
			if(frm.gopaymethod.value == "Card" || frm.gopaymethod.value == "iche" || frm.gopaymethod.value == "virtual"){
				
				new Ajax.Request('card_insert.php',
				{
					method: 'POST',
					//parameters: 'sattle_module=lgdacom',
					onComplete: function(transport){
						//alert("'"+transport.responseText+"'");
						if(transport.responseText == 'OK'){
							//alert(1);
							//alert(xpay_check(frm, frm.CST_PLATFORM.value));
							if(frm.gopaymethod.value == "iche" || frm.gopaymethod.value == "virtual"){
								if(frm.LGD_AMOUNT.value >= 100000){
									var X = confirm('에스크로 결제를 진행하시겠습니까?');
									if(X == true){
										frm.LGD_ESCROW_USEYN.value = 'Y';
									}else{
										frm.LGD_ESCROW_USEYN.value = 'N';
									}
								}
							}
							ret = xpay_check(frm, frm.CST_PLATFORM.value);
							
							if (ret=="00"){     //ActiveX 로딩 성공  
								var LGD_RESPCODE        = dpop.getData('LGD_RESPCODE');       //결과코드
								var LGD_RESPMSG         = dpop.getData('LGD_RESPMSG');        //결과메세지    
								//alert(frm.LGD_HASHDATA.value);
								//alert(LGD_RESPCODE);
								if( "0000" == LGD_RESPCODE ) { //결제성공
								
									var LGD_TID             = dpop.getData('LGD_TID');            //LG데이콤 거래KEY
									var LGD_PAYTYPE         = dpop.getData('LGD_PAYTYPE');        //결제수단
									var LGD_PAYDATE         = dpop.getData('LGD_PAYDATE');        //결제일자
									var LGD_FINANCECODE     = dpop.getData('LGD_FINANCECODE');    //결제기관코드
									var LGD_FINANCENAME     = dpop.getData('LGD_FINANCENAME');    //결제기관이름        
									var LGD_NOTEURL_RESULT  = dpop.getData('LGD_NOTEURL_RESULT'); //상점DB처리(LGD_NOTEURL)결과 ('OK':정상,그외:실패)
									var LGD_FINANCEAUTHNUM  = dpop.getData('LGD_FINANCEAUTHNUM');
									var LGD_ACCOUNTNUM		= dpop.getData('LGD_ACCOUNTNUM');
									var LGD_CASFLAG		= dpop.getData('LGD_CASFLAG');
									var LGD_CASHRECEIPTNUM		= dpop.getData('LGD_CASHRECEIPTNUM');
									//alert(LGD_ACCOUNTNUM);
									//alert(LGD_NOTEURL_RESULT);
									//메뉴얼의 결제결과 파라미터내용을 참고하시어 필요하신 파라미터를 추가하여 사용하시기 바랍니다.           
									//alert(frm.LGD_NOTEURL.value);
									//var msg = "결제결과 : " + LGD_RESPMSG + "\n";            
									//msg += "LG데이콤거래TID : " + LGD_TID +"\n\n"; 
									if( LGD_NOTEURL_RESULT != "null" ){
									//alert(msg);
									//frm.LGD_NOTEURL.value='http://oxobike.co.kr/shop/securepay_card.php'
									frm.LGD_RESPCODE.value = LGD_RESPCODE;
									frm.LGD_RESPMSG.value = LGD_RESPMSG;
									frm.LGD_TID.value = LGD_TID;
									frm.LGD_PAYTYPE.value = LGD_PAYTYPE;
									frm.LGD_PAYDATE.value = LGD_PAYDATE;
									frm.LGD_FINANCECODE.value = LGD_FINANCECODE;
									frm.LGD_FINANCENAME.value = LGD_FINANCENAME;
									frm.LGD_NOTEURL_RESULT.value = LGD_NOTEURL_RESULT;
									frm.LGD_FINANCEAUTHNUM.value = LGD_FINANCEAUTHNUM;
									frm.LGD_ACCOUNTNUM.value = LGD_ACCOUNTNUM;
									frm.LGD_CASFLAG.value = LGD_CASFLAG;
									frm.LGD_CASHRECEIPTNUM.value = LGD_CASHRECEIPTNUM;
									frm.action = "securepay_card.php";
									frm.submit();	
									/*
									 * 결제성공 화면 처리
									 */
									}
								} else { //결제실패
									 alert("결제가 실패하였습니다. " + LGD_RESPMSG);
									/*
									 * 결제실패 화면 처리
									 */        
								}
							} else {
								alert("LG데이콤 전자결제를 위한 ActiveX 설치 실패");
							}
						}else{
							alert('세션정보가 존재하지 않습니다. 확인후 다시 주문해주세요');
							return false;
						}
					}
				});
				
			}else{
				frm.action = "securepay_bank.php";
				frm.submit();
			}

		}else if(frm.pg_com.value == "kgtg"){	
			
			if(frm.gopaymethod.value == "Card" || frm.gopaymethod.value == "iche" || frm.gopaymethod.value == "virtual"){
				//alert(frm.pg_com.value)
				new Ajax.Request('card_insert.php',
				{
					method: 'POST',
					//parameters: 'sattle_module=lgdacom',
					onComplete: function(transport){
						//alert("'"+transport.responseText+"'");
						/*if(frm.gopaymethod.value == "virtual"){
							if(frm.Amount.value >= 100000){
								var X = confirm('에스크로 결제를 진행하시겠습니까?');
								if(X == true){
									frm.Smode.value = '9011';
								}
							}
						}*/
						
						if(transport.responseText == 'OK'){
							PAY_REQUEST(frm);
						}else{
							alert('세션정보가 존재하지 않습니다. 확인후 다시 주문해주세요');
							return false;
						}
					}
				});
				
			}else{
				frm.action = "securepay_bank.php";
				frm.submit();
			}

		}else if(frm.pg_com.value == "kcp"){
			//alert(frm.gopaymethod.value);
			if(frm.gopaymethod.value == "Card" || frm.gopaymethod.value == "vbank"){
				//alert(1);
				new Ajax.Request('cash_pop.act.php',
				{
					method: 'POST',
					onComplete: function(transport){
						//alert(transport.responseText);
						if(transport.responseText == 'OK'){
							var RetVal = false;
							
							if( document.Payplus.object == null )
							{
								openwin = window.open( "/popup/kcp/sample/chk_plugin.html", "chk_plugin", "width=420, height=100, top=300, left=300" );
							}

							/* Payplus Plugin 실행 */
							if ( MakePayMessage( frm ) == true )
							{
								//openwin = window.open( "/popup/kcp/sample/proc_win.html", "proc_win", "width=449, height=209, top=300, left=300" );
								RetVal = true ;
								
								document.order_info.action = "/popup/cash_pop02.php";
								document.order_info.submit();
							}
							
							else
							{
								/*  res_cd와 res_msg변수에 해당 오류코드와 오류메시지가 설정됩니다.
									ex) 고객이 Payplus Plugin에서 취소 버튼 클릭시 res_cd=3001, res_msg=사용자 취소
									값이 설정됩니다.
								*/
								res_cd  = document.order_info.res_cd.value ;
								res_msg = document.order_info.res_msg.value ;

								alert ( "Payplus Plug-in \n" + "res_cd = " + res_cd + "|" + "res_msg=" + res_msg ) ;
							}

							
						}else{
							alert('세션정보가 존재하지 않습니다. 확인후 다시 주문해주세요');
							return false;
						}
					}
				});
				
			}else{
				frm.action = "cash_pop02.php";
				frm.submit();
			}
		}else if(frm.pg_com.value == "nicepay"){
			//alert(frm.selectType.value);
			if(frm.selectType.value == "CARD" || frm.selectType.value == "VBANK" || frm.selectType.value == "BANK"){
				 $.ajax({
					url : 'charge_pop.act.php',
					type : 'POST',
					dataType: 'html',
					error: function(data,error){// 실패시 실행함수 
						alert(error);}, 
					success: function(transport){
							//alert(transport);
						if(transport == 'OK'){
							var RetVal = false;
							/*
							if( document.Payplus.object == null )
							{
								openwin = window.open( "/shop/kcp/sample/chk_plugin.html", "chk_plugin", "width=420, height=100, top=300, left=300" );
							}
							*/
							/* Payplus Plugin 실행 */
							//top.document.getElementById("iframe_act2").src='nicepayRequest.jsp';
							if (goPay( frm ) == true )
							{
								//openwin = window.open( "/shop/kcp/sample/proc_win.html", "proc_win", "width=449, height=209, top=300, left=300" );
								//RetVal = true ;
								//document.order_info.action = "securepay_card.php";
								//document.order_info.submit();
							}
							
							else
							{
								//alert("결제가 취소 되었습니다");
							}
						}
					}
				});	
			}else if(frm.selectType.value == "after_bank"){
				frm.action='charge_pop.act.php'			
				frm.submit();
			}else if(frm.selectType.value == "virtual"){
				frm.action='charge_pop.act.php'			
				frm.submit();	
			}else if(frm.selectType.value == "after"){
				frm.action='charge_pop.act.php'
				frm.submit();
			}else if(frm.selectType.value == "VBank"){
				frm.paymethod.value = 'VBank';
				frm.action='/shop/ascrow.php'
				frm.submit();
				
			}else{
				alert('결제방법을 선택해주세요(나이스페이)');
				return false;
			}
		
		}
		
	}
	function CheckValue(frm){
	//alert(document.sef_form.elements[0].name)
		var discount;
		
		for(i=0;i < frm.elements.length;i++){		
			if(!CheckForm(frm.elements[i])){
				return false;
			}
		}

		for(i=0;i< frm.pay_method.length;i++){
			if(frm.pay_method[i].checked){
				check_method =frm.pay_method[i].value;
			}
		}

	}