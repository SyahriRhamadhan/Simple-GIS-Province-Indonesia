<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta Wilayah Indonesia</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <style>
        #map {
            height: 900px;
            width: 100%;
        }
    </style>
</head>

<body>
    <h2>Peta Batas Wilayah Provinsi Indonesia</h2>
    <div id="map"></div>

    <script>
        // Inisialisasi peta
        var map = L.map('map').setView([-2.5489, 118.0149], 5);

        // Layer OSM
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Ambil data GeoJSON dari API
        fetch("/api/provinsi")
            .then(response => response.json())
            .then(data => {
                data.forEach(provinsi => {
                    // Parse geometry yang tersimpan dalam bentuk JSON
                    L.geoJSON(JSON.parse(provinsi.geojson), {
                        style: {
                            color: "blue",
                            weight: 2
                        }
                    }).bindPopup(provinsi.nama).addTo(map);
                });
            });
    </script>
</body>

</html>
