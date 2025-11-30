<?php
if(isset($_GET["num"])){
    $num = $_GET["num"];
    $url = "https://numapi.anshapi.workers.dev?num=".$num;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0");
    $response = curl_exec($ch);
    curl_close($ch);

    header("Content-Type: application/json");
    echo $response;
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Mobile Tracker</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { 
    background: #f4f4f4; 
    font-family: Arial, sans-serif; 
    margin: 0;
    padding: 10px;
    height: 100vh;
    overflow-x: hidden;
}
.box {
    background: white;
    padding: 20px 15px;
    border-radius: 12px;
    box-shadow: 0 3px 15px rgba(0,0,0,0.25);
    height: calc(100vh - 20px);
    display: flex;
    flex-direction: column;
}

/* RGB Effect ONLY on SOUMIK Text */
.rgb-text {
    text-align: center;
    margin-bottom: 20px;
    font-size: 2em;
    font-weight: bold;
    background: linear-gradient(90deg, #ff0000, #ff8000, #ffff00, #00ff00, #00ffff, #0000ff, #8000ff, #ff0080, #ff0000);
    background-size: 400% 400%;
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    animation: rgbFlow 3s linear infinite;
    text-shadow: 0 0 10px rgba(255,255,255,0.3);
    letter-spacing: 2px;
}

@keyframes rgbFlow {
    0% { background-position: 0% 50%; }
    100% { background-position: 400% 50%; }
}

input {
    width: 100%;
    padding: 16px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 18px;
    margin-bottom: 10px;
}
button {
    width: 100%;
    padding: 18px;
    background: #0d1b3d;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 18px;
    cursor: pointer;
}
button:hover {
    background: #091229;
}
#info {
    white-space: pre-wrap;
    margin-top: 15px;
    font-size: 14px;
    background: white;
    padding: 15px;
    border-radius: 10px;
    display: none;
    flex: 1;
    overflow-y: auto;
}
#map {
    height: 250px;
    border-radius: 12px;
    margin-top: 15px;
    display: none;
}
.loader {
    display: none;
    margin: 20px auto;
    width: 50px;
    height: 50px;
    border: 5px solid #ccc;
    border-top: 5px solid #0d1b3d;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}
@keyframes spin {
    100% { transform: rotate(360deg); }
}

/* Mobile responsive improvements */
@media (max-width: 480px) {
    body {
        padding: 8px;
    }
    .box {
        padding: 15px 10px;
        height: calc(100vh - 16px);
    }
    .rgb-text {
        font-size: 1.6em;
        margin-bottom: 15px;
    }
    input, button {
        padding: 14px;
        font-size: 16px;
    }
    #info {
        font-size: 13px;
        padding: 12px;
    }
    #map {
        height: 200px;
    }
}

/* Scrollbar styling for mobile */
#info::-webkit-scrollbar {
    width: 5px;
}
#info::-webkit-scrollbar-track {
    background: #f1f1f1;
}
#info::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}
</style>

<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY_HERE"></script>

</head>

<body>

<div class="box">
    <!-- सिर्फ SOUMIK text पर RGB effect -->
    <div class="rgb-text">SOUMIK NOMBER INFO</div>

    <input id="number" placeholder="📱Enter 10 digit number" type="tel" maxlength="10">
    <button onclick="startTrack()">Track Now</button>

    <div id="loader" class="loader"></div>

    <div id="info"></div>
    <div id="map"></div>
</div>

<script>

let targetNumber = "";

// START TRACKING
function startTrack(){
    let num = document.getElementById("number").value;

    if(num.length!==10 || isNaN(num)){
        alert("📱Enter valid 10 digit number");
        return;
    }

    targetNumber = num;

    document.getElementById("loader").style.display="block";
    document.getElementById("info").style.display="none";
    document.getElementById("map").style.display="none";

    fetch("?num="+num)
    .then(r=>r.json())
    .then(api=>{
        document.getElementById("loader").style.display="none";

        let info = api.result[0];
        showFormattedInfo(info);

        let cleanedAddress = cleanAddress(info.address);

        geocode(cleanedAddress);
    })
    .catch(error => {
        document.getElementById("loader").style.display="none";
        alert("Error fetching data");
        console.error(error);
    });
}


// FORMAT OUTPUT TEXT
function showFormattedInfo(x){
    let text = `
✅ Information Found

🔢 Target Number: ${targetNumber}
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

📄 Record:
• 👤 Full Name: ${x.name}
• 👨‍🦳 Father Name: ${x.father_name}
• 📱 Mobile Number: ${x.mobile}
• 🆔 Aadhar Number: ${x.id_number}
• 🏠 Complete Address: ${x.address}
• 📞 Alternate Mobile: ${x.alt_mobile}
• 📍 Telecom Circle: ${x.circle}
• 🔢 User ID: ${x.id}

──────────────────────────────

💻 Bot by SOUMIK GHOSH 
📱 Join: @MUNNA_BHAI
`;

    document.getElementById("info").innerText = text;
    document.getElementById("info").style.display = "block";
}


// ADDRESS CLEANER
function cleanAddress(a){
    a = a.replace(/S\/O|W\/O|D\/O/gi," ");
    a = a.replace(/[^\w\s]/g," ");
    a = a.replace(/\s+/g," ").trim();
    return a;
}


// GOOGLE MAP GEOCODING
function geocode(address){
    let geocoder = new google.maps.Geocoder();

    geocoder.geocode({ address: address }, function(results, status){
        if(status !== "OK"){
            alert("Unable to locate: "+address);
            return;
        }

        let location = results[0].geometry.location;
        showGoogleMap(location.lat(), location.lng(), address);
    });
}


// SHOW GOOGLE MAP
function showGoogleMap(lat, lng, address){
    document.getElementById("map").style.display="block";

    let map = new google.maps.Map(document.getElementById("map"), {
        zoom: 14,
        center: { lat: parseFloat(lat), lng: parseFloat(lng) },
        mapTypeId: "hybrid"
    });

    let marker = new google.maps.Marker({
        position: { lat: parseFloat(lat), lng: parseFloat(lng) },
        map: map
    });

    let circle = new google.maps.Circle({
        strokeColor: "#7a00ff",
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: "#7a00ff",
        fillOpacity: 0.35,
        map: map,
        center: { lat: parseFloat(lat), lng: parseFloat(lng) },
        radius: 1500
    });
}

// Enter key support
document.getElementById("number").addEventListener("keypress", function(e){
    if(e.key === "Enter"){
        startTrack();
    }
});

</script>

</body>
</html>