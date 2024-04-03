<?
include("../class/layout.class");
$Script = "
<style>
.sub_centes	{width:734px;margin:0 auto;}
.serviceBox ul li {line-height: 170%;}
.Pgap_B50 {padding-bottom: 50px;}
.Mgap_L10 {margin-left: 10px;}
.Mgap_L15 {margin-left: 15px;}
.serviceBox h2 {margin: 40px 0 20px 0;}
.under_line01 {border-bottom: solid 1px #D6D6D7;}
.left_listarrow li {background: url(/admin/images/pg/Angle_Bracket_Right.gif) no-repeat 0 4px;}
.left_listarrow li span {margin-left: 10px;}

/*테이블 간격및 선색상*/
.Table_box	td	{border-top:solid 1px #d6d6d7;padding:10px 0; }
.Table_box	.td_border_b	{border-bottom:solid 1px #d6d6d7;}
.bg_td	{background:#f8f8f8;}
.btn_Right	{text-align:right;border-bottom:solid 1px #d6d6d7;}
.border_down_line	td	{border-bottom:solid 1px #d4d4d4;}

.bg_td, .right_line	{border-right:solid 1px #D6D6D7;}
.pg_info	{}
.pg_info	h5	{margin-bottom:20px;}
.pg_info	ul	{margin-bottom:30px;}
.pg_info	ul	li	{line-height:180%;}
.box_pg		{float:left;width:100%;}
.box_pg01	{width:232px;float:left;}
.box_pg02	{float:right;}

</style>
<script language='javascript'>

function changeTab(vid, tab_id){
	var area = new Array('kcp','lgdacom','inicis','ksnet');
	var tab = new Array('tab_01','tab_02','tab_04','tab_05');
	
	for(var i=0; i<area.length; ++i){
		if(area[i]==vid){
			document.getElementById(vid).style.display = 'block';			
			document.getElementById(tab_id).className = 'on';
		}else{			
			document.getElementById(area[i]).style.display = 'none';
			document.getElementById(tab[i]).className = '';
		}
	}
	
	
		
}


function action(){
	window.open('','ptn_open','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=665,height=630');
	document.form.target = 'ptn_open';
	document.form.submit();
}
	
</script>";
$Contents =	"<table cellpadding=0 cellspacing=0 width='100%' >
			<tr>
			    <td align='left' colspan=4 style='padding-bottom:10px;'> ".GetTitleNavigation("결제모듈신청", "상점관리 > 결제모듈신청")."</td>
			</tr>
			<tr>
				<td colspan='4'>
						<div class='sub_centes'>
						<div class='serviceBox'>
							<div class='Pgap_B50 under_line01'>
								<h2><img src='/admin/images/pg/electron_pay_title02.gif' title='서비스이용안내' align='absmiddle' /></h2>
								<div>
									<img src='/admin/images/pg/electron_pay_img02.gif' title='서비스이용안내' align='absmiddle' />
								</div>
							</div>
							<div class='Pgap_B50 under_line01'>
								<h2><img src='/admin/images/pg/electron_pay_title03.gif' title='필수체크사항' align='absmiddle' /></h2>
								<ul class='left_listarrow'>
									<li>
										<span> PG신청 전, 사업자등록을 하시고 사업자등록증과 대표자 인감 등 PG사가 요청하는 서류를 준비하셔야 합니다.</span>
									</li>
									<li>
										<span> 관리자 로그인 후 [기본정보관리]에서 쇼핑몰 기본필수정보를 입력하세요.</span>
									</li>
									<li>
										<span> 관리자 로그인 후 [상품관리]에서 판매할 상품을 10개이상 등록하셔야 합니다.</span>
									</li>
								</ul>
							</div>
							<div class='Pgap_B50 box_pg'>
								<h2><img src='/admin/images/pg/electron_pay_title04.gif' title='필수체크사항' align='absmiddle' /></h2>
								<div class='box_pg01'>
									<table cellpadding='0' cellspacing='0' border='0' width='232'>
										<tr>
											<td>
												<div class='pg_info'>
													<h5><img src='/admin/images/pg/kcp_img01.gif' title='' align='absmiddle' /></h5>
													<ul>
														<li>
															<img src='/admin/images/pg/list_arrow_icon.jpg' title='' align='absmiddle' /> <span style='vertical-align:middel;'>홈페이지 : <a href='http://www.kcp.ci.kr' target='_blank' style='color:red;'>http://www.kcp.co.kr</a></span>
														</li>
														<li>
															<img src='/admin/images/pg/list_arrow_icon.jpg' title='' align='absmiddle' /> <span style='vertical-align:middel;'>대표번호 : <span style='color:red;'>1544-8662</span></span>
														</li>
														<li>
															<img src='/admin/images/pg/list_arrow_icon.jpg' title='' align='absmiddle' /> <span style='vertical-align:middel;'> FAX        : 02-2108-1087</span>
														</li>
														<li>
															<img src='/admin/images/pg/list_arrow_icon.jpg' title='' align='absmiddle' /> <span style='vertical-align:middel;'> 담당자     : 이은옥(leo0922@kcp.co.kr)<br />
															<span style='margin-left:65px;'>문정은(jungeun@kcp.co.kr)</span></span>
														</li>
													</ul>
												</div>
												<div>
													<a href='http://www.kcp.co.kr/service.payment.guide.do?cmd=guid03' target='_blank'><img src='/admin/images/pg/contract_down.gif' title='' align='absmiddle' /></a>
													<a href='https://admin8.kcp.co.kr/hp.HomePageAction.do?cmd=apply&host_id=몰스토리'  target='_blank'><img src='/admin/images/pg/online_btns02.gif' title='kcp온라인신청' align='absmiddle' /></a>
												</div>
											</td>
										</tr>
									</table>
								</div>
								<div class='box_pg02'>
									<table cellpadding='0' cellspacing='0' border='0' width='488' class='Table_box'>
										<col width='92' />
										<col width='80' />
										<col width='*'/>
										<tr>
											<td rowspan='3' class='bg_td'>
												<span class='Mgap_L15'>수수료</span>
											</td>
											<td class='right_line'>
												<span class='Mgap_L15'>신용카드</span>
											</td>
											<td>
												<span class='Mgap_L15' style='color:red;'><strong>3.4</strong>%(부가세 별도)</span><br />
												<strong style='color: gray;' class='Mgap_L15'>★ 그랜드오픈기념 추가 할인 이벤트 준비중입니다</strong>
											</td>
										</tr>
										<tr>
											<td class='right_line'>
												<span class='Mgap_L15'>계좌이체</span>
											</td>
											<td>
												<span class='Mgap_L15'><strong>1.8</strong>% 만원 이하 소액(200원),부가세 별도</span>
											</td>
										</tr>
										<tr>
											<td class='right_line'>
												<span class='Mgap_L15'>가상계좌</span>
											</td>
											<td>
												<span class='Mgap_L15'><strong>300</strong>원/건, 부가세 별도</span>
											</td>
										</tr>
										<tr>
											<td colspan='2' class='bg_td'>
												<span class='Mgap_L15'>초기 등록비</span>
											</td>
											<td>
												<div class='Mgap_L15' style='line-height:140%;'>
													<span style='color:red;'>소호형 : 220,000원 → 기본가입 <br />
													비즈형 : <s>220,000원</s>  → 0원 (가입비 무료 행사 진행중)<br />
													</span>
													*가입시 최초 1회만 납부
												</div>
											</td>
										</tr>
										<tr>
											<td colspan='2' class='bg_td'>
												<span class='Mgap_L15'>연관리비</span>
											</td>
											<td>
												<span class='Mgap_L15'><strong style='color:red;'>면제</strong></span>
											</td>
										</tr>
										<tr>
											<td colspan='2' class='bg_td'>
												<span class='Mgap_L15'>보증보험</span>
											</td>
											<td>
												<span class='Mgap_L15' style='verticla-align:middle;'>500만원(84,700원/년)</span> <a href='http://www.kcp.co.kr/service.payment.guide.do?cmd=guid04' target='_blank'><img src='/admin/images/pg/online_btns01.gif' title='보증보험온라인신청' align='absmiddle' style='vertical-align:middle;' /></a>
											</td>
										</tr>
										<tr>
											<td colspan='2' class='bg_td' valign='top'>
												<span class='Mgap_L15'>정산주기</span>
											</td>
											<td>
												<div class='Mgap_L15'>
													<ul>
														<li>
															- 일일정산 매일지급 <br /><span class='Mgap_L10'>(결제일로부터 7일이후 부터 매일 입금)</span>
														</li>
														<li>
															- 월 4회 / 월 2회/ 월 1회 선택가능  <br /> <span class='Mgap_L10'>(위 4개지 중 선택 비용 수수료 동일)</span>
														</li>
													</ul>
												</div>
											</td>
										</tr>
										<tr>
											<td colspan='2' class='bg_td' style='border-bottom:solid 1px #d6d6d7;'>
												<span class='Mgap_L15'>서비스개시</span>
											</td>
											<td style='border-bottom:solid 1px #d6d6d7;'>
												<span class='Mgap_L15'>카드 심사 완료 후 바로</span>
											</td>
										</tr>
									</table>
								</div>
							</div>
							<div class='Pgap_B50 box_pg'>
								<div class='box_pg01'>
									<table cellpadding='0' cellspacing='0' border='0' width='232'>
										<tr>
											<td>
												<div class='pg_info'>
													<h5><img src='/admin/images/pg/lg_img01.gif' title='' align='absmiddle' /></h5>
													<ul>
														<li>
															<img src='/admin/images/pg/list_arrow_icon.jpg' title='' align='absmiddle' /> <span style='vertical-align:middel;'> 홈페이지 : <a href='http://ecredit.uplus.co.kr' target='_blank' style='color:red;'>http://ecredit.uplus.co.kr</a></span>
														</li>
														<li>
															<img src='/admin/images/pg/list_arrow_icon.jpg' title='' align='absmiddle' /> <span style='vertical-align:middel;'> 대표번호 : <span style='color:red;'>1544-7772</span></span>
														</li>
													</ul>
												</div>
												<div>
													<a href='http://www.mallstory.com/addservice/LGDACOM_eCredit_contact.doc'><img src='/admin/images/pg/contract_down.gif' title='다운로드' align='absmiddle' /></a>
													<a href='http://pgweb.dacom.net/pg/wmp/Home/application/apply_testid.jsp?cooperativecode=forbiz'  target='_blank'><img src='/admin/images/pg/online_btns02.gif' title='LG온라인신청' align='absmiddle' /></a>
												</div>
											</td>
										</tr>
									</table>
								</div>
								<div class='box_pg02'>
									<table cellpadding='0' cellspacing='0' border='0' width='488' class='Table_box'>
										<col width='92' />
										<col width='80' />
										<col width='*' />
										<tr>
											<td rowspan='3' class='bg_td'>
												<span class='Mgap_L15'>수수료</span>
											</td>
											<td class='right_line'>
												<span class='Mgap_L15'>신용카드</span>
											</td>
											<td>
												<span class='Mgap_L15'><strong>3.5</strong>%(부가세 별도)</span>
											</td>
										</tr>
										<tr>
											<td class='right_line'>
												<span class='Mgap_L15'>계좌이체</span>
											</td>
											<td>
												<span class='Mgap_L15'><strong>1.8</strong>% 만원 이하 소액(200원),부가세 별도</span>
											</td>
										</tr>
										<tr>
											<td class='right_line'>
												<span class='Mgap_L15'>가상계좌</span>
											</td>
											<td>
												<span class='Mgap_L15'><strong>400</strong>원/건, 부가세 별도</span>
											</td>
										</tr>
										<tr>
											<td colspan='2' class='bg_td'>
												<span class='Mgap_L15'>초기 등록비</span>
											</td>
											<td>
												<div class='Mgap_L15' style='line-height:140%;'>
													<span style='color:red;'>초기등록비 : 220,000원</span><br />
													*가입시 최초 1회만 납부
												</div>
											</td>
										</tr>
										<tr>
											<td colspan='2' class='bg_td'>
												<span class='Mgap_L15'>연관리비</span>
											</td>
											<td>
												<span class='Mgap_L15'><strong style='color:red;'>면제</strong></span>
											</td>
										</tr>
										<tr>
											<td colspan='2' class='bg_td'>
												<span class='Mgap_L15'>보증보험</span>
											</td>
											<td>
												<span class='Mgap_L15' style='verticla-align:middle;'>500만원(84,700원/년)</span> <a href='http://www.insansc.co.kr/insansc_co_kr/is/iso/isod/ibf_isod001_a.htm'><img src='/admin/images/pg/online_btns01.gif' title='보증보험온라인신청' align='absmiddle' style='vertical-align:middle;' /></a>
											</td>
										</tr>
										<tr>
											<td colspan='2' class='bg_td' valign='top'>
												<span class='Mgap_L15'>정산주기</span>
											</td>
											<td>
												<div class='Mgap_L15'>
													<ul>
														<li>
															매입요청일 기준 D+5일지급, 매일지급 <br />
															예)월요일 매입 요청시 차주 화요일 지급
														</li>
													</ul>
												</div>
											</td>
										</tr>
										<tr>
											<td colspan='2' class='bg_td' style='border-bottom:solid 1px #d6d6d7;'>
												<span class='Mgap_L15'>서비스개시</span>
											</td>
											<td style='border-bottom:solid 1px #d6d6d7;'>
												<span class='Mgap_L15'>카드 심사 완료 후 바로</span>
											</td>
										</tr>
									</table>
								</div>
							</div>
							<div class='Pgap_B50 box_pg'>
								<div class='box_pg01'>
									<table cellpadding='0' cellspacing='0' border='0' width='232'>
										<tr>
											<td>
												<div class='pg_info'>
													<h5><img src='/admin/images/pg/inicis_img01.gif' title='' align='absmiddle' /></h5>
													<ul>
														<li>
															<img src='/admin/images/pg/list_arrow_icon.jpg' title='' align='absmiddle' /> <span style='vertical-align:middel;'> 홈페이지 : <a href='http://www.inicis.co.kr' target='_blank' style='color:red;'>http://www.inicis.co.kr</a></span>
														</li>
														<li>
															<img src='/admin/images/pg/list_arrow_icon.jpg' title='' align='absmiddle' /> <span style='vertical-align:middel;'> 대표번호 : <span style='color:red;'>1588-4954</span></span>
														</li>
													</ul>
												</div>
												<div>
													<a href='http://www.mallstory.com/addservice/inipay03.zip'><img src='/admin/images/pg/contract_down.gif' title='' align='absmiddle' /></a>
													<a href='http://landing.inicis.com/landing/application/application01_2.php?cd=hostinglanding&product=foirbiz' target='_blank'><img src='/admin/images/pg/online_btns02.gif' title='inicis온라인신청' align='absmiddle' /></a>
												</div>
											</td>
										</tr>
									</table>
								</div>
								<div class='box_pg02'>
									<table cellpadding='0' cellspacing='0' border='0' width='488' class='Table_box'>
										<col width='92' />
										<col width='80' />
										<col width='*' />
										<tr>
											<td rowspan='3' class='bg_td'>
												<span class='Mgap_L15'>수수료</span>
											</td>
											<td class='right_line'>
												<span class='Mgap_L15'>신용카드</span>
											</td>
											<td>
												<span class='Mgap_L15'><strong>3.4</strong>%(부가세 별도)</span>
											</td>
										</tr>
										<tr>
											<td class='right_line'>
												<span class='Mgap_L15'>계좌이체</span>
											</td>
											<td>
												<span class='Mgap_L15'><strong>1.8</strong>% 만원 이하 소액(200원),부가세 별도</span>
											</td>
										</tr>
										<tr>
											<td class='right_line'>
												<span class='Mgap_L15'>가상계좌</span>
											</td>
											<td>
												<span class='Mgap_L15'><strong>300</strong>원/건, 부가세 별도</span>
											</td>
										</tr>
										<tr>
											<td colspan='2' class='bg_td'>
												<span class='Mgap_L15'>초기 등록비</span>
											</td>
											<td>
												<div class='Mgap_L15' style='line-height:140%;'>
													<span style='color:red;'>초기등록비  : 220,000원<br /></span>
													*가입시 최초 1회만 납부
												</div>
											</td>
										</tr>
										<tr>
											<td colspan='2' class='bg_td'>
												<span class='Mgap_L15'>연관리비</span>
											</td>
											<td>
												<span class='Mgap_L15'><strong style='color:red;'>면제</strong></span>
											</td>
										</tr>
										<tr>
											<td colspan='2' class='bg_td'>
												<span class='Mgap_L15'>보증보험</span>
											</td>
											<td>
												<div class='Mgap_L15'>
													<ul>
														<li>기본면제 / 월한도 천만원 <br /> (카드사 및 당사 유의 업종은 별도 협의) <br/> 200만원( 33,880원/년)
													</li>
													<a href='http://www.okbojeung.co.kr/inicis/' target='_blank'><img src='/admin/images/pg/online_btns01.gif' title='보증보험온라인신청' align='absmiddle' style='vertical-align:middle;' /></a>
													</ul>
												</div>
											</td>
										</tr>
										<tr>
											<td colspan='2' class='bg_td' valign='top'>
												<span class='Mgap_L15'>정산주기</span>
											</td>
											<td>
												<div class='Mgap_L15'>
													<ul>
														<li>
														통합정산 월4회, 월2회, 월1회, 7일정산
														</li>
													</ul>
												</div>
											</td>
										</tr>
										<tr>
											<td colspan='2' class='bg_td' style='border-bottom:solid 1px #d6d6d7;'>
												<span class='Mgap_L15'>서비스개시</span>
											</td>
											<td style='border-bottom:solid 1px #d6d6d7;'>
												<span class='Mgap_L15'>카드 심사 완료 후 바로</span>
											</td>
										</tr>
									</table>
								</div>
							</div>
							<!--div class='Pgap_B50 under_line01 box_pg'>
								<div class='box_pg01'>
									<table cellpadding='0' cellspacing='0' border='0' width='232'>
										<tr>
											<td>
												<div class='pg_info'>
													<h5><img src='/admin/images/pg/ksnet_img01.gif' title='' align='absmiddle' /></h5>
													<ul>
														<li>
															<img src='/admin/images/pg/list_arrow_icon.jpg' title='' align='absmiddle' /> <span style='vertical-align:middel;'> 홈페이지 : <a href='http://www.ksnet.co.kr' style='color:red;'>http://www.ksnet.co.kr</a></span>
														</li>
														<li>
															<img src='/admin/images/pg/list_arrow_icon.jpg' title='' align='absmiddle' /> <span style='vertical-align:middel;'> 대표번호 : <span style='color:red;'>02-3420-5800</span></span>
														</li>
													</ul>
												</div>
												<div>
													<a href='#'><img src='/admin/images/pg/contract_down.gif' title='' align='absmiddle' /></a>
													<a href='http://cms.ksnet.co.kr/index.php?mid=c3-4'  target='_blank'><img src='/admin/images/pg/online_btns02.gif' title='ksnet온라인신청' align='absmiddle' /></a>
												</div>
											</td>
										</tr>
									</table>
								</div>
								<div class='box_pg02'>
									<table cellpadding='0' cellspacing='0' border='0' width='488' class='Table_box'>
										<col width='92' />
										<col width='80' />
										<col width='*' />
										<tr>
											<td rowspan='3' class='bg_td'>
												<span class='Mgap_L15'>수수료</span>
											</td>
											<td class='right_line'>
												<span class='Mgap_L15'>신용카드</span>
											</td>
											<td>
												<span class='Mgap_L15'><strong>3.4</strong>%(부가세 별도)</span>
											</td>
										</tr>
										<tr>
											<td class='right_line'>
												<span class='Mgap_L15'>계좌이체</span>
											</td>
											<td>
												<span class='Mgap_L15'><strong>2.0%</strong>% 만원 이하 소액(250원),부가세 별도</span>
											</td>
										</tr>
										<tr>
											<td class='right_line'>
												<span class='Mgap_L15'>가상계좌</span>
											</td>
											<td>
												<span class='Mgap_L15'><strong>300</strong>원/건, 부가세 별도</span>
											</td>
										</tr>
										<tr>
											<td colspan='2' class='bg_td'>
												<span class='Mgap_L15'>초기 등록비</span>
											</td>
											<td>
												<span class='Mgap_L15'><strong style='color:red;'>200,000</strong>원 (부가세 별도,가입시 최초 1회만 납부)</span>
											</td>
										</tr>
										<tr>
											<td colspan='2' class='bg_td'>
												<span class='Mgap_L15'>연관리비</span>
											</td>
											<td>
												<span class='Mgap_L15'><strong style='color:red;'>면제</strong></span>
											</td>
										</tr>
										<tr>
											<td colspan='2' class='bg_td'>
												<span class='Mgap_L15'>보증보험</span>
											</td>
											<td>
												<span class='Mgap_L15' style='verticla-align:middle;'>500만원(96,000원/년)</span> <a href='#'><img src='/admin/images/pg/online_btns01.gif' title='보증보험온라인신청' align='absmiddle' /></a>
											</td>
										</tr>
										<tr>
											<td colspan='2' class='bg_td' valign='top'>
												<span class='Mgap_L15'>정산주기</span>
											</td>
											<td>
												<div class='Mgap_L15'>
													<ul>
														<li>
														매입요청일 기준 D+5일지급, 매일지급 <br />
														예)월요일 매입 요청시 차주 화요일 지급
														</li>
													</ul>
												</div>
											</td>
										</tr>
										<tr>
											<td colspan='2' class='bg_td'>
												<span class='Mgap_L15'>서비스개시</span>
											</td>
											<td>
												<span class='Mgap_L15'>카드 심사 완료 후 바로</span>
											</td>
										</tr>
									</table>
								</div>
							</div-->
						</div>
					</div>	
				</td>
			</tr>
		</table>";

$P = new LayOut();
$P->strLeftMenu = store_menu();
$P->addScript = $Script;
$P->Navigation = "상점관리 > 결제모듈신청";
$P->title = "결제모듈신청";
$P->strContents = $Contents;
$P->PrintLayOut();
?>