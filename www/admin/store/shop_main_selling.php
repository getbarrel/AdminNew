<?
include_once("../class/layout.class");


$contents = "
			<div class='wrap-selling-area'>
				<div class='selling-intro'>
					<img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/img_banner_top.jpg' alt='' />
					<h2>Global selling tool 서비스란?</h2>
					<p class='desc'>국내 뿐만 아니라 및 각 나라를 대표하는 Global 마켓으로 <em>판매채널을 확장하여 추가 매출을 실현</em>할 수 있습니다.<br />
					자사몰에 등록하신 모든 상품을 제휴몰에 일괄 등록하고 수정할 수 있으며, 판매관리를 한번에 처리할 수 있습니다.<br />
					또한 제휴몰의 주문을 자동으로 수집하여 주문 발주확인 및 일괄 배송처리할 수 있습니다.</p>
				</div>
				<div class='selling-service'>
					<h2>서비스 특징</h2>
					<ul class='service-list'>
						<li>
							<img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/img_service01.png' alt='' class='img' />
							<strong>국내최고 이커머스 통합 솔루션</strong>
							매출을 빠르게 향상시킬 수 있는 다양한 기능과 서비스들을<br />하나의 솔루션으로 통합하였습니다.
						</li>
						<li>
							<img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/img_service02.png' alt=''class='img' />
							<strong>이커머스 ERP 역할 수행</strong>
							비즈니스 관리도구로서 이커머스 ERP 역할을 수행 할 수 있는<br />국내 유일의 통합솔루션 입니다.
						</li>
						<li>
							<img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/img_service03.png' alt='' class='img' />
							<strong>비즈니스 단계별 확장이 가능</strong>
							스몰 비즈니스부터 엔터프라이즈, 그리고 글로벌 비즈니스<br />까지 단계별 비즈니스 확장성을 고려하여 만들어 졌습니다.
						</li>
						<li>
							<img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/img_service04.png' alt='' class='img' />
							<strong>옴니채널 관리가 가능</strong>
							PC, Mobile, Off shop 그리고 Social Media까지 고객 구매 패턴에<br>최적화된 다채널 관리를 수행 할 수 있습니다.
						</li>
						<li>
							<img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/img_service05.png' alt='' class='img' />
							<strong>구축경험과 노하우 반영</strong>
							500개 프로젝트 구축 및 운영노하우와 고객의 피드백<br />까지 담아 구현된 솔루션 입니다.
						</li>
					</ul>
				</div>
				<div class='service-status'>
					<h2>서비스 제휴몰 현황</h2>
					<div class='menu-tab'>
						<a href='#tab-cont01' class='active'>국내제휴몰</a>
						<a href='#tab-cont02'>해외제휴몰</a>
					</div>
					<div class='wrap-tab-cont'>
						<div id='tab-cont01' class='tab-cont active'>
							<table class='status-table'>
								<colgroup>
									<col width='*' />
									<col width='14%' />
									<col width='11%' />
									<col width='11%' />
									<col width='11%' />
									<col width='11%' />
									<col width='11%' />
									<col width='11%' />
								</colgroup>
								<thead>
								<tr>
									<th scope='col' rowspan='2'>제휴몰 명</th>
									<th scope='col' rowspan='2'>서비스 상태</th>
									<th scope='col' colspan='6' class='end'>서비스 제공현황</th>
								</tr>
								<tr>
									<th scope='col'>상품등록</th>
									<th scope='col'>상품수정</th>
									<th scope='col'>주문수집</th>
									<th scope='col'>송장전송</th>
									<th scope='col'>클레임</th>
									<th scope='col' class='end'>문의/후기</th>
								</tr>
								</thead>
								<tbody>
								<tr>
									<td class='inner-logo'>
										<span class='c-logo'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/img_service_status01.png' alt='' /></span>
										11번가
									</td>
									<td>서비스 이용중</td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td class='end'><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
								</tr>
								<tr>
									<td class='inner-logo'>
										<span class='c-logo'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/img_service_status02.png' alt='' /></span>
										옥션
									</td>
									<td>서비스 이용중</td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td class='end'><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
								</tr>
								<tr>
									<td class='inner-logo'>
										<span class='c-logo'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/img_service_status03.png' alt='' /></span>
										지마켓
									</td>
									<td>서비스 이용중</td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td class='end'><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
								</tr>
								<tr>
									<td class='inner-logo'>
										<span class='c-logo'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/img_service_status04.png' alt='' /></span>
										인터파크
									</td>
									<td>서비스 이용중</td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td class='end'><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
								</tr>
								<tr>
									<td class='inner-logo'>
										<span class='c-logo'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/img_service_status05.png' alt='' /></span>
										CJmall
									</td>
									<td>서비스 이용가능</td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td class='end'><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
								</tr>
								<tr>
									<td class='inner-logo'>
										<span class='c-logo'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/img_service_status06.png' alt='' /></span>
										GSshop
									</td>
									<td>서비스 이용가능</td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon2'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status02.png' alt='' />연동불가</em></td>
									<td class='end'><em class='icon2'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status02.png' alt='' />연동불가</em></td>
								</tr>
								<tr>
									<td class='inner-logo'>
										<span class='c-logo'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/img_service_status07.png' alt='' /></span>
										패션플러스
									</td>
									<td>서비스 이용가능</td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td class='end'><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
								</tr>
								<tr>
									<td class='inner-logo'>
										<span class='c-logo'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/img_service_status08.png' alt='' /></span>
										아이스타일
									</td>
									<td>서비스 이용가능</td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td class='end'><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
								</tr>
								<tr>
									<td class='inner-logo'>
										<span class='c-logo'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/img_service_status09.png' alt='' /></span>
										G9
									</td>
									<td>서비스 이용중</td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td class='end'><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
								</tr>
								<tr>
									<td class='inner-logo'>
										<span class='c-logo'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/img_service_status10.png' alt='' /></span>
										글로벌지마켓
									</td>
									<td>서비스 이용중</td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td class='end'><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
								</tr>
								<tr>
									<td class='inner-logo'>
										<span class='c-logo'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/img_service_status11.png' alt='' /></span>
										하프클럽
									</td>
									<td>서비스 이용중</td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td class='end'><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
								</tr>
								<tr>
									<td class='inner-logo'>
										<span class='c-logo'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/img_service_status12.png' alt='' /></span>
										11번가 쇼킹딜
									</td>
									<td>서비스 이용중</td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td class='end'><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
								</tr>
								<tr>
									<td class='inner-logo'>
										<span class='c-logo'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/img_service_status13.png' alt='' /></span>
										다이소몰
									</td>
									<td>개발중</td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td class='end'><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
								</tr>
								<tr>
									<td class='inner-logo'>
										<span class='c-logo'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/img_service_status14.png' alt='' /></span>
										네이버스토어팜
									</td>
									<td>서비스 이용중</td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td class='end'><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
								</tr>
								<tr>
									<td class='inner-logo'>
										<span class='c-logo'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/img_service_status15.png' alt='' /></span>
										티몬
									</td>
									<td>개발중</td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td class='end'><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
								</tr>
								<tr>
									<td class='inner-logo'>
										<span class='c-logo'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/img_service_status16.png' alt='' /></span>
										위메프
									</td>
									<td>개발중</td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td class='end'><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
								</tr>
								<tr>
									<td class='inner-logo'>
										<span class='c-logo'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/img_service_status17.png' alt='' /></span>
										쿠팡
									</td>
									<td>개발중</td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td class='end'><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
								</tr>
								<tr>
									<td class='inner-logo'>
										<span class='c-logo'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/img_service_status18.png' alt='' /></span>
										롯데닷컴
									</td>
									<td>개발중</td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td class='end'><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
								</tr>
								<tr>
									<td class='inner-logo'>
										<span class='c-logo'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/img_service_status19.png' alt='' /></span>
										롯데아이몰
									</td>
									<td>개발중</td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td class='end'><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
								</tr>
								<tr>
									<td class='inner-logo'>
										<span class='c-logo'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/img_service_status20.png' alt='' /></span>
										롯데마트
									</td>
									<td>개발중</td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td class='end'><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
								</tr>
								<tr>
									<td class='inner-logo'>
										<span class='c-logo'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/img_service_status21.png' alt='' /></span>
										홈플러스
									</td>
									<td>개발중</td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td class='end'><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
								</tr>
								<tr>
									<td class='inner-logo'>
										<span class='c-logo'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/img_service_status22.png' alt='' /></span>
										미미박스
									</td>
									<td>개발중</td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td class='end'><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
								</tr>
								<tr>
									<td class='inner-logo'>
										<span class='c-logo'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/img_service_status23.png' alt='' /></span>
										먼슬러
									</td>
									<td>개발중</td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td class='end'><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
								</tr>
								</tbody>
							</table>
						</div>
						<div id='tab-cont02' class='tab-cont'>
							<table class='status-table'>
								<colgroup>
									<col width='*' />
									<col width='14%' />
									<col width='11%' />
									<col width='11%' />
									<col width='11%' />
									<col width='11%' />
									<col width='11%' />
									<col width='11%' />
								</colgroup>
								<thead>
								<tr>
									<th scope='col' rowspan='2'>제휴몰 명</th>
									<th scope='col' rowspan='2'>서비스 상태</th>
									<th scope='col' colspan='6' class='end'>서비스 제공현황</th>
								</tr>
								<tr>
									<th scope='col'>상품등록</th>
									<th scope='col'>상품수정</th>
									<th scope='col'>주문수집</th>
									<th scope='col'>송장전송</th>
									<th scope='col'>클레임</th>
									<th scope='col' class='end'>문의/후기</th>
								</tr>
								</thead>
								<tbody>
								
								<tr>
									<td class='inner-logo'>
										<span class='c-logo'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/img_service_status02_09.png' alt='' /></span>
										elevenia
									</td>
									<td>서비스 이용가능</td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td class='end'><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
								</tr>
								<tr>
									<td class='inner-logo'>
										<span class='c-logo'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/img_service_status02_01.png' alt='' /></span>
										ensogo
									</td>
									<td>서비스 이용가능</td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon1'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status01.png' alt='' />연동가능</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td class='end'><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
								</tr>								
								<tr>
									<td class='inner-logo'>
										<span class='c-logo'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/img_service_status02_08.png' alt='' /></span>
										Lazada
									</td>
									<td>서비스 이용가능</td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td class='end'><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
								</tr>
								<tr>
									<td class='inner-logo'>
										<span class='c-logo'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/img_service_status02_02.png' alt='' /></span>
										amazon
									</td>
									<td>서비스 이용가능</td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td class='end'><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
								</tr>
								<tr>
									<td class='inner-logo'>
										<span class='c-logo'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/img_service_status02_03.png' alt='' /></span>
										melishou
									</td>
									<td>서비스 이용가능</td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td class='end'><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
								</tr>
								<tr>
									<td class='inner-logo'>
										<span class='c-logo'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/img_service_status02_04.png' alt='' /></span>
										Taobo
									</td>
									<td>서비스 이용가능</td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td class='end'><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
								</tr>
								<tr>
									<td class='inner-logo'>
										<span class='c-logo'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/img_service_status02_05.png' alt='' /></span>
										JD.com
									</td>
									<td>서비스 이용가능</td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td class='end'><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
								</tr>
								<tr>
									<td class='inner-logo'>
										<span class='c-logo'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/img_service_status02_06.png' alt='' /></span>
										Tmall
									</td>
									<td>서비스 이용가능</td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td class='end'><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
								</tr>
								<tr>
									<td class='inner-logo'>
										<span class='c-logo'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/img_service_status02_07.png' alt='' /></span>
										라쿠텐
									</td>
									<td>서비스 이용가능</td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
									<td class='end'><em class='icon3'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/bg_status03.png' alt='' />개발중</em></td>
								</tr>
								</tbody>
							</table>
						</div>
					</div>
					<script type='text/javascript'>
						$('.menu-tab a').on('click',function() {
							$(this).siblings().removeClass('active');
							$(this).addClass('active');
							$($(this).attr('href')).siblings().removeClass('active');
							$($(this).attr('href')).addClass('active');
							return false;
						})
					</script>
				</div>
				<div class='service-process'>
					<h2>서비스 절차</h2>
					<div class='process-img'>
						<img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/img_service_process.gif' alt='' />
					</div>
				</div>
				<div class='service-faq'>	
					<h2>자주하는 질문</h2>
					<ol>
						<li>
							<img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/img_service_faq01.png' alt='' class='faq-num' />
							<strong>마켓 통합관리 상품의 이용 요금이 있나요?</strong>
							중소형 비즈니스부터 대형 비즈니스를 위한 엔터프라이즈 솔루션 까지, 획기적인 매춯 향상ㄹ및 운영 효율성을 보장하는 국내 최고
							'이커머스 통합 소프트웨어 솔루션' 입니다. 
						</li>
						<li>
							<img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/img_service_faq02.png' alt='' class='faq-num' />
							<strong>오픈마켓 판매 시 수수료는 어떻게 되나요?</strong>
							오픈마켓, 종합몰 수준의 입점형 기능을 기반으로 재고관리/상품주문/배송/클레임/정산/회계 단계별 프로세스를 통합 부서별 업무
							최적화를 최우선으로 설계,개발 되었습니다.
						</li>
						<li>
							<img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/img_service_faq03.png' alt='' class='faq-num' />
							<strong>서비스 신청 후 언제부터 사용 할 수 있나요?</strong>
							기가 오피스는 기업 ICT 운영에 관련된 GiGA급 회선에서 네트위크 보안, 호스팅 상면, 관리 서비스와 모바일 VPN까지! 
							이 모든 것이 한번에 해결되는 토탈 기업 ICT 운영의 해결책 입니다.
						</li>
						<li>
							<img src='../v3/images/".$_SESSION["admininfo"]["language"]."/selling/img_service_faq04.png' alt='' class='faq-num' />
							<strong>서비스 약정 및 상품가격이 궁금합니다.</strong>
							기가 오피스는 기업 ICT 운영에 관련된 GiGA급 회선에서 네트위크 보안, 호스팅 상면, 관리 서비스와 모바일 VPN까지! 
							이 모든 것이 한번에 해결되는 토탈 기업 ICT 운영의 해결책 입니다.
						</li>
					</ol>
				</div>
			</div>	
";


$Contents = $contents;

$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = store_menu();
$P->strContents = $Contents;
$P->Navigation = "메인화면";
$P->TitleBool = false;
$P->ServiceInfoBool = false;
$P->ContentsWidth = "98%";
echo $P->PrintLayOut();

?>