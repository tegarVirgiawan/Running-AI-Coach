document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('run-form');
    const resultsDiv = document.getElementById('results');
    const submitBtn = document.getElementById('submit-btn');
    let hrChart = null; // Variable to hold the chart instance

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        resultsDiv.classList.remove('hidden');
        document.getElementById('ai-result').innerHTML = '';
        
        submitBtn.disabled = true;
        submitBtn.textContent = 'Menganalisis...';

        // Get form data
        const distance = parseFloat(document.getElementById('distance').value);
        const hours = parseInt(document.getElementById('hours').value) || 0;
        const minutes = parseInt(document.getElementById('minutes').value) || 0;
        const seconds = parseInt(document.getElementById('seconds').value) || 0;
        const age = parseInt(document.getElementById('age').value);
        const hr_rest = parseInt(document.getElementById('hr_rest').value);
        
        // Logika "question" DIKEMBALIKAN
        const question = document.getElementById('question').value;

        const totalSeconds = (hours * 3600) + (minutes * 60) + seconds;

        const formData = {
            distance,
            totalSeconds,
            age,
            hr_rest,
            question // "question" DIKEMBALIKAN
        };

        try {
            const response = await fetch('api/analyze.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`HTTP error! status: ${response.status} | Pesan: ${errorText}`);
            }

            const data = await response.json();
            
            if(data.error) {
                // Menampilkan error yang diformat dari PHP
                document.getElementById('ai-result').innerHTML = `<pre style="color:red;">${data.error}</pre>`;
                throw new Error(data.error);
            }

            // Display results
            document.getElementById('pace-result').textContent = data.calculations.pace;
            document.getElementById('calories-result').textContent = `${data.calculations.calories} kcal`;
            document.getElementById('hr-avg-result').textContent = `${data.calculations.estimated_hr_avg} bpm`;
            
            // Menggunakan innerHTML agar format markdown (baris baru) dari AI tampil rapi
            document.getElementById('ai-result').innerHTML = data.ai_analysis.replace(/\n/g, '<br>');
            
            // Render Chart
            renderHRZoneChart(data.calculations.hr_zones);

        } catch (error) {
            // Menampilkan error di bagian analisis AI agar terlihat
            document.getElementById('ai-result').innerHTML = `<span style="color:red;">${error.message}</span>`;
            console.error('Error during fetch:', error);
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Analisis Sekarang';
        }
    });

    function renderHRZoneChart(zones) {
        const ctx = document.getElementById('hr-zone-chart').getContext('2d');
        const labels = ['Zona 1 (Sangat Ringan)', 'Zona 2 (Ringan)', 'Zona 3 (Moderat)', 'Zona 4 (Berat)'];
        
        if (!zones || !zones.zone1 || !zones.zone2 || !zones.zone3 || !zones.zone4) {
             console.error("Data zona HR tidak lengkap atau tidak valid");
             return;
        }
        
        const data = [
            zones.zone1.split('-')[1], 
            zones.zone2.split('-')[1], 
            zones.zone3.split('-')[1], 
            zones.zone4.split('-')[1]
        ];
        
        if (hrChart) {
            hrChart.destroy();
        }

        hrChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Batas Atas Detak Jantung (bpm)',
                    data: data,
                    backgroundColor: [
                        'rgba(110, 212, 128, 0.6)',
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(255, 99, 132, 0.6)'
                    ],
                    borderColor: [
                        'rgba(110, 212, 128, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(255, 99, 132, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: false, ticks: { color: 'white' } },
                    x: { ticks: { color: 'white' } }
                },
                plugins: {
                    legend: { labels: { color: 'white' } }
                }
            }
        });
    }
});