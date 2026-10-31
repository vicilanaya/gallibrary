# gallibrary

GAL Resource Library

Live site: https://gallibrary.rf.gd/ (hosted on InfinityFree).

The page is rendered by PHP (`index.php`) from a MySQL database. It used to be
`resources.xml` + `resources.xsl` rendered by the browser, but Chrome is
removing XSLT support. `resources.xml` is kept only as an archive of the
original content; the database is now the source of truth.

## Files

| File                                           | Purpose                                                                                                                                |
| ---------------------------------------------- | -------------------------------------------------------------------------------------------------------------------------------------- |
| `index.php`                                    | The page. Static text (intro, "What to do after high school?", footer) is in this file; sections and resources come from the database. |
| `inc/db.php`                                   | Database connection (PDO) and queries.                                                                                                 |
| `inc/html.php`                                 | Escaping (`h()`), link check (`safe_url()`), anchor ids.                                                                               |
| `resources.css`, `resources.js`, `logosvg.svg` | Styles, English/Spanish toggle, logo.                                                                                                  |
| `config.sample.php`                            | Template for `config.php` (database settings). `config.php` is git-ignored.                                                            |
| `schema.sql`                                   | Creates the two tables.                                                                                                                |
| `data.sql`                                     | Content exported from the local database, for the first import on the host.                                                            |
| `tools/`                                       | Command-line tools (import, export, check). Not uploaded to the host.                                                                  |
| `tests/run_tests.php`                          | Command-line tests. Not uploaded to the host.                                                                                          |
| `.htaccess`                                    | Makes `index.php` the home page and blocks private files.                                                                              |

## Database

Two tables. English and Spanish are separate rows, so names, descriptions and
links can differ per language.

`sections`

| Column       | Meaning                                                                                                                                                                                                                                                                    |
| ------------ | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `id`         | Automatic number.                                                                                                                                                                                                                                                          |
| `slug`       | Short id shared by both languages, lowercase `a-z`, `0-9`, `-` (e.g. `education`). Also used for the anchor: `#education` (English), `#education-es` (Spanish). Don't use `top`, `bottom`, `options`, `content`, `logo`, `wrapper`, `english`, `spanish`, `print` or `h1`. |
| `lang`       | `en` or `es`.                                                                                                                                                                                                                                                              |
| `name`       | Heading shown on the page (e.g. `Educación`).                                                                                                                                                                                                                              |
| `sort_order` | Order on the page, lower first. Give the English and Spanish rows the same number.                                                                                                                                                                                         |

`resources`

| Column        | Meaning                                                                                                         |
| ------------- | --------------------------------------------------------------------------------------------------------------- |
| `id`          | Automatic number.                                                                                               |
| `section_id`  | The `id` of the section row **in the same language**.                                                           |
| `name`        | Link text.                                                                                                      |
| `link`        | Full URL starting with `https://` or `http://` (`mailto:` and `tel:` also work). Anything else is shown as `#`. |
| `description` | Text under the link. May be empty.                                                                              |
| `sort_order`  | Order inside the section, lower first.                                                                          |

Imported rows use steps of 10 (10, 20, 30...) so you can put a new row in
between, e.g. `15`.

## Editing content (phpMyAdmin)

On InfinityFree: Control Panel > MySQL Databases > phpMyAdmin next to your database.

**Edit a resource:** open the `resources` table > Browse > Edit (pencil) on the
row > change the fields > Go. Remember the other language has its own row.

**Add a resource:**

1. Open `sections` > Browse and note the `id` of the section in each language
   (e.g. Education = `3` in English, `4` in Spanish).
2. Open `resources` > Insert. Leave `id` empty. Fill in `section_id`, `name`,
   `link`, `description` and `sort_order` > Go.
3. Insert the Spanish version with the Spanish section's `id`.

**Delete a resource:** `resources` > Browse > Delete on the row (and its other-language row).

**Add a section:** `sections` > Insert two rows with the same `slug` and
`sort_order`, one with `lang` = `en`, one with `lang` = `es`. Then add its
resources as above. It appears in the menu automatically.

**Delete a section:** deleting a section row also deletes all its resources
(foreign key cascade). Delete both language rows.

**Backups:** before big changes, phpMyAdmin > Export > Quick > SQL saves a copy.

Changes show on the site immediately (reload the page).

## Local development (XAMPP on Windows)

1. Install XAMPP to `C:\xampp`, add `C:\xampp\php` to PATH, and start Apache and MySQL in the XAMPP Control Panel.
2. Copy `config.sample.php` to `config.php` (the defaults match XAMPP).
3. Create the database and tables:
   ```powershell
   C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE gallibrary CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   C:\xampp\mysql\bin\mysql.exe -u root --default-character-set=utf8mb4 gallibrary -e "source schema.sql"
   ```
4. Load content, either from the live database export (phpMyAdmin > Export, then
   `source` that file) or the original XML:
   ```powershell
   php tools/import_xml.php            # --replace to wipe and re-import
   php tools/check_db.php              # shows connection and row counts
   php tests/run_tests.php             # compares the database with resources.xml and checks the page
   ```
   `run_tests.php` compares against `resources.xml`, so it only passes while the
   database still matches the XML.
5. Serve the project folder with Apache. Add this at the end of
   `C:\xampp\apache\conf\httpd.conf`, then Stop/Start Apache:
   ```apache
   Alias /gallibrary "C:/Users/toptr/OneDrive/.git/gallibrary"
   <Directory "C:/Users/toptr/OneDrive/.git/gallibrary">
       Options -Indexes
       AllowOverride All
       Require local
   </Directory>
   ```
   Open http://localhost/gallibrary/

## Deploying to InfinityFree

First time only:

1. Control Panel > MySQL Databases: create a database (e.g. `gallibrary`). Note
   the **MySQL host** (e.g. `sql123.infinityfree.com`), **database name**
   (e.g. `if0_12345678_gallibrary`), **username** and **password** shown there.
2. phpMyAdmin for that database > Import `schema.sql` > Go. Then Import `data.sql` > Go.
3. Create `config.php` from `config.sample.php` with the values from step 1.

Files to upload to `htdocs` (FTP or the online File Manager):
`index.php`, `config.php`, `inc/` (with its `.htaccess`), `resources.css`,
`resources.js`, `logosvg.svg`, `.htaccess`. Keep or delete the old
`resources.xml`/`resources.xsl` on the host; the `.htaccess` redirects them to the home page.
Do not upload `tools/`, `tests/`, `*.sql`, `README.md` or `.git`.

Later updates: upload only the changed files. Content changes are made in
phpMyAdmin and need no upload.

To refresh `data.sql` from the local database: `php tools/export_sql.php`.
