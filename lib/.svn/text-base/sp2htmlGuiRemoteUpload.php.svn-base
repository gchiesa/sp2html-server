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
* sp2htmlGuiRemoteUpload
*
* This library contains gui for remote upload dialog
* @package sp2html
* @author Giuseppe Chiesa <gchiesa@smos.org>
* @version 1.0
* @abstract
* @copyright GNU Public License
*/

require_once("lib/sp2htmlConf.php");
require_once("lib/sp2htmlGuiPrompt.php");

/**
 * sp2htmlGuiRemoteUpload
 *
 * class contains gui for remote upload dialog
 */
class sp2htmlGuiRemoteUpload {

   var $layout;
   var $handleWindow;
   
   var $dbHandle;
   var $sp2htmlConf;
   var $sp2htmlData;
   var $sp2htmlLogger;
   var $validMail;


   /**
    * sp2htmlGuiRemoteUpload : costruttore classe 
    */
   function sp2htmlGuiRemoteUpload(&$sp2htmlConf, &$sp2htmlLogger, &$sp2htmlData, $codCli, $azienda, &$error)
   {
      $this->sp2htmlConf = $sp2htmlConf;
      $this->sp2htmlData = $sp2htmlData;
      $this->sp2htmlLogger = $sp2htmlLogger;
      
      $codCli = trim($codCli);
      $azienda = trim($azienda);
      
      /* apro il db */
      $this->dbHandle = dba_open($this->sp2htmlConf->getValue('file.db.remoteupload.email', $this->sp2htmlLogger), 'c', 'db4');
      if($this->dbHandle == null) 
         $error = true;
      
      /* carico l'interfaccia glade */
      $this->layout = &new GladeXML('gui/windowRemoteUpload.glade');
      
      /* segnali principali */
      $this->handleWindow = $this->layout->get_widget("windowRemoteUpload");
      $this->handleWindow->connect("destroy", array(&$this, "destroy"));
      $this->handleWindow->connect("delete-event", array(&$this, "deleteEvent"));
      
      /* SEGNALI */
      $ctrl = $this->layout->get_widget('buttonNoMail');
      $ctrl->connect("clicked", array(&$this, "destroy"));
      $ctrl = $this->layout->get_widget('buttonConferma');
      $ctrl->connect("clicked", array(&$this, "checkAndConfirm"));
      
      /* aggiorno i campi di default */
      $ctrl = $this->layout->get_widget('entryCodCliente');
      $ctrl->set_text($codCli);
      
      $ctrl = $this->layout->get_widget('entryAnagrafica');
      $ctrl->set_text($azienda);

      $ctrl = $this->layout->get_widget('labelMailAlert');
      $ctrl->set_text('...controllo mail in corso...');
      
      /* cerco sul db se esiste gi� una mail da proporre */
      if(dba_exists('C'.$codCli, $this->dbHandle)) {
         $ctrl = $this->layout->get_widget('entryEmail');
         $ctrl->set_text(dba_fetch('C'.$codCli, $this->dbHandle));
      }
      
      $error = false;
      return;
   }
   
   
   
   
   
   /** --- FUNZIONI SEGNALI --- */
   
   /**
    * checkAndConfirm : verifica che l'email sia valida e esce dalla dialog altrimenti 
    *                   ritorna alla dialog
    */
   function checkAndConfirm()
   {
      $this->verifyMail();
      if($this->validMail == false) 
         return;
         
      $this->destroy();
   }
   
   
   /**
    * verifyMail : verifica se la mail � corretta e imposta il flag dell'ooggetto 
    */
   function verifyMail()
   {
      $ctrl = $this->layout->get_widget('entryEmail');
      $tmpMail = strtolower(trim($ctrl->get_text()));
      
      if($tmpMail == '') {
         $ctrlAlert = $this->layout->get_widget('labelMailAlert');
         $ctrlAlert->set_text("<b>E-Mail non valida</b>");
         $this->validMail = false;
      }
      
      if(preg_match("/^[a-z0-9_\+-]+(\.[a-z0-9_\+-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*\.([a-z]{2,4})$/", $tmpMail)) {
         $ctrlAlert = $this->layout->get_widget('labelMailAlert');
         $ctrlAlert->set_text("<b>controllo E-Mail OK</b>");
         $this->validMail = true;
      } else {
         $ctrlAlert = $this->layout->get_widget('labelMailAlert');
         $ctrlAlert->set_text("E-Mail non valida");
         $this->validMail = false;
      }         
      
      $ctrl->set_text($tmpMail);
   }     

         
   /**
    * insertMailDb : inserisce la mail nel db
    */
   function insertMailDb()
   {
      $ctrl = $this->layout->get_widget('entryCodCliente');
      $tmpCodCli = $ctrl->get_text();
      
      $ctrl = $this->layout->get_widget('entryEmail');
      $tmpEmail = $ctrl->get_text();
      
      dba_replace('C'.$tmpCodCli, $tmpEmail, $this->dbHandle);
   }
   
   
   /**
    * show() : this method show and start the gui 
    */
   function show()
   {  
      $this->handleWindow->show();
      Gtk::main();
   }
   
   
   /** 
    * destroy : funzioni finali per la distruzione della gui
    */
   function destroy()
   {
      $this->verifyMail();
      if($this->validMail) {
         $this->insertMailDb();
         dba_close($this->dbHandle);
         $ctrl = $this->layout->get_widget('entryEmail');
         $this->sp2htmlData->RUMail = $ctrl->get_text();
      } else {
         $this->sp2htmlData->RUMail = '';
         if($this->dbHandle != NULL) dba_close($this->dbHandle);
      }
      
      $this->handleWindow->destroy();
      Gtk::main_quit();
   }
   
   
   /**
    * deleteEvent : funzione richiesta da gestore gui
    */
   function deleteEvent()
   {
      return (false);
   }

      
   
} // END OF CLASS
?>
