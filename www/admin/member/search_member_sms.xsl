<?xml version='1.0' ?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/TR/WD-xsl">
<xsl:template match="/">
<table cellpadding='0' cellspacing='0' width='100%'>
	<!--tr height='27'><td class='s_td  e_td' align='center' colspan='2'>search list</td></tr-->
	<xsl:for-each select="members/member[mem_code]">
	<tr height='25'>
		<td width='40' align='center' bgcolor='#efefef'><xsl:value-of select="mem_num"/></td>
		<td width='120' align='center' ><xsl:value-of select="mem_name"/></td>
		<td style='padding:0 5 0 5;' bgcolor='#efefef'><xsl:value-of select="mem_mobile"/></td>
	</tr>
	<tr height='1'><td colspan='3' background='../image/dot.gif'></td></tr>
	</xsl:for-each>
</table>
</xsl:template>
</xsl:stylesheet>