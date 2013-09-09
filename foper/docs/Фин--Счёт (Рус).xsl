<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"
  xmlns:fo="http://www.w3.org/1999/XSL/Format"
  xmlns:fox="http://xmlgraphics.apache.org/fop/extensions"
  xmlns:java="http://xml.apache.org/xslt/java" exclude-result-prefixes="java">

  <xsl:include href="http://localhost:7676/catalog/2013-06-20%2011%3A51%3A41/template/hyphenate.xsl" />

  <xsl:param name="page-count" select="0"/>
  <xsl:param name="signed" select="/request/Документ/@Подписанный"/>

  <xsl:variable name="host">http://localhost:7676</xsl:variable>
  <xsl:variable name="catalog"><xsl:value-of select="$host"/>/catalog/<xsl:value-of select="/request/Документ/Дата"/>/object/</xsl:variable>
  <xsl:variable name="lang">ru</xsl:variable>
  <xsl:variable name="company_id" select="'8'"/>
  <xsl:variable name="company" select="document( concat( $catalog, 'company.xml?O=', $company_id ) )/response/company[@id=$company_id]"/>
  <xsl:variable name="employees" select="document( concat( $catalog, 'employees.xml?O=', $company_id ) )/response/company[@id=$company_id]"/>

  <xsl:template match ="/">
    <fo:root>
      <fo:layout-master-set>

      <fo:simple-page-master master-name="СЧЕТ_page" page-width="21cm" page-height="29.7cm">
          <fo:region-body margin="7mm 7mm 15mm 7mm" />
          <fo:region-after extent="10mm"/>
        </fo:simple-page-master>

        <fo:page-sequence-master master-name="СЧЕТ">
          <fo:repeatable-page-master-alternatives>
            <fo:conditional-page-master-reference master-reference="СЧЕТ_page"  page-position="next" />
          </fo:repeatable-page-master-alternatives>
        </fo:page-sequence-master>
      </fo:layout-master-set>

      <fo:page-sequence master-reference="СЧЕТ">
        <fo:static-content flow-name="xsl-region-after"  font-family="Arial" font-size="6pt">
          <fo:block-container margin="0" padding="0">
            <fo:table>
              <fo:table-body>
                <fo:table-row>
                  <fo:table-cell padding="0 5mm 0 0"><fo:block text-align="center"><!--fo:inline color="black">Стр. <fo:page-number/></fo:inline> из <xsl:value-of select="$page-count"/--></fo:block></fo:table-cell>
                </fo:table-row>
              </fo:table-body>
            </fo:table>
          </fo:block-container>
        </fo:static-content>
        
        <fo:flow flow-name="xsl-region-body" font-family="Arial" font-size="10pt" >
          <fo:block-container margin="0" padding="0">
            <fo:table>
              <fo:table-body>
                <fo:table-row>
                  <fo:table-cell padding="0 0 0 0">
                    <xsl:call-template name="body"><xsl:with-param name="invoice" select="request/Документ" /></xsl:call-template>
                  </fo:table-cell>
                </fo:table-row>
              </fo:table-body>
            </fo:table>
          </fo:block-container>
        </fo:flow>
      </fo:page-sequence>

    </fo:root>
  </xsl:template>

  <xsl:template name="body">
    <xsl:param name="invoice"/>
    

    <xsl:choose>  
      <xsl:when test="$invoice/Услуги/@Валюта='RUB'">

      </xsl:when>
      <xsl:otherwise>
        
      </xsl:otherwise>
    </xsl:choose>

    <fo:block>
      <fo:block keep-with-previous="always" margin="0 2mm">
        <fo:table width="100%">
          <fo:table-column column-width="7em"/>
          <fo:table-column/>
          <fo:table-body>
            <fo:table-row>
              <fo:table-cell margin="0mm">
                <fo:block>Поставщик:</fo:block>
              </fo:table-cell>
              <fo:table-cell margin="0mm">
                <fo:block>
                  <fo:inline font-weight="bold">
                    <xsl:value-of select="$company/ownership/complete[@lang=$lang]/nominative"/> &#171;<xsl:value-of select="$company/name/complete[@lang=$lang]/nominative"/>&#187; (<xsl:value-of select="$company/ownership/short[@lang=$lang]/nominative"/> &#171;<xsl:value-of select="$company/name/short[@lang=$lang]/nominative"/>&#187;)
                  </fo:inline>
                </fo:block>
                <fo:block>Телефон: <xsl:value-of select="$company/Телефон[1]/Номер"/></fo:block>
                <fo:block>Факс: <xsl:value-of select="$company/Факс[1]/Номер"/></fo:block>
                <fo:block>

                  <fo:inline font-weight="bold">Банковские реквизиты:</fo:inline>
                  <fo:block>
                    <xsl:for-each select="$invoice/Договор/Поставщик/Реквизиты/Строка">
                      <xsl:if test="string-length(.)">
                        <fo:block><xsl:value-of select="."/></fo:block>
                      </xsl:if>
                    </xsl:for-each>
                  </fo:block>
                </fo:block>
                <xsl:if test="string-length( $company/КПП )"><fo:block><fo:inline font-weight="bold">КПП: </fo:inline><xsl:value-of select="$company/КПП"/></fo:block></xsl:if>
                <xsl:if test="string-length( $company/ИНН )"><fo:block><fo:inline font-weight="bold">ИНН: </fo:inline><xsl:value-of select="$company/ИНН"/></fo:block></xsl:if>				
                <xsl:if test="string-length( $company/ОКВЭД )"><fo:block><fo:inline font-weight="bold">Код по ОКВЭД: </fo:inline><xsl:value-of select="$company/ОКВЭД"/></fo:block></xsl:if>
                <xsl:if test="string-length( $company/ОКПО )"><fo:block><fo:inline font-weight="bold">Код по ОКПО: </fo:inline><xsl:value-of select="$company/ОКПО"/></fo:block></xsl:if>
                </fo:table-cell>
            </fo:table-row>
            <fo:table-row>
              <fo:table-cell margin="0mm">
                <fo:block margin-top="5mm" text-align="left">Плательщик:</fo:block>
              </fo:table-cell>
              <fo:table-cell margin="0mm">
                <fo:block margin-top="5mm">
                  <fo:inline font-weight="bold">
                    <fo:inline font-weight="bold"><xsl:value-of select="$invoice/Договор/Покупатель/Название"/></fo:inline>
                  </fo:inline>
                </fo:block>
                <xsl:if test="string-length( $invoice/Договор/Покупатель/Адрес/Юридический )"><fo:block>Адрес: <xsl:value-of select="$invoice/Договор/Покупатель/Адрес/Юридический"/></fo:block></xsl:if>
                <xsl:if test="string-length( $invoice/Договор/Покупатель/Телефон )"><fo:block>Телефон: <xsl:value-of select="$invoice/Договор/Покупатель/Телефон"/></fo:block></xsl:if>
                <xsl:if test="string-length( $invoice/Договор/Покупатель/Факс )"><fo:block>Факс: <xsl:value-of select="$invoice/Договор/Покупатель/Факс"/></fo:block></xsl:if>
                <xsl:if test="string-length( $invoice/Договор/Покупатель/ОКВЭД )"><fo:block>Код по ОКВЭД: <xsl:value-of select="$invoice/Договор/Покупатель/ОКВЭД"/></fo:block></xsl:if>
                <xsl:if test="string-length( $invoice/Договор/Покупатель/ОКПО )"><fo:block>Код по ОКПО: <xsl:value-of select="$invoice/Договор/Покупатель/ОКПО"/></fo:block></xsl:if>
                <xsl:if test="string-length( $invoice/Договор/Покупатель/КПП )"><fo:block>КПП: <xsl:value-of select="$invoice/Договор/Покупатель/КПП"/></fo:block></xsl:if>
                <xsl:if test="string-length( $invoice/Договор/Покупатель/ИНН )"><fo:block>ИНН: <xsl:value-of select="$invoice/Договор/Покупатель/ИНН"/></fo:block></xsl:if>
              </fo:table-cell>
            </fo:table-row>
          </fo:table-body>
        </fo:table>
      </fo:block>

      <fo:block text-indent="0pc" keep-with-previous="always" margin-top="10mm" text-align="justify" text-align-last="left">
        <xsl:attribute name="country"><xsl:value-of select="$lang"/></xsl:attribute>
        <xsl:attribute name="language"><xsl:value-of select="$lang"/></xsl:attribute>

        <fo:block keep-with-next="always" margin="1mm" text-align="center" text-align-last="center">
          <fo:block font-weight="bold" font-size="110%">СЧЁТ № <xsl:value-of select="$invoice/Счёт/Номер" /> от <xsl:value-of select="$invoice/Счёт/Дата" /></fo:block>
          <fo:block>на оплату по договору <xsl:value-of select="$invoice/Договор/Номер"/>/<xsl:value-of select="$invoice/Договор/Тип"/> от <xsl:value-of select="$invoice/Договор/Дата"/></fo:block>
        </fo:block>
        <fo:block keep-with-next="always" hyphenate="true">
          <fo:block keep-with-next="always" padding="0 2mm" text-align="end" text-align-last="end" >Валюта: <fo:inline><xsl:value-of select="$invoice/Услуги/@Валюта"/></fo:inline></fo:block>
          <fo:table border-collapse="collapse" margin-top="0mm">
            <fo:table-column/>
            <fo:table-column column-width="8em"/>

            <fo:table-header font-weight="bold" text-align="center" text-align-last="center" display-align="after">
              <fo:table-row keep-with-next="always">
                <fo:table-cell padding="2mm 5mm" border="0.2mm solid black" text-align="left" text-align-last="left" ><fo:block>Предмет счёта</fo:block></fo:table-cell>
                <fo:table-cell padding="2mm 5mm" border="0.2mm solid black" text-align="center" text-align-last="center"><fo:block>Сумма</fo:block></fo:table-cell>
              </fo:table-row>
            </fo:table-header>

            <fo:table-body text-align="center" text-align-last="center" margin="1mm" display-align="center">
              <xsl:for-each select="$invoice/Услуги/Услуга">
                <fo:table-row>
                  <fo:table-cell display-align="before" text-align="justify" text-align-last="start" hyphenate="true" border="0.2mm solid black">
                    <fo:block><xsl:apply-templates select="Название"/></fo:block>
                  </fo:table-cell>
                  <fo:table-cell border="0.2mm solid black" text-align="end" text-align-last="end">
                    <fo:block><xsl:call-template name="float_ru"><xsl:with-param name="number" select="Стоимость/Сумма"/></xsl:call-template> руб.</fo:block>
                  </fo:table-cell>
                </fo:table-row>
              </xsl:for-each>

              <fo:table-row keep-with-previous="always" margin="1mm">
                <fo:table-cell border="0.2mm solid black" text-align="justify" text-align-last="start" ><fo:block>НДС <xsl:value-of select="$invoice/Услуги/НДС/Ставка"/>%</fo:block></fo:table-cell>
                <fo:table-cell border="0.2mm solid black" text-align="end" text-align-last="end"><fo:block><xsl:call-template name="float_ru"><xsl:with-param name="number" select="$invoice/Услуги/НДС/Сумма"/></xsl:call-template> руб.</fo:block></fo:table-cell>
              </fo:table-row>
              <fo:table-row keep-with-previous="always" margin="1mm">
                <fo:table-cell border="0.2mm solid black" text-align="justify" text-align-last="start" ><fo:block>Итого с НДС для зачисления на личный счёт договора:</fo:block></fo:table-cell>
                <fo:table-cell border="0.2mm solid black" text-align="end" text-align-last="end"><fo:block><xsl:call-template name="float_ru"><xsl:with-param name="number" select="$invoice/Услуги/Итого/Сумма"/></xsl:call-template> руб.</fo:block></fo:table-cell>
              </fo:table-row>
              <fo:table-row keep-with-previous="always" margin="1mm">
                <fo:table-cell number-columns-spanned="2" border="0.2mm solid black" text-align="justify" text-align-last="start" >
                  <fo:block><fo:inline font-weight="bold">Итого к оплате: </fo:inline><xsl:call-template name="money"><xsl:with-param name="sum" select="$invoice/Услуги/Итого/Сумма"/></xsl:call-template></fo:block>
                </fo:table-cell>
              </fo:table-row>

            </fo:table-body>
          </fo:table>

        </fo:block>
        <fo:block keep-with-next="always">
          <fo:table border-collapse="collapse" margin-top="1pc"  keep-with-next="always">
            <fo:table-column/>
            <fo:table-body>

              <fo:table-row keep-with-next="always">
                <fo:table-cell margin="0 2mm" display-align="after">
                  <fo:block text-indent="0pc" keep-with-next="always">
                    <xsl:call-template name="stampplace">
                      <xsl:with-param name="role">
                        <xsl:call-template name="lower-case-but-first">
                          <xsl:with-param name="str" select="$employees/employee[@nicname='top']/position/complete[@lang=$lang]/nominative"/>
                        </xsl:call-template>
                        <xsl:value-of select="' '"/>
                        <xsl:value-of select="$company/ownership/short[@lang=$lang]/nominative"/> &#171;<xsl:value-of select="$company/name/short[@lang=$lang]/nominative"/>&#187;
                      </xsl:with-param>
                      <xsl:with-param name="person" select="$employees/employee[@nicname='top']/person/name/short[@lang=$lang]/nominative"/>
                      <xsl:with-param name="signature" select="$employees/employee[@nicname='top']/person/name/short[@lang=$lang]/nominative"/>
                      <xsl:with-param name="signature" select="$employees/employee[@nicname='top']/person/signature"/>
                    </xsl:call-template>
                  </fo:block>
                </fo:table-cell>
              </fo:table-row>

              <fo:table-row>
                <fo:table-cell margin="0 2mm" display-align="after">
                  <fo:block text-indent="0pc" keep-with-next="always">
                    <xsl:call-template name="stampplace">
                      <xsl:with-param name="role">
                        <xsl:call-template name="lower-case-but-first">
                          <xsl:with-param name="str" select="$employees/employee[@nicname='account']/position/complete[@lang=$lang]/nominative"/>
                        </xsl:call-template>
                      </xsl:with-param>
                      <xsl:with-param name="person" select="$employees/employee[@nicname='account']/person/name/short[@lang=$lang]/nominative"/>
                      <xsl:with-param name="signature" select="$employees/employee[@nicname='account']/person/signature"/>
                    </xsl:call-template>
                  </fo:block>
                </fo:table-cell>
              </fo:table-row>

              <fo:table-row>
                <fo:table-cell>
                  <fo:block keep-with-previous="always">
                    <xsl:if test="$signed = 2">
                      <fo:block-container padding-top="-30mm" margin-left="90mm" width="40mm">
                        <fo:block-container absolute-position="absolute" padding="0 0 0 0" margin="0 0 0 0" >
                          <xsl:attribute name="fox:transform">rotate(<xsl:value-of select="(java:java.lang.Math.random()*20)-10"/>)</xsl:attribute>
                          <fo:block>
                            <fo:external-graphic content-width="40mm">
                              <xsl:attribute name="src"><xsl:value-of select="$company/stamp/url"/></xsl:attribute>
                            </fo:external-graphic>
                          </fo:block>
                        </fo:block-container>
                      </fo:block-container>
                    </xsl:if>
                  </fo:block>
                </fo:table-cell>
              </fo:table-row>

            </fo:table-body>
          </fo:table>
        </fo:block>

      </fo:block>
    </fo:block>
  </xsl:template>

  <xsl:template name ="float_ru">
    <xsl:param name="number" select="0"/>

    <xsl:choose>
      <xsl:when test="$number = 0">
        <xsl:value-of select="'0,00'"/>
      </xsl:when>
      <xsl:when test="contains($number, '.')">
        <xsl:value-of select="substring-before($number,'.')"/>,<xsl:value-of select="substring-after($number,'.')"/>
      </xsl:when>
      <xsl:otherwise>
        <xsl:value-of select="$number"/>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>  

  <xsl:template name ="money">
    <xsl:param name="sum" select="0"/>
    <xsl:param name="currency" select="'RUB'"/>

    <xsl:call-template name="float_ru"><xsl:with-param name="number" select="$sum"/></xsl:call-template>&#160;руб.&#160;(<xsl:call-template name="lower-case-but-first">
      <xsl:with-param name="str">
        <xsl:call-template name="sum2text_ru"><xsl:with-param name="sum" select="format-number($sum,'0.00')"/></xsl:call-template>
      </xsl:with-param>
     </xsl:call-template>)
  </xsl:template>

  <xsl:template name ="sum2text_ru"><xsl:param name="sum" select="0.00"/><xsl:param name="gender" select="0"/><xsl:variable name="number2text" select="document(concat('http://localhost:7676/utils/function/number2text/', format-number($sum,'0.00'), '?gender=', $gender))/response/parts"/><xsl:value-of select="$number2text/integral/complete[@lang=$lang]/nominative"/>&#160;РУБ. <xsl:value-of select="$number2text/fractional/numeric"/>&#160;КОП.</xsl:template>
  <xsl:template name="lower-case-but-first"><xsl:param name="str" select="''"/><xsl:param name="up" select="'ABCDEFGHIJKLMNOPQRSTUVWXYZАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЬЫЪЭЮЯ'"/><xsl:param name="lw" select="'abcdefghijklmnopqrstuvwxyzабвгдеёжзийклмнопрстуфхцчшщьыъэюя'"/><xsl:value-of select="translate(substring( $str, 1, 1 ),$lw,$up)" /><xsl:value-of select="translate(substring( $str, 2, string-length($str) ),$up,$lw)" /></xsl:template>
  <xsl:template match="domainname"><fo:inline hyphenate="true" hyphenation-character="&#032;"><xsl:call-template name="hyphenate"><xsl:with-param name="string" select="." /></xsl:call-template></fo:inline></xsl:template>

  <xsl:template name="stampplace">
    <xsl:param name="role"/>
    <xsl:param name="person"/>
    <xsl:param name="signature"/>

      <fo:block keep-with-next="always">
        <fo:table>
          <fo:table-column/>
          <fo:table-column column-width="25mm"/>
          <fo:table-column column-width="12em"/>
          <fo:table-body>
            <fo:table-row keep-with-next="always">
              <fo:table-cell>
                <fo:block keep-with-next="always" text-align="start" text-align-last="start" margin-top="15mm">
                  <xsl:value-of select="$role"/>
                </fo:block>
              </fo:table-cell>
              <fo:table-cell text-align="end" text-align-last="end">
                <fo:block>
                  <xsl:if test="$signed = 1 or $signed = 2">
                    <fo:external-graphic content-width="25mm" padding-bottom="-5mm" padding-left="5mm">
                      <xsl:attribute name="src"><xsl:value-of select="$signature/url"/></xsl:attribute>
                    </fo:external-graphic>
                  </xsl:if>
                </fo:block>
              </fo:table-cell>
              <fo:table-cell>
                <fo:block keep-with-next="always" text-align="start" text-align-last="start" margin-top="15mm">
                  <xsl:value-of select="$person"/>
                </fo:block>
              </fo:table-cell>
            </fo:table-row>
          </fo:table-body> 
        </fo:table>
      </fo:block>
  </xsl:template>

  <xsl:template name="month-as-string">
    <xsl:param name="month"/>
    <xsl:param name="lang" select="'ru'"/>

    <xsl:if test="$lang = 'ru'">
      <xsl:if test="$month =  1">января</xsl:if>
      <xsl:if test="$month =  2">февраля</xsl:if>
      <xsl:if test="$month =  3">марта</xsl:if>
      <xsl:if test="$month =  4">апреля</xsl:if>
      <xsl:if test="$month =  5">мая</xsl:if>
      <xsl:if test="$month =  6">июня</xsl:if>
      <xsl:if test="$month =  7">июля</xsl:if>
      <xsl:if test="$month =  8">августа</xsl:if>
      <xsl:if test="$month =  9">сентября</xsl:if>
      <xsl:if test="$month = 10">октября</xsl:if>
      <xsl:if test="$month = 11">ноября</xsl:if>
      <xsl:if test="$month = 12">декабря</xsl:if>
    </xsl:if>
  </xsl:template>
</xsl:stylesheet>