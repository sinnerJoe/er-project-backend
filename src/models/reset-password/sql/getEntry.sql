SELECT restore_request_id, user_id
FROM restore_request
WHERE NOW() <= expires