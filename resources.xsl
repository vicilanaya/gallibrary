<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!-- <resources> -->
	<!-- <section section="" lang=""> -->
		<!-- <sectionname section="" lang=""></sectionname> -->
		<!-- <resource lang=""> -->
			<!-- <name lang=""></name> -->
			<!-- <link lang=""></link> -->
			<!-- <description lang=""></description> -->
		<!-- </resource> -->
	<!-- </section> -->
<!-- </resources> -->


<!-- <xsl:template match="/"> -->
	<!-- <html> -->
	<!-- <body> -->

		<!-- <xsl:apply-templates select="/resources/section[@lang='es'] [@section='social']"/> -->

	<!-- </body> -->
	<!-- </html> -->
<!-- </xsl:template> -->





<xsl:template match="/">
	<html>
	<body>

		<xsl:for-each select="resources/section">
		
			<h2><xsl:value-of select="sectionname"/></h2>
		  
			<xsl:for-each select="resource">
				<dl>
					<dt><a href=""><xsl:value-of select="name"/></a></dt>
						<dd><xsl:value-of select="link"/></dd>
					<dd><xsl:value-of select="description"/></dd>
				</dl>
			</xsl:for-each>
			
		</xsl:for-each>
		

	</body>
	</html>
</xsl:template>





</xsl:stylesheet>