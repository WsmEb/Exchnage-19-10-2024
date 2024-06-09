function info(id, value) {
    document.getElementById(id).value = value;
}
function info_devise(id, value) {
    document.getElementById(id).value = value;
    TotalDevises(value);
}
function info_bloque(id, value, info) {
    document.getElementById(id).value = value;
    document.getElementById("verrouiller").innerHTML = info;
}

function info_update(username, nom, localisation, commentaire) {
    document.getElementById("username_update").value =
        username !== null ? username : "";
    document.getElementById("nom_update").value = nom !== "null" ? nom : "";
    document.getElementById("localisation_update").value =
        localisation !== "null" ? localisation : "";
    document.getElementById("commentaire_update").value =
        commentaire !== "null" ? commentaire : "";
}


function TotalDevises(client) {
    var x = new XMLHttpRequest();
    x.open("GET", `/totaldesvises/${client}`, true);
    x.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            var response = JSON.parse(this.responseText);
            let soldes_devises = response.soldes_devises;
            let entreprise = response.entreprise;
            // console.log(soldes_devises)
            document.getElementById("soldesdevises").innerHTML = "";
            if (soldes_devises.length != 0) {
                ligne = "";
                for (const i in soldes_devises) {
                    ligne += "<tr>";
                    ligne += `<td>${soldes_devises[i][0]}</td>`;
                    ligne += `<td class='text-end'>${soldes_devises[
                        i
                    ][1].toFixed(2)}</td>`;
                    ligne += `<td class='text-end'>${soldes_devises[
                        i
                    ][2].toFixed(2)} ${entreprise.base_devise}</td>`;
                    ligne += "</tr>";
                }
                document.getElementById("soldesdevises").innerHTML += ligne;
            } else document.getElementById("soldesdevises").innerHTML = ``;
        }
    };

    document.getElementById("soldesdevises").innerHTML =
        "<tr><td colspan='3' class='text-center'><div class='loading'></div></td></tr>";
    x.send();
}
setTimeout(function () {
    document.querySelector("#autoCloseAlert").style.display = "none";
}, 5000);
