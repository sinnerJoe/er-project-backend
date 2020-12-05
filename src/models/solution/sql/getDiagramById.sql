SELECT *
FROM diagram
JOIN images USING(image_id)
WHERE diagram_id = :diagram_id