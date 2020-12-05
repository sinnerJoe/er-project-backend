SELECT *
FROM solution
JOIN diagram USING(solution_id)
JOIN images USING(image_id)
WHERE solution_id = :solution_id