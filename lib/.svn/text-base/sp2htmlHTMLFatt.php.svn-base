<?
/*********************************************************
* Copyright 2005, 2006 - Giuseppe Chiesa
*
* This file is part of spigax2html.
*
* spigax2html is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* spigax2html is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with spigax2html; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*
* Created by: Giuseppe Chiesa - http://gchiesa.smos.org
*
* Version: 2.0.0
*
* ABSTRACT:
* ---------
* spigax2html.htmlord.php
*
* funzione di creazione pagina fattura.
*
*********************************************************/

if($k==0) {
   eval(" fwrite(\$fout, \"".$sp2htmlConf->tplData['fat-rendering']['header']."\"); ");
} // IF $K = 0


$totPages = count($sp2htmlData->docPages);
if(($k+1) == $totPages)
   $pageBreak = "";
else
   $pageBreak = "class=page";


eval(" fwrite(\$fout, \"".$sp2htmlConf->tplData['fat-rendering']['bodyheader']."\"); "); 


foreach($sp2htmlData->docBody as $corpo) {
eval(" fwrite(\$fout, \"".$sp2htmlConf->tplData['fat-rendering']['body']."\"); "); 
}


eval(" fwrite(\$fout, \"".$sp2htmlConf->tplData['fat-rendering']['bodydata']."\"); "); 


foreach($sp2htmlData->docIva as $campoiva) { 
	eval(" fwrite(\$fout, \"".$sp2htmlConf->tplData['fat-rendering']['bodyiva']."\"); ");	
}

eval(" fwrite(\$fout, \"".$sp2htmlConf->tplData['fat-rendering']['bodyfooter']."\"); "); 

if(($k+1) == $totPages) {
   eval(" fwrite(\$fout, \"".$sp2htmlConf->tplData['fat-rendering']['footer']."\"); ");
}

?>
