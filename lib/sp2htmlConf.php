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
* sp2htmlConf
*
* This library contains classes to read and manage the configuration file
* @package sp2html
* @author Giuseppe Chiesa <gchiesa@smos.org>
* @version 1.0
* @abstract
* @copyright GNU Public License
*/

require_once('lib/sp2htmlGuiPrompt.php');
require_once('lib/sp2htmlTemplate.php');

/**
 * sp2htmlConf
 * 
 * Class to read and manage the configuration file
 */
class sp2htmlConf {
   
   var $confFile;
   var $confData;
	var $tplData;
   
   
   /**
    * sp2htmlConf($confFile) : constructor wich create the sp2htmlConf class
    * @param string $confFile configuration file to read and analyze
    */
   function sp2htmlConf($confFile)
   {
      if(!file_exists($confFile)) {
         die("FATAL ERROR: impossibile trovare il file: ".$confFile."\n\n");
      }
      $this->confData = array();
      $this->confData['file.conf'] = $confFile;
      $this->confFile = $confFile;
		$this->tplData = null;
		
   }
   
   
   /** 
    * loadData() : parse the configuration file and populate the confData associative array
    */
   function loadData()
   {
      $content = file($this->confFile);
      foreach($content as $row) {
         $row = trim($row);
         if(substr($row, 0, 1)=="#" || substr($row, 0, 1)==";") // se inizia per ; o # sono commenti 
            continue;
         list($key, $value) = explode("=", $row);              // analizzo ogni riga che deve essere in formato key=value
         if(trim($key)=="") continue;                          // evito valori vuoti
         $this->confData[trim($key)] = trim($value);
      }
   }
   
   
   /**
    * getValue($key, &$sp2htmlLogger) : this function get the associated value for the key gived
    * if the value not exists or it's empty a warning were send to logger object
    * @param string $key key reference to search
    * @param object $sp2htmlLogger poiter to a sp2htmlLogger object
    */
   function getValue($key, &$sp2htmlLogger) 
   {
      if(!isset($this->confData[$key])) {
         $sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : **WARNING** configuration value ".$key." is empty");
      } 
      return ($this->confData[$key]);
   }

   
   /**
    * checkConfData(&$sp2htmlLogger) : this function check for errors the configuration data
    * @param object $sp2htmlLogger pointer to a sp2htmlLogger object
    */
   function checkConfData(&$sp2htmlLogger)
   {
      $errMsg = "";
      $dieMe = false;

      /* prima fase, verifico i parametri di configurazione essenziali */
      if($this->confData['command.email']=='') 
         $errMsg .= " - parametro command.email non impostato. \n";
      if($this->confData['command.email.viewer_pdf']=='')
         $errMsg .= " - parametro command.email.print.only non impostato. \n";
      if($this->confData['params.email.attach_html']=='')
         $errMsg .= " - parametro params.email.attach_html non impostato. \n";
      if($this->confData['command.fattura.viewer_html']=='')
         $errMsg .= " - parametro command.fattura.viewer_html non impostato. \n";
      if($this->confData['directory.printed']=='')
         $errMsg .= " - parametro directory.printed non impostato. \n";
      if($this->confData['directory.temp']=='')
         $errMsg .= " - parametro directory.temp non impostato. \n";
      if($this->confData['file.log.command']=='') 
         $errMsg .= " - parametro file.log.command non impostato. \n";
      if($this->confData['file.moduli.fattura']=='')
         $errMsg .= " - parametro file.moduli.fattura non impostato. \n";
      if($this->confData['file.moduli.ordine']=='')
         $errMsg .= " - parametro file.moduli.ordine non impostato. \n";
      if($this->confData['params.suffix.fattura']=='')
         $errMsg .= " - parametro params.suffix.fattura non impostato. \n";
      if($this->confData['params.suffix.ordine']=='')
         $errMsg .= " - parametro params.suffix.ordine non impostato. \n";
      if($this->confData['server.port']=='')
         $errMsg .= " - parametro server.port non impostato. \n";

      if($errMsg != "") {
         $sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : *** ERRORS IN CONF FILE ***\n\n".$errMsg."\n\nExiting\n\n");
         $dieMe = true;
      }
      
      $errMsg = "";
      

      /* -------------------------------------------------- 
       * ---------- CHECK DIRECTORY ----------------------- */
      if(!file_exists($this->confData['directory.printed'])) 
         $errMsg .= " - errore nel valore directory.printed: ".$this->confData['directory.printed'].", directory inesistente. \n";
      if(!file_exists($this->confData['directory.temp']))
         $errMsg .= " - errore nel valore directory.temp: ".$this->confData['directory.temp'].", directory inesistente.\n";


      /* -------------------------------------------------- 
       * ---------- CHECK FILE COMANDI IN BIN/ ------------ */
         
      /* il command.file puo' essere un file con parametri, percio prendo solo il primo chunk delimitato da spazi */
      list($cmd, $param) = explode(" ", $this->confData['command.email'], 2);
      if(!file_exists($cmd))
         $errMsg .= " - errore nel valore command.email: ".$cmd.", il file non esiste.\n";
      
      /* il command.email.viewer_pdf puo' essere un file con parametri, percio prendo solo il primo chunk delimitato da spazi */
      list($cmd, $param) = explode(" ", $this->confData['command.email.viewer_pdf'], 2);
      if(!file_exists($cmd))
         $errMsg .= " - errore nel valore command.email.viewer_pdf: ".$cmd.", il file non esiste.\n";

      /* il command.file puo' essere un file con parametri, percio prendo solo il primo chunk delimitato da spazi */
      list($cmd, $param) = explode(" ", $this->confData['command.fattura.viewer_html'], 2);
      if(!file_exists($cmd))
         $errMsg .= " - errore nel valore command.fattura.viewer_html: ".$cmd.", il file non esiste.\n";


      /* -------------------------------------------------- 
       * ---------- TEMPLATES ----------------------------- */
		if( (!file_exists($this->confData['template.fat_rendering'])) || ($this->confData['template.fat_rendering']=='') )
			$errMsg .= " - errore nel valore template.fat_rendering: ".$this->confData['template.fat_rendering'].", file non trovato \n";
		if( (!file_exists($this->confData['template.ord_rendering'])) || ($this->confData['template.ord_rendering']=='') )
			$errMsg .= " - errore nel valore template.ord_rendering: ".$this->confData['template.ord_rendering'].", file non trovato \n";
		
		$tmpTplCompiler = new sp2htmlTemplate();
		if(! $tmpTplCompiler->addTemplate($this->confData['template.fat_rendering']))
			$errMsg .= " - errore nella compilazione del template ".$this->confData['template.fat_rendering'].", verificare il template.\n";
		if(! $tmpTplCompiler->addTemplate($this->confData['template.ord_rendering']))
			$errMsg .= " - errore nella compilazione del template ".$this->confData['template.ord_rendering'].", verificare il template.\n";
		
		$this->tplData = $tmpTplCompiler->tplFiles;
		unset($tmpTplCompiler);

      
      /* -------------------------------------------------- 
       * ---------- REMOTEUPLOAD -------------------------- */
      if($this->confData['params.remoteupload.active'] =='true') {
         
         if(! file_exists($this->confData['directory.remoteupload']) ) {
            $errMsg .= " - errore nel valore directory.remoteupload: ".$this->confData['directory.remoteupload'].", directory non esistente\n";
         }
         if($this->confData['file.db.remoteupload.email'] == '') {
            $errMsg .= " - errore nel valore file.db.remoteupload.email: ".$this->confData['file.db.remoteupload.email']." valore non impostato \n";
         }
      }
      
      
      /* -------------------------------------------------- 
       * ---------- STAMPA ERRORI GENERATI ---------------- */
      if($errMsg != "") {
         
         $sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : *** ERRORS IN CONF FILE ***\n\n".$errMsg."\n\nExiting\n\n");
         
         //$prompt = &new $sp2htmlGuiPrompt(" Si sono verificati alcuni errori durante l'avvio. Controllare il logfile.txt per maggiori informazioni. ", SP2HTMLMB_YES);
         //$prompt->show();
         //Gtk::main();
         $dieMe = true;
      }

		
      if($dieMe)  die("Si sono verificati alcuni errori, controllare il log logfile.txt\n\n");
      
      /* pulisco i files vecchi */
      $this->cleanupFiles($sp2htmlLogger);
   }

   
   /** 
    * reloadTemplate: ricarica i template 
    */
   function reloadTemplate()
   {
      if( (!file_exists($this->confData['template.fat_rendering'])) || ($this->confData['template.fat_rendering']=='') ) 
         return false;
      
		if( (!file_exists($this->confData['template.ord_rendering'])) || ($this->confData['template.ord_rendering']=='') )
         return false;

		$tmpTplCompiler = new sp2htmlTemplate();
		
      if(! $tmpTplCompiler->addTemplate($this->confData['template.fat_rendering']))
         return false;
      if(! $tmpTplCompiler->addTemplate($this->confData['template.ord_rendering']))
         return false;
         
		$this->tplData = $tmpTplCompiler->tplFiles;
      unset($tmpTplCompiler);
      
      return true;
   }

   
   /**
    * setValue: imposta un valore nella configurazione 
    */
	function setValue($key, $value)
   {
      $this->confData[$key] = $value;
   }
   
   
   /**
    * cleanUpFiles - pulisce i file vecchi creati nelle sessioni precedenti
    */
   function cleanupFiles(&$sp2htmlLogger)
   {
      /* apro la directory corrente per pulire i file doc e tpl */
      if( $handleDir = opendir('.') ) {
         
         while( ($file = readdir($handleDir)) !== false ) {
            
            if(strncmp($file, 'doc-', 4) == 0 || strncmp($file, 'tpl-', 4) == 0 ) {
               
               $sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : Pulizia file ".$file);
               unlink($file);
               
            }
            
         } // end while
         
         closedir($handleDir);
      } // end if 
      
      /* pulisco la directory temporanea dai file .pdf e .html */
      if( $handleDir = opendir($this->confData['directory.temp']) ) {
         
         while( ($file = readdir($handleDir)) !== false ) {
            
            if(substr($file, -4) == '.pdf' || substr($file, -5) == '.html') {
               
               $sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : Pulizia file ".$this->confData['directory.temp'].'/'.$file);
               unlink($this->confData['directory.temp'].'/'.$file);
               
            }

            if(strncmp($file, 'doc-', 4) == 0 || strncmp($file, 'tpl-', 4) == 0 ) {
               
               $sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : Pulizia file ".$file);
               unlink($this->confData['directory.temp'].'/'.$file);
               
            }
            
         } // end while
         
         closedir($handleDir);
      } // end if 
      
      $sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : Pulizia file completata");
      
   }
   
} // END OF CLASS
?>
   
