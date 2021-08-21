<?php
require_once("../control/classes/phpMailer/class.phpmailer.php");
require_once("../control/banco/conexao.php");
require_once("../control/arquivo/funcao/Outras.php");
require_once("../control/arquivo/funcao/Dados.php");
$conexao    = conexao::getInstance();
$sql = "SELECT ct.idContrato, cd.Nome AS Cliente, cdu.Nome AS Unidade,ct.nContrato AS Contrato, ct.cCusto, ct.pCompra, ct.DataReajuste FROM contrato ct INNER JOIN contratante co ON co.idContratante = ct.Contratante_idContratante INNER JOIN cadastro cd ON cd.idCadastro = co.Cadastro_idCadastro INNER JOIN unidadefaturamento un ON un.idUnidadeFaturamento = ct.Unidade_idUnidade INNER JOIN cadastro cdu ON cdu.idCadastro = un.Cadastro_idCadastro WHERE ct.Finalizado = 'N' ORDER BY ct.DataReajuste;";
$stm = $conexao->prepare($sql);
$stm->execute();
$data_envio 	= date('d/m/Y');
$hora_envio 	= date('H:i:s');
ob_start();
?>
<html>
    <head>
    </head>
    <body>
        <style>
            table {
                border-spacing: 0;
                border-collapse: collapse;
            }
            .table td,
            .table > tbody + tbody {
                border-top: 2px solid #000;
            }
            .table-bordered {
                border: 2px solid #000 !important;
            }
            .table-bordered > thead > tr > th,
            .table-bordered > tbody > tr > th,
            .table-bordered > tfoot > tr > th,
            .table-bordered > thead > tr > td,
            .table-bordered > tbody > tr > td,
            .table-bordered > tfoot > tr > td {
                border: 1px solid #ddd;
            }
            .table-bordered > thead > tr > th,
            .table-bordered > thead > tr > td {
                border-width: 2px;
            }
            th {
                border: 2px solid #000 !important;
                background-color: #8DB5BD !important;
                text-align: center !important;
                vertical-align: middle !important;
                padding-top: 8px;
            }
            td {
                border: 1px solid #000 !important;
                text-align: center !important;
                vertical-align: middle !important;
                padding-top: 8px;
            }
            .table {
                width: 100%;
                max-width: 100%;
                margin-bottom: 20px;
            }
            .table-responsive {
                min-height: .01%;
                overflow-x: auto;
            }
            thead{
                border: 2px solid #000 !important;
                background-color: #8DB5BD !important;
            }
            .table > thead > tr > th {
                vertical-align: bottom;
                border-bottom: 2px solid #ddd;
            }
            .fd90{
                background-color: greenyellow !important;
            }
            .fd60{
                background-color: yellow !important;
            }
            .fd01{
                background-color: red !important;
            }
            .fd90_t{
                color: greenyellow !important;
            }
            .fd60_t{
                color: yellow !important;
            }
            .fd01_t{
                color: red !important;
            }
            a{
                text-decoration: none;
            }
        </style>
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-12 col-md-12 col-lg-12">
                    <p>Próximos reajustes de contratos:</p>
                </div>
                <div class="col-xs-12 col-md-12 col-lg-12">
                    <table class="table table-bordered table-responsive">
                        <thead>
                            <tr>
                                <th>CLIENTE</th>
                                <th>UNIDADE</th>
                                <th>CONTRATO</th>
                                <th>C. CUSTO</th>
                                <th>P. COMPRA</th>
                                <th>DATA FINAL</th>
                                <th>VER</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if($stm->rowCount() > 0){
                                $rs = $stm->fetchAll(PDO::FETCH_OBJ);
                                foreach($rs as $r){
                                    $data 		= strtotime(date('Y-m-d', strtotime($r->DataReajuste)));
                                    $data1 		= strtotime(date('Y-m-d'));
                                    $diferenca = $data - $data1;
                                    $dias = (int)floor( $diferenca / (60 * 60 * 24));
                                    $contador = 0;
                                    if($dias <= 90){
                                        $contador++;
                                        if($dias > 60){
                            ?>
                            <tr class="fd90">
                                <?php
                                        }elseif($dias <= 60 && $dias >=1){
                                ?>
                            <tr class="fd60">
                                <?php
                                        }else{
                                ?>
                            <tr class="fd01">
                                <?php
                                        }
                                ?>
                                <td><?php echo utf8_decode($r->Cliente); ?></td>
                                <td><?php echo utf8_decode($r->Unidade); ?></td>
                                <td><?php echo utf8_decode($r->Contrato); ?></td>
                                <td><?php echo utf8_decode($r->cCusto); ?></td>
                                <td><?php echo utf8_decode($r->pCompra); ?></td>
                                <td><?php echo utf8_decode(date("d/m/Y", strtotime($r->DataReajuste))); ?></td>
                                <td><a class="btn" href="http://www.nutribemrefeicoescoletivas.com.br/intranet/gestorcontratos/pesquisas/DetalheContrato.php?id=<?php echo $r->idContrato;?>">DETALHE</a></td>
                            </tr>
                            <?php
                                    }
                                }
                            }
                            ?>
                            <?php
                            if($contador == 0){
                            ?>
                            <tr>
                                <td colspan="7">
                                    <p>NÃO EXISTEM CONTRATOS COM REAJUSTE PARA OS PROXIMOS 90 DIAS.</p>
                                </td>
                            </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                        <tfoot class="text-center">
                            <tr>
                                <td colspan="7">
                                    <strong>ESTE E-MAIL FOI ENVIADO NO DIA <?php echo date("d/m/Y \A\S H:i:s");?>.</strong>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="col-xs-12 col-md-12 col-lg-12">
                    <p><span class="fd90_t">█</span> De 90 a 61 dias para o reajuste.</p>
                    <p><span class="fd60_t">█</span> De 60 a 1 dia para o reajuste.</p>
                    <p><span class="fd01_t">█</span> Já se encontra estourado o prazo.</p>
                </div>
            </div>
        </div>
    </body>
</html>
<?php
//INICIO ENVIO DE E-MAIL
$MsgEmail = ob_get_clean();
$email = new PHPMailer();
$email->CharSet = 'UTF-8';
$email->From		= 'contato@nutribemrefeicoescoletivas.com.br';
$email->FromName	= 'Gestor de Contratos';
$email->Subject		= 'Gestor de Contratos: REAJUSTE DE CONTRATO';
$email->IsHTML(true); // Define que o e-mail será enviado como HTML
$email->Body		= $MsgEmail;

//$email->AddAddress( 'cleitonteixeirasantos@gmail.com' , 'Cleiton Teixeira dos Santos' );
//$email->AddAddress( 'super.adm@nutribemrefeicoescoletivas.com.br', 'Carlos Magno'); // Copia
$email->AddAddress('supervisao.faturamento@nutribemrefeicoescoletivas.com.br', 'Ney Nunes'); // Copia
//$email->AddCC('virgiliofaria@nutribemrefeicoescoletivas.com.br', 'Virgilio Faria'); // Copia
//$email->AddCC('anderson@nutribemrefeicoescoletivas.com.br', 'Anderson R. M. Dengo'); // Copia
//$email->AddCC('faturamento1@nutribemrefeicoescoletivas.com.br', 'Faturamento'); // Copia
//$email->AddCC('faturamento2@nutribemrefeicoescoletivas.com.br', 'Faturamento'); // Copia

$enviado = $email->Send();
// Limpa os destinatários e os anexos
$email->ClearAllRecipients();
$email->ClearAttachments();
// Exibe uma mensagem de resultado
//FIM ENVIO DE E-MAIL
?>