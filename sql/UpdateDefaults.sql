-- Upgrader SQL

-- Copyright (C) 2010 Aikilab

-- This program is free software: you can redistribute it and/or modify
-- it under the terms of the GNU Affero General Public License as
-- published by the Free Software Foundation, either version 3 of the
-- License, or (at your option) any later version.

-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU Affero General Public License for more details.

-- You should have received a copy of the GNU Affero General Public License
-- along with this program.  If not, see <http://www.gnu.org/licenses/>.

-- IMPORTANT in between each pair of SQL statements there must
-- exist a comment line with a precise series of dashes.


-- Here we delete records that will be inserted by insertDefauls.sql
-- to ensure overwritten of data. 

-- aiki_dictionary last id is 39
-- aiki_widget 28 is last id for oficial aiki widget.
-- aiki_forms id (1,4,6,9,12,13,17,18,20 are oficial
--
-- 

-- ------------------------------------------------------

DELETE FROM `aiki_dictionary` WHERE `term_id` < 39;

-- ------------------------------------------------------

DELETE FROM `aiki_forms` WHERE `id` IN (1,4,6,9,12,13,17,18,20);

-- ------------------------------------------------------

DELETE FROM `aiki_widgets` WHERE `id` < 28;

-- ------------------------------------------------------
