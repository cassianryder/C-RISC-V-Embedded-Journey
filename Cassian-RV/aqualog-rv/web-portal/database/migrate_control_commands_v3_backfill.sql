UPDATE control_commands
SET
	pond_code = CASE
		WHEN pond_code IS NULL OR pond_code = '' THEN 'pond_01'
		ELSE pond_code
	END,
	device_type = CASE
		WHEN device_name LIKE '增氧机 %' THEN '增氧机'
		WHEN device_name LIKE '水泵 %' THEN '水泵'
		ELSE device_type
	END,
	device_no = CASE
		WHEN device_name LIKE '增氧机 %号' OR device_name LIKE '水泵 %号' THEN
			CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(device_name, ' ', 2), ' ', -1) AS UNSIGNED)
		ELSE device_no
	END;

UPDATE control_commands
SET device_name = CONCAT(device_type, ' ', device_no, ' 号');
