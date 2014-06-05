create table if not exists anexo (
    id int not null auto_increment primary key,
    nome varchar(255) not null,
    caminho varchar(255) not null,
    id_historico int,
    id_tarefa int,
    id_usuario int not null,
    foreign key (id_historico) references historico(id),
    foreign key (id_tarefa) references tarefa(id),
    foreign key (id_usuario) references usuario(id)
)
