//from user
/*
let price_id = "input_for_prices"
let choice_input_native_id = "input_for_choices"
let choices_native_id = "product_options_choices"
let choice_number_native_id = "nbChoix"
let name_id = "nom"
let ajax_post_url = "/productOptions/list"
let ajax_post_type = "POST"
let post_button_native_id = "post-btn"
*/
//from script

let all_choices = [];
let current_length = 0

let price_native = document.getElementById(price_id);

let choice_input_jquery_id = "#" + choice_input_native_id
let choice_input_native = document.getElementById(choice_input_native_id);
let choice_input_jquery = $(choice_input_jquery_id)

let choices_jquery_id = "#" + choices_native_id
let choices_native = document.getElementById(choices_native_id);
let choices_jquery = $(choices_jquery_id);

let name_input_native = document.getElementById(name_id)

let choice_number_jquery_id = "#" + choice_number_native_id
let choice_number_jquery = $(choice_number_jquery_id)

let post_button_jquery_id = "#" + post_button_native_id
let jquery_post_button = $(post_button_jquery_id)

let container_jquery_id = "#"+container_id;
let container_jquery = $(container_jquery_id)

function setCurrent_length(y, selectedElement) {
    //alert("selectedelement " + selectedElement)
    choice_number_jquery.children().remove().end()
    current_length = y;
    for (let i = 1; i <= y; i++) {
        if (i.toString() === selectedElement) {
            // alert("i= " + i + " selected: " + selectedElement)
            choice_number_jquery.append($("<option selected    />").val(i).text(i));
        } else
            choice_number_jquery.append($("<option     />").val(i).text(i));
    }
    //alert(current_length)
}

function _add() {
    if (price_native.value === '' || parseFloat(price_native.value) <= 0) {
        alert("Vérifier le prix");
        return;
    }

    if (choice_input_native.value === null || choice_input_native.value === "" || choice_input_native.value.trim() === "") {
        alert("Inserer le choix")
        choice_input_native.value = null;
        return;
    }

    for (i = 0; i < choices_native.length; ++i) {
        //alert(choices_native.options[i].value)
        if (choices_native.options[i].value === choice_input_native.value.trim()) {
            alert("Choix existe déja");
            choice_input_native.value = null;
            return;
        }
    }



    choices_jquery.append("<option selected value='" + choice_input_native.value.trim() + "' data-price='" + parseFloat(price_native.value) + "' data-name='" + choice_input_native.value.trim() + "' >" + choice_input_native.value.trim() + " à " + parseFloat(price_native.value) + " dt" + "</option>");
    choice_input_jquery.val("");
    price_native.value = null;
    setCurrent_length(current_length + 1)


}

function _save() {
    if (name_input_native.value === null || name_input_native.value === "" || name_input_native.value.trim() === "") {
        alert("Inserer le nom de l'option")
        name_input_native.value = null;
        return;
    }
    for (let f = 0; f < all_choices.length; f++) {
        if (all_choices[f]['nom'] === name_input_native.value.trim()) {
            alert("Le nom de l'option doit être unique");
            name_input_native.value = null;
            return
        }
    }

/*
    for (let k = 0; k < option_names.length; k++) {
        if (option_names[k] === name_input_native.value.trim()) {
            alert("Le nom du l'option doit être unique");
            name_input_native.value = null;
            return
        }
    }
*/

    let responseStatus = false
    let data_to_check = {product_id: product_id, option_name: name_input_native.value.trim()}

    $.ajax({
        type: check_type,
        async: false,
        url: check_url,
        data: data_to_check,//jQ will sort this out for you
        success: function (response) {
            console.log('empty');
            if (response.success) {
                responseStatus = true;
            }else{
                alert("Le nom de l'option doit être unique")
                responseStatus = false;
            }
        },
    });
    if(!responseStatus) return;

    let selectedNbChoices = $(choice_number_jquery_id + " option:selected").val();
    let json_obj = {};
    let results = [];
    let es = $(choices_jquery_id + " option");
    $.map(es, e => {
        alert($(e).val());
        alert($(e).data('price'))
        results.push({name: $(e).data('name'), price: $(e).data('price')});
    })

    //alert(results);
    if (results.length > 0) {
        json_obj = {
            nom: name_input_native.value.trim(),
            choices: results,
            selectedNbChoices: selectedNbChoices,
            product: product_id
        };
        all_choices.push(json_obj);

        function delete_from_json(obj) {
            //delete from json
            for (let t = 0; t < all_choices.length; t++) {
                //alert($(this).data("num")["nom"])
                if (all_choices[t]["nom"] === obj.data("num")["nom"]) {
                    all_choices.splice(t, 1)
                    t--
                }
            }
        }

        let choicesElement = "<div class='element-box' id='row_for_" + name_input_native.value + "' ><div class='form-desc'><table class='table'><tbody><tr><th>Nom du l'option</th><td>" + name_input_native.value + "</td></tr><tr><th>Les choix</th><td><ul id='choices_for_azeae'>"
        results.forEach(option => choicesElement += "<li style='width:100%;word-break:break-all;overflow-wrap: break-word;height: fit-content;'><h5 style='color: #ccd9e8;'><h6>" + option["name"] + " à " + option["price"] + " dt</h6></li>");
        choicesElement += "</ul></td></tr><tr><th>Nombre maximum de choix à selectionner</th><td>" + selectedNbChoices + "</td></tr><tr><th>Actions</th><td><div>"
        choicesElement += "<button class='btn btn-primary' data-num='' name='" + name_input_native.value + "' id='Edit_" + name_input_native.value + "' type='button' '>Modifier</button><button class='btn btn-danger' data-num='' id='Delete_" + name_input_native.value + "' type='button' '>Supprimer</button></div></td></tr></tbody></table></div></div>"
        container_jquery.append(choicesElement);

        let editElement = $("#Edit_" + name_input_native.value)
        editElement.data("num", json_obj);
        editElement.on("click", function () {
            let jsonObj = $(this).data("num");
            //clearing old choices
            choices_jquery.children().remove().end()
            name_input_native.value = null;
            choice_input_native.value = null;
            //adding the selected object choices to the element for edit
            setCurrent_length(jsonObj["choices"].length, jsonObj["selectedNbChoices"])
            for (let inc = 0; inc < jsonObj["choices"].length; inc++) {
                choices_jquery.append("<option selected value='" + jsonObj['choices'][inc]['name'] + "' data-price='" + jsonObj['choices'][inc]['price'] + "' data-name='" + jsonObj['choices'][inc]['name'] + "' >" + jsonObj['choices'][inc]['name'] + " à " + jsonObj['choices'][inc]['price'] + " dt" + "</option>");
            }
            //adding the selected object name for edit
            name_input_native.value = jsonObj["nom"];
            delete_from_json($(this))
            document.getElementById("row_for_" + $(this).data('num')['nom']).remove();
        });


        let deleteElement = $("#Delete_" + name_input_native.value)
        deleteElement.data("num", json_obj);
        deleteElement.on("click", function () {
            //alert("the element is"+JSON.stringify($(this).data("num")))
            delete_from_json($(this))
            //delete from view
            document.getElementById("row_for_" + $(this).data('num')['nom']).remove();
        });

    } else {
        alert("Remplir les choix");
        return
    }
    choices_jquery.children().remove().end()
    choice_input_native.value = null;
    name_input_native.value = null;
    setCurrent_length(0)
}

function _delete() {
    choices_native.remove(choices_native.selectedIndex);
    setCurrent_length(current_length - 1)
}

function _check_json_content() {
    if (all_choices.length === 0) alert("check => json is empty")
    let jsoncheck = []
    for (let k = 0; k < all_choices.length; k++) {
        jsoncheck.push(JSON.stringify(all_choices[k]))
    }
    alert("check => " + jsoncheck)
}


jquery_post_button.click(function (e) {
    e.preventDefault();
    let data_to_send
    if (all_choices.length === 0) {
        if (typeof user_id === 'undefined')
            data_to_send = {array: [], product_id: product_id}
        else
            data_to_send = {array: [], product_id: product_id, user_id: user_id}

        $.ajax({
            type: ajax_post_type,
            url: ajax_post_url,//post how you get this URL please...
            data: data_to_send,//jQ will sort this out for you
            success: function (response) {
                console.log('empty');
                if (response.success) {
                    console.log('success');
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
    } else {
        if (typeof user_id === 'undefined')
            data_to_send = {array: all_choices, product_id: product_id}
        else
            data_to_send = {array: all_choices, product_id: product_id, user_id: user_id}
        $.ajax({
            type: ajax_post_type,
            url: ajax_post_url,//post how you get this URL please...
            data: data_to_send,//jQ will sort this out for you
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
    }
});