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
* sp2htmlGuiPersonalMail
*
* This library contains gui for manage ad company mail per order
* @package sp2html
* @author Giuseppe Chiesa <gchiesa@smos.org>
* @version 1.0
* @abstract
* @copyright GNU Public License
*/

require_once("lib/sp2htmlConf.php");
require_once("lib/sp2htmlGuiPrompt.php");

/**
 * sp2htmlGuiMailOrdini
 *
 * class contains gui for  manage ad company mail per order
 */
class sp2htmlGuiPersonalMail {

   var $layout;
   var $handleWindow;
   
   var $dbHandle;
   var $sp2htmlConf;
   var $sp2htmlLogger;
   var $validMail;
   
   var $onlyMainMail;
   var $isExclusive;
   var $reqMail; 


   /**
    * sp2htmlGuiRemoteUpload : costruttore classe 
    */
   function sp2htmlGuiPersonalMail(&$sp2htmlConf, &$sp2htmlLogger, $codFor, $azienda, &$error)
   {
      $this->sp2htmlConf = $sp2htmlConf;
      $this->sp2htmlLogger = $sp2htmlLogger;
      
      $codFor = trim($codFor);
      $azienda = trim($azienda);
      
      /* apro il db */
      $this->dbHandle = dba_open($this->sp2htmlConf->getValue('file.db.personalmail', $this->sp2htmlLogger), 'c', 'db4');
      if($this->dbHandle == null) 
         $error = true;
      
      /* carico l'interfaccia glade */
      $this->layout = &new GladeXML('gui/windowPersonalMail.glade');
      
      /* segnali principali */
      $this->handleWindow = $this->layout->get_widget("windowPersonalMail");
      $this->handleWindow->connect("destroy", array(&$this, "destroy"));
      $this->handleWindow->connect("delete-event", array(&$this, "deleteEvent"));
      
      /* SEGNALI */
      $ctrl = $this->layout->get_widget('buttonNoMail');
      $ctrl->connect("clicked", array(&$this, "onlyMainMail"));
      $ctrl = $this->layout->get_widget('buttonConferma');
      $ctrl->connect("clicked", array(&$this, "checkAndConfirm"));
      $ctrl = $this->layout->get_widget('checkbuttonInvioEsclusivo');
      $ctrl->connect('toggled', array(&$this, "changeInvioEsclusivo"));
      
      /* aggiorno i campi di default */
      $ctrl = $this->layout->get_widget('entryCodFor');
      $ctrl->set_text($codFor);
      
      $ctrl = $this->layout->get_widget('entryAnagrafica');
      $ctrl->set_text($azienda);

      $ctrl = $this->layout->get_widget('labelMailAlert');
      $ctrl->set_text('...controllo mail in corso...');
      
      /* cerco sul db se esiste giï¿œ una mail da proporre */
      if(dba_exists('F'.$codFor, $this->dbHandle)) {
         $ctrl = $this->layout->get_widget('entryEmail');
         $ctrl->set_text(dba_fetch('F'.$codFor, $this->dbHandle));
      }
      
      $error = false;
      return;
   }
   
   
   
   /**
    * getMail: restituisce la mail caricata in reqMail
    */
   function getMail()
   {
      return $this->reqMail;
   }
   
   
   /**
    * isExclusive: restituisce true se è stata richiesta la mail esclusiva 
    */
   function isExclusive()
   {
      return $this->isExclusive;
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
    * onlyMainMail: imposta che venga inviata la mail solo alla mail principale di conf
    */
   function onlyMainMail()
   {
      $this->onlyMainMail = true;
      $this->destroy();
   }
   
      
   /** 
    * changeInvioEsclusivo: attiva o disattiva l'invio esclusivo della mail bypassando il mailto
    *                       del file di conf.
    */
   function changeInvioEsclusivo() 
   {
      $ctrl = $this->layout->get_widget('checkbuttonInvioEsclusivo');
      
      if($ctrl->get_active()) {
         $this->isExclusive = true;
      } else {
         $this->isExclusive = false;
      }
   }
          
  
   /**
    * verifyMail : verifica se la mail è corretta e imposta il flag dell'ooggetto 
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
      $ctrl = $this->layout->get_widget('entryCodFor');
      $tmpCodCli = $ctrl->get_text();
      
      $ctrl = $this->layout->get_widget('entryEmail');
      $tmpEmail = $ctrl->get_text();
      
      dba_replace('F'.$tmpCodCli, $tmpEmail, $this->dbHandle);
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
      
      /* se è richiesto l'invio solo alla mail principale */
      if($this->onlyMainMail) 
         $this->validMail = false;
         
      if($this->validMail) {
         $this->insertMailDb();
         dba_close($this->dbHandle);
         $ctrl = $this->layout->get_widget('entryEmail');
         $this->reqMail = $ctrl->get_text();
      } else {
         $this->reqMail = '';
         dba_close($this->dbHandle);
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
