<?php  
include 'koneksi.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

     <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz@0,14..32;1,14..32&display=swap" rel="stylesheet">
    <script type="text/javascript" src="ajax.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teras</title>
    <link rel="stylesheet" href="style.css">
    <script>
function setMinToday(input) {
    const today = new Date().toISOString().split('T')[0];
    input.min = today;
}

function updateEndDateMin() {
    const startDate = document.getElementById('agendaDate').value;
    const endInput = document.getElementById('endagendaDate');
    
    if (startDate) {
        endInput.min = startDate;

        // Optional: Reset end date if it is earlier than new min
        if (endInput.value && endInput.value < startDate) {
            endInput.value = startDate;
        }
    }
}

function updateClock() {
    const now = new Date();
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');
    const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    document.getElementById('clock').innerHTML = `${hours}:${minutes} <span>${timezone}</span>`;

    // Calculate how much time until the next minute starts
    const msUntilNextMinute = (60 - now.getSeconds()) * 1000 - now.getMilliseconds();

    // Schedule next update exactly at the next full minute
    setTimeout(updateClock, msUntilNextMinute);
}


</script>

</head>

<body>
<div id="loader-wrapper">
  <div class="loader"></div>
  <div class="loading-text">Gathering user's weather information...</div>
</div>
<!-- Navbar -->
<div class="top-navbar">
    <div class="navbar-left">
        <a href="../About/about.html">
            <span class="TitleTeras">téras</span>
        </a>
    </div>

    <div class="account">
        Hi, <span><?= $_SESSION['username']?></span>
    </div>

    
    <div class="toggle-buttons">
        <div class="login">
    
        </div>
        <button class="active" onclick="window.location.href='logout.php';">Log out</button>
    </div>
</div>


    <!-- Main Content INI BAGIAN KEY-->
<div class="general-container">

        
    <div class="weather-card">

        <div class="upper-main">
            <div class="top-left-container">
                    
                <div id="temp" class="temp">
                    21°
                    <span>feels like 19°
                    </span> <br>
                </div>
                
                
                <div id="loc" class="loc">
                    Citeureup
                </div>
                
                <div id="alert" class="alert">
                    <!-- FLOOD ALERT -->
                    Flood-free!
                    <!-- kalo intensity di bawah 10 -->

                    <!-- Beware
                     kalo rain intensity di atas 15-->
                   
                     
                </div>
                <div id="summary" class="summary">
                    Visibility
                </div>
                
                <div class="weather-details">
                    <div id="clock" class="clock">
                            03:27 <span>utc +7</span>
                    </div>
                    
                    <div id="timeInfo" class="blocks">
                        ☀️ Rises <span>at</span> 05:40 <br>
                        ☀️ Sets <span>at</span> 17:59
                    </div>
                    
                    
                </div>
                
            </div>
            
            
            <div class="top-right-container">
                
                <div class="upper-right">
                    <div id="Day" class="Day">
                        Friday
                    </div>
                    
                    <div id="Date" class="Date">
                        April 4
                    </div>
                </div>
                
                <div class="lower-right">
                    <div class="blockings">

                        <div class="infos">
                            <span>Uv Index</span>
                            <p id="uvIndex">4 <span>Moderate</span></p>
                        </div>
                        
                        <div class="scale-container">
                            <div class="scale">
                                <div class="markers">
                                    <div class="marker"></div>
                                    <div class="marker"></div>
                                    <div class="marker"></div>
                                    <div class="marker"></div>
                                    <div class="marker"></div>
                                    <div class="marker"></div>
                                </div>
                                <div id="uvValue" class="scale-value uv-value">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="blockings">
                        <div class="infos">
                            <span>Wind Speed</span>
                            <p id="windSpeed">3 Km/h <span>Good</span></p>
                        </div>
                        
                        <div class="scale-container">
                            <div class="scale">
                                <div class="markers">
                                    <div class="marker"></div>
                                    <div class="marker"></div>
                                    <div class="marker"></div>
                                    <div class="marker"></div>
                                    <div class="marker"></div>
                                    <div class="marker"></div>
                                </div>
                                <div id="windValue" class="scale-value airpol-value">
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <div class="blockings">

                        <div class="infos">
                            <span>Rain Intensity</span> 
                            <p id="rainInfo">10 mm/h <span>Light</span></p>
                        </div>
                        
                        <div class="scale-container">
                            <div class="scale">
                                <div class="markers">
                                    <div class="marker"></div>
                                    <div class="marker"></div>
                                    <div class="marker"></div>
                                    <div class="marker"></div>
                                    <div class="marker"></div>
                                    <div class="marker"></div>
                                </div>
                                    <div id="rainValue" class="scale-value temp-value"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
                
                
        <div class="bottom-container">

            <div id="forecasthourly" class="section-hour"></div>


            <div id="daily" class="daily"></div>
    
        </div>
    </div>
    
    
    <!-- Right: Card with Vertical Rectangles
    
    INI BAGIAN EPALL-->
    <div class="agenda-card">
        <div id="listAgenda" class="agenda-container">
            <div class="agenda-date">
                <div class="wrapper-day">
                    <p class="today">Today</p>
                </div>

                <div class="rectangle-container">
                    <div class="rec-items">
                        <div class="delete-button">
                            <button onclick="location.reload()">
                                <img src="./assets/material-symbols_delete-rounded.png" alt="delete">
                            </button>
                        </div>

                        <div class="mendung" onclick="location.reload()">
                            <div>
                                <p>Belanja bulanan</p>
                                <p class="jam-cerah">08:00 - 12:00</p>
                            </div>
                            
                            <img class="right-side-icon" src="./assets/material-symbols-light_umbrella-rounded.png" alt="payung tertutup">
                        </div>
                        
                    </div>

                    <div class="rec-items">
                        <div class="delete-button">
                            <button onclick="location.reload()">
                                <img src="./assets/material-symbols_delete-rounded.png" alt="delete">
                            </button>
                        </div>
    
                        <div class="hujan">
                            <div>
                                <p>ISD</p>
                                <p class="jam-hujan">08:00 - 12:00</p>
                            </div>
                            <img class="right-side-icon" src="./assets/carbon_umbrella.png" alt="payung tertutup">
                        </div>
                    </div>

                    <div class="rec-items">
                        <div class="delete-button">
                            <button onclick="location.reload()">
                                <img src="./assets/material-symbols_delete-rounded.png" alt="delete">
                            </button>
                        </div>
    
                        <div class="hujan">
                            <div>
                                <p>self-stud praktikum</p>
                                <p class="jam-hujan">08:00 - 12:00</p>
                            </div>
                            <img class="right-side-icon" src="./assets/carbon_umbrella.png" alt="payung tertutup">
                        </div>
                    </div>
                </div>
            </div>

            <div class="agenda-date">
                <div class="wrapper-day">
                    <div class="day">Mon,</div>
                    <div class="tanggal">7</div>
                </div>

                <div class="rectangle-container">
                    <div class="rec-items">
                        <div class="delete-button">
                            <button onclick="location.reload()">
                                <img src="./assets/material-symbols_delete-rounded.png" alt="delete">
                            </button>
                        </div>

                        <div class="cerah">
                            <div>
                                <p>Nyuci</p>
                                <p class="jam-cerah">09:00 - 10:00</p>
                            </div>
                            <img class="right-side-icon" src="./assets/Sun.png" alt="payung tertutup">
                        </div>
                    </div>

                    <div class="rec-items">
                        <div class="delete-button">
                            <button onclick="location.reload()">
                                <img src="./assets/material-symbols_delete-rounded.png" alt="delete">
                            </button>
                        </div>

                        <div class="cerah">
                            <div>
                                <p>Workout</p>
                                <p class="jam-cerah">07:00 - 09:00</p>
                            </div>
                            <img class="right-side-icon" src="./assets/Sun.png" alt="payung tertutup">
                        </div>
                    </div>

                    <div class="rec-items">
                        <div class="delete-button">
                            <button onclick="location.reload()">
                                <img src="./assets/material-symbols_delete-rounded.png" alt="delete">
                            </button>
                        </div>

                        <div class="cerah">
                            <div>
                                <p>Workout</p>
                                <p class="jam-cerah">07:00 - 09:00</p>
                            </div>
                            <img class="right-side-icon" src="./assets/Sun.png" alt="payung tertutup">
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <div class="input-schedule-card">
        <div class="top-input-container">
            <div class="icon-container">
                <div class="add-icon">
                    <button id="inputButton" onclick="buatAgenda()">
                        <img src="./assets/mingcute_add-fill.png" alt="add">
                    </button>
                </div>

                <div class="discard-icon"> <!-- discard di hide sebelum data keisi di input --> 
                    <button onclick="clearForm()">
                        <img src="./assets/streamline_delete-1-solid.png" alt="add">
                    </button>
                </div>
            </div>

            <div class="input-schedule">                   
                <form id="postForm"> <!-- ini buat form -->
                    <div class="block-input">
                        <input type="text" id="title" name="title" size="25" placeholder="Add Title"/> <!-- id dan nama masih dummy--> 
                    </div>

                    <div class="block-input time-range">
                        <input 
                        id="startingHour"
                        type="text" placeholder="Start Hour" onfocus="this.type='time'" onblur="this.type='text'" />
                        <span>→</span> 
                        <input 
                        id="endHour" type="text" placeholder="End Hour" onfocus="this.type='time'" onblur="this.type='text'" />
                    </div>

                    <div class="block-input">
                        <input 
                        id="agendaDate"
                        type="text" placeholder="Date" onfocus="this.type='date'; setMinToday(this);" onblur="this.type='text'" onchange="updateEndDateMin()" >
                        <input id="endagendaDate" type="text" placeholder="End Date" onfocus="this.type='date'; updateEndDateMin();" onblur="this.type='text'"> 
                    </div>
                        
                    <div class="block-input all-day-repeat">
                        <button type="button"
                        id="allDayBtn"
                        style="margin-left: 10px; cursor: pointer;">All day</button>
                        <script>
        const currentAMInput = document.getElementById('startingHour');
        const endAMInput = document.getElementById('endHour');
        const allDayBtn = document.getElementById('allDayBtn');
        const allDayPenanda = document.getElementById('allDayPenanda');
        let isAllDay = false;
        let hasAutoFilledEndAM = false; 
        allDayBtn.addEventListener('click', () => {
            isAllDay = !isAllDay;
            if (isAllDay) {
                currentAMInput.value = '00:00';
                endAMInput.value = '23:59';
                currentAMInput.disabled = true;
                endAMInput.disabled = true;
                allDayBtn.style.fontWeight = 'bold';
            } else {
                currentAMInput.value = '';
                endAMInput.value = '';
                currentAMInput.disabled = false;
                endAMInput.disabled = false;
                allDayBtn.style.fontWeight = 'normal';
            }
        });
        currentAMInput.addEventListener('change', () => {
            if (hasAutoFilledEndAM) return;
            const [hours, minutes] = currentAMInput.value.split(':').map(Number);
            if (isNaN(hours) || isNaN(minutes)) return;

            let endHours = hours + 2;
            let endMinutes = minutes;

            if (endHours >= 24) {
            endHours = endHours - 24;
            }

           
            const paddedHours = endHours.toString().padStart(2, '0');
            const paddedMinutes = endMinutes.toString().padStart(2, '0');

            endAMInput.value = `${paddedHours}:${paddedMinutes}`;
            hasAutoFilledEndAM = true; 
  });
    </script>
                        <select name="repeat" id="repeat">
                            <option value="1">No repeat</option>
                            <option value="2">Everyday</option>
                            <option value="3">Every week</option>
                            <option value="4">Every 2 weeks</option>
                            <option value="5">Every month</option>
                        </select>
                    </div>
                </form>

            </div>
        </div>

        <div class="bottom-drop-container" id="uploadBox">
            <div class="input-file" id="fileClickArea" style="cursor: pointer;">
            <span id="fileLabel">or just drop schedule here...</span>
            <input type="file" id="filename" name="image" accept="image/*" hidden>
            </div>

            <div class="icon-input-file" id="iconUpload" style="cursor: pointer;">
                <img src="./assets/Upload.png" alt="drop file">
            </div>
        </div>
    </div>
</div>

<footer>
    &copy Telkom University ByteForBait
</footer>

<div id="uploadPreviewPopup" class="confirm-popup hidden">
  <div class="confirm-box">

    <p id="previewFileInfo">Agenda's taken from <strong>{filename}</strong></p>

    <div id="agendaPreviewList" class="agenda-preview-list">
 
  <div id="agendaLoader" class="agenda-loader hidden">
    <div class="loader"></div>
    <div class="loading-text">Gathering agenda from file...</div>
  </div>
</div>



    <div class="confirm-actions">
      <button onclick="confirmUpload()" class="confirm-yes">Confirm</button>
      <button onclick="cancelUpload()" class="confirm-no">Cancel</button>
    </div>
  </div>
</div>

<script>
  const fileInput = document.getElementById('filename');
  const fileClickArea = document.getElementById('fileClickArea');
  const iconUpload = document.getElementById('iconUpload');
  const fileLabel = document.getElementById('fileLabel');

  const previewPopup = document.getElementById('uploadPreviewPopup');
  const previewFileInfo = document.getElementById('previewFileInfo');
  const agendaPreviewList = document.getElementById('agendaPreviewList');

  let agendaData = null;

  let selectedFile = null;

  fileClickArea.addEventListener('click', () => {
    fileInput.click();
  });

  fileInput.addEventListener('change', function () {
    if (fileInput.files.length > 0) {
      selectedFile = fileInput.files[0];
      fileLabel.textContent = selectedFile.name;
      iconUpload.classList.add('upload-ready'); 
    }
    fileInput.value = '';
  });


  iconUpload.addEventListener('click', async function () {
  if (!selectedFile) return;


  previewPopup.classList.remove('hidden');

  previewFileInfo.innerHTML = `Agenda's taken from <strong>${selectedFile.name}</strong>`;


  const agendaLoader = document.getElementById('agendaLoader');
  agendaLoader.classList.remove('hidden');


  const agendaPreviewList = document.getElementById('agendaPreviewList');
  [...agendaPreviewList.querySelectorAll('.agenda-item')].forEach(item => item.remove());

  const formData = new FormData();
  formData.append('image', selectedFile);

  try {
    agendaData = await getAgendaFromAI(formData);

   
    agendaLoader.classList.add('hidden');

    agendaData.forEach(agenda => {
      const item = document.createElement('div');
      item.classList.add('agenda-item');
      item.innerHTML = `
        <p class="agenda-name">${agenda.nama}</p>
        <p class="agenda-day">${agenda.hari}</p>
        <p class="agenda-time">${agenda.mulai} - ${agenda.selesai}</p>
      `;
      agendaPreviewList.appendChild(item);
    });

  } catch (error) {
    console.error('Error loading agenda:', error);
    agendaLoader.classList.add('hidden');
    agendaPreviewList.innerHTML += `<p style="color: red; text-align: center;">Failed to load agenda from file.</p>`;
  }
});

  function confirmUpload() {
    if (!selectedFile) return;
    const jsonAgenda = JSON.stringify(agendaData);
    const formDataToSend = new FormData();
    formDataToSend.append('formData', jsonAgenda);
    buatAgendaFromAI(formDataToSend);
    previewPopup.classList.add('hidden');
    resetState();
  }

  function cancelUpload() {
    previewPopup.classList.add('hidden');
  }

  function resetState() {
    fileInput.value = '';
    selectedFile = null;
    agendaData = null;
    iconUpload.classList.remove('upload-ready');
    fileLabel.textContent = 'or just drop schedule here...';
    [...agendaPreviewList.querySelectorAll('.agenda-item')].forEach(item => item.remove());
  }
</script>

<div id="confirmPopup" class="confirm-popup hidden">
  <div class="confirm-box">
    <p>Are you sure you want to delete this agenda?</p>
    <div class="confirm-actions">
      <button onclick="confirmDelete()" class="confirm-yes">Yes</button>
      <button onclick="cancelDelete()" class="confirm-no">Cancel</button>
    </div>
  </div>
</div>


</body>

</html>