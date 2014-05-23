function DepartamentosController($scope, $http, $window) {

    /**
     * Metododo que retorna a lista dew departamentos de uma empresa
     * @param id_empresa O identificador da empresa em questão
     */
    $scope.getDepartamentos = function(id_empresa) {
        $http.get("/empresas/lista-departamentos/empresa/" + id_empresa).success(function(data) {
            $scope.departamentos = data.departamentos;
        }).error(function(status) {
            $scope.status = status;
        });
    };

    $scope.salvar = function(f, id_empresa) {
        var form = $('#' + f);
        var dados = form.serialize();
        console.log(dados);
        $.ajax({
            type: 'post',
            url: '/empresas/salva-departamento',
            data: dados
        }).success(function(data) {
            $scope.getDepartamentos(id_empresa);
            reset();
        });
    };

    $scope.excluir = function(departamento, id_empresa) {
        var confirm = $window.confirm('Tem certeza que deseja excluir o departamento ' + departamento.nome + '?');
        if (confirm) {
            $http.get('/empresas/remove-departamento/id/' + departamento.id).success(function(data) {
                $scope.getDepartamentos(id_empresa);
            });
        }
    };

    $scope.editar = function(departamento) {
        $scope.departamento = departamento;
        console.log($scope.departamento.id);
    };

    var reset = function() {
        $scope.departamento = {id: 0, nome: '', ativo: ''};
    };


}

function ProcessosController($scope, $http, $window) {
    
    $scope.getItens = function(id_processo) {
        $http.get("/processos/lista-itens/processo/" + id_processo).success(function(data) {
            $scope.itens = data.itens;
        }).error(function(status) {
            $scope.status = status;
        });
    };

    $scope.salvar = function(f, id_processo) {
        var form = $('#' + f);
        var dados = form.serialize();
        $.ajax({
            type: 'post',
            url: '/processos/salva-item',
            data: dados
        }).success(function(data) {
            $scope.getItens(id_processo);
            reset();
        });
    };

    $scope.excluirItem = function(item, id_processo) {
        var confirmItem = $window.confirm('Tem certeza que deseja excluir o Item ' + item.nome + '?');
        if (confirmItem) {
            $http.get('/processos/remove-item/id/' + item.id).success(function(data) {
                $scope.getItens(id_processo);
            });
        }
    };

    $scope.editar = function(item) {
        $scope.item = item;
    };

    var reset = function() {
        $scope.item = {id: 0, nome: '', ativo: ''};
    };


}