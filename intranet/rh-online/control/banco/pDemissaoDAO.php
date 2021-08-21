<?php    
if (!isset($_SESSION)) session_start();
require_once("conexao.php");
require_once("../arquivo/funcao/Outras.php");
require_once("../arquivo/funcao/Dados.php");
require_once("../arquivo/header/Header.php");
require_once("../../control/classes/phpMailer/class.phpmailer.php");
$email = new PHPMailer();
$conexao = conexao::getInstance();
$data_envio 	= date('d/m/Y');
$hora_envio 	= date('H:i:s');
// INÍCIO FÉRIAS

if(isset($_POST['tipo']) && $_POST['tipo']  === "demissao"){
    try{
        $conexao->beginTransaction();
        $rh = array(1,3,16,14);
        if(in_array($_SESSION['idusuarios'], $rh)){
            try{
                $data = date("Y-m-d");
                $hora = date("H:i:s");
                $Voto = Anti_Injection(utf8_encode($_POST['Voto']));
                $Pendencia = Anti_Injection(utf8_encode($_POST['Pendencia']));
                if($Voto === "Recusada"){
                    $Motivo = Anti_Injection(utf8_encode($_POST['Motivo']));    
                }else{
                    $Motivo = utf8_encode("A pendência foi aprovada!");
                }

                $Usuario = $_SESSION['idusuarios'];
                $sql = "INSERT INTO validapendencia (Usuario_idUsuario, Pendencia_idPendencia, Data, Hora, Voto) VALUES (?,?,?,?,?);";
                $stmt = $conexao->prepare($sql);
                $stmt->bindParam(1, $Usuario);
                $stmt->bindParam(2, $Pendencia);
                $stmt->bindParam(3, $data);
                $stmt->bindParam(4, $hora);
                $stmt->bindParam(5, $Voto);
                $stmt->execute();

                $sql = "UPDATE pendencias SET Resultado = ? WHERE idPendencias = ?;";
                $stmt = $conexao->prepare($sql);
                $stmt->bindParam(1, $Voto);
                $stmt->bindParam(2, $Pendencia);
                $stmt->execute();

                $conexao->commit();
                echo '
                    <div class="alert alert-success">
                        <p><strong>Sucesso!</strong> Voto computado com sucesso!</p>
                        <p>Você será redirecionado em alguns segundos. Caso não seja clique <a href="'.BASE.'formularios/vDemissao.php?p='.$Pendencia.'">aqui</a>.</p>
                    </div>
                    ';
                header('Refresh: 5;URL='.BASE.'formularios/vDemissao.php?p='.$Pendencia);exit;

            }catch(PDOException $e){
                $conexao->rollBack();
                echo '
                    <div class="alert alert-danger">
                        <p><strong>Falha!</strong> Voto não computado com sucesso!</p>
                        '.$e->getMessage().'
                        <p>Você será redirecionado em alguns segundos. Caso não seja clique <a href="'.BASE.'formularios/vDemissao.php?p='.$Pendencia.'">aqui</a>.</p>
                    </div>
                    ';
                header('Refresh: 5;URL='.BASE.'formularios/vDemissao.php?p='.$Pendencia);exit;
            }
        }else{
            try{
                $Voto = Anti_Injection(utf8_encode($_POST['Voto']));
                if($Voto === "Aprovada"){
                    $Voto = utf8_encode("Aprovada");
                }else{
                    $Voto = utf8_encode("Recusada");
                }

                $data = date("Y-m-d");
                $hora = date("H:i:s");
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

                $sql = "UPDATE pendencias SET Resultado = ? WHERE idPendencias = ?;";
                $stmt = $conexao->prepare($sql);
                $stmt->bindParam(1, $Voto);
                $stmt->bindParam(2, $Pendencia);
                $stmt->execute();

                $email->CharSet = 'UTF-8';
                $email->From		= 'contato@nutribemrefeicoescoletivas.com.br';
                $email->FromName	= 'RH-Online';
                $email->Subject		= 'RH-Online: Analise Desligamento';
                $email->IsHTML(true); // Define que o e-mail será enviado como HTML

                $v = Anti_Injection(utf8_encode($_POST['Voto']));
                try{
                    $sql = "SELECT f.*, (SELECT cad.CNPJ AS Empresa FROM empresa em INNER JOIN cadastro cad ON cad.idCadastro = em.Cadastro_idCadastro WHERE em.idEmpresa = u.Empresa_idEmpresa) AS CNPJ,(SELECT cad.Nome AS Empresa FROM empresa em INNER JOIN cadastro cad ON cad.idCadastro = em.Cadastro_idCadastro WHERE em.idEmpresa = u.Empresa_idEmpresa) AS Empresa, cdu.Nome AS Unidade, cc.Nome AS Colaborador, ct.dAdmissao AS Admissao, us.Email AS Email, us.Nome AS Imediato FROM ferias f INNER JOIN colaborador c ON c.CodColaborador = ? INNER JOIN cadastro cc ON cc.idCadastro = c.Cadastro_idCadastro INNER JOIN contratacao ct ON ct.idContratacao = c.Contratacao_idContratacao INNER JOIN unidade u ON u.idUnidade = ct.Unidade_idUnidade INNER JOIN cadastro cdu ON cdu.idCadastro = u.Cadastro_idCadastro INNER JOIN chefia ch ON ch.Colaborador_idColaborador = c.idColaborador INNER JOIN usuarios us ON us.idusuarios = ch.Usuario_idUsuario WHERE f.Colaborador_idColaborador = c.idColaborador ORDER BY f.idFerias DESC LIMIT 1"; 
                    $stm = $conexao->prepare($sql);
                    $stm->bindParam(1, $_POST['Cod']);
                    $stm->execute();
                    $row = $stm->fetch(PDO::FETCH_OBJ);
                }catch(PDOException $e){
                    echo $e;
                }
                ///EMAIL AQUI

                $v = Anti_Injection(utf8_encode($_POST['Voto']));
                $sql = "SELECT d.*, (SELECT cad.CNPJ AS Empresa FROM empresa em INNER JOIN cadastro cad ON cad.idCadastro = em.Cadastro_idCadastro WHERE em.idEmpresa = u.Empresa_idEmpresa) AS CNPJ,(SELECT cad.Nome AS Empresa FROM empresa em INNER JOIN cadastro cad ON cad.idCadastro = em.Cadastro_idCadastro WHERE em.idEmpresa = u.Empresa_idEmpresa) AS Empresa, cdu.Nome AS Unidade,c.CodColaborador AS Cod, cc.Nome AS Colaborador, ct.dAdmissao AS Admissao, us.Email AS Email, us.Nome AS Imediato FROM demissao d INNER JOIN colaborador c ON c.CodColaborador = ? INNER JOIN cadastro cc ON cc.idCadastro = c.Cadastro_idCadastro INNER JOIN contratacao ct ON ct.idContratacao = c.Contratacao_idContratacao INNER JOIN unidade u ON u.idUnidade = ct.Unidade_idUnidade INNER JOIN cadastro cdu ON cdu.idCadastro = u.Cadastro_idCadastro INNER JOIN chefia ch ON ch.Colaborador_idColaborador = c.idColaborador INNER JOIN usuarios us ON us.idusuarios = ch.Usuario_idUsuario WHERE d.Colaborador_idColaborador = c.idColaborador ORDER BY idDemissao DESC LIMIT 1"; 
                $conexao = conexao::getInstance();
                $stm = $conexao->prepare($sql);
                $stm->bindParam(1, $_POST['Cod']);
                $stm->execute();
                $res = $stm->fetch(PDO::FETCH_OBJ);
                if($v == "Aprovada"){
                    $msg = "
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
                            <h1>A seguinte Análise de Desligamento foi aprovada!</h1>
                            <h3><strong>Empresa: </strong>". utf8_decode($res->Empresa)." - ".CNPJ_Padrao($res->CNPJ) ." </p>
                            <h3><strong>Unidade: </strong>". utf8_decode($res->Unidade) ."</p>
                            <h3><strong>Superior Imediato: </strong>".utf8_decode($res->Imediato)." </p>
                            <p>
                                <strong>Colaborador: </strong>".utf8_decode($res->Cod)."-". utf8_decode($res->Colaborador)."
                                &thinsp;&thinsp;&thinsp;&thinsp;&thinsp;
                                <strong>Data de Admissão: </strong>".utf8_decode(date("d/m/Y",strtotime($res->Admissao))) ."
                            </p>
                            <p><strong>Data Pretendida para Recisão: </strong>". utf8_decode(date("d/m/Y",strtotime($res->DataRecisao)))."</p>
                            <p><strong>Tipo de Contrato: </strong>". utf8_decode($res->TipoContrato)." </p>
                            <p><strong>Advertência/Suspenção: </strong>". utf8_decode($res->AdvSup) ."</p>
                            <p><strong>Promoção: </strong>". utf8_decode($res->Promocao)."</p>
                            <p><strong>Bens da Empresa: </strong></p>
                            <ul>
                                ". utf8_decode($res->BensEmpresa)."
                            </ul>
                            <p><strong>Justifica: </strong>". utf8_decode($res->Justificativa)."</p>
                            <p><strong>Outros: </strong>". utf8_decode($res->Outros)." </p>
                                <p>Este e-mail foi enviado em <b> $data_envio </b> às <b> $hora_envio </b></p>
                            </body>
                        </html>
                      "; 
                    //Anexo
                    $email->AddAttachment( 'Analise_Desligamento_'.$_POST['Cod'].'.pdf' );
                }else{
                    $msg = "
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
                        <h1>A seguinte Análise de Desligamento foi reprovado!</h1>
                        <h3><strong>Empresa: </strong>". utf8_decode($res->Empresa)." - ".CNPJ_Padrao($res->CNPJ) ." </p>
                        <h3><strong>Unidade: </strong>". utf8_decode($res->Unidade) ."</p>
                        <h3><strong>Superior Imediato: </strong>".utf8_decode($res->Imediato)." </p>
                        <p>
                            <strong>Colaborador: </strong>".utf8_decode($res->Cod)."-". utf8_decode($res->Colaborador)."
                            &thinsp;&thinsp;&thinsp;&thinsp;&thinsp;
                            <strong>Data de Admissão: </strong>".utf8_decode(date("d/m/Y",strtotime($res->Admissao))) ."
                        </p>
                        <p><strong>Data Pretendida para Recisão: </strong>". utf8_decode(date("d/m/Y",strtotime($res->DataRecisao)))."</p>
                        <p><strong>Tipo de Contrato: </strong>". utf8_decode($res->TipoContrato)." </p>
                        <p><strong>Advertência/Suspenção: </strong>". utf8_decode($res->AdvSup) ."</p>
                        <p><strong>Promoção: </strong>". utf8_decode($res->Promocao)."</p>
                        <p><strong>Bens da Empresa: </strong></p>
                        <ul>
                            ". utf8_decode($res->BensEmpresa)."
                        </ul>
                        <p><strong>Justifica: </strong>". utf8_decode($res->Justificativa)."</p>
                        <p><strong>Outros: </strong>". utf8_decode($res->Outros)." </p>
                            <p>Este e-mail foi enviado em <b> $data_envio </b> às <b> $hora_envio </b></p>
                        </body>
                    </html>
                  "; 
                }
                
                $email->Body = $msg;

                if($_SESSION["idusuarios"] != 1){
                    $email->AddAddress( $res->Email , $res->Imediato );
                    $email->AddCC('rh@nutribemrefeicoescoletivas.com.br', 'RH Nutribem'); // Copia
                    $email->AddCC('rh02@nutribemrefeicoescoletivas.com.br', 'RH Nutribem'); // Copia
                    $email->AddCC('virgiliofaria@nutribemrefeicoescoletivas.com.br', 'Virgilio Faria'); // Copia
                    $email->AddCC('anderson@nutribemrefeicoescoletivas.com.br', 'Anderson R. M. Dengo'); // Copia
                    $email->AddCC('patricia.lopes@nutribemrefeicoescoletivas.com.br', 'Patricia Lopes - RH Nutribem'); //
                }else{
                    $email->AddAddress( 'cleitonteixeirasantos@gmail.com' , 'Cleiton Teixeira' );
                    $email->AddCC('rh@nutribemrefeicoescoletivas.com.br', 'RH Nutribem'); // Copia
                }
                $enviado = $email->Send();
                // Limpa os destinatários e os anexos
                $email->ClearAllRecipients();
                $email->ClearAttachments();
                if ($enviado) {
                    if(file_exists('Analise_Desligamento_.'.$_POST['Cod'].'.pdf')){
                        unlink('Analise_Desligamento_.'.$_POST['Cod'].'.pdf');
                    }
                    $conexao->commit();
                    echo '
                        <div class="alert alert-success">
                            <p><strong>Sucesso!</strong> Voto computado com sucesso! E-mail enviado com sucesso!</p>
                            <p>Você será redirecionado em alguns segundos. Caso não seja clique <a href="'.BASE.'formularios/vDemissao.php?p='.$Pendencia.'">aqui</a>.</p>
                        </div>
                        ';
                    header('Refresh: 5;URL='.BASE.'formularios/vDemissao.php?p='.$Pendencia);exit;

                } else {
                    if(file_exists('Analise_Desligamento_.'.$_POST['Cod'].'.pdf')){
                        unlink('Analise_Desligamento_.'.$_POST['Cod'].'.pdf');
                    }
                    $conexao->commit();
                    echo '
                    <div class="alert alert-warning">
                        <p><strong>Sucesso!</strong> Voto computado com sucesso! E-mail não enviado com sucesso!</p>
                        <p>Você será redirecionado em alguns segundos. Caso não seja clique <a href="'.BASE.'formularios/vDemissao.php?p='.$Pendencia.'">aqui</a>.</p>
                    </div>
                    ';
                    header('Refresh: 5;URL='.BASE.'formularios/vDemissao.php?p='.$Pendencia);exit;
                }
            }catch(PDOException $e){
                $conexao->rollBack();
                echo '
                    <div class="alert alert-danger">
                        <p><strong>Falha!</strong> Erro ao computar o Voto!</p>
                        <p><strong>'.$e.' </strong></p>
                        <p>Você será redirecionado em alguns segundos. Caso não seja clique <a href="'.BASE.'formularios/vDemissao.php?p='.$Pendencia.'">aqui</a>.</p>
                    </div>
                    ';
                header('Refresh: 5;URL='.BASE.'formularios/vDemissao.php?p='.$Pendencia);exit;
            }
        }
    }catch(PDOException $e){
        $conexao->rollBack();
        echo '
            <div class="alert alert-danger">
                <p><strong>Falha!</strong> Erro ao computar o Voto!</p>
                <p><strong>'.$e.' </strong></p>
                <p>Você será redirecionado em alguns segundos. Caso não seja clique <a href="'.BASE.'formularios/vDemissao.php?p='.$Pendencia.'">aqui</a>.</p>
            </div>
            ';
        header('Refresh: 5;URL='.BASE.'formularios/vDemissao.php?p='.$Pendencia);exit;
    }
}else{
    header("Location: http://www.nutribemrefeicoescoletivas.com.br/intranet/rh-online/");
}
?>