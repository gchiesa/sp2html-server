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
* sp2htmlNet
*
* Contiene le classi per la comunicazione client-server del software 
* @package sp2html
* @author Giuseppe Chiesa <gchiesa@smos.org>
* @version 1.0
* @abstract
* @copyright GNU Public License
*/

require_once("lib/sp2htmlGuiPrompt.php");

/* definitions */
define("USELIBMCRYPT", 0);
define("WELCOME", "CONNECTED -- Welcome to Sp2Html - Server - Version 1.0\r\n\0");
define("SRVSOCKETMAXLEN", 2048);
define("TPL", 0);
define("DOC", 1);
define("STOP", "\0");

/* GTK definitions */
define("GTKSOCKTIMEACCEPT", 1000);
define("GTKSOCKTIMERECEIVE", 100);
define("GTKSOCKMAXREAD", 4096);
define("GTKSOCKREADJUSTTHIS", 1);

/**
 * sp2htmlServer
 * 
 * Classe per la gestione della comunicazione su protocollo proprietario lato server
 */
class sp2htmlServer {
   var $port;
   var $remoteAddr;
   var $remotePort;
   var $stepCom;
   var $flEncoding;
   var $flCloseSocket;
   var $flReadyProcess;
   var $authData;
   var $encKey;
   var $tplFname;
   var $tplName;
   var $tplSize;
   var $tplMd5;
   var $tplData;
   var $docFname;
   var $docMd5;
   var $docData;
   var $docSize;
   var $gtkSock;
   var $gtkConn;
   var $gtkFlSendWelcome;
   var $gtkTimeoutCommunication;
   var $gtkTimeoutAccept;
   var $gtkMessage;
   var $gtkBuffer;
   var $sp2htmlLogger;
   var $sp2htmlConf;
   var $controlDownload;
   var $controlStatus;
   
   /**
    * sp2htmlServer($port, &$sp2htmlLogger) : constructor of class sp2htmlServer
    * @param int $port port to open listen socket
    */
   function sp2htmlServer($port, &$sp2htmlLogger, $sp2htmlConf, &$controlDownload, &$controlStatus)
   {
      $this->port = $port;
      $this->stepCom = 0;
      $this->flCloseSocket = false;
      $this->goingLock = 0;
      $this->flEncoding = false;
      $this->flReadyProcess = false;
      $this->authData = array("pippo"=>"pluto");
      $this->encKey = "";
      $this->tplData = "";
      $this->docData = "";
      $this->sp2htmlLogger = $sp2htmlLogger;
      $this->sp2htmlConf = $sp2htmlConf;
      $this->controlDownload = $controlDownload;
      $this->controlStatus = $controlStatus;
   }

   /**
    * resetCommFlags() : reset the communication flags and data 
    */
   function resetCommFlags()
   {
      $this->stepCom = 0;
      $this->goingLock = 0;
      $this->flEncoding = false;
      $this->encKey = "";
      $this->tplName = "";
      $this->tplSize = 0;
      $this->tplMd5 = "";
      $this->tplData = "";
      $this->docData = "";
      $this->docSize = 0;
      $this->docMd5 = "";
      $this->gtkMessage = "";
      $this->gtkBuffer = "";
      
      return 0;
   }

   /**
    * processMsg($msg) : this function parse and evaluate the messages of communication between 
    * client and server and generate reply messages.
    * @param string $msg message getted from the client 
    * @return string message to send at client in reply
    */
   function processMsg($msg)
   {
      $retMsg = "";
      if(strncmp($msg, "QUIT", strlen("QUIT"))==0) {          // richiesta di uscita
         $this->flCloseSocket = true;
         return ($retMsg = "QUIT_REQUEST, BYE".STOP);
      } 
      
      if($this->stepCom==4) {          // se sono in ricezione dati template
         $this->tplData = $this->decode($msg);
         if($this->checkData(TPL)<0) {
            $this->stepCom = 3;
            return ($retMsg = "PRESENT_TEMPLATE (md5 not match in prev transmission)".STOP);
         }
         $this->stepCom = 5;
         return ($retMsg = "PRESENT_DATA".STOP);
      }

      if($this->stepCom==6) {          // se sono in ricezione dati documento
         $this->docData .= $this->decode($msg);
         if($this->checkData(DOC)<0) {
            $this->stepCom = 5;
            return ($retMsg = "PRESENT_DATA (md5 not match in prev transmission)".STOP);
         }
         $this->stepCom = 7;
         $this->flCloseSocket = true;
         $this->flReadyProcess = true;
         return ($retMsg = "THANKS_BYE".STOP);
      }
      

      if($this->flEncoding) {
         //echo "codificato: ".$msg."\n";
         $msg = $this->decode($msg);
         //echo "decodificato con Key ($this->encKey): ".$msg."\n";
      }

      if(strncmp($msg, "HI", strlen("HI"))==0) {            // fase 1: apertura comunicazione
         if($this->stepCom!=0) {
            $retMsg = "ERROR_IN_COMMUNICATION".STOP;
            $this->flCloseSocket = true;
         } else {
            $this->stepCom = 1;
            $retMsg = "IDENTIFY".STOP;
         }
      } else if(strncmp($msg, "ID:", strlen("ID:"))==0) {   // fase 2: autenticazione
         if($this->stepCom!=1) {
            $retMsg = "ERROR_IN_COMMUNICATION".STOP;
            $this->flCloseSocket = true;
         } else {
            $this->stepCom = 2;
            list($a, $b) = explode(",", $msg);
            list($null, $id) = explode(":", $a);
            list($null, $hash) = explode(":", $b);
            if($this->checkAuth($id, $hash)<0) {
               $retMsg = "LOGIN_FAILED";
               $this->flCloseSocket = true;
            } else {
               $retMsg = "START_ENCODING".STOP;
               $this->flEncoding = true;
               $this->encKey = $this->authData[$id];
            }
         }
      } else if(strncmp($msg, "START_ENCODING_OK", strlen("START_ENCODING_OK"))==0) {  // fase 3: negoziazione encoding
         if($this->stepCom!=2) {
            $retMsg = "ERROR_IN_COMMUNICATION".STOP;
            $this->flCloseSocket = true;
         } else {
            $this->stepCom = 3;
            $retMsg = "PRESENT_TEMPLATE".STOP;
         }
      } else if(strncmp($msg, "TPL:", strlen("TPL:"))==0) {     // fase 4: presentazione template
         if($this->stepCom!=3) {
            $retMsg = "ERROR_IN_COMMUNICATION".STOP;
            $this->flCloseSocket = true;
         } else {
            list($a, $b, $c) = explode(",", trim($msg));
            list($null, $this->tplName) = explode(":", $a);
            list($null, $this->tplSize) = explode(":", $b);
            list($null, $this->tplMd5) = explode(":", $c);
            if($this->tplName=="" || !is_numeric($this->tplSize) || strlen($this->tplMd5)!=32) {
               // echo "tpl: |$this->tplName| - size: |$this->tplSize| - md5: |$this->tplMd5|\n";
               $retMsg = "MALFORMED_TEMPLATE_HEADER".STOP;
               $this->flCloseSocket = true;
            } else {
               // echo "tpl: |$this->tplName| - size: |$this->tplSize| - md5: |$this->tplMd5|\n";
               $retMsg = "SEND_TEMPLATE".STOP;
               $this->tplData = "";
               $this->stepCom = 4;
            }
         }
      } else if(strncmp($msg, "MD5:", strlen("MD5:"))==0) {       // fase 5: presentazione dati
         if($this->stepCom!=5) {
            $retMsg = "ERROR_IN_COMMUNICATION".STOP;
            $this->flCloseSocket = true;
         } else {
            list($a, $b) = explode(",", trim($msg));
            list($null, $this->docMd5) = explode(":", $a);
            list($null, $this->docSize) = explode(":", $b);
            if(!is_numeric($this->docSize) || strlen($this->docMd5)!=32) {
               // echo "MD5:|$this->docMd5|, Size:|$this->docSize|\n";
               $retMsg = "MALFORMED_DOC_HEADER".STOP;
               $this->flCloseSocket = true;
            } else {
               $retMsg = "SEND_DATA".STOP;
               $this->docData = "";
               $this->stepCom = 6;
            }
         }
      } else {
         $retMsg = "UNKNOW_DATA".STOP;
      }

      // echo "inviato: |".$retMsg."|\n";
      return $retMsg;
   }

   /**
    * chechAuth($id, $hash) : check and authenticate the remote client.
    * @param string $id user-id of remote client
    * @param string $hash md5-hash of password
    * @return mixed 0 if OK, -1 if wrong authentication
    */
   function checkAuth($id, $hash)
   {
      if(md5($this->authData[$id])==$hash)
         return 0;

      return -1;
   }
   
   /**
    * encode($txt) : encode the text give by param with base64 & 3DES encoding
    * @param string $txt text to encode
    * @return string data encoded
    */
   function encode($txt)
   {
      if(USELIBMCRYPT) 
         $retData = base64_encode(@mcrypt_ecb(MCRYPT_3DES, $this->encKey, $txt, MCRYPT_ENCRYPT));
      else 
         $retData = base64_encode($txt);
         
      return $retData;
   }

   /**
    * decode($txt) : decode the gived text
    * @param string $txt text to decode
    * @return string text decoded
    */
   function decode($txt)
   {
      if(USELIBMCRYPT)
         $retData = @mcrypt_ecb(MCRYPT_3DES, $this->encKey, base64_decode($txt), MCRYPT_DECRYPT);
      else
         $retData = base64_decode($txt);
         
      return $retData;
   }
      
   /**
    * checkData($type) : verify the integrity of data received 
    * @param string $type data type to select verification
    * @return string 0 if OK, -1 if wrong check
    */
   function checkData($type)
   {
      if($type == TPL) {
         $this->tplData = substr($this->tplData, 0, $this->tplSize); // taglio il file per evitare bytes in piu'
         $this->tplFname = "tpl-".sprintf("%04s", rand(0, 9999));
         // echo "\n---\ncalcolo md5 (".$this->tplMd5.") su bytes: |".$this->encode($this->tplData)."|\n---\n";
         if(md5($this->encode($this->tplData))!=$this->tplMd5) {
            return -1;
         }
         // file_put_contents($this->tplFname, $this->tplData); SOLO >=PHP5
         $fp = fopen($this->sp2htmlConf->getValue("directory.temp", $this->sp2htmlLogger).'/'.$this->tplFname, "wb");
         fwrite($fp, $this->tplData);
         fclose($fp);
      } else {
         $this->docData = substr($this->docData, 0, $this->docSize);
         $this->docFname = "doc-".sprintf("%04s", rand(0, 9999));
         if(md5($this->encode($this->docData))!=$this->docMd5) {
            return -1;
         }
         // file_put_contents($this->docFname, $this->docData); SOLO >= PHP5
         $fp = fopen($this->sp2htmlConf->getValue("directory.temp", $this->sp2htmlLogger).'/'.$this->docFname, "wb");
         fwrite($fp, $this->docData);
         fclose($fp);
      }
      
      return 0;
   }
   
   /** 
    * gtkStart() : main routine to build the server and make it in listen status
    */
   function gtkStart()
   {
      $this->gtkSock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
      socket_set_nonblock($this->gtkSock);
      if(!socket_bind($this->gtkSock, 0, $this->port)) {
         $this->sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : impossibile effettuare il bind, uscita forzata.");
         die("Si e' verificato un errore nella procedura di avvio, verificare i log.\n\n");
      }
      socket_listen($this->gtkSock, 5);
      $this->gtkTimeoutAccept = gtk::timeout_add(GTKSOCKTIMEACCEPT, array(&$this, 'gtkCheckConnQueue'));
      $this->sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : socket: ".$this->gtkSock." attivata su porta ".$this->port);
      $this->controlStatus->set_text("Server attivo su porta : ".$this->port);
   }

   /** 
    * gtkCheckConnQueue() : this function verify if in backlog queue of server there are ready connection
    * to serve.
    * @return true|false always true to keep the timeout in gtk main thread.
    */
   function gtkCheckConnQueue()
   {  
      if($this->gtkConn != NULL) return true; // se gia' presente una connessione in corso non eseguo nuovamente l'accept...
      $this->gtkConn = @socket_accept($this->gtkSock);
      if($this->gtkConn == false) {
         $this->gtkConn = NULL;
         if($this->gtkSock)
            $this->controlStatus->set_text("Server attivo su porta: ".$this->port);
         else 
            $this->controlStatus->set_text("**Server DOWN**");
            
         return true;
      } else {
         // connessione accettata
         socket_getpeername($this->gtkConn, $this->remoteAddr, $this->remotePort);
         $this->gtkFlSendWelcome = true;
         $this->resetCommFlags();
         $this->gtkTimeoutCommunication = gtk::timeout_add(GTKSOCKTIMERECEIVE, array(&$this, 'gtkCommunicate'));
         $this->sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : connessione remota da : ".$this->remoteAddr.":".$this->remotePort);
         $this->controlStatus->set_text("connessione da : ".$this->remoteAddr." porta: ".$this->remotePort);
      } // end if..else..     
      
      return true; 
   }

   /**
    * gtkCommunicate() : this function parse the communication between client and server. It send and receive
    * messages
    * @return true|false always true to keep the timeout callback function in the main thread of gtk environment
    */
   function gtkCommunicate()
   {  
      /* sistema antiblocco: salvo lo stato della stepCom precedente */
      $oldStepCom = $this->stepCom; 
      
      if($this->gtkFlSendWelcome) {
         $msg = WELCOME."CONNECTED from: ".$this->remoteAddr.":".$this->remotePort."\r\n";
         socket_write($this->gtkConn, $msg, strlen($msg));
         $this->gtkFlSendWelcome = false;
      }
      
      if($this->stepCom == 4) {     // se sono in modalità ricezione dati binari template ricevo il pacchetto completo
         $this->gtkBuffer = $this->gtkRecvMsg($this->gtkConn, $this->tplSize);
         $this->gtkMessage = $this->gtkBuffer;
         $this->gtkBuffer = "";
      } else if($this->stepCom == 6) { // se sono in modalità ricezione dati binari documento ricevo il pacchetto completo
         $this->gtkBuffer = $this->gtkRecvMsg($this->gtkConn, $this->docSize);
         $this->gtkMessage = $this->gtkBuffer;
         $this->gtkBuffer = "";
      } else {
         $this->gtkBuffer = $this->gtkRecvMsg($this->gtkConn, NULL); // leggo qualcosa dal lato remoto
      
         // echo "\n---\nsituazione gtkBuffer: *".$this->gtkBuffer."*\n---\n";
         /* se non ci sono dati nella socket o se il messaggio non termina con \n non mando nulla */
/*         if(!strstr($this->gtkBuffer, "\n")) {
            return true;
         } 
*/                  
         /* estrapolo il primo chunk encodato dal buffer */
         $this->gtkBuffer = trim($this->gtkBuffer);
      
         list($this->gtkMessage) = explode("\n", $this->gtkBuffer);
         $this->gtkBuffer = substr($this->gtkBuffer, strlen($this->gtkMessage));
         $this->gtkMessage = trim($this->gtkMessage);

         // echo "---\nricevuto: ".$this->gtkMessage."\n---\n";
      }
      
            
      $writeBuff = $this->processMsg($this->gtkMessage); // preparo la risposta adeguata
      
      // echo "+++\nstepcom: ".$this->stepCom.", mando: ".$writeBuff."\n+++\n";

      /* sistema antiblocco: controllo per prevenire blocchi del server: se si ripete piu' di xxx volte lo stesso stato di stepCom chiudo la connessione */
      if($oldStepCom == $this->stepCom) $this->goingLock++;
      if($this->goingLock > 300) {
         $this->sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : situazione di Lock... chiudo connessione.");
         $this->flCloseSocket = true;
      }
      
      socket_write($this->gtkConn, $writeBuff, strlen($writeBuff));
      if($this->flCloseSocket) {
         socket_close($this->gtkConn);
         $this->flCloseSocket = false;
         $this->stepCom = -1;
         $this->gtkConn = NULL;
         gtk::timeout_remove($this->gtkTimeoutCommunication);
      }
      $this->gtkMessage = "";
      $this->sp2htmlLogger->logFile(__CLASS__."::".__FUNCTION__." : comm.status: ".$this->stepCom." Enc.: ".(($this->flEncoding==true)?"Attivo":"Inattivo"));
      if($this->stepCom>0 && $this->stepCom<7) {
         $this->controlDownload->show();
	 $this->controlDownload->set_text('Ricezione in corso ...'.(15 * $this->stepCom).'%');
         $this->controlDownload->set_fraction(15*$this->stepCom/100);
      } else {
         $this->controlDownload->hide();
      }
      
      return true;
   }   
   
   /**
    * gtkRecvMsg($socket) : this function receive a message from client
    * @param resource $socket the socket opened to communication
    * @param string $bytes the number of bytes to read (NULL = default)
    * @return string return the data read from the socket
    */
   function gtkRecvMsg($socket, $bytes)
   {  
      if($bytes == NULL) {
         $tmpBuff = "";
         $tmpBuff = @socket_read($socket, GTKSOCKMAXREAD, PHP_BINARY_READ);
      } else {
         $tmpBuff = "";
         $loops = (int) floor($bytes / GTKSOCKMAXREAD);
         $rest = $bytes % GTKSOCKMAXREAD;
         
         // echo "\n---\nENTRO IN recvmsg -> bytes da ricevere: ".$bytes." | loops: ".$loops." | resto: ".$rest."\n---\n";
         for($k = 0; $k < $loops; $k++) {
            $tmpBuff .= @socket_read($socket, GTKSOCKMAXREAD, PHP_NORMAL_READ);
         }
         $tmpBuff .= @socket_read($socket, $rest, PHP_NORMAL_READ);
      }
      // echo "\n---\nricevuti n. ".strlen($tmpBuff)." bytes\n---\n".$tmpBuff."\n---\nESCO DA rcvMsg\n";
      return $tmpBuff;
   }

} // end class

?>
      

