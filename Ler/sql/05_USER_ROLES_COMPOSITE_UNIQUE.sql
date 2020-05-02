ALTER TABLE `UserRoles` ADD UNIQUE `unique_index`
(`role_id`, `user_id`);