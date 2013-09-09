<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"
  xmlns:fo="http://www.w3.org/1999/XSL/Format">

  <xsl:template match ="/">
    <fo:root>
      <fo:layout-master-set>
        <fo:simple-page-master
          master-name="job"
          page-width="21cm"
          page-height="29.7cm"
          margin="0cm 0cm 0cm 0cm">
  
          <fo:region-body/>
  
        </fo:simple-page-master>
      </fo:layout-master-set>
  
      <fo:page-sequence master-reference="job">
  
        <fo:flow flow-name="xsl-region-body" font-family="Arial">
          <fo:block>
            Hellow World!
          </fo:block>
        </fo:flow>
    
      </fo:page-sequence>
    </fo:root>
  </xsl:template>

</xsl:stylesheet>
