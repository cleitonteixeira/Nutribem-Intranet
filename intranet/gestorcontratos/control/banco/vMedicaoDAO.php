<?php
if (!isset($_SESSION)) session_start();
require_once("../arquivo/funcao/Outras.php");
require_once("../arquivo/funcao/Dados.php");
require_once("../arquivo/header/Header.php");
require_once("../../control/classes/phpMailer/class.phpmailer.php");
require_once("conexao.php");
$conexao    = conexao::getInstance();
if(isset($_POST['Medicao']) && $_POST['Medicao']  ==  "voto"){
    $idMedicao  = utf8_encode($_POST['idMedicao']);
    $sqlx = "SELECT m.Medicao, u.Nome, cad.Nome AS Cliente , u.Email, ca.Nome AS Unidade, ct.nContrato, ct.Unidade_idUnidade FROM medicao m INNER JOIN contrato ct ON ct.idContrato  = m.Contrato_idContrato INNER JOIN contratante cont ON cont.idContratante = ct.Contratante_idContratante INNER JOIN cadastro cad ON cad.idCadastro = cont.Cadastro_idCadastro INNER JOIN unidadefaturamento un ON un.idUnidadeFaturamento = ct.Unidade_idUnidade INNER JOIN cadastro ca ON ca.idCadastro = un.Cadastro_idCadastro INNER JOIN usuarios u ON u.idusuarios = m.Usuario_idUsuario WHERE m.idMedicao = ?;";
    $stm = $conexao->prepare($sqlx);
    $stm->bindParam( 1, $idMedicao );
    $stm->execute();
    $dados = $stm->fetch(PDO::FETCH_OBJ);
    $IDUnidade =$dados->Unidade_idUnidade;
    $NomeRespon = utf8_decode($dados->Nome);
    $EmailRespon = utf8_decode($dados->Email);
    try{
        $conexao->beginTransaction();
        if(isset($_POST["recusada"])){
            $voto       = $_POST["recusada"].", ".utf8_encode($_POST['voto']);
        }else{
            $voto       = utf8_encode($_POST['voto']);   
        }
        $dh         = date("Y-m-d H:i:s");
        $sql = "UPDATE medicao SET Situacao = ?, idFaturamento = ?, tFaturamento = ? WHERE idMedicao = ?;";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(1, $voto);
        $stmt->bindParam(2, $_SESSION['idusuarios']);
        $stmt->bindParam(3, $dh);
        $stmt->bindParam(4, $idMedicao);
        $stmt->execute();
        $conexao->commit();
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
                    <table width='100%' border='0' cellspacing='0' cellpadding='20 background='".BASE."img/Fundo.png'>
                        <tr>
                            <td>
                                <p><strong>MEDICAO: </strong>". utf8_decode($dados->Medicao) ."</p>
                                <p><strong>CONTRATO: </strong>". utf8_decode($dados->nContrato) ."</p>
                                <p><strong>CLIENTE: </strong>". utf8_decode($dados->Cliente) ."</p>
                                <p><strong>UNIDADE: </strong>". utf8_decode($dados->Unidade) ."</p>
                                <p>A seguinte medição foi : <strong>". utf8_decode($voto) ."</strong> por <strong>". $_SESSION['Nome'] ."</strong></p>
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
        $email->Subject		= 'Gestor de Contratos: VALIDACAO DE MEDICAO'.utf8_decode($dados->Cliente);
        $email->IsHTML(true); // Define que o e-mail será enviado como HTML
        $email->Body		= $MsgEmail;

        $email->AddCC( $EmailRespon, $NomeRespon); // Copia
        $sql2 = "SELECT u.Nome, u.Email FROM unidadefuser uf INNER JOIN usuarios u ON u.idusuarios = uf.Usuario_idUsuario WHERE uf.Unidade_idUnidade = ? AND uf.Usuario_idUsuario NOT IN (1,4,5,26,36,37,38,39,42) AND uf.Usuario_idUsuario IN (28, 27, 9);";
        $stmt = $conexao->prepare($sql2);
        $stmt->bindParam(1, $IDUnidade);
        $stmt->execute();
        $resultado = $stmt->fetchAll(PDO::FETCH_OBJ);
        foreach($resultado as $result){
            $email->AddAddress( $result->Email, $result->Nome); // Copia
        }
        //$email->AddAddress( 'cleitonteixeira@secservices.com.br' , 'Cleiton Teixeira dos Santos' );
        //$email->AddCC( 'super.adm@nutribemrefeicoescoletivas.com.br', 'Carlos Magno'); // Copia
        $email->AddCC( 'supervisao.faturamento@nutribemrefeicoescoletivas.com.br', 'Ney Francisco'); // Copia
        //$email->AddCC('virgiliofaria@nutribemrefeicoescoletivas.com.br', 'Virgilio Faria'); // Copia
        //$email->AddCC('anderson@nutribemrefeicoescoletivas.com.br', 'Anderson R. M. Dengo'); // Copia
        $email->AddCC('faturamento1@nutribemrefeicoescoletivas.com.br', 'Faturamento'); // Copia
        $email->AddCC('faturamento2@nutribemrefeicoescoletivas.com.br', 'Faturamento'); // Copia
        //$email->AddCC('laravieira@nutribemrefeicoescoletivas.com.br', 'Lara Vieira'); // Copia
        
        $enviado = $email->Send();
        // Limpa os destinatários e os anexos
        $email->ClearAllRecipients();
        $email->ClearAttachments();
        // Exibe uma mensagem de resultado
        //FIM ENVIO DE E-MAIL
        echo '
            <div class="alert alert-success">
                <p><strong>Sucesso!</strong>Voto Computado com Sucesso!</p>
                <p>Você será redirecionado em alguns segundos. Caso não seja, clique <a href="'.BASE.'medicao/HMedicao.php">aqui</a>.</p>
            </div>
            ';
        header('Refresh: 5;URL='.BASE.'medicao/HMedicao.php');exit;
        
    }catch(PDOException $erro_cad){
        $conexao->rollBack();
        echo '
			<div class="alert alert-danger">
			  	<p><strong>Falha!</strong> Falha ao Computar Voto...</p>
			  	<p><strong>O sistema apresentou o seguinte erro:</strong>'.$erro_cad.'</p>
				<p>Você será redirecionado em alguns segundos. Caso não seja clique <a href="'.BASE.'medicao/HMedicao.php">aqui</a>.</p>
			</div>
			';
        header('Refresh: 5;URL='.BASE.'medicao/HMedicao.php');exit;
    }
}else{
    header("Location: ".BASE);
}
?>