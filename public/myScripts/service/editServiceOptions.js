var all_choices = [];

//var current_length = 0
function setCurrent_length(y, selectedElement) {
    //alert("selectedelement " + selectedElement)
    let list = $("#nbChoix");
    list.children().remove().end()
    current_length = y;
    for (let i = 1; i <= y; i++) {
        if (i.toString() === selectedElement) {
            // alert("i= " + i + " selected: " + selectedElement)

            list.append($("<option selected    />").val(i).text(i));
        } else
            list.append($("<option     />").val(i).text(i));
    }
    //alert(current_length)
}

function _add() {
    var x = document.getElementById("service_options_choices");
    var j = document.getElementById("input_for_choices");
    if (j.value === null || j.value === "" || j.value.trim() === "") {
        alert("Inserer le choix")
        j.value = null;
        return;
    }
    for (i = 0; i < x.length; ++i) {
        if (x.options[i].value === j.value) {
            alert("Choix existe déja");
            j.value = null;
            return;
        }
    }
    var option = document.createElement("option")
    option.text = j.value;
    option.selected = true;
    x.add(option);
    j.value = null;
    setCurrent_length(current_length + 1)
}


function _delete() {
    var x = document.getElementById("service_options_choices");
    x.remove(x.selectedIndex);
    setCurrent_length(current_length - 1)
}

function _check_json_content() {
    if (all_choices.length === 0) alert("check => json is empty")
    var jsoncheck = []
    for (var k = 0; k < all_choices.length; k++) {
        jsoncheck.push(JSON.stringify(all_choices[k]))
    }
    alert("check => " + jsoncheck)
}

const button = document.getElementById('post-btn');


$("#post-btn").click(function (e) {

    e.preventDefault();
    var name = document.getElementById("nom").value;
    if (name === null || name === "" || name.trim() === "") {
        alert("Inserer le nom du l'option")
        document.getElementById("nom").value = null;
        return;
    }

    for (var k = 0; k < option_names.length; k++) {
        if (option_names[k] === name) {
            alert("Le nom du l'option doit être unique");
            document.getElementById("nom").value = null;
            return
        }
    }
    var elements = document.getElementById("service_options_choices");
    let selectedNbChoices = $("#nbChoix option:selected").val();
    var json_obj = {};
    var results = [];
    for (var i = 0; i < elements.length; i++) {
        var element = elements[i];
        //var strSel = element.options[element.selectedIndex].text;
        results.push(element.text);
    }
    //alert(results);
    if (results.length > 0) {
        var nom = document.getElementById("nom");
        json_obj = {nom: nom.value, choices: results, selectedNbChoices: selectedNbChoices, service: service_id};
        all_choices[0] = json_obj;
    } else {
        alert("Remplir les choix");
        return
    }
    //setCurrent_length(0)
    //alert(all_choices[0]["nom"])
    //alert(all_choices[0]["choices"])
    //alert(all_choices[0]["selectedNbChoices"])
    //alert(all_choices[0]["service"])

    //alert(all_choices.length);

    if (all_choices.length === 0) {
        $.ajax({
            type: "POST",
            url: "/serviceOptions/editOption",//post how you get this URL please...
            data: {array: [], service_id: service_id},//jQ will sort this out for you
            success: function (response) {
                console.log('empty');
                if (response.success) {
                    console.log('sucess');
                    window.location.href = response.redirect; // <-- HERE
                }
            },
            error: function (response) {
                console.log('an error occured');
                if (!response.success) {
                    console.log('fail');
                }
            }
        });
    }
    $.ajax({
        type: "POST",
        url: "/serviceOptions/editOption",//post how you get this URL please...
        data: {array: all_choices, service_id: service_id, option_id: parseInt(option_id)},//jQ will sort this out for you
        success: function (response) {
            console.log('a2');
            if (response.success) {
                console.log('sucess');
                window.location.href = response.redirect; // <-- HERE
            }
        },
        error: function (response) {
            console.log('error');
            if (!response.success) {
                console.log('fail');
            }
        },
        empty: function (empty) {
            console.log('empty');
            if (!empty.empty) {
                console.log('empty');
            }
        }
    });


});
