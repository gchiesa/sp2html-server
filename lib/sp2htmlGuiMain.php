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
* sp2htmlGuiMain
*
* This library contains gui for main dialog
* @package sp2html
* @author Giuseppe Chiesa <gchiesa@smos.org>
* @version 1.0
* @abstract
* @copyright GNU Public License
*/

require_once("lib/sp2htmlConf.php");
require_once("lib/sp2htmlLogger.php");
require_once("lib/sp2htmlNet.php");
require_once("lib/sp2htmlGuiEditConfiguration.php");
require_once("lib/sp2htmlGuiPrompt.php");
require_once("lib/sp2htmlGuiAbout.php");
require_once("lib/sp2htmlBuilder.php");
require_once("lib/sp2htmlData.php");
require_once("lib/sp2htmlRemoteUpload.php");

define("GTKCHECKCONVERSION", 100);

/**
 * sp2htmlGuiMain
 *
 * class contains gui for main dialog
 */
class sp2htmlGuiMain {

   var $layout;
   var $handleWindow;
   var $sp2htmlConf;
   var $sp2htmlLogger;
   var $sp2htmlServer;
   var $sp2htmlBuilder;
   var $sp2htmlRemoteUpload;
   
   var $timeoutCheckConversion;
   
   /**
    * sp2htmlGuiMain() : main constructor
    */
   function sp2htmlGuiMain()
   {
      /* apro il file di configurazione */
      $this->sp2htmlConf = &new sp2htmlConf("etc/sp2html.cfg");
      $this->sp2htmlConf->loadData();
            
      // Gtk::rc_parse( GTK::rc_get_theme_dir() . "/" ."Blue". "/gtk/gtkrc");
      $this->layout = &new GladeXML('gui/windowMainDialog.glade');
      
      /* attivo i segnali principali della finestra */
      $this->handleWindow = $this->layout->get_widget("windowMainDialog");
      // $this->handleWindow->connect("destroy", array(&$this, "destroy"));
      $this->handleWindow->connect("delete-event", array(&$this, "deleteEvent"));

      /* aggiorno opzioni menu */
      $ctrl = $this->layout->get_widget('uploadRemoto');
      if($this->sp2htmlConf->getValue('params.remoteupload.active') == 'true') { 
         $ctrl->set_active(true);
      } else { 
         $ctrl->set_active(false);
      }
      
      $ctrl = $this->layout->get_widget('invioImmediato');
      if($this->sp2htmlConf->getValue('params.remoteupload.curl.active') == 'true') {
         $ctrl->set_active(true);
      } else {
         $ctrl->set_active(false);
      }
      
      /* attivo i segnali per il menu */
      $ctrl = $this->layout->get_widget("visualizzaDatiConf");
      $ctrl->connect("activate", array(&$this, "on_visualizzaDatiConf_activate"));
      $ctrl = $this->layout->get_widget('ricaricaTemplate');
      $ctrl->connect('activate', array(&$this, 'on_ricaricaTemplate_activate'));
      $ctrl = $this->layout->get_widget("exitProgram");
      $ctrl->connect("activate", array(&$this, "on_exitProgram_activate"));
      $ctrl = $this->layout->get_widget('uploadRemoto');
      $ctrl->connect('activate', array(&$this, 'on_uploadRemoto_activate'));
      $ctrl = $this->layout->get_widget('invioImmediato'); 
      $ctrl->connect('activate', array(&$this, 'on_invioImmediato_activate')); 
      $ctrl = $this->layout->get_widget("infoAbout");
      $ctrl->connect("activate", array(&$this, "on_infoAbout_activate"));
      
      /* attivo i segnali per checkbutton verbose */
      $ctrl = $this->layout->get_widget("checkbuttonVerbose");
      $ctrl->connect("toggled", array(&$this, "switchVerbose"));
      
      /* attivo i segnali per il pulsante di interruzione task */
      $ctrl = $this->layout->get_widget('buttonBreakThread');
      $ctrl->connect('clicked', array(&$this, 'breakThread'));
            
      /* carico il logo */
      $ctrl = $this->layout->get_widget("imageLogo");
      $ctrl->set_from_file("gui/logo.png");

      /* istanzio l'oggetto Logger */
      $this->sp2htmlLogger = &new sp2htmlLogger($this->layout->get_widget("treeviewLogger"), $this->handleWindow, $this->sp2htmlConf);

      /* verifico la congruit? dei dati di configurazione */
      $this->sp2htmlConf->checkConfData($this->sp2htmlLogger);      
         
      /* aggiorno i dati/etichette sulla finestra */
      $ctrl = $this->layout->get_widget("labelFileToProcess");
      $ctrl->set_text("in attesa");
      $ctrl = $this->layout->get_widget("labelLogFile");
      $ctrl->set_text("sp2html.log");
      $ctrl = $this->layout->get_widget("labelConfigurationFile");
      $ctrl->set_text($this->sp2htmlConf->getValue("file.conf", $this->sp2htmlLogger));
      $ctrl = $this->layout->get_widget("labelServerStatus");
      $ctrl->set_text("listening on port: ".$this->sp2htmlConf->getValue("server.port", $this->sp2htmlLogger));
      $ctrl = $this->layout->get_widget("labelDirectoryProcessed");
      $ctrl->set_text($this->sp2htmlConf->getValue("directory.printed", $this->sp2htmlLogger));
      $ctrl = $this->layout->get_widget("labelFileToUpload");
      $ctrl->set_text('nessun upload in corso.');
      
      $ctrl = $this->layout->get_widget("labelStatusBar");
      $ctrl->set_text(APPVERSION);      
      
      /* nascondo/compatto tutte le widget che non sono visualizzate in modalita' verbosa */
      $this->hideArrayWidgets(array('hboxBreakThread', 'hboxFileConf', 'hboxFileLog', 'hboxServerStatus', 'hboxFileElab', 'hboxFileProc', 'hboxFileUpload', 'frameLog'));

      /* istanzio e attivo l'oggetto server */
      $this->sp2htmlServer = &new sp2htmlServer($this->sp2htmlConf->getValue("server.port", $this->sp2htmlLogger), $this->sp2htmlLogger, $this->sp2htmlConf, $this->layout->get_widget("progressbarDownload"), $this->layout->get_widget("labelServerStatus"));
      $this->sp2htmlServer->gtkStart();
      
      /* istanza dell'oggetto sp2htmlRemoteUpload */
      $this->sp2htmlRemoteUpload = &new sp2htmlRemoteUpload();
      $this->sp2htmlRemoteUpload->setHandlers($this->sp2htmlConf, $this->sp2htmlLogger);
      $this->sp2htmlRemoteUpload->linkToWidget($this->layout->get_widget('labelFileToUpload'));
      
      /* attivo il timeout per il gestore/controllo conversione */
      $this->timeoutCheckConversion = gtk::timeout_add(GTKCHECKCONVERSION, array(&$this, 'startConversion'));
   }
   
   
   /**
    * start() : start the windows application
    */
   function start()
   {
      Gtk::main();
   }
   
   /**
    * destroy() : manage the destroy signal
    */
   function destroy()
   {
      Gtk::main_quit();
   }
   
   /**
    * deleteEvent() : manage the signal delete-event
    */
   function deleteEvent()
   {
      return (true);
   }
   
   /**
    * switchVerbose() : handler for checkbutton signal
    */
   function switchVerbose()
   {  
      $ctrl = $this->layout->get_widget("checkbuttonVerbose");
      
      if($ctrl->get_active()) {

         $this->sp2htmlLogger->setVerbose(true);
         $this->sp2htmlServer->sp2htmlLogger->setVerbose(true);
         $this->showArrayWidgets(array('hboxFileConf', 'hboxFileLog', 'hboxServerStatus', 'hboxFileElab', 'hboxFileProc', 'hboxFileUpload', 'frameLog'));

      } else { 

         $this->sp2htmlLogger->setVerbose(false);
         $this->sp2htmlServer->sp2htmlLogger->setVerbose(false);
         $this->hideArrayWidgets(array('hboxFileConf', 'hboxFileLog', 'hboxServerStatus', 'hboxFileElab', 'hboxFileProc', 'hboxFileUpload', 'frameLog'));

      }
   }
   
   
   /**
    * blocca il thread di creazione pdf 
    */
   function breakThread() 
   {
      if($this->sp2htmlBuilder != NULL && $this->sp2htmlBuilder->handlePDF != NULL) {
         
         $this->sp2htmlBuilder->handlePDF->forceStopProc = true;
      }
   }
      
   
   /**
    * nasconde disattiva widget 
    */
   function hideArrayWidgets($aWidgetNames)
   {
      foreach($aWidgetNames as $widget) {
         $ctrl = $this->layout->get_widget($widget);
         $ctrl->set_visible(false);
      }
   }
   
   
   /** 
    * mostra widget
    */
   function showArrayWidgets($aWidgetNames)
   {
      foreach($aWidgetNames as $widget) {
         $ctrl = $this->layout->get_widget($widget);
         $ctrl->set_visible(true);
      }
   }

   
   /**
    * startConversion() : this function check if there are files to process 
    */
   function startConversion()
   {
      if($this->sp2htmlServer->flReadyProcess!=true) 
         return true;
      else {
         /* aggiorno gui */
         $ctrlLabel = $this->layout->get_widget("labelFileToProcess");
         $ctrlLabel->set_text("Tipo: ".$this->sp2htmlServer->tplName." - Nome: ".$this->sp2htmlServer->tplFname.", ".$this->sp2htmlServer->docFname);
         
         /* loggo su file */
         $this->sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : analisi file: ".$this->sp2htmlServer->tplName." , ".$this->sp2htmlServer->tplFname." , ".$this->sp2htmlServer->docFname." avviata...");
         
         /* istanzio la classe builder */
         $sp2htmlBuilder = new sp2htmlBuilder();
         $this->sp2htmlBuilder = &$sp2htmlBuilder;
         $sp2htmlBuilder->setProgressBar($this->layout->get_widget("progressbarDownload"));
         $sp2htmlBuilder->setHboxBreakThread($this->layout->get_widget('hboxBreakThread'));
         
         /* istanzio la classe data */
         $sp2htmlData = new sp2htmlData();
         $sp2htmlData->origFileTemplate = $this->sp2htmlConf->getValue('directory.temp', $this->sp2htmlLogger).'/'.$this->sp2htmlServer->tplFname;
         $sp2htmlData->origFileDoc = $this->sp2htmlConf->getValue('directory.temp', $this->sp2htmlLogger).'/'.$this->sp2htmlServer->docFname;
        
         /* verifico di che documento di si tratta */
         if($this->sp2htmlServer->tplName=="fat-rendering") {
            $sp2htmlBuilder->makeFatt($this->sp2htmlConf, $sp2htmlData, $this->sp2htmlLogger, $this->sp2htmlRemoteUpload);
         } else if($this->sp2htmlServer->tplName=="ord-sending") {
            $sp2htmlBuilder->makeOrdAndSend($this->sp2htmlConf, $sp2htmlData, $this->sp2htmlLogger);
         } else if($this->sp2htmlServer->tplName=="ord-rendering") {
            $sp2htmlBuilder->makeOrdPDF($this->sp2htmlConf, $sp2htmlData, $this->sp2htmlLogger);
         }
         
         /* reimposto il processore file in modalit? attesa */
         $this->sp2htmlServer->flReadyProcess = false;
      }

      return true;
   }


/**
 * FUNZIONI PER LA GESTIONE GUI
 */
    
   function on_visualizzaDatiConf_activate()
   {
      $windowEditConfiguration = &new sp2htmlGuiEditConfiguration($this->sp2htmlConf);
      $windowEditConfiguration->show();
   }
   
   function on_exitProgram_activate()
   {
      $prompt = &new sp2htmlGuiPrompt(" Confermi la chiusura applicazione Sp2HTML Server ?", SP2HTMLMB_YES | SP2HTMLMB_NO);
      $prompt->show();
      if($prompt->returnMessage!=SP2HTMLMB_YES)
         return;
      $this->destroy();
   }
   
   function on_infoAbout_activate()
   {
      $about = &new sp2htmlGuiAbout();
      $about->show();
   }
   
   function on_uploadRemoto_activate()
   {
      $ctrl = $this->layout->get_widget('uploadRemoto');
      
      if($this->sp2htmlConf->getValue('params.remoteupload.active', $this->sp2htmlLogger) == 'false') {
         $this->sp2htmlConf->setValue('params.remoteupload.active', 'true');
         $ctrl->set_active(true);
         $prompt = &new sp2htmlGuiPrompt('Servizio di upload remoto attivato con successo.', SP2HTMLMB_YES);
         $prompt->show();
      } else {
         $ctrl->set_active(false);
         $this->sp2htmlConf->setValue('params.remoteupload.active', 'false');
         $prompt = &new sp2htmlGuiPrompt("Servizio di upload remoto disattivato.\nNon verranno piu' creati i documenti per l'invio su server remoto", SP2HTMLMB_YES);
         $prompt->show();
      }
   }

   
   function on_invioImmediato_activate()
   {
      $ctrl = $this->layout->get_widget('invioImmediato');
      
      if($this->sp2htmlConf->getValue('params.remoteupload.curl.active') == 'false') {
         $this->sp2htmlConf->setValue('params.remoteupload.curl.active', 'true');
         $ctrl->set_active(true);
         $prompt = &new sp2htmlGuiPrompt('Invio immediato documenti attivato con successo.', SP2HTMLMB_YES);
         $prompt->show();
      } else {
         $ctrl->set_active(false);
         $this->sp2htmlConf->setValue('params.remoteupload.curl.active', 'false');
         $prompt = &new sp2htmlGuiPrompt("Invio immediato documenti disattivato.", SP2HTMLMB_YES);
         $prompt->show();
      }
   }      
   
   function on_ricaricaTemplate_activate()
   {
      if(! $this->sp2htmlConf->reloadTemplate()) {       // se reload non va a buon fine 
         $prompt = &new sp2htmlGuiPrompt('ATTENZIONE: non è stato possibile ricompilare i template, verificare la sintassi', SP2HTMLMB_YES);
         $prompt->show();
      } else {
         $prompt = &new sp2htmlGuiPrompt('File template ricaricari e ricompilati con successo.', SP2HTMLMB_YES);
         $prompt->show();
      }
   }


} // END OF CLASS
  
?>
