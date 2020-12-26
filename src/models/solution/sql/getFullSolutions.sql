SELECT
    s.solution_id,
    s.user_id,
    s.planned_assign_id,
    s.created_at,
    s.updated_at,
    mark,
    reviewed_at,
    s.title,

    s.reviewed_by,
    t.first_name,
    t.last_name,

    d.diagram_id,
    d.name,
    d.type,
    d.content,
    i.filepath,

    a.assign_id,
    a.title as a_title
FROM solution s 
JOIN diagram d USING(solution_id)
JOIN images i USING(image_id)
LEFT JOIN planned_assign p USING(planned_assign_id)
LEFT JOIN assign a USING(assign_id)
LEFT JOIN user_account t ON t.user_id = s.reviewed_by
ORDER BY solution_id, diagram_id