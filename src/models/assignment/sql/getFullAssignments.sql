SELECT 
    u.user_id as student_id,

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

    t.user_id as teacher_id,
    t.first_name,
    t.last_name


    
FROM active_user u
JOIN college_group g USING(college_group_id)
JOIN planned_assign p ON p.plan_id = g.plan_id
JOIN assign a USING(assign_id)
LEFT JOIN solution s USING(planned_assign_id, user_id)
LEFT JOIN active_user t ON reviewed_by = t.user_id
WHERE date_gte(NOW(), start_date) OR s.solution_id IS NOT NULL
ORDER BY planned_assign_id
