# Le righe che iniziano con il tag # vengono intese come commenti.
# ATTENZIONE: poich� il parser non � XML, OGNI riga di commento deve iniziare
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
#�<sp2html_bodyheader /> = contiene la parte iniziale del body (logo aziendale modulo, cliente, indirizzi etc)
# <sp2html_body /> = contiene le righe degli articoli che verranno ciclicamente ripetute
# <sp2html_bodydata /> = contiene la parte riepilogativa del modulo della fattura (importo totale, riepiloghi etc)
# <sp2html_iva /> = contiene la parte relativa allo specchietto di riepilogo iva che verr� ciclicamente ripetuta
# <sp2html_bodyfooter /> = contiene la parte finale del modulo fattua
# <sp2html_footer /> = tag per chiusura documento (solitamente non richiede modifiche)
#
# 

#
# Dichiarazione tipo template 
#
<sp2html_template>
fat-rendering
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
     <tr>
       <td align="left" colspan="2" style="padding:10px 10px 5px 0px;">
           <div class="f2" style="padding:2px;font-size:7pt;color:#d70000;border-width:1px;border-style:solid;border-color:b:red;">
              La Vostra ragione sociale, l&#39;indirizzo, il codice fiscale nonche la partita IVA che appaiono sul presente documento
              sono quelli che verranno utilizzati, salvo Vostra diversa precisazione, agli effetti dell art.29 del D.P.R.26-18-1972
              n.628 relativo all&#39;IVA. Con quest&#39;avviso ci consideriamo pertanto esonerati da qualsiasi responsabilita&#39; prevista dell
              art.41 dello stesso D.P.R. 633-1972.
        </div>
       </td>
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
      <br>
      <div style="margin-top:20px;width:230px;background-color:black;color:white" class="f2"><b>&nbsp;DESTINAZIONE</b></div>
      <div class="f2">
         {{V019}}<br>
         {{V020}}<br>
         {{V021}} - {{V022}} ({{V023}})<br>
         Tel. {{V045}}
      </div>
   </td>
   </tr>
   </table>

   <hr class="l1">

   <table width="630" cellspacing="0" cellpadding="0" border="0">
   <tr>
      <td align="left" width="100">
         <div class="f3">tipo documento</div>
         <div class="f4">&nbsp;{{V001}}</div></td>
      <td align="left" width="200">
         <div class="f3">cod.cliente</div>
         <div class="f4">&nbsp;{{V005}}</div></td>
      <td align="left">
         <div class="f3">partita iva / cod.fiscale</div>
         <div class="f4">&nbsp;{{V011}} / {{V048}}</div></td>
      <td align="left" width="50">
         <div class="f3">pagina</div>
         <div class="f4">&nbsp;{{V002}} di [[$totPages]]</div></td>
   </tr>
   </table>

   <hr class="l2">

   <table width="630" cellspacing="0" cellpadding="0" border="0">
   <tr>
      <td align="left" width="100">
         <div class="f3">num.documento</div>
         <div class="f4">&nbsp;{{V003}}</div></td>
      <td align="left" width="200">
         <div class="f3">data documento</div>
         <div class="f4">&nbsp;{{V004}}</div></td>
      <td align="left" width="100">
         <div class="f3">cod.pagamento</div>
         <div class="f4">&nbsp;{{V024}}</div></td>
      <td align="left">
         <div class="f3">descrizione pagamento</div>
         <div class="f4">&nbsp;{{V025}}</div></td>
   </tr>
   </table>

   <hr class="l2">

   <table width="630" cellspacing="0" cellpadding="0" border="0">
   <tr>
      <td align="left" width="400">
         <div class="f3">banca d'appoggio</div>
         <div class="f4">&nbsp;{{V028}} / {{V029}}</div></td>
      <td align="left" width="100">
         <div class="f3">valuta</div>
         <div class="f4">&nbsp;{{V033}}</div></td>
      <td align="left">
         <div class="f3">cod.magazzino</div>
         <div class="f4">&nbsp;{{V030}}</div></td>
   </tr>
   </table>

   <br>

   <table width="630" cellspacing="0" cellpadding="0" border="0">
   <tr>
      <td align="left" valign="top" height="380">
         <img src="res/null.png" border="0" width="1" height="380" style="height:380px;width:1px;"></td>
      <td align="left" valign="top">

         <table width="620" cellspacing="0" cellpadding="0" border="0">
         <tr>
            <td align="center" valign="top" height="12" width="30" bgcolor="black">
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
            <td align="center" valign="top" bgcolor="black">
               <div style="color:white;" class="f5">Cod.IVA</div></td>
         </tr>
			
</sp2html_bodyheader>


#
# Sezione contenente il corpo del documento che verrà ripetuto ciclicamente per ogni articolo
#
<sp2html_body>

         <tr>
            <td align="left" valign="top" width="30" height="12" class="c1"><div class="f2">&nbsp;{{V929}}</div></td>
            <td align="left" valign="top" width="280" height="12" class="c1"><div class="f2">&nbsp;{{V125}} {{V126}}</div></td>
            <td align="left" valign="top" width="30" height="12" class="c1"><div class="f2">&nbsp;{{V105}}</div></td>
            <td align="left" valign="top" width="40" height="12" class="c1"><div class="f2">&nbsp;{{V106}}</div></td>
            <td align="right" valign="top" width="70" height="12" class="c1"><div class="f2">&nbsp;{{V107}}</div></td>
            <td align="left" valign="top" width="40" height="12" class="c1"><div class="f2">&nbsp;{{V108}} &nbsp; {{V109}}</div></td>
            <td align="right" valign="top" width="80" height="12" class="c1"><div class="f2">&nbsp;{{V111}}</div></td>
            <td align="left" valign="top" height="12" class="c1" style="border-right-width:1pt"><div class="f2">&nbsp;{{V113}}</div></td>
         </tr>

</sp2html_body>			


#
#�Sezione contenente i dati di riepilogo del body, tipo netto e lordo merce, totale modulo etc.
#
<sp2html_bodydata>

         <tr>
            <td colspan="8" valign="top" align="left" class="c1" style="border-bottom-width:0px;border-top-width:1px;border-left-width:0px;border-right-width:0px;">
               &nbsp;</td>
         </tr>
         </table>
      </td>
   </tr>
   </table>

   <hr class="l1">

   <table width="630" cellspacing="0" cellpadding="0" border="0">
   <tr>
      <td align="left" width="100">
         <div class="f3">totale merce</div>
         <div class="f4">&nbsp;{{V300}}</div></td>
      <td align="left" width="150">
         <div class="f3">%sconti</div>
         <div class="f4">&nbsp;{{V301}} {{V302}} {{V303}}</div></td>
      <td align="left" width="100">
         <div class="f3">netto merce</div>
         <div class="f4">&nbsp;{{V304}}</div></td>
      <td align="left" width="100">
         <div class="f3">spese trasp./acc.</div>
         <div class="f4">&nbsp;{{V305}}</div></td>
      <td align="left" width="100">
         <div class="f3">spese incasso</div>
         <div class="f4">&nbsp;{{V306}}</div></td>
      <td align="left" width="80">
         <div class="f3">bolli(es.art15)</div>
         <div class="f4">&nbsp;{{V308}}</div></td>
   </tr>
   </table>

   <br>

   <table width="630" cellspacing="0" cellpadding="0" border="0">
   <tr>
      <td align="left" valign="top" width="500">

         <table width="450" cellspacing="0" cellpadding="0" border="0">
         <tr>
            <td align="center" colspan="5" bgcolor="black">
               <div class="f2" style="color:white">Riepilogo dati IVA</div></td>
         </tr>
         <tr>
            <td align="right" width="50">
               <div class="f3">Cod.iva</div></td>
            <td align="right" width="50">
               <div class="f3">Spese</div></td>
            <td align="right" width="150">
               <div class="f3">Imponibili</div></td>
            <td align="right" width="50">
               <div class="f3">% Iva</div></td>
            <td align="right" width="150">
               <div class="f3">Imposta</div></td>
         </tr>

</sp2html_bodydata>


#
# Sezione contenente i dati del campo iva che verranno ripetuti ciclicamente per ogni riga iva
#
<sp2html_bodyiva>

         <tr>
            <td align="right" width="50"><div class="f2">&nbsp;{{V312}}</div></td>
            <td align="right" width="50"><div class="f2">&nbsp;{{V313}}</div></td>
            <td align="right" width="150"><div class="f2">&nbsp;{{V314}}</div></td>
            <td align="right" width="50"><div class="f2">&nbsp;{{V315}}</div></td>
            <td align="right" width="150"><div class="f2">&nbsp;{{V316}}</div></td>
         </tr>

</sp2html_bodyiva>


#
# Sezione finale del corpo del documento
#
<sp2html_bodyfooter>

         <tr>
            <td align="left" colspan="5">
               <hr class="l1"></td>
         </tr>
         <tr>
            <td align="right" width="50">&nbsp;</td>
            <td align="right" width="50">&nbsp;</td>
            <td align="right" width="150">
               <div class="f3">Totale Imponibili</div>
               <div class="f4">{{V317}}</div></td>
            <td align="right" width="50">&nbsp;</td>
            <td align="right" width="150">
               <div class="f3">Totale Imposta</div>
               <div class="f4">{{V318}}</div></td>
         </tr>
         </table>

      </td>
      <td align="right" valign="top" width="130">

         <table width="130" cellspacing="0" cellpadding="0" border="0">
         <tr>
            <td align="center" width="130" bgcolor="black">
               <div class="f2" style="color:white">Totali</div></td>
         </tr>
         <tr>
           <td align="right" width="130">
               <div class="f3">Totale Documento</div>
               <div class="f4" style="font-size:10pt">&nbsp;<b>Euro&nbsp;{{V319}}</b></div>
               <div class="f4" style="font-size:7pt;">&nbsp;({{V340}})</div><br></td>
         </tr>
         <tr>
            <td align="right" width="130" bgcolor="black">
               <div class="f2" style="color:white">TOTALE A PAGARE</div></td>
         </tr>
         <tr>
            <td align="right" width="130">
               <div class="f4" style="font-size:10pt;">&nbsp;<b>Euro&nbsp;{{V319}}</b></div>
               <div class="f4" style="font-size:7pt;">&nbsp;({{V340}})</div></td>
        </tr>
        </table>

      </td>
   </tr>
   </table>

   <hr class="l1">

   <table width="630" cellspacing="0" cellpadding="0" border="0">
   <tr>
      <td align="left" valign="top" width="130">
         <div class="f3">Causale del trasporto</div>
         <div class="f4">&nbsp;{{V204}}</div></td>
      <td align="left" valign="top" width="80">
         <div class="f3">Numero Colli</div>
         <div class="f4">&nbsp;{{V207}}</div></td>
      <td align="left" valign="top" width="150">
         <div class="f3">Aspetto esteriore beni</div>
         <div class="f4">&nbsp;{{V203}}</div></td>
      <td align="left" valign="top">
         <div class="f3">Vettori</div>
         <div class="f4">&nbsp;{{V212}}</div>
         <div class="f4" style="font-size:7pt;">&nbsp;{{V213}}</div></td>
   </tr>
   </table>

   <hr class="l2">

   <table width="630" cellspacing="0" cellpadding="0" border="0">
   <tr>
      <td align="right" valign="top">
         <div class="f3">Firma del Conducente</div>
         <br>
      </td>
      <td align="right" valign="top" width="300">
         <div class="f3">Firma del Destinatario</div>
         <br>
      </td>
   </tr>
   </table>

   <br>
   <table width="630" cellspacing="0" cellpadding="0" border="0">
   <tr>
      <td align="center" valign="top">
         <div class="f5" style="font-size:6pt;">
            Essendo a conoscenza dei criteri previsti dai DLGS 675/96 e 196/03 a tutela della privacy autorizzo la Andrea Chiesa L. Srl ad utilizzare i dati forniti per le finalit&aacute; consentite dalla legge.
         </div>
      </td>
   </tr>
   </table>

   <table width="630" cellspacing="0" cellpadding="0" border="0">
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


