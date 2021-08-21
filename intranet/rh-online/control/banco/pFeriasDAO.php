<?php    
if (!isset($_SESSION)) session_start();
require_once("conexao.php");
$conexao = conexao::getInstance();
require_once("../arquivo/funcao/Outras.php");
require_once("../arquivo/funcao/Dados.php");
require_once("../../control/classes/phpMailer/class.phpmailer.php");
$email = new PHPMailer();
$data_envio 	= date('d/m/Y');
$hora_envio 	= date('H:i:s');
// INÍCIO FÉRIAS
if(isset($_POST['tipo']) && $_POST['tipo']  === "ferias"){
    try{
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
        $att = $stmt->execute();
        if($Voto === "Validada" or $Voto === "Recusada (Preenchimento Incorreto)"){
            try{
                $sql = "UPDATE pendencias SET Resultado = ? WHERE idPendencias = ?;";
                $stmt = $conexao->prepare($sql);
                $stmt->bindParam(1, $Voto);
                $stmt->bindParam(2, $Pendencia);
                $stmt->execute();
                echo "<script>alert('Atualizado com Sucesso!');window.location.href='".BASE."formularios/ValidaPendencia.php?p=".$Pendencia."';</script>";exit;
            }catch(PDOException $e){
                echo "<script>alert('Falha ao atualizar! Erro: ".$e."');window.location.href='".BASE."formularios/Pendencias.php';</script>";exit;
            }
        }else{
            try{
                if($Voto === "Aprovada"){
                    $Voto = utf8_encode("Em Votação");
                }else{
                    $Voto = utf8_encode("Recusada");
                }

                $sql = "UPDATE pendencias SET Resultado = ? WHERE idPendencias = ?;";
                $stmt = $conexao->prepare($sql);
                $stmt->bindParam(1, $Voto);
                $stmt->bindParam(2, $Pendencia);
                $stmt->execute();

                $email->CharSet = 'UTF-8';
                $email->From		= 'contato@nutribemrefeicoescoletivas.com.br';
                $email->FromName	= 'RH-Online';
                $email->Subject		= 'RH-Online: Aviso de Férias';
                $email->IsHTML(true); // Define que o e-mail será enviado como HTML

                $v = Anti_Injection(utf8_encode($_POST['Voto']));
                try{
                    $sql = "SELECT f.*, (SELECT cad.CNPJ AS Empresa FROM empresa em INNER JOIN cadastro cad ON cad.idCadastro = em.Cadastro_idCadastro WHERE em.idEmpresa = u.Empresa_idEmpresa) AS CNPJ,(SELECT cad.Nome AS Empresa FROM empresa em INNER JOIN cadastro cad ON cad.idCadastro = em.Cadastro_idCadastro WHERE em.idEmpresa = u.Empresa_idEmpresa) AS Empresa, cdu.Nome AS Unidade, cc.Nome AS Colaborador, ct.dAdmissao AS Admissao, us.Email AS Email, us.Nome AS Imediato FROM ferias f INNER JOIN colaborador c ON c.CodColaborador = ? INNER JOIN cadastro cc ON cc.idCadastro = c.Cadastro_idCadastro INNER JOIN contratacao ct ON ct.idContratacao = c.Contratacao_idContratacao INNER JOIN unidade u ON u.idUnidade = ct.Unidade_idUnidade INNER JOIN cadastro cdu ON cdu.idCadastro = u.Cadastro_idCadastro INNER JOIN chefia ch ON ch.Colaborador_idColaborador = c.idColaborador INNER JOIN usuarios us ON us.idusuarios = ch.Usuario_idUsuario WHERE f.Colaborador_idColaborador = c.idColaborador ORDER BY f.idFerias DESC LIMIT 1"; 
                    $stm = $conexao->prepare($sql);
                    $stm->bindValue(1, $_POST['Cod']);
                    $stm->execute();
                    $row = $stm->fetch(PDO::FETCH_OBJ);
                }catch(PDOException $e){
                    echo $e;
                }
                if($v == "Aprovada"){
                    $msg =  "
                        <html>
                            <head>
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
                            </head>
                            <body>
                                <h2>O seguinte pedido de férias foi aprovado!</h2>
                                <h3><strong>Empregador: </strong>". utf8_decode($row->Empresa) ."</h3>
                                <h3><strong>CNPJ nº: </strong>". CNPJ_Padrao($row->CNPJ) ."</h3>
                                <p><strong>Unidade: </strong>". utf8_decode($row->Unidade) ."</p>
                                <p><strong>Funcionário: </strong>". utf8_decode($row->Colaborador) .".</p>
                                <p>
                                    <strong>Registro: </strong> ".$_POST['Cod']."
                                    <strong>Data de Admissão: </strong>".date('d/m/Y' , strtotime($row->Admissao)).".
                                </p>
                                <p><strong>Período Aquisitivo de Férias: </strong>". date('d/m/Y' , strtotime($row->AquisitivoInicio)) ." à ". date('d/m/Y' , strtotime($row->AquisitivoFinal)) .".</p>
                                <p><strong>Período Gozo das Férias: </strong> ". date('d/m/Y' , strtotime($row->pGozoInicio)) ." à ". date('d/m/Y' , strtotime($row->pGozoFinal)).".</p>
                                <p><strong>Abono Pecuniário, sim ou não? </strong>". utf8_decode($row->Abono) ."</p>

                                <br >
                                <p>Aguardando validação do RH!</p>
                                <br >
                                <p>Este e-mail foi enviado em <b> $data_envio </b> às <b> $hora_envio </b></p>
                            </body>
                        </html>
                        ";
                    //Anexo
                    $email->AddAttachment( 'Ferias_REG.'.$_POST['Cod'].'.pdf' );
                }else{
                    $msg =  "
                        <html>
                            <head>
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
                            </head>
                            <body>
                                <h1>O seguinte pedido de férias foi reprovado!</h1>
                                <h3><strong>Empregador: </strong>". utf8_decode($row->Empresa) ."</h3>
                                <h3><strong>CNPJ nº: </strong>". CNPJ_Padrao($row->CNPJ) ."</h3>
                                <p><strong>Unidade: </strong>". utf8_decode($row->Unidade) ."</p>
                                <p><strong>Funcionário: </strong>". utf8_decode($row->Colaborador) .".</p>
                                <p>
                                    <strong>Registro: </strong> ".$_POST['Cod']."
                                    <strong>Data de Admissão: </strong>".date('d/m/Y' , strtotime($row->Admissao)).".
                                </p>
                                <p><strong>Período Aquisitivo de Férias: </strong>". date('d/m/Y' , strtotime($row->AquisitivoInicio)) ." à ". date('d/m/Y' , strtotime($row->AquisitivoFinal)) .".</p>
                                <p><strong>Período Gozo das Férias: </strong> ". date('d/m/Y' , strtotime($row->pGozoInicio)) ." à ". date('d/m/Y' , strtotime($row->pGozoFinal)).".</p>
                                <p><strong>Abono Pecuniário, sim ou não? </strong>". utf8_decode($row->Abono) ."</p>

                                <p>Este e-mail foi enviado em <b> $data_envio </b> às <b> $hora_envio </b></p>
                            </body>
                        </html>
                        ";
                }
                $email->Body = $msg;
                //$email->AddAddress( 'cleitonteixeira@secservices.com.br' , 'Cleiton' );
                $email->AddAddress( $row->Email , utf8_decode($row->Imediato) );
                //$email->AddCC('virgiliofaria@nutribemrefeicoescoletivas.com.br', 'Virgilio Faria'); // Copia
                $email->AddCC('rh@nutribemrefeicoescoletivas.com.br', 'RH Nutribem'); // Copia
                //$email->AddCC('anderson@nutribemrefeicoescoletivas.com.br', 'Anderson R. M. Dengo'); // Copia
                //$email->AddCC('estagiario.dp@nutribemrefeicoescoletivas.com.br', 'Estagiario(a) DP'); // Copia
                //$email->AddCC('estagiario.rh@nutribemrefeicoescoletivas.com.br', 'Estagiario(a) RH');
                $email->AddCC('rh02@nutribemrefeicoescoletivas.com.br', 'RH Nutribem'); // Copia
                $email->AddCC('patricia.lopes@nutribemrefeicoescoletivas.com.br', 'Patricia Lopes - RH'); // Copia

                $enviado = $email->Send();
                // Limpa os destinatários e os anexos
                $email->ClearAllRecipients();
                $email->ClearAttachments();
                if ($enviado) {
                    unlink('Ferias_REG.'.$_POST['Cod'].'.pdf');
                    echo "<script>alert('Voto computado com sucesso! E-mail enviado com sucesso!');window.location.href='".BASE."formularios/ValidaPendencia.php?p=".$Pendencia."';</script>";exit;
                } else {
                    unlink('Ferias_REG.'.$_POST['Cod'].'.pdf');
                    echo "<script>alert('Voto computado com sucesso! Falha ao enviar e-mail!');window.location.href='".BASE."formularios/ValidaPendencia.php?p=".$Pendencia."';</script>";exit;
                }
               // echo $msg;
            }catch(PDOException $e){
                echo "<script>alert('Falha ao atualizar! Erro: ".$e."');window.location.href='".BASE."formularios/Pendencias.php';</script>";exit;
            }
        }
    }catch(PDOException $e){
        echo "<script>alert('Falha ao atualizar! Erro: ".$e."');window.location.href='".BASE."formularios/Pendencias.php';</script>";exit;
    }
}else{
    header("Location: http://www.nutribemrefeicoescoletivas.com.br/intranet/rh-online/");
}
?>