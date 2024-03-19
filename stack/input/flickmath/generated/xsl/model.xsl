<xsl:stylesheet 
  xmlns:mdf="http://www.mathdox.org/MathDox/Functions"
  xmlns:model='local:model' 
  xmlns:xs='http://www.w3.org/2001/XMLSchema'
  xmlns:xsl='http://www.w3.org/1999/XSL/Transform' 
  version='2.0'
>
  <xsl:include href='mdf.xsl'/>

  <xsl:output method='text' encoding='UTF-8' indent='no'/>

  <!-- default: copy -->
  <xsl:template match='*|/' mode='#all'>
    <xsl:apply-templates mode='#current'/>
  </xsl:template>

  <xsl:template match='text()' mode='#all'>
    <xsl:value-of select='.'/>
  </xsl:template>

  <!-- copy with attribute capitalize -->
  <xsl:template match='*' mode='copy' priority="2">
    <xsl:param name='capitalize'/>

    <xsl:apply-templates select='node()[1]' mode='#current'>
      <xsl:with-param name='capitalize' select='$capitalize'/>
    </xsl:apply-templates>
    <xsl:apply-templates select='node()[position() &gt; 1]' mode='#current'>
      <xsl:with-param name='capitalize' select='false'/>
    </xsl:apply-templates>
  </xsl:template>

  <xsl:template match='text()' mode='copy' priority="3">
    <xsl:param name='capitalize'/>

    <xsl:choose>
      <xsl:when test='$capitalize="true"'>
        <xsl:value-of select='mdf:capitalize(.)'/>
      </xsl:when>
      <xsl:otherwise>
        <xsl:value-of select='.'/>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>

  <!--
    process the list
  -->
  <xsl:template match='model:list'>
    <xsl:apply-templates>
      <xsl:with-param name='groupmember' as='node()' tunnel='yes'>
        <empty/>
      </xsl:with-param>
    </xsl:apply-templates>
  </xsl:template>

  <xsl:template match='model:group'>
    <xsl:result-document href='{@target}'>
      <xsl:apply-templates select='document(@model)' mode='group'>
        <xsl:with-param name='group' select='.' tunnel='yes' as='node()'/>
      </xsl:apply-templates>
    </xsl:result-document>
  </xsl:template>

  <!--
    check if the instance should be written to another file (default)
    or not (default inside a model:group)
  -->
  <xsl:template match='model:instance'>
    <xsl:param name='groupmember' tunnel='yes' as='node()'/>

    <xsl:result-document href='{@target}'>
      <xsl:apply-templates select='document(@model)' mode='model'>
	<xsl:with-param name='instance' select='.' tunnel='yes' as='node()'/>
      </xsl:apply-templates>
    </xsl:result-document>
  </xsl:template>

  <!-- 
    model:model, mode group, ignore
  -->
  <xsl:template match='model:model' mode='group'>
    <xsl:apply-templates mode='#current'/>
  </xsl:template>

  <!--
    model:group, mode group
  -->

  <xsl:template match='model:groupmember' mode='group'>
    <xsl:param name='group' as='node()' tunnel='yes'/>
    <xsl:variable name='this' as='node()' select='.'/>

    <xsl:for-each select='$group/model:instance'>
      <xsl:variable name='instance' as='node()' select='.'/>
      <xsl:apply-templates select='$this/node()' mode='model'>
        <xsl:with-param name='instance' tunnel='yes' as='node()' select='$instance'/>
        <xsl:with-param name='groupmember' select='$this' as='node()'/>
      </xsl:apply-templates>
    </xsl:for-each>
  </xsl:template>

  <!-- 
    mode: model, ignore the model:model container 
  -->
  <xsl:template match='model:model' mode='model'>
    <xsl:apply-templates mode='#current'/>
  </xsl:template>

  <!--
    mode: model, substitute the rest, using tunnel parameter instance (the
    instance from the auto.xml file, containing the substitutions)
  -->
  <xsl:template match='model:*' mode='model'>
    <xsl:param name='instance' as='node()' tunnel='yes'/>

    <xsl:variable name='local-name' select='local-name()'/>
    <xsl:choose>
      <xsl:when test='$instance/*[local-name()=$local-name]'>
        <xsl:apply-templates select='$instance/*[local-name()=$local-name]'
          mode="copy"
        >
          <xsl:with-param name='capitalize' select='@capitalize'/>
        </xsl:apply-templates>
      </xsl:when>
      <xsl:when test='@default'>
        <!-- using default value for the model -->
        <xsl:value-of select='@default'/>
      </xsl:when>
      <xsl:otherwise>
        <!-- no substitution found, produce a warning instead -->
        <xsl:message>
          <xsl:text>Could not find entry for </xsl:text>
          <xsl:value-of select='$local-name'/>
          <xsl:text>, when generating </xsl:text>
          <xsl:value-of select='$instance/@target'/>
          <xsl:text> from </xsl:text>
          <xsl:value-of select='$instance/@model'/>
          <xsl:text>.</xsl:text>
        </xsl:message>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>
</xsl:stylesheet>

