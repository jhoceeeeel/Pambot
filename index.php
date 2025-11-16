<?php 
session_start();
require 'config.php';

// Check if student is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Save search history only (results handled by JavaScript from JSON)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['keyword'])) {
    $keyword = trim($_POST['keyword']);

    if (!empty($keyword)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO search_history (email, search_query) VALUES (?, ?)");
            $stmt->execute([$email, $keyword]);
        } catch (PDOException $e) {
            error_log("Search history insert failed: " . $e->getMessage());
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pambot</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="index.css">
</head>
<!-- Back to Top Button -->
<button id="backToTop" onclick="window.scrollTo({top: 0, behavior: 'smooth'});">↑</button>

<script>
    const backToTopBtn = document.getElementById("backToTop");
    window.addEventListener("scroll", () => {
        backToTopBtn.style.display = window.scrollY > 200 ? "block" : "none";
    });
</script>

<body>
    <header>
        <div class="logo">
            <img src="pambotlogo.png" alt="Pambot logo">
            <span>Pambot</span>
        </div>
        <nav>
            <a href="#" class="active">HOME</a>
            <button class="plmun-btn">PLMUN</button>
        </nav>
    </header>
    


    <main>
        <!-- Main Title -->
        <div class="main-title">
        <img src="gettoknowplmun.png" alt="GET TO KNOW PLMUN" style="filter: brightness(1.5);">
        </div>

        <!-- Search Form -->
        <!-- Search Form -->
<form action="" method="POST" class="search-form" onsubmit="return handleCombinedSearch(event);">
    <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" name="keyword" id="combinedSearch" placeholder="search a topic or keywords" required>
        <button type="submit">Search</button>
    </div>
</form>


        <!-- Display Search Results -->
        <!-- Combined Search Results Area -->
<div class="results hidden" id="combinedResults"></div>



        <a href="endsession.php?manual=1" class="end-session">END SESSION</a>


        <!-- Robot Images -->
        <div class="robot-images">
    <img src="robotleft.png" alt="Robot Left" class="left">
    <img src="robotright.png" alt="Robot Right" class="right">
</div>

    </main>

    <script src="index.js"></script>
    <script>
(function(){if(!window.chatbase||window.chatbase("getState")!=="initialized"){window.chatbase=(...arguments)=>{if(!window.chatbase.q){window.chatbase.q=[]}window.chatbase.q.push(arguments)};window.chatbase=new Proxy(window.chatbase,{get(target,prop){if(prop==="q"){return target.q}return(...args)=>target(prop,...args)}})}const onLoad=function(){const script=document.createElement("script");script.src="https://www.chatbase.co/embed.min.js";script.id="G11bPBcRR2EZ3-4ZX9ulX";script.domain="www.chatbase.co";document.body.appendChild(script)};if(document.readyState==="complete"){onLoad()}else{window.addEventListener("load",onLoad)}})();
</script>
<script>
window.addEventListener("beforeunload", function () {
    navigator.sendBeacon("endsession.php");
});
</script>

</script>

</body>
</html>
