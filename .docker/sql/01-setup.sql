
create table users
(
    id int NOT NULL AUTO_INCREMENT,
    is_active tinyint NOT NULL default 1,
    name varchar(255)  not null,
    email varchar(255)  not null,
    points_balance int default 0 null,
    constraint users_pk
        primary key (id)
)  ENGINE = InnoDB;

create table user_points_activity
(
    id int NOT NULL AUTO_INCREMENT,
    user_id int NOT NULL,
    points int NOT NULL,
    description varchar(255),
    created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT `user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB;