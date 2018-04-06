CREATE DATABASE IF NOT EXISTS apilaravel;

USE apilaravel;

CREATE TABLE users(

id          int(255) auto_increment not null,
email       VARCHAR (255),
role        varchar(20),
name        varchar(20),
surname     varchar(20),
password    varchar(20),
created_at   DATETIME DEFAULT NULL,
updated_at   DATETIME DEFAULT NULL,
remember_token VARCHAR(255),
CONSTRAINT pk_users PRIMARY KEY(id)
)ENGINE=InnoDb;

CREATE TABLE cars(

id              int(255) auto_increment not null,
user_id         int(255) not null,
title           varchar(20),
description     text,
prince          varchar(20),
status          varchar(30),
created_at   DATETIME DEFAULT NULL,
updated_at   DATETIME DEFAULT NULL,

CONSTRAINT pk_cars PRIMARY KEY(id),
CONSTRAINT fk_cars_users FOREIGN KEY(user_id) REFERENCES users(id)
)ENGINE=InnoDb;