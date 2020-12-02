DELETE FROM user_session 
WHERE session_id != :sid 
AND user_id != :userId