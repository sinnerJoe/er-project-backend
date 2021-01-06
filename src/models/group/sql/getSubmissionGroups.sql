SELECT 
    g.college_group_id,
    g.name, 
    ed_year,
    g.coordinator_id,
    (SELECT COUNT(*) 
     FROM active_user a 
     JOIN solution USING(user_id)
     JOIN planned_assign p USING(planned_assign_id)
     WHERE g.plan_id = p.plan_id 
       AND a.college_group_id = g.college_group_id
       AND mark IS NULL
     ) as unchecked_count
FROM college_group g
WHERE plan_id IS NOT NULL
GROUP BY g.name, g.college_group_id