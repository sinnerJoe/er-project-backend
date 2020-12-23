SELECT t.user_id
FROM solution
JOIN user_account using(user_id)
JOIN college_group using(college_group_id)
JOIN user_account t ON coordinator_id = t.user_id
WHERE solution_id = :id