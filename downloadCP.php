<?php

 if(file_exists("Remessa-".date("d-m-Y")."-CP.rem")) {
    $fileNameCP = "Remessa-".date("d-m-Y")."-CP.rem";
     
    header("Content-Type: aplication/rem");
    // informa o tipo do arquivo ao navegador
    header("Content-Length: ".filesize($fileNameCP));
    // informa o tamanho do arquivo ao navegador
    header("Content-Disposition: attachment; filename=".basename($fileNameCP));
    // informa ao navegador que é tipo anexo e faz abrir a janela de download,
    //tambem informa o nome do arquivo
    readfile($fileNameCP); // lê o arquivo
    exit; // aborta pós-ações

 }

?>