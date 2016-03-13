<?
/*********************************************************
* Copyright 2008, 2009 - Giuseppe Chiesa
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
* sp2htmlHtml2Pdf
*
* This library contains to build and print pdf from html files
* @package sp2html
* @author Giuseppe Chiesa <gchiesa@smos.org>
* @version 1.0
* @abstract
* @copyright GNU Public License
*/
require_once('lib/ext/dompdf/dompdf_config.inc.php');

class sp2htmlHtml2Pdf {
   
   var $htmlFile;
   var $pdfFile;
   var $DOMPDF;
   var $basePath;
   var $progressBar;
   var $sp2htmlLogger;
   
   var $procHandle;
   var $gtkTimeout;
   var $finish;
   var $forceStopProc;
   
   
   /** 
    * costruttore della classe
    * @param string $htmlFile nome del file html da renderizzare 
    * @return null
    */
   function sp2htmlHtml2Pdf($htmlFile, $pdfFile = '') 
   {
      $this->htmlFile = $htmlFile;
      $this->pdfFile = $pdfFile;
      $this->finish = false;
      $this->forceStopProc = false;
      
      $this->DOMPDF = new DOMPDF();
      
      /* Inizializzazione variabili pagina */
      $this->DOMPDF->set_paper(array(0, 0, 690.00, 962.00), 'portrait');
      $this->DOMPDF->load_html(file_get_contents($htmlFile));
      
    }
   
   
   
   /**
    * renderizza e restituisce i dati pdf
    * @return string contenuto pdf
    */
   function getPDF($requireStatus = false)
   {
      setlocale(LC_NUMERIC, 'C'); /* serve per i dati del pdf altrimenti risulta un pdf corrotto */ 
      $this->DOMPDF->render($requireStatus);
      
      if($this->pdfFile == '') 
         return ($this->DOMPDF->output());
      
      file_put_contents($this->pdfFile, $this->DOMPDF->output());
   }
   
   
   
   /**
    * imposta il base path per la creazione e l'integrazione degli elementi del pdf 
    * @param string $basePath percorso dove trovare i files da integrare nel pdf
    * @return null
    */
   function setBasePath($basePath)
   {
      $this->basePath = $basePath;
      $this->DOMPDF->set_base_path($basePath);
   }
   
   
   
   /**
    * imposta il puntatore alla progress bar 
    * @param object $progressBar 
    * @return null 
    */
   function setProgressBar(&$progressBar)
   {
      $this->progressBar = $progressBar;
      $this->progressBar->set_fraction(1);
      $this->progressBar->set_text('Creazione PDF in corso...attendere');
      $this->progressBar->set_pulse_step(0.20);      
   }
   
   
   
   function startThread()
   {
      $this->sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : inizio conversione PDF in background...");
      
      if($this->procHandle != null) {
         
         $this->sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : handle per il processo di conversione già occupato, attendo che si liberi...");
         return;
         
      }
      
      /* apro il processo handle */
      $cmd = 'php -r \'ini_set("memory_limit", "256M");require_once("lib/sp2htmlHtml2Pdf.php");'.
             '$r = new sp2htmlHtml2Pdf("'.$this->htmlFile.'", "'.$this->pdfFile.'");'.
             '$r->setBasePath("'.$this->basePath.'");'.
             '$r->getPDF(true); \' ';
      
      $this->sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : apro handle ".$cmd);
      
      $this->procHandle = popen($cmd, 'r');
      
      if($this->procHandle != null) {

         stream_set_blocking($this->procHandle, FALSE);
         stream_set_timeout($this->procHandle, 0, 200);
         $this->gtkTimeout = gtk::timeout_add(200, array(&$this, 'updateConversionStatus'));
         
      } else {
         
         if($this->gtkTimeout != null) 
            gtk::timeout_remove($this->gtkTimeout);
      
      }
      
   }
   
   
   
   function updateConversionStatus()
   {
      /* se il procHandle è null ritorno true per staccare il timeout */
      if($this->procHandle == null) return false;
      
      /* se richiesto l'annullamento del processo */
      if($this->forceStopProc) {
         
         pclose($this->procHandle);
         $this->procHandle = null;
         $this->finish = true;
         $this->progressBar->set_fraction(1);
         $this->progressBar->set_text('Pulizia PDF');
         gtk::main_iteration();
         return false;
      }
      
      /* leggo la pipe nbyte */
      $tmpData = fread($this->procHandle, 1024);
      
      /* Se il contenuto è END chiudo la pipe */
      if(preg_replace("/.*<sp2html_html2pdf>(.*)<\/sp2html_html2pdf>.*/", "\$1", $tmpData) == 'END') {
         
         pclose($this->procHandle);
         $this->procHandle = null;
         $this->finish = true;
         $this->progressBar->set_fraction(1);
         $this->progressBar->set_text('Pulizia PDF');
         gtk::main_iteration();
         return false;
      }
      
      $tmpData = preg_replace("/.*<sp2html_html2pdf>(.*)<\/sp2html_html2pdf>.*/", "\$1", $tmpData);
      $this->progressBar->set_text('Creazione PDF, frame: '.$tmpData);
      $this->progressBar->pulse();
      gtk::main_iteration();
      
      return true;
   }
   
   
   
   function setLogger(&$sp2htmlLogger)
   {
      $this->sp2htmlLogger = $sp2htmlLogger;
   }
   
   
}
?>
