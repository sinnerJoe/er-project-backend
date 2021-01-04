SELECT *
FROM active_user
LEFT JOIN college_group USING(college_group_id)