<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])){
    session_destroy();
    header("Location: http://www.nutribemrefeicoescoletivas.com.br/intranet/");
}else{
    require_once("../control/Pacote.php");
    $conexao = conexao::getInstance();
    $Troca = array("/","\\","|");
    $sql = "SELECT  c.idContratante, cd.Nome AS Cliente, cd.CNPJ AS CNPJ FROM contratante c INNER JOIN cadastro cd ON cd.idCadastro = c.Cadastro_idCadastro;";
    $stm = $conexao->prepare($sql);
    $stm->execute();
    $rs = $stm->fetchAll(PDO::FETCH_OBJ);
    $sql = "SELECT e.idEmpresa, cad.Nome AS Empresa, cad.CNPJ AS CNPJ FROM empresa e INNER JOIN cadastro cad ON cad.idCadastro =  e.Cadastro_idCadastro";
    $stm = $conexao->prepare($sql);
    $stm->execute();
    $rsEm = $stm->fetchAll(PDO::FETCH_OBJ);
?>
<script>
$(document).ready(function(){
    $('.desativar').click(function(e){
        e.preventDefault();
    });
    $("#pesquisa").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#alteracoes tr").filter(function() {
          $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
});
</script>

<!-- Content -->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 col-xs-12 col-lg-12 conteudo">
            <h1 class="text-center">HISTÓRICO DE MODIFICAÇÃO DE LANÇAMENTO</h1>
            <div class="text-center">
                <div class="">


                    <?php   
                    /* Constantes de configuração */  
                    define('QTDE_REGISTROS', 50);   
                    define('RANGE_PAGINAS', 1);   

                    /* Recebe o número da página via parâmetro na URL */  
                    $pagina_atual = (isset($_GET['page']) && is_numeric($_GET['page'])) ? $_GET['page'] : 1;   

                    /* Calcula a linha inicial da consulta */  
                    $linha_inicial = ($pagina_atual -1) * QTDE_REGISTROS;  
                    /* Instrução de consulta para paginação com MySQL */  
                    $sql = "SELECT h.idHistoricoLancamento, cdts.Nome AS Cliente, ct.cCusto AS cCusto, u.Nome AS Modifica, us.Nome AS Responsavel, cdt.Nome AS Unidade, ct.nContrato, h.dLancamento, h.dAlteracao, h.Descricao, h.Justificativa FROM historicolancamento h INNER JOIN usuarios u ON u.idusuarios = h.Usuario_idUsuario INNER JOIN contrato ct ON ct.idContrato = h.Contrato_idContrato INNER JOIN contratante ctt ON ctt.idContratante = ct.Contratante_idContratante INNER JOIN cadastro cdts ON cdts.idCadastro = ctt.Cadastro_idCadastro INNER JOIN usuarios us ON us.idusuarios = (SELECT DISTINCT(Usuario_idUsuario) FROM lancamento WHERE idLancamento = h.NumLancamento) INNER JOIN unidadefaturamento ud ON ud.idUnidadeFaturamento = (SELECT Unidade_idUnidade FROM lancamento WHERE dLancamento = h.dLancamento AND Contrato_idContrato = h.Contrato_idContrato LIMIT 1) INNER JOIN cadastro cdt ON cdt.idCadastro = ud.Cadastro_idCadastro WHERE ud.idUnidadeFaturamento IN (SELECT Unidade_idUnidade FROM unidadefuser WHERE Usuario_idUsuario = ?) LIMIT {$linha_inicial}, " . QTDE_REGISTROS;  
                    $stm = $conexao->prepare($sql);   
                    $stm->bindParam(1, $_SESSION['idusuarios']);
                    $stm->execute();   
                    $dados = $stm->fetchAll(PDO::FETCH_OBJ);   

                    /* Conta quantos registos existem na tabela */  
                    $sqlContador = "SELECT COUNT(*) AS total_registros FROM historicolancamento";   
                    $stm = $conexao->prepare($sqlContador);   
                    $stm->execute();   
                    $valor = $stm->fetch(PDO::FETCH_OBJ);   

                    /* Idêntifica a primeira página */  
                    $primeira_pagina = 1;   

                    /* Cálcula qual será a última página */  
                    $ultima_pagina  = ceil($valor->total_registros / QTDE_REGISTROS);   

                    /* Cálcula qual será a página anterior em relação a página atual em exibição */   
                    $pagina_anterior = ($pagina_atual > 1) ? $pagina_atual -1 : 0 ;   

                    /* Cálcula qual será a pŕoxima página em relação a página atual em exibição */   
                    $proxima_pagina = ($pagina_atual < $ultima_pagina) ? $pagina_atual +1 : 0 ;  

                    /* Cálcula qual será a página inicial do nosso range */    
                    $range_inicial  = (($pagina_atual - RANGE_PAGINAS) >= 1) ? $pagina_atual - RANGE_PAGINAS : 1 ;   

                    /* Cálcula qual será a página final do nosso range */    
                    $range_final   = (($pagina_atual + RANGE_PAGINAS) <= $ultima_pagina ) ? $pagina_atual + RANGE_PAGINAS : $ultima_pagina ;   

                    /* Verifica se vai exibir o botão "Primeiro" e "Pŕoximo" */   
                    $exibir_botao_inicio = ($range_inicial < $pagina_atual) ? 'mostrar' : 'desativar'; 

                    /* Verifica se vai exibir o botão "Anterior" e "Último" */   
                    $exibir_botao_final = ($range_final > $pagina_atual) ? 'mostrar' : 'desativar';  
                    ?>   
                    <?php if (!empty($dados)): ?>  
                    <table class="table table-striped table-bordered table-responsive">    
                        <thead>
                            <tr>
                                <th colspan="6"><input class="form-control" id="pesquisa" type="text" placeholder="Search..."></th>
                            </tr>
                            <tr>    
                                <th>CLIENTE</th>
                                <th>UNIDADE</th>
                                <th>RESPONSÁVEL</th>
                                <th>LANÇADO POR</th>
                                <th>DATA</th>
                                <th>DETALHES</th>
                            </tr>    
                        </thead>
                        <tbody id="alteracoes">    
                            <?php foreach($dados as $r):?>   
                            <tr>    
                                <td class="text-left"><?=$r->Cliente.' - <strong>CC:</strong> '.$r->cCusto;?></td>
                                <td><?=$r->Unidade?></td>
                                <td><?=$r->Modifica?></td>
                                <td><?=$r->Responsavel?></td>
                                <td><?=date("d/m/Y H:i:s", strtotime($r->dAlteracao));?></td>   
                                <td><button data-toggle="modal" data-target="#view<?php echo $r->idHistoricoLancamento; ?>" class="btn btn-default"><i class="far fa-folder-open"></i></button></td>
                            </tr>
                            <div id="view<?php echo $r->idHistoricoLancamento; ?>" class="modal fade" role="dialog">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span></button>
                                            <h4 class="modal-title">DETALHES ALTERAÇÃO DE LANÇAMENTO</h4>
                                        </div>
                                        <div class="modal-body text-justify">
                                            <p><strong>Contrato: </strong><?=$r->nContrato?></p>
                                            <p><strong>Unidade: </strong><?=$r->Unidade?></p>
                                            <p><strong>Responsável Alteração: </strong><?=$r->Modifica?></p>
                                            <p><strong>Responsável Lançamento: </strong><?=$r->Responsavel?></p>
                                            <p><strong>Data Lançamento: </strong><?=date("d/m/Y", strtotime($r->dLancamento));?></p>
                                            <p><strong>Data/Hora Modificação: </strong><?=date("d/m/Y H:i:s", strtotime($r->dAlteracao));?></p>
                                            <p><strong>Descrição: </strong><?=utf8_decode($r->Descricao);?></p>
                                            <p><strong>Justificativa: </strong><?=utf8_decode($r->Justificativa);?></p>
                                        </div>
                                    </div><!-- /.modal-content -->
                                </div>
                            </div>
                            <?php endforeach; ?>   
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6"><strong>CC = Centro de Custo</strong></td>
                            </tr>
                        </tfoot>
                    </table>    
                    <div class='box-paginacao'>     
                        <a class='box-navegacao <?=$exibir_botao_inicio?>' href="Historico.php?page=<?=$primeira_pagina?>" title="Primeira Página">Primeira</a>    
                        <a class='box-navegacao <?=$exibir_botao_inicio?>' href="Historico.php?page=<?=$pagina_anterior?>" title="Página Anterior">Anterior</a>     

                        <?php  
                        /* Loop para montar a páginação central com os números */   
                        for ($i=$range_inicial; $i <= $range_final; $i++):   
                        $destaque = ($i == $pagina_atual) ? 'destaque' : '' ;  
                        ?>   
                        <a class='box-numero <?=$destaque?>' href="Historico.php?page=<?=$i?>"><?=$i?></a>    
                        <?php endfor; ?>    

                        <a class='box-navegacao <?=$exibir_botao_final?>' href="Historico.php?page=<?=$proxima_pagina?>" title="Próxima Página">Próxima</a>    
                        <a class='box-navegacao <?=$exibir_botao_final?>' href="Historico.php?page=<?=$ultima_pagina?>" title="Última Página">Último</a>    
                    </div>   
                    <?php else: ?>   
                    <p class="bg-danger">Nenhum registro foi encontrado!</p>  
                    <?php endif; ?>   
                </div>    
            </div>    






        </div>
    </div>
</div>
<?php
}
?>