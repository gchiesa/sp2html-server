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
* sp2htmlParser
*
* This library contains classes to permit parsing of document in native format SpigaX
* @package sp2html
* @author Giuseppe Chiesa <gchiesa@smos.org>
* @version 1.0
* @abstract
* @copyright GNU Public License
*/

/**
 * sp2htmlParser
 * 
 * Class to execute parsing of the template and documents in native format SpigaX
 */
class sp2htmlParser {

   /**
    * sp2htmlParser() : main constructor
    * wich create the object used by class.
    */
   function sp2htmlParser()
   {
   }
   
   /** 
    * parseTemplate() : this function parse the template file in spigax format to fill the data structs
    * @param object $sp2htmlData pointer to a object Data of sp2html
    * @param object $sp2htmlLogger pointer to a resource logger
    */
   function parseTemplate(&$sp2htmlData, &$sp2htmlLogger)
   {
      if(!($if = fopen($sp2htmlData->origFileTemplate, "rb")))
         $sp2htmlLogger->exitError(__CLASS__."::".__FUNCTION__.": impossibile aprire il file ".$sp2htmlData->origFileTemplate." ");
      fclose($if);
      
      $tmp_array = file($sp2htmlData->origFileTemplate);
      $parsed_iva = 0;
      $idx = 0;

      foreach($tmp_array as $riga)
      {
         $riga = chop($riga);
         if(strstr($riga, "|C001|")) {
            /* RIGA LAYOUT CORPO, la splitto */
            $tmp_cp_array = explode("|C001|", $riga);
            foreach($tmp_cp_array as $tmp_corpo)
               array_push($sp2htmlData->templateBody, str_replace("|", "", $tmp_corpo));

            $idx++;
         } else if(strstr($riga, "|C003|")) {
            /* RIGA LAYOUT CORPO IVA */
            if($parsed_iva) {
               $idx++;
               continue;
            }
            $tmp_cp_array = explode("|C003|", $riga);
            foreach($tmp_cp_array as $tmp_corpo)
               array_push($sp2htmlData->templateIva, str_replace("|", "", $tmp_corpo));

            $parsed_iva = 1;
            $idx++;
         } else if(strstr($riga, "|V")) {
            /* VARIABILE LAYOUT, la inserisco nell'array */
            $sp2htmlData->templateOverall[$idx] = str_replace("|", "", $riga);
            $idx++;
         }
      } // end foreach
      return 0;
   }
   
   /**
    * parseDocPages(&$sp2htmlData) : this function parse the printed document in spigax format and store 
    * it in array of pages
    * @param object $sp2htmlData a pointer to a sp2htmlData class
    * @param object $sp2htmlLogger pointer to a sp2htmlLogger object
    */
   function parseDocPages(&$sp2htmlData, &$sp2htmlLogger)
   {
      if( ($if = fopen($sp2htmlData->origFileDoc, "rb"))==0 )
         $sp2htmlLogger->exitError(__CLASS__."::".__FUNCTION__.": unable to open file <".$sp2htmlData->origFileDoc."> ");
      fclose($if);

      $tmp_array = file($sp2htmlData->origFileDoc);
   
      $pagine = 0;
      foreach($tmp_array as $riga)
         if(strstr($riga, "--END--"))
            $pagine++;
   
      if( $pagine == 0)
         return __CLASS__."::".__FUNCTION__.": errore nel conteggio pagine ($pagine).\n";
   
      $pag = 0;
      $tmp_page = array();
      reset($tmp_array);
      foreach($tmp_array as $riga)
      {
         if(strstr($riga, "--END--"))
         {  array_push($sp2htmlData->docPages, $tmp_page);
            $pag++;
            unset($tmp_page);
            $tmp_page = array();
            continue;
         }
         array_push($tmp_page, $riga);
      }
      return 0;
   }


   /**
    * createAssocData(&$sp2htmlData) : process and reassoc template data and document data to create a associative hash 
    * wich contains variable to use in final template in format :
    * KEY=>VALUE where 
    * KEY is the variable name in template of spigax format (es. V106, V225)
    * VALUE is the associated value.
    * @param object $sp2htmlData pointer to sp2htmlData object
    */
   function createAssocData(&$sp2htmlData)
   {
      $idx = 0;
      $tbrg = 0;
      $tbrgiva = 0;
   
      foreach($sp2htmlData->docPagesCurrent as $riga)
      {
         chop($riga);
         $riga = str_replace("\r", "", $riga);
         $riga = str_replace("\n", "", $riga);
         if(strstr($riga, ":-:"))
         {
            /* parsing del body */
            $new_rec = array();
            $tmp_record = explode(":-:", $riga);
            for($k=0;$k<count($tmp_record);$k++)
            {  $key = $sp2htmlData->templateBody[$k];
               $new_rec[$key] = $tmp_record[$k];
            }
            $sp2htmlData->docBody[$tbrg] = $new_rec;
            $tbrg++;
            $idx++;
         }
         else if(strstr($riga, ":+:"))
         {
            /* parsing del campo iva */
            $new_rec = array();
            $tmp_record = explode(":+:", $riga);
            for($k=0;$k<count($tmp_record);$k++)
            {  $key = $sp2htmlData->templateIva[$k];
               $new_rec[$key] = $tmp_record[$k];
            }
            $sp2htmlData->docIva[$tbrgiva] = $new_rec;
            $tbrgiva++;
            $idx++;
         }
         else
         {  $key = $sp2htmlData->templateOverall[$idx];
            $sp2htmlData->docOverall[$key] = $riga;
            $idx++;
         }
      }
      return 0;
   }

}// END CLASS
?>


