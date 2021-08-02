<?php

if(file_exists("Remessa-".date("d-m-Y").".rem")){
    $fileName = "Remessa-".date("d-m-Y").".rem";
    header("Content-Type: aplication/rem");
    // informa o tipo do arquivo ao navegador
    header("Content-Length: ".filesize($fileName));
    // informa o tamanho do arquivo ao navegador
    header("Content-Disposition: attachment; filename=".basename($fileName));
    // informa ao navegador que é tipo anexo e faz abrir a janela de download,
    //tambem informa o nome do arquivo
    readfile($fileName); // lê o arquivo
    exit; // aborta pós-ações
 }


?>