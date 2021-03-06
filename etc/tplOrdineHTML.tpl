# Le righe che iniziano con il tag # vengono intese come commenti.
# ATTENZIONE: poichè il parser non è XML, OGNI riga di commento deve iniziare
# col tag di commento. 
#
# Ogni template deve contenere la dichiarazione iniziale del tipo di template racchiusa tra le seguenti tag:
#
# <sp2html_template>fat-rendering | ord-rendering | ord-printing</sp2html_template>
#
#
# Il modulo deve contenere le seguenti sezioni in base il suo contenuto sia una fattura (template html) 
# o un ordine (template PDML).
# Nel caso sia un template per fattura sono necessarie le seguenti sezioni:
#
# <sp2html_header /> = contiene le intestazioni pre-body dell'html (css e varie)
# <sp2html_bodyheader /> = contiene la parte iniziale del body (logo aziendale modulo, cliente, indirizzi etc)
# <sp2html_body /> = contiene le righe degli articoli che verranno ciclicamente ripetute
# <sp2html_bodydata /> = contiene la parte riepilogativa del modulo della fattura (importo totale, riepiloghi etc)
# <sp2html_iva /> = contiene la parte relativa allo specchietto di riepilogo iva che verrà ciclicamente ripetuta
# <sp2html_bodyfooter /> = contiene la parte finale del modulo fattua
# <sp2html_footer /> = tag per chiusura documento (solitamente non richiede modifiche)
#
# 

#
# Dichiarazione tipo template 
#
<sp2html_template>
ord-rendering
</sp2html_template>


#
# Dichiarazione header delle pagine 
#
<sp2html_header>

      <html>
      <head>
      <style type="text/css">
      .page { page-break-after:always }
      .f1 {font-family:Helvetica;font-size:12pt;}
      .f2 {font-family:Helvetica;font-size:8pt;}
      .f3 {font-family:Helvetica;font-size:6pt;font-variant:small-caps;}
      .f4 {font-family:Helvetica;font-size:8pt;}
      .f5 {font-family:Helvetica;font-size:8pt;}
      .l1 {color:black;height:1px;border-color:black;border-width:1px 0px 0px 0px;border-style:solid;}
      .l2 {color:black;height:1px;border-color:black;border-width:1px 0px 0px 0px;border-style:dotted;}
      .c1 {padding-top:3px;padding-left:3px;padding-right:3px;border-color:black;border-style:solid;border-top-width:0px;border-bottom-width:0px;border-left-width:1px;border-right-width:0px;}
      </style>
      </head>
      <body onload="self.focus();window.print();">

</sp2html_header>      


#
# Parte del corpo documento contenente i dati header, tipo destinatario, destinazione 
# pagamento codice cliente etc.
#
<sp2html_bodyheader>

<table width="630" cellspacing="0" cellpadding="0" border="0" [[$pageBreak]]>
<tr>
   <td width="630" align="left">
      <img src="res/null.png" width="630" height="1" border="0"></td>
</tr>
<tr>
   <td width="630" align="left">

   <table width="630" height="150" cellspacing="0" cellpadding="0" border="0">
   <tr>
   <td width="400" align="left">

      <table width="400" cellspacing="0" cellpadding="0" border="0">
      <tr>
         <td align="left">
            <img src="res/brico.logo.jpg" width="150" height="69" border="0" style="float:left;"><br>
            <div class="f1" style="font-size:9pt;color:#0000cc">
               <b>Andrea Chiesa L. S.r.l.</b></div>
            <div class="f1" style="font-size:9pt;font-weight:bolder;color:#0000cc">
               <b>Market del Legno</b></div>
            <div class="f2" style="font-size:8pt;">
               Viale Marconi Loc.Fenosu<br />
               09170 - Oristano</div>
         </td>
         <td align="left">
            <img src="res/brico.logo2.jpg" width="250" height="50" border="0"><br>
            <div class="f2" style="font-size:8pt;">
               Via Salvo D&#39;acquisto, 5<br>09170 - Oristano (OR)</div>
            <div class="f2">
               Tel. 0783 215022 - Fax 178 274 3480</div>
            <div class="f2" style="font-size:9pt;">
               <b>web: www.marketlegno.it</b></div></td>
      </tr>
      <tr>
         <td align="left" colspan="2">
            <div class="f2" style="font-size:7pt;padding-top:3px;">
               P.IVA/Cod.Fisc.: <b>0064 329 095 0</b><br>
               Cap.Soc. Euro 51.084,00 i.v.
               C.C.I.A.A. OR 110969 Trib. OR 4887 Vol. 4856</div></td>
     </tr>
     </table>

   </td>
   <td width="230" align="left">
      <div style="width:230px;background-color:black;color:white" class="f2"><b>&nbsp;SPETT.LE</b></div>
      <div class="f2">
         {{V006}}<br>
         {{V007}}<br>
         {{V008}} - {{V009}} ({{V010}})
      </div>
   </td>
   </tr>
   </table>

   <center><div class="f1" style="width:630px;margin-top:20px;margin-bottom:20px;border-width:1px;border-style:solid;">ORDINE MATERIALE</div></center>

   <table width="630" cellspacing="0" cellpadding="0" border="0">
   <tr>
      <td valign="top">
      
         <div class="f2">
            <b><font class="f1">Fatturare A:</font></b>
            <br />
            <b>Andrea Chiesa Legnami S.r.l.</b><br />
            Viale Marconi snc, Loc. Fenosu - 09170 - Oristano (OR)<br />
            P.IVA/Cod.Fisc. 00643290950<br />
            Tel. +39 0783 215022 - Fax. +39 1782743480<br />
            BANCA DI ARBOREA - AG. Donigala F. - ABI 08362 - CAB 17400
         </div>
   
      </td>
      <td valign="top">
      
         <div class="f2">
            <b><font class="f1">Destinazione Merce Filiale:</font></b>
            <br />
            <b>Andrea Chiesa Legnami S.r.l. - Bricofer Oristano</b><br />
            Viale Marconi snc, Loc. Fenosu - 09170 - Oristano (OR)<br />
            Tel. +39 0783 215022 - Fax. +39 1782743480
         </div>
      
      </td>
   </tr>
   </table>

   <hr class="l2">
      
   <table width="630" cellspacing="0" cellpadding="0" border="0">
   <tr>
      <td align="left" width="100">
         <div class="f3">Ordine Num.</div>
         <div class="f4">&nbsp;{{V003}}</div></td>
      <td align="left" width="200">
         <div class="f3">Data Documento</div>
         <div class="f4">&nbsp;{{V004}}</div></td>
      <td align="left" width="50">
         <div class="f3">pagina</div>
         <div class="f4">&nbsp;{{V002}} di [[$totPages]]</div></td>
   </tr>
   </table>

   <hr class="l2">

   <table width="630" cellspacing="0" cellpadding="0" border="0">
   <tr>
      <td align="left" valign="top" height="380">
         <img src="res/null.png" border="0" width="1" height="380" style="height:380px;width:1px;"></td>
      <td align="left" valign="top">

         <table width="620" cellspacing="0" cellpadding="0" border="0">
         <tr>
            <td align="center" valign="top" height="12" width="50" bgcolor="black">
               <div style="color:white;" class="f5">&nbsp;Codice</div></td>
            <td align="center" valign="top" width="280" bgcolor="black">
               <div style="color:white;" class="f5">Descrizione</div></td>
            <td align="center" valign="top" width="30" bgcolor="black">
               <div style="color:white;" class="f5">U.m.</div></td>
            <td align="center" valign="top" width="40" bgcolor="black">
               <div style="color:white;" class="f5">Quant.</div></td>
            <td align="center" valign="top" width="70" bgcolor="black">
               <div style="color:white;" class="f5">Prezzo</div></td>
            <td align="center" valign="top" width="40" bgcolor="black">
               <div style="color:white;" class="f5">Sc.%</div></td>
            <td align="center" valign="top" width="80" bgcolor="black">
               <div style="color:white;" class="f5">Importo Tot.</div></td>
         </tr>
			
</sp2html_bodyheader>


#
# Sezione contenente il corpo del documento che verrà ripetuto ciclicamente per ogni articolo
#
<sp2html_body>

         <tr>
            <td align="left" valign="top" width="50" height="12" class="c1"><div class="f2">&nbsp;{{V929}}<div class="f3">&nbsp;({{V127}})</div></div></td>
            <td align="left" valign="top" width="280" height="12" class="c1"><div class="f2">&nbsp;{{V125}} {{V126}}</div></td>
            <td align="left" valign="top" width="30" height="12" class="c1"><div class="f2">&nbsp;{{V105}}</div></td>
            <td align="left" valign="top" width="40" height="12" class="c1"><div class="f2">&nbsp;{{V106}}</div></td>
            <td align="right" valign="top" width="70" height="12" class="c1"><div class="f2">&nbsp;{{V107}}</div></td>
            <td align="right" valign="top" width="40" height="12" class="c1"><div class="f2">&nbsp;{{V108}} &nbsp; {{V109}}</div></td>
            <td align="right" valign="top" width="80" height="12" class="c1"><div class="f2">&nbsp;{{V111}}</div></td>
         </tr>

</sp2html_body>			


#
# Sezione contenente i dati di riepilogo del body, tipo netto e lordo merce, totale modulo etc.
#
<sp2html_bodydata>
# NESSUN DATO QUI PER GLI ORDINI
</sp2html_bodydata>


#
# Sezione contenente i dati del campo iva che verranno ripetuti ciclicamente per ogni riga iva
#
<sp2html_bodyiva>
# NESSUN DATO QUI PER GLI ORDINI
</sp2html_bodyiva>


#
# Sezione finale del corpo del documento
#
<sp2html_bodyfooter>

         <tr>
            <td align="left" colspan="7">
               <hr class="l1"></td>
         </tr>
         </table>
         
      </td>
   </tr>
   </table>
   
   <table width="630" cellspacing="0" cellpadding="0" border="0">
   <tr>
      <td align="left" width="100">
         <div class="f3">Spese Trasporto</div>
         <div class="f4">Euro&nbsp;{{V306}}</div></td>
      <td align="left" width="200">
         <div class="f3">Totale Imponibile</div>
         <div class="f4">Euro&nbsp;{{V317}}</div></td>
      <td align="left" width="50">
         <div class="f3">Totale a Pagare</div>
         <div class="f4">Euro&nbsp;{{V320}}</div></td>
   </tr>
   </table>

   <div style="background-color:black;color:white;margin-bottom:5px;" class="f2"><b>&nbsp;NOTE:</b></div>
   <div class="f1">Le consegne vanno effettuate nei giorni dal Lunedi al Venerdi dalle ore 08.30 alle ore 13.00</div>

   <table width="630" cellspacing="0" cellpadding="0" border="0" style="margin-top:10px;">
   <tr>
      <td align="center" bgcolor="black">
         <div class="f2" style="color:white;"><b>Sito Internet: www.marketlegno.it &nbsp;&nbsp; E-mail: info@marketlegno.it</b></div></td>
   <tr>
      <td align="right">
         <div class="f5" style="font-size:6pt;">
            Pagina creata con <i>Sp2HTML</i> di Giuseppe Chiesa Software development &amp; System Security - email: gchiesa@smos.org - web: www.smos.org</div></td>
   </tr>
   </table>

   </td>
</tr>
</table>

</sp2html_bodyfooter>


#
# Fine del modulo
#
<sp2html_footer>

</body></html>

</sp2html_footer>


