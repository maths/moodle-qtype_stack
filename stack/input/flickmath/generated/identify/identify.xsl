<xsl:stylesheet 
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
  version="2.0"
>
  <xsl:output method="text" encoding="UTF-8"/>

  <xsl:param name='filedir'>
    <xsl:text>.</xsl:text>
  </xsl:param>
	
  <xsl:template match="/*">
    <xsl:text>/*
 * This file has been generated automatically. 
 *
 * DO NOT EDIT
 *
 * Instead look for the $identify functions which are placed at the top 
 * of each file and indicate which file should be edited instead.
 */
</xsl:text>
    <xsl:apply-templates mode='contains'/>
    <xsl:text>$main( function() {} );
</xsl:text>
    <xsl:apply-templates/>
  </xsl:template>

  <xsl:template match="identify" mode='contains'>
    <xsl:text>$contains("</xsl:text>
    <xsl:value-of select='@file'/>
    <xsl:text>");
</xsl:text>
  </xsl:template>

  <xsl:template match="identify">
    <xsl:value-of select='unparsed-text(concat($filedir,"/",@file))'/>
  </xsl:template>

</xsl:stylesheet>

