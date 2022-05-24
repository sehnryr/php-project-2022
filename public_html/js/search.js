$.ajax("api.php/specialties", {
    method: "GET"
}).done((data) => {
    data.forEach(item => {
        $('#spe').append('<option value="'+item['id']+'">'+item['name']+'</option>')
    });
})