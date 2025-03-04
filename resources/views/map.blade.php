<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta Wilayah & CRUD Provinsi</title>
    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <style>
        #map {
            height: 500px;
            width: 100%;
        }

        .crud-section {
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        form {
            margin-top: 20px;
        }

        input,
        textarea,
        button {
            padding: 8px;
            margin: 4px 0;
            width: 100%;
        }

        button {
            cursor: pointer;
        }
    </style>
</head>

<body>
    <h2>Peta Batas Wilayah Provinsi Indonesia</h2>
    <div id="map"></div>

    <hr>

    <div class="crud-section">
        <h2>CRUD Data Provinsi</h2>
        <button id="refreshBtn">Refresh Data</button>
        <table id="provinsiTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data provinsi akan ditampilkan di sini -->
            </tbody>
        </table>

        <h3 id="formTitle">Tambah Provinsi</h3>
        <form id="provinsiForm">
            <!-- Input tersembunyi untuk menyimpan ID saat edit -->
            <input type="hidden" id="provinsiId" value="">
            <div>
                <label>Nama:</label>
                <input type="text" id="nama" placeholder="Masukkan nama provinsi" required>
            </div>
            <div>
                <label>GeoJSON:</label>
                <textarea id="geojson" rows="5" placeholder='Masukkan data GeoJSON (misal: {"type":"MultiPolygon", ...})'
                    required></textarea>
            </div>
            <div>
                <button type="submit" id="submitBtn">Simpan</button>
                <button type="button" id="cancelBtn" style="display:none;">Batal</button>
            </div>
        </form>
    </div>

    <script>
        // Inisialisasi peta dengan Leaflet
        var map = L.map('map').setView([-2.5489, 118.0149], 5);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Fungsi untuk mendapatkan warna acak
        function getRandomColor() {
            return '#' + Math.floor(Math.random() * 16777215).toString(16);
        }

        // Fungsi untuk memuat data provinsi ke dalam peta
        function loadProvinsiOnMap() {
            // Hapus layer overlay sebelumnya (jika ada)
            map.eachLayer(function(layer) {
                if (layer.options && layer.options.pane === "overlayPane") {
                    map.removeLayer(layer);
                }
            });
            // Ambil data dari API
            fetch(apiUrl)
                .then(response => response.json())
                .then(data => {
                    data.forEach(provinsi => {
                        let featureData;
                        try {
                            featureData = JSON.parse(provinsi.geojson);
                        } catch (e) {
                            console.error("GeoJSON tidak valid untuk provinsi ID: " + provinsi.id);
                            return;
                        }
                        let color = getRandomColor();
                        L.geoJSON(featureData, {
                            style: {
                                color: color,
                                weight: 1
                            },
                            onEachFeature: function(feature, layer) {
                                let props = feature.properties || {};
                                let popupContent = `
                      <b>Nama:</b> ${props.NAME_1 ?? provinsi.nama ?? 'N/A'}<br>
                      <b>Kode:</b> ${props.kode ?? 'N/A'}<br>
                      <b>Propinsi:</b> ${props.Propinsi ?? 'N/A'}<br>
                      <b>Sumber:</b> ${props.SUMBER ?? 'N/A'}`;
                                layer.bindPopup(popupContent);
                            }
                        }).addTo(map);
                    });
                })
                .catch(error => console.error("Error loading map data:", error));
        }

        // CRUD Front-End
        const apiUrl = '/api/provinsi';

        // Fungsi untuk memuat data provinsi ke tabel
        function loadProvinsiTable() {
            fetch(apiUrl)
                .then(response => response.json())
                .then(data => {
                    const tbody = document.querySelector('#provinsiTable tbody');
                    tbody.innerHTML = '';
                    data.forEach(provinsi => {
                        let tr = document.createElement('tr');
                        tr.innerHTML = `
                  <td>${provinsi.id}</td>
                  <td>${provinsi.nama}</td>
                  <td>
                    <button onclick="editProvinsi(${provinsi.id})">Edit</button>
                    <button onclick="deleteProvinsi(${provinsi.id})">Delete</button>
                  </td>
                `;
                        tbody.appendChild(tr);
                    });
                })
                .catch(error => console.error("Error loading table data:", error));
        }

        // Refresh data saat tombol "Refresh Data" diklik
        document.getElementById('refreshBtn').addEventListener('click', () => {
            loadProvinsiTable();
            loadProvinsiOnMap();
        });

        // Handle form submission (create/update)
        document.getElementById('provinsiForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('provinsiId').value;
            const nama = document.getElementById('nama').value;
            const geojson = document.getElementById('geojson').value;

            // Validasi GeoJSON (opsional)
            try {
                JSON.parse(geojson);
            } catch (e) {
                alert("GeoJSON tidak valid. Pastikan formatnya benar.");
                return;
            }

            const payload = {
                nama,
                geojson
            };

            if (id) {
                // Update data (PUT)
                fetch(apiUrl + '/' + id, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(response => response.json())
                    .then(data => {
                        resetForm();
                        loadProvinsiTable();
                        loadProvinsiOnMap();
                    })
                    .catch(error => console.error("Error updating provinsi:", error));
            } else {
                // Create data baru (POST)
                fetch(apiUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => {
                                throw err;
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        resetForm();
                        loadProvinsiTable();
                        loadProvinsiOnMap();
                    })
                    .catch(error => {
                        console.error("Error creating provinsi:", error);
                        alert("Gagal menambahkan data: " + JSON.stringify(error.errors));
                    });
            }
        });

        // Fungsi untuk mengisi form dengan data yang akan diedit
        function editProvinsi(id) {
            fetch(apiUrl + '/' + id)
                .then(response => response.json())
                .then(result => {
                    console.log('Response untuk ID ' + id, result);
                    if (result.success) {
                        // Gunakan properti data dari respons
                        const prov = result.data;
                        document.getElementById('provinsiId').value = prov.id;
                        document.getElementById('nama').value = prov.nama;
                        document.getElementById('geojson').value = prov.geojson;
                        document.getElementById('formTitle').textContent = "Edit Provinsi (ID: " + prov.id + ")";
                        document.getElementById('cancelBtn').style.display = "inline-block";
                    } else {
                        alert(result.message);
                    }
                })
                .catch(error => console.error("Error fetching provinsi data:", error));
        }

        // Fungsi untuk menghapus data provinsi
        function deleteProvinsi(id) {
            if (confirm("Apakah Anda yakin ingin menghapus provinsi dengan ID " + id + "?")) {
                fetch(apiUrl + '/' + id, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        loadProvinsiTable();
                        loadProvinsiOnMap();
                    })
                    .catch(error => console.error("Error deleting provinsi:", error));
            }
        }

        // Tombol cancel untuk reset form edit
        document.getElementById('cancelBtn').addEventListener('click', function() {
            resetForm();
        });

        function resetForm() {
            document.getElementById('provinsiForm').reset();
            document.getElementById('provinsiId').value = '';
            document.getElementById('formTitle').textContent = "Tambah Provinsi";
            document.getElementById('cancelBtn').style.display = "none";
        }

        // Inisialisasi awal: muat data ke tabel dan peta
        loadProvinsiTable();
        loadProvinsiOnMap();
    </script>
</body>

</html>
