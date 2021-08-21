<?php
require_once("../control/banco/conexao.php");
require_once("../control/arquivo/funcao/Dados.php");
$conexao = conexao::getInstance();
ob_start();
$sql = "SELECT ca.*, col.*, co.*, con.*, cad.Nome, cad.CPF,doc.*, en.*, (SELECT cad.Nome as Unidade FROM unidade un INNER JOIN cadastro cad ON cad.idCadastro = un.Cadastro_idCadastro WHERE un.idUnidade = con.Unidade_idUnidade) AS Unidade, (SELECT cad.Nome AS Empresa FROM empresa em INNER JOIN cadastro cad ON cad.idCadastro = em.Cadastro_idCadastro WHERE em.idEmpresa = un.Empresa_idEmpresa) AS Empresa, (SELECT cad.CNPJ AS Empresa FROM empresa em INNER  JOIN cadastro cad ON cad.idCadastro = em.Cadastro_idCadastro WHERE em.idEmpresa = un.Empresa_idEmpresa) AS CNPJ  FROM colaborador col INNER JOIN cadastro cad ON cad.idCadastro = col.Cadastro_idCadastro INNER JOIN endereco en ON en.idEndereco = col.Endereco_idEndereco INNER JOIN contato co ON co.idContato  = col.Contato_idContato INNER JOIN documento doc ON doc.idDocumento = col.Documento_idDocumento INNER JOIN contratacao con ON con.idContratacao = col.Contratacao_idContratacao INNER JOIN unidade un ON un.idUnidade = con.Unidade_idUnidade INNER JOIN cargo ca ON ca.idCargo = con.Cargo_idCargo WHERE col.idColaborador = ?;";
$stm = $conexao->prepare($sql);
$stm-> bindParam(1, $_GET['colaborador']);
$stm->execute();
$rs = $stm->fetch(PDO::FETCH_OBJ);
$sql = "SELECT en.* FROM empresa em INNER JOIN endereco en ON en.idEndereco = em.Endereco_idEndereco INNER JOIN unidade un ON un.idUnidade = ? WHERE em.idEmpresa = un.Empresa_idEmpresa";
$stm = $conexao->prepare($sql);
$stm-> bindParam(1, $rs->Unidade_idUnidade);
$stm->execute();
$rsEm = $stm->fetch(PDO::FETCH_OBJ);
$sql = "SELECT en.* FROM unidade un INNER JOIN endereco en ON en.idEndereco = un.Endereco_idEndereco WHERE un.idUnidade = ?";
$stm = $conexao->prepare($sql);
$stm-> bindParam(1, $rs->Unidade_idUnidade);
$stm->execute();
$rsUn = $stm->fetch(PDO::FETCH_OBJ);
$sql = "SELECT * FROM dependente WHERE Colaborador_idColaborador = ?;";
$stm = $conexao->prepare($sql);
$stm-> bindParam(1, $_GET['colaborador']);
$stm->execute();
if($stm->rowCount() == 0):
    $Dependente = "";
else:
    $Dependente = $stm->fetchAll(PDO::FETCH_OBJ);
    $contador = $stm->rowCount();
endif;
?>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title></title>
        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <link rel="shortcut icon" href="../img/Icone.png" type="image/x-icon" />
        <!-- Place favicon.ico in the root directory -->


        <!-- Fim Arquivos JS -->
        <!-- Início Arquivos CSS -->
        <link rel="stylesheet" href="../css/pdf.css">
    </head>
    <body>
        <div class="">
            <div class="ficha">
                <div class="col-xs-12 "><h3 class="text-center t"><dt>REGISTRO DE EMPREGADO</dt></h3></div>
                <div class="foto">
                </div>
                <div class="col-xs-12 e b ba">
                    <p><strong>Empregado:</strong> <?php echo utf8_decode($rs->Nome); ?></p>
                    <p><strong>Residência:</strong> <?php  echo  utf8_decode($rs->Endereco) .", Nº: ". $rs->Numero .", ". utf8_decode($rs->Bairro) .", ". utf8_decode($rs->Cidade) ."-". $rs->UF.' - CEP: '.CEP_Padrao($rs->CEP); ?></p>
                
                    <p>
                        <span><strong>Empregador:</strong> <?php echo utf8_decode(strtoupper($rs->Empresa));?></span>
                    </p>
                    <p>
                        <span><strong >CNPJ:</strong> <?php  echo  CNPJ_Padrao($rs->CNPJ); ?></span>
                    </p>
                    <p>
                        <span><strong>Endereço:</strong> <?php  echo  utf8_decode($rsEm->Endereco) .", nº: ". $rsEm->Numero .", ". utf8_decode($rsEm->Bairro) .", ". utf8_decode($rsEm->Cidade) ."-". $rsEm->UF.' - CEP: '.CEP_Padrao($rsEm->CEP); ?></span>
                    </p>
                </div>
                
                <div class="col-xs-12 e b">
                    <h5><strong>Filiação</strong></h5>
                    <p><strong>Mãe: </strong><?php echo utf8_decode($rs->nMae); ?></p>
                    <p><strong>Pai: </strong><?php echo utf8_decode($rs->nPai); ?></p>
                </div>

                <div class="col-xs-12 e b">
                    <h5><strong>Dados Pessoais</strong></h5>
                    <p>
                        <strong>Naturalidade:</strong> <?php echo utf8_decode($rs->Naturalidade); ?>
                        &thinsp;&thinsp;&thinsp;&thinsp;&thinsp;
                        <strong>País da Nacionalidade:</strong> <?php echo utf8_decode($rs->Nacionalidade); ?>
                        &thinsp;&thinsp;&thinsp;&thinsp;&thinsp;
                        <strong>Data de Nascimento:</strong> <?php echo Muda_Data($rs->dNascimento); ?>
                    </p>
                    <p>
                        <strong>Cor:</strong> <?php echo utf8_decode($rs->Cor); ?>
                        &thinsp;&thinsp;&thinsp;&thinsp;&thinsp;
                        <strong>Sexo:</strong> <?php echo utf8_decode($rs->Sexo); ?>
                        &thinsp;&thinsp;&thinsp;&thinsp;&thinsp;
                        <strong>Estado Civil:</strong> <?php echo utf8_decode($rs->eCivil); ?>
                        &thinsp;&thinsp;&thinsp;&thinsp;&thinsp;
                        
                    </p>
                    <p>
                        <strong>Grau de Instrução:</strong> <?php echo utf8_decode($rs->Escolaridade); ?>
                        &thinsp;&thinsp;&thinsp;&thinsp;&thinsp;
                    </p>
                </div>
                <div class="col-xs-12 e b">
                    <h5><strong>Documentação</strong></h5>
                    <p>
                        <strong>RG:</strong> <?php echo utf8_decode($rs->RG); ?>
                        &thinsp;&thinsp;&thinsp;&thinsp;&thinsp;
                        <strong>Data de Emissão:</strong> <?php echo Muda_Data($rs->dEmissao); ?>
                        &thinsp;&thinsp;&thinsp;&thinsp;&thinsp;
                        <strong>Orgão/UF Emissor:</strong> <?php echo utf8_decode($rs->Emissor); ?>
                    </p>
                    <p>
                        <strong>Título Eleitoral:</strong> <?php echo utf8_decode($rs->Titulo); ?>
                        &thinsp;&thinsp;&thinsp;&thinsp;&thinsp;
                        <strong>Zona:</strong> <?php echo utf8_decode($rs->Zona); ?>
                        &thinsp;&thinsp;&thinsp;&thinsp;&thinsp;
                        <strong>Seção:</strong> <?php echo utf8_decode(str_pad($rs->Secao,4,0,STR_PAD_LEFT)); ?>
                    </p>
                    <p><strong>CPF:</strong> <?php echo CPF_Padrao(str_pad($rs->CPF,11,0, STR_PAD_LEFT)); ?></p>
                    <p>
                        <strong>CTPS:</strong> <?php echo utf8_decode($rs->CTPS);?>
                        &thinsp;&thinsp;&thinsp;&thinsp;&thinsp;
                        <strong>Série:</strong> <?php echo str_pad($rs->sCTPS,4,0, STR_PAD_LEFT);?>
                        &thinsp;&thinsp;&thinsp;&thinsp;&thinsp;
                        <strong>Expedição CTPS:</strong> <?php echo Muda_Data($rs->dCTPS);?>
                        &thinsp;&thinsp;&thinsp;&thinsp;&thinsp;
                        <strong>UF:</strong> <?php echo utf8_decode($rs->UFCTPS);?>
                    </p>
                    <p>
                        <?php if($rs->CNH != ""):?>
                        <strong>CNH:</strong> <?php echo utf8_decode($rs->CNH); ?>
                        &thinsp;&thinsp;&thinsp;&thinsp;&thinsp;
                        <strong>Categoria CNH:</strong> <?php echo utf8_decode($rs->CategoriaCNH); ?>
                        <?php else: ?>
                        <strong>CNH:</strong> ###########
                        &thinsp;&thinsp;&thinsp;&thinsp;&thinsp;
                        <strong>Categoria CNH:</strong> ##
                        <?php endif; ?>
                    </p>
                    <p>
                        <?php if($rs->docMilitar != ""):?>
                        <strong>Doc. Militar:</strong> <?php echo utf8_decode($rs->docMilitar); ?>
                        &thinsp;&thinsp;&thinsp;&thinsp;&thinsp;
                        <strong>Categoria:</strong> <?php echo utf8_decode($rs->mCategoria); ?>
                        <?php else: ?>
                        <strong>Doc. Militar:</strong> ###########
                        &thinsp;&thinsp;&thinsp;&thinsp;&thinsp;
                        <strong>Categoria:</strong> ##
                        <?php endif; ?>
                    </p>
                </div>
                <div class="col-xs-12 e b">
                    <h5><strong>Dados Contratação</strong></h5>
                    <p>
                        <strong>Cargo/CBO:</strong> <?php echo $rs->CodCargo."-".utf8_decode($rs->Cargo)." - ".$rs->CBO; ?>
                        &thinsp;&thinsp;&thinsp;&thinsp;&thinsp;
                        <strong>Função:</strong> <?php echo utf8_decode($rs->Funcao); ?>
                        &thinsp;&thinsp;&thinsp;&thinsp;&thinsp;
                    </p>
                    <p>
                        <strong>Salário:</strong> <?php echo "R$ ".number_format($rs->Salario,2,',','.'); ?>
                        &thinsp;&thinsp;&thinsp;&thinsp;&thinsp;
                    </p>
                    <p>
                        <strong>Admissão:</strong> <?php echo Muda_Data($rs->dAdmissao); ?>
                        &thinsp;&thinsp;&thinsp;&thinsp;&thinsp;
                        <strong>PIS:</strong> <?php echo PIS_Padrao(str_pad($rs->PIS,11,0, STR_PAD_LEFT)); ?>
                        &thinsp;&thinsp;&thinsp;&thinsp;&thinsp;
                    </p>
                </div>
                <div class="col-xs-12 e b">
                    <h5><strong>Contato</strong></h5>
                    <p>
                        <?php if($rs->Celular != "" && $rs->Celular != 0):?>
                        <strong>Celular:</strong> <?php echo Cel_Padrao($rs->Celular); ?>
                        &thinsp;&thinsp;&thinsp;&thinsp;&thinsp;
                        <?php else: ?>
                        <strong>Celular:</strong> (##) #####-####
                        &thinsp;&thinsp;&thinsp;&thinsp;&thinsp;
                        <?php endif; ?>
                        <?php if($rs->Telefone != "" && $rs->Telefone != 0):?>
                        <strong>Telefone:</strong> <?php echo Tel_Padrao($rs->Telefone); ?>
                        &thinsp;&thinsp;&thinsp;&thinsp;&thinsp;
                        <?php else: ?>
                        <strong>Telefone:</strong> (##) ####-####
                        &thinsp;&thinsp;&thinsp;&thinsp;&thinsp;
                        <?php endif; ?>
                    </p>
                    <p>
                        <?php if($rs->email != ""):?>
                        <strong>E-mail:</strong> <?php echo $rs->email; ?>
                        &thinsp;&thinsp;&thinsp;&thinsp;&thinsp;
                        <?php else: ?>
                        <strong>E-mail:</strong> * Nenhum Registro *
                        &thinsp;&thinsp;&thinsp;&thinsp;&thinsp;
                        <?php endif; ?>
                    </p>
                </div>
                <div class="col-xs-12 e b">
                    <h5><strong>Dependentes</strong></h5>
                    <?php if ($Dependente == ""):?>
                    <p class="text-center">
                        Nenhum Registro Encontrado.
                    </p>
                    <?php else:
                        foreach($Dependente as $Valor):
                    ?>
                    <p>
                        <strong>Nome:</strong> <?php echo utf8_decode($Valor->dNome); ?>
                        &thinsp;&thinsp;&thinsp;&thinsp;&thinsp;
                        <strong>Data de Nascimento:</strong> <?php echo Muda_Data($Valor->dNascimento); ?>
                        &thinsp;&thinsp;&thinsp;&thinsp;&thinsp;
                        <strong>Parentesco:</strong> <?php echo utf8_decode($Valor->Parentesco) ?>
                        &thinsp;&thinsp;&thinsp;&thinsp;&thinsp;
                    </p>
                    <p>
                       <?php if($Valor->dCPF != "" && $Valor->dCPF != 0):?>
                        <strong>CPF:</strong> <?php echo CPF_Padrao(str_pad($Valor->dCPF,11,0, STR_PAD_LEFT)); ?>
                        &thinsp;&thinsp;&thinsp;&thinsp;&thinsp;
                        <?php else: ?>
                        <strong>CPF:</strong> ###########
                        &thinsp;&thinsp;&thinsp;&thinsp;&thinsp;
                        <?php endif; ?>
                        <strong>Mãe:</strong> <?php echo utf8_decode($Valor->nMae); ?>
                        &thinsp;&thinsp;&thinsp;&thinsp;&thinsp;
                    </p>
                    <?php 
                        endforeach;
                    endif;
                    ?>
                </div>
                <div class="col-xs-12 e b text-center">
                    <p></p>
                    <p></p>
                    <p></p>
                    <p>________________________________________________</p>
                    <p> <?php echo utf8_decode($rs->Nome); ?></p>
                    <p></p>
                    <p></p>
                    <p></p>
                    <p>________________________________________________</p>
                    <p> Empregador</p>
                </div>

            </div>
        </div>
    </body>
</html>
<?php
$html = ob_get_clean();
//$html = utf8_encode($html);
define('MPDF_PATH', '../control/classes/mpdf60/');
include(MPDF_PATH.'mpdf.php');
$mpdf = new mPDF('utf-8','A4-P');
$mpdf->allow_charset_conversion=true;
$mpdf->charset_in='utf-8';
//$mpdf->SetHeader('Ficha Funcionário||{PAGENO}');
$mpdf->SetAuthor('RH-Online');

// carrega uma folha de estilo – MAGICA!!!
$stylesheet = file_get_contents('../css/pdf.css');

// incorpora a folha de estilo ao PDF
// O parâmetro 1 diz que este é um css/style e deverá ser interpretado como tal
$mpdf->WriteHTML($stylesheet,1);
//Algumas configurações do PDF
$mpdf->SetDisplayMode('fullpage');
// modo de visualização
//$mpdf->SetFooter('{DATE j/m/Y H:i}|{PAGENO}/{nb}|RH Manager');
//bacana este rodape, nao eh mesmo?      

$arquivo = 'COL_'.utf8_decode($rs->Nome).'_'.date("y-m-d_h.i.s").'.pdf';
$mpdf->WriteHTML($html,2);
$mpdf->Output($arquivo, 'D');
exit();
?>