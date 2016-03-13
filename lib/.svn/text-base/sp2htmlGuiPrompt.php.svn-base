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
* sp2htmlGuiPrompt
*
* This library contains classes to display a simple prompt alert
* @package sp2html
* @author Giuseppe Chiesa <gchiesa@smos.org>
* @version 1.0
* @abstract
* @copyright GNU Public License
*/

define("SP2HTMLMB_YES", "1");
define("SP2HTMLMB_NO", "2");
define("SP2HTMLMB_CANCEL", "4");

/**
 * sp2htmlGuiPrompt
 * 
 * Class to display a simple prompt
 */
class sp2htmlGuiPrompt {
   
   var $handleWindow;
   var $layout;
   var $returnMessage;
   
   
   function sp2htmlGuiPrompt($promptText, $optionButton)
   {
      /* azzero valori */ 
      $this->returnMessage = -1;
      
      /* carico il file resource */
      // Gtk::rc_parse( GTK::rc_get_theme_dir() . "/" ."Blue". "/gtk/gtkrc");
      $this->layout = new GladeXML('gui/windowPrompt.glade');
   
      $this->handleWindow = $this->layout->get_widget("windowPrompt");
      $this->handleWindow->set_title("Sp2HTML - Informazione");
      $this->handleWindow->set_modal(true);
      
      /* attivo i segnali principali */
      $this->handleWindow->connect("destroy", array(&$this, "destroy"));
      $this->handleWindow->connect("delete-event", array(&$this, "deleteEvent"));

      /* creo l'etichetta di testo */
      $ctrl = $this->layout->get_widget("labelText");
      $ctrl->set_text($promptText);
      
      if($optionButton & SP2HTMLMB_CANCEL) {
         $ctrl = $this->layout->get_widget("buttonCancel");
         $ctrl->connect("clicked", array(&$this, "destroy"));
         $ctrl->show();
      }
      if($optionButton & SP2HTMLMB_NO) {
         $ctrl = $this->layout->get_widget("buttonNo");
         $ctrl->connect("clicked", array(&$this, "closeMessageBox"), SP2HTMLMB_NO);
         $ctrl->show();
      }
      if($optionButton & SP2HTMLMB_YES) {
         $ctrl = $this->layout->get_widget("buttonYes");
         $ctrl->connect("clicked", array(&$this, "closeMessageBox"), SP2HTMLMB_YES);
         
         /* se SP2HTMLMB_CANCEL non è attivata allora è un pulsante di OK, ovvero un alert */
         if(!($optionButton & SP2HTMLMB_CANCEL) && !($optionButton & SP2HTMLMB_NO)) {
            $child = $ctrl->children();
            $child[0]->set_text(" OK ");
         }
         $ctrl->show();
      }
   }     
   
   function closeMessageBox($obj, $returnValue)
   {
      $this->returnMessage = $returnValue;
      $this->destroy();
   }
   
   function show()
   {
      $this->handleWindow->show();
      Gtk::main();
      return ($this->returnMessage);
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
