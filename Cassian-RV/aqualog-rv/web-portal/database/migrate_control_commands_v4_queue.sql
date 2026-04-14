ALTER TABLE control_commands
	ADD COLUMN command_uuid VARCHAR(64) NOT NULL DEFAULT '' AFTER id,
	ADD COLUMN dispatched_at DATETIME NULL AFTER issued_at,
	ADD COLUMN acknowledged_at DATETIME NULL AFTER dispatched_at,
	ADD COLUMN executed_at DATETIME NULL AFTER acknowledged_at,
	ADD COLUMN device_response TEXT NULL AFTER executed_at;

UPDATE control_commands
SET command_uuid = CONCAT('cmd_', id)
WHERE command_uuid = '';
