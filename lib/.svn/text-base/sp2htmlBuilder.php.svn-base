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
* sp2htmlBuilder
*
* This library contains to build and print in browser the template of spigax processed by sp2html
* @package sp2html
* @author Giuseppe Chiesa <gchiesa@smos.org>
* @version 1.0
* @abstract
* @copyright GNU Public License
*/

require_once("lib/sp2htmlParser.php");
require_once("lib/sp2htmlGuiRemoteUpload.php");
require_once("lib/sp2htmlGuiPersonalMail.php");
require_once("lib/sp2htmlRemoteUpload.php");
require_once('lib/sp2htmlHtml2Pdf.php');


/**
 * sp2htmlBuilder 
 *
 * class contains methods to build and print documents
 */
class sp2htmlBuilder {
   
   
   var $progressBar;
   var $hboxBreakThread;
   var $handlePDF; 
   

   /**
    * makeFatt(&$sp2htmlConf, $sp2htmlData, &$sp2htmlLogger) : this function build a final document FATTURA and 
    * render it with the browser selected
    * @param object $sp2htmlConf pointer to a object sp2htmlConf
    * @param object $sp2htmlData pointer to a object sp2htmlData
    * @param object $sp2htmlLogger pointer to a object sp2htmlLogger
    * @param object $sp2htmlRemoteUpload puntatore all'oggetto remote upload
    */
   function makeFatt(&$sp2htmlConf, &$sp2htmlData, &$sp2htmlLogger, &$sp2htmlRemoteUpload)
   {
      $sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : trovata fattura <".$sp2htmlData->origFileDoc.">, inizio conversione...");
      $sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : lettura file tracciato...");
   
      /* istanzio le classi per il parsing */
      $sp2htmlParser = new sp2htmlParser();
      
      if($sp2htmlData->origFileTemplate=="")
         $sp2htmlData->origFileTemplate = $sp2htmlConf->getValue("file.moduli.fattura", $sp2htmlLogger);
      
      $sp2htmlParser->parseTemplate($sp2htmlData, $sp2htmlLogger);
      $sp2htmlParser->parseDocPages($sp2htmlData, $sp2htmlLogger);
      
      $sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : fattura di numero ".count($sp2htmlData->docPages)." pagine");
   
      $fout = NULL;     /* mi assicuro che l'handle sia nullo */
      $this->handlePDF = NULL;
      $tmp_file_pdf = ''; 
      
      $this->progressBar->show();
      $this->progressBar->set_text('Rendering file HTML...');
      $this->progressBar->set_pulse_step(0.20);
      
      for($k=0;$k<count($sp2htmlData->docPages);$k++)
      { 
         $this->progressBar->pulse();
         Gtk::main_iteration();
         
         $sp2htmlData->docPagesCurrent = $sp2htmlData->docPages[$k];
         $sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__.": stampa pagina ".($k+1));
   
         /* azzero gli array */
         $sp2htmlData->clearDoc();
   
         /* creo l'array associativo */
         $sp2htmlParser->createAssocData($sp2htmlData);
   
         if($fout==NULL) {
            $tmp_file = $sp2htmlConf->getValue("directory.temp", $sp2htmlLogger).'/'."FATT_".str_replace("/", "-", $sp2htmlData->docOverall['V003']).".html";
            $sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : creazione file <$tmp_file>...");
            $fout = fopen($tmp_file, "wb");
         }
   
         require("lib/sp2htmlHTMLFatt.php");
         $sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : creata pagina ".($k+1)." nel file html output...");
         
         /* cattura dati per Remote Upload */
         if($k == 0) {                                      // solo nella prima pagina
            $RUCodCli = $sp2htmlData->docOverall['V005'];
            $RUPIva =$sp2htmlData->docOverall['V011'];
            $RUData = $sp2htmlData->docOverall['V004'];
            $RUAzienda = $sp2htmlData->docOverall['V006'];
            $RUNumDoc = $sp2htmlData->docOverall['V003'];
            $RUTot =  $sp2htmlData->docOverall['V319'];
         }
         
      } // END FOR
      fclose($fout);
   
      
      
      /* -------------------- SEZIONE RENDERING PDF -------------------- 
       * --------------------------------------------------------------- */
      if($sp2htmlConf->getValue('params.fattura.create_pdf', $sp2htmlLogger) == 'true') {
         
         $tmp_file_pdf = $sp2htmlConf->getValue("directory.temp", $sp2htmlLogger).'/'."FATT_".str_replace("/", "-", $sp2htmlData->docOverall['V003']).".pdf";
         $sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : creazione file PDF <$tmp_file_pdf>...");
         
         $PDF = new sp2htmlHtml2PDF($tmp_file, $tmp_file_pdf);
         $this->handlePDF = &$PDF;
         $this->hboxBreakThread->set_visible(true);
         $PDF->setLogger($sp2htmlLogger);
         $PDF->setProgressBar($this->progressBar);
         $PDF->setBasePath($sp2htmlConf->getValue("directory.temp", $sp2htmlLogger));
         $PDF->startThread();

         while(!$PDF->finish) {
            gtk::main_iteration();
         }
         
         $this->hboxBreakThread->set_visible(false);
         
         /* se è stato bloccato il processo switcho alla modalita' html */
         if($PDF->forceStopProc) {
            $tmp_file_pdf = '';
            if($sp2htmlConf->getValue('params.fattura.switchtohtml', $sp2htmlLogger) != true) {
               $sp2htmlData->clearData();
            
               $sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : creazione fattura annullata, ritorno monitorizzazione...");
               $this->progressBar->hide();
               return;
            }
         }

      }         
       
      /* -------------------- SEZIONE REMOTE UPLOAD -------------------- 
       * --------------------------------------------------------------- */
      if(!$PDF->forceStopProc && $sp2htmlConf->getValue('params.remoteupload.active', $sp2htmlLogger) == 'true') {
         
         $RUploadGui = &new sp2htmlGuiRemoteUpload($sp2htmlConf, $sp2htmlLogger,$sp2htmlData, $sp2htmlData->docOverall['V005'], $sp2htmlData->docOverall['V006'], $tmpError);
         
         if(!$tmpError) {
            $RUploadGui->show();
         }
         
         unset($RuploadGui);
         
         /* se la mail in sp2htmlData è stata riempita e se è attivo l'upload 
            remoto chiamo il metodo di sp2htmlRemoteUpload per l'upload immediato  */
         if( ($sp2htmlData->RUMail != '') && ($sp2htmlConf->getValue('params.remoteupload.curl.active', $sp2htmlLogger)=='true') ) {
            
            $sp2htmlRemoteUpload->startRemoteUpload(($tmp_file_pdf!='')?$tmp_file_pdf:$tmp_file, $RUCodCli, $RUPIva, $sp2htmlData->RUMail, $RUData, $RUAzienda, $RUNumDoc, $RUTot);
            
         } else if( $sp2htmlData->RUMail != '' ) {
            
            $sp2htmlRemoteUpload->createRUFile(($tmp_file_pdf!='')?$tmp_file_pdf:$tmp_file, $RUCodCli, $RUPIva, $sp2htmlData->RUMail, $RUData, $RUAzienda, $RUNumDoc, $RUTot);
            
         }
         
      } // end if($sp2htmlConf->getValue('params.remoteupload.active', $sp2htmlLogger) == 'true') 

      
      
      /* ---------------------STAMPA E RENDERING --------------------
       * ------------------------------------------------------------ */
      if($tmp_file_pdf!='') {
         
         $cmd = $sp2htmlConf->getValue("command.fattura.viewer_pdf", $sp2htmlLogger)." ".$tmp_file_pdf." >> ".$sp2htmlConf->getValue("file.log.command", $sp2htmlLogger)." 2>&1 & ";
         $sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : invio file PDF ".$tmp_file_pdf." al visualizzatore...");
         
      } else {
         
         $cmd = $sp2htmlConf->getValue("command.fattura.viewer_html", $sp2htmlLogger)." ".$tmp_file." >> ".$sp2htmlConf->getValue("file.log.command", $sp2htmlLogger)." 2>&1 & ";
         $sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : invio file ".$tmp_file." al browser per la stampa...");
         
      }
      
      system($cmd); // mando il comando 
   
      /* non più necessari poichè la pulizia viene fatta per sessione del programma */
      // gtk::timeout_add($sp2htmlConf->getValue("params.fattura.delete_after", $sp2htmlLogger), array(&$this, 'deleteFile'), $tmp_file, $sp2htmlLogger);
      // if($tmp_file_pdf!='')   gtk::timeout_add($sp2htmlConf->getValue("params.fattura.delete_after", $sp2htmlLogger), array(&$this, 'deleteFile'), $tmp_file_pdf, $sp2htmlLogger);

         
      /* sposto il file processato */
      $newname = "fattura_".str_replace("/", "-", $sp2htmlData->docOverall['V003']).".".$sp2htmlConf->getValue("params.suffix.fattura", $sp2htmlLogger);
      if(file_exists($sp2htmlConf->getValue("directory.printed", $sp2htmlLogger).'/'.$newname))
         $newname = "fattura_".str_replace("/", "-", $sp2htmlData->docOverall['V003'])."-".time().".".$sp2htmlConf->getValue("params.suffix.fattura", $sp2htmlLogger);
      rename($sp2htmlData->origFileDoc, $sp2htmlConf->getValue("directory.printed", $sp2htmlLogger)."/".$newname); 
      rename($sp2htmlData->origFileTemplate, $sp2htmlConf->getValue("directory.printed", $sp2htmlLogger)."/".$newname.".tpl");
      
      $sp2htmlData->clearData();
   
      $sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : creazione fattura terminata, ritorno monitorizzazione...");
      $this->progressBar->hide();
      
   }

   

   /**
    * deleteFile($fileName) : this function implements the routine of deleting files under gtk timers & signals
    * envirmonment.
    * @param string $fileName the name of file to delete
    * @param object $sp2htmlLogger pointer to a object sp2htmlLogger
    * return boolean(false|true) returns always false to drop down the gtk timer
    */ 
   function deleteFile($fileName, &$sp2htmlLogger)
   {
      if(!file_exists($fileName)) {
         $sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : *WARNING* file ".$fileName." non trovato per eliminazione");
      } else {
         unlink($fileName);
      }
      return (false);
   }
   



   /**
    * makeOrdAndSend(&$sp2htmlConf, $sp2htmlData, &$sp2htmlLogger) : this function build a final document ORDINE and   
    * send it with the selected mail client
    * @param object $sp2htmlConf pointer to a object sp2htmlConf
    * @param object $sp2htmlData pointer to a object sp2htmlData
    * @param object $sp2htmlLogger pointer to a object sp2htmlLogger
    */
    function makeOrdAndSend(&$sp2htmlConf, &$sp2htmlData, &$sp2htmlLogger)
   {
      $sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : ricevuto ordine, inizio conversione...");
      $sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : lettura file tracciato...");

      /* istanzio le classi per il parsing */
      $sp2htmlParser = new sp2htmlParser();
      
      if($sp2htmlData->origFileTemplate=="")
         $sp2htmlData->origFileTemplate = $sp2htmlConf->getValue("file.moduli.ordine", $sp2htmlLogger);

      $sp2htmlParser->parseTemplate($sp2htmlData, $sp2htmlLogger);
      $sp2htmlParser->parseDocPages($sp2htmlData, $sp2htmlLogger);
      
      $sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : ordine di numero ".count($sp2htmlData->docPages)." pagine");
      
      $fout = NULL; /* mi assicuro che l'handle sia nullo */
      $this->handlePDF = NULL;
      
      $this->progressBar->show();
      $this->progressBar->set_text('Rendering file HTML...');
      $this->progressBar->set_pulse_step(0.20);
      
      for($k=0;$k<count($sp2htmlData->docPages);$k++)
      {
         $this->progressBar->pulse();
         Gtk::main_iteration();
         
         $sp2htmlData->docPagesCurrent = $sp2htmlData->docPages[$k];
         $sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__.": stampa pagina ".($k+1));
   
         /* azzero gli array */
         $sp2htmlData->clearDoc();

         $sp2htmlParser->createAssocData($sp2htmlData);
   
         if($fout==NULL) {
            $tmp_file = $sp2htmlConf->getValue("directory.temp", $sp2htmlLogger).'/'."ORD_".str_replace("/", "-", $sp2htmlData->docOverall['V003']).".html";
            $sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : creazione file <$tmp_file>...");
            $fout = fopen($tmp_file, "wb");
         }
         require("lib/sp2htmlHTMLOrd.php");

         $sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : creata pagina ".($k+1)." nel file html output...");

        /* ---------- PERSONALMAIL cattura dati ----------- */
        if($k == 0) {
           $codFor =  $sp2htmlData->docOverall['V005'];
           $azienda = $sp2htmlData->docOverall['V006'];
           $numDoc = $sp2htmlData->docOverall['V003'];
        }
        
      } // END FOR
      fclose($fout);

      /* -------------------- SEZIONE RENDERING PDF -------------------- 
       * --------------------------------------------------------------- */
      if($sp2htmlConf->getValue('params.email.create_pdf', $sp2htmlLogger) == 'true') {
         
         $tmp_file_pdf = $sp2htmlConf->getValue("directory.temp", $sp2htmlLogger).'/'."ORD_".str_replace("/", "-", $sp2htmlData->docOverall['V003']).".pdf";
         $sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : creazione file PDF <$tmp_file_pdf>...");
         
         $PDF = new sp2htmlHtml2PDF($tmp_file, $tmp_file_pdf);

         $this->handlePDF = &$PDF;
         $this->hboxBreakThread->set_visible(true);
         
         $PDF->setLogger($sp2htmlLogger);
         $PDF->setProgressBar($this->progressBar);
         $PDF->setBasePath($sp2htmlConf->getValue("directory.temp", $sp2htmlLogger));
         $PDF->startThread();

         while(!$PDF->finish) {
            gtk::main_iteration();
         }
         
         $this->hboxBreakThread->set_visible(false);
         
         if($PDF->forceStopProc) {
            $sp2htmlData->clearData();

            $sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : creazione pdf annullata, ritorno monitorizzazione...");
            $this->progressBar->hide();
            return;
         }

      }      
      // sleep(1);
      
      /* -------------------- SEZIONE GUIMAILORDINI --------------------
       * --------------------------------------------------------------- */
      if($sp2htmlConf->getValue('params.email.personalmail.active', $sp2htmlLogger) == 'true') {
         
         $tmpError = false;
         $PMail = &new sp2htmlGuiPersonalMail($sp2htmlConf, $sp2htmlLogger, $codFor, $azienda, $tmpError);
         
         if(! $tmpError) {       // se tutto è ok continuo
            
            $PMail->show();
            $mailCC = $PMail->getMail();
            
         } // if tmpError
         
      } // if getValue

      
      $sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : invio mail con allegato ".(($tmp_file_pdf!='')?$tmp_file_pdf:$tmp_file)." al programma di email");
      
      
      /* creo l'oggetto della mail in base alle opzioni (fornitore si/no) */
      $mailSubject = $sp2htmlConf->getValue("params.email.subject", $sp2htmlLogger).' ';
      $mailFrom = $sp2htmlConf->getValue("params.email.mail_from", $sp2htmlLogger);
      $mailTo = $sp2htmlConf->getValue("params.email.mail_to", $sp2htmlLogger);
      
      /* se è stata richiesta la mail esclusiva sostituisco il mailTo con mailCC */
      if($PMail) {
         
         if($PMail->isExclusive()) {
            
            $mailTo = $mailCC;
            $mailCC = '';
            
         }
         
      } // $PMail
      
      if($sp2htmlConf->getValue("params.email.subject.field_ordnum", $sp2htmlLogger) == 'true' ) {
         
         $mailSubject .= 'Numero: '.$numDoc.' ';
         
      } /* aggiunta numero ordine */
      
      if($sp2htmlConf->getValue("params.email.subject.field_company", $sp2htmlLogger) == 'true' ) {
         
         $mailSubject .= 'Fornitore: '.$azienda.' ';
         
      } /* aggiunta nome azienda */
      
                  
      /* verifico se devo inserire l'attach anche dell'html */
      if($sp2htmlConf->getValue("params.email.attach_html", $sp2htmlLogger)=="true") {
      
         if($tmp_file_pdf != '') 
            $cmd = $sp2htmlConf->getValue("command.email", $sp2htmlLogger).' "to='.$mailTo.',cc='.$mailCC.',from='.$mailFrom.',subject=\''.$mailSubject.'\',attachment=\'file:///'.getenv("PWD").'/'.$tmp_file.',file:///'.getenv("PWD").'/'.$tmp_file_pdf.'\'"';
         else 
            $cmd = $sp2htmlConf->getValue("command.email", $sp2htmlLogger).' "to='.$mailTo.',cc='.$mailCC.',from='.$mailFrom.',subject=\''.$mailSubject.'\',attachment=\'file:///'.getenv("PWD").'/'.$tmp_file.'\'"';
    
      } else {
    
         $cmd = $sp2htmlConf->getValue("command.email", $sp2htmlLogger).' "to='.$mailTo.',cc='.$mailCC.',from='.$mailFrom.',subject=\''.$mailSubject.'\',attachment=\'file:///'.getenv("PWD").'/'.$tmp_file_pdf.'\'"';
         
      } // attach_html

      system($cmd);

      /* non più necessari poichè la cancellazione viene effettuata a inizio sessione */
      // gtk::timeout_add($sp2htmlConf->getValue("params.email.delete_after", $sp2htmlLogger), array(&$this, 'deleteFile'), $tmp_file, $sp2htmlLogger);

      //if($tmp_file_pdf != '') {
      //   
      //   gtk::timeout_add($sp2htmlConf->getValue("params.email.delete_after", $sp2htmlLogger), array(&$this, 'deleteFile'), $tmp_file_pdf, $sp2htmlLogger);
      //   
      //}      
   
      /* sposto il file processato */
      $newname = "ordine_".str_replace("/", "-", $sp2htmlData->docOverall['V003']).".".$sp2htmlConf->getValue("params.suffix.ordine", $sp2htmlLogger);
      if(file_exists($sp2htmlConf->getValue("directory.printed", $sp2htmlLogger).$newname))
         $newname = "ordine_".str_replace("/", "-", $sp2htmlData->docOverall['V003'])."-".time().".".$sp2htmlConf->getValue("params.suffix.ordine", $sp2htmlLogger);

      rename($sp2htmlData->origFileDoc, $sp2htmlConf->getValue("directory.printed", $sp2htmlLogger)."/".$newname); 
      rename($sp2htmlData->origFileTemplate, $sp2htmlConf->getValue("directory.printed", $sp2htmlLogger)."/".$newname.".tpl");
   
      $sp2htmlData->clearData();

      $sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : creazione ordine terminata, ritorno monitorizzazione...");
      $this->progressBar->hide();

   }




   /**
    * makeOrdPDF(&$sp2htmlConf, $sp2htmlData, &$sp2htmlLogger) : this function build a final document ORDINE and   
    * just render with your viewer
    * @param object $sp2htmlConf pointer to a object sp2htmlConf
    * @param object $sp2htmlData pointer to a object sp2htmlData
    * @param object $sp2htmlLogger pointer to a object sp2htmlLogger
    */
   function makeOrdPDF(&$sp2htmlConf, &$sp2htmlData, &$sp2htmlLogger)
   {
      $sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : ricevuto ordine, inizio conversione...");
      $sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : lettura file tracciato...");

      /* istanzio le classi per il parsing */
      $sp2htmlParser = new sp2htmlParser();
      
      if($sp2htmlData->origFileTemplate=="")
         $sp2htmlData->origFileTemplate = $sp2htmlConf->getValue("file.moduli.ordine", $sp2htmlLogger);

      $sp2htmlParser->parseTemplate($sp2htmlData, $sp2htmlLogger);
      $sp2htmlParser->parseDocPages($sp2htmlData, $sp2htmlLogger);
      
      $sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : ordine di numero ".count($sp2htmlData->docPages)." pagine");
      

      $fout = NULL; /* mi assicuro che l'handle sia nullo */
      $this->handlePDF = NULL;
      
      $this->progressBar->show();
      $this->progressBar->set_text('Rendering file HTML...');   
      $this->progressBar->set_pulse_step(0.20);
      
      for($k=0;$k<count($sp2htmlData->docPages);$k++)
      {
         $this->progressBar->pulse();
         Gtk::main_iteration();
         
         $sp2htmlData->docPagesCurrent = $sp2htmlData->docPages[$k];
         $sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__.": stampa pagina ".($k+1));
         
         /* azzero gli array */
         $sp2htmlData->clearDoc();
         
         $sp2htmlParser->createAssocData($sp2htmlData);
         
         if($fout==NULL) {
            
            $tmp_file = $sp2htmlConf->getValue("directory.temp", $sp2htmlLogger).'/'."ORD_".str_replace("/", "-", $sp2htmlData->docOverall['V003']).".html";
            $sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : creazione file <$tmp_file>...");
            $fout = fopen($tmp_file, "wb");
            
         }
         
         require("lib/sp2htmlHTMLOrd.php");
         
         $sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : creata pagina ".($k+1)." nel file html output...");
         
         
         /* ---------- PERSONALMAIL cattura dati ----------- */
         if($k == 0) {
           
           $codFor =  $sp2htmlData->docOverall['V005'];
           $azienda = $sp2htmlData->docOverall['V006'];
           $numDoc = $sp2htmlData->docOverall['V003'];
           
         } // $k == 0
        
      } // END FOR
      
      fclose($fout);

      /* -------------------- SEZIONE RENDERING PDF -------------------- 
       * --------------------------------------------------------------- */
      if($sp2htmlConf->getValue('params.email.create_pdf', $sp2htmlLogger) == 'true') {
         
         $tmp_file_pdf = $sp2htmlConf->getValue("directory.temp", $sp2htmlLogger).'/'."ORD_".str_replace("/", "-", $sp2htmlData->docOverall['V003']).".pdf";
         $sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : creazione file PDF <$tmp_file_pdf>...");
         
         $PDF = new sp2htmlHtml2PDF($tmp_file, $tmp_file_pdf);

         $this->handlePDF = &$PDF;
         $this->hboxBreakThread->set_visible(true);
         
         $PDF->setLogger($sp2htmlLogger);
         $PDF->setProgressBar($this->progressBar);
         $PDF->setBasePath($sp2htmlConf->getValue("directory.temp", $sp2htmlLogger));
         $PDF->startThread();

         while(!$PDF->finish) {
            gtk::main_iteration();
         }

         $this->hboxBreakThread->set_visible(false);
         
         if($PDF->forceStopProc) {
            $sp2htmlData->clearData();

            $sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : creazione pdf annullata, ritorno monitorizzazione...");
            $this->progressBar->hide();
            return;
         }

      }
      
      // sleep(1);

      /* stampo il file pdf a video */
      if($tmp_file_pdf != '') {
         
         $cmd = $sp2htmlConf->getValue("command.email.viewer_pdf", $sp2htmlLogger).' '.$tmp_file_pdf." >> ".$sp2htmlConf->getValue("file.log.command", $sp2htmlLogger);
      
      } else {

         $cmd = $sp2htmlConf->getValue("command.email.viewer_html", $sp2htmlLogger).' '.$tmp_file." >> ".$sp2htmlConf->getValue("file.log.command", $sp2htmlLogger);

      }
      
      system($cmd);

      gtk::timeout_add($sp2htmlConf->getValue("params.email.delete_after", $sp2htmlLogger), array(&$this, 'deleteFile'), $tmp_file, $sp2htmlLogger);
      
      if($tmp_file_pdf != '') 
         gtk::timeout_add($sp2htmlConf->getValue("params.email.delete_after", $sp2htmlLogger), array(&$this, 'deleteFile'), $tmp_file_pdf, $sp2htmlLogger);
   
      /* sposto il file processato */
      $newname = "ordine_".str_replace("/", "-", $sp2htmlData->docOverall['V003']).".".$sp2htmlConf->getValue("params.suffix.ordine", $sp2htmlLogger);
      if(file_exists($sp2htmlConf->getValue("directory.printed", $sp2htmlLogger).$newname))
         $newname = "ordine_".str_replace("/", "-", $sp2htmlData->docOverall['V003'])."-".time().".".$sp2htmlConf->getValue("params.suffix.ordine", $sp2htmlLogger);
      
      rename($sp2htmlData->origFileDoc, $sp2htmlConf->getValue("directory.printed", $sp2htmlLogger)."/".$newname); 
      rename($sp2htmlData->origFileTemplate, $sp2htmlConf->getValue("directory.printed", $sp2htmlLogger)."/".$newname.".tpl");
   
      $sp2htmlData->clearData();

      $sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : creazione ordine terminata, ritorno monitorizzazione...");
      $this->progressBar->hide();
      
   }
   
   
   
   /**
    * imposta  il puntatore alla barra download 
    * @param object $progressBar 
    * @return null
    */
   function setProgressBar(&$progressBar)
   {
      $this->progressBar = $progressBar;
   }
	  
   
   /**
    * imposta il puntatore alla hboxBreakThread
    * @param @object $hboxBreakThread
    * @return null
    */
   function setHboxBreakThread(&$hboxBreakThread)
   {
      $this->hboxBreakThread = $hboxBreakThread;
   }
   
    
} // END OF CLASS
?>
