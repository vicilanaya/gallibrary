-- GAL Resource Library: the last 5 broken links, 2026-11-02
-- (includes the FDLE and Ninth Circuit links chosen on 2026-11-03)
--
-- Run once in phpMyAdmin on the live database (SQL tab, or Import), after
-- updates/2026-11-01-links.sql.
-- Like the previous update, each statement only touches a row that still has
-- its OLD link, so it's safe to run again and leaves your own edits alone.
-- Each new URL was opened and its page title checked (2026-11-02).

SET NAMES utf8mb4;
START TRANSACTION;

-- 1. Legal Aid Society of Osceola: the program page is gone. The Osceola County
--    Bar Association now refers residents to Community Legal Services of
--    Mid-Florida, (407) 847-4866 (osceolabar.org/find-an-attorney.html).
UPDATE resources SET
    name = 'Community Legal Services of Mid-Florida',
    link = 'https://www.legalaccessforall.org/',
    description = 'No-cost civil legal help for low-income residents of Osceola County and Central Florida. Call (407) 847-4866.'
WHERE id = 135 AND link = 'https://osceolabar.org/laso/';

UPDATE resources SET
    name = 'Servicios Legales Comunitarios de Florida Central (Community Legal Services of Mid-Florida)',
    link = 'https://www.legalaccessforall.org/',
    description = 'Ayuda legal civil gratuita para residentes de bajos ingresos del condado de Osceola y Florida Central. Llame al (407) 847-4866.'
WHERE id = 147 AND link = 'https://osceolabar.org/laso/';

-- 2. FDLE Seal and Expunge: new page chosen by the site owner.
UPDATE resources SET link = 'https://www.fdle.state.fl.us/records-services/seal-expunge-records'
WHERE id IN (138, 150) AND link = 'https://www.fdle.state.fl.us/Seal-and-Expunge-Process/Seal-and-Expunge-Home.aspx';

-- 3. Osceola County Courthouse: renamed the Jon B. Morgan Osceola County
--    Courthouse; links to the Ninth Circuit's Resources page (site owner's choice).
UPDATE resources SET
    name = 'Jon B. Morgan Osceola County Courthouse',
    link = 'https://ninthcircuit.org/resources'
WHERE id = 139 AND link = 'https://www.ninthcircuit.org/about/courthouses/osceola-county-courthouse';

UPDATE resources SET
    name = 'Juzgado Jon B. Morgan del Condado de Osceola',
    link = 'https://ninthcircuit.org/resources'
WHERE id = 151 AND link = 'https://www.ninthcircuit.org/about/courthouses/osceola-county-courthouse';

-- 4. Florida Traffic Watch: the website no longer exists (domain gone). Removed.
DELETE FROM resources
WHERE id IN (140, 152) AND link = 'https://floridatrafficwatch.org/';

-- 5. Florida College System: the old site now redirects to the FCS Foundation,
--    and the "Online Programs" page is gone. Use the Florida Department of
--    Education's FCS page, and FloridaShines' statewide online course search.
UPDATE resources SET link = 'https://www.fldoe.org/schools/higher-ed/fl-college-system/'
WHERE id IN (26, 39) AND link = 'https://www.floridacollegesystem.com/';

UPDATE resources SET
    name = 'FloridaShines: Online Courses',
    link = 'https://www.floridashines.org/learn-online',
    description = 'Search online courses offered at Florida''s public colleges and universities.'
WHERE id = 27 AND link = 'https://www.floridacollegesystem.com/students/programs/online_programs.aspx';

UPDATE resources SET
    name = 'FloridaShines: Cursos en Línea',
    link = 'https://www.floridashines.org/learn-online',
    description = 'Busque cursos en línea ofrecidos por los colegios y universidades públicas de Florida.'
WHERE id = 40 AND link = 'https://www.floridacollegesystem.com/students/programs/online_programs.aspx';

-- 6. Links checked in a browser by the site owner (these sites block automated
--    checks). If you already ran this file before this section was added,
--    running it again only applies these.
UPDATE resources SET link = 'https://www.fldoe.org/academics/career-adult-edu/apprenticeship-programs/'
WHERE id IN (25, 38) AND link = 'http://www.fldoe.org/academics/career-adult-edu/apprenticeship-programs/apprenticeship-toolbox/various-links.stml';

-- The new link is the general Apprenticeship Programs page, so rename to match.
UPDATE resources SET name = 'Apprenticeship Programs'
WHERE id = 25 AND name = 'Apprenticeship Toolbox';
UPDATE resources SET name = 'Programas de Aprendizaje'
WHERE id = 38 AND name = 'Caja de herrameintas para aprendices';

UPDATE resources SET link = 'https://www.osceolasheriff.org/'
WHERE id = 154 AND link = 'https://www.osceolasheriff.org/#';

UPDATE resources SET link = 'https://www.voteosceola.gov/en-us/'
WHERE id = 192 AND link = 'https://www.voteosceola.com/en-us/Voter-Information/Check-Your-Voter-Status';

UPDATE resources SET link = 'https://www.voteosceola.gov/es-us/'
WHERE id = 201 AND link = 'https://www.voteosceola.com/es-us/Informacin-del-Elector/Verifique-Su-Estatus-Electoral';

UPDATE resources SET link = 'https://travel.state.gov/en/passports.html'
WHERE id = 198 AND link = 'https://travel.state.gov/content/travel/en/passports.html/';

UPDATE resources SET link = 'https://travel.state.gov/es/pasaportes.html'
WHERE id = 207 AND link = 'https://travel.state.gov/content/travel/es/pasaportes.html';

COMMIT;
