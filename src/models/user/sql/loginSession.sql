SELECT * 
FROM user_session
JOIN active_user USING(user_id)
WHERE session_id = :sid
AND login_time >= (NOW() - INTERVAL 7 DAY)