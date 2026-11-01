-- GAL Resource Library: link and crisis-line updates, 2026-11-01
--
-- Run once in phpMyAdmin on the live database (select the database > Import,
-- or paste into the SQL tab). Each UPDATE only changes a row that still has
-- its OLD link, so rows you've already edited are left alone, and running the
-- file twice changes nothing the second time.
--
-- Found with: php tools/check_links.php
-- Each new URL was opened and its page title checked (2026-11-01).

SET NAMES utf8mb4;
START TRANSACTION;

-- ---- Crisis line: the National Suicide Prevention Lifeline is now 988 ----
-- Source: https://988lifeline.org/get-help/ (call/text 988; Spanish: press 2 or text AYUDA to 988)
UPDATE resources SET
    name = '988 Suicide & Crisis Lifeline',
    link = 'https://988lifeline.org/',
    description = 'Call or text 988, or chat at 988lifeline.org. Free, confidential support 24/7 for anyone in suicidal crisis or emotional distress.'
WHERE id = 213 AND link = 'https://suicidepreventionlifeline.org/';

UPDATE resources SET
    name = 'Línea 988 de Prevención del Suicidio y Crisis',
    link = 'https://988lifeline.org/es/inicio/',
    description = 'Llame al 988 y presione 2, o envíe un texto con la palabra AYUDA al 988. Apoyo gratuito y confidencial 24/7 para cualquier persona en crisis de suicidio o aflicción emocional.'
WHERE id = 221 AND link = 'https://suicidepreventionlifeline.org/help-yourself/en-espanol/';

-- ---- Public Defender: old domain now points to an unrelated gambling site ----
UPDATE resources SET link = 'https://pd9.org/' WHERE id IN (136, 148) AND link = 'http://www.myfloridapd.com/';

-- ---- Renamed organizations (new link and name) ----
UPDATE resources SET name = 'FloridaCommerce (formerly the Department of Economic Opportunity)'
WHERE id = 74 AND link = 'https://www.floridajobs.org/';
UPDATE resources SET name = 'FloridaCommerce (antes Departamento de Oportunidad Económica de Florida)', link = 'https://www.floridajobs.org/'
WHERE id = 87 AND link = 'http://www.floridajobs.org/espanol';

UPDATE resources SET name = 'HCA Florida Osceola Hospital', link = 'https://www.hcafloridahealthcare.com/locations/osceola-hospital'
WHERE id IN (104, 118) AND link = 'https://osceolaregional.com/';

UPDATE resources SET name = 'Orlando Health St. Cloud Hospital', link = 'https://www.orlandohealth.com/facilities/st-cloud-hospital'
WHERE id IN (106, 120) AND link = 'https://www.stcloudregional.com/';

UPDATE resources SET name = 'Goodwill Industries of Central Florida', link = 'https://www.goodwillcfl.org/services/job-skills-programs/'
WHERE id IN (72, 85) AND link = 'https://goodwilljobconnection.org/';

UPDATE resources SET name = 'IDignity', link = 'https://idignity.org/'
WHERE id IN (191, 200) AND link = 'https://osceola.idignity.org/';

UPDATE resources SET name = 'The Best Cheap Car Insurance', link = 'https://www.nerdwallet.com/insurance/auto/cheapest-car-insurance'
WHERE id = 168 AND link = 'https://www.nerdwallet.com/blog/insurance/best-cheap-car-insurance/';
UPDATE resources SET name = 'El mejor seguro de autos más barato', link = 'https://www.nerdwallet.com/insurance/auto/cheapest-car-insurance'
WHERE id = 178 AND link = 'https://www.nerdwallet.com/blog/insurance/best-cheap-car-insurance/';

-- ---- Broken links (404 / dead domain / bad certificate) -> current pages ----
UPDATE resources SET link = 'https://www.myflfamilies.com/services/child-family/independent-living'
WHERE id IN (5, 13) AND link = 'https://www.myflfamilies.com/service-programs/independent-living/';
UPDATE resources SET link = 'https://www.myflfamilies.com/services/child-family/independent-living/resources-and-useful-links'
WHERE id IN (6, 14) AND link = 'https://www.myflfamilies.com/service-programs/independent-living/links.shtml';
UPDATE resources SET link = 'https://studentaid.gov/h/apply-for-aid/fafsa'
WHERE id = 19 AND link = 'https://studentaid.ed.gov/sa/fafsa';
UPDATE resources SET link = 'https://studentaid.gov/es/h/apply-for-aid/fafsa'
WHERE id = 32 AND link = 'https://studentaid.ed.gov/sa/es/fafsa';
UPDATE resources SET link = 'https://studentaid.gov/understand-aid/types'
WHERE id = 44 AND link = 'https://studentaid.ed.gov/sa/types';
UPDATE resources SET link = 'https://studentaid.gov/es/understand-aid/types'
WHERE id = 58 AND link = 'https://studentaid.ed.gov/sa/types';
UPDATE resources SET link = 'https://otech.osceolaschools.net/'
WHERE id IN (22, 35) AND link = 'https://otec.osceolaschools.net/';
UPDATE resources SET link = 'https://valenciacollege.edu/academics/accelerated-skills-training/'
WHERE id IN (23, 36) AND link = 'https://net1.valenciacollege.edu/continuing-education/accelerated-skills-training-programs/';
UPDATE resources SET link = 'https://www.myflfamilies.com/services/public-assistance'
WHERE id IN (46, 60) AND link = 'https://www.myflfamilies.com/service-programs/access/map.shtml';
UPDATE resources SET link = 'https://osceola.floridahealth.gov/programs-and-services/clinical-and-nutrition-services/wic/'
WHERE id IN (51, 65) AND link = 'http://osceola.floridahealth.gov/programs-and-services/clinical-and-nutrition-services/wic/index.html';
UPDATE resources SET link = 'https://consumidor.ftc.gov/articulos/informes-de-credito-gratuitos'
WHERE id = 61 AND link = 'https://www.consumidor.ftc.gov/articulos/s0155-informes-de-credito-gratuitos';
UPDATE resources SET link = 'https://www.mymoney.gov/es'
WHERE id = 67 AND link = 'https://www.mymoney.gov/es/Pages/default.aspx';
UPDATE resources SET link = 'https://www.investor.gov/informacion-en-espanol'
WHERE id = 70 AND link = 'https://www.investor.gov/espanol';
UPDATE resources SET link = 'https://www.kissimmee.gov/My-Government/Jobs'
WHERE id IN (79, 92) AND link = 'https://www.kissimmee.org/departments/human-resources-risk-management/employment-opportunities';
UPDATE resources SET link = 'https://osceola.floridahealth.gov/programs-and-services/infectious-disease-control/std/'
WHERE id IN (99, 113) AND link = 'http://osceola.floridahealth.gov/programs-and-services/infectious-disease-services/std/index.html';
UPDATE resources SET link = 'https://www.osceola.org/Services/Housing-Programs/Section-8'
WHERE id IN (125, 130) AND link = 'https://www.osceola.org/agencies-departments/human-services/housing/section-8-program/index.stml';
UPDATE resources SET link = 'https://www.osceola.org/Services/Corrections-and-Jail-Services'
WHERE id IN (143, 155) AND link = 'https://www.osceola.org/agencies-departments/corrections/index.stml';
UPDATE resources SET link = 'https://osceola.floridahealth.gov/certificates/'
WHERE id IN (196, 205) AND link = 'http://osceola.floridahealth.gov/certificates/index.html';

-- ---- Moved pages (old URL redirects; use the new address directly) ----
UPDATE resources SET link = 'https://gulfcoastjfcs.org/'
WHERE id IN (3, 11) AND link = 'https://gulfcoastjewishfamilyandcommunityservices.org/';
UPDATE resources SET link = 'https://www.usa.gov/help-with-utility-bills'
WHERE id = 45 AND link = 'https://www.usa.gov/help-with-bills';
UPDATE resources SET link = 'https://consumer.ftc.gov/articles/free-credit-reports'
WHERE id = 47 AND link = 'https://www.ftc.gov/faq/consumer-protection/get-my-free-credit-report';
UPDATE resources SET link = 'https://www.usa.gov/benefit-finder'
WHERE id = 49 AND link = 'https://www.benefits.gov/';
UPDATE resources SET link = 'https://www.usa.gov/es/buscador-beneficios'
WHERE id = 63 AND link = 'https://www.benefits.gov/es/';
UPDATE resources SET link = 'https://acf.gov/occ/contact-information/state-and-territory-child-care-and-development-fund-administrators'
WHERE id IN (50, 64) AND link = 'https://www.acf.hhs.gov/occ/resource/ccdf-grantee-state-and-territory-contacts';
UPDATE resources SET link = 'https://www.irs.gov/es'
WHERE id = 57 AND link = 'https://www.irs.gov/es/spanish';
UPDATE resources SET link = 'https://www.usa.gov/es/ayuda-pagar-facturas'
WHERE id = 59 AND link = 'https://www.usa.gov/espanol/pago-facturas';
UPDATE resources SET link = 'https://www.usa.gov/es/beneficios-gobierno'
WHERE id = 62 AND link = 'https://www.usa.gov/espanol/programas-sociales';
UPDATE resources SET link = 'https://www.usa.gov/unemployment-benefits'
WHERE id = 77 AND link = 'https://www.usa.gov/unemployment';
UPDATE resources SET link = 'https://www.usa.gov/es/beneficios-desempleo'
WHERE id = 90 AND link = 'https://www.usa.gov/espanol/desempleo';
UPDATE resources SET link = 'https://www.adventhealth.com/locations/hospitals/kissimmee'
WHERE id IN (105, 119) AND link = 'https://www.adventhealth.com/hospital/adventhealth-kissimmee';
UPDATE resources SET link = 'https://www.usa.gov/rent-help-groups'
WHERE id = 126 AND link = 'https://www.usa.gov/housing-help-audiences';
UPDATE resources SET link = 'https://www.usa.gov/es/ayuda-emergencia-vivienda'
WHERE id = 131 AND link = 'https://www.usa.gov/espanol/ayuda-asuntos-vivienda';
UPDATE resources SET link = 'https://www.fdc.myflorida.com/'
WHERE id IN (144, 156) AND link = 'http://www.dc.state.fl.us/';
UPDATE resources SET link = 'https://www.fbi.gov/how-we-can-help-you/online-safety/sex-offender-registry'
WHERE id IN (146, 158) AND link = 'https://www.fbi.gov/scams-and-safety/sex-offender-registry';
UPDATE resources SET link = 'https://www.consumerreports.org/cars/used-a6992848230/'
WHERE id IN (165, 175) AND link = 'https://www.consumerreports.org/buying-a-car/used-car-buying-guide/';
UPDATE resources SET link = 'https://www.consumerreports.org/cars/new-cars/buying-guide/'
WHERE id IN (166, 176) AND link = 'https://www.consumerreports.org/buying-a-car/new-car-buying-guide/';
UPDATE resources SET link = 'https://www.ssa.gov/es'
WHERE id = 206 AND link = 'https://www.ssa.gov/espanol/';
UPDATE resources SET link = 'https://guides.ocls.org/therightservice'
WHERE id IN (210, 218) AND link IN ('http://rightservicefl.org', 'http://rightservicefl.org/');
UPDATE resources SET link = 'https://www.hfuw.org/get-help/'
WHERE id IN (212, 220) AND link = 'https://www.hfuw.org/gethelp/';
UPDATE resources SET link = 'https://www.myflorida.gov/'
WHERE id IN (215, 223) AND link = 'http://www.myflorida.com/';

-- ---- Same address, but use https:// ----
UPDATE resources SET link = 'https://keystoindependencefl.com/'
WHERE id IN (7, 15) AND link = 'http://keystoindependencefl.com/';
UPDATE resources SET link = 'https://careersourcecentralflorida.com/'
WHERE id IN (71, 84) AND link = 'http://careersourcecentralflorida.com/';
UPDATE resources SET link = 'https://osceola.floridahealth.gov/'
WHERE id IN (102, 116) AND link = 'http://osceola.floridahealth.gov/';
UPDATE resources SET link = 'https://www.djj.state.fl.us/'
WHERE id IN (137, 149) AND link = 'http://www.djj.state.fl.us/';
UPDATE resources SET link = 'https://www.usareststops.com/'
WHERE id IN (182, 188) AND link = 'http://www.usareststops.com/';
UPDATE resources SET link = 'https://www.osceolalibrary.org/'
WHERE id IN (209, 217) AND link = 'http://www.osceolalibrary.org/';

COMMIT;
