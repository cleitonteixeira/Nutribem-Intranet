<?php
if  (!isset($_SESSION)) session_start();
require_once("conexao.php");
$conexao = conexao::getInstance();
require_once("../arquivo/funcao/Outras.php");
require_once("../arquivo/funcao/Dados.php");
require_once("../arquivo/header/Header.php");
require_once("../../control/classes/phpMailer/class.phpmailer.php");
try{
    $NomeRespon = $_SESSION['Nome'];
    $EmailRespon = $_SESSION['Email'];
    $conexao->beginTransaction();
    $sql = "SELECT c.idContrato, ca.Nome AS Unidade, un.idUnidadeFaturamento FROM contrato c INNER JOIN unidadefaturamento un ON un.idUnidadeFaturamento = c.Unidade_idUnidade INNER JOIN cadastro ca ON ca.idCadastro = un.Cadastro_idCadastro INNER JOIN contratante ct ON ct.idContratante = c.Contratante_idContratante INNER JOIN cadastro cad ON cad.idCadastro = ct.Cadastro_idCadastro WHERE c.nContrato = ?;";
    $stm = $conexao->prepare($sql);
    $stm->bindValue(1, $_POST['vContrato']);
    $stm->execute();
    $ct = $stm->fetch(PDO::FETCH_OBJ);
    $nContrato = $ct->idContrato;
    $IDUnidade = $ct->idUnidade;
    $sql = 'SELECT c.idContratante, cd.Nome AS Cliente, cd.CNPJ AS CNPJ, ed.*, cb.Endereco AS eCobranca, cb.Bairro AS bCobranca, cb.CEP AS ceCobranca, cb.Cidade AS cCobranca, cb.Numero AS nCobranca, cb.UF AS uCobranca FROM contratante c INNER JOIN cadastro cd ON cd.idCadastro = c.Cadastro_idCadastro INNER JOIN endereco ed ON ed.idEndereco = c.Endereco_idEndereco INNER JOIN ecobranca cb ON cb.idECobranca = c.Cobranca_idCobranca INNER JOIN contrato cont ON cont.nContrato = ? WHERE c.idContratante = cont.Contratante_idContratante;';
    $stm = $conexao->prepare($sql);
    $stm->bindValue(1, $_POST['vContrato']);
    $stm->execute();
    $row = $stm->fetch(PDO::FETCH_OBJ);
    $sql = "SELECT * FROM ccontratante WHERE Contratante_idContratante = ?;";
    $stm = $conexao->prepare($sql);
    $stm->bindParam(1, $row->idContratante);
    $stm->execute();
    $rx = $stm->fetchAll(PDO::FETCH_OBJ);
    
    $valor2x = explode('/',$_POST['iMedicao']);
    $dataIN = $valor2x[2]."-".$valor2x[1]."-".$valor2x[0];
    $valor3x = explode('/',$_POST['fMedicao']);
    $dataFN = $valor3x[2]."-".$valor3x[1]."-".$valor3x[0];
    $sql = "SELECT * FROM lancamento WHERE Contrato_idContrato = ? AND dLancamento BETWEEN ? AND ? GROUP BY ValorUni ORDER BY dLancamento;";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(1, $nContrato);
    $stmt->bindParam(2, $dataIN);
    $stmt->bindParam(3, $dataFN);
    $stmt->execute();
    $rs = $stmt->fetchAll(PDO::FETCH_OBJ);
    $sql = "SELECT i.Servico FROM contrato c INNER JOIN itensproposta i ON i.Proposta_idProposta = c.Proposta_idProposta WHERE c.idContrato = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(1, $nContrato);
    $stmt->execute();
    $date = date("Y-m-d H:i:s");
    
    $itens = $stmt->fetchAll(PDO::FETCH_OBJ);
    //echo empty($_FILES['doc']["name"]);
    if(empty($_FILES['doc']["name"])){
        $sqli = "INSERT INTO medicao (Contrato_idContrato, Usuario_idUsuario, Medicao, dInicio, dFinal, dMedicao) VALUES (?, ?, ?, ?, ?, ?);";
        $stmt = $conexao->prepare($sqli);
        $stmt->bindParam(1, $nContrato);
        $stmt->bindParam(2, $_SESSION['idusuarios']);
        $stmt->bindParam(3, $_POST['vMedicao']);
        $stmt->bindParam(4, $dataIN);
        $stmt->bindParam(5, $dataFN);
        $stmt->bindParam(6, $date); 
        $stmt->execute();
    }else{
        $DataCad= date("Y-m-d");
        $Arquivo   = $_FILES['doc'];
        // Pega extensão da imagem
        $Extensao = strrchr($Arquivo["name"], '.');
        // Gera um nome único para a imagem
        $Nome = md5(uniqid(time())) . $Extensao;
        //Nome da pasta das imagens
        $NomePasta = '../../medicao/docs/'.$Nome;
        if(move_uploaded_file($Arquivo["tmp_name"], $NomePasta)){
            $SQL = "INSERT INTO medicao (Contrato_idContrato, Usuario_idUsuario, Medicao, dInicio, dFinal, dMedicao, Documento) VALUES (?, ?, ?, ?, ?, ?, ?);";
            $stmt = $conexao->prepare($SQL);
            $stmt->bindParam(1, $nContrato);
            $stmt->bindParam(2, $_SESSION['idusuarios']);
            $stmt->bindParam(3, $_POST['vMedicao']);
            $stmt->bindParam(4, $dataIN);
            $stmt->bindParam(5, $dataFN);
            $stmt->bindParam(6, $date); 
            $stmt->bindParam(7, $Nome);
            $stmt->execute();
        }
    }
    
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
                            <p><strong>MEDICAO: </strong>". $_POST['vMedicao'] ."</p>
                            <p><strong>CONTRATO: </strong>". $_POST['vContrato'] ."</p>
                            <p><strong>CLIENTE: </strong>". utf8_decode($row->Cliente) ."</p>
                            <p><strong>UNIDADE: </strong>". utf8_decode($ct->Unidade) ."</p>
                            <p>A medição citada acima foi enviada, aguardando a validação da mesma, responsavel pelo cadastramento <strong>". $_SESSION['Nome'] ."</strong></p>
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
        $email->Subject		= 'Gestor de Contratos: NOVA MEDICAO ENVIADA '.utf8_decode($row->Cliente);
        $email->IsHTML(true); // Define que o e-mail será enviado como HTML
        $email->Body		= $MsgEmail;

        //$email->AddAddress( 'cleitonteixeira@secservices.com.br' , 'Cleiton Teixeira dos Santos' );
    
        $email->AddAddress( $EmailRespon, $NomeRespon); // Copia
        $sql2 = "SELECT u.Nome, u.Email FROM unidadefuser uf INNER JOIN usuarios u ON u.idusuarios = uf.Usuario_idUsuario WHERE uf.Unidade_idUnidade = ? AND uf.Usuario_idUsuario NOT IN (1,4,5,26,36,37,39,42);";
        $stmt = $conexao->prepare($sql2);
        $stmt->bindParam(1, $IDUnidade);
        $stmt->execute();
        $resultado = $stmt->fetchAll(PDO::FETCH_OBJ);
        foreach($resultado as $result){
            $email->AddAddress( $result->Email, $result->Nome); // Copia
        }
        
        //$email->AddCC( 'super.adm@nutribemrefeicoescoletivas.com.br', 'Carlos Magno'); // Copia
        $email->AddCC( 'supervisao.faturamento@nutribemrefeicoescoletivas.com.br', 'Ney Francisco'); // Copia
        //$email->AddCC('virgiliofaria@nutribemrefeicoescoletivas.com.br', 'Virgilio Faria'); // Copia
        //$email->AddCC('anderson@nutribemrefeicoescoletivas.com.br', 'Anderson R. M. Dengo'); // Copia
        //$email->AddCC('faturamento1@nutribemrefeicoescoletivas.com.br', 'Faturamento 01'); // Copia
        //$email->AddCC('faturamento2@nutribemrefeicoescoletivas.com.br', 'Faturamento 02'); // Copia
        //$email->AddCC('laravieira@nutribemrefeicoescoletivas.com.br', 'Lara Vieira'); // Copia
        
        $enviado = $email->Send();
        // Limpa os destinatários e os anexos
        $email->ClearAllRecipients();
        $email->ClearAttachments();
        // Exibe uma mensagem de resultado
        //FIM ENVIO DE E-MAIL
    echo '
        <div class="alert alert-success">
            <p><strong>Sucesso!</strong> Medição salva com sucesso!</p>
            <p>Você será redirecionado em alguns segundos. Caso não seja, clique <a href="'.BASE.'medicao/Medicao.php">aqui</a>.</p>
        </div>
        ';
    header('Refresh: 5;URL='.BASE.'medicao/Medicao.php');exit;
}catch(PDOException $e){
    $conexao->rollBack();
	echo '
		<div class="alert alert-danger">
			<p><strong>Falha!</strong> Falha ao salvar medição.</p>
			<p><strong>O sistema apresentou o seguinte erro:</strong>'.$e.'</p>
			<p>Você será redirecionado em alguns segundos. Caso não seja clique <a href="'.BASE.'medicao/Medicao.php">aqui</a>.</p>
		</div>
		';
	header('Refresh: 5;URL='.BASE.'medicao/Medicao.php');exit;
}
?>