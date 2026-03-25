SHOW CREATE VIEW facility_status_lga;
SHOW CREATE VIEW facility_status_lga_pivot;
SHOW CREATE VIEW facility_status_state_pivot;
SHOW CREATE VIEW hospital_details;
SHOW CREATE VIEW hospital_details_history;
SHOW CREATE VIEW hospital_offered_services;
SHOW CREATE VIEW hospital_status_tracking;
SHOW CREATE VIEW hospitals_count_by_level_lga;
SHOW CREATE VIEW hospitals_count_by_level_lga_column;
SHOW CREATE VIEW hospitals_count_by_level_state;
SHOW CREATE VIEW hospitals_count_by_level_state_column;
SHOW CREATE VIEW hospitals_count_by_ownership_state;
SHOW CREATE VIEW hospitals_count_by_ownership_state_column;
SHOW CREATE VIEW imaging_count_by_ownership_lga;
SHOW CREATE VIEW imaging_count_by_ownership_lga_column;
SHOW CREATE VIEW imaging_count_by_ownership_state;
SHOW CREATE VIEW imaging_count_by_ownership_state_column;
SHOW CREATE VIEW hospitals_count_by_ownership_level_lga;
SHOW CREATE VIEW hospitals_count_by_ownership_level_lga_column;
SHOW CREATE VIEW hospitals_count_by_ownership_level_state;
SHOW CREATE VIEW hospitals_count_by_ownership_level_state_column;
SHOW CREATE VIEW hospitals_count_by_ownership_lga;
SHOW CREATE VIEW hospitals_count_by_ownership_lga_column;



ALTER TABLE hs_hospitals_history
ADD COLUMN action VARCHAR(255) NULL,
ADD COLUMN verified_id INT NULL,
ADD COLUMN verified_email VARCHAR(255) NULL,
ADD COLUMN verified_mobile VARCHAR(20) NULL,
ADD COLUMN validated_email VARCHAR(255) NULL,
ADD COLUMN validated_mobile VARCHAR(20) NULL,
ADD COLUMN published_email VARCHAR(255) NULL,
ADD COLUMN published_mobile VARCHAR(20) NULL;

UPDATE `hs_hospitals_history` SET `action`='CREATE FACILITY' WHERE 1




CREATE OR REPLACE VIEW facility_status_lga AS
SELECT
    h.state_id,
    s.name AS state,
    h.lga_id,
    l.name AS lga,
    h.status_id,
    st.status AS status,
    COUNT(*) AS count
FROM hs_hospitals_history h
JOIN ou_states s ON h.state_id = s.id
JOIN ou_lgas l ON h.lga_id = l.id
JOIN lst_status st ON h.status_id = st.id
GROUP BY
    h.state_id, s.name,
    h.lga_id, l.name,
    h.status_id, st.status;




-- This view aggregates the status of facilities by state and LGA, providing a pivot table format for easier analysis.
-- The view counts the number of facilities in each status category for each LGA within a state.
-- The status_id values are assumed to be as follows:
-- 1: New Facility Requested
-- 2: Update Requested
-- 3: Deletion Requested
-- 4: Request Verified
-- 5: Request Validated
-- 6: Facility Created
-- 7: Facility Updated
-- 8: Facility Deleted
-- 9: Verification Rejected
-- 10: Validation Rejected
-- 11: Publishing Rejected
CREATE OR REPLACE VIEW facility_status_lga_pivot AS
SELECT
    lga.state_id,
    s.name AS state,
    lga.id AS lga_id,
    lga.name AS lga,

    COUNT(CASE WHEN h.status_id = 1 THEN 1 END) AS New_Facility_Requested,
    COUNT(CASE WHEN h.status_id = 8 THEN 1 END) AS Update_Requested,
    COUNT(CASE WHEN h.status_id = 15 THEN 1 END) AS Deletion_Requested,
    COUNT(CASE WHEN h.status_id IN (2, 9, 16) THEN 1 END) AS Request_Verified,
    COUNT(CASE WHEN h.status_id IN (4, 11, 18) THEN 1 END) AS Request_Validated,
    COUNT(CASE WHEN h.status_id = 6 THEN 1 END) AS Facility_Created,
    COUNT(CASE WHEN h.status_id = 13 THEN 1 END) AS Facility_Updated,
    COUNT(CASE WHEN h.status_id = 20 THEN 1 END) AS Facility_Deleted,
    COUNT(CASE WHEN h.status_id IN (3, 10, 17) THEN 1 END) AS Verification_Rejected,
    COUNT(CASE WHEN h.status_id IN (5, 12, 19) THEN 1 END) AS Validation_Rejected,
    COUNT(CASE WHEN h.status_id IN (7, 14, 21) THEN 1 END) AS Publishing_Rejected

FROM hs_hospitals_history h
JOIN ou_lgas lga ON h.lga_id = lga.id
JOIN ou_states s ON lga.state_id = s.id
GROUP BY lga.id, lga.state_id, s.name, lga.name;



-- This view aggregates the status of facilities by state, providing a pivot table format for easier analysis.
-- The view counts the number of facilities in each status category for each state.
-- The status_id values are assumed to be as follows:
-- 1: New Facility Requested
-- 2: Update Requested
-- 3: Deletion Requested
-- 4: Request Verified
-- 5: Request Validated
-- 6: Facility Created
-- 7: Facility Updated
-- 8: Facility Deleted
-- 9: Verification Rejected
-- 10: Validation Rejected
-- 11: Publishing Rejected
-- The view can be used to quickly assess the status of facilities across different states.

CREATE OR REPLACE VIEW facility_status_state_pivot AS
SELECT
  h.state_id,
  s.name AS state,
  SUM(CASE WHEN h.status_id = 1 THEN 1 ELSE 0 END) AS New_Facility_Requested,
  SUM(CASE WHEN h.status_id = 8 THEN 1 ELSE 0 END) AS Update_Requested,
  SUM(CASE WHEN h.status_id = 15 THEN 1 ELSE 0 END) AS Deletion_Requested,
  SUM(CASE WHEN h.status_id IN (2, 9, 16) THEN 1 ELSE 0 END) AS Request_Verified,
  SUM(CASE WHEN h.status_id IN (4, 11, 18) THEN 1 ELSE 0 END) AS Request_Validated,
  SUM(CASE WHEN h.status_id = 6 THEN 1 ELSE 0 END) AS Facility_Created,
  SUM(CASE WHEN h.status_id = 13 THEN 1 ELSE 0 END) AS Facility_Updated,
  SUM(CASE WHEN h.status_id = 20 THEN 1 ELSE 0 END) AS Facility_Deleted,
  SUM(CASE WHEN h.status_id IN (3, 10, 17) THEN 1 ELSE 0 END) AS Verification_Rejected,
  SUM(CASE WHEN h.status_id IN (5, 12, 19) THEN 1 ELSE 0 END) AS Validation_Rejected,
  SUM(CASE WHEN h.status_id IN (7, 14, 21) THEN 1 ELSE 0 END) AS Publishing_Rejected
FROM
  hs_hospitals_history h
JOIN
  ou_states s ON h.state_id = s.id
GROUP BY
  h.state_id, s.name;




CREATE OR REPLACE VIEW hospital_details AS
SELECT
    id,
    unique_id,
    state_unique_id,
    registration_no,
    start_date,
    close_date,
    facility_name,
    image_url,
    alt_facility_name,
    state_id,
    lga_id,
    ward_id,
    ownership_id,
    ownership_type_id,
    facility_level_id,
    facility_type_id,
    facility_level_option_id,
    facility_level_options_category_id,
    physical_location,
    postal_address,
    longitude,
    latitude,
    phone_number,
    alternate_number,
    email_address,
    website,
    operational_days,
    operational_hours,
    operational_status_id,
    registration_status_id,
    license_status_id,
    outpatient,
    inpatient,
    doctors,
    pharmacists,
    dentist,
    pharmacy_technicians,
    nurses,
    lab_scientists,
    midwifes,
    lab_technicians,
    nurse_midwife,
    him_officers,
    community_health_officer,
    community_extension_workers,
    jun_community_extension_worker,
    dental_technicians,
    env_health_officers,
    attendants,
    beds,
    onsite_laboratory,
    onsite_imaging,
    onsite_pharmarcy,
    mortuary_services,
    ambulance_services,
    status_id,
    created_by,
    created_at,
    updated_at,
    requested_by,
    requested_at,
    request_note,
    verified_by,
    verified_at,
    verify_note,
    validated_by,
    validated_at,
    validate_note,
    published_by,
    published_at,
    publish_note,
    service_id88,
    action,
    verified_id,
    verified_email,
    verified_mobile,
    validated_email,
    validated_mobile,
    published_email,
    published_mobile
FROM hs_hospitals_history;



CREATE OR REPLACE VIEW hospital_details_history AS
SELECT
    id,
    unique_id,
    state_unique_id,
    registration_no,
    start_date,
    close_date,
    facility_name,
    image_url,
    alt_facility_name,
    state_id,
    lga_id,
    ward_id,
    ownership_id,
    ownership_type_id,
    facility_level_id,
    facility_type_id,
    facility_level_option_id,
    facility_level_options_category_id,
    physical_location,
    postal_address,
    longitude,
    latitude,
    phone_number,
    alternate_number,
    email_address,
    website,
    operational_days,
    operational_hours,
    operational_status_id,
    registration_status_id,
    license_status_id,
    outpatient,
    inpatient,
    doctors,
    pharmacists,
    dentist,
    pharmacy_technicians,
    nurses,
    lab_scientists,
    midwifes,
    lab_technicians,
    nurse_midwife,
    him_officers,
    community_health_officer,
    community_extension_workers,
    jun_community_extension_worker,
    dental_technicians,
    env_health_officers,
    attendants,
    beds,
    onsite_laboratory,
    onsite_imaging,
    onsite_pharmarcy,
    mortuary_services,
    ambulance_services,
    status_id,
    created_by,
    created_at,
    updated_at,
    requested_by,
    requested_at,
    request_note,
    verified_by,
    verified_at,
    verify_note,
    validated_by,
    validated_at,
    validate_note,
    published_by,
    published_at,
    publish_note,
    service_id88,
    action,
    verified_id,
    verified_email,
    verified_mobile,
    validated_email,
    validated_mobile,
    published_email,
    published_mobile
FROM hs_hospitals_history;



CREATE OR REPLACE VIEW hospital_offered_services AS
SELECT
    h.id,
    h.unique_id,
    h.state_unique_id,
    h.registration_no,
    h.start_date,
    h.close_date,
    h.facility_name,
    h.image_url,
    h.alt_facility_name,
    h.state_id,
    h.lga_id,
    h.ward_id,
    h.ownership_id,
    h.ownership_type_id,
    h.facility_level_id,
    h.facility_type_id,
    h.facility_level_option_id,
    h.facility_level_options_category_id,
    h.physical_location,
    h.postal_address,
    h.longitude,
    h.latitude,
    h.phone_number,
    h.alternate_number,
    h.email_address,
    h.website,
    h.operational_days,
    h.operational_hours,
    h.operational_status_id,
    h.registration_status_id,
    h.license_status_id,
    h.outpatient,
    h.inpatient,
    h.doctors,
    h.pharmacists,
    h.dentist,
    h.pharmacy_technicians,
    h.nurses,
    h.lab_scientists,
    h.midwifes,
    h.lab_technicians,
    h.nurse_midwife,
    h.him_officers,
    h.community_health_officer,
    h.community_extension_workers,
    h.jun_community_extension_worker,
    h.dental_technicians,
    h.env_health_officers,
    h.attendants,
    h.beds,
    h.onsite_laboratory,
    h.onsite_imaging,
    h.onsite_pharmarcy,
    h.mortuary_services,
    h.ambulance_services,
    h.status_id,
    h.created_by,
    h.created_at,
    h.updated_at,
    h.requested_by,
    h.requested_at,
    h.request_note,
    h.verified_by,
    h.verified_at,
    h.verify_note,
    h.validated_by,
    h.validated_at,
    h.validate_note,
    h.published_by,
    h.published_at,
    h.publish_note,
    h.service_id88,
    h.action,
    h.verified_id,
    h.verified_email,
    h.verified_mobile,
    h.validated_email,
    h.validated_mobile,
    h.published_email,
    h.published_mobile,
    COALESCE(sv.services, '') AS services
FROM hs_hospitals_history h
LEFT JOIN (
    SELECT
        hs.hospital_id,
        GROUP_CONCAT(DISTINCT s.name ORDER BY s.name SEPARATOR ', ') AS services
    FROM hs_hospital_services hs
    INNER JOIN lst_hosp_services s ON hs.service_id = s.id
    GROUP BY hs.hospital_id
) sv ON sv.hospital_id = h.id
WHERE h.status_id IN (6, 20, 13);




CREATE OR REPLACE VIEW hospital_status_tracking AS
SELECT
    h.id AS hospital_id,
    h.state_id,
    hs.status_id,
    h.action,
    st.status,
    hs.created_at,
    hs.note,
    hs.user_id,
    CONCAT(u.firstname, ' ', u.lastname) AS user,
    u.mobile,
    u.email,
    u.job_title
FROM hs_status_tracking hs
JOIN hospital_details_history h ON hs.hospital_id = h.id
LEFT JOIN lst_status st ON hs.status_id = st.id
LEFT JOIN users u ON hs.user_id = u.id;




CREATE OR REPLACE VIEW hospitals_count_by_level_lga AS
SELECT
    l.state_id,
    s.name AS state,
    l.id AS lga_id,
    l.name AS lga,
    fl.name AS facility_level,
    COUNT(*) AS total
FROM hs_hospitals_history h
JOIN ou_lgas l ON h.lga_id = l.id
JOIN ou_states s ON l.state_id = s.id
JOIN lst_level_of_care fl ON h.facility_level_id = fl.id
GROUP BY l.id, l.state_id, fl.name;





CREATE OR REPLACE VIEW hospitals_count_by_level_lga_column AS
SELECT
    l.state_id,
    s.name AS state,
    l.id AS lga_id,
    l.name AS lga,
    SUM(CASE WHEN fl.name = 'primary' THEN 1 ELSE 0 END) AS `Primary`,
    SUM(CASE WHEN fl.name = 'secondary' THEN 1 ELSE 0 END) AS `Secondary`,
    SUM(CASE WHEN fl.name = 'tertiary' THEN 1 ELSE 0 END) AS `Tertiary`
FROM hs_hospitals_history h
JOIN ou_lgas l ON h.lga_id = l.id
JOIN ou_states s ON l.state_id = s.id
JOIN lst_level_of_care fl ON h.facility_level_id = fl.id
GROUP BY l.id, l.state_id, s.name, l.name;



CREATE OR REPLACE VIEW hospitals_count_by_level_state AS
SELECT
    s.id AS state_id,
    s.name AS state,
    fl.name AS facility_level,
    COUNT(*) AS total
FROM hs_hospitals_history h
JOIN ou_states s ON h.state_id = s.id
JOIN lst_level_of_care fl ON h.facility_level_id = fl.id
GROUP BY s.id, s.name, fl.name;



CREATE OR REPLACE VIEW hospitals_count_by_level_state_column AS
SELECT
    s.id AS state_id,
    s.name AS state,
    SUM(CASE WHEN fl.name = 'primary' THEN 1 ELSE 0 END) AS `Primary`,
    SUM(CASE WHEN fl.name = 'secondary' THEN 1 ELSE 0 END) AS `Secondary`,
    SUM(CASE WHEN fl.name = 'tertiary' THEN 1 ELSE 0 END) AS `Tertiary`
FROM hs_hospitals_history h
JOIN ou_states s ON h.state_id = s.id
JOIN lst_level_of_care fl ON h.facility_level_id = fl.id
GROUP BY s.id, s.name;






CREATE OR REPLACE VIEW hospitals_count_by_ownership_state AS
SELECT
    s.id AS state_id,
    s.name AS state,
    o.name AS ownership,
    COUNT(*) AS total
FROM hs_hospitals_history h
JOIN ou_states s ON h.state_id = s.id
JOIN lst_ownerships o ON h.ownership_id = o.id
GROUP BY s.id, s.name, o.name;



CREATE OR REPLACE VIEW hospitals_count_by_ownership_state_column AS
SELECT
    s.id AS state_id,
    s.name AS state,
    SUM(CASE WHEN o.name = 'Private' THEN 1 ELSE 0 END) AS Private,
    SUM(CASE WHEN o.name = 'Public' THEN 1 ELSE 0 END) AS Public
FROM hs_hospitals_history h
JOIN ou_states s ON h.state_id = s.id
JOIN lst_ownerships o ON h.ownership_id = o.id
GROUP BY s.id, s.name;


CREATE OR REPLACE VIEW imaging_count_by_ownership_lga AS
SELECT
    s.id AS state_id,
    s.name AS state,
    l.id AS lga_id,
    l.name AS lga,
    o.name AS ownership,
    COUNT(*) AS total
FROM hs_hospitals_history h
JOIN ou_lgas l ON h.lga_id = l.id
JOIN ou_states s ON l.state_id = s.id
JOIN lst_ownerships o ON h.ownership_id = o.id
WHERE h.onsite_imaging = 1
GROUP BY s.id, s.name, l.id, l.name, o.name;


CREATE OR REPLACE VIEW imaging_count_by_ownership_lga_column AS
SELECT
    s.id AS state_id,
    s.name AS state,
    l.id AS lga_id,
    l.name AS lga,
    SUM(CASE WHEN o.name = 'Public' THEN 1 ELSE 0 END) AS Public,
    SUM(CASE WHEN o.name = 'Private' THEN 1 ELSE 0 END) AS Private
FROM hs_hospitals_history h
JOIN ou_lgas l ON h.lga_id = l.id
JOIN ou_states s ON l.state_id = s.id
JOIN lst_ownerships o ON h.ownership_id = o.id
WHERE h.onsite_imaging = 'Yes'
GROUP BY s.id, s.name, l.id, l.name;


CREATE OR REPLACE VIEW imaging_count_by_ownership_state AS
SELECT
    s.id AS state_id,
    s.name AS state,
    o.name AS ownership,
    COUNT(*) AS total
FROM hs_hospitals_history h
JOIN ou_states s ON h.state_id = s.id
JOIN lst_ownerships o ON h.ownership_id = o.id
WHERE h.onsite_imaging = 'Yes'
GROUP BY s.id, s.name, o.name;


CREATE OR REPLACE VIEW imaging_count_by_ownership_state_column AS
SELECT
    s.id AS state_id,
    s.name AS state,
    SUM(CASE WHEN o.name = 'Public' THEN 1 ELSE 0 END) AS Public,
    SUM(CASE WHEN o.name = 'Private' THEN 1 ELSE 0 END) AS Private
FROM hs_hospitals_history h
JOIN ou_states s ON h.state_id = s.id
JOIN lst_ownerships o ON h.ownership_id = o.id
WHERE h.onsite_imaging = 'Yes'
GROUP BY s.id, s.name;



CREATE OR REPLACE VIEW hospitals_count_by_ownership_level_lga AS
SELECT
    s.id AS state_id,
    s.name AS state,
    l.id AS lga_id,
    l.name AS lga,
    o.name AS ownership,
    fl.name AS facility_level,
    COUNT(*) AS total
FROM hs_hospitals_history h
JOIN ou_lgas l ON h.lga_id = l.id
JOIN ou_states s ON l.state_id = s.id
JOIN lst_ownerships o ON h.ownership_id = o.id
JOIN lst_level_of_care fl ON h.facility_level_id = fl.id
GROUP BY s.id, s.name, l.id, l.name, o.name, fl.name;


CREATE OR REPLACE VIEW hospitals_count_by_ownership_level_lga_column AS
SELECT
    s.id AS state_id,
    s.name AS state,
    l.id AS lga_id,
    l.name AS lga,
    SUM(CASE WHEN o.name = 'Public' AND fl.name = 'primary' THEN 1 ELSE 0 END) AS Pub_Primary,
    SUM(CASE WHEN o.name = 'Public' AND fl.name = 'secondary' THEN 1 ELSE 0 END) AS Pub_Secondary,
    SUM(CASE WHEN o.name = 'Public' AND fl.name = 'tertiary' THEN 1 ELSE 0 END) AS Pub_Tertiary,
    SUM(CASE WHEN o.name = 'Private' AND fl.name = 'primary' THEN 1 ELSE 0 END) AS Priv_Primary,
    SUM(CASE WHEN o.name = 'Private' AND fl.name = 'secondary' THEN 1 ELSE 0 END) AS Priv_Secondary,
    SUM(CASE WHEN o.name = 'Private' AND fl.name = 'tertiary' THEN 1 ELSE 0 END) AS Priv_Tertiary
FROM hs_hospitals_history h
JOIN ou_lgas l ON h.lga_id = l.id
JOIN ou_states s ON l.state_id = s.id
JOIN lst_ownerships o ON h.ownership_id = o.id
JOIN lst_level_of_care fl ON h.facility_level_id = fl.id
GROUP BY s.id, s.name, l.id, l.name;





CREATE OR REPLACE VIEW hospitals_count_by_ownership_level_state AS
SELECT
    s.id AS state_id,
    s.name AS state,
    o.name AS ownership,
    fl.name AS facility_level,
    COUNT(*) AS total
FROM hs_hospitals_history h
JOIN ou_states s ON h.state_id = s.id
JOIN lst_ownerships o ON h.ownership_id = o.id
JOIN lst_level_of_care fl ON h.facility_level_id = fl.id
GROUP BY s.id, s.name, o.name, fl.name;




CREATE OR REPLACE VIEW hospitals_count_by_ownership_level_state_column AS
SELECT
    s.id AS state_id,
    s.name AS state,
    SUM(CASE WHEN o.name = 'Public' AND fl.name = 'primary' THEN 1 ELSE 0 END) AS Pub_Primary,
    SUM(CASE WHEN o.name = 'Public' AND fl.name = 'secondary' THEN 1 ELSE 0 END) AS Pub_Secondary,
    SUM(CASE WHEN o.name = 'Public' AND fl.name = 'tertiary' THEN 1 ELSE 0 END) AS Pub_Tertiary,
    SUM(CASE WHEN o.name = 'Private' AND fl.name = 'primary' THEN 1 ELSE 0 END) AS Priv_Primary,
    SUM(CASE WHEN o.name = 'Private' AND fl.name = 'secondary' THEN 1 ELSE 0 END) AS Priv_Secondary,
    SUM(CASE WHEN o.name = 'Private' AND fl.name = 'tertiary' THEN 1 ELSE 0 END) AS Priv_Tertiary
FROM hs_hospitals_history h
JOIN ou_states s ON h.state_id = s.id
JOIN lst_ownerships o ON h.ownership_id = o.id
JOIN lst_level_of_care fl ON h.facility_level_id = fl.id
GROUP BY s.id, s.name;



CREATE OR REPLACE VIEW hospitals_count_by_ownership_lga AS
SELECT
    s.id AS state_id,
    s.name AS state,
    l.id AS lga_id,
    l.name AS lga,
    o.name AS ownership,
    COUNT(*) AS total
FROM hs_hospitals_history h
JOIN ou_lgas l ON h.lga_id = l.id
JOIN ou_states s ON l.state_id = s.id
JOIN lst_ownerships o ON h.ownership_id = o.id
GROUP BY s.id, s.name, l.id, l.name, o.name;



CREATE OR REPLACE VIEW hospitals_count_by_ownership_lga_column AS
SELECT
    s.id AS state_id,
    s.name AS state,
    l.id AS lga_id,
    l.name AS lga,
    SUM(CASE WHEN o.name = 'Private' THEN 1 ELSE 0 END) AS Private,
    SUM(CASE WHEN o.name = 'Public' THEN 1 ELSE 0 END) AS Public
FROM hs_hospitals_history h
JOIN ou_lgas l ON h.lga_id = l.id
JOIN ou_states s ON l.state_id = s.id
JOIN lst_ownerships o ON h.ownership_id = o.id
GROUP BY s.id, s.name, l.id, l.name;


CREATE OR REPLACE VIEW imaging_details AS
SELECT
    i.id,
    i.unique_id,
    i.registration_no,
    i.radiographers_reg_number,
    i.start_date,
    i.facility_name,
    i.alt_facility_name,
    s.id AS state_id,
    s.name AS state,
    l.id AS lga_id,
    l.name AS lga,
    w.id AS ward_id,
    w.name AS ward,
    o.id AS ownership_id,
    o.name AS ownership,
    ot.id AS ownership_type_id,
    ot.type AS ownership_type,
    i.ownership_details,
    i.house_no,
    i.street_name,
    i.longitude,
    i.latitude,
    i.postal_address,
    i.phone_number,
    i.email_address,
    i.website,
    i.operational_days,
    i.operational_hours,
    ops.id AS operational_status_id,
    ops.status AS operational_status,
    rs.id AS registration_status_id,
    rs.status AS registration_status,
    ls.id AS license_status_id,
    ls.status AS license_status,
    i.radiologists,
    i.radiographers,
    i.radiography_tech,
    pt.id AS premises_type_id,
    pt.name AS premises_type,
    i.created_at,
    i.updated_at
FROM im_imagings i
LEFT JOIN ou_states s ON i.state_id = s.id
LEFT JOIN ou_lgas l ON i.lga_id = l.id
LEFT JOIN ou_wards w ON i.ward_id = w.id
LEFT JOIN lst_ownerships o ON i.ownership_id = o.id
LEFT JOIN lst_ownership_types ot ON i.ownership_type_id = ot.id
LEFT JOIN lst_oparational_status ops ON i.operational_status_id = ops.id
LEFT JOIN lst_registration_status rs ON i.registration_status_id = rs.id
LEFT JOIN lst_license_status ls ON i.license_status_id = ls.id
LEFT JOIN lst_premises_type pt ON i.premises_type_id = pt.id;



CREATE OR REPLACE VIEW laboratory_count_by_level_lga AS
SELECT
    s.id AS state_id,
    s.name AS state,
    l.id AS lga_id,
    l.name AS lga,
    fl.name AS facility_level,
    COUNT(*) AS total
FROM lb_laboratories lab
JOIN ou_lgas l ON lab.lga_id = l.id
JOIN ou_states s ON l.state_id = s.id
JOIN lst_level_of_care fl ON lab.facility_level_id = fl.id
GROUP BY s.id, s.name, l.id, l.name, fl.name;



CREATE OR REPLACE VIEW laboratory_count_by_level_lga_column AS
SELECT
    s.id AS state_id,
    s.name AS state,
    l.id AS lga_id,
    l.name AS lga,
    SUM(CASE WHEN fl.name = 'primary' THEN 1 ELSE 0 END) AS `Primary`,
    SUM(CASE WHEN fl.name = 'secondary' THEN 1 ELSE 0 END) AS `Secondary`,
    SUM(CASE WHEN fl.name = 'tertiary' THEN 1 ELSE 0 END) AS `Tertiary`
FROM lb_laboratories lab
JOIN ou_lgas l ON lab.lga_id = l.id
JOIN ou_states s ON l.state_id = s.id
JOIN lst_level_of_care fl ON lab.facility_level_id = fl.id
GROUP BY s.id, s.name, l.id, l.name;


CREATE OR REPLACE VIEW laboratory_count_by_level_state AS
SELECT
    s.id AS state_id,
    s.name AS state,
    fl.name AS facility_level,
    COUNT(*) AS total
FROM lb_laboratories lab
JOIN ou_lgas l ON lab.lga_id = l.id
JOIN ou_states s ON l.state_id = s.id
JOIN lst_level_of_care fl ON lab.facility_level_id = fl.id
GROUP BY s.id, s.name, fl.name;




CREATE OR REPLACE VIEW laboratory_count_by_level_state_column AS
SELECT
    s.id AS state_id,
    s.name AS state,
    SUM(CASE WHEN fl.name = 'Primary' THEN 1 ELSE 0 END) AS `Primary`,
    SUM(CASE WHEN fl.name = 'Secondary' THEN 1 ELSE 0 END) AS `Secondary`,
    SUM(CASE WHEN fl.name = 'Tertiary' THEN 1 ELSE 0 END) AS `Tertiary`
FROM lb_laboratories lab
JOIN ou_lgas l ON lab.lga_id = l.id
JOIN ou_states s ON l.state_id = s.id
JOIN lst_level_of_care fl ON lab.facility_level_id = fl.id
GROUP BY s.id, s.name;



CREATE OR REPLACE VIEW laboratory_count_by_ownership_lga AS
SELECT
    s.id AS state_id,
    s.name AS state,
    l.id AS lga_id,
    l.name AS lga,
    o.name AS ownership,
    COUNT(*) AS total
FROM lb_laboratories lab
JOIN ou_lgas l ON lab.lga_id = l.id
JOIN ou_states s ON l.state_id = s.id
JOIN lst_ownerships o ON lab.ownership_id = o.id
GROUP BY s.id, s.name, l.id, l.name, o.name;


CREATE OR REPLACE VIEW laboratory_count_by_ownership_lga_column AS
SELECT
    s.id AS state_id,
    s.name AS state,
    l.id AS lga_id,
    l.name AS lga,
    SUM(CASE WHEN o.name = 'Public' THEN 1 ELSE 0 END) AS Public,
    SUM(CASE WHEN o.name = 'Private' THEN 1 ELSE 0 END) AS Private
FROM lb_laboratories lab
JOIN ou_lgas l ON lab.lga_id = l.id
JOIN ou_states s ON l.state_id = s.id
JOIN lst_ownerships o ON lab.ownership_id = o.id
GROUP BY s.id, s.name, l.id, l.name;



CREATE OR REPLACE VIEW laboratory_count_by_ownership_state AS
SELECT
    s.id AS state_id,
    s.name AS state,
    o.name AS ownership,
    COUNT(*) AS total
FROM lb_laboratories lab
JOIN ou_states s ON lab.state_id = s.id
JOIN lst_ownerships o ON lab.ownership_id = o.id
GROUP BY s.id, s.name, o.name;


CREATE OR REPLACE VIEW laboratory_details AS
SELECT
    lab.id,
    lab.unique_id,
    lab.registration_no,
    lab.medical_laboratory_number,
    lab.start_date,
    lab.facility_name,
    lab.alt_facility_name,
    s.id AS state_id,
    s.name AS state,
    l.id AS lga_id,
    l.name AS lga,
    w.id AS ward_id,
    w.name AS ward,
    o.id AS ownership_id,
    o.name AS ownership,
    ot.id AS ownership_type_id,
    ot.type AS ownership_type,
    lab.ownership_details,
    fl.id AS facility_level_id,
    fl.name AS facility_level,
    lab.house_no,
    lab.street_name,
    lab.longitude,
    lab.latitude,
    lab.postal_address,
    lab.phone_number,
    lab.email_address,
    lab.website,
    lab.operational_days,
    lab.operational_hours,
    os.id AS operational_status_id,
    os.status AS operation_status,
    rs.id AS registration_status_id,
    rs.status AS registration_status,
    ac.id AS accreditation_status_id,
    ac.status AS accreditation_status,
    ls.id AS license_status_id,
    ls.status AS license_status,
    lab.laboratory_scientists,
    lab.laboratory_technicians,
    lab.quality_assurance,
    pt.id AS premises_type_id,
    pt.name AS premises_type,
    lab.created_at,
    lab.updated_at
FROM lb_laboratories lab
LEFT JOIN ou_states s ON lab.state_id = s.id
LEFT JOIN ou_lgas l ON lab.lga_id = l.id
LEFT JOIN ou_wards w ON lab.ward_id = w.id
LEFT JOIN lst_ownerships o ON lab.ownership_id = o.id
LEFT JOIN lst_ownership_types ot ON lab.ownership_type_id = ot.id
LEFT JOIN lst_level_of_care fl ON lab.facility_level_id = fl.id
LEFT JOIN lst_oparational_status os ON lab.operational_status_id = os.id
LEFT JOIN lst_registration_status rs ON lab.registration_status_id = rs.id
LEFT JOIN lst_accreditation_status ac ON lab.accreditation_status_id = ac.id
LEFT JOIN lst_license_status ls ON lab.license_status_id = ls.id
LEFT JOIN lst_premises_type pt ON lab.premises_type_id = pt.id;



CREATE OR REPLACE VIEW pharmacy_count_by_ownership_lga AS
SELECT
    s.id AS state_id,
    s.name AS state,
    l.id AS lga_id,
    l.name AS lga,
    o.name AS ownership,
    COUNT(*) AS total
FROM pharmacies p
JOIN ou_lgas l ON p.lga_id = l.id
JOIN ou_states s ON l.state_id = s.id
JOIN lst_ownerships o ON p.ownership_id = o.id
GROUP BY s.id, s.name, l.id, l.name, o.name;


CREATE OR REPLACE VIEW pharmacy_count_by_ownership_lga_column AS
SELECT
    s.id AS state_id,
    s.name AS state,
    l.id AS lga_id,
    l.name AS lga,
    SUM(CASE WHEN o.name = 'Public' THEN 1 ELSE 0 END) AS Public,
    SUM(CASE WHEN o.name = 'Private' THEN 1 ELSE 0 END) AS Private
FROM pharmacies p
JOIN ou_lgas l ON p.lga_id = l.id
JOIN ou_states s ON l.state_id = s.id
JOIN lst_ownerships o ON p.ownership_id = o.id
GROUP BY s.id, s.name, l.id, l.name;



CREATE OR REPLACE VIEW pharmacy_count_by_ownership_state AS
SELECT
    s.id AS state_id,
    s.name AS state,
    o.name AS ownership,
    COUNT(*) AS total
FROM pharmacies p
JOIN ou_lgas l ON p.lga_id = l.id
JOIN ou_states s ON l.state_id = s.id
JOIN lst_ownerships o ON p.ownership_id = o.id
GROUP BY s.id, s.name, o.name;



CREATE OR REPLACE VIEW pharmacy_count_by_ownership_state_column AS
SELECT
    s.id AS state_id,
    s.name AS state,
    SUM(CASE WHEN o.name = 'Public' THEN 1 ELSE 0 END) AS Public,
    SUM(CASE WHEN o.name = 'Private' THEN 1 ELSE 0 END) AS Private
FROM pharmacies p
JOIN ou_lgas l ON p.lga_id = l.id
JOIN ou_states s ON l.state_id = s.id
JOIN lst_ownerships o ON p.ownership_id = o.id
GROUP BY s.id, s.name;



CREATE OR REPLACE VIEW pharmacy_details AS
SELECT
    p.id,
    p.unique_id,
    p.registration_no,
    p.pharmacists_reg_number,
    p.start_date,
    p.facility_name,
    p.alt_facility_name,
    s.id AS state_id,
    s.name AS state,
    l.id AS lga_id,
    l.name AS lga,
    w.id AS ward_id,
    w.name AS ward,
    o.id AS ownership_id,
    o.name AS ownership,
    ot.id AS ownership_type_id,
    ot.type AS ownership_type,
    p.ownership_details,
    p.house_no,
    p.street_name,
    p.longitude,
    p.latitude,
    p.postal_address,
    p.phone_number,
    p.email_address,
    p.website,
    p.operational_days,
    p.operational_hours,
    os.id AS operational_status_id,
    os.status AS operational_status,
    rs.id AS registration_status_id,
    rs.status AS registration_status,
    ls.id AS license_status_id,
    ls.status AS license_status,
    pt.id AS premises_type_id,
    pt.name AS premises_type,
    oc.id AS outlet_category_id,
    oc.name AS outlet_category,
    p.pharmacists,
    p.pharmacy_technicians,
    p.created_at,
    p.updated_at
FROM pharmacies p
LEFT JOIN ou_lgas l ON p.lga_id = l.id
LEFT JOIN ou_states s ON l.state_id = s.id
LEFT JOIN ou_wards w ON p.ward_id = w.id
LEFT JOIN lst_ownerships o ON p.ownership_id = o.id
LEFT JOIN lst_ownership_types ot ON p.ownership_type_id = ot.id
LEFT JOIN lst_oparational_status os ON p.operational_status_id = os.id
LEFT JOIN lst_registration_status rs ON p.registration_status_id = rs.id
LEFT JOIN lst_license_status ls ON p.license_status_id = ls.id
LEFT JOIN lst_premises_type pt ON p.premises_type_id = pt.id
LEFT JOIN lst_outlet_category oc ON p.outlet_category_id = oc.id;



CREATE OR REPLACE VIEW dhis_log_details AS
SELECT
    id,
    request_type,
    hfr_id,
    dhis_uid,
    facility_status,
    ownership_status,
    level_status,
    level_option_status,
    error_details,
    created_at,
    updated_at,
    user_id
FROM dhis_log;




INSERT INTO model_has_roles (role_id, model_type, model_id)
VALUES (1, 'App\\Models\\User', 5);




-- Remaining one by adams
SHOW CREATE VIEW population_by_state;
SHOW CREATE VIEW signature_domain_completenes;

