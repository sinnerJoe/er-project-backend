INSERT INTO user_account (first_name, last_name, email, college_group_id, role_level, password)
VALUES(
    :first_name,
    :last_name,
    :email,
    :college_group_id,
    10, -- regular student
    :password
);