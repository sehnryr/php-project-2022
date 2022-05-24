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