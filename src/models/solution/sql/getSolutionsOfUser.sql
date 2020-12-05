SELECT *
FROM solution s 
JOIN diagram d USING(solution_id)
JOIN images i USING(image_id)
WHERE user_id = :user_id 
ORDER BY solution_id