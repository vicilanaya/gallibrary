-- GAL Resource Library: courthouse, credit report and 2-1-1 text; https link, 2026-11-04
--
-- Run once in phpMyAdmin on the live database (SQL tab, or Import), after
-- 2026-11-01-links.sql and 2026-11-02-remaining-links.sql.
-- Each statement only changes a row that still has the expected current value,
-- so it's safe to run again and leaves other edits alone.

SET NAMES utf8mb4;
START TRANSACTION;

-- Ninth Circuit: the link now goes to the court's Resources page, not the
-- courthouse page, so name and describe it to match.
-- Source: https://ninthcircuit.org/resources
UPDATE resources SET
    name = 'Legal Resources, Ninth Judicial Circuit Court of Florida',
    description = 'Local, state and federal legal resources compiled by the court for Orange and Osceola counties, for attorneys and for people representing themselves in court.'
WHERE id = 139 AND link = 'https://ninthcircuit.org/resources';

UPDATE resources SET
    name = 'Recursos Legales, Noveno Circuito Judicial de Florida',
    description = 'Recursos legales locales, estatales y federales recopilados por la corte de los condados de Orange y Osceola, para abogados y para personas que se representan a sí mismas en la corte.'
WHERE id = 151 AND link = 'https://ninthcircuit.org/resources';

-- Free credit reports are now available weekly, not once every 12 months.
-- Source: https://www.annualcreditreport.com/ourmission.action
UPDATE resources SET
    name = 'Get your free credit reports',
    description = 'You can get free credit reports every week from each of the three nationwide credit reporting companies (Equifax, Experian and TransUnion) at AnnualCreditReport.com.'
WHERE id = 47 AND link = 'https://consumer.ftc.gov/articles/free-credit-reports';

UPDATE resources SET
    description = 'Puede obtener gratis cada semana sus informes de crédito de cada una de las tres compañías nacionales de informes de crédito (Equifax, Experian y TransUnion) en AnnualCreditReport.com.'
WHERE id = 61 AND link = 'https://consumidor.ftc.gov/articulos/informes-de-credito-gratuitos';

-- 2-1-1: call 211, text 898-211, or chat online.
-- Source: https://www.hfuw.org/get-help/
UPDATE resources SET
    description = 'Free help 24/7/365: call 211, text 898-211, or chat online at hfuw.org/chat. A 2-1-1 specialist can refer you to local help with food and housing, family support, health and wellness, and more.'
WHERE id = 212 AND link = 'https://www.hfuw.org/get-help/';

UPDATE resources SET
    description = 'Ayuda gratis 24/7/365: llame al 211, envíe un texto al 898-211 o converse en línea en hfuw.org/chat. Un especialista de 2-1-1 le puede referir a ayuda local con alimentos y vivienda, apoyo familiar, salud y bienestar, y más.'
WHERE id = 220 AND link = 'https://www.hfuw.org/get-help/';

-- Use https:// for the Florida Department of Education link.
-- (FATDEC, resources #18 and #31, stays http://: its https:// certificate is
-- invalid, so browsers would show a security warning.)
UPDATE resources SET link = 'https://www.fldoe.org/academics/career-adult-edu/dist-ps-instit.stml'
WHERE id IN (24, 37) AND link = 'http://www.fldoe.org/academics/career-adult-edu/dist-ps-instit.stml';

COMMIT;
