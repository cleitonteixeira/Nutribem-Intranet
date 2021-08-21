<?php
if(!isset($_SESSION)) session_start();
require_once("../arquivo/funcao/Outras.php");
require_once("../arquivo/funcao/Dados.php");
require_once("../arquivo/header/Header.php");
require_once("conexao.php");
$Trocas = array(' ','-','.',')','(','/');
$conexao = conexao::getInstance();
if(isset($_POST['Cliente']) && $_POST['Cliente']  ==  "Atualiza"){
    $conexao->beginTransaction();
    try{
        //ENDEREÇO DE COBRANÇA
        $idCobranca = $_POST['idCobranca'];
        $EnderecoC  = $_POST['logradouroCobranca'];
        $BairroC    = $_POST['bairroCobranca'];
        $CEPC       = str_replace($Trocas,"",$_POST['cepCobranca']);
        $UFC        = $_POST['ufCobranca'];
        $CidadeC    = $_POST['cidadeCobranca'];
        $NumeroC    = $_POST['numeroCobranca'];
        $SQL = "UPDATE ecobranca SET Endereco = ?, Bairro = ?, CEP = ?, Cidade =?, Numero = ?, UF = ? WHERE idECobranca = ?";
        $stmt = $conexao->prepare($SQL);
        $stmt->bindParam(1, $EnderecoC);
        $stmt->bindParam(2, $BairroC);
        $stmt->bindParam(3, $CEPC);
        $stmt->bindParam(4, $CidadeC);
        $stmt->bindParam(5, $NumeroC);
        $stmt->bindParam(6, $UFC);
        $stmt->bindParam(7, $idCobranca);
        $stmt->execute();
        //FIM COBRANÇA
        //ENDEREÇO
        $idEndereco = $_POST['idEndereco'];
        $Endereco   = utf8_decode($_POST['logradouro']);
        $Bairro     = utf8_decode($_POST['bairro']);
        $CEP        = str_replace($Trocas,"",$_POST['cep']);
        $UF         = $_POST['uf'];
        $Cidade     = utf8_decode($_POST['cidade']);
        $Numero     = $_POST['numero'];
        $SQL = "UPDATE endereco SET Endereco = ?, Bairro = ?, CEP = ?, Cidade =?, Numero = ?, UF = ? WHERE idEndereco = ?";
        $stmt = $conexao->prepare($SQL);
        $stmt->bindParam(1, $Endereco);
        $stmt->bindParam(2, $Bairro);
        $stmt->bindParam(3, $CEP);
        $stmt->bindParam(4, $Cidade);
        $stmt->bindParam(5, $Numero);
        $stmt->bindParam(6, $UF);
        $stmt->bindParam(7, $idEndereco);
        $stmt->execute();
        //FIM ENDEREÇO
        //DADOS DE CONTATO COMERCIAL
        $idComercial          = utf8_decode($_POST['idComercial']);
        $ResponsavelComercial = utf8_decode($_POST['ResponsavelComercial']);
        $EmailComercial       = utf8_decode($_POST['EmailComercial']);
        $TelefoneComercial    = str_replace($Trocas,"",$_POST['TelefoneComercial']);
        $SQL = "UPDATE ccontratante SET Responsavel = ?, Email = ?, Telefone = ? WHERE idCContratante = ?";
        $stmt = $conexao->prepare($SQL);
        $stmt->bindParam(1, $ResponsavelComercial);
        $stmt->bindParam(2, $EmailComercial);
        $stmt->bindParam(3, $TelefoneComercial);
        $stmt->bindParam(4, $idComercial);
        $stmt->execute();
        //FIM CONTATO COMERCIAL
        //DADOS DE CONTATO FINANCEIRO
        $idFinanceiro          = utf8_decode($_POST['idFinanceiro']);
        $ResponsavelFinanceiro = utf8_decode($_POST['ResponsavelFinanceiro']);
        $EmailFinanceiro       = utf8_decode($_POST['EmailFinanceiro']);
        $TelefoneFinanceiro    = str_replace($Trocas,"",$_POST['TelefoneFinanceiro']);
        $SQL = "UPDATE ccontratante SET Responsavel = ?, Email = ?, Telefone = ? WHERE idCContratante = ?";
        $stmt = $conexao->prepare($SQL);
        $stmt->bindParam(1, $ResponsavelFinanceiro);
        $stmt->bindParam(2, $EmailFinanceiro);
        $stmt->bindParam(3, $TelefoneFinanceiro);
        $stmt->bindParam(4, $idFinanceiro);
        $stmt->execute();
        //FIM CONTATO FINANCEIRO
        //DADOS DE CADASTRO
        $idCadastro = utf8_decode($_POST['idCadastro']);
        $Nome       = utf8_decode($_POST['razao']);
        $CNPJ       = str_replace($Trocas,"",$_POST['cnpj']);
        $SQL = "UPDATE cadastro SET Nome = ?, CNPJ = ? WHERE idCadastro = ?";
        $stmt = $conexao->prepare($SQL);
        $stmt->bindParam(1, $Nome);
        $stmt->bindParam(2, $CNPJ);
        $stmt->bindParam(3, $idCadastro);
        $stmt->execute();
        //FIM CADASTRO
        //DADOS DE CLIENTE
        $idContratante = utf8_decode($_POST['idContratante']);
        $ie            = utf8_decode($_POST['ie']);
        $SQL = "UPDATE contratante SET IE = ? WHERE idContratante = ?";
        $stmt = $conexao->prepare($SQL);
        $stmt->bindParam(1, $ie);
        $stmt->bindParam(2, $idContratante);
        $stmt->execute();
        //FIM CLIENTE
        $conexao->commit();
        echo '
        <div class="alert alert-success">
            <p><strong>Sucesso!</strong> Cadastro Atualizado.</p>
            <p>Você será redirecionado em alguns segundos. Caso não seja clique <a href="'.BASE.'clientes/Editar.php?cod='.$_POST['idContratante'].'">aqui</a>.</p>
        </div>
        ';
        header('Refresh: 5;URL='.BASE.'clientes/Editar.php?cod='.$_POST['idContratante']);exit;
    }catch(PDOException $erro_cad){
        $conexao->rollBack();
        echo '
        <div class="alert alert-danger">
            <p><strong>Falha!</strong> Falha na Atualização do Cadastro.</p>
            <p><strong>'.$erro_cad.' </strong></p>
            <p>Você será redirecionado em alguns segundos. Caso não seja clique <a href="'.BASE.'clientes/Editar.php?cod='.$_POST['idContratante'].'">aqui</a>.</p>
        </div>
        ';
        header('Refresh: 5;URL='.BASE.'clientes/Editar.php?cod='.$_POST['idContratante']);exit;
    }
}else{
    header("Location: http://www.nutribemrefeicoescoletivas.com.br/intranet/");exit;
}
?>