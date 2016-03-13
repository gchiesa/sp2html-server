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
* sp2htmlRemoteUpload
*
* This library contains classes to upload to remote server documents
* @package sp2html
* @author Giuseppe Chiesa <gchiesa@smos.org>
* @version 1.0
* @abstract
* @copyright GNU Public License
*/

define('SP2HTML_RU_NOT_A_DIR', -1);

/**
 * sp2htmlRemoteUpload
 * 
 * Class to upload to remote server documents
 */
class sp2htmlRemoteUpload {
   
   var $sp2htmlConf;
   var $sp2htmlLogger; 
   var $procHandle;
   var $gtkTimeout;
   var $labelUpdate;
    
    
   /**
    * sp2htmlRemoteUpload : costruttore
    */
   function sp2htmlRemoteUpload()
   {
      /*if(! function_exists('curl_init') && ($sp2htmlLogger == null))
        die("php-curl non installato\n\n");
      
      if(! function_exists('curl_init')) {
         $sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : ERRORE, php con supporto curl non attivo (risp.".function_exists('curl_init').") , nonostante check con superato. USCITA FORZATA");
         die();
      } */
      $this->procHandle = null;
      $this->gtkTimeout = null;
   }
   
   
   function setHandlers(&$sp2htmlConf, &$sp2htmlLogger)
   {
      $this->sp2htmlConf = $sp2htmlConf;
      $this->sp2htmlLogger = $sp2htmlLogger;
   }
   
   
   /**
    * linkToWidget : assegna un widget di tipo label da aggiornare sullo stato dell'upload
    * @param widget $widget puntatore al widget 
    */
   function linkToWidget(&$widget) 
   {
      $this->labelUpdate = $widget;
   }
   
    
   /**
    * startTaskAlone : funzione per l'upload a chiamata indipendente
    * @param string $localDir directory locale da scansionare 
    * @param string $localExt estensione dei file da uploadare
    * @param string $remoteUrl url remoto in cui uploadare i file 
    */
   function startTaskAlone($localDir, $localExt, $remoteUrl)
   { 
      $endString = "<sp2html_remoteupload>END</sp2html_remoteupload>";
      
      if(!is_dir($localDir)) { 
         echo $endString;
         return;
      }
      
      if($dh = opendir($localDir)) {
         
         while(($file = readdir($dh)) !== false) {
            //sleep(5);
            $fExt = substr($file, strlen($file)-strlen($localExt));
            $file = substr($file, 0, strlen($file)-strlen($localExt));

            //echo "fExt: $fExt, file: $file, localExt: $localExt";
            if($fExt != $localExt) {
               continue;
            }
            
            //echo "esistenza ".$localDir.$file.'.rupload';
            if(! file_exists($localDir.$file.'.rupload')) { 
               continue;
            }

            //echo "SI";
            $dataUpload = $this->parseRUFile($localDir.$file.'.rupload');
            
            if($dataUpload == null) {     // file rupload corrotto 
               rename($localDir.$file.'.rupload', $localDir.$file.'.rupload.error.'.time());
               continue;
            }
            
            echo "<sp2html_remoteupload>".$dataUpload['numdoc'].'||'.substr($dataUpload['azienda'], 0, 20)."</sp2html_remoteupload>";
            
            /* costruisco l'array post */
            $postData['codcli'] = $dataUpload['codcli'];
            $postData['piva'] = $dataUpload['piva'];
            $postData['email'] = $dataUpload['email'];
            $postData['data'] = $dataUpload['data'];
            $postData['azienda'] = $dataUpload['azienda'];
            $postData['numdoc'] = $dataUpload['numdoc'];
            $postData['tot'] = $dataUpload['tot'];
            $postData['filedata'] = "@".$localDir.$file.$localExt;
            
            $uploadStatus = $this->curlUpload($remoteUrl, $postData, $dataUpload['user'], $dataUpload['password']);
            
            if($uploadStatus == '<OK />') {
               /* rinomino i file in rupload-ok */
               rename($localDir.$file.'.rupload', $localDir.$file.'.rupload.OK.'.time());
            } else {
               //echo "status: ".$uploadStatus;
            }
            
         } // while
         
         echo "<sp2html_lastupload>".$dataUpload['numdoc'].'||'.substr($dataUpload['azienda'], 0, 20)."</sp2html_lastupload>".$endString;
      } // if dh
      
      return 0;
   }

   
   /**
    * parseRUFile : effettua il parsing dei file .rupload che contengono le informazioni per l'invio
    * @param string $fName nome del file da parsare 
    */
   function parseRUFile($fName)
   {
      $fp = fopen($fName, 'rb'); 
      if($fp == null) return null;
      
      $tmpData = fread($fp, 1024);
      
      fclose($fp);
      
      list($codCli, $pIva, $email, $data, $azienda, $numDoc, $tot, $user, $password) = explode("||", $tmpData);
      
      if($codCli == '' ||
         $pIva == '' ||
         $email == '' ||
         $data == '' ||
         $azienda == '' ||
         $numDoc == '' ||
         $tot == '' ||
         $user == '' ||
         $password == '') return null;
         
      return array(  'codcli' => $codCli, 
                     'piva' => $pIva,
                     'email' => $email,
                     'data' => $data,
                     'azienda' => $azienda,
                     'numdoc' => $numDoc,
                     'tot' => $tot,
                     'user' => $user,
                     'password' => $password);
                     
   }
   
   
   /**
    * curlUpload : esegue l'upload di un file 
    * @param string $url indirizzo web remoto
    * @param string $postData il file da inviare e altri dati post
    * @param array $aOpt array di opzioni aggiuntive da passare a curl
    */
   function curlUpload($url, $postData, $user, $password)
   {
      $ch = curl_init();
      
      /* per sicurezza forzo il timeout a 120sec */
      curl_setopt($ch, CURLOPT_TIMEOUT, 120);
      
      // curl_setopt($ch, CURLOPT_VERBOSE, true);
      curl_setopt($ch, CURLOPT_FAILONERROR, true);
      curl_setopt($ch, CURLOPT_POST, true );  
      curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
      curl_setopt($ch, CURLOPT_USERPWD, $user.':'.$password);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
      
      $retData = curl_exec($ch);
      
      if(curl_errno($ch)!=0) {
         curl_close($ch);
         return '';
      }
      
      curl_close($ch);
      return $retData;
   }
   

   /**
    * startRemoteUpload : effettua il queuing di un file per l'upload remoto e attiva il subprocesso 
    *                     di upload se non gi? presente
    * @param string $fname file da mettere in coda 
    */
   function startRemoteUpload($fName, $codCli, $piva, $email, $data, $azienda, $numDoc, $tot)
   {
      $this->sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : inizioprocedura invio documento ".$numDoc." in corso ...");
      
      $newName = $this->createRUFile($fName, $codCli, $piva, $email, $data, $azienda, $numDoc, $tot);
      
      /* verifico se il process handler ? gi? attivo */
      if($this->procHandle != null) {
         $this->sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : handle per processo RYUpload già esistente, esco e attendo che si liberi...");
         return;
      }
      
      /* altrimenti apro il process handle */
      $cmd = 'php -r \'require("lib/sp2htmlRemoteUpload.php");$r=new sp2htmlRemoteUpload();$r->startTaskAlone("'.$this->sp2htmlConf->getValue('directory.remoteupload', $this->sp2htmlLogger).'/", ".upl", "'.$this->sp2htmlConf->getValue('params.remoteupload.curl.url', $this->sp2htmlLogger).'"); \' ';
      $this->sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : apro handle: ".$cmd);

      $this->procHandle = popen($cmd, 'r');
      
      if($this->procHandle != null) {     // attivo il timeout gtk per il check dello status
         $this->gtkTimeout = gtk::timeout_add(1000, array(&$this, 'updateRUploadStatus'));
      } else {
         if($this->gtkTimeout != null) 
            gtk::timeout_remove($this->gtkTimeout);
      }

   }
   
   
   /**
    * updateRUploadStatus : viene chiamata da un timer gtk per aggiornare la label contenente
    *                       lo status di upload dei file su server remoto.
    */
   function updateRUploadStatus()
   {
      /* se il procHandle ? null ritorno true per staccare il timeuout */
      if($this->procHandle == null) return false;
      
      /* leggo dalla pipe nbyte */
      stream_set_timeout($this->procHandle, 0, 200);
      $tmpData = fread($this->procHandle, 1024);
      // echo "letto: ".$tmpData."\nrilevo: ".preg_replace("/.*<sp2html_remoteupload>(.*)<\/sp2html_remoteupload>.*/", "\$1", $tmpData)."\n";
      
      /* se il contenuto è END chiudo la pipe */
      if(preg_replace("/.*<sp2html_remoteupload>(.*)<\/sp2html_remoteupload>.*/", "\$1", $tmpData) == 'END') {
         pclose($this->procHandle);
         $this->procHandle = null;
         $tmpData = preg_replace("/.*<sp2html_lastupload>(.*)<\/sp2html_lastupload>.*/", "\$1", $tmpData);
         list($numDoc, $azienda) = explode("||", $tmpData); 
         $this->labelUpdate->set_text("completato: ".$numDoc." - ".$azienda);
         return false;
      }
      
      $tmpData = preg_replace("/.*<sp2html_remoteupload>(.*)<\/sp2html_remoteupload>.*/", "\$1", $tmpData);
      list($numDoc, $azienda) = explode("||", $tmpData); 
      $this->labelUpdate->set_text("upload in corso doc: ".$numDoc." - ".$azienda."...");

      return true;      
   }

   
   /**
    * createRUFile : crea i file data e .rupload in base ai dati passati
    *
    * restituisce il nuovo basename file
    */
   function createRUFile($fName, $codCli, $piva, $email, $data, $azienda, $numDoc, $tot)
   {
      $this->sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : creazione file Rupload di ".$fName." in corso ...");
      
      $newBasename = $this->sp2htmlConf->getValue('directory.remoteupload', $this->sp2htmlLogger).'/'.time();
      copy($fName, $newBasename.'.upl');
      
      $fp = fopen($newBasename.'.rupload', 'wb');
      fwrite($fp, trim($codCli).'||'.trim($piva).'||'.trim($email).'||'.trim($data).'||'.trim($azienda).'||'.trim($numDoc).'||'.trim($tot).'||'.$this->sp2htmlConf->getValue('params.remoteupload.curl.username', $this->sp2htmlLogger).'||'.$this->sp2htmlConf->getValue('params.remoteupload.curl.password', $this->sp2htmlLogger)."\n");
      fclose($fp);
      
      return $newBasename;
   }
   
   

} // END OF CLASS
?>
