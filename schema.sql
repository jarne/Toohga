create table users
(
    id          int auto_increment
        primary key,
    upin        varchar(16) not null,
    displayName varchar(64) null,
    constraint users_upin_uindex
        unique (upin)
);

create table urls
(
    id      int                                not null
        primary key,
    created datetime default CURRENT_TIMESTAMP not null,
    client  varchar(255)                       not null,
    target  longtext                           not null,
    userId  int                                null,
    constraint urls_users_id_fk
        foreign key (userId) references users (id)
)
    collate = utf8_unicode_ci;
