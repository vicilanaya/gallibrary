-- GAL Resource Library schema (MySQL 8 / MariaDB 10.x)
--
-- Run this ONCE on an empty database (locally or in InfinityFree phpMyAdmin:
-- select your database, then Import > schema.sql).
-- It intentionally has no CREATE DATABASE (InfinityFree database names have an
-- account prefix) and no DROP TABLE (so it can never wipe live data).

-- One row per section per language. English and Spanish rows share the same
-- slug and normally the same sort_order.
CREATE TABLE sections (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    slug       VARCHAR(50)  NOT NULL COMMENT 'lowercase a-z, 0-9, hyphen; e.g. education',
    lang       CHAR(2)      NOT NULL COMMENT 'en or es',
    name       VARCHAR(100) NOT NULL COMMENT 'heading shown on the page',
    sort_order INT          NOT NULL DEFAULT 0 COMMENT 'lower numbers first',
    PRIMARY KEY (id),
    UNIQUE KEY uq_sections_slug_lang (slug, lang),
    KEY idx_sections_sort (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- One row per resource per language. Links may differ between languages.
CREATE TABLE resources (
    id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    section_id  INT UNSIGNED  NOT NULL COMMENT 'sections.id (pick the section row of the same language)',
    name        VARCHAR(255)  NOT NULL,
    link        VARCHAR(2048) NOT NULL,
    description TEXT          NULL,
    sort_order  INT           NOT NULL DEFAULT 0 COMMENT 'lower numbers first within the section',
    PRIMARY KEY (id),
    KEY idx_resources_section_sort (section_id, sort_order),
    CONSTRAINT fk_resources_section
        FOREIGN KEY (section_id) REFERENCES sections (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
