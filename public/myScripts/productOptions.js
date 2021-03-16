var all_choices = [];
let current_length = 0

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
    var x = document.getElementById("product_options_choices");
    var j = document.getElementById("input_for_choices");
    if (j.value === null || j.value === "" || j.value.trim() === "") {
        alert("Inserer le choix")
        j.value = null;
        return;
    }
    for (i = 0; i < x.length; ++i) {
        //alert(x.options[i].value)
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

function _save() {
    var name = document.getElementById("nom").value;
    if (name === null || name === "" || name.trim() === "") {
        alert("Inserer le nom du l'option")
        document.getElementById("nom").value = null;
        return;
    }
    for (var f = 0; f < all_choices.length; f++) {
        if (all_choices[f]['nom'] === name) {
            alert("Le nom du l'option doit être unique");
            document.getElementById("nom").value = null;
            return
        }
    }

    for (var k = 0; k < option_names.length; k++) {
        if (option_names[k] === name) {
            alert("Le nom du l'option doit être unique");
            document.getElementById("nom").value = null;
            return
        }
    }
    var elements = document.getElementById("product_options_choices");
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
        json_obj = {nom: nom.value, choices: results, selectedNbChoices: selectedNbChoices, product:product_id};
        all_choices.push(json_obj);


        var choicesElement = "<div class='element-box' id='row_for_" + nom.value + "' ><div class='form-desc'><table class='table'><tbody><tr><th>Nom du l'option</th><td>" + nom.value + "</td></tr><tr><th>Les choix</th><td><ul id='choices_for_azeae'>"
        results.forEach(option => choicesElement += "<li style='width:100%;word-break:break-all;overflow-wrap: break-word;height: fit-content;'><h5 style='color: #ccd9e8;'><h6>" + option + "</h6></li>");
        choicesElement += "</ul></td></tr><tr><th>Nombre maximum de choix à selectionner</th><td>" + selectedNbChoices + "</td></tr><tr><th>Actions</th><td><div>"
        choicesElement += "<button class='btn btn-primary' data-num='' name='" + nom.value + "' id='Edit_" + nom.value + "' type='button' '>Modifier</button><button class='btn btn-danger' data-num='' id='Delete_" + nom.value + "' type='button' '>Supprimer</button></div></td></tr></tbody></table></div></div>"
        $("#newOptions_container").append(choicesElement);


        editElement = $("#Edit_" + nom.value)
        editElement.data("num", json_obj);
        editElement.on("click", function () {
            var jsonObj = $(this).data("num");
            var choicesElement = document.getElementById("product_options_choices")
            var choicesInputElement = document.getElementById("input_for_choices")
            var nomElement = document.getElementById("nom")
            //clearing old choices
            $('#product_options_choices').children().remove().end()
            nomElement.value = null;
            choicesInputElement.value = null;
            //adding the selected object choices to the element for edit
            //alert("heeeeere" + jsonObj["selectedNbChoices"])
            setCurrent_length(jsonObj["choices"].length, jsonObj["selectedNbChoices"])
            for (var x = 0; x < jsonObj["choices"].length; x++) {
                var opt = document.createElement('option');
                opt.appendChild(document.createTextNode(jsonObj["choices"][x]));
                opt.value = jsonObj["choices"][x];
                opt.selected = true;
                choicesElement.appendChild(opt);
            }
            //adding the selected object name for edit
            nomElement.value = jsonObj["nom"];
            //delete from json
            for (var t = 0; t < all_choices.length; t++) {
                //alert($(this).data("num")["nom"])
                if (all_choices[t]["nom"] === $(this).data("num")["nom"]) {
                    all_choices.splice(t, 1)
                    t--
                }
            }
            document.getElementById("row_for_" + $(this).data('num')['nom']).remove();
        });


        editElement = $("#Delete_" + nom.value)
        editElement.data("num", json_obj);
        editElement.on("click", function () {
            //alert("the element is"+JSON.stringify($(this).data("num")))
            /*
            for (var k = 0; k < all_choices.length; k++) {
                alert("current table"+JSON.stringify(all_choices[k]))
            }
            */
            //delete from json
            for (var t = 0; t < all_choices.length; t++) {
                //alert($(this).data("num")["nom"])
                if (all_choices[t]["nom"] === $(this).data("num")["nom"]) {
                    all_choices.splice(t, 1)
                    t--
                }
            }
            /*
            for (var k2 = 0; k2 < all_choices.length; k2++) {
                alert("after delete table"+JSON.stringify(all_choices[k2]))
            }

             */
            //delete from view
            document.getElementById("row_for_" + $(this).data('num')['nom']).remove();
        });

    } else {
        alert("Remplir les choix");
        return
    }
    $('#product_options_choices').children().remove().end()
    document.getElementById("input_for_choices").value = null;
    document.getElementById("nom").value = null;

    setCurrent_length(0)
}

function _delete() {
    var x = document.getElementById("product_options_choices");
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

    alert("am clicked")
    if(all_choices.length === 0){

        $.ajax({
            type: "POST",
            url: "/productOptions/list",//post how you get this URL please...
            data: {array: [], product_id: product_id},//jQ will sort this out for you
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
    e.preventDefault();
    $.ajax({
        type: "POST",
        url: "/productOptions/list",//post how you get this URL please...
        data: {array: all_choices, product_id: product_id},//jQ will sort this out for you
        success: function (response) {
            console.log('aaaaa');
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
});

