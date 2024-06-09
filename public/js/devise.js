function info(id, value) {
    document.getElementById(id).value = value;
}

function info_update(symbol, description, base) {
    document.getElementById("symbol_update").value = symbol;
    document.getElementById("description_update").value = description;
    document.getElementById("base_update").value = base;
}

function rechercher(input) {
    let val = input.value;
    var x = new XMLHttpRequest();

    if (val === "") x.open("GET", `/devise/search/`, true);
    else x.open("GET", `/devise/search/${val}`, true);
    x.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            // console.log(this.responseText);
            devise = JSON.parse(this.responseText);
            document.getElementById("resultats").innerHTML = "";
            if (Clients.length != 0) {
                for (const i in devise) {
                    ligne = "<tr>";
                    ligne += `<td>${devise[i].symbol}</td>`;
                    ligne += `<td>${devise[i].description}</td>`;
                    ligne += `<td>${devise[i].base}</td>`;
                    ligne += `<td></td>`;
                    ligne += `<td> 
                            
                            <a class="btn btn-danger" href="#deletedevise" data-bs-toggle="modal" onclick="info('Symbol_delete','${devise[i].symbol}')"><i class="bi bi-trash"></i></a>
                            <a class="btn btn-warning" href="#updatedevise" data-bs-toggle="modal" onclick="info_update('${devise[i].symbol}','${devise[i].description}','${devise[i].base}')"><i class="bi bi-pencil"></i></a>
                        </td>`;
                    ligne += "</tr>";
                    document.getElementById("resultats").innerHTML += ligne;
                }
            } else
                document.getElementById(
                    "resultats"
                ).innerHTML = `<tr><td colspan="7" class="alert alert-danger text-center fw-bold p-3 text-light">N'est aucun client</td></tr>`;
        }
    };
    x.send();
}

function saveCurrency() {
    var selectedCurrency = document.getElementById("currencySelect").value;
    $.ajax({
        url: "{{ route('entreprise.updateBaseDevise') }}",
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            selectedCurrency: selectedCurrency,
        },
        success: function (response) {
            // Handle success response
            console.log(response);
        },
        error: function (xhr) {
            // Handle error response
            console.log(xhr.responseText);
        },
    });
    // Close the modal
    $("#currencyModal").modal("hide");
}

// Ensure that the element with ID 'autoCloseAlert' exists before accessing its style property
var autoCloseAlert = document.querySelector("#autoCloseAlert");
if (autoCloseAlert) {
    setTimeout(function () {
        autoCloseAlert.style.display = "none";
    }, 5000);
}

setTimeout(function () {
    document.querySelector("#autoCloseAlert").style.display = "none";
}, 5000);


