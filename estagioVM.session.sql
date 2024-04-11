create table if NOT EXISTS computers(
    id int not null auto_increment,
    equipment_id int not null unique,
    business_unit text,
    hwid varchar(255) unique,
    computer_model text,
    computer_type int,
    os text,
    has_battery boolean,
    ram text,
    psu text,
    cpu text,
    drives text,
    gpu text, 
    primary key (id),
    foreign key (equipment_id) references equipment(id)
);

create table if NOT EXISTS phones(
    id int not null auto_increment,
    equipment_id int not null unique,
    holder text,
    phone_model text,
    phone_number varchar(255),
    country_code varchar(10),
    IMEI varchar(255) unique,
    opperator varchar(255),
    mobile_data varchar(255),
    phone_plan_cost varchar(255),
    currency varchar(63),
    roaming boolean,
    primary key (id),
    foreign key (equipment_id) references equipment(id)
);

create table IF NOT EXISTS retired_equipments(
    id int not null auto_increment,
    equipment_id int not null unique,
    retire_date date,
    reason text,
    comment text,
    primary key (id),
    foreign key (equipment_id) references equipment(id)
);

create table IF NOT EXISTS equipment_logs(
    id int not null auto_increment,
    equipment_id int not null,
    log_description text,
    date_modified timestamp default current_timestamp, 
    primary key (id),
    foreign key (equipment_id) references equipment(id)
);

create table IF NOT EXISTS equipment_invoices(
    id int not null auto_increment,
    equipment_id int not null,
    invoice_description text,
    file_name varchar(255) unique,
    date_modified timestamp default current_timestamp, 
    primary key (id),
    foreign key (equipment_id) references equipment(id)
);

CREATE TABLE IF NOT EXISTS user_groups(
    id INT NOT NULL AUTO_INCREMENT,
    group_name VARCHAR(255),
    group_type INT,
    group_status INT,
    PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS users_groups(
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT,
    group_id INT,
    user_permission_level INT NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (group_id) REFERENCES user_groups(id)
);

CREATE TABLE IF NOT EXISTS users_groups_equipment(
    id INT NOT NULL AUTO_INCREMENT,
    usersgroups_id INT,
    equipment_id INT,
    user_permission_level INT NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (usersgroups_id) REFERENCES users_groups(id),
    FOREIGN KEY (equipment_id) REFERENCES equipment(id)
);

CREATE TABLE IF NOT EXISTS user_logs(
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    log_description TEXT NOT NULL,
    date_modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);