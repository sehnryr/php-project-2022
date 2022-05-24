import { createCookie, getCookie } from './utils.js'

// onload if session cookie exists, redirect to user.html
$(() => {
	let cookie = getCookie('docto_session')
	if (cookie.length > 0) {
		$.ajax("api.php/user", {
			method: "GET", headers: {
				Authorization: 'Bearer ' + cookie
			}
		}).done((_) => {
			let url = window.location.href.replace(/login\.html.*/i, 'user.html')
			window.location.href = url
		})
	}
})


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