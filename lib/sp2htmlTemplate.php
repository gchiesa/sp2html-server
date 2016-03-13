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
* sp2htmlTemplate
*
* This library contains classes to read and manage the template files
* @package sp2html
* @author Giuseppe Chiesa <gchiesa@smos.org>
* @version 1.0
* @abstract
* @copyright GNU Public License
*/

/**
 * sp2htmlTemplate
 * 
 * Class to read and manage the templates
 */
 class sp2htmlTemplate {
 
	 var $tplFiles;
	 
	 
	 /**
	  * sp2htmlTemplate : costruttore classe
	  */
	 function sp2htmlTemplate()
	 {
		 $this->tplFiles = array();
	 }
	 
	 
	 /**
	  * addTemplate : aggiunge un template passando il nome del file
	  * @param string $tplFile file contenente il template 
	  */
	 function addTemplate($tplFile)
	 {
		 $tmpTplType = $this->loadFile($tplFile);
		 
		 if($tmpTplType == null) 
		 	return false;
		 
		 if(! $this->compileTemplate($tmpTplType)) 
		 	return false;
			
		 return true;
	 }
	 
	 
	 /**
	  * loadFile : carico e pulisco un file template pronto per le fasi successive di parsing
	  * @param string $fileName nome del file 
	  */
	 function loadFile($fileName)
	 {
		 if(!file_exists($fileName)) {
			 return null;
		 }
		 
		 $tmpFile = file($fileName);
		 
		 $resultFile = '';
		 
		 /* pulisco il file dai commenti e rimuovo spazi iniziali e finali dalle righe */
		 foreach($tmpFile as $row) {
			 $row = trim($row);
			 if($row[0] == '#') continue;
			 $resultFile .= $row;
		 }
		 
		 $tmpTplType = $this->checkTemplateType($resultFile);
		 
		 if($tmpTplType == null) return null;
		 
		 $this->tplFiles[$tmpTplType] = array('rawdata' =>  $resultFile);
		 
		 return $tmpTplType;
	 }
	 
	 
	 /**
	  * checkTemplateType : controlla il tipo di template in base al contenuto di <sp2html_template />
	  * @param string $data contenuto del file
	  */
	 function checkTemplateType($data)
	 {
		 $tmpValues = array();
		 if(!preg_match("/<sp2html_template>.*<\/sp2html_template>/", $data, $tmpValues)) {
			 return null;
		 }
		 
		 return preg_replace("/<sp2html_template>(.*)<\/sp2html_template>/", "\$1", $tmpValues[0]); 
		 // return $tmpValues[0];
	 }
	 

	 /**
	  * compileTemplate : compila in modo che possa essere passato a eval il template caricato
	  */
	 function compileTemplate($templateType)
	 {
		 if($this->tplFiles[$templateType]['rawdata'] == null) {
			 return false;
		 }
		 
		 /*--- HEADER ---*/
		 /* step 1: addslashes & doubleslaslashes */
		 $this->tplFiles[$templateType]['rawdata'] = addslashes($this->tplFiles[$templateType]['rawdata']);
		 
		 /* step 2: prelevo sezione header */
		 $tmpData = preg_replace("/.*<sp2html_header>(.*)<\/sp2html_header>.*/", "\$1",  $this->tplFiles[$templateType]['rawdata']);
		 if($tmpData == null) {
			 $this->tplFiles[$templateType]['header'] = null;
		 } else {
			 $this->tplFiles[$templateType]['header'] = $tmpData;
			 //$this->tplFiles[$templateType]['header'] = addslashes($this->tplFiles[$templateType]['header']);
		 }
		 

		 /*--- BODYHEADER ---*/
		 $tmpData = preg_replace("/.*<sp2html_bodyheader>(.*)<\/sp2html_bodyheader>.*/", "\$1", $this->tplFiles[$templateType]['rawdata']);
		 if($tmpData ==null) {
			 $this->tplFiles[$templateType]['bodyheader'] = null;
		 } else {
			 $this->tplFiles[$templateType]['bodyheader'] = $tmpData;
		 }
		 
		 if(  $templateType == 'fat-rendering' 
            || $templateType == 'ord-rendering') {
			 $this->tplFiles[$templateType]['bodyheader'] = preg_replace("/(.*)\[\[(.*)\]\](.*)/U", "$1\".$2.\"$3", $this->tplFiles[$templateType]['bodyheader']);
			 //$this->tplFiles[$templateType]['bodyheader'] = addslashes($this->tplFiles[$templateType]['bodyheader']);
			 $this->tplFiles[$templateType]['bodyheader'] = preg_replace("/(.*)\{\{(.*)\}\}(.*)/U", "$1\".\$sp2htmlData->docOverall['$2'].\"$3", $this->tplFiles[$templateType]['bodyheader']);
			 $this->tplFiles[$templateType]['bodyheader'] = str_replace('.$', '.\$', $this->tplFiles[$templateType]['bodyheader']); 
		 }


		 /*--- BODY ---*/
		 $tmpData = preg_replace("/.*<sp2html_body>(.*)<\/sp2html_body>.*/", "\$1", $this->tplFiles[$templateType]['rawdata']);
		 if($tmpData == null) {
			 $this->tplFiles[$templateType]['body'] = null;
		 } else {
			 $this->tplFiles[$templateType]['body'] = $tmpData;
		 }
		 
		 if(  $templateType == 'fat-rendering'
            || $templateType == 'ord-rendering') {
			 $this->tplFiles[$templateType]['body'] = preg_replace("/(.*)\[\[(.*)\]\](.*)/U", "$1\".$2.\"$3", $this->tplFiles[$templateType]['body']);
			 //$this->tplFiles[$templateType]['body'] = addslashes($this->tplFiles[$templateType]['body']);
			 $this->tplFiles[$templateType]['body'] = preg_replace("/(.*)\{\{(.*)\}\}(.*)/U", "$1\".\$corpo['$2'].\"$3", $this->tplFiles[$templateType]['body']);
			 $this->tplFiles[$templateType]['body'] = str_replace('.$', '.\$', $this->tplFiles[$templateType]['body']); 
		 }
		 

		 /*--- BODYDATA ---*/
		 $tmpData = preg_replace("/.*<sp2html_bodydata>(.*)<\/sp2html_bodydata>.*/", "\$1", $this->tplFiles[$templateType]['rawdata']);
		 if($tmpData == null) {
			 $this->tplFiles[$templateType]['bodydata'] = null;
		 } else {
			 $this->tplFiles[$templateType]['bodydata'] = $tmpData;
		 }
		 
		 if($templateType == 'fat-rendering') {
			 $this->tplFiles[$templateType]['bodydata'] = preg_replace("/(.*)\[\[(.*)\]\](.*)/U", "$1\".$2.\"$3", $this->tplFiles[$templateType]['bodydata']);
			 //$this->tplFiles[$templateType]['bodydata'] = addslashes($this->tplFiles[$templateType]['bodydata']);
			 $this->tplFiles[$templateType]['bodydata'] = preg_replace("/(.*)\{\{(.*)\}\}(.*)/U", "$1\".\$sp2htmlData->docOverall['$2'].\"$3", $this->tplFiles[$templateType]['bodydata']);
			 $this->tplFiles[$templateType]['bodydata'] = str_replace('.$', '.\$', $this->tplFiles[$templateType]['bodydata']); 
		 }
		 
		 
		 /*--- BODYIVA ---*/
		 $tmpData = preg_replace("/.*<sp2html_bodyiva>(.*)<\/sp2html_bodyiva>.*/", "\$1", $this->tplFiles[$templateType]['rawdata']);
		 if($tmpData == null) {
			 $this->tplFiles[$templateType]['bodyiva'] = null;
		 } else {
			 $this->tplFiles[$templateType]['bodyiva'] = $tmpData;
		 }
		 
		 if($templateType == 'fat-rendering') {
			 $this->tplFiles[$templateType]['bodyiva'] = preg_replace("/(.*)\[\[(.*)\]\](.*)/U", "$1\".$2.\"$3", $this->tplFiles[$templateType]['bodyiva']);
			 //$this->tplFiles[$templateType]['bodyiva'] = addslashes($this->tplFiles[$templateType]['bodyiva']);
			 $this->tplFiles[$templateType]['bodyiva'] = preg_replace("/(.*)\{\{(.*)\}\}(.*)/U", "$1\".\$campoiva['$2'].\"$3", $this->tplFiles[$templateType]['bodyiva']);
			 $this->tplFiles[$templateType]['bodyiva'] = str_replace('.$', '.\$', $this->tplFiles[$templateType]['bodyiva']); 
		 }
		 
		 	
		 /*--- BODYFOOTER ---*/
		 $tmpData = preg_replace("/.*<sp2html_bodyfooter>(.*)<\/sp2html_bodyfooter>.*/", "\$1", $this->tplFiles[$templateType]['rawdata']);
		 if($tmpData == null) {
			 $this->tplFiles[$templateType]['bodyfooter'] = null;
		 } else {
			 $this->tplFiles[$templateType]['bodyfooter'] = $tmpData;
		 }
		 
		 if(  $templateType == 'fat-rendering'
            || $templateType == 'ord-rendering') {
			 $this->tplFiles[$templateType]['bodyfooter'] = preg_replace("/(.*)\[\[(.*)\]\](.*)/U", "$1\".$2.\"$3", $this->tplFiles[$templateType]['bodyfooter']);
			 //$this->tplFiles[$templateType]['bodyfooter'] = addslashes($this->tplFiles[$templateType]['bodyfooter']);
			 $this->tplFiles[$templateType]['bodyfooter'] = preg_replace("/(.*)\{\{(.*)\}\}(.*)/U", "$1\".\$sp2htmlData->docOverall['$2'].\"$3", $this->tplFiles[$templateType]['bodyfooter']);
			 $this->tplFiles[$templateType]['bodyfooter'] = str_replace('.$', '.\$', $this->tplFiles[$templateType]['bodyfooter']); 
		 }

		
		 /*--- FOOTER ---*/
		 $tmpData = preg_replace("/.*<sp2html_footer>(.*)<\/sp2html_footer>.*/", "\$1", $this->tplFiles[$templateType]['rawdata']);
		 if($tmpData == null) {
			 $this->tplFiles[$templateType]['footer'] = null;
		 } else {
			 $this->tplFiles[$templateType]['footer'] = $tmpData;
		 }
		 
		 if(  $templateType == 'fat-rendering'
            || $templateType == 'ord-rendering') {
			 $this->tplFiles[$templateType]['footer'] = preg_replace("/(.*)\[\[(.*)\]\](.*)/U", "$1\".$2.\"$3", $this->tplFiles[$templateType]['footer']);
			 //$this->tplFiles[$templateType]['footer'] = addslashes($this->tplFiles[$templateType]['footer']);
			 $this->tplFiles[$templateType]['footer'] = preg_replace("/(.*)\{\{(.*)\}\}(.*)/U", "$1\".\$sp2htmlData->docOverall['$2'].\"$3", $this->tplFiles[$templateType]['footer']);
			 $this->tplFiles[$templateType]['footer'] = str_replace('.$', '.\$', $this->tplFiles[$templateType]['footer']); 
		 }

		 return true;
	 }
	 
	 
	 
} // END OF CLASS	 
?>
