/* Create default database user for aiki

 * Copyright (C) 2010-2011 Aiki Lab Pte Ltd

 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.

 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.

 * Written by Steven Garcia
 * 
 * IMPORTANT in between each pair of SQL statements there must
 * exist a comment line with a precise series of dashes.
 * These are delimiters used by system/libraries/installer.php
 * For more info see SQL_DELIMIT in system/libraries/installer.php.
 */

CREATE USER '@DB_USER@'@'@DB_HOST@' IDENTIFIED BY '@DB_PASS@';
-- ------------------------------------------------------
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, ALTER
ON @DB_NAME@.* TO '@DB_USER@'@'@DB_HOST@';
