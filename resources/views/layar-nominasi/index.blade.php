<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Layar Nominasi - Fullscreen</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            overflow: hidden;
        }
        .container {
            display: flex;
            flex-direction: column;
            height: 100vh;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 3em;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            margin-bottom: 10px;
        }
        .header h2 {
            font-size: 1.5em;
            opacity: 0.9;
        }
        .nominasi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            flex: 1;
            overflow-y: auto;
        }
        .nominasi-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .nominasi-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .card-header {
            font-size: 1.2em;
            font-weight: bold;
            margin-bottom: 15px;
            text-align: center;
            border-bottom: 2px solid rgba(255,255,255,0.3);
            padding-bottom: 10px;
        }
        .card-body {
            font-size: 1em;
        }
        .card-body p {
            margin: 10px 0;
        }
        .ranking {
            font-size: 2em;
            font-weight: bold;
            text-align: center;
            color: #ffd700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        .close-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(255,255,255,0.2);
            border: none;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            z-index: 1000;
        }
        .close-btn:hover {
            background: rgba(255,255,255,0.3);
        }
    </style>
</head>
<body>
    <button class="close-btn" onclick="window.close()">Tutup (ESC)</button>
    
    <div class="container">
        <div class="header">
            <h1>LAYAR NOMINASI</h1>
            <h2>Daftar Nominasi Terbaik</h2>
        </div>
        
        <div class="nominasi-grid" id="nominasiGrid">
            <!-- Data akan ditampilkan di sini -->
            <div class="nominasi-card">
                <div class="card-header">Peringkat 1</div>
                <div class="card-body">
                    <div class="ranking">🥇</div>
                    <p><strong>Peserta:</strong> Contoh Peserta 1</p>
                    <p><strong>Burung:</strong> Contoh Burung 1</p>
                    <p><strong>Kelas:</strong> Kicau</p>
                    <p><strong>Total Nilai:</strong> 95.5</p>
                </div>
            </div>
            <div class="nominasi-card">
                <div class="card-header">Peringkat 2</div>
                <div class="card-body">
                    <div class="ranking">🥈</div>
                    <p><strong>Peserta:</strong> Contoh Peserta 2</p>
                    <p><strong>Burung:</strong> Contoh Burung 2</p>
                    <p><strong>Kelas:</strong> Warna</p>
                    <p><strong>Total Nilai:</strong> 92.3</p>
                </div>
            </div>
            <div class="nominasi-card">
                <div class="card-header">Peringkat 3</div>
                <div class="card-body">
                    <div class="ranking">🥉</div>
                    <p><strong>Peserta:</strong> Contoh Peserta 3</p>
                    <p><strong>Burung:</strong> Contoh Burung 3</p>
                    <p><strong>Kelas:</strong> Kicau</p>
                    <p><strong>Total Nilai:</strong> 90.1</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Fullscreen on load
        if (document.documentElement.requestFullscreen) {
            document.documentElement.requestFullscreen();
        } else if (document.documentElement.webkitRequestFullscreen) {
            document.documentElement.webkitRequestFullscreen();
        } else if (document.documentElement.msRequestFullscreen) {
            document.documentElement.msRequestFullscreen();
        }

        // Close on ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                }
                setTimeout(() => window.close(), 100);
            }
        });

        // Auto refresh setiap 30 detik (optional)
        // setInterval(() => location.reload(), 30000);
    </script>
</body>
</html>

