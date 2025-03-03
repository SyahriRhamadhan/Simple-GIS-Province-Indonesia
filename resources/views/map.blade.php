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

        function getRandomColor() {
            return '#' + Math.floor(Math.random() * 16777215).toString(16);
        }

        // Layer OSM
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Ambil data GeoJSON dari API
        fetch("/api/provinsi")
            .then(response => response.json())
            .then(data => {
                data.forEach(provinsi => {
                    let featureData = JSON.parse(provinsi.geojson);
                    let color = getRandomColor();
                    L.geoJSON(featureData, {
                        style: {
                            color: color,
                            weight: 2
                        },
                        onEachFeature: function(feature, layer) {
                            // Ambil semua property yang diinginkan dari feature.properties
                            let props = feature.properties;
                            let popupContent = `
            <b>Nama:</b> ${props.NAME_1 ?? 'N/A'}<br>
            <b>Kode:</b> ${props.kode ?? 'N/A'}<br>
            <b>Propinsi:</b> ${props.Propinsi ?? 'N/A'}<br>
            <b>Sumber:</b> ${props.SUMBER ?? 'N/A'}`;
                            layer.bindPopup(popupContent);
                        }
                    }).addTo(map);
                });
            })
            .catch(error => console.error("Error:", error));
    </script>
</body>

</html>
