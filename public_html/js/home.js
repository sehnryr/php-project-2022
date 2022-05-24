$.ajax("api.php/specialties", {
    method: "GET"
}).done((data) => {
    data.forEach(item => {
        $('#spe').append('<option value="'+item['id']+'">'+item['name']+'</option>')
    });
});

$('#search').click(() => {
    if($('#spe').val() == "" && $('#nom').val() == ""){
        $('#search_form').append('<div class="alert alert-warning" role="alert">Veuillez remplir soit le nom ou choisir une spécialité!</div>');
    }else{
        window.location.href = 'search.html?spe='+$('#spe').val()+'&nom='+$('#nom').val()+'&ou='+$('#ou').val();
    }
});