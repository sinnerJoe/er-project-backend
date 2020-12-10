SELECT 
    plan_id,
    name,
    p.updated_at as plan_updated_at,
    planned_assign_id,
    start_date,
    end_date,
    assign_id,
    title,
    description,
    a.updated_at
FROM plan p
LEFT JOIN planned_assign pa USING(plan_id)
LEFT JOIN assign a USING (assign_id)
ORDER BY plan_id DESC, assign_id