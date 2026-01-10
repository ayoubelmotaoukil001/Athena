create database athena;
use athena;

create table user (
    id int auto_increment primary key,
    nom varchar(100) not null,
    email varchar(150) not null unique,
    password varchar(255) not null,
    role enum('admin','projectchef','membre') not null
);

create table project (
    projet_id int auto_increment primary key,
    titre varchar(150) not null,
    description text,
    etat enum('actif','inactif'),
    chef_id int not null,
    foreign key (chef_id) references user(id)
);

create table sprint (
    sprintid int auto_increment primary key,
    nom varchar(100) not null,
    date_debut date not null,
    date_fin date not null,
    projet_id int not null,
    foreign key (projet_id) references project(projet_id) on delete cascade
);

create table task (
    task_id int auto_increment primary key,
    title varchar(150) not null,
    description text,
    status enum('a_faire','en_cours','terminee'),
    date_fin date,
    sprint_id int not null,
    user_id int not null,
    foreign key (sprint_id) references sprint(sprintid) on delete cascade,
    foreign key (user_id) references user(id)
);

create table comment (
    id int auto_increment primary key,
    contenu text not null,
    user_id int not null,
    task_id int not null,
    foreign key (user_id) references user(id),
    foreign key (task_id) references task(task_id) on delete cascade
);

create table notification (
    id int auto_increment primary key,
    message text not null,
    user_id int not null,
    task_id int not null,
    foreign key (user_id) references user(id),
    foreign key (task_id) references task(task_id) on delete cascade
);
