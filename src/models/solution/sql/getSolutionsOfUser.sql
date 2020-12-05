SELECT *
FROM solution s 
JOIN diagram d USING(solution_id)
JOIN images i USING(image_id)
-- TODO: Add joins later
-- LEFT JOIN planned_assign p USING(planned_assign_id)
WHERE user_id = :user_id 
ORDER BY solution_id, diagram_id