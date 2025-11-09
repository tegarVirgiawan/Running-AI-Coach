<?php
header('Content-Type: application/json');

// --- API KEY (HARUS DARI GOOGLE CLOUD - ENABLE BILLING) ---
$geminiApiKey = 'AIzaSyC8sjGu65I_JwRWRu4zP_ThddbLXIucKYY';

// Ambil data JSON dari frontend
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['error' => 'Data input tidak valid.']);
    exit;
}

$distance = $input['distance'];
$totalSeconds = $input['totalSeconds'];
$age = $input['age'];
$hr_rest = $input['hr_rest'];
$question = $input['question'];

// --- PERHITUNGAN DATA LARI ---
$paceDecimal = ($totalSeconds / 60) / $distance;
$paceMinutes = floor($paceDecimal);
$paceSeconds = round(($paceDecimal - $paceMinutes) * 60);
$paceFormatted = sprintf("%d'%02d\" /km", $paceMinutes, $paceSeconds);
$weight = 65;
$caloriesBurned = round($distance * $weight * 1.05);
$hr_max = 220 - $age;
$hrr = $hr_max - $hr_rest;
$zone1 = round($hrr * 0.5) + $hr_rest . '-' . round($hrr * 0.6) + $hr_rest;
$zone2 = round($hrr * 0.6) + $hr_rest . '-' . round($hrr * 0.7) + $hr_rest;
$zone3 = round($hrr * 0.7) + $hr_rest . '-' . round($hrr * 0.8) + $hr_rest;
$zone4 = round($hrr * 0.8) + $hr_rest . '-' . round($hrr * 0.9) + $hr_rest;
$minutesPerKm = $totalSeconds / $distance / 60;
$intensity = ($minutesPerKm <= 4.5) ? 0.85 : (($minutesPerKm <= 5.5) ? 0.75 : (($minutesPerKm <= 7) ? 0.65 : 0.6));
$estimatedHRAvg = round(($hrr * $intensity) + $hr_rest);

// --- PROMPT ---
$prompt = "
Anda adalah seorang pelatih lari virtual dan pakar olahraga bernama AI Coach.

Data:
Usia: {$age}
RHR: {$hr_rest} bpm
Max HR: {$hr_max}
Pace: {$paceFormatted}
Jarak: {$distance} km

Zona HR:
• Zona 1: {$zone1} bpm
• Zona 2: {$zone2} bpm
• Zona 3: {$zone3} bpm
• Zona 4: {$zone4} bpm

Pertanyaan: {$question}

Berikan analisis dalam format markdown yang ramah pemula.
";

// --- FUNGSI PANGGIL API GEMINI ---
function callGeminiAPI($apiKey, $prompt) {

    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $apiKey;

    $data = [
        "contents" => [
            [
                "parts" => [
                    ["text" => $prompt]
                ]
            ]
        ]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [ "Content-Type: application/json" ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);

    $response = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http !== 200) {
        return "Error API: HTTP {$http} - " . $response;
    }

    $res = json_decode($response, true);

    // ✅ Struktur terbaru v2
    if (isset($res['candidates'][0]['content']['parts'][0]['text'])) {
        return $res['candidates'][0]['content']['parts'][0]['text'];
    }

    return "Tidak ada respons AI yang valid. Raw: " . $response;
}

$aiAnalysis = callGeminiAPI($geminiApiKey, $prompt);

// --- RESPON JSON ---
echo json_encode([
    "calculations" => [
        "pace" => $paceFormatted,
        "calories" => $caloriesBurned,
        "estimated_hr_avg" => $estimatedHRAvg,
        "hr_zones" => [
            "zone1" => $zone1,
            "zone2" => $zone2,
            "zone3" => $zone3,
            "zone4" => $zone4
        ]
    ],
    "ai_analysis" => $aiAnalysis
]);
