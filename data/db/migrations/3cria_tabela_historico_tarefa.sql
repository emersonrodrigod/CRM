create table if not exists historico_tarefa(
    id int not null auto_increment primary key,
    quando timestamp not null default current_timestamp,
    texto text not null,
    id_tarefa int not null,
    id_usuario int not null,
    foreign key (id_tarefa) references tarefa(id),
    foreign key (id_usuario) references usuario(id)
)
