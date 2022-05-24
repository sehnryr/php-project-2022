import { getCookie, deleteCookie } from './utils.js'

// onload
$(() => {
	let cookie = getCookie('docto_session')
	if (cookie.length > 0) {
		$.ajax("api.php/user", {
			method: "GET", headers: {
				Authorization: 'Bearer ' + cookie
			}
		}).done((data) => {
			$("#username").html(data['firstname'] + ' ' + data['lastname'])
		})
	}
})

$("#disconnect").on("click", () => {
	let cookie = getCookie('docto_session')
	$.ajax("api.php/logout", {
		method: "POST", headers: {
			Authorization: 'Bearer ' + cookie
		}
	}).done((_) => {
		deleteCookie('docto_session')
		let url = window.location.href.replace(/user\.html.*/i, '')
		window.location.href = url
	})
})