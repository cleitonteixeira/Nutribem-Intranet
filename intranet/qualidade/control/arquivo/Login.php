<div class="col-lg-12 login">
    <form enctype="multipart/form-data" action="<?php echo BASE; ?>control/acesso/Validacao.php" method="post" role="form"  class="form-horizontal" id="login" name="login" data-toggle="validator">
        
        <div class="col-xs-6">
            <div class="form-group">
                <div class="col-sm-2">
                    <label class="control-label" for="login">Login:</label>
                </div>
                <div class="col-sm-8">
                    <input type="text" id="login" name="login" required class="form-control" autofocus />
                </div>
                <div class="col-sm-2"></div>
            </div>
            <div class="form-group">
                <div class="col-sm-2">
                    <label class="control-label" for="senha">Senha:</label>
                </div>
                <div class="col-sm-8">
                    <input type="password" id="senha" name="senha" required class="form-control" />
                </div>
                <div class="col-sm-2"></div>
            </div>
            <div class="form-group">
                <div class="col-sm-12">
                    <button type="submit" id="btn_enviar" class="btn btn-primary btn-sm">Entrar</button>
                    <button type="reset" class="btn btn-danger btn-sm">Cancelar</button>
                </div>
            </div>
            <div class="form-group">
                <span id="aviso"></span>
            </div>
        </div>
        <div class="col-xs-4">    
            <img src="<?php echo BASE; ?>img/Login.png" class="img-rounded image-login img-responsive" />
        </div>
    </form>
	<div class="row">
		<div class="col-xs-12">
			<span title="Notas de Atualização">Notas de Atualização
				<a data-toggle="modal" data-target="#myModal">
					 <i class="fa fa-plus-square" aria-hidden="true"></i>
				</a>
			</span>
		</div>
	</div>
</div>
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Notas de Atualização</h4>
            </div>
            <div class="modal-body">
				<h2>Atualização do dia 18/09/2017</h2>
				<ul class="list-group">
					<li class="list-group-item">A partir de 18/09/2017, todos os usuários deverão atualizar a senha no primeiro acesso ao sistema.</li>
					<li class="list-group-item">Adicionado opção para alteração de senha.</li>
				</ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>