<?xml version='1.0'?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/TR/WD-xsl">
<xsl:template match="/">
<table cellpadding='0' cellspacing='0' width='100%' border='0'>
	<tr height="27">
		<td align='center'><b>우편번호</b></td>
		<td align='center' width="200"><b>주소</b></td>
		<td align='center' colspan="2"><b>비용(택배)</b></td>
		<td align='center' colspan="2"><b>비용(퀵서비스)</b></td>
		<td align='center' colspan="2"><b>비용(용달)</b></td>
	</tr>
	<tr height="1"><td colspan="8" background='/manage/image/dot.gif'></td></tr>
	<tr height="37" bgcolor='#efefef' >
		<td align='center' colspan='2'><span class="small"> <font color="#5B5B5B">검색하신 항목에 공통적으로 배송비를 입력하시려면<br/> 오른쪽 필드에 입력해주세요.</font></span></td>		
		<td align='center' ><input type="checkbox" name="all_edit" class='radio' id='all_edit'  value="0" onclick="fixAll(this.form)" title="전체수정"/></td>
		<td align='center' ><input type="text" name="bsend_cost_tekbae" class='textbox' id='btekbae_cost' size="10" value="0" onkeyup="changeTekbaeCost(this.value);" style='text-align:right'/><input type="hidden" id='tekbae_cost' /></td><td width="20"> 원</td>
		<td align='center' ><input type="text" name="bsend_cost_quick" class='textbox' id='bquick_cost' size="10" value="0" onkeyup="changeQuickCost(this.value);" style='text-align:right'/><input type="hidden" id='quick_cost' /></td><td width="20"> 원</td>
		<td align='center' ><input type="text" name="bsend_cost_truck" class='textbox' id='btruck_cost' size="10" value="0" onkeyup="changeTruckCost(this.value);" style='text-align:right'/><input type="hidden" id='truck_cost' /></td><td width="20"> 원</td>
	</tr>
	<tr height="1"><td colspan="8" background='/manage/image/dot.gif'></td></tr>
	<xsl:for-each select="zips/zip[zip_code]">
	<input type="hidden" class='textbox' id='zip' size="10" style='text-align:right'>
	<xsl:attribute name="name">zip_<xsl:value-of select="zip_ix"/></xsl:attribute>
		<xsl:attribute name="value">
		<xsl:value-of select="zip_code"/>
		</xsl:attribute>
	</input>
	<tr height='25'>
		<td align='center' bgcolor='#ffffff' style='color:#535353;font-weight:bold;'><xsl:value-of select="zip_code"/></td>
		<td align='left' style='padding-left:10px;'><xsl:value-of select="address"/></td>
		<td align='left' >
			<input type="checkbox" name='zipcode[]' id='edit_cost' >
				<xsl:attribute name="value">
				<xsl:value-of select="zip_ix"/>
				</xsl:attribute>
			</input>
		</td>
		<td bgcolor='#ffffff' align='center'  nowrap="true">			
			<input type="text" class='textbox' id='tekbae_cost' size="10" style='text-align:right'>
			<xsl:attribute name="name">send_cost_tekbae_<xsl:value-of select="zip_ix"/></xsl:attribute>
				<xsl:attribute name="value">
				<xsl:value-of select="send_cost_tekbae"/>
				</xsl:attribute>
			</input>
			<input type="hidden" class='textbox' size="10" style='text-align:right'>
			<xsl:attribute name="name">b_send_cost_tekbae_<xsl:value-of select="zip_ix"/></xsl:attribute>
				<xsl:attribute name="value">
				<xsl:value-of select="send_cost_tekbae"/>
				</xsl:attribute>
			</input>
		</td>
		<td width="20"> 원</td>
		<td bgcolor='#ffffff' align='center'  nowrap="true">
			<input type="text" class='textbox' id='quick_cost' size="10" style='text-align:right'>
			<xsl:attribute name="name">send_cost_quick_<xsl:value-of select="zip_ix"/></xsl:attribute>
			<xsl:attribute name="value">
			<xsl:value-of select="send_cost_quick"/>
			</xsl:attribute>
			</input>
			<input type="hidden" class='textbox' size="10" style='text-align:right'>
			<xsl:attribute name="name">b_send_cost_quick_<xsl:value-of select="zip_ix"/></xsl:attribute>
			<xsl:attribute name="value">
			<xsl:value-of select="send_cost_quick"/>
			</xsl:attribute>
			</input>
		</td>
		<td width="20"> 원</td>
		<td bgcolor='#ffffff' align='center'  nowrap="true">
			<input type="text" class='textbox' id='truck_cost' size="10" style='text-align:right'>
			<xsl:attribute name="name">send_cost_truck_<xsl:value-of select="zip_ix"/></xsl:attribute>
			<xsl:attribute name="value">
			<xsl:value-of select="send_cost_truck"/>
			</xsl:attribute>
			</input>
			<input type="hidden" class='textbox' size="10" style='text-align:right'>
			<xsl:attribute name="name">b_send_cost_truck_<xsl:value-of select="zip_ix"/></xsl:attribute>
			<xsl:attribute name="value">
			<xsl:value-of select="send_cost_truck"/>
			</xsl:attribute>
			</input>
		</td>
		<td width="20"> 원</td>
	</tr>
	<tr height='1'><td colspan='8' background='/manage/image/dot.gif'></td></tr>
	</xsl:for-each>
</table>
</xsl:template>
</xsl:stylesheet>