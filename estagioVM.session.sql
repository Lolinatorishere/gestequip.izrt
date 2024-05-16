CREATE table if not EXISTS equipment(
    id int NOT NULL AUTO_INCREMENT,
    registration_date datetime DEFAULT CURRENT_TIMESTAMP,
    registration_lock tinyint(1) DEFAULT NULL,
    equipment_type int NOT NULL,
    -- 0 delivered, 1 is in storage, 2 is ordered, 3 to be ordered
    delivery_status int DEFAULT NULL,
    purchase_date date DEFAULT NULL,
    brand varchar(255) DEFAULT NULL,
    serial_number text NOT NULL,
    -- 0 is active the higher the int the closer to retirement
    equipment_status int NOT NULL,
    serial_md5 char(32) AS (md5(serial_number)) unique not null,
    PRIMARY KEY (id),
    CONSTRAINT equipment_ibfk_1 FOREIGN KEY (equipment_type) REFERENCES equipment_types (id)
);

CREATE TABLE equipment_types (
    id int NOT NULL AUTO_INCREMENT,
    equipment_type varchar(255) NOT NULL,
    PRIMARY KEY (id)
);

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

CREATE TABLE IF NOT EXISTS users_inside_groups(
    user_id INT,
    group_id INT,
    user_permission_level INT NOT NULL,
    PRIMARY KEY (user_id, group_id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (group_id) REFERENCES user_groups(id)
);

CREATE TABLE IF NOT EXISTS users_inside_groups_equipments(
    user_id INT,
    group_id INT,
    equipment_id INT,
    user_permission_level INT NOT NULL,
    status INT NOT NULL,
    PRIMARY KEY (user_id, group_id , equipment_id),
    constraint users_inside_groups_fk FOREIGN KEY (user_id, group_id) REFERENCES users_inside_groups(user_id, group_id),
    constraint fk_equipment foreign key (equipment_id) references equipment(id)
);

CREATE TABLE IF NOT EXISTS user_logs(
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    log_description TEXT NOT NULL,
    date_modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS users (
  id int NOT NULL AUTO_INCREMENT,
  username varchar(256) DEFAULT NULL,
  pass char(255) NOT NULL,
  users_name varchar(256) DEFAULT NULL,
  email varchar(256) DEFAULT NULL,
  phone_number bigint DEFAULT NULL,
  regional_indicator varchar(10) DEFAULT NULL,
  date_created datetime DEFAULT CURRENT_TIMESTAMP,
  account_status int DEFAULT NULL,
  active_directory_user tinyint(1) DEFAULT NULL,
  PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS sudo_group (
  id int NOT NULL AUTO_INCREMENT,
  id_user int NOT NULL,
  admin_status tinyint(1) DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY id (id),
  UNIQUE KEY id_user (id_user),
  FOREIGN KEY (id_user) REFERENCES users (id)
);

-- 
-- CREATE OR REPLACE VIEW computer_equipment AS
    -- SELECT 
        --   e.id,
        --   e.registration_date,
        --   e.registration_lock,
        --   e.equipment_type,
        --   e.delivery_status,
        --   e.purchase_date,
        --   e.brand,
        --   e.serial_number,
        --   e.equipment_status,
        --   c.id as computer_id,
        --   c.business_unit,
        --   c.computer_model,
        --   c.computer_type,
        --   c.os,
        --   c.has_battery,
        --   c.ram,
        --   c.psu,
        --   c.cpu,
        --   c.drives,
        --   c.gpu,
        --   c.mac_address
    -- from equipment e, computers c
    -- where e.id = c.equipment_id;
-- 
-- CREATE OR REPLACE VIEW phone_equipment AS
    -- SELECT 
        --   e.id,
        --   e.registration_date,
        --   e.registration_lock,
        --   e.equipment_type,
        --   e.delivery_status,
        --   e.purchase_date,
        --   e.brand,
        --   e.serial_number,
        --   e.equipment_status,
        --   p.equipment_id,
        --   p.holder,
        --   p.phone_model,
        --   p.phone_number,
        --   p.country_code,
        --   p.IMEI,
        --   p.opperator,
        --   p.mobile_data,
        --   p.phone_plan_cost,
        --   p.currency,
        --   p.roaming
    -- from equipment e, phones p
    -- where e.id = p.equipment_id;
-- 