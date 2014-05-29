create table if not exists migrations (
    id int not null auto_increment primary key,
    nome varchar(255) not null,
    quando timestamp not null default current_timestamp,
    id_usuario int not null,
    foreign key(id_usuario) references usuario(id)
);