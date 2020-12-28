SELECT count(solution_id) as count
FROM solution 
WHERE user_id = :user_id