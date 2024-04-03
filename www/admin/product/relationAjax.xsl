<?xml version='1.0'?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/TR/WD-xsl">
<xsl:template match="/">	
	<xsl:for-each select="relationProducts/products[pid]">	
	<div style='cursor:hand;' class='draggable'>		
		<xsl:attribute name="id">
		  <xsl:value-of select="pid"/>
		</xsl:attribute>
			<table bgcolor="#ffffff" width="100%" border="0">
				<xsl:attribute name="id">
				  <xsl:value-of select="tb_pid"/>
				</xsl:attribute>		
				<tr>
					<td width="30">
						<input type="checkbox" name="pid[]" id="cpid" >
						<xsl:attribute name="value">
						  <xsl:value-of select="pid"/>
						</xsl:attribute>
						</input>
					</td>
					<td width="60">						
						<img border="0" id="IMAGE" width="50" height="50">
						<xsl:attribute name="src">
						  <xsl:value-of select="img_src"/>
						</xsl:attribute>
						<xsl:attribute name="title">[<xsl:value-of select="pid"/>]<xsl:value-of select="pname"/></xsl:attribute>
						</img>
					</td>
					<td align="left">[<xsl:value-of select="brand_name"/>]<br/><xsl:value-of select="pname"/><br/><xsl:value-of select="sellprice"/></td>
				</tr>
				<tr height="1"><td colspan="5" background="../image/dot.gif"></td></tr>
			</table>
		</div>
	</xsl:for-each>
</xsl:template>
</xsl:stylesheet>