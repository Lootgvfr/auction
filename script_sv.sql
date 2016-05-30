INSERT INTO category (name) VALUES 
('TVs'),
('Routers'),
('Fridges'),
('Microwaves');

INSERT INTO property (category_id, p_range, example, name) VALUES
(3, '', '','Producer'),
(3, '', '','Screen size'),
(3, '', '','Screen resolution'),
(3, '', '','Screen type'),
(4, '', '','Producer'),
(4, '', '','LAN speed'),
(4, '', '','Wi-Fi speed'),
(4, '', '','Wi-Fi frequency'),
(4, '', '','LAN ports amount'),
(4, '', '','WAN ports amount'),
(5, '', '','Producer'),
(5, '', '','Size'),
(5, '', '','Volume'),
(5, '', '','Sections amount'),
(6, '', '','Producer'),
(6, '', '','Size'),
(6, '', '','Volume'),
(6, '', '','Power'),
(6, '', '','Power levels');

UPDATE property SET name='Producer' WHERE name='Manufacturer';
UPDATE property SET is_nullable=false WHERE name='Producer';
UPDATE property SET example='10.0", 4.7"', is_nullable=false WHERE name='Screen size';
UPDATE property SET example='1920x1080, 1920 1080' WHERE name='Screen resolution';
UPDATE property SET example='2 GB, 1024 MB' WHERE name='RAM Size';
UPDATE property SET example='1 TB, 500 GB' WHERE name='HDD Size' OR name='Memory size';
UPDATE property SET example='1.0 kg, 100 g' WHERE name='Weight';
UPDATE property SET example='2' WHERE name='Processor cores' OR name='WAN ports amount' OR name='LAN ports amount' OR name='Sections amount' OR name='Power levels';
UPDATE property SET example='2.0 GHz, 3.5 GHz' WHERE name='Processor frequency' OR name='Wi-Fi frequency';
UPDATE property SET example='5000 mAh' WHERE name='Battery capacity';
UPDATE property SET name='Operating system', example='Windows 8.1, Android 5.0', is_nullable=false WHERE name='Operating System' OR name='Operating system';
UPDATE property SET example='5.0 MP, 13.5 MP' WHERE name='Camera resolution';
UPDATE property SET example='5.5 Mbit/s, 100.0 Mbit/s' WHERE name='LAN speed' OR name='Wi-Fi speed';
UPDATE property SET example='20 L, 150 L' WHERE name='Volume';
UPDATE property SET example='140x50x50 cm, 140 50 50 cm', is_nullable=false WHERE name='Size';
UPDATE property SET example='500W, 1200W' WHERE name='Power';
UPDATE property SET example='Samsung' WHERE name='Producer';
UPDATE property SET example='LCD, IPS' WHERE name='Screen type';
UPDATE property SET example='GeForce 960 GTX 2 GB' WHERE name='Video card';
UPDATE property SET name='Processor producer', example='Intel' WHERE name='Processor producer' OR name='Processor manufacturer';
UPDATE property SET example='Core i5 5570' WHERE name='Processor model';

INSERT INTO regular (property_id, name, expr) VALUES 
((SELECT id FROM property WHERE name='Screen size' AND category_id=1), 'Screen size', '%^[0-9]+.[0-9]+"$%'),
((SELECT id FROM property WHERE name='Screen resolution' AND category_id=1), 'Screen resolution', '%^[0-9]+x[0-9]+$%'),
((SELECT id FROM property WHERE name='Screen resolution' AND category_id=1), 'Screen resolution', '%^[0-9]+ [0-9]+$%'),
((SELECT id FROM property WHERE name='RAM size' AND category_id=1), 'RAM size', '%^[0-9]+ GB$%'),
((SELECT id FROM property WHERE name='RAM size' AND category_id=1), 'RAM size', '%^[0-9]+ MB$%'),
((SELECT id FROM property WHERE name='HDD size' AND category_id=1), 'HDD size', '%^[0-9]+ TB$%'),
((SELECT id FROM property WHERE name='HDD size' AND category_id=1), 'HDD size', '%^[0-9]+ GB$%'),
((SELECT id FROM property WHERE name='Weight' AND category_id=1), 'Weight', '%^[0-9]+\.[0-9]+ kg$%'),
((SELECT id FROM property WHERE name='Weight' AND category_id=1), 'Weight', '%^[0-9]+ g$%'),
((SELECT id FROM property WHERE name='Processor cores' AND category_id=1), 'Processor cores', '%^[0-9]+$%'),
((SELECT id FROM property WHERE name='Processor frequency' AND category_id=1), 'Processor frequency', '%^[0-9]+\.[0-9]+ GHz$%'),
((SELECT id FROM property WHERE name='Battery capacity' AND category_id=1), 'Battery capacity', '%^[0-9]+ mAh$%'),
((SELECT id FROM property WHERE name='Screen size' AND category_id=2), 'Screen size', '%^[0-9]+.[0-9]+"$%'),
((SELECT id FROM property WHERE name='Screen resolution' AND category_id=2), 'Screen resolution', '%^[0-9]+x[0-9]+$%'),
((SELECT id FROM property WHERE name='Screen resolution' AND category_id=2), 'Screen resolution', '%^[0-9]+ [0-9]+$%'),
((SELECT id FROM property WHERE name='RAM size' AND category_id=2), 'RAM size', '%^[0-9]+ GB$%'),
((SELECT id FROM property WHERE name='RAM size' AND category_id=2), 'RAM size', '%^[0-9]+ MB$%'),
((SELECT id FROM property WHERE name='Memory size' AND category_id=2), 'Memory size', '%^[0-9]+ TB$%'),
((SELECT id FROM property WHERE name='Memory size' AND category_id=2), 'Memory size', '%^[0-9]+ GB$%'),
((SELECT id FROM property WHERE name='Weight' AND category_id=2), 'Weight', '%^[0-9]+\.[0-9]+ kg$%'),
((SELECT id FROM property WHERE name='Weight' AND category_id=2), 'Weight', '%^[0-9]+ g$%'),
((SELECT id FROM property WHERE name='Processor cores' AND category_id=2), 'Processor cores', '%^[0-9]+$%'),
((SELECT id FROM property WHERE name='Processor frequency' AND category_id=2), 'Processor frequency', '%^[0-9]+\.[0-9]+ GHz$%'),
((SELECT id FROM property WHERE name='Battery capacity' AND category_id=2), 'Battery capacity', '%^[0-9]+ mAh$%'),
((SELECT id FROM property WHERE name='Camera resolution' AND category_id=2), 'Camera resolution', '%^[0-9]+\.[0-9]+ MP$%'),
((SELECT id FROM property WHERE name='Screen size' AND category_id=3), 'Screen size', '%^[0-9]+.[0-9]+"$%'),
((SELECT id FROM property WHERE name='Screen resolution' AND category_id=3), 'Screen resolution', '%^[0-9]+x[0-9]+$%'),
((SELECT id FROM property WHERE name='Screen resolution' AND category_id=3), 'Screen resolution', '%^[0-9]+ [0-9]+$%'),
((SELECT id FROM property WHERE name='LAN speed' AND category_id=4), 'LAN speed', '%^[0-9]+.[0-9]+ Mbit/s$%'),
((SELECT id FROM property WHERE name='Wi-Fi speed' AND category_id=4), 'Wi-Fi speed', '%^[0-9]+.[0-9]+ Mbit/s$%'),
((SELECT id FROM property WHERE name='LAN ports amount' AND category_id=4), 'LAN ports amount', '%^[0-9]+$%'),
((SELECT id FROM property WHERE name='WAN ports amount' AND category_id=4), 'WAN ports amount', '%^[0-9]+$%'),
((SELECT id FROM property WHERE name='Wi-Fi frequency' AND category_id=4), 'Wi-Fi frequency', '%^[0-9]+\.[0-9]+ GHz$%'),
((SELECT id FROM property WHERE name='Sections amount' AND category_id=5), 'Sections amount', '%^[0-9]+$%'),
((SELECT id FROM property WHERE name='Size' AND category_id=5), 'Size', '%^[0-9]+x[0-9]+x[0-9]+ cm$%'),
((SELECT id FROM property WHERE name='Size' AND category_id=5), 'Size', '%^[0-9]+ [0-9]+ [0-9]+ cm$%'),
((SELECT id FROM property WHERE name='Volume' AND category_id=5), 'Volume', '%^[0-9]+ L$%'),
((SELECT id FROM property WHERE name='Power levels' AND category_id=6), 'Power levels', '%^[0-9]+$%'),
((SELECT id FROM property WHERE name='Size' AND category_id=6), 'Size', '%^[0-9]+x[0-9]+x[0-9]+ cm$%'),
((SELECT id FROM property WHERE name='Size' AND category_id=6), 'Size', '%^[0-9]+ [0-9]+ [0-9]+ cm$%'),
((SELECT id FROM property WHERE name='Volume' AND category_id=6), 'Volume', '%^[0-9]+ L$%'),
((SELECT id FROM property WHERE name='Power' AND category_id=6), 'Power', '%^[0-9]+W$%');

INSERT INTO currency (name, course) VALUES ('USD', 1), ('UAH', 25), ('RUB', 66);

UPDATE lot SET currency_id=(SELECT id FROM currency WHERE name='UAH');