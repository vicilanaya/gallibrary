<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">



<xsl:template match="/">
	<html>
	<body>

		<xsl:apply-templates select="/resources/section[@lang='es'] [@section='social']"/>

	</body>
	</html>
</xsl:template>








</xsl:stylesheet>