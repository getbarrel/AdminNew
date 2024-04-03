<?php 


include ($_SERVER ["DOCUMENT_ROOT"] . "/admin/openapi/cjmall/cjmall.class.php");
include ($_SERVER ["DOCUMENT_ROOT"] . "/class/layout.class");
require 'cjmall.config.php';

$db = new Database();
$call = new Call_cjmall();

$requestXmlBody = '<?xml version="1.0" encoding="UTF-8"?>
<tns:ifRequest xmlns:tns="http://www.example.org/ifpa" tns:ifId="IF_03_01" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.example.org/ifpa ../IF_03_01.xsd">
<tns:vendorId>437595</tns:vendorId>
<tns:vendorCertKey>CJ03234375950</tns:vendorCertKey>
<tns:good>
<tns:chnCls>30</tns:chnCls>
<tns:tGrpCd>50010101</tns:tGrpCd>
<tns:uniqBrandCd>43319000</tns:uniqBrandCd>
<tns:giftInd>Y</tns:giftInd>
<tns:uniqMkrNatCd>901</tns:uniqMkrNatCd>
<tns:uniqMkrCompCd>54498</tns:uniqMkrCompCd>
<tns:itemDesc>Bonnie Stripe Pony  </tns:itemDesc>
<tns:zLocalBolDesc>Bonnie Stripe Pony  </tns:zLocalBolDesc>
<tns:zlocalCcDesc>Bonnie Str</tns:zlocalCcDesc>
<tns:vatCode>S</tns:vatCode>
<tns:zDeliveryType>20</tns:zDeliveryType>
<tns:zShippingMethod>10</tns:zShippingMethod>
<tns:courier>11</tns:courier>
<tns:deliveryHomeCost>2500</tns:deliveryHomeCost>
<tns:zreturnNotReqInd>10</tns:zreturnNotReqInd>
<tns:zCostomMadeInd>N</tns:zCostomMadeInd>
<tns:stockMgntLevel>2</tns:stockMgntLevel>
<tns:leadtime>15</tns:leadtime>
<tns:lowpriceInd>N</tns:lowpriceInd>
<tns:delayShipRewardIind>N</tns:delayShipRewardIind>
<tns:reserveDayInd>Y</tns:reserveDayInd>
<tns:zContactSeqNo>5002</tns:zContactSeqNo>
<tns:zSupShipSeqNo>4000</tns:zSupShipSeqNo>
<tns:zReturnSeqNo>4000</tns:zReturnSeqNo>
<tns:zAsSupShipSeqNo>4000</tns:zAsSupShipSeqNo>
<tns:zAsReturnSeqNo>4000</tns:zAsReturnSeqNo>
<tns:unit>
<tns:unitNm>MULTICOLOR - ONESIZE</tns:unitNm>
<tns:unitRetail>82500</tns:unitRetail>
<tns:unitCost>65000</tns:unitCost>
<tns:availableQty>3000</tns:availableQty>
<tns:leadTime>03</tns:leadTime>
<tns:unitApplyRsn>20</tns:unitApplyRsn>
<tns:startSaleDt>2014-01-13</tns:startSaleDt>
<tns:endSaleDt>9999-12-30</tns:endSaleDt>
<tns:vpn>56017215</tns:vpn>
</tns:unit>
<tns:mallitem>
<tns:mallItemDesc>[Coach]Bonnie Stripe Pony  </tns:mallItemDesc>
<tns:keyword>엔조이뉴욕;뉴욕;해외쇼핑;구매대행;명품;헐리웃스타일;Coach</tns:keyword>
<tns:mallCtg>
<tns:mainInd>Y</tns:mainInd>
<tns:ctgName>155376</tns:ctgName>
</tns:mallCtg>
<tns:mallCtg>
<tns:mainInd>N</tns:mainInd>
<tns:ctgName>106252</tns:ctgName>
</tns:mallCtg>
</tns:mallitem>
<tns:goodsReport>
<tns:pedfId>91059</tns:pedfId>
<tns:html>
<![CDATA[ <script language="javascript" src="http://image.cjmall.com/common/jsCommon.js">
</script>
<div style="text-align:center;width:738px">
<img src="http://image.cjmall.com/prd/new2008/njoyny_notice_west.jpg" border="0" align="absmiddle"  usemap="#NjoyNY_Map1">
</div>
<map name="NjoyNY_Map1">
</map>
<br/>
<br/>[Coach] Bonnie Stripe Pony [98589]<br>
<br>
<li> Signature Coach Op Art and legacy stripe prints on imported silk<li> 2.1/2 (W) x 35 (L)<br>
<table width="600">
<tr>
<td>
</td>
<td>
</td>
</tr>
</table>]]>
</tns:html>
</tns:goodsReport>

	<tns:goodsReport>
	<tns:pedfId>25004</tns:pedfId>
	<tns:html>
	<![CDATA[[상품고시]]>
	</tns:html>
	</tns:goodsReport>

	<tns:goodsReport>
	<tns:pedfId>25005</tns:pedfId>
	<tns:html>
	<![CDATA[[상품고시]]>
	</tns:html>
	</tns:goodsReport>

	<tns:goodsReport>
	<tns:pedfId>25024</tns:pedfId>
	<tns:html>
	<![CDATA[[상품고시]]>
	</tns:html>
	</tns:goodsReport>

	<tns:goodsReport>
	<tns:pedfId>25154</tns:pedfId>
	<tns:html>
	<![CDATA[[상품고시]]>
	</tns:html>
	</tns:goodsReport>

	<tns:goodsReport>
	<tns:pedfId>25155</tns:pedfId>
	<tns:html>
	<![CDATA[[상품고시]]>
	</tns:html>
	</tns:goodsReport>
<tns:image>
<tns:imageMain>http://img.buynjoy.com/images_2009_1/automd/coach/200908/1250146500_main_550.jpg</tns:imageMain>
<tns:imageSub1>http://img.buynjoy.com/images_2009_1/automd/coach/200908/1250146500_main_550.jpg</tns:imageSub1>
</tns:image>
</tns:good>
</tns:ifRequest>';

$result = $call->call ( CJMALL_URL , $requestXmlBody );

print_r($result);