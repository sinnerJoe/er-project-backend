SELECT
    g.college_group_id,

    start_date,
    end_date,
    p.planned_assign_id,

    a.assign_id,
    a.title,
    a.description,

    s.solution_id,
    s.title as solution_title,
    s.submitted_at,
    s.created_at,
    s.updated_at,
    s.mark,
    s.reviewed_at,

    d.diagram_id,
    d.name as diagram_name,
    img.filepath as image,
    
    u.user_id as student_id,
    u.first_name as student_first_name,
    u.last_name as student_last_name,
    u.email,

    t.user_id as teacher_id,
    t.first_name as teacher_first_name,
    t.last_name as teacher_last_name


    
FROM college_group  g
JOIN planned_assign p USING(plan_id)
JOIN assign a USING(assign_id)
LEFT JOIN active_user u USING(college_group_id)
LEFT JOIN solution s ON s.planned_assign_id = p.planned_assign_id AND s.user_id = u.user_id
LEFT JOIN diagram d USING(solution_id)
LEFT JOIN images img USING(image_id)
LEFT JOIN active_user t ON s.reviewed_by = t.user_id
ORDER BY planned_assign_id, u.user_id, d.diagram_id 

