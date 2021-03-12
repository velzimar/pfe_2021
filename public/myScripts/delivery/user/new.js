json = [
    {
        country: "tunis",
        cities: [{name: "tunis1", selected: false}, {name: "tunis2", selected: false}, {
            name: "tunis3",
            selected: false
        }],
    },
    {
        country: "nabeul",
        cities: [{name: "nabeul1", selected: false}, {name: "nabeul2", selected: false}, {
            name: "nabeul3",
            selected: false
        }],
    },
    {
        country: "sousse",
        cities: [{name: "sss1", selected: false}, {name: "nsssl2", selected: false}, {
            name: "sssl3",
            selected: false
        }],
    }
]

let activation = false;
let cout = 0;
let seuil = 0;

input_json = {
    activation: false,
    cout: 0,
    seuil: 0
}

let select_country = $("#select_country")
let select_city = $("#select_city")

let country = 0
let city = []

//init countries
$.each(json, function (k, v) {
    var o = new Option(v["country"], k)
    $(o).html(v["country"])
    select_country.append(o)
});
//init cities
$.each(json[0]["cities"], function (k, v) {
    //alert(k+v)
    //alert(json[0]["cities"][k]["name"])
    var o = new Option(json[0]["cities"][k]["name"], k)
    $(o).html(json[0]["cities"][k]["name"]).prop("selected", json[0]["cities"][k]["selected"])
    select_city.append(o)
});

function update_activation(a) {
    activation = a
    input_json["activation"] = a
    // alert(activation)
    //  alert(input_json["activation"])
}

function update_cout(a) {
    cout = a
    input_json["cout"] = a
    // alert(cout)
    //alert(input_json["cout"])
}

function update_seuil(a) {
    seuil = a
    input_json["seuil"] = a
    // alert(seuil)
    // alert(input_json["seuil"])
}

function update_country(c) {
    country = c
    // alert(country)
}

function update_city(c) {
    if (c.length === 0)
        return
    $.each(json[country]["cities"], function (k, v) {
        json[country]["cities"][k]["selected"] = false
    });
    //  alert(c)
    $.each(c, function (key, val) {
        //  alert(val)
        json[country]["cities"][val]["selected"] = true
    });

    city = c
    //alert(city)
}


select_country.on("change", function () {

    update_city([])
    select_city.children().remove()
    update_country($(this).val())
    //alert(country)

    $.each(json[country]["cities"], function (k, v) {
        // alert(k+v)
        //alert(json[country]["cities"][k]["name"])
        var o = new Option(json[country]["cities"][k]["name"], k)
        $(o).html(json[country]["cities"][k]["name"]).prop("selected", json[country]["cities"][k]["selected"])
        select_city.append(o)
    });

})
select_city.on("change", function () {
    select_city_values = []
    select_city_values = $(this).val()
    // alert(select_city_values)

    update_city(select_city_values)
})


function _delete() {
    //alert("unselect")
    select_city.children("option").prop("selected", false)
    $.each(json[country]["cities"], function (k, v) {
        json[country]["cities"][k]["selected"] = false
    });

}

function _reset() {
    //alert("unselectall")
    select_city.children("option").prop("selected", false)
    json = [
        {
            country: "tunis",
            cities: [{name: "tunis1", selected: false}, {name: "tunis2", selected: false}, {
                name: "tunis3",
                selected: false
            }],
        },
        {
            country: "nabeul",
            cities: [{name: "nabeul1", selected: false}, {name: "nabeul2", selected: false}, {
                name: "nabeul3",
                selected: false
            }],
        },
        {
            country: "sousse",
            cities: [{name: "sss1", selected: false}, {name: "nsssl2", selected: false}, {
                name: "sssl3",
                selected: false
            }],
        }
    ]
}

function _check_json_content() {
    if (json.length === 0) alert("check => json is empty")
    var jsoncheck = []
    for (var k = 0; k < json.length; k++) {
        jsoncheck.push(JSON.stringify(json[k]))
    }
    alert("check => " + jsoncheck)


    jsoncheck = []
    jsoncheck.push(JSON.stringify(input_json))

    alert("check => " + jsoncheck)
}

function _verifCout() {
    element = $("#cout")
    value = parseFloat(element.val())
    if (Number.isInteger(value) && value >= 0 && value.toString().length <= 11) {
        // alert("pass")
        update_cout(parseInt(value))
    } else {
        alert("Vérifier le cout ")
        element.val(0)
        update_cout(0)
    }
}

function _verifSeuil() {
    element = $("#seuil")
    value = parseFloat(element.val())
    if (Number.isInteger(value) && value >= 0 && value.toString().length <= 11) {
        //  alert("pass")
        update_seuil(parseInt(value))
    } else {
        alert("Vérifier le seuil")
        element.val(0)
        update_seuil(0)
    }
}


function _toggleActive() {
    _switch = $("#flexSwitchCheckDefault")
    if (_switch.is(':checked')) {
        switchStatus = _switch.is(':checked');
        update_activation(false)
    } else {
        switchStatus = _switch.is(':checked');
        update_activation(true)
    }

}


function _fill() {
    livraison_div = $("#_livraison").html("Livraison " + (input_json["activation"] === true ? "activé" : "désactivé"))
    cout_div = $("#_cout").html("Cout " + (input_json["cout"]) + " dt <br>Seuil " + (input_json["seuil"]) + " dt")

    ville_div = $("#_ville").html("")
    $.each(json, function (k, v) {
        var this_city = ""
        this_country = json[k]["country"]
        //alert(json[k]["country"])
        $.each(json[k]["cities"], function (a, s) {
            if (json[k]["cities"][a]["selected"]) {

                this_city += json[k]["cities"][a]["name"] + " ,"
            }
            //alert(json[k]["cities"][a]["name"])

        })
        if (this_city !== "")
            ville_div.append("<li>" + this_country + "</li>" + this_city.slice(0, -1))
    })
}

$("#post-btn").click(function (e) {
    //alert("am clicked")
    e.preventDefault();
    $.ajax({
        type: "POST",
        url: "/delivery/myDelivery/" + user_id + "/response",//post how you get this URL please...
        data: {user: user_id, array: json, info: input_json},//jQ will sort this out for you
        success: function (response) {
            console.log("sucees");
            console.log(response.msg);

            window.location.href = (response.redirect);
        },
        error: function () {
            console.log('an error occured');
        }
    });
});