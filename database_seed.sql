INSERT INTO products (name, brand, size, resolution, panel, is_curved, price, image, description)
SELECT 'ASUS ProArt PA248QFV 24 inch', 'ASUS', 24, 'FHD', 'IPS', 0, 4290000,
       'uploads/products/1770142714_asus_pa248qfv_gearvn_5f41282df87344cfb09d4e24b30a8d35_master.jpg',
       'Color accurate monitor for study, office, and creative work.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'ASUS ProArt PA248QFV 24 inch');

INSERT INTO products (name, brand, size, resolution, panel, is_curved, price, image, description)
SELECT 'ASUS VY279HGR 27 inch', 'ASUS', 27, 'FHD', 'IPS', 0, 3690000,
       'uploads/products/1770142805_asus_vy279hgr_120hz_gearvn_82956fa8030e4a16a9bbfdcbdd07bebe_master.jpg',
       'Smooth everyday monitor with bright colors and a clean design.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'ASUS VY279HGR 27 inch');

INSERT INTO products (name, brand, size, resolution, panel, is_curved, price, image, description)
SELECT 'ASUS ProArt PA248CRV 24 inch', 'ASUS', 24, 'FHD', 'IPS', 0, 5590000,
       'uploads/products/1770142841_man-hinh-asus-proart-pa248crv-24-inch-1.png_f5f89eeec36f4ca98e12a1b4fae08ace_master.jpg',
       'Professional display for designers and productivity setups.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'ASUS ProArt PA248CRV 24 inch');

INSERT INTO products (name, brand, size, resolution, panel, is_curved, price, image, description)
SELECT 'ASUS ProArt PA27CGRV 27 inch', 'ASUS', 27, '2K', 'IPS', 0, 8990000,
       'uploads/products/1770142950_asus_pa27cgrv_gearvn_c83dfe4e85b54880a207cc6feab32eec_master.jpg',
       'Sharp 2K display for workstations and content creation.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'ASUS ProArt PA27CGRV 27 inch');

INSERT INTO products (name, brand, size, resolution, panel, is_curved, price, image, description)
SELECT 'ASUS ProArt PA278QEV 27 inch', 'ASUS', 27, '2K', 'IPS', 0, 6490000,
       'uploads/products/1770143170_asus_pa278qev_gearvn_3a45574fb7be41e48b83d4dbe9dc68fb_master.jpg',
       'Balanced 2K IPS monitor for school, office, and editing.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'ASUS ProArt PA278QEV 27 inch');

INSERT INTO products (name, brand, size, resolution, panel, is_curved, price, image, description)
SELECT 'ASUS ROG Strix XG32UQ 32 inch', 'ASUS', 32, '4K', 'IPS', 0, 18990000,
       'uploads/products/1770143305_asus_xg32uq_gearvn_45c4567f4263401abed634921db41811_master.jpg',
       'Large 4K gaming monitor with premium color and fast response.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'ASUS ROG Strix XG32UQ 32 inch');

INSERT INTO products (name, brand, size, resolution, panel, is_curved, price, image, description)
SELECT 'ASUS ROG XG27ACMEG 27 inch', 'ASUS', 27, '2K', 'IPS', 0, 7990000,
       'uploads/products/1770144216_asus_xg27acmeg-g_hatsune_miku_gearvn_cff7848d64cf4f3ea95b307f2261bca6_master.jpg',
       'Gaming monitor with responsive motion and strong color coverage.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'ASUS ROG XG27ACMEG 27 inch');

INSERT INTO products (name, brand, size, resolution, panel, is_curved, price, image, description)
SELECT 'ASUS ROG PG27AQWP-W 27 inch', 'ASUS', 27, '2K', 'OLED', 0, 24990000,
       'uploads/products/1770144249_asus_pg27aqwp-w_gearvn_ffffe2baa33b4f9092cbe4b7c94a4399_master.jpg',
       'OLED gaming display with deep contrast and high refresh rate.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'ASUS ROG PG27AQWP-W 27 inch');

INSERT INTO products (name, brand, size, resolution, panel, is_curved, price, image, description)
SELECT 'ASUS ProArt PA278QGV 27 inch', 'ASUS', 27, '2K', 'IPS', 0, 7290000,
       'uploads/products/1770144293_asus_pa278qgv_gearvn_0a8ed7a3ef624d4087fa89f0e4fddd50_master.jpg',
       'Reliable 2K monitor for productivity and image work.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'ASUS ProArt PA278QGV 27 inch');

INSERT INTO products (name, brand, size, resolution, panel, is_curved, price, image, description)
SELECT 'ASUS VZ279HG 27 inch', 'ASUS', 27, 'FHD', 'IPS', 0, 3290000,
       'uploads/products/1770144332_asus_vz279hg_gearvn_7bfb0cb6dc9f44f69f79bfaed7dcbcb7_master.jpg',
       'Slim monitor for daily work, browsing, and entertainment.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'ASUS VZ279HG 27 inch');
