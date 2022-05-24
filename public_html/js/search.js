let getValues = new URLSearchParams(window.location.search);

$.ajax("api.php/specialties", {
    method: "GET"
}).done((data) => {
    data.forEach(item => {
        if(getValues.get('spe') == item['id']){
            $('#spe').append('<option value="'+item['id']+'" selected>'+item['name']+'</option>');
        }else{
            $('#spe').append('<option value="'+item['id']+'">'+item['name']+'</option>');
        }
    });
})

if(getValues.get('nom') != null){
    $('#nom').val(getValues.get('nom'));
}

if(getValues.get('ou') != null){
    $('#ou').val(getValues.get('ou'));
}

$.ajax("api.php/appointments", {
    method: "GET"
}).done((data) => {
    data.forEach(appointment => {
        
    });
})