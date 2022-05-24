$.ajax("api.php/specialties", {
    method: "GET"
}).done((data) => {
    console.log(data);
    data.forEach(item => {
        console.log(item);
        $('#spe').append('<option value="'+item['id']+'">'+item['name']+'</option>')
    });
})
