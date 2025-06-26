function getUserLocation() {
  return new Promise((resolve, reject) => {
    if (!navigator.geolocation) {
      reject("Geolocation is not supported.");
    }

    navigator.geolocation.getCurrentPosition(
      (position) => {
        const lat = position.coords.latitude;
        const lon = position.coords.longitude;
        resolve({ lat, lon });
      },
      (error) => {
        reject("Error getting location: " + error.message);
      }
    );
  });
}

function getUserTime() {
  const now = new Date();
  return now;
}

function realtimeWeather() {
  const days = [
    "Sunday",
    "Monday",
    "Tuesday",
    "Wednesday",
    "Thursday",
    "Friday",
    "Saturday",
  ];
  const day = days[getUserTime().getDay()];
  var xhr = new XMLHttpRequest();
  xhr.open("GET", "./api/weather.php", true);
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      try {
        var data = JSON.parse(xhr.responseText);

        document.getElementById(
          "temp"
        ).childNodes[0].nodeValue = `${data.temperature}° `;
        document.querySelector(
          "#temp span"
        ).textContent = `feels like ${data.temperatureApparent}°`;
        document.getElementById(
          "summary"
        ).textContent = `Visibility : ${data.visibility} Km`;
        const location = data.location.split(", ").slice(0, -4).join(", ");
        document.getElementById("loc").textContent = location;
        if (data.rainIntensity < 10) {
          document.getElementById("alert").textContent = "Flood-free!";
          document.getElementById("alert").style.color = "rgb(71, 69, 69)";
          document.getElementById("alert").style.backgroundColor = "#fde68ab5";
          document.getElementById("alert").style.border =
            "3px solid rgb(247, 241, 169)";
        } else {
          document.getElementById("alert").textContent = "Beware!";
          document.getElementById("alert").style.color = "whitesmoke";
          document.getElementById("alert").style.backgroundColor = "#e61010";
          document.getElementById("alert").style.border =
            "3px solid rgb(205, 2, 2)";
        }
        document.getElementById(
          "uvIndex"
        ).innerHTML = `${data.uvIndex} <span>${data.uvStatus}</span>`;
        document.getElementById("uvValue").style.width = `${
          (data.uvIndex / 11) * 100
        }%`;
        document.getElementById(
          "windSpeed"
        ).innerHTML = `${data.windSpeed} m/s <span>${data.windDirection}</span>`;
        document.getElementById("windValue").style.width = `${
          (data.windSpeed / 3) * 100
        }%`;
        document.getElementById(
          "rainInfo"
        ).innerHTML = `${data.rainIntensity} mm/h <span>${data.rainStatus}</span>`;
        document.getElementById("rainValue").style.width = `${
          (data.rainIntensity / 30) * 100
        }%`;
        document.getElementById("Day").textContent = day;
        document.getElementById("Date").textContent =
          getUserTime().toLocaleString("en-US", {
            month: "long",
            day: "numeric",
          });
        document.getElementById(
          "timeInfo"
        ).innerHTML = `☀️ Rises <span>at</span> ${convertTimesToLocal(
          data.sunriseTime
        )} <br>☀️ Sets <span>at</span> ${convertTimesToLocal(data.sunsetTime)}`;
      } catch (e) {
        console.error("Failed to parse JSON: ", e);
      }
    }
  };
  xhr.send();
}

function convertTimesToLocal(isoString) {
  const date = new Date(isoString);
  return date.toLocaleTimeString("en-GB", {
    hour: "2-digit",
    minute: "2-digit",
  });
}

function forecastperhour() {
  const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
  var xhr = new XMLHttpRequest();
  xhr.open(
    "GET",
    "./api/forecastperhour.php?timezone=" + encodeURIComponent(timezone),
    true
  );
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      document.getElementById("forecasthourly").innerHTML = xhr.responseText;
    }
  };
  xhr.send();
}

function forecastdaily() {
  const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
  var xhr = new XMLHttpRequest();
  xhr.open(
    "GET",
    "./api/forecastdaily.php?timezone=" + encodeURIComponent(timezone),
    true
  );
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      document.getElementById("daily").innerHTML = xhr.responseText;
    }
  };
  xhr.send();
}

function getAgenda() {
  const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
  var xhr = new XMLHttpRequest();
  xhr.open(
    "GET",
    "./api/ambilAgenda.php?timezone=" + encodeURIComponent(timezone),
    true
  );
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      document.getElementById("listAgenda").innerHTML = xhr.responseText;
    }
  };
  xhr.send();
}

function clearForm() {
  isAllDay = false;
  document.getElementById("title").value = "";
  document.getElementById("startingHour").value = "";
  document.getElementById("endHour").value = "";
  document.getElementById("agendaDate").value = "";
  document.getElementById("endagendaDate").value = "";
  document
    .getElementById("inputButton")
    .setAttribute("onclick", "buatAgenda()");

  const slidingItems = document.querySelectorAll(".cerah, .mendung, .hujan");

  slidingItems.forEach((item) => {
    if (item.classList.contains("slid")) {
      item.classList.remove("slid");
    }
  });
}

function buatAgenda() {
  var title = document.getElementById("title").value;
  var startingHour = document.getElementById("startingHour").value;
  var endHour = document.getElementById("endHour").value;
  var agendaDate = document.getElementById("agendaDate").value;
  var endDate = document.getElementById("endagendaDate").value;
  if (!endDate.trim()) {
    endDate = agendaDate;
  }
  var repeat = document.getElementById("repeat").value;

  if (
    !title.trim() ||
    !startingHour.trim() ||
    !endHour.trim() ||
    !agendaDate.trim() ||
    !repeat.trim()
  ) {
    alert("Please fill out all fields.");
    return;
  }
  var data = new URLSearchParams({
    agendaTitle: title,
    startingHour: startingHour,
    endingHour: endHour,
    startingDate: agendaDate,
    endingDate: endDate,
    repeat: repeat,
  });
  var xhr = new XMLHttpRequest();
  xhr.open("POST", "./api/uploadAgenda.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      getAgenda();
      clearForm();
    }
  };
  xhr.send(data.toString());
}

let deleteAgendaId = null;

function Delete(id) {
  deleteAgendaId = id;
  document.getElementById("confirmPopup").classList.remove("hidden");
}

function confirmDelete() {
  if (deleteAgendaId !== null) {
    var xhr = new XMLHttpRequest();
    xhr.open(
      "GET",
      "./api/hapusAgenda.php?id=" + encodeURIComponent(deleteAgendaId),
      true
    );
    xhr.onreadystatechange = function () {
      if (xhr.readyState === 4 && xhr.status === 200) {
        clearForm();
        getAgenda();
      }
    };
    xhr.send();
  }

  document.getElementById("confirmPopup").classList.add("hidden");
  deleteAgendaId = null;
}

function cancelDelete() {
  document.getElementById("confirmPopup").classList.add("hidden");
  deleteAgendaId = null;
}

function hapusAgenda(id) {
  var xhr = new XMLHttpRequest();
  xhr.open("GET", "./api/hapusAgenda.php?id=" + encodeURIComponent(id), true);
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      getAgenda();
    }
  };
  xhr.send();
}

function getAgendaFromAI(formData) {
  return new Promise((resolve, reject) => {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "./api/Agenda-proses-AI.php", true);

    xhr.onload = function () {
      if (xhr.status === 200) {
        resolve(JSON.parse(xhr.responseText));
      } else {
        reject(new Error("Request failed with status " + xhr.status));
      }
    };

    xhr.onerror = function () {
      reject(new Error("Network error"));
    };

    xhr.send(formData);
  });
}

function buatAgendaFromAI(formData) {
  var xhr = new XMLHttpRequest();
  xhr.open("POST", "./api/uploadAgendaAI.php", true);
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      getAgenda();
    }
  };
  xhr.send(formData);
}

function toggleSlide(clickedElement) {
  const slidingItems = document.querySelectorAll(".cerah, .mendung, .hujan");
  const id = clickedElement.dataset.agendaId;
  const wasAlreadySlid = clickedElement.classList.contains("slid");

  // Slide reset
  slidingItems.forEach((el) => el.classList.remove("slid"));

  if (!wasAlreadySlid) {
    clickedElement.classList.add("slid");

    const xhr = new XMLHttpRequest();
    xhr.open(
      "GET",
      "./api/getAgendaInfo.php?id=" + encodeURIComponent(id),
      true
    );

    xhr.onreadystatechange = function () {
      if (xhr.readyState === 4 && xhr.status === 200) {
        try {
          const data = JSON.parse(xhr.responseText);

          const [tanggalMulai, jamMulai] = data.tanggalMulaiAgenda.split(" ");
          const [tanggalSelesai, jamSelesai] =
            data.tanggalSelesaiAgenda.split(" ");

          document.getElementById("title").value = data.namaAktivitas;
          document.getElementById("startingHour").value = jamMulai;
          document.getElementById("endHour").value = jamSelesai;
          document.getElementById("agendaDate").value = tanggalMulai;
          document.getElementById("endagendaDate").value = tanggalSelesai;
          document
            .getElementById("inputButton")
            .setAttribute("onclick", `updateAgenda(${id})`);
        } catch (e) {
          console.error("Failed to parse JSON:", e);
        }
      }
    };

    xhr.send();
  } else {
    clearForm(); // Deselect agenda and reset form
  }
}

function updateAgenda(id) {
  var title = document.getElementById("title").value;
  var startingHour = document.getElementById("startingHour").value;
  var endHour = document.getElementById("endHour").value;
  var agendaDate = document.getElementById("agendaDate").value;
  var endDate = document.getElementById("endagendaDate").value;
  if (!endDate.trim()) {
    endDate = agendaDate;
  }
  var repeat = document.getElementById("repeat").value;

  if (
    !title.trim() ||
    !startingHour.trim() ||
    !endHour.trim() ||
    !agendaDate.trim() ||
    !repeat.trim()
  ) {
    alert("Please fill out all fields.");
    return;
  }
  var data = new URLSearchParams({
    id: id,
    agendaTitle: title,
    startingHour: startingHour,
    endingHour: endHour,
    startingDate: agendaDate,
    endingDate: endDate,
    repeat: repeat,
  });
  var xhr = new XMLHttpRequest();
  xhr.open("POST", "./api/updateAgenda.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      getAgenda();
      clearForm();
    }
  };
  xhr.send(data.toString());
}

window.onload = function () {
  realtimeWeather();
  forecastperhour();
  getAgenda();
  updateClock();
  forecastdaily();
  setTimeout(function () {
    document.body.classList.remove("loading");
    document.body.classList.add("loaded");
  }, 5000);
};
