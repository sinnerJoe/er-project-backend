INSERT INTO user_account (first_name, last_name, email, college_group, role_level, password)
VALUES(
    :first_name,
    :last_name,
    :email,
    COALESCE(:college_group, 'N/A'),
    10, -- regular student
    :password
);