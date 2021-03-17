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
    /*
    var option = document.createElement("option")
    option.value = j.value;
    option.text = j.value + " à "+price.value+ " dt";
    option.selected = true;
    option.data_price=price.value*/
    $("#product_options_choices").append(op);


    /*
    $("#product_options_choices").append($('<option />')  // Create new <option> element
            .val(j.value)            // Set value as "Hello"
            .text(j.value + " à "+price.value+ " dt")           // Set textContent as "Hello"
            .prop('selected', true)  // Mark it selected
            .data({                  // Set multiple data-* attributes
                price: price.value,
                name:j.value
            })
        );
    */
    $("#input_for_choices").val("");
    price.value = null;
    setCurrent_length(current_length + 1)
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
