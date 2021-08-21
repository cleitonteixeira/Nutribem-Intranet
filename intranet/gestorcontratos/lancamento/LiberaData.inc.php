<?php
    if (!isset($_SESSION)) session_start();
    require_once("../control/banco/conexao.php");
    require_once("../control/arquivo/funcao/Dados.php");
    require_once("../control/classes/phpMailer/class.phpmailer.php");
    if(isset( $_GET['unidade'] )){
        $valor = isset( $_GET['unidade'] ) ? (int)$_GET['unidade'] : 0;
        getUser($valor);
    }
    function getUser($cod){
        $conexao = conexao::getInstance();
        $sql = "SELECT * FROM usuarios WHERE idusuarios IN (SELECT Usuario_idUsuario FROM unidadefuser WHERE Unidade_idUnidade = ?)";
        $stm = $conexao->prepare($sql);
        $stm->bindValue(1, $cod);
        $stm->execute();
        $row = $stm->fetchAll(PDO::FETCH_OBJ);
        $Dados = array();
        foreach($row as $r){
            $Itens = [
                "idusuarios" => $r->idusuarios,
                "Nome" => utf8_decode($r->Nome)
            ];
            array_push($Dados, $Itens);
        }
        //echo json_encode($stm->fetch(PDO::FETCH_ASSOC));
        echo json_encode($Dados);
    }
    if(isset( $_POST['un'] )){
        $valor = isset( $_POST['un'] ) ? (int)$_POST['un'] : 0;
        $dLiberada = strtotime( $_POST['dataLiberada'] );
        $dLimite = strtotime( $_POST['dataLimite'] );
        $user = isset( $_POST['user'] ) ? (int)$_POST['user'] : 0;
        //echo $valor." - ".$dLiberada." - ".$dLimite." - ".$user;
        //var_dump($valor);
        
        EnviaEmail($valor, $dLiberada, $dLimite, $user);
    }
    function EnviaEmail($valor, $dLiberada, $dLimite, $user){
        //echo $valor." - ".$dLiberada." - ".$dLimite." - ".$user;
        //var_dump($valor);
        $conexao = conexao::getInstance();
        try{
            $conexao->beginTransaction();
            $sql = "SELECT MAX(cda.idControleData) AS ControleData, cd.Nome AS Unidade, u.Nome AS Usuario, u.Email FROM usuarios u INNER JOIN controledata cda ON cda.Usuario_idUsuario = u.idusuarios INNER JOIN unidadefaturamento uf ON uf.idUnidadeFaturamento = ? INNER JOIN cadastro cd ON cd.idCadastro = uf.Cadastro_idCadastro WHERE u.idusuarios = ?";
            $stm = $conexao->prepare($sql);
            $stm->bindParam(1, $valor);
            $stm->bindParam(2, $user);
            $stm->execute();
            $row = $stm->fetch(PDO::FETCH_OBJ);

            if($row->ControleData == null){
                $cda = 1;
            }else{
                $cda = $stm->rowCount()+1;
            }

            $cod = "LD.".str_pad($cda,3,0,STR_PAD_LEFT).".".date("Y").'.'.str_pad($user,3,0,STR_PAD_LEFT);
            
            $dl = date("Y-m-d",$dLiberada);
            $dlt = date("Y-m-d",$dLimite);
            $sql = "INSERT INTO controledata (Usuario_idUsuario, Autorizador, DataLiberada, CodControle, Unidade_idUnidade, dLimite) VALUES(?,?,?,?,?,?);";
            $stm = $conexao->prepare($sql);
            $stm->bindParam(1, $user);
            $stm->bindParam(2, $_SESSION['idusuarios']);
            $stm->bindParam(3, $dl);
            $stm->bindParam(4, $cod);
            $stm->bindParam(5, $valor);
            $stm->bindParam(6, $dlt);
            $stm->execute();

            $data_envio 	= date('d/m/Y');
            $hora_envio 	= date('H:i:s');

            $MsgEmail = "
                <style type='text/css'>
                  body {
                  margin:0px;
                  font-family:Verdana;
                  font-size:12px;
                  color: #666666;
                  }
                  a{
                  color: #666666;
                  text-decoration: none;
                  }
                  a:hover {
                  color: #FF0000;
                  text-decoration: none;
                  }
                  .solicitante{
                    color: #FF0401;
                  }
                </style>
                <html>
                    <head>
                    </head>
                    <body>
                        <table width='100%' border='0' cellspacing='0' cellpadding='20'>
                            <tr>
                                <td>
                                    <p>Os lançamentos para o dia ".date("d/m/Y",$dLiberada)." foi autorizada por ".$_SESSION['Nome']."</p>

                                    <p><strong>DATA LIBERADA: </strong>". date("d/m/Y",$dLiberada) ."</p>
                                    <p><strong>DATA LIMITE: </strong>". date("d/m/Y",$dLimite)."</p>
                                    <p><strong>USUÁRIO AUTORIZADO: </strong>". utf8_decode($row->Usuario) ."</p>
                                    <p><strong>UNIDADE: </strong>". utf8_decode($row->Unidade) ."</p>
                                    <p><strong>CODIGO PARA LANÇAMENTO RETROATIVO: </strong>". $cod ."</p>

                                    <br />
                                    <p>PARA USAR BASTA ACESSAR O SISTEMA GESTOR DE CONTRATOS, IR AO MENU DE LANCAMENTO SELECIONAR A OPÇÃO LANCAMENTO RETROATIVO E INFORMAR O CODIGO ACIMA.</p>
                                    <br />
                                    <p>Este e-mail foi enviado em <b> $data_envio </b> às <b> $hora_envio </b></p>
                                </td>
                            </tr>
                        </table>
                    </body>
                </html>
            ";
            //INICIO ENVIO DE E-MAIL
            $email = new PHPMailer();
            $email->CharSet = 'UTF-8';
            $email->From		= 'contato@nutribemrefeicoescoletivas.com.br';
            $email->FromName	= 'Gestor de Contratos';
            $email->Subject		= 'Gestor de Contratos: AUTORIZACAO LANCAMENTO RETROATIVO';
            $email->IsHTML(true); // Define que o e-mail será enviado como HTML
            $email->Body		= $MsgEmail;

            $email->AddAddress( 'cleitonteixeirasantos@gmail.com' , 'Cleiton Teixeira dos Santos' );
            $email->AddAddress( $row->Email, $row->Usuario );
            
            if($_SESSION['email'] != 'super.adm@nutribemrefeicoescoletivas.com.br' && $_SESSION['email'] != 'laravieira@nutribemrefeicoescoletivas.com.br'){
                $email->AddCC($_SESSION['Email'], utf8_decode($_SESSION['Nome'])); // Copia
            }
            $email->AddCC( 'super.adm@nutribemrefeicoescoletivas.com.br', 'Carlos Magno'); // Copia
            
            //$email->AddCC('virgiliofaria@nutribemrefeicoescoletivas.com.br', 'Virgilio Faria'); // Copia
            //$email->AddCC('anderson@nutribemrefeicoescoletivas.com.br', 'Anderson R. M. Dengo'); // Copia
            //$email->AddCC( $EmailRespon, $NomeRespon); // Copia
            //$email->AddCC('faturamento1@nutribemrefeicoescoletivas.com.br', 'Faturamento'); // Copia
            //$email->AddCC('faturamento2@nutribemrefeicoescoletivas.com.br', 'Faturamento'); // Copia
            $email->AddCC('laravieira@nutribemrefeicoescoletivas.com.br', 'Lara Vieira'); // Copia
            $enviado = $email->Send();
            // Limpa os destinatários e os anexos
            $email->ClearAllRecipients();
            $email->ClearAttachments();
            // Exibe uma mensagem de resultado
            //FIM ENVIO DE E-MAIL
            //echo json_encode($stm->fetch(PDO::FETCH_ASSOC));
            $Dados = array();
            $Itens = [
                    "resultado" => utf8_decode("Sucesso")
                ];
            array_push($Dados, $Itens);
            $conexao->commit();
        }catch(PDOException $e){
            $conexao->rollBack();
            $Dados = array();
            $Itens = [
                    "resultado" => utf8_decode("Erro")
                ];
            array_push($Dados, $Itens);
        }
        echo json_encode($Dados);
        //echo json_encode($stm->fetchAll(PDO::FETCH_ASSOC));
        $pdo = null;
    }
    if(isset( $_POST['codigo'] )){
        $valor = isset( $_POST['codigo'] );
        confimaCodigo($valor);
    }
    function confimaCodigo($v){
        //echo $_POST['codigo'];
        //echo $valor." - ".$dLiberada." - ".$dLimite." - ".$user;
        //var_dump($valor);
        $conexao = conexao::getInstance();
        //echo "SELECT * FROM controledata WHERE CodControle = ".$_POST['codigo']." AND Usuario_idUsuario = ".$_SESSION['idusuarios']." AND Ativo = 'S' LIMIT 1;";
        $agora 	= date('Y-m-d');
        $sql = "SELECT cd.DataLiberada, cd.Unidade_idUnidade, cd.CodControle, cda.Nome AS Unidade, cd.dLimite FROM controledata cd INNER JOIN unidadefaturamento uf ON uf.idUnidadeFaturamento = cd.Unidade_idUnidade INNER JOIN cadastro cda ON cda.idCadastro = uf.Cadastro_idCadastro WHERE cd.CodControle = ? AND cd.Usuario_idUsuario = ? AND cd.Ativo = 'S' AND cd.dLimite > ? LIMIT 1;";
        $stm = $conexao->prepare($sql);
        $stm->bindParam(1, $_POST['codigo']);
        $stm->bindParam(2, $_SESSION['idusuarios']);
        $stm->bindParam(3, $agora);
        $stm->execute();
        $row = $stm->fetch(PDO::FETCH_OBJ);
        if($stm->rowCount() == 1){
            $Dados = array();
            $Itens = [
                    "resultado" => utf8_decode("Sucesso"),
                    "dataLiberada" => $row->DataLiberada,
                    "dataLiberada1" => date("d/m/Y", strtotime($row->DataLiberada)),
                    "unidade" => $row->Unidade_idUnidade,
                    "nUnidade" => $row->Unidade,
                    "dLimite" => $row->dLimite,
                    "dLimite1" => date("d/m/Y", strtotime($row->dLimite)),
                    "cdl" => $row->CodControle
                    
                ];
            array_push($Dados, $Itens);
        }else{
            $Dados = array();
            $Itens = [
                    "resultado" => utf8_decode("Erro")
                ];
            array_push($Dados, $Itens);
        }
        echo json_encode($Dados);
        //echo json_encode($stm->fetchAll(PDO::FETCH_ASSOC));
        $pdo = null;
    }
?>