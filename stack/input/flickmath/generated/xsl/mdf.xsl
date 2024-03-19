<xsl:stylesheet 
  xmlns:mdf="http://www.mathdox.org/MathDox/Functions"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
  version="2.0"
>

  <!-- capitalize a string -->
  <xsl:function name="mdf:capitalize">
    <xsl:param name="string"/>
    <xsl:value-of select="upper-case(substring($string,1,1))"/>
    <xsl:value-of select="substring($string,2)"/>
  </xsl:function>

</xsl:stylesheet>

