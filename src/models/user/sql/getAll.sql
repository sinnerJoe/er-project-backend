SELECT * 
FROM user_account
LEFT JOIN college_group USING(college_group_id)