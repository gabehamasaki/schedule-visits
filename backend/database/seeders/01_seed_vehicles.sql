INSERT INTO
    vehicles (
        brand,
        model,
        version,
        price,
        location,
        image_url
    )
VALUES (
        'Porsche',
        '911',
        '3.0 CARRERA 6 CILINDROS',
        850000.00,
        'São Paulo - SP',
        'https://commons.wikimedia.org/wiki/Special:FilePath/Porsche%20992%20Carrera%20S%20coupe%20IMG%205838.jpg'
    ),
    (
        'BMW',
        'X5',
        '3.0 4X4 XDRIVE M SPORT',
        520000.00,
        'Barueri - SP',
        'https://commons.wikimedia.org/wiki/Special:FilePath/BMW%20X5%20%28G05%29%20China.jpg'
    ),
    (
        'Toyota',
        'Corolla Cross',
        '2.0 VVT-IE XRX DIRECT SHIFT',
        175000.00,
        'Campinas - SP',
        'https://commons.wikimedia.org/wiki/Special:FilePath/Toyota%20Corolla%20Cross%20Hybrid%201X7A1861.jpg'
    ),
    (
        'Honda',
        'Civic',
        '2.0 16V FLEXONE TOURING CVT',
        155000.00,
        'São Paulo - SP',
        'https://commons.wikimedia.org/wiki/Special:FilePath/2022%20Honda%20Civic%20Touring%20in%20Lunar%20Silver%20Metallic%2C%20Front%20Left%2C%2005-10-2022.jpg'
    ),
    (
        'Jeep',
        'Compass',
        '1.3 T270 TURBO LONGITUDE',
        165000.00,
        'Osasco - SP',
        'https://commons.wikimedia.org/wiki/Special:FilePath/Jeep%20Compass%20MK%20facelift%20Shishi%2002%202022-03-03.jpg'
    ),
    (
        'Volkswagen',
        'T-Cross',
        '1.4 250 TSI HIGHLINE',
        128000.00,
        'Guarulhos - SP',
        'https://commons.wikimedia.org/wiki/Special:FilePath/Volkswagen%20T-Cross%201X7A0366.jpg'
    ),
    (
        'Fiat',
        'Pulse',
        '1.0 TURBO 200 IMPETUS',
        98000.00,
        'Santo André - SP',
        'https://commons.wikimedia.org/wiki/Special:FilePath/2023%20Fiat%20Pulse%20Impetus%20%28Colombia%29%20front%20view%2001.jpg'
    ),
    (
        'Hyundai',
        'Creta',
        '1.0 TGDI PLATINUM',
        138000.00,
        'Ribeirão Preto - SP',
        'https://commons.wikimedia.org/wiki/Special:FilePath/2022%20Hyundai%20Creta%20SE.jpg'
    ) ON CONFLICT (brand, model, version) DO NOTHING;
