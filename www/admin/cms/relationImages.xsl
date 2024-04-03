<?xml version='1.0'?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/TR/WD-xsl">
<xsl:template match="/">	
	<xsl:for-each select="relationImages/images[di_ix]">	
	<div style='cursor:hand;' class='draggable' >		
		<xsl:attribute name="id">
		  <xsl:value-of select="di_ix"/>
		</xsl:attribute>
			<table bgcolor="#ffffff" width="100%" border="0" height="75">
				<xsl:attribute name="id">
				  <xsl:value-of select="tb_di_ix"/>
				</xsl:attribute>		
				<tr style="url(../images/dot.gif) repeat-x bottom">
					<td width="30" >
						<input type="checkbox" name="di_ix[]" id="cdi_ix" >
						<xsl:attribute name="value">
						  <xsl:value-of select="di_ix"/>
						</xsl:attribute>
						</input>
					</td>
					<td width="60" style="url(../images/dot.gif) repeat-x bottom">					
						<img border="0" id="IMAGE" width="50" >
						<xsl:attribute name="src">
						  <xsl:value-of select="img_src"/>
						</xsl:attribute>
						<xsl:attribute name="title">[<xsl:value-of select="di_ix"/>]<xsl:value-of select="image_name"/></xsl:attribute>
						</img>
					</td>
					<td align="left" style="url(../images/dot.gif) repeat-x bottom"><xsl:value-of select="image_name"/><br/></td>
				</tr>
				
			</table>
		</div>
	</xsl:for-each>
</xsl:template>
</xsl:stylesheet>
