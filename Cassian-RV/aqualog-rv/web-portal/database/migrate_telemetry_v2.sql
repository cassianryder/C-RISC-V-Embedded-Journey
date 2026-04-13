ALTER TABLE telemetry_samples
	ADD COLUMN pond_code VARCHAR(32) NOT NULL DEFAULT 'pond_01' AFTER sampled_at,
	ADD COLUMN ammonia_nitrogen DECIMAL(10,2) NULL AFTER water_level,
	ADD COLUMN nitrite DECIMAL(10,2) NULL AFTER ammonia_nitrogen,
	ADD COLUMN salinity DECIMAL(10,2) NULL AFTER nitrite,
	ADD COLUMN alkalinity DECIMAL(10,2) NULL AFTER salinity;

ALTER TABLE telemetry_samples
	DROP INDEX sampled_at,
	ADD UNIQUE KEY uniq_telemetry_pond_time (pond_code, sampled_at);
