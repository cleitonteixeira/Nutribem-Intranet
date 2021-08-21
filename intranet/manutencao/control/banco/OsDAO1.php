<?php
if (!isset($_SESSION)) session_start();
$troca = array('linha',',');
$Trocas = array(' ','-','.',')','(','/');
require_once("../arquivo/funcao/Outras.php");
require_once("../arquivo/header/Header.php");
require_once("conexao.php");
// Atribui uma conexão PDO
require_once("../../control/classes/phpMailer/class.phpmailer.php");

$conexao = conexao::getInstance();
if(isset($_POST['OS']) && $_POST['OS']  ==  "Cadastrar"){
    try{
        $conexao->beginTransaction();

        $DataHoraAbertura = date("Y-m-d H:i:s");
        $idUnidade = $_POST['unidade'];
        $Ano = date("Y");
        $UnidadeC = str_pad($idUnidade, 3, 0, STR_PAD_LEFT);
        $Quant = 1;
        $nOS = "OS.".$UnidadeC.".".$Ano.".".str_pad($Quant, 2, 0, STR_PAD_LEFT);
        $sql = 'SELECT nOS FROM os WHERE Unidade_idUnidade = ?;';
        $stm = $conexao->prepare($sql);
        $stm->bindValue(1, $idUnidade);
        $stm->execute();
        $nOSL = array();
        while($row = $stm->fetch(PDO::FETCH_OBJ)){
            array_push($nOSL, $row->nOS);
        }
        while(in_array($nOS, $nOSL)){
            $Quant += 1;
            $nOS = "OS.".$UnidadeC.".".$Ano.".".str_pad($Quant, 2, 0, STR_PAD_LEFT);
        }

        $sql = "INSERT INTO os( Unidade_idUnidade, Responsavel_idResponsavel, DataHoraAbertura, nOS ) VALUES (?, ?, ?, ?);";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(1, $idUnidade);
        $stmt->bindParam(2, $_SESSION['idusuarios']);
        $stmt->bindParam(3, $DataHoraAbertura);
        $stmt->bindParam(4, $nOS);
        $stmt->execute();

        $idOs = $conexao->lastInsertId();
        $z = 0;
        foreach ($_SESSION['equipamento'] as $x) {
            $sql  = "SELECT idEquipamento FROM equipamento WHERE Codigo = ? LIMIT 1;";
            $stmt = $conexao->prepare($sql);
            $stmt->bindParam(1, $x['id']);
            $stmt->execute();
            $idEquipamento = $stmt->fetch(PDO::FETCH_OBJ);
            
            $sql = "INSERT INTO itemos (OS_idOS, Equipamento_idEquipamento, Comentario) VALUES (?, ?, ?);";
            $stmt = $conexao->prepare($sql);
            $stmt->bindParam(1, $idOs);
            $stmt->bindParam(2, $idEquipamento->idEquipamento);
            $stmt->bindParam(3, $x['defeito']);
            $stmt->execute();
            $idItemOS = $conexao->lastInsertId();
            
            $k = $_FILES['img'.$z];
            $limit = count($k['name']);
            $x = 0;
            while($x < $limit){
                $Extensao   = strrchr($k["name"][$x], '.');
                $Nome = md5(uniqid(time())) . $Extensao;

                if(!is_dir("../../equipamento/doc/")){
                    mkdir("../../equipamento/doc/");
                }

                $NomePasta  = '../../equipamento/doc/'.$Nome;
                if(move_uploaded_file($k["tmp_name"][$x], $NomePasta)){
                    $sql = "INSERT INTO anexoos (Item_idItem, Arquivo, nFantasia) VALUES (?, ?, ?);";
                    $stmt = $conexao->prepare($sql);
                    $stmt->bindParam(1, $idItemOS);
                    $stmt->bindParam(2, $k["name"][$x]);
                    $stmt->bindParam(3, $Nome);
                    $stmt->execute();
                }

                $x++;
            }
            $z++;
        }
        $conexao->commit();

        $data_envio     = date('d/m/Y');
        $hora_envio     = date('H:i:s');
        $MsgEmail = "
        <html>
            <head>
            </head>
            <body>
                <table width='100%' border='0' cellspacing='0' cellpadding='20 background='".BASE."img/Fundo.png'>
                    <tr>
                        <td>
                            <p><strong>Nova OS disponível no sistema.</strong></p>
                            <p>OS: <strong>".$nOS."</strong> solicitada por <strong>".$_SESSION['Nome']."</strong></p>
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
        $email->From        = 'contato@nutribemrefeicoescoletivas.com.br';
        $email->FromName    = 'Manutencao On-line';
        $email->Subject     = 'Manutencao On-line: NOVA OS ENVIADA '.$nOS;
        $email->IsHTML(true); // Define que o e-mail será enviado como HTML
        $email->Body        = $MsgEmail;

        $email->AddAddress( $_SESSION['Email'], $_SESSION['Nome']); // Copia
        $email->AddAddress( 'manutencao@nutribemrefeicoescoletivas.com.br', 'Manutencao Nutribem');
        $email->AddCC( 'ti@nutribemrefeicoescoletivas.com.br' , 'TI Nutribem' );
        
        $enviado = $email->Send();
        // Limpa os destinatários e os anexos
        $email->ClearAllRecipients();
        $email->ClearAttachments();
        // Exibe uma mensagem de resultado
        //FIM ENVIO DE E-MAIL
        unset($_SESSION['codigos']);
        unset($_FILES);
        unset($_SESSION['equipamento']);
        unset($_SESSION['cont']);
        echo '
            <div class="alert alert-success">
                <p><strong>Sucesso!</strong> Sucesso ao Cadastrar Equipamento!</p>
                <p>Você será redirecionado em alguns segundos. Caso não seja, clique <a href="'.BASE.'ordemservico/OS.php?id='.$idOs.'">aqui</a>.</p>
            </div>
            ';
        header('Refresh: 5;URL='.BASE.'ordemservico/OS.php?id='.$idOs);exit;
    }catch(PDOException $er){
        $conexao->rollBack();
        echo '
            <div class="alert alert-danger">
                <p><strong>Falha!</strong> Falha ao Cadastrar Equipamento!</p>
                <p><strong>O sistema apresentou o seguinte erro:</strong>'.$er->getMessage().'</p>
                <p>Você será redirecionado em alguns segundos. Caso não seja clique <a href="'.BASE.'ordemservico/NovaOS.php">aqui</a>.</p>
            </div>
            ';
        header('Refresh: 5;URL='.BASE.'ordemservico/NovaOS.php');exit;
    }

}elseif (isset($_POST['Aceite']) && $_POST['Aceite']  ==  "Aceitar") {
    try{
        $conexao->beginTransaction();
        $idOs   = $_POST['os'];
        $DataAg = $_POST['dataag'];
        $DataAc = date("Y-m-d H:i:s");
        $sql  = "UPDATE os SET DataHoraAc = ?, DataAgenda = ?, Manutencao_idManutencao = ? WHERE idOS = ?;";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(1, $DataAc);
        $stmt->bindParam(2, $DataAg);
        $stmt->bindParam(3, $_SESSION['idusuarios']);
        $stmt->bindParam(4, $idOs);
        $stmt->execute();
        
        $sql  = 'SELECT * FROM usuarios WHERE idusuarios = (SELECT Responsavel_idResponsavel FROM os WHERE idOs = ?);'; 
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(1, $idOs);
        $stmt->execute();
        $rest = $stmt->fetch(PDO::FETCH_OBJ);
        $data_envio     = date('d/m/Y');
        $hora_envio     = date('H:i:s');

        $MsgEmail = "
        <html>
            <head>
            </head>
            <body>
                <table width='100%' border='0' cellspacing='0' cellpadding='20 background='".BASE."img/Fundo.png'>
                    <tr>
                        <td>
                            <p>OS: <strong>".$nOS."</strong> agendada para o dia ".date("d/m/Y", strtotime($DataAg))." por <strong>".$_SESSION['Nome']."</strong></p>
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
        $email->From        = 'contato@nutribemrefeicoescoletivas.com.br';
        $email->FromName    = 'Manutencao On-line';
        $email->Subject     = 'Manutencao On-line: OS Agendada '.$nOS;
        $email->IsHTML(true); // Define que o e-mail será enviado como HTML
        $email->Body        = $MsgEmail;

        $email->AddAddress( $rest->Email, $rest->Nome ); // Copia
        $email->AddAddress( 'manutencao@nutribemrefeicoescoletivas.com.br', 'Manutencao Nutribem');
        $email->AddCC( 'ti@nutribemrefeicoescoletivas.com.br' , 'TI Nutribem' );
        
        $conexao->commit();

        $enviado = $email->Send();
        // Limpa os destinatários e os anexos
        $email->ClearAllRecipients();
        $email->ClearAttachments();
        // Exibe uma mensagem de resultado
        //FIM ENVIO DE E-MAIL
        echo '
            <div class="alert alert-success">
                <p><strong>Sucesso!</strong> Sucesso ao Aceitar OS!</p>
                <p>Você será redirecionado em alguns segundos. Caso não seja, clique <a href="'.BASE.'ordemservico/OS.php?id='.$idOs.'">aqui</a>.</p>
            </div>
            ';
        header('Refresh: 5;URL='.BASE.'equipamento/ordemservico/OS.php?id='.$idOs);exit;
    }catch(PDOException $er){
        $conexao->rollBack();
        echo '
            <div class="alert alert-danger">
                <p><strong>Falha!</strong> Falha ao Aceitar OS!</p>
                <p><strong>O sistema apresentou o seguinte erro:</strong>'.$er->getMessage().'</p>
                <p>Você será redirecionado em alguns segundos. Caso não seja clique <a href="'.BASE.'ordemservico/OS.php?id='.$idOs.'">aqui</a>.</p>
            </div> 
            ';
        header('Refresh: 5;URL='.BASE.'ordemservico/OS.php?id='.$idOs);exit;
    }
}elseif (isset($_POST['Comentario']) && $_POST['Comentario']  ==  "Comentar") {
    try{
        $DataHora = date("Y-m-d H:i:s");
        $Comentario   = utf8_encode($_POST['comentario']);
        $idOs   = $_POST['os'];

        $conexao->beginTransaction();
        $sql  = "INSERT INTO comentarioos ( Usuario_idUsuario, OS_idOs, Comentario, DataHora ) VALUES ( ?, ?, ?, ? );";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(1, $_SESSION['idusuarios']);
        $stmt->bindParam(2, $idOs);
        $stmt->bindParam(3, $Comentario);
        $stmt->bindParam(4, $DataHora);
        $stmt->execute();

        if(isset($_POST['BTFinaliza'])){
            $sql  = "UPDATE os SET DataHoraFinalizada = ? WHERE idOS = ?;";
            $stmt = $conexao->prepare($sql);
            $stmt->bindParam(1, $DataHora);
            $stmt->bindParam(2, $idOs);
            $stmt->execute();
        } 
        $conexao->commit();
        echo '
            <div class="alert alert-success">
                <p><strong>Sucesso!</strong> Sucesso ao Adicionar Comentário na OS!</p>
                <p>Você será redirecionado em alguns segundos. Caso não seja, clique <a href="'.BASE.'ordemservico/OS.php?id='.$idOs.'">aqui</a>.</p>
            </div>
            ';
        header('Refresh: 5;URL='.BASE.'ordemservico/OS.php?id='.$idOs);exit;
    }catch(PDOException $er){
        $conexao->rollBack();
        echo '
            <div class="alert alert-danger">
                <p><strong>Falha!</strong> Falha ao Adicionar Comentário na OS!</p>
                <p><strong>O sistema apresentou o seguinte erro:</strong>'.$er->getMessage().'</p>
                <p>Você será redirecionado em alguns segundos. Caso não seja clique <a href="'.BASE.'ordemservico/OS.php?id='.$idOs.'">aqui</a>.</p>
            </div> 
            ';
        header('Refresh: 5;URL='.BASE.'ordemservico/OS.php?id='.$idOs);exit;
    }
}else{
    header("Location: ".BASE);
}

?>