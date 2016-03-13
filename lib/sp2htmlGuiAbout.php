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
* sp2htmlGuiAbout
*
* This library contains classes to display a simple about window
* @package sp2html
* @author Giuseppe Chiesa <gchiesa@smos.org>
* @version 1.0
* @abstract
* @copyright GNU Public License
*/

/**
 * sp2htmlGuiAbout
 * 
 * Class to display a simple prompt
 */
class sp2htmlGuiAbout {
   var $layout;
   var $handleWindow;
   
   function sp2htmlGuiAbout()
   {
      /* carico il file resource */
      // Gtk::rc_parse( GTK::rc_get_theme_dir() . "/" ."Blue". "/gtk/gtkrc");
      $this->layout = &new GladeXML('gui/windowAbout.glade');
   
      $this->handleWindow = $this->layout->get_widget("windowAbout");
      $this->handleWindow->set_modal(true);
      
      /* attivo i segnali principali */
      $this->handleWindow->connect("destroy", array(&$this, "destroy"));
      $this->handleWindow->connect("delete-event", array(&$this, "deleteEvent"));      

      /* carico il logo */
      $ctrl = $this->layout->get_widget("imageLogo");
      $ctrl->set_from_file("gui/logo.png");
      
      /* carico il numero di versione */
      $ctrl = $this->layout->get_widget("labelVersion");
      $ctrl->set_text(APPVERSION.' - Memory Usage: '.round(memory_get_usage()/1024/1024).'MB - Memory Peak: '.round(memory_get_peak_usage()/1024/1024).'MB');
   }
   
   function show()
   {
      $this->handleWindow->show();
      Gtk::main();
   }
   
   function destroy()
   {
      $this->handleWindow->destroy();
      Gtk::main_quit();
   }
   
   function deleteEvent()
   {
      return (false);
   }

} // END OF CLASS
?>      
