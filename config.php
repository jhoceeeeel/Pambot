<?php
$host = "localhost";
$dbname = "pambot";
$username = "root";
$password = "";

// Securely store the OpenAI API key
define("OPENAI_API_KEY", "sk-n2NcQCjufjtaPZteSuPWo3a2mgil5BnEeJDFyYU5lCT3BlbkFJNYWL1tW77V0m9UoHs1fgHj6cdwOz287RIYY-zCxh4A");

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
