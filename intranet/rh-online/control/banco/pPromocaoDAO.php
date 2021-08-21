<?php    
if (!isset($_SESSION)) session_start();
require_once("conexao.php");
require_once("../arquivo/funcao/Outras.php");
require_once("../arquivo/funcao/Dados.php");
require_once("../classes/phpMailer/class.phpmailer.php");
require_once("../arquivo/header/Header.php");
$conexao = conexao::getInstance();
$data_envio 	= date('d/m/Y');
$hora_envio 	= date('H:i:s');
if(isset($_POST['tipo']) && $_POST['tipo']  === "promocao"){
    try{
        $conexao->beginTransaction();
        $data = date("Y-m-d");
        $hora = date("H:i:s");
        $Voto = Anti_Injection(utf8_encode($_POST['Voto']));
        $Pendencia = Anti_Injection(utf8_encode($_POST['Pendencia']));
        $Usuario = $_SESSION['idusuarios'];
        $sql = "INSERT INTO validapendencia (Usuario_idUsuario, Pendencia_idPendencia, Data, Hora, Voto) VALUES (?,?,?,?,?);";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(1, $Usuario);
        $stmt->bindParam(2, $Pendencia);
        $stmt->bindParam(3, $data);
        $stmt->bindParam(4, $hora);
        $stmt->bindParam(5, $Voto);
        $stmt->execute();
        $rh = array(3,16);
        if(in_array($_SESSION['idusuarios'], $rh)){
            try{
                $sql = "UPDATE pendencias SET Resultado = ? WHERE idPendencias = ?;";
                $stmt = $conexao->prepare($sql);
                $stmt->bindParam(1, $Voto);
                $stmt->bindParam(2, $Pendencia);
                $stmt->execute();
                $conexao->commit();
                echo '
                    <div class="alert alert-success">
                        <p><strong>Sucesso!</strong> Voto computado com sucesso!</p>
                        <p>Você será redirecionado em alguns segundos. Caso não seja clique <a href="'.BASE.'formularios/vPromocao.php?p='.$Pendencia.'">aqui</a>.</p>
                    </div>
                    ';
                header('Refresh: 5;URL='.BASE.'formularios/vPromocao.php?p='.$Pendencia);exit;
            }catch(PDOException $e){
                $conexao->rollBack();
                echo '
                    <div class="alert alert-danger">
                        <p><strong>Falha!</strong> Erro ao computar o Voto!</p>
                        <p><strong>'.$e.'</strong></p>
                        <p>Você será redirecionado em alguns segundos. Caso não seja clique <a href="'.BASE.'formularios/vPromocao.php?p='.$Pendencia.'">aqui</a>.</p>
                    </div>
                    ';
                header('Refresh: 5;URL='.BASE.'formularios/vPromocao.php?p='.$Pendencia);exit;
            }
        }else{
            try{
                if($Voto === "Aprovada"){
                    $Voto = utf8_encode("Aprovada");
                }else{
                    $Voto = utf8_encode("Recusada");
                }
                $sql = "UPDATE pendencias SET Resultado = ? WHERE idPendencias = ?;";
                $stmt = $conexao->prepare($sql);
                $stmt->bindParam(1, $Voto);
                $stmt->bindParam(2, $Pendencia);
                $stmt->execute();
                $sql = "SELECT pm.Cargo AS aCargo, pm.*, co.*, cd.*, cn.*, cg.* FROM promocao pm INNER JOIN colaborador co ON co.idColaborador = pm.Colaborador_idColaborador INNER JOIN cadastro cd ON cd.idcadastro = co.Cadastro_idCadastro INNER JOIN contratacao cn ON cn.idContratacao = co.Contratacao_idContratacao INNER JOIN cargo cg ON cg.idCargo = cn.Cargo_idCargo WHERE idPromocao = (SELECT CodTipo FROM pendencias WHERE idPendencias = ?);";
                $stm = $conexao->prepare($sql);
                $stm->bindValue(1, $Pendencia );
                $stm->execute();
                $r = $stm->fetch(PDO::FETCH_OBJ);

                $data_envio = date('d/m/Y');
                $hora_envio = date('H:i:s');
                $motivo         = "";
                $Demissao       = "###";    
                $Admissao       = "###";    
                $Salario        = "###";    
                $colSub         = "###";
                $justificativa  = "###";    
                $cargo          = "###";
                $cargoS         = "###";
                $SalarioS       = "###";
                $jusAum         = "###";
                $aSalario       = "###";
                $jusASal        = "###";
                switch($r->Motivo){
                    case("aQuadro"):
                        $motivo         = "Aumento de Quadro";
                        $sql            = "SELECT * FROM cargo WHERE idCargo = ?";
                        $stm            = $conexao->prepare($sql);
                        $stm->bindParam(1, $r->aCargo);
                        $stm->execute();
                        $row            = $stm->fetch(PDO::FETCH_OBJ);
                        $cargoS         = $row->CodCargo."-".$row->Funcao;
                        $SalarioS       = "R$ ". number_format($row->Salario,2,',','.');
                        $jusAum         = utf8_decode($r->JustificativaAum);
                        break;
                    case("aSalario"):
                        $motivo = "Aumento de Salário";
                        $jusASal = utf8_decode($r->JustSalario);
                        $aSalario = "R$ ". number_format($r->Salario,2,'.',',');
                        break;
                    case("substituicao"):
                        $motivo = "Substituição";
                        $sql = "SELECT ca.CodCargo, ca.Funcao, ca.Salario, cad.Nome, co.dDemissao, co.dAdmissao, his.Historico, his.Justificativa FROM cargo ca INNER JOIN colaborador col ON col.idColaborador = ? INNER JOIN contratacao co ON co.idContratacao = col.Contratacao_idContratacao INNER JOIN cadastro cad ON cad.idCadastro = col.Cadastro_idCadastro INNER JOIN historico his ON his.Colaborador_idColaborador = col.idColaborador WHERE ca.idCargo = co.Cargo_idCargo AND his.Historico = 'DemissÃ£o'";
                        $stm = $conexao->prepare($sql);
                        $stm->bindValue(1, $r->ColSub);
                        $stm->execute();
                        $row = $stm->fetch(PDO::FETCH_OBJ);
                        $Demissao = date('d/m/Y', strtotime($row->dDemissao));
                        $Admissao = date('d/m/Y', strtotime($row->dAdmissao));
                        $Salario = "R$ ". number_format($row->Salario,2,',','.');
                        $colSub = utf8_decode($row->Nome);
                        $justificativa = utf8_decode($row->Justificativa);
                        $cargo = utf8_decode($row->CodCargo).' - '.$row->Funcao;
                        break;
                }
                $Nome       = $r->CodColaborador." - ".$r->Nome;
                $sql = "SELECT cem.Nome AS Empresa, cem.CNPJ, c.idColaborador, c.CodColaborador, u.Email, ca.Nome AS Unidade, un.idUnidade, ch.Usuario_idUsuario AS Responsavel, u.Nome AS Imediato FROM colaborador c INNER JOIN contratacao cn ON cn.idContratacao = c.Contratacao_idContratacao INNER JOIN unidade un ON un.idUnidade = cn.Unidade_idUnidade INNER JOIN cadastro ca ON ca.idCadastro = un.Cadastro_idCadastro INNER JOIN empresa em ON em.idEmpresa = un.Empresa_idEmpresa INNER JOIN cadastro cem ON cem.idCadastro = em.Cadastro_idCadastro INNER JOIN chefia ch ON ch.Colaborador_idColaborador = c.idColaborador INNER JOIN usuarios u ON  u.idusuarios = ch.Usuario_idUsuario WHERE c.CodColaborador = ?;"; 
                $conexao = conexao::getInstance();
                $stm = $conexao->prepare($sql);
                $stm->bindValue(1, $r->CodColaborador);
                $stm->execute();
                $row        = $stm->fetch(PDO::FETCH_OBJ);
                $resp       = $row->Responsavel;
                $email      = $row->Email;
                $Imediato   = utf8_decode($row->Imediato);
                $IDUnidade  = $row->idUnidade;
                $orcamento  = utf8_decode($r->Orcamento);

                $MsgEmail = "<html><head></head><body><style type='text/css'>body{margin:0px;font-family:Verdana;font-size:12px;color: #666666;}a{color: #666666;text-decoration: none;}a:hover{color: #FF0000;text-decoration: none;}.solicitante{color: #FF0401;}</style>";
                $MsgEmail .= "<h1>Formulário de Promoção foi ".$_POST['Voto']."!</h1>";
                $MsgEmail .= "<h3><strong>Empregador: </strong>". $row->Empresa ."</h3>";
                $MsgEmail .= "<h3><strong>CNPJ nº: </strong>". $row->CNPJ ."</h3>";
                $MsgEmail .= "<h3><strong>Solicitante: </strong><span  class='solicitante'>". $row->Imediato ."</span></h3>";
                $MsgEmail .= "<p><strong>Unidade: </strong>". utf8_decode($row->Unidade) ."</p>";
                $MsgEmail .= "<p><strong>Nome do Colaborador: </strong>".utf8_decode($Nome) ."</p>";
                $MsgEmail .= "<p><strong>Cargo: </strong>". utf8_decode($r->Funcao) ."</p>";
                $MsgEmail .= "<p><strong>Data de Admissão: </strong>". date("d/m/Y", strtotime($r->dAdmissao)) ."</p>";
                $MsgEmail .= "<h3><strong>Tipo: </strong><small class='solicitante'>". $motivo ."</small></h3>";
                $MsgEmail .= "<p><strong>Tipo Vaga: </strong>". utf8_decode($r->TipoVaga) ."</p>";
                $MsgEmail .= "<p><strong>Perfil da vaga: </strong>". $r->PerfilVaga ."</p>";
                $MsgEmail .= "<p><strong>Data Prevista: </strong>". date("d/m/Y", strtotime($r->DataPrev)) ."</p>";
                $MsgEmail .= "<p><strong>Esta solicitação está prevista no orçamento: </strong> ". $orcamento ." </p>";
                $MsgEmail .= "<h3>Aumento de Quadro</h3>";
                $MsgEmail .= "<p><strong>Cargo: </strong> ". $cargoS ."<strong> Salário: </strong> ". $SalarioS ."</p>";
                $MsgEmail .= "<p><strong>Justificativa: </strong>". $jusAum ."</p>";
                $MsgEmail .= "<h3>Substituição</h3>";
                $MsgEmail .= "<p><strong>Nome do Colaborador a ser substituido: </strong> ". $colSub ."<strong> Cargo: </strong>". $cargo ."</p>";
                $MsgEmail .= "<p><strong>Admissão: </strong> ". $Admissao ." <strong> Demissão: </strong> ". $Demissao ." <strong> Último Salário: </strong> ". $Salario ;"</p>";
                $MsgEmail .= "<p><strong>Justificativa da Demissão: </strong> ". $justificativa ." </p>";
                $MsgEmail .= "<h3>Aumento de Salário</h3>";
                $MsgEmail .= "<p><strong>Justificativa: </strong> ". $jusASal ."</p>";
                $MsgEmail .= "<p><strong>Salário: </strong> ". $aSalario ."</p>";
                $MsgEmail .= "<p>Este e-mail foi enviado em <b> $data_envio </b> às <b> $hora_envio </b></p>";
                $MsgEmail .= "</body></html>";
                //echo $MsgEmail;
                $email = new PHPMailer();
                $email->Body		= $MsgEmail;

                if($_SESSION["idusuarios"] != 1){
                    $email->AddAddress( $row->Email , $row->Imediato );
                    $email->AddCC('rh@nutribemrefeicoescoletivas.com.br', 'RH Nutribem'); // Copia
                    $email->AddCC('rh02@nutribemrefeicoescoletivas.com.br', 'RH Nutribem'); // Copia
                    $email->AddCC('virgiliofaria@nutribemrefeicoescoletivas.com.br', 'Virgilio Faria'); // Copia
                    $email->AddCC('anderson@nutribemrefeicoescoletivas.com.br', 'Anderson R. M. Dengo'); // Copia
                    //$email->AddCC('estagiario.dp@nutribemrefeicoescoletivas.com.br', 'Estagiario(a) DP'); // Copia
                    //$email->AddCC('estagiario.rh@nutribemrefeicoescoletivas.com.br', 'Estagiario(a) RH');
                }else{
                    $email->AddAddress( 'cleitonteixeirasantos@gmail.com' , 'Cleiton Teixeira' );
                    $email->AddCC('rh@nutribemrefeicoescoletivas.com.br', 'RH Nutribem'); // Copia
                }
                $enviado = $email->Send();
                // Limpa os destinatários e os anexos
                $email->ClearAllRecipients();
                $email->ClearAttachments();
                if ($enviado) {
                    $conexao->commit();
                    echo '
                        <div class="alert alert-success">
                            <p><strong>Sucesso!</strong> Voto computado com sucesso! E-mail enviado com sucesso!</p>
                            <p>Você será redirecionado em alguns segundos. Caso não seja clique <a href="'.BASE.'formularios/vPromocao.php?p='.$Pendencia.'">aqui</a>.</p>
                        </div>
                        ';
                    header('Refresh: 5;URL='.BASE.'formularios/vPromocao.php?p='.$Pendencia);exit;
                } else {
                    $conexao->commit();
                    echo '
                    <div class="alert alert-warning">
                        <p><strong>Sucesso!</strong> Voto computado com sucesso! E-mail não enviado com sucesso!</p>
                        <p>Você será redirecionado em alguns segundos. Caso não seja clique <a href="'.BASE.'formularios/vPromocao.php?p='.$Pendencia.'">aqui</a>.</p>
                    </div>
                    ';
                    header('Refresh: 5;URL='.BASE.'formularios/vPromocao.php?p='.$Pendencia);exit;
                }
            }catch(PDOException $e){
                $conexao->rollBack();
                echo '
                    <div class="alert alert-danger">
                        <p><strong>Falha!</strong> Erro ao computar o Voto!</p>
                        <p><strong>'.$e.' </strong></p>
                        <p>Você será redirecionado em alguns segundos. Caso não seja clique <a href="'.BASE.'formularios/vPromocao.php?p='.$Pendencia.'">aqui</a>.</p>
                    </div>
                    ';
                header('Refresh: 5;URL='.BASE.'formularios/vPromocao.php?p='.$Pendencia);exit;
            }
        }
    }catch(PDOException $e){
        $conexao->rollBack();
        echo '
            <div class="alert alert-danger">
                <p><strong>Falha!</strong> Erro ao computar o Voto!</p>
                <p><strong>'.$e.' </strong></p>
                <p>Você será redirecionado em alguns segundos. Caso não seja clique <a href="'.BASE.'formularios/vPromocao.php?p='.$Pendencia.'">aqui</a>.</p>
            </div>
            ';
        header('Refresh: 5;URL='.BASE.'formularios/vPromocao.php?p='.$Pendencia);exit;
    }
}else{
    header("Location: http://www.nutribemrefeicoescoletivas.com.br/intranet/rh-online/");
}
?>