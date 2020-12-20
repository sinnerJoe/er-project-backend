SELECT 
    g.college_group_id,
    g.name, 
    ed_year,
    s.user_id,
    s.first_name,
    s.last_name,
    s.email,
    s.created_at,
    c.user_id as coord_user_id,
    c.first_name as coord_first_name,
    c.last_name as coord_last_name,
    c.email as coord_email,
    p.plan_id,
    p.name as plan_name
FROM college_group g
LEFT JOIN user_account s USING(college_group_id)
LEFT JOIN user_account c ON c.user_id = coordinator_id
LEFT JOIN plan p USING (plan_id)
ORDER BY college_group_id, coordinator_id, s.user_id