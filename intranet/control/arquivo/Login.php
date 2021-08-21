<div class="col-lg-12 col-xs-12 col-md-12 col-sm-12 login">
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
</div>