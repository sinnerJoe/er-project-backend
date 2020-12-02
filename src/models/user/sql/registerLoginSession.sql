REPLACE INTO user_session (session_id, user_id, login_time)
VALUES (:sid, :userId, NOW())