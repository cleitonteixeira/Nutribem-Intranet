<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])):
    session_destroy();
    require_once("../control/arquivo/funcao/Outras.php");
    header("Location: ".BASE);
else:
    require_once("../control/Pacote.php");
    $conexao = conexao::getInstance();
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 conteudo">
            <div class="col-xs-12 text-center"><h1><u>Formulários</u></h1></div>
            <div class="col-xs-4 text-center">
                <div class="form-group">
                    <blockquote>
                        <p>Formulário para Admissão</p>
                    </blockquote>
                    <a href="Arq/Formulario de Requisicao de Pessoal.pdf" download><button name="Files" class="btn btn-primary"><i class="fa fa-download" aria-hidden="true"></i> Baixar</button></a>
                </div>
            </div>
            <div class="col-xs-4 text-center">
                <blockquote>
                    <p>Ficha Funcional</p>
                </blockquote>
                <a href="Arq/Ficha de Cadastro Funcional.pdf" download><button name="Files" type="submit" class="btn btn-primary"><i class="fa fa-download" aria-hidden="true"></i> Baixar</button></a>
            </div>
            <div class="col-xs-4 text-center">
                <blockquote>
                    <p>CheckList para Novos Funcionários</p>
                </blockquote>
                <a href="Arq/CheckList.pdf" download><button name="Files" type="submit" class="btn btn-primary"><i class="fa fa-download" aria-hidden="true"></i> Baixar</button></a>
            </div>
            <div class="col-xs-12 divisa"></div>
            <div class="col-xs-6 text-center">
                <blockquote>
                    <p>Cartão de Ponto</p>
                </blockquote>
                <a href="Arq/Cartao_Ponto.doc" download><button name="Files" type="submit" class="btn btn-primary"><i class="fa fa-download" aria-hidden="true"></i> Baixar</button></a>
            </div>
            <div class="col-xs-6 text-center">
                <blockquote>
                    <p>Orientações de Ponto</p>
                </blockquote>
                <a href="Arq/Orientacoes_Ponto.pdf" download><button name="Files" type="submit" class="btn btn-primary"><i class="fa fa-download" aria-hidden="true"></i> Baixar</button></a>
            </div>
        </div>
    </div>
</div>
<?php
  endif;
require_once("../control/arquivo/footer/Footer.php");
?>