SELECT 
    a.assign_id, 
    a.title, 
    a.description,
    a.updated_at,

    pa.planned_assign_id,
    start_date,
    end_date,

    p.plan_id,
    p.name

 FROM assign a
 LEFT JOIN planned_assign pa USING(assign_id)
 LEFT JOIN plan p USING(plan_id)
 ORDER BY a.updated_at DESC, a.assign_id DESC, pa.planned_assign_id DESC
