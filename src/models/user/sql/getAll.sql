SELECT 
    u.user_id,
    u.first_name,
    u.last_name,
    u.role_level,
    email,
    u.created_at,
    u.disabled,

    g.college_group_id,
    g.name,
    g.ed_year,

    s.solution_id,
    s.reviewed_at,
    mark,

    a.assign_id,
    a.title

FROM user_account u
LEFT JOIN college_group g USING(college_group_id)
LEFT JOIN solution s ON u.user_id = s.user_id AND s.mark IS NOT NULL
LEFT JOIN planned_assign p USING(planned_assign_id)
LEFT JOIN assign a USING (assign_id)
WHERE role_level != 0 AND (:year IS NULL OR in_year(u.created_at, :year))
ORDER BY u.user_id, s.submitted_at
