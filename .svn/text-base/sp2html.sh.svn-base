#!/bin/bash

BASEDIR=`dirname $0`
cd $BASEDIR 

#
# Cerco l'interprete php 
#
if [ "`php --version | grep 'command not found'`" != "" ]; then 
   echo "Errore: php non trovato. Verificare l'installazione di php-cli o di php"
   echo "        e che sia raggiungibile nel path di sistema"
   echo -en "\n\n<INVIO> per concludere lo script"
   read 
   exit 1
fi

EXT_DIR=`php -i | grep extension_dir | cut -d'=' -f2 | cut -d' ' -f2`
INI_FILE=`php -i | grep php.ini | cut -d'=' -f2 | cut -d' ' -f2`


#
# controllo supporto gtk 
#
if [ ! -e "$EXT_DIR/php_gtk2.so" ]; then 
   echo "Errore: estensione gtk non disponibile. Occorre installare il pacchetto "
   echo "php5-gtk disponibile insieme al download di Sp2HTMLServer su www.smos.org"
   echo "Una volta scaricato, installarlo con il comando:"
   echo 
   echo "dpkg -i php5-gtk2*.deb"
   echo -en "\neseguito da utente root"
   echo -en "\n\n<INVIO> per concludere lo script\n"
   read
   exit 1
fi

#
# controllo supporto gtk in php.ini 
#
CHECK=`php -m 2>&1 | grep gtk`
if [ "$CHECK" == "" ]; then 
   echo "Errore: estensione gtk non inserita in php.ini. Occorre inserire alla fine del file"
   echo "php.ini la direttiva per il caricamento della libreria con il comando:" 
   echo 
   echo "echo \"extension=php_gtk2.so\" >> $INI_FILE"
   echo -en "\neseguito da utente root"
   echo -en "\n\n<INVIO> per concludere lo script "
   read
   exit 1
fi

#
# controllo supporto pcre
#
CHECK=`php -m 2>&1 | grep pcre`
if [ "$CHECK" == "" ]; then 
   echo "Errore: supporto per PCRE non attivo. Verificare l'installazione di php o"
   echo "la presenza del modulo pcre"
   echo -en "\n\n<INVIO> per concludere lo script"
   read
   exit 1
fi

#
# controllo supporto curl 
#
CHECK=`php -m 2>&1 | grep curl`
if [ "$CHECK" == "" ]; then 
   echo "Errore: supporto per CURL non attivo. Verificare l'installazione di php o"
   echo "la presenza del modulo curl"
   echo -en "\n\n<INVIO> per concludere lo script"
   read
   exit 1
fi


#
# Controllo se avviato con il check/no background
#
if [ "$1" == "--check" -o "$1" == "-c" ]; then 
   php -q sp2html.php
else
   php -q sp2html.php > /dev/null 2>&1 & 
fi
