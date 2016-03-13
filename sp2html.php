<?
/*********************************************************
* Copyright 2007, 2007 - Giuseppe Chiesa
*
* This file is part of sp2html.
*
* spigax2html is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* sp2html is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with sp2html; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*
* Created by: Giuseppe Chiesa - http://gchiesa.smos.org
*
**
* sp2html
*
* This library contains gui for main dialog
* @package sp2html
* @author Giuseppe Chiesa <gchiesa@smos.org>
* @version 1.0
* @abstract
* @copyright GNU Public License
*/
error_reporting (E_ALL ^ E_WARNING ^ E_NOTICE);
// error_reporting (E_ALL);
ini_set('memory_limit', '256M');
ini_set('max_execution_time', 0); 

define("APPVERSION", "Versione 4.1.2");
define("SP2HTMLSTOP", "\r\n");

//Load GTK if not happened yet
// dl( "php_gtk." . ( strstr( PHP_OS, "WIN") ? "dll" : "so"));

require_once("lib/sp2htmlGuiMain.php");

// Parse personal gtkrc file
if(file_exists('etc/gtkrc')) {
   Gtk::rc_parse('etc/gtkrc');
}

$application = new sp2htmlGuiMain();
$application->start();
?>
