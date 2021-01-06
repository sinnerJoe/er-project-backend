SELECT count(solution_id) as count
FROM solution 
JOIN active_user USING(user_id)
WHERE user_id = :user_id