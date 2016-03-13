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
* sp2htmlData
*
* This library contains classes to store the main data object of program
* @package sp2html
* @author Giuseppe Chiesa <gchiesa@smos.org>
* @version 1.0
* @abstract
* @copyright GNU Public License
*/

/** 
 * sp2htmlData 
 * 
 * class to store data structures of program
 */
class sp2htmlData {

   var $origFileTemplate;   
   var $templateOverall;
   var $templateBody;
   var $templateIva;
   
   var $origFileDoc;
   var $docOverall;
   var $docBody;
   var $docIva;

   var $docPages;
   var $docPagesCurrent;
   
   var $dataFile;
   
   var $RUMail;      /* mail per RemoteUpload */
   
   /**
    * sp2htmlData() : main constructor create the object
    */
   function sp2htmlData()
   {
      $this->templateOverall = array();
      $this->templateBody = array();
      $this->templateIva = array();
      $this->docOverall = array();
      $this->docBody = array();
      $this->docIva = array();
      $this->docPages = array();
      $this->docPagesCurrent = "";
      $this->origFileTemplate = "";
      $this->origFileDoc = "";
      $this->dataFile = "";
      $this->RUMail = '';
   }
   
   /** 
    * clearData() : this function clear structs filled by other classes
    */
   function clearData()
   {  
      $this->clearTemplate();
      $this->clearDoc();
      $this->clearPages();
   }

   /**
    * clearTemplate() : this function clear the templateData
    */
   function clearTemplate()
   {
      unset($this->templateOverall);
      unset($this->templateBody);
      unset($this->templateIva);
      
      $this->templateOverall = array();
      $this->templateBody = array();
      $this->templateIva = array();
   }
   
   /**
    * clearDoc() : this function clears the doc data
    */
   function clearDoc()
   {
      unset($this->docOverall);
      unset($this->docBody);
      unset($this->docIva);
      
      $this->docOverall = array();
      $this->docBody = array();
      $this->docIva = array();
   }
   
   /** 
    * clearPages() : this function clears pages data
    */
   function clearPages()
   {
      unset($this->docPages);
      unset($this->docPagesCurrent);
      
      $this->docPages = array();
      $this->docPagesCurrent = "";
   }

} // END OF CLASS

?>
