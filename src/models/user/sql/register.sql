INSERT INTO user_account (first_name, last_name, email, college_group_id, role_level, disabled, password)
VALUES(
    :first_name,
    :last_name,
    :email,
    :college_group_id,
    10, -- regular student
    TRUE, -- disabled until user enables it by following the link sent to his email address
    :password
);