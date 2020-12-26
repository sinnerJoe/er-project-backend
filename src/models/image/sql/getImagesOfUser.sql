SELECT image_id, filepath
FROM user_account u 
JOIN solution USING(user_id)
JOIN diagram USING(solution_id)
JOIN images i USING(image_id)
WHERE u.user_id = :user_id