function validate(form){
    //https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/hasOwnProperty
    //Technically this check isn't necessary since we're not using it across different forms, but
    //it's to show an example of how you can check for a property so you can reuse a validate function for similar
    //forms.
    errors = [];
    if(form.hasOwnProperty("name") && form.hasOwnProperty("quantity")){
        if (form.name.value == null ||
            form.name.value == undefined ||
            form.name.value.length == 0) {
            errors.push("Name must not be empty");
        }
        if (form.quantity.value == null ||
            form.quantity.value == undefined ||
            form.quantity.value.length == 0 || form.quantity.value < 0) {
            errors.push("Quantity must not be empty and a positive number");
        }
    }
    if(errors.length > 0){
        alert(errors);
        return false;//prevent form submission
    }
    return  true;//allow form submission
}