INSERT INTO party_type (description) VALUES
('Person'),
('Organization'),
('Government Entity'),
('Non-Profit');

INSERT INTO party (id) VALUES
(1), (2), (3), (4), (5); -- IDs will be auto-generated; these are placeholders


INSERT INTO party_classification (fromdate, thrudate, party_id, party_type_id) VALUES
('2023-01-01', NULL, 1, 1), -- Person
('2023-01-01', NULL, 2, 2), -- Organization
('2022-06-01', '2023-05-31', 3, 2), -- Organization (expired)
('2024-01-01', NULL, 4, 3), -- Government Entity
('2023-03-01', NULL, 5, 4); -- Non-Profit


INSERT INTO organization_classification (id) VALUES
(1), (2), (3), (4), (5);

INSERT INTO minority_type (name_en, name_th) VALUES
('Women-Owned', 'เป็นเจ้าของโดยผู้หญิง'),
('Veteran-Owned', 'เป็นเจ้าของโดยทหารผ่านศึก'),
('Minority-Owned', 'เป็นเจ้าของโดยชนกลุ่มน้อย'),
('Disabled-Owned', 'เป็นเจ้าของโดยผู้พิการ');

INSERT INTO industry_type (naics_code, description) VALUES
('541511', 'Custom Software Development'),
('722511', 'Full-Service Restaurants'),
('621111', 'Physician Offices'),
('111998', 'Organic Farming');

INSERT INTO employee_count_range (description) VALUES
('1-10'),
('11-50'),
('51-200'),
('201-1000');

INSERT INTO classify_by_minority (id, minority_type_id) VALUES
(1, 1), -- Women-Owned
(2, 2); -- Veteran-Owned

INSERT INTO classify_by_industry (id, industry_type_id) VALUES
(3, 1), -- Software Development
(4, 2); -- Restaurants

INSERT INTO classify_by_size (id, employee_count_range_id) VALUES
(5, 1); -- 1-10 employees

INSERT INTO person (id, socialsecuritynumber, birthdate, mothermaidenname, totalyearworkexperience, comment) VALUES
(1, '123-45-6789', '1990-05-15', 'Smith', 10, 'Software Engineer'),
(3, '987-65-4321', '1985-09-22', 'Johnson', 15, 'Manager');

INSERT INTO person_classification (id) VALUES
(1), (2);

INSERT INTO ethnicity (name_en, name_th) VALUES
('Asian', 'เอเชีย'),
('Caucasian', 'คอเคเซียน'),
('Hispanic', 'ฮิสแปนิก'),
('African', 'แอฟริกัน');

INSERT INTO income_range (description) VALUES
('$0-$50K'),
('$50K-$100K'),
('$100K-$200K'),
('$200K+');

INSERT INTO classify_by_eeoc (id, ethnicity_id) VALUES
(1, 1); -- Asian

INSERT INTO classify_by_income (id, income_range_id) VALUES
(2, 2); -- $50K-$100K

INSERT INTO maritalstatustype (description) VALUES
('Single'),
('Married'),
('Divorced'),
('Widowed');

INSERT INTO maritalstatus (fromdate, thrudate, person_id, maritalstatustype_id) VALUES
('2015-01-01', NULL, 1, 2), -- Married
('2010-01-01', '2018-12-31', 3, 2); -- Married, then divorced

INSERT INTO physicalcharacteristictype (description) VALUES
('Height'),
('Weight'),
('Eye Color'),
('Hair Color');

INSERT INTO physicalcharacteristic (fromdate, thrudate, person_id, physicalcharacteristictype_id) VALUES
('2023-01-01', NULL, 1, 1), -- Height
('2023-01-01', NULL, 1, 2); -- Weight

INSERT INTO personnametype (description) VALUES
('Legal Name'),
('Nickname'),
('Maiden Name'),
('Professional Name');

INSERT INTO personname (fromdate, thrudate, person_id, personnametype_id) VALUES
('1990-05-15', NULL, 1, 1), -- Legal Name
('2010-01-01', NULL, 1, 2); -- Nickname

INSERT INTO country (isocode, name_en, name_th) VALUES
('US', 'United States', 'สหรัฐอเมริกา'),
('TH', 'Thailand', 'ประเทศไทย'),
('JP', 'Japan', 'ญี่ปุ่น'),
('UK', 'United Kingdom', 'สหราชอาณาจักร');

INSERT INTO citizenship (fromdate, thrudate, person_id, country_id) VALUES
('1990-05-15', NULL, 1, 2), -- Thai citizenship
('1985-09-22', NULL, 3, 1); -- US citizenship

INSERT INTO passport (passportnumber, fromdate, thrudate, citizenship_id) VALUES
('TH1234567', '2020-01-01', '2030-01-01', 1),
('US9876543', '2019-06-01', '2029-06-01', 2);

INSERT INTO organization (id, name_en, name_th) VALUES
(2, 'TechCorp', 'เทคคอร์ป'),
(4, 'GovAgency', 'หน่วยงานรัฐ');

INSERT INTO legal_organization (id, federal_tax_id_number) VALUES
(2, '12-3456789');

INSERT INTO informal_organization (id) VALUES
(5); -- Non-Profit from earlier