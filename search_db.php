<?php
session_start();
require 'config.php'; // contains $pdo and OPENAI_API_KEY

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['email'])) {
    echo json_encode([]);
    exit();
}

$keyword = trim($_POST['keyword'] ?? '');
if ($keyword === '') {
    echo json_encode([]);
    exit();
}
$email = $_SESSION['email'];

// Save raw search
$stmt = $pdo->prepare("INSERT INTO search_history (email, search_query) VALUES (?, ?)");
$stmt->execute([$email, $keyword]);

// 1) Try DB keyword search first
$stmt = $pdo->prepare("SELECT keyword, content FROM keywords WHERE keyword LIKE ? LIMIT 20");
$stmt->execute(["%$keyword%"]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($results) > 0) {
    echo json_encode(['source' => 'db', 'results' => $results]);
    exit();
}

// 2) If no DB results, use OpenAI to extract helpful keywords or an answer
function call_openai_chat($system, $user) {
    $ch = curl_init("https://api.openai.com/v1/chat/completions");
    $payload = json_encode([
        "model" => "gpt-4o-mini", // choose model you have access to
        "messages" => [
            ["role" => "system", "content" => $system],
            ["role" => "user", "content" => $user]
        ],
        "max_tokens" => 300,
        "temperature" => 0.2
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer " . OPENAI_API_KEY
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    $resp = curl_exec($ch);
    if ($resp === false) { return null; }
    $data = json_decode($resp, true);
    if (isset($data['choices'][0]['message']['content'])) {
        return $data['choices'][0]['message']['content'];
    }
    return null;
}

// Ask OpenAI to produce 5 short search keywords (comma-separated) in the same language as the query
$system = "You are a helpful assistant that extracts 3-7 concise keywords from a user's question or provides a short answer. Return only the keywords separated by commas if asked for keywords.";
$user_prompt = "Extract short search keywords from this student question (no explanation). Question: \"{$keyword}\"";

$openai_out = call_openai_chat($system, $user_prompt);

$openai_results = [];
if ($openai_out) {
    // parse comma separated keywords
    $kwstr = trim($openai_out);
    $parts = preg_split('/[,\n]+/', $kwstr);
    foreach ($parts as $p) {
        $p = trim($p);
        if ($p !== '') {
            // search for each keyword in DB
            $stmt = $pdo->prepare("SELECT keyword, content FROM keywords WHERE keyword LIKE ? LIMIT 5");
            $stmt->execute(["%$p%"]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $r) { $openai_results[] = $r; }
        }
    }
}

// If DB found results from OpenAI keywords, return them
if (count($openai_results) > 0) {
    echo json_encode(['source' => 'db_via_openai', 'results' => $openai_results]);
    exit();
}

// 3) If still nothing, ask OpenAI for a short direct answer (with recommendation to check handbook)
$system2 = "You are a polite assistant. Provide a short answer suitable for university students. If the answer is policy/legal, advise to consult the student handbook and guidance office.";
$user2 = "Student asked: \"{$keyword}\". Give a short helpful answer (1-3 sentences) and suggest to check the handbook for details.";
$openai_answer = call_openai_chat($system2, $user2);

if ($openai_answer) {
    // Save chat log
    $stmt = $pdo->prepare("INSERT INTO chat_logs (email, user_message, bot_response) VALUES (?, ?, ?)");
    $stmt->execute([$email, $keyword, $openai_answer]);

    echo json_encode(['source' => 'openai', 'answer' => $openai_answer]);
    exit();
}

// fallback
echo json_encode(['source' => 'none', 'results' => []]);
exit();

$lang = $_POST['lang'] ?? 'en';
if ($lang === 'tl') {
    $system = "You are a helpful assistant that outputs keywords in Filipino where appropriate...";
}