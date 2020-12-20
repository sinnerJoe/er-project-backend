SELECT *, c.college_group_id as group_id
FROM user_account
LEFT JOIN college_group c ON coordinator_id = user_id AND ed_year = :year
WHERE role_level = 5
ORDER BY user_id, c.college_group_id