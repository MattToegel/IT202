SELECT r.id, r.name from Roles r join UserRoles ur on r.id = ur.role_id 
WHERE user_id = :id AND ur.is_active = 1 AND r.is_active = 1