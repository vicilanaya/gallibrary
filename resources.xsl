<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="/">
	<!-- <!DOCTYPE html/> -->
	<html>
	<head>
		
		<title>GAL Resource Library | Guardian ad Litem</title>
	
		<meta name="description" content="Resources for Foster Children in Transition to Adulthood in Osceola County, FL"/>
		<meta name="author" content="Florence Vicil Anaya"/>
		<meta name="author" content="Guardian ad Litem, Osceola, FL"/>
		<meta charset="utf-8"/>
		<meta name="robots" content="Homepage"/>
		<meta name="robots" content="index, follow"/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
		
		<link rel="stylesheet" type="text/css" href="resources.css" />
		<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
		<script src="resources.js"></script>
		
		<!--https://www.favicon-generator.org/	-->
		<!-- <link rel="shortcut icon" href="images/flag-guayama-favicon.gif" type="image/x-icon"/> -->
		<!-- <link rel="icon" href="images/flag-guayama-favicon.gif" type="image/x-icon"/> -->
		
		<!-- á	&#225;	é	&#233;	í	&#237;	ó	&#243;	ú	&#250;	ñ	&#241;	¡	&#161;	¿	&#191; -->
		
	</head>
	<body>
	<div id="wrapper">
	
	
<!-- options --><!-- options --><!-- options --><!-- options --><!-- options --><!-- options --><!-- options -->
	<div id="options">
		<ul>
			<li><a id="english" href="">[English]</a></li>
			<li><a id="spanish" href="">[Español]</a></li><br class="break"/>
			<li lang="en"><a href="#top">[Go to Top]</a></li>
			<li lang="es"><a href="#top">[Ir arriba]</a></li>
			<li lang="en"><a href="#bottom">[Go to Bottom]</a></li>
			<li lang="es"><a href="#bottom">[Ir abajo]</a></li><br class="break"/>
			<li><a id="print" href=""><span lang="en">[Print]</span><span lang="es">[Imprimir]</span></a></li>
		</ul>
	</div>
	


<!-- header --><!-- header --><!-- header --><!-- header --><!-- header --><!-- header --><!-- header --><!-- header -->
	<header id="top">
		<div id="logo"><img src="logosvg.svg" alt="Guardian ad litem logo" /></div>
		<h1 id="h1">GAL Resource Library</h1>
	</header>
	
	

<!-- main --><!-- main --><!-- main --><!-- main --><!-- main --><!-- main --><!-- main --><!-- main --><!-- main -->
	<main>
		<nav>
			<!-- dyamically generate nav list -->
			<ul>
				<xsl:for-each select="resources/section">
					<li>
						<xsl:attribute name="lang"><xsl:value-of select="lang"/></xsl:attribute>
						<a>
							<xsl:attribute name="href">#<xsl:value-of select="sectionname"/></xsl:attribute>
							<xsl:value-of select="sectionname"/>
						</a>
					</li>
				</xsl:for-each>
			</ul>
		</nav>
	
	
		<div id="content">
		<section class="intro">
			<p lang="en">The <span class="gal">GAL Resource Library</span> website is a list of resources helpful to minors that are in the state of Florida’s shelter system in Osceola County and are transitioning out of the program into adulthood. The goal of this website is to make available these resources to those who will take advantage of them the most: the children, their caretakers, and young adults after transitioning.</p>
			
			<p lang="en">Your local GAL office: <a href="tel:407-742-6655">407-742-6655</a>, <a href="https://goo.gl/maps/chMJ9fKULdJar6UQ9">3 Courthouse Square, Suite 100, Kissimmee, FL 34741</a></p>
			
			<p lang="en" class="disclaimer"><strong>DISCLAIMER</strong>&#58; This website is for informational purposes only. Inclusion or mention of a resource in this website does not constitute legal or other advice or endorsement by the <a href="https://galosceola.org/" target="_blank">Guardian ad Litem Foundation of Osceola County</a>, the <a href="https://flgal.org/" target="_blank">Florida Guardian ad Litem Foundation</a> or the <a href="https://guardianadlitem.org/" target="_blank">Florida Guardian ad Litem Program</a>.</p>
						
			<p lang="es">El sitio web <span class="gal"><em>GAL Resource Library</em></span> es una lista de recursos útiles para menores en el sistema de albergue de Florida en el condado de Osceola y que están en el proceso de transición hacia la adultez. La meta de este sitio web es hacer disponibles estos recursos a quienes los van a aprovechar más: los menores, sus cuidadores y adultos jóvenes después de la transición.</p>
			
			<p lang="es">Su officina de GAL local: <a href="tel:407-742-6655">407-742-6655</a>, <a href="https://goo.gl/maps/chMJ9fKULdJar6UQ9">3 Courthouse Square, Suite 100, Kissimmee, FL 34741</a></p>
			
			<p lang="es" class="disclaimer"><strong>DESCARGO DE RESPONSABILIDAD</strong>&#58; Este sitio web es para propósitos informativos solamente. La inclusión o mención de algún recurso en este sitio web no constituye consejo legal o de algún otro tipo ni aprobación por parte de la <a href="https://galosceola.org/" target="_blank">Foundación Guardian ad Litem del Condado de Osceola</a>, la <a href="https://flgal.org/" target="_blank">Foundación Guardian ad Litem de Florida</a> o el <a href="https://guardianadlitem.org/" target="_blank">Programa Guardian ad Litem de Florida</a>.</p>
		</section>
	
	
		<section lang="en">
			<h2>What to do after high school?</h2>
						
			<ul>
				<li>Learn a profession or trade by earning an <a href="#Education">academic degree or certificate</a></li>
				<li>Get <a href="#Employment">a job</a></li>
				<li>Join the <a href="https://www.usa.gov/join-military">military</a></li>
					<ul>
						<li>Other uniformed services mostly require a degree:</li>
							<li><a href="https://www.usphs.gov/">US Public Health Service</a></li>
							<li><a href="https://www.noaa.gov/">National Oceanic and Atmospheric Administration</a></li>
					</ul>
				<li>Do an apprenticeship:</li>
					<ul>
						<li><a href="https://apprenticeflorida.com/">In Florida</a></li>
						<li><a href="https://www.apprenticeship.gov/">Outside of Florida</a></li>
					</ul>
				<li>Volunteer:</li>
					<ul>
						<li><a href="https://www.volunteerflorida.org/">Volunteer Florida</a></li>
						<li><a href="https://www.nationalservice.gov/">Corporation for National and Community Service</a></li>
						<li><a href="https://www.peacecorps.gov/">Peace Corps</a></li>
					</ul>
				<li>Start a <a href="https://www.sba.gov/">business</a></li>
				<li><a href="#Travel">Travel</a></li>
			</ul>
		</section>
		
		
		<section lang="es">
			<h2>¿Qué se puede hacer después de la escuela superior?</h2>
						
			<ul>
				<li>Aprender una profesión u oficio obteniendo un <a href="#Educación">grado académico o un certificado</a></li>
				<li>Obtener <a href="#Empleo">empleo</a></li>
				<li>Entrar a las <a href="https://www.usa.gov/espanol/enlistarse-fuerzas-armadas">fuerzas armadas</a></li>
					<ul>
						<li>Otros servicios uniformados mayormente requieren un grado:</li>
							<li><a href="https://www.usphs.gov/">US Public Health Service</a></li>
							<li><a href="https://www.noaa.gov/">National Oceanic and Atmospheric Administration</a></li>
					</ul>
				<li>Trabajar como aprendiz:</li>
					<ul>
						<li><a href="https://apprenticeflorida.com/">En Florida</a></li>
						<li><a href="https://www.apprenticeship.gov/">Fuera de Florida</a></li>
					</ul>
				<li>Trabajar como voluntario:</li>
					<ul>
						<li><a href="https://www.volunteerflorida.org/">Volunteer Florida</a></li>
						<li><a href="https://www.nationalservice.gov/">Corporación para el Servicio Nacional y Comunitario</a></li>
						<li><a href="https://www.peacecorps.gov/">Cuerpos de Paz</a></li>
					</ul>
				<li>Comenzar un <a href="https://www.sba.gov/?lang=es">negocio</a></li>
				<li><a href="#Viajar">Viajar</a></li>
			</ul>
		</section>
		
	
		<!-- dynamically generate sections and definition lists -->
		<xsl:for-each select="resources/section">
		
			<section>
				<xsl:attribute name="lang"><xsl:value-of select="lang"/></xsl:attribute>
			
				<h2>
					<xsl:attribute name="id"><xsl:value-of select="sectionname"/></xsl:attribute>
					<xsl:value-of select="sectionname"/>
				</h2>
			  
				<xsl:for-each select="resource">
					<dl>
						<dt><a target="_blank">
							<xsl:attribute name="href"><xsl:value-of select="link"/></xsl:attribute>
							<xsl:value-of select="name"/></a></dt>
						<dd><xsl:value-of select="description"/></dd>
					</dl>
				</xsl:for-each>
				
			</section>
			
		</xsl:for-each>
		</div>
	</main>
	


<!-- footer --><!-- footer --><!-- footer --><!-- footer --><!-- footer --><!-- footer --><!-- footer --><!-- footer -->
		<footer id="bottom">
			
			<p lang="en">This website was created for the Guardian ad Litem Program of Osceola County, FL, (9th Judicial Circuit) by Florence Vicil Anaya.</p>
			
			<p lang="es">Este sitio web fue creado para el Programa Guardian ad Litem del Condado de Osceola, FL, (9no Circuito Judicial) por Florence Vicil Anaya.</p>
			
			<p><span lang="en">Web Development</span><span lang="es">Desarrollo Web</span>&#58; <a href="mailto:vicilanaya@gmail.com">vicilanaya@gmail.com</a></p>
			
			<!-- <p>Date last modified: <?php echo date('j F Y H:i', getlastmod()); ?></p> -->
			<!--#config timefmt="%d %B %Y" --><!--#echo var="LAST_MODIFIED" -->
			<!-- <script> -->
						<!-- var x = document.lastModified; -->
						<!-- document.write(x) -->
					<!-- </script> -->
			
			<!-- <p>Copyright &copy; 2020-<script> -->
				<!-- var date = new Date(); -->
				<!-- var year = date.getFullYear(); -->
				<!-- document.write(year);</script> All Rights Reserved</p> -->
				
		</footer>
		
	</div><!-- end wrapper -->			
	</body>
	</html>
</xsl:template>


</xsl:stylesheet>