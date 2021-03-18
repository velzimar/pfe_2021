/*
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
    //added for price
    var price = document.getElementById("input_for_prices");
    if(price.value === '' || price.value === 0){
        alert("Vérifier le prix");
        return;
    }


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
    op = "<option selected value='"+j.value+"' data-price='"+price.value+"' data-name='"+j.value+"' >"+j.value + " à "+price.value+ " dt"+"</option>"

    $("#product_options_choices").append(op);


    $("#input_for_choices").val("");
    price.value = null;
    setCurrent_length(current_length + 1)
}



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
    var elements = document.getElementById("product_options_choices");
    let selectedNbChoices = $("#nbChoix option:selected").val();
    var json_obj = {};
    var results = [];
    var es = $("#product_options_choices option");
    var v = $.map(es, e => {
        alert($(e).val());alert($(e).data('price'))

        results.push({name:$(e).data('name'),price:$(e).data('price')});
    }  )
    //alert(results);
    if (results.length > 0) {
        var nom = document.getElementById("nom");
        json_obj = {nom: nom.value, choices: results, selectedNbChoices: selectedNbChoices, product: product_id};
        all_choices[0] = json_obj;
    } else {
        alert("Remplir les choix");
        return
    }
    //setCurrent_length(0)
    //alert(all_choices[0]["nom"])
    //alert(all_choices[0]["choices"])
    //alert(all_choices[0]["selectedNbChoices"])
    //alert(all_choices[0]["product"])

    //alert(all_choices.length);

    if (all_choices.length === 0) {
        $.ajax({
            type: "POST",
            url: "/productOptions/editOption",//post how you get this URL please...
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
    $.ajax({
        type: "POST",
        url: "/productOptions/editOption",//post how you get this URL please...
        data: {array: all_choices, product_id: product_id, option_id: parseInt(option_id)},//jQ will sort this out for you
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

*/

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
    let data_to_check = {product_id: product_id, option_name: name_input_native.value.trim(), option_id: option_id}

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
    } else {
        alert("Remplir les choix");
        return;
    }

    let data_to_send
    if(all_choices.length === 0)  all_choices = []

    if (typeof user_id === 'undefined' && typeof option_id === 'undefined')
        data_to_send = {array: all_choices, product_id: product_id}
    else if (typeof user_id !== 'undefined' && typeof option_id === 'undefined')
        data_to_send = {array: all_choices, product_id: product_id, user_id: user_id}
    else if (typeof user_id === 'undefined' && typeof option_id !== 'undefined')
        data_to_send = {array: all_choices, product_id: product_id, option_id: parseInt(option_id) }
    else
        data_to_send = {array: all_choices, product_id: product_id, user_id: user_id , option_id: parseInt(option_id)}

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
});