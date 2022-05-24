import { createCookie, getCookie } from './utils.js'


$("#loginForm").on("submit", (event) => {
	event.preventDefault()
	$.ajax("api.php/login", {
		method: "POST", data: {
			email: $("#emailLogin").val(),
			password: $("#passwordLogin").val()
		}
	}).done((data) => {
		createCookie('docto_session', data['access_token'])
		let url = window.location.href.replace(/login\.html.*/i, 'user.html')
		window.location.href = url
	})
})