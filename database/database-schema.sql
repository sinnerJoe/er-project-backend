-- HELPER FUNCTIONS 

DROP FUNCTION IF EXISTS in_year;

CREATE FUNCTION in_year(checked_date date, y INT) RETURNS BOOLEAN DETERMINISTIC
BEGIN 
	RETURN checked_date BETWEEN STR_TO_DATE(concat(y, '-09-01'), '%Y-%m-%d') 
							AND STR_TO_DATE(concat(y + 1, '-09-01'), '%Y-%m-%d') - INTERVAL 1 DAY;
END;

-- HELPER FUNCTIONS END

-- CLEANUP
DROP TABLE IF EXISTS images, 
                    solution, 
                    planned_assign, 
                    plan, 
                    assign, 
                    user_session, 
                    user_account, 
                    college_group;
--

-- TABLE-DEFINITIONS


CREATE TABLE IF NOT EXISTS user_account (
	user_id INT AUTO_INCREMENT primary key,
    first_name varchar(64),
    last_name varchar(64),
	email varchar(64) NOT NULL UNIQUE,
    role_level INT,
    disabled boolean NOT NULL default(FALSE),
    password varchar(256) not null,
    created_at DATETIME DEFAULT(CURRENT_TIMESTAMP)
);

CREATE TABLE IF NOT EXISTS college_group (
	college_group_id INT AUTO_INCREMENT PRIMARY KEY,
	name varchar(8) NOT NULL,
	ed_year INT NOT NULL,
	coordinator_id INT NULL REFERENCES user_account(user_id)
);


ALTER TABLE user_account ADD COLUMN college_group_id INT NULL REFERENCES college_group(college_group_id);

CREATE TABLE IF NOT EXISTS user_session (
	session_id varchar(255) NOT NULL PRIMARY KEY,
	user_id INT REFERENCES user_account(user_id),
	login_time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS plan (
	plan_id INT AUTO_INCREMENT PRIMARY KEY,
	name varchar(128) NOT NULL UNIQUE,
	updated_at DATETIME default(CURRENT_TIMESTAMP)
);

ALTER TABLE college_group ADD COLUMN plan_id INT REFERENCES plan(plan_id);

CREATE TABLE IF NOT EXISTS assign (
	assign_id INT  AUTO_INCREMENT PRIMARY KEY,
	title varchar(128) NOT NULL,
	description TEXT,
	updated_at DATETIME DEFAULT(CURRENT_TIMESTAMP)
);

CREATE TABLE IF NOT EXISTS planned_assign (
	planned_assign_id INT AUTO_INCREMENT PRIMARY KEY,
	start_date DATETIME NOT NULL,
	end_date DATETIME NOT NULL,
	assign_id INT NOT NULL REFERENCES assign(assign_id),
	plan_id INT NOT NULL REFERENCES plan(plan_id)
);

CREATE TABLE IF NOT EXISTS solution (
	solution_id INT AUTO_INCREMENT PRIMARY KEY,
	title varchar(128),
	user_id INT NOT NULL REFERENCES user_account(user_id),
	planned_assign_id INT NULL REFERENCES planned_assign(planned_assign_id),
	created_at DATETIME NOT NULL DEFAULT(CURRENT_TIMESTAMP),
	updated_at DATETIME NOT NULL DEFAULT(CURRENT_TIMESTAMP),
	mark INT NULL CHECK(mark <= 10 AND mark >= 1 OR mark IS null),
	reviewed_by INT NULL REFERENCES user_account(user_id),
	reviewed_at DATETIME NULL
);

CREATE TABLE IF NOT EXISTS images (
	image_id INT AUTO_INCREMENT PRIMARY KEY,
	filepath varchar(256) NOT NULL
);

CREATE TABLE IF NOT EXISTS diagram (
	diagram_id INT AUTO_INCREMENT PRIMARY KEY,
	solution_id INT NOT NULL REFERENCES solution(solution_id),
	name varchar(64),
	content TEXT NOT NULL,
	image_id INT NULL REFERENCES images(image_id)
);
