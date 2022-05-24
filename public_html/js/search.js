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

$('#search').click(() => {
    if($('#spe').val() == "" && $('#nom').val() == ""){
        $('#search_form').append('<div class="alert alert-warning" role="alert">Veuillez remplir soit le nom ou choisir une spécialité!</div>');
    }else{
        window.location.href = 'search.html?spe='+$('#spe').val()+'&nom='+$('#nom').val()+'&ou='+$('#ou').val();
    }
});

$.ajax("api.php/appointments", {
    method: "GET"
}).done((data) => {
    data.forEach(appointment => {
        console.log(appointment);
        $('#resultField').append('<div class="card m-1" style="width: 18rem;">'+
        '<div class="card-body">'+
          '<h5 class="card-title">'+ appointment['firstname'] + ' '+ appointment['lastname'] +'</h5>'+
          '<h6 class="card-subtitle mb-2 text-muted">'+ appointment['specialty_name'] +'</h6>'+
          '<p class="card-text">'+ appointment['date_time'] +'</p>'+
          '<form>'+
            '<input type="text" class="form-control" name="appointment_id" value="'+ appointment['id'] +'"hidden>'+
            '<button class="btn btn-primary" id="set" name="setAppointment">Réserver le rdv</button>'+
          '</form>'+
        '</div>');
    });
});