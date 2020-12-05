SELECT *
FROM diagram
JOIN images USING(diagram_id)
WHERE diagram_id = :diagram_id