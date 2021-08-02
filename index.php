<?php



/*Arquivo Gerador CNBAB 240 a parir de tabelas em formato CSV para Banco do Brasil
  Autor: Gabriel Keven Domingues de Souza
  e-mail: gabriel-keven@outlook.com
*/

//Upload do arquivo para a pasta uploads
if($_SERVER["REQUEST_METHOD"] === "POST"){

    $file = $_FILES["fileUpload"];

    if($file["error"]){
        throw new Exception("ERROR: ".$file["error"]);
    }

    $dirUploads = "uploads";

    if(!is_dir($dirUploads)){

        mkdir($dirUploads);
    }

   if( move_uploaded_file($file["tmp_name"],$dirUploads.DIRECTORY_SEPARATOR.$file["name"])){

    setlocale(LC_ALL, 'pt_BR.utf8');
        $fileOpen = fopen("uploads".DIRECTORY_SEPARATOR.$file["name"],"r");
        $i = 0;
        $quantidadeTotalDeSolicitantes = 0;
        $quantidadeBancoDoBrasil = 0;
    
    while (($dados = fgetcsv($fileOpen, 0, ";")) !== FALSE){
        $solicitacoes[$i] = $dados;
        if($dados!=NULl){
            if($solicitacoes[$i][3]=='001')
            {
                $quantidadeBancoDoBrasil++;
            }        
            $quantidadeTotalDeSolicitantes++;
            $i++;
        }
        else
        {
            break;
        }
       
    }
   }else{

    throw new Exception("ERROR: Não foi possível realizar o upload. ");

   }

    }

   //Removendo os notices
   error_reporting(0);

    //Variáveis
    $cnpj = $_POST["cnpj"];
    $numero_convenio = $_POST["numero_convenio"];
    $agencia = $_POST["agencia"];
    $digito_agencia = $_POST["digito_agencia"];
    $conta = $_POST["conta"];
    $digito_conta = $_POST["digito_conta"];
    $digito_ag_conta = $_POST["digito_ag_conta"];
    $nome_empresa = $_POST["nome_empresa"];
    $data_pagamento = $_POST["data_pagamento"];
    $arquivo = $_POST["file"];

	
    $headerArquivo =  0;
    $headerAB = 0; 
    $aux = 0; //Variável auxiliar para o for
    $segmentoABBancodoBrasilCC = [];//Vetor que contem os segmentos A e B referentes ao banco do brasil do tipo conta corrente
    $segmentoABBancodoBrasilCP = [];//Vetor que contem os segmentos A e B referentes ao banco do brasil do tipo conta poupança
    $segmentoABOutrosBancosCP = [];//Vetor que contem os segmentos A e B referentes a outros bancos
    $segmentoABOutrosBancosCC = [];//Vetor que contem os segmentos A e B referentes a outros bancos

    $valorParcialOutrosBancosCC = 0; //Variável recebe os valores parcias de outras contas corrente 
    $valorParciaBancoDoBrasilCC = 0; //Variável recebe os valores parcias de contas do Banco do Brasil do tipo corrente
    $valorParcialOutrosBancosCP = 0; //Variável recebe os valores parcias de outras contas do tipo poupança 
    $valorParcialBancoDoBrasilCP = 0; //Variável recebe os valores parcias de contas do Banco do Brasil do tipo poupança

    $headerABBancoDoBrasilCC =""; //Variável recebe as informações do header AB do banco do brasil conta corrente
    $headerABBancoDoBrasilCP =""; //Variável recebe as informações do header AB do banco do brasil conta poupança
    $headerABOutrosBancosCP =""; //Variável recebe as informações do header AB de outros bancos do tipo poupança
    $headerABOutrosBancosCC ="";//Variável recebe as informações do header AB de outros bancos conta corrente

    $loteABBancoDoBrasilCC =""; //Variável recebe as informações do lote AB do banco do brasil conta corrente
    $loteABBancoDoBrasilCP =""; //Variável recebe as informações do lote AB do banco do brasil conta poupança
    $loteABOutrosBancosCP =""; //Variável recebe as informações do lote AB de outros bancos do tipo poupança
    $loteABOutrosBancosCC =""; //Variável recebe as informações do lote AB de outros bancos do tipo corrente

    $quantidadeLotes = 0; //Quantidade de lotes
    $contadorBancodoBrasilCC = 1; //Contador para escrever o segmentos A e B do banco do brasil do tipo conta corrente
    $contadorBancodoBrasilCP = 1; //Contador para escrever o segmentos A e B do banco do brasil do tipo poupança 
    $contadorOutrosBancosCP = 1; //Contador para escrever o segmentos A e B de outros Bancos do tipo poupança
    $contadorOutrosBancosCC = 1; //Contador para escrever o segmentos A e B de outros Bancos do tipo corrente

    $valorTotal = 0; //Valor total do arquivo
    $quantidadeRegistrosTotal = 0;

    $j = 1; //Contador para o número sequencial do lote dos solicitantes de outros bancos contta corrente
    $l = 1; //Contador para o número sequencial do lote dos solicitantes do Banco do Brasil Conta corrente
    $m = 1; //Contador para o número sequencial do lote dos solicitantes do Banco do Brasil Conta poupança
    $n = 1; //Contador para o número sequencial do lote dos solicitantes do outros bancos Conta poupança
    $i = 1;//Váriavel para acessar os dados dos solicitantes
    
    //Evitar a criação do arquivo antes do fornecimento de dados da planilha
    if(isset($solicitacoes)){
    
    if($arquivo==1){
        $file = fopen('Remessa-'.date("d-m-Y")."-CC.rem", 'w+');
    }else{
        $file = fopen('Remessa-'.date("d-m-Y").".rem", 'w+');
    }
   
    //header('Content-type: text/html; charset=ISO-8859-1');//Codficação do arquivo de acordo com o estabelecido
    
    /*                                     HEADER DO ARQUIVO                              */
    $headerArquivo =  "001";  //Código no banco na compensação
    $headerArquivo .= "0000"; //Lote de Serviço
    $headerArquivo .= "0";    //Tipo de Serviço
    $headerArquivo.=  "         ";  //Uso exclusivo FEBRABAN/CNAB
    $headerArquivo.=  "2";  //Tipo de inscrição da empresa
    $headerArquivo.=  $cnpj; //Número de Inscrição da empresa(CNPJ)
    $headerArquivo.=  $numero_convenio;  //Número do convênio
    $headerArquivo.=  "0126"; //Código do convênio
    $headerArquivo.=  "     ";  //Uso reservado do banco (Brancos)
    $headerArquivo.=  "  "; // Arquivo de teste
    $headerArquivo.=  $agencia;  //Agência mantenedora da conta
    $headerArquivo.=  $digito_agencia;//  Digito verificado da agência
    $headerArquivo.=  $conta; //Número da conta corrente
    $headerArquivo.=  $digito_conta;  //Digito verificador da conta 
    $headerArquivo.=  $digito_ag_conta;  //Digito verificador da Ag/Conta
    $headerArquivo.=  str_pad($nome_empresa, 30, ' ', STR_PAD_RIGHT ); //Nome da Empresa
    $headerArquivo.=  str_pad("BANCO DO BRASIL S/A", 30, ' ', STR_PAD_RIGHT );  //Nome do banco
    $headerArquivo.=  "          "; //Uso exclusivo FEBRABAN/CNAB
    $headerArquivo.=  "1";  //Arquivo de remessa
    $headerArquivo.=  date("dmY");// Data de geração do arquivo
    $headerArquivo.=  "000000"; //Hora de geração do arquivo
    $headerArquivo.=  "001779"; //Número sequencial do arquivo
    $headerArquivo.=  "000";  //Nº d versão do layout do arquivo 
    $headerArquivo.=  "00000";  //Densidade da gravação do arquivo
    $headerArquivo.=  str_pad("   ", 69, ' ', STR_PAD_RIGHT );  //Número da versão do Layout do arquivo. OBS: Valor veio do arquivo padrão de teste 
    $headerArquivo.=  "\r\n"; //Quebra de linha
    fwrite($file,$headerArquivo);
    /*                                 HEADER DO ARQUIVO                              */

    /*                                 HEADER DO LOTE AB                              */ 
    $headerAB = "001";  //Código no banco na compensação
    $headerAB.= "0001"; //Lote de Serviço
    $headerAB.= "1";  //Tipo de registro
    $headerAB.= "C";  //Tipo  de operação
    $headerAB.= "98";//Tipo de serviço->Diversos(Contas de diferentes bancos)
    $headerAB.= "03"; //Forma  de Lançamentos(DOC/TED)
    $headerAB.= "031";//N° da versão do Layout do lOTE
    $headerAB.= " "; //FENABRAN/CNAB
    $headerAB.= "2"; //Tipo de inscrição da empresa(CNPJ)
    $headerAB.= $cnpj;  //Número de Inscrição da empresa
    $headerAB.= $numero_convenio;//Número do convênio
    $headerAB.= "0126";//Código do convênio
    $headerAB.= "     ";//Uso reservado do banco (Brancos)
    $headerAB.= "  ";// Arquivo de teste
    $headerAB.= $agencia;//Agência mantenedora da conta
    $headerAB.= $digito_agencia;//Digito verificado da agência
    $headerAB.= $conta;//Número da conta corrente
    $headerAB.= $digito_conta;//Digito verificador da conta 
    $headerAB.= $digito_ag_conta;//Digito verificador da Ag/Conta
    $headerAB.= str_pad($nome_empresa, 30, ' ', STR_PAD_RIGHT );//Nome da Empresa
    $headerAB.= "                                        "; //Mensagem - Prenchimento Exclusivo do BB
    $headerAB.= "        ";//Uso  Exclusivo da FEBRABAN/ CNAB
    $headerAB.= "          ";// Código das Ocorrências para retorno  
    $headerAB.= str_pad(" ", 80, ' ', STR_PAD_RIGHT );//Completar a quantidade de cartacteres do registro
    $headerAB.= "\r\n"; //Quebra de linha
    
   /*                                 HEADER DO LOTE AB                              */
  

   for($i = 0; $i < $quantidadeTotalDeSolicitantes; $i++){
    
    //Variáveis
    $segmentoA = 0;       //Várivael que contém os dados da conta corrente de outros bancos
    $segmentoB = 0;    //Várivael que contém os dados da conta corrente de outros bancos
    $trailerLote = 0;
    /*                              SEGMENTO A                             */  
    $segmentoA = "001"; //Código no banco na compensação 
    $segmentoA.= "0001";
    $segmentoA.= "3"; //Tipo de registro
    if($solicitacoes[$i][3]!='001')// número sequencial do Lote
    { 
        if($solicitacoes[$i][6]=='CC')// número sequencial do Lote
        { 
            $segmentoA.= str_pad( $j, 5, '0', STR_PAD_LEFT ); 
            $j++;
        }

        else if($solicitacoes[$i][6]=='CP')// número sequencial do Lote
        { 
            $segmentoA.= str_pad( $m, 5, '0', STR_PAD_LEFT ); 
            $m++;
        }
    }
    else{
        if($solicitacoes[$i][6]=='CC')// número sequencial do Lote
        {   
            $segmentoA.= str_pad( $l, 5, '0', STR_PAD_LEFT ); 
            $l++;
        }

        else if($solicitacoes[$i][6]=='CP')// número sequencial do Lote
        { 
            $segmentoA.= str_pad( $n, 5, '0', STR_PAD_LEFT ); 
            $n++;
        }
    }
    $segmentoA.= "A"; //Cóidgo de Segmento no Reg.Detalhe
    $segmentoA.= "0"; //Tipo de Movimento
    $segmentoA.= "00"; //Código Instrução para movimento
    if($solicitacoes[$i][3]!='001'){
        $segmentoA.= "018"; //Código da Câmara centralizadora  //Banco do Brasil
    }else{
        $segmentoA.= "000"; //Código da Câmara centralizadora  
    }
    
    $segmentoA.= $solicitacoes[$i][3]; //Código do Banco
    $segmentoA.= str_pad($solicitacoes[$i][4], 5, '0', STR_PAD_LEFT ); //Agencia
    $segmentoA.= $solicitacoes[$i][5]; //Digito da Agência
    if($solicitacoes[$i][3]=='001' && $solicitacoes[$i][6]==2){
        $segmentoA.= "51".str_pad($solicitacoes[$i][7], 10, '0', STR_PAD_LEFT ); //Conta
    }else{
        $segmentoA.= str_pad($solicitacoes[$i][7], 12, '0', STR_PAD_LEFT ); //Conta
    }
    $segmentoA.= $solicitacoes[$i][8]; //Digito verificador da Conta  
    $segmentoA.= " "; //Digito verificador da Ag/Conta
    $nome =       utf8_encode($solicitacoes[$i][0]);
    $acentos =    array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'O', 'Ù', 'Ü', 'Ú','Ç');
    $semAcentos = array('A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U','C');
    $nome =       str_replace($acentos, $semAcentos, $nome);
    $nome = strtoupper($nome);
    $nomePadrao = substr($nome,0,30);
    $segmentoA.= str_pad($nomePadrao, 30, " ", STR_PAD_RIGHT ); //Nome verificador
    $segmentoA.= "                    " ;//Número Doc Atribuído pela empresa
    $segmentoA.= date("dmY",strtotime($data_pagamento)); // Data do pagamento
    $segmentoA.=  "BRL";  // Tipo de Moeda
    $segmentoA.=  "000000000000000"; // Quantidade da Moeda
    $valor = $solicitacoes[$i][2];
    $valor = str_replace('.', '', $valor); // remove o ponto
    $segmentoA.=  str_pad($valor, 13, 0, STR_PAD_LEFT); 
    $segmentoA.=  "00";
    $segmentoA.=  "                    "; //Número Doc atribúido pelo banco
    //$segmentoA.=  date("dmY",strtotime($data_pagamento)); //Data real da efetivação do pgto
    $segmentoA.="00000000";//Data real da efetivação do pgto
    $segmentoA.=  "000000000000000";  //Valor da Efetivação do pgto
    if($solicitacoes[$i][6]=="CP" && $solicitacoes[$i][3]=="001")//Tipo conta poupança
    {
      $segmentoA.=  "11                                      "; //Outras informações
    }else{
        $segmentoA.=  "                                        "; //Outras informações
    }
   
    $segmentoA.=  "  "; //Compl. Tipo serviço
    $segmentoA.=  "     ";  //Código finalidade da TED
    $segmentoA.=  "  "; //Complemento finalidade pgto
    $segmentoA.=  "   ";  //Exclusivo FEBRABAN
    $segmentoA.=  "0";  //Aviso ao Fornecedor
    $segmentoA.=  "          "; //Código de ocorrências para retorno
    $segmentoA.=  "\r\n"; //Quebra de linha
   
    
    /*                              SEGMENTO A                             */  

    /*                              SEGMENTO B                             */  
     $segmentoB = "001";  //Código no banco na compensação
     $segmentoB.= "0001";
     $segmentoB.= "3";  //Tipo de registro
     if($solicitacoes[$i][3]!='001')// número sequencial do Lote
    { 
        if($solicitacoes[$i][6]=='CC')// número sequencial do Lote
        { 
            $segmentoB.= str_pad( $j, 5, '0', STR_PAD_LEFT ); 
            $j++;
        }

        else if($solicitacoes[$i][6]=='CP')// número sequencial do Lote
        { 
            $segmentoB.= str_pad( $m, 5, '0', STR_PAD_LEFT ); 
            $m++;
        }
    }
    else{
        if($solicitacoes[$i][6]=='CC')// número sequencial do Lote
        { 
            $segmentoB.= str_pad( $l, 5, '0', STR_PAD_LEFT ); 
            $l++;
        }

        else if($solicitacoes[$i][6]=='CP')// número sequencial do Lote
        { 
            $segmentoB.= str_pad( $n, 5, '0', STR_PAD_LEFT ); 
            $n++;
        }
    }
     $segmentoB .= "B";//Cóidgo de Segmento no Reg.Detalhe
     $segmentoB.= "   ";// FEBRABAN/CNAB
     $segmentoB.= "1";// TIPO DE FAVORECIDO - CPF
     $segmentoB.= str_pad($solicitacoes[$i][1], 14, '0', STR_PAD_LEFT ); //N° inscrição favorecido
     $segmentoB.= str_pad(" ", 95, ' ', STR_PAD_RIGHT ); //Itens em branco
     $segmentoB.= date("dmY");//Data do Vencimento(Nominal)
     $segmentoB.= "000000000000000";//Valor do Documento
     $segmentoB.= "000000000000000";//Valor do abatimento
     $segmentoB.= "000000000000000";//Valor do Desconto
     $segmentoB.= "000000000000000";//Valor da Mora
     $segmentoB.= "000000000000000";//Valor da Multa    
     $segmentoB.= str_pad("0", 30, ' ', STR_PAD_LEFT ); //Aviso ao favorecido
     $segmentoB.="\r\n"; //Quebra de linha
    /*                              SEGMENTO B                             */  
    if($solicitacoes[$i][3]!="001")
    {
        if($solicitacoes[$i][6]=='CC'){
            $loteCCOutrosBancos = 1;
            $contadorOutrosBancosCC++;
            $segmentoABOutrosBancosCC[$contadorOutrosBancosCC].= $segmentoA;
            $contadorOutrosBancosCC++;
            $segmentoABOutrosBancosCC[$contadorOutrosBancosCC].= $segmentoB;
            $valorParcialOutrosBancosCC = $valorParcialOutrosBancosCC + $solicitacoes[$i][2];
            
        }
       else if($solicitacoes[$i][6]=='CP')
        {
            $loteCPOutrosBancos = 3;
            $contadorOutrosBancosCP++;
            $segmentoABOutrosBancosCP[$contadorOutrosBancosCP].= $segmentoA;
            $contadorOutrosBancosCP++;
            $segmentoABOutrosBancosCP[$contadorOutrosBancosCP].= $segmentoB;
            $valorParcialOutrosBancosCP = $valorParcialOutrosBancosCP + $solicitacoes[$i][2];
        }

    }
    else
    {   
        if($solicitacoes[$i][6]=='CC'){
            $loteCCBancoDoBrasil = 2;
            $contadorBancodoBrasilCC++;
            $segmentoABBancodoBrasilCC[$contadorBancodoBrasilCC].= $segmentoA;
            $contadorBancodoBrasilCC++;
            $segmentoABBancodoBrasilCC[$contadorBancodoBrasilCC].= $segmentoB;
            $valorParciaBancoDoBrasilCC = $valorParciaBancoDoBrasilCC  + $solicitacoes[$i][2];
        }
        else if($solicitacoes[$i][6]=='CP'){
            $loteCPBancoDoBrasil = 4;
            $contadorBancodoBrasilCP++;
            $segmentoABBancodoBrasilCP[$contadorBancodoBrasilCP].= $segmentoA;
            $contadorBancodoBrasilCP++;
            $segmentoABBancodoBrasilCP[$contadorBancodoBrasilCP].= $segmentoB;
            $valorParcialBancoDoBrasilCP = $valorParcialBancoDoBrasilCP  + $solicitacoes[$i][2];

        }
    }
  }

    /*                              TRAILER DO LOTE                             */
    $trailerLote = "001"; //Código banco na compensação
    $trailerLote.= "0001";  //Lote Serviço
    $trailerLote.= "5"; //Tipo de registro
    $trailerLote.= "         "; //Uso exclusivo FEBRABAN/CNAB
    $trailerLote.=  str_pad($j+1, 6, '0', STR_PAD_LEFT ); //Quantidade de registros de lote
    $trailerLote.=  str_pad($valorParcialOutrosBancosCC, 16, '0', STR_PAD_LEFT );  //Somátorio dos valores 
    $trailerLote.=  "00"; //Somátorio dos valores -> continuação dos valores, duas casas após a vírgula
    $trailerLote.=  "000000000000000000";//Somatória de Quantidade de moedas
    $trailerLote.=  "000000";//Número aviso débito
    $trailerLote.=  str_pad(" ", 165, ' ', STR_PAD_LEFT );//Espaços em branco
    $trailerLote.=  "          ";//Código de ocorrências para retorno
    $trailerLote.=  "\r\n";//Quebra de linha
    //fwrite($file,$trailerLote);
    
    /*                              TRAILER DO LOTE                             */

   /*CONTA BANCO DO BRASIL*/
  
  
        //Conta corrente do Banco do Brasil

        if($loteCCOutrosBancos==1)
        {
        $aux = 1;
        $headerABOutrosBancosCC = str_replace('00011C9803', '000'.$loteCCOutrosBancos.'1C9803', $headerAB);
            fwrite($file,$headerABOutrosBancosCC);
                for($contadorOutrosBancosCC;$aux<=$contadorOutrosBancosCC;$aux++)
                {
                    str_replace('0010001','001000'.$loteCCOutrosBancos,$segmentoABOutrosBancosCC[$aux]);
                    fwrite($file,$segmentoABOutrosBancosCC[$aux]);
                   
                }
            $trailerLoteOutrosBancosCC = str_replace('0001','000'.$loteCCOutrosBancos,$trailerLote);   // Lote de Serviço
            fwrite($file,$trailerLoteOutrosBancosCC);
            $quantidadeRegistrosTotal += $j+1;
            $quantidadeLotes++;
        }
        if($loteCCBancoDoBrasil==2)
        {
            //Se não existir outros pagamentos do tipo CC de outtros bancos
            if(!$loteCCOutrosBancos){
                $loteCCBancoDoBrasil=1;
            }
            $aux = 1;
            $headerABBancoDoBrasilCC = str_replace('00011C9803', '000'.$loteCCBancoDoBrasil.'1C9801', $headerAB);
            fwrite($file,$headerABBancoDoBrasilCC);
                for($contadorBancodoBrasilCC;$aux<=$contadorBancodoBrasilCC;$aux++)
                {   
                    $segmentoABBancodoBrasilCC[$aux] = str_replace('0010001','001000'.$loteCCBancoDoBrasil,$segmentoABBancodoBrasilCC[$aux]);
                    fwrite($file,$segmentoABBancodoBrasilCC[$aux]);
                   
                    
                }
            $trailerLoteBancoDoBrasilCC = str_replace('0010001','001000'.$loteCCBancoDoBrasil,$trailerLote);   // Lote de Serviço
            $trailerLoteBancoDoBrasilCC = str_replace(str_pad($j+1, 6, '0', STR_PAD_LEFT ),str_pad($l+1, 6, '0', STR_PAD_LEFT ),$trailerLoteBancoDoBrasilCC);  //Quantidade de registros do lote
            $trailerLoteBancoDoBrasilCC = str_replace(str_pad($l+1, 6, '0', STR_PAD_LEFT ).str_pad($valorParcialOutrosBancosCC, 16, '0', STR_PAD_LEFT ),str_pad($l+1, 6, '0', STR_PAD_LEFT ).str_pad($valorParciaBancoDoBrasilCC, 16, '0', STR_PAD_LEFT ),$trailerLoteBancoDoBrasilCC);//Somatório de valores do Lote
            fwrite($file,$trailerLoteBancoDoBrasilCC);
            $quantidadeLotes++;
            $quantidadeRegistrosTotal += $l+1;

        }
/***************************************CRIAÇÃO DE DOIS ARQUIVOS**************************************** */
        if($arquivo==1){
            
            $trailerArquivo = 0;
            $trailerArquivo = "001";//Código banco na compensação
            $trailerArquivo.= "9999";//Lote Serviço
            $trailerArquivo.= "9";//Tipo de registro
            $trailerArquivo.= "         ";//Uso exclusivo FEBRABAN/CNAB
            $trailerArquivo.= "00000".$quantidadeLotes;//Quantidade de lotes do arquivo
            $trailerArquivo.= str_pad($quantidadeRegistrosTotal+2,6,'0',STR_PAD_LEFT);//Quantidade de registro de arquivos
            $trailerArquivo.= "000000";//Qtde. de Contas p/Conc..(LOTES)
            $trailerArquivo.= str_pad(" ", 205, ' ', STR_PAD_RIGHT );//Espaços em branco
            $trailerArquivo.= "\r\n";//Quebra de linha
            fwrite($file,$trailerArquivo);
            fclose($file);

           //mudança de númeração do lote devido a criação de um novo arquivo
            $file = fopen('Remessa-'.date("d-m-Y")."-CP.rem", 'w+');
            fwrite($file,$headerArquivo);
            if(isset($loteCPOutrosBancos)){
                $loteCPOutrosBancos = 1;
            }
            if(isset($loteCPBancoDoBrasil)){
                $loteCPBancoDoBrasil = 2;
            }
            
            $quantidadeRegistrosTotal = 2;
            $quantidadeLotes = 0;
        if($loteCPOutrosBancos==1)
        {
        $aux = 1;
        $headerABOutrosBancosCP = str_replace('00011C9803', '000'.$loteCPOutrosBancos.'1C9803', $headerAB);
            fwrite($file,$headerABOutrosBancosCP);
                for($contadorOutrosBancosCP;$aux<=$contadorOutrosBancosCP;$aux++)
                {
                    
                    fwrite($file,$segmentoABOutrosBancosCP[$aux]);
                   
                }
            $trailerLoteOutrosBancosCP = str_replace('0001','000'.$loteCPOutrosBancos,$trailerLote);   // Lote de Serviço
            $trailerLoteOutrosBancosCP = str_replace(str_pad($j+1, 6, '0', STR_PAD_LEFT ),str_pad($m+1, 6, '0', STR_PAD_LEFT ),$trailerLoteOutrosBancosCP);  //Quantidade de registros do lote
            $trailerLoteOutrosBancosCP = str_replace(str_pad($m+1, 6, '0', STR_PAD_LEFT ).str_pad($valorParcialOutrosBancosCC, 16, '0', STR_PAD_LEFT ),str_pad($m+1, 6, '0', STR_PAD_LEFT ).str_pad($valorParcialOutrosBancosCP, 16, '0', STR_PAD_LEFT ),$trailerLoteOutrosBancosCP);//Somatório de valores do Lote
            fwrite($file,$trailerLoteOutrosBancosCP);
            $quantidadeLotes++;
            $quantidadeRegistrosTotal += $m+1;
           
        }
        //Conta Popupança do Banco do Brasil
        if($loteCPBancoDoBrasil==2) 
        {
            if(!($loteCPOutrosBancos)){
                $loteCPBancoDoBrasil = 1;
            }

            $aux = 1;
            $headerABBancoDoBrasilCP = str_replace('00011C9803', '000'.$loteCPBancoDoBrasil.'1C9805', $headerAB);
            fwrite($file,$headerABBancoDoBrasilCP);
                for($contadorBancodoBrasilCP;$aux<=$contadorBancodoBrasilCP;$aux++)
                {
                    $segmentoABBancodoBrasilCP[$aux] = str_replace('0010001','001000'.$loteCPBancoDoBrasil,$segmentoABBancodoBrasilCP[$aux]);
                    fwrite($file,$segmentoABBancodoBrasilCP[$aux]);
                    
                }
            $trailerLoteBancoDoBrasilCP = str_replace('10001','1000'.$loteCPBancoDoBrasil,$trailerLote);   // Lote de Serviço
            $trailerLoteBancoDoBrasilCP = str_replace(str_pad($j+1, 6, '0', STR_PAD_LEFT ),str_pad($n+1, 6, '0', STR_PAD_LEFT ),$trailerLoteBancoDoBrasilCP);  //Quantidade de registros do lote
            $trailerLoteBancoDoBrasilCP = str_replace(str_pad($n+1, 6, '0', STR_PAD_LEFT ).str_pad($valorParcialOutrosBancosCC, 16, '0', STR_PAD_LEFT ),str_pad($n+1, 6, '0', STR_PAD_LEFT ).str_pad($valorParcialBancoDoBrasilCP, 16, '0', STR_PAD_LEFT ),$trailerLoteBancoDoBrasilCP);//Somatório de valores do Lote
            fwrite($file,$trailerLoteBancoDoBrasilCP);
            $quantidadeLotes++;
            $quantidadeRegistrosTotal += $n+1;
        }
            //Trailer do segundo arquivo

            $trailerArquivo = 0;
            $trailerArquivo = "001";//Código banco na compensação
            $trailerArquivo.= "9999";//Lote Serviço
            $trailerArquivo.= "9";//Tipo de registro
            $trailerArquivo.= "         ";//Uso exclusivo FEBRABAN/CNAB
            $trailerArquivo.= "00000".$quantidadeLotes;//Quantidade de lotes do arquivo
            $trailerArquivo.= str_pad($quantidadeRegistrosTotal,6,'0',STR_PAD_LEFT);//Quantidade de registro de arquivos
            $trailerArquivo.= "000000";//Qtde. de Contas p/Conc..(LOTES)
            $trailerArquivo.= str_pad(" ", 205, ' ', STR_PAD_RIGHT );//Espaços em branco
            $trailerArquivo.= "\r\n";//Quebra de linha    
            fwrite($file,$trailerArquivo);
            fclose($file);
            exit;
            
        /***************************************CRIAÇÃO DE DOIS ARQUIVOS**************************************** */
        }else{

        }
         //Conta Poupança de outros Bancos
        if($loteCPOutrosBancos==3)
        {
        $aux = 1;
        $headerABOutrosBancosCP = str_replace('00011C9803', '000'.$loteCPOutrosBancos.'1C9803', $headerAB);
            fwrite($file,$headerABOutrosBancosCP);
                for($contadorOutrosBancosCP;$aux<=$contadorOutrosBancosCP;$aux++)
                {
                    
                    fwrite($file,$segmentoABOutrosBancosCP[$aux]);
                   
                }
            $trailerLoteOutrosBancosCP = str_replace('0001','000'.$loteCPOutrosBancos,$trailerLote);   // Lote de Serviço
            $trailerLoteOutrosBancosCP = str_replace(str_pad($j+1, 6, '0', STR_PAD_LEFT ),str_pad($m+1, 6, '0', STR_PAD_LEFT ),$trailerLoteOutrosBancosCP);  //Quantidade de registros do lote
            $trailerLoteOutrosBancosCP = str_replace(str_pad($valorParcialOutrosBancosCC, 16, '0', STR_PAD_LEFT ),str_pad($valorParcialOutrosBancosCP, 16, '0', STR_PAD_LEFT ),$trailerLoteOutrosBancosCP);//Somatório de valores do Lote
            fwrite($file,$trailerLoteOutrosBancosCP);
            $quantidadeLotes++;
            $quantidadeRegistrosTotal += $m+1;
           
        }
        //Conta Popupança do Banco do Brasil
        if($loteCPBancoDoBrasil==4) 
        {
            $aux = 1;
            $headerABBancoDoBrasilCP = str_replace('00011C9803', '000'.$loteCPBancoDoBrasil.'1C9805', $headerAB);
            fwrite($file,$headerABBancoDoBrasilCP);
                for($contadorBancodoBrasilCP;$aux<=$contadorBancodoBrasilCP;$aux++)
                {
                    
                    fwrite($file,$segmentoABBancodoBrasilCP[$aux]);
                    
                }
            $trailerLoteBancoDoBrasilCP = str_replace('0001','000'.$loteCPBancoDoBrasil,$trailerLote);   // Lote de Serviço
            $trailerLoteBancoDoBrasilCP = str_replace(str_pad($j+1, 6, '0', STR_PAD_LEFT ),str_pad($n+1, 6, '0', STR_PAD_LEFT ),$trailerLoteBancoDoBrasilCP);  //Quantidade de registros do lote
            $trailerLoteBancoDoBrasilCP = str_replace(str_pad($valorParcialOutrosBancosCC, 16, '0', STR_PAD_LEFT ),str_pad($valorParcialBancoDoBrasilCP, 16, '0', STR_PAD_LEFT ),$trailerLoteBancoDoBrasilCP);//Somatório de valores do Lote
            fwrite($file,$trailerLoteBancoDoBrasilCP);
            $quantidadeLotes++;
            $quantidadeRegistrosTotal += $n+1;
            
            
        }
     
    

    /*CONTA BANCO DO BRASIL*/
   
   /*                              TRAILER DO ARQUIVO                             */
  if($arquivo==0){
    $trailerArquivo = 0;
    $trailerArquivo = "001";//Código banco na compensação
    $trailerArquivo.= "9999";//Lote Serviço
    $trailerArquivo.= "9";//Tipo de registro
    $trailerArquivo.= "         ";//Uso exclusivo FEBRABAN/CNAB
    $trailerArquivo.= "00000".$quantidadeLotes;//Quantidade de lotes do arquivo
    $trailerArquivo.= str_pad($quantidadeRegistrosTotal,6,'0',STR_PAD_LEFT);//Quantidade de registro de arquivos
    $trailerArquivo.= "000000";//Qtde. de Contas p/Conc..(LOTES)
    $trailerArquivo.= str_pad(" ", 205, ' ', STR_PAD_RIGHT );//Espaços em branco
    $trailerArquivo.= "\r\n";//Quebra de linha
    fwrite($file,$trailerArquivo);
    /*                              TRAILER DO ARQUIVO                             */
    fclose($file);
    //Evitar fazer o download quando as váriaveis não forem inciadas
    if(isset($segmentoA) && isset($segmentoB)){
        //header("Location: download.php");
        exit;
    } 
   
  }
  else
  {

  }
}
?>
  <link href="style.css" rel="stylesheet">
  <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
  <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <div class="container login-container">
      <div class="col-xs-3 ">
          <h3>CRIAR ARQUIVO CNAB 240</h3>
    <form method="post" autocomplete="off" action="" enctype="multipart/form-data">                          
        <div class="col-xs-3">
            <label>CNPJ da empresa</label>
        </div>
        <div class="col-xs-3">
            <select class="form-control" id="cnpj" name="cnpj">
                <option value="">CNPJ</option>
            </select>
        </div>
        <br>
        <div class="col-xs-3">
            <label>N° do convênio</label>
        </div>
        <div class="col-xs-3">
            <select class="form-control" id="numero_convenio" name="numero_convenio">
                <option value="">N° do Convênio</option>
            </select>
        </div>
        <br> 
        <div class="col-xs-3">
            <label>Agência mantenedora da Conta</label>
        </div> 
        <div class="col-xs-3">
            <select class="form-control" id="agencia" name="agencia">
                <option value="">Agência</option>
            </select>
        </div>
        <br> 
        <div class="col-xs-3">
            <label>Dígito verificado da Agência</label>
        </div> 
        <div class="col-xs-3">
            <select class="form-control" id="digito_agencia" name="digito_agencia">
                <option value="">Dígito</option>
            </select>
        </div >
        <br>  
        <div class="col-xs-3">
            <label>Número da conta corrente</label>
        </div> 
        <div class="col-xs-3">
            <select class="form-control" id="conta" name="conta">
                <option value="">Conta</option>
            </select>
        </div>
        <br>
        <div class="col-xs-3">
            <label>Dígito verificador da conta</label>
        </div>                                  
        <div class="col-xs-3">
            <select class="form-control" id="digito_conta" name="digito_conta">
                <option value="">Dígito</option>
            </select>
        </div>  
        <div class="col-xs-3">
            <label>Dígito verificador da Ag/Conta</label>
        </div>
        <div class="col-xs-3">
            <select class="form-control" id="digito_ag_conta" name="digito_ag_conta">
                <option value="">Dígito Verficador</option>
            </select>
        </div>
        <br>
        <div class="col-xs-9">
            <label>Nome da empresa</label>
        </div> 
        <div class="col-xs-9">
            <select class="form-control" id="nome_empresa" name="nome_empresa">
                <option value="">Nome da empresa</option>
            </select>
        </div>
    <br>
      <div class="col-xs-3">
          <label>Data do pagamento</label>
      </div>                                 
    
        <div class="col-xs-3">
            <input class="form-control" id="data_pagamento" name="data_pagamento" type="date">
        </div>
        <br>
         <div class="form-check">
       <input class="form-check-input" type="checkbox" value="1" id="file" name="file">
        <label class="form-check-label">
            Arquivos separados
        </label>
        </div> 
        <input class="form-control-file" type="file" name="fileUpload">
        <!-- Botão enviar -->
        <br>
        <div class="col-xs-3">
            <button type="submit" class="btn btn-primary " id="btGerarArquivo">
                <span class="glyphicon glyphicon-file" aria-hidden="true"></span> Gerar Arquivo
            </button> 
        </div>
        <br>
    <br>

    </div> 
    </form>
    <p>Versão: 1.0 - Desenvolvido por Gabriel Keven Domingues de Souza - gabriel-keven@outlook.com</p>
</div> 
</div> 
<script>
  
</script>
