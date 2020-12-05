SELECT *
FROM solution
JOIN diagram USING(solution_id)
WHERE solution_id = :solution_id