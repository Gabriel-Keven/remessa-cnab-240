<?php

if(file_exists("Remessa-".date("d-m-Y")."-CC.rem")) {
    $fileNameCC = "Remessa-".date("d-m-Y")."-CC.rem";
 
    
    header("Content-Type: aplication/rem");
    // informa o tipo do arquivo ao navegador
    header("Content-Length: ".filesize($fileNameCC));
    // informa o tamanho do arquivo ao navegador
    header("Content-Disposition: attachment; filename=".basename($fileNameCC));
    // informa ao navegador que é tipo anexo e faz abrir a janela de download,
    //tambem informa o nome do arquivo
    readfile($fileNameCC); // lê o arquivo
    exit;
   
 
 }
 

?>