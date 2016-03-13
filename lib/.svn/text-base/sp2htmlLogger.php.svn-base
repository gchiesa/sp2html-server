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
* sp2htmlLogger
*
* This library contains class logger hander
* @package sp2html
* @author Giuseppe Chiesa <gchiesa@smos.org>
* @version 1.0
* @abstract
* @copyright GNU Public License
*/

define("SP2HTMLLOGGER_STOP", "\r\n");

/**
 * sp2htmlLogger
 *
 * class contains gui for main dialog
 */
class sp2htmlLogger {
   var $listControl;
   var $listStoreControl;
   var $logFp;
   var $logVerbose;
   var $windowMain;

   /** 
    * sp2htmlLogger(&$listControl) : this constructor implements the logger handler
    * @param object $listControl pointer to a listControl gtk object
    */
   function sp2htmlLogger(&$listControl, &$windowMain, &$sp2htmlConf)
   {  
      /* istanzio l'oggetto liststore */
      $this->listStoreControl = new GtkListStore(GObject::TYPE_STRING, GObject::TYPE_STRING);
      $this->listStoreControl->append(array(date("d.m.Y-H:i:s",time()), " --LOGGER START--"));
      
      /* prendo il puntatore al treeView */
      $this->listControl = $listControl;
      $this->listControl->set_model($this->listStoreControl);
      
      /* instanzio un cell renderer per la visualizzazione del testo */
      $cellRenderer = new GtkCellRendererText();
      
      /* creo gli oggetti colonna */
      $colData = new GtkTreeViewColumn('Data', $cellRenderer, 'text', 0);
      $colData->set_resizable(true);
      $colData->set_sort_column_id(1);
      $this->listControl->append_column($colData);
      $colEvento = new GtkTreeViewColumn('Evento', $cellRenderer, 'text', 1);
      $colEvento->set_resizable(true);
      $colEvento->set_sort_column_id(2);
      $this->listControl->append_column($colEvento);
      
      $this->listControl->columns_autosize();
      
      $this->logFp = fopen("logfile.txt", "a+");
		fwrite($this->logFp, "\n\n-- MARK -- START AT ".date("d-m-Y , h:i:s", time())." --\n");
		
      $this->windowMain = $windowMain;
      
      $this->logVerbose = false;
   }
   
   /**
    * logFile($text) : this function log the event on file and if verbose on window
    * @param string $text text to send to log
    */
   function logFile($text)
   {
      fwrite($this->logFp, $text.SP2HTMLLOGGER_STOP);
      if($this->logVerbose==true) {
         $this->listStoreControl->prepend(array(date("d.m.Y-H:i:s",time()), $text));
         $this->listControl->scroll_to_point(0, 0);
         // $this->listControl->set_model($this->listStoreControl);
         // $this->listControl->insert(0, array(date("d.m.Y-H:i:s",time()), $text));
         // $this->listControl->columns_autosize();
         // $this->listControl->thaw();
      }
   }
   
   /** 
    * exitError($text) : this function generate an error message in log file and exit from procedure
    * @param string $text text to send to log
    */
   function exitError($text) 
   { 
      $this->logFile($text);
      die("USCITA PER ERRORE NON GESTIBILE\n\n");
   }
   
   
   /** 
    * setVerbose($boolean) : this function sets log verbosity
    * @param boolean $boolean
    */
   function setVerbose($boolean)
   {  
      $this->logVerbose = $boolean;
   }
   
   
      
} // END OF CLASS
?>
   
