INSERT INTO `usuario`(
    `id`, 
    `nome`, 
    `email`, 
    `senha`, 
    `ativo`, 
    `dataCadastro`, 
    `role`, 
    `id_empresa`, 
    `id_departamento`, 
    `telefone`, 
    `ramal`
) VALUES (
    null,
    'Administrador',
    'admin@vicentinos.com.br',
    sha1('admin'),
    1,
    null,
    'admin',
    null,
    null,
    null,
    null
)
