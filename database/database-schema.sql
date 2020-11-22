CREATE TABLE user_account (
	user_id INT AUTO_INCREMENT primary key,
    first_name varchar(64),
    last_name varchar(64),
	email varchar(64) NOT NULL UNIQUE,
    college_group varchar(8),
    role_level INT,
    password varchar(256) not null,
    created_at DATETIME DEFAULT(NOW())
);


ALTER TABLE user_account 
ADD UNIQUE KEY email_k (email);


CREATE TABLE user_session (
	session_id varchar(255) NOT NULL PRIMARY KEY,
	user_id INT REFERENCES user_account(user_id),
	login_time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE assignment (
    assignment_id INTEGER AUTO_INCREMENT primary key,
    title varchar(128) NOT NULL,
    description TEXT NOT NULL,
    created_at DATETIME default(NOW()),
    updated_at DATETIME default(NOW())

)
