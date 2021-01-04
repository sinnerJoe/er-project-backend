SELECT t.user_id
FROM solution
JOIN active_user using(user_id)
JOIN college_group using(college_group_id)
JOIN active_user t ON coordinator_id = t.user_id
WHERE solution_id = :id