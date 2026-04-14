ALTER TABLE control_commands
	ADD COLUMN pond_code VARCHAR(32) NOT NULL DEFAULT 'pond_01' AFTER id,
	ADD COLUMN device_type VARCHAR(64) NOT NULL DEFAULT '增氧机' AFTER pond_code,
	ADD COLUMN device_no INT NOT NULL DEFAULT 1 AFTER device_type;
