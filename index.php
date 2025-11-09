<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pakar Analisis Lari | AI Coach</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
</head>
<body>
    <header>
        <h1>AI Running Coach 🏃‍♂️💨</h1>
        <p>Masukkan data lari Anda dan dapatkan analisis mendalam dari AI.</p>
    </header>

    <main>
        <div class="container">
            <form id="run-form">
                <div class="form-section">
                    <h2>Data Lari & Personal</h2>
                    <div class="input-group">
                        <label for="distance">Jarak (km)</label>
                        <input type="number" id="distance" required step="0.1" placeholder="cth: 5.2">
                    </div>
                    <div class="input-group">
                        <label for="hours">Waktu Lari (Jam:Menit:Detik)</label>
                        <div class="time-inputs">
                            <input type="number" id="hours" min="0" placeholder="Jam">
                            <input type="number" id="minutes" min="0" max="59" required placeholder="Menit">
                            <input type="number" id="seconds" min="0" max="59" required placeholder="Detik">
                        </div>
                    </div>
                    <div class="input-group">
                        <label for="age">Usia Anda</label>
                        <input type="number" id="age" required placeholder="cth: 30">
                    </div>
                    <div class="input-group">
                        <label for="hr_rest">Detak Jantung Istirahat (bpm)</label>
                        <input type="number" id="hr_rest" required placeholder="cth: 65">
                    </div>
                </div>

                <div class="form-section">
                    <h2>Pertanyaan untuk AI Coach</h2>
                    <div class="input-group">
                         <label for="question">Ajukan pertanyaan spesifik</label>
                        <textarea id="question" rows="4" placeholder="Contoh: Mengapa saya merasa sangat lelah di 2km terakhir?"></textarea>
                    </div>
                </div>

                <button type="submit" id="submit-btn">Analisis Sekarang</button>
            </form>

            <div id="results" class="hidden">
                <h2>Hasil Analisis Anda</h2>
                <div class="result-grid">
                    <div class="metric-card">
                        <h3>Pace Lari</h3>
                        <p id="pace-result">-</p>
                    </div>
                    <div class="metric-card">
                        <h3>Estimasi Kalori Terbakar</h3>
                        <p id="calories-result">-</p>
                    </div>
                    <div class="metric-card">
                        <h3>Estimasi Heart Rate Rata-rata</h3>
                        <p id="hr-avg-result">-</p>
                    </div>
                </div>
                
                <div class="chart-container">
                     <h3>Zona Detak Jantung Anda</h3>
                     <canvas id="hr-zone-chart"></canvas>
                </div>

                <div class="ai-analysis">
                    <h3>🔍 Analisis dari AI Coach</h3>
                    <div id="ai-result"></div>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <p>Dibuat dengan kombinasi PHP & AI</p>
    </footer>
    <script src="js/script.js"></script>
</body>
</html>