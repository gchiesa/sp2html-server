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
* sp2htmlGuiEditConfiguration
*
* This library contains gui for edit configuration dialog
* @package sp2html
* @author Giuseppe Chiesa <gchiesa@smos.org>
* @version 1.0
* @abstract
* @copyright GNU Public License
*/

require_once("lib/sp2htmlConf.php");
require_once("lib/sp2htmlGuiPrompt.php");

/**
 * sp2htmlGuiEditConfiguration
 *
 * class contains gui for main dialog
 */
class sp2htmlGuiEditConfiguration {
   
   var $layout;
   var $handleWindow;
   var $sp2htmlConf;
   var $flConfModified;
   var $listControl;
   var $listStoreControl;
  

   /**
    * sp2htmlGuiEditConfiguration() : 
    */
   function sp2htmlGuiEditConfiguration(&$sp2htmlConf)
   {
      /* azzero i flag */
      $this->flConfModified = false;
      
      /* assegno i pointers */
      $this->sp2htmlConf = $sp2htmlConf;
      
      /* carico il file resource */
      // Gtk::rc_parse( GTK::rc_get_theme_dir() . "/" ."Blue". "/gtk/gtkrc");
      $this->layout = new GladeXML('gui/windowEditConfiguration.glade');
      
      /* attivo i segnali principali della finestra */
      $this->handleWindow = $this->layout->get_widget("windowEditConfiguration");
      $this->handleWindow->connect("destroy", array(&$this, "destroy"));
      $this->handleWindow->connect("delete-event", array(&$this, "deleteEvent"));
            
      /* attivo i segnali dei pulsanti */
      $ctrl = $this->layout->get_widget("buttonAnnulla");
      $ctrl->connect("clicked", array(&$this, "destroy"));
      $ctrl = $this->layout->get_widget("buttonUpdate");
      $ctrl->connect("clicked", array(&$this, "updateList"));
      $ctrl = $this->layout->get_widget("buttonSaveConfiguration");
      $ctrl->connect("clicked", array(&$this, "saveConfiguration"));
      
      /* Creo l'oggetto listStore */
      $this->listStoreControl = new GtkListStore(GObject::TYPE_STRING, GObject::TYPE_STRING);
      
      /* prendo il puntatore al treeview */
      $this->listControl = $this->layout->get_widget("treeviewConfiguration");
      $this->listControl->set_model($this->listStoreControl);
   
      /* instanzio un cell renderer per la visualizzazione del testo */
      $cellRenderer = new GtkCellRendererText();
      
      /* creo gli oggetti colonna */
      $colData = new GtkTreeViewColumn('Chiave', $cellRenderer, 'text', 0);
      $colData->set_resizable(true);
      $colData->set_sort_column_id(1);
      $this->listControl->append_column($colData);
      $colEvento = new GtkTreeViewColumn('Valore', $cellRenderer, 'text', 1);
      $colEvento->set_resizable(true);
      $colEvento->set_sort_column_id(2);
      $this->listControl->append_column($colEvento);
      
      $this->listControl->columns_autosize();

      /* attivo i segnali sul controllo lista */
      $this->listControl->connect("row-activated", array(&$this, "editItemEnter"));

      /* carico dati della configurazione */
      $this->loadConfData();
   }
   
   /** 
    * loadConfDat() : this method load the conf in the list to permit editing
    */
   function loadConfData()
   {
      /* riordino l'array di conf */
      ksort($this->sp2htmlConf->confData, SORT_REGULAR);
      
      /* pulisco la lista */
      $this->listStoreControl->clear();

      /* riempio la lista */
      foreach($this->sp2htmlConf->confData as $key=>$value) {
         $this->listStoreControl->append(array($key, $value));
         $this->listControl->columns_autosize();
      }
   }
   
   /** 
    * function editItem() : this method handle the click on the list and edit the selected voice
    */
   function editItemEnter()
   {  
      /* creo un puntatore all'oggetto selection */
      $ctrlSelection = $this->listControl->get_selection();
      
      list($model, $iter) = $ctrlSelection->get_selected();

      /* aggiorno i campi chiave e valore */
      $ceditKey = $this->layout->get_widget("entryKey");
      $ceditValue = $this->layout->get_widget("entryValue");
      $ceditKey->set_text($model->get_value($iter, 0));
      $ceditValue->set_text($model->get_value($iter, 1));
      $ceditValue->select_region(0, -1);
      $ceditValue->grab_focus();
   }

   /**
    * updateList() : this method update the configuration list
    */
   function updateList()
   {
      $this->flConfModified = true;
      $ceditKey = $this->layout->get_widget("entryKey");
      $buff = $ceditKey->get_text();
      if($buff=="")
         return ;
      
      $ceditValue = $this->layout->get_widget("entryValue");
      $this->sp2htmlConf->confData[$ceditKey->get_text()] = $ceditValue->get_text();
      
      /* aggiorno la lista */
      $this->loadConfData();
   }
         
   /** 
    * saveConfiguration() : this method save the current configuration 
    */
   function saveConfiguration()
   {
      /* eseguo il backup */
      $ctrl = $this->layout->get_widget("checkbuttonBackup");
      if($ctrl->get_active()) {
         rename($this->sp2htmlConf->confData["file.conf"], $this->sp2htmlConf->confData["file.conf"].date("YmdHis", time()).".bak");
      }
      
      $fp = fopen($this->sp2htmlConf->confData["file.conf"], "wp");
      if($fp == NULL) {
         // prompt(
         return;
      }
      
      fwrite($fp, "### SP2HTML :: File Configurazione - Generato automaticamente da SP2HTMLServer ###\n#\n# le voci di configurazione sono chiave = valore\n#\n".SP2HTMLSTOP);
      foreach($this->sp2htmlConf->confData as $key=>$value) {
         if(trim($value)!="")
            fwrite($fp, trim($key)." = ".trim($value).SP2HTMLSTOP);
      }
      
      fclose($fp);  
      
      /* resetto il flag modificato */
      $this->flConfModified = false;
      
      $prompt = &new sp2htmlGuiPrompt(" La configurazione sara' attiva dal prossimo riavvio di Sp2HTML ", SP2HTMLMB_YES);
      $prompt->show();
   }
   
   /**
    * show() : this method show and start the gui 
    */
   function show()
   {  
      $this->handleWindow->show();
      Gtk::main();
   }
   
   
   function destroy()
   {
      $retValue = 0;
      if($this->flConfModified==true) {
         $prompt = &new sp2htmlGuiPrompt(" Attenzione la configurazione e' stata modificata, confermi l'uscita? ", SP2HTMLMB_YES | SP2HTML_NO);
         $prompt->show();
         if($prompt->returnMessage!=SP2HTMLMB_YES) {
            return;
         }
      }
      $this->flConfModified = false;
      $this->handleWindow->destroy();
      Gtk::main_quit();
   }
   
   function deleteEvent()
   {
      return (false);
   }
   
} // END OF CLASS

?>
