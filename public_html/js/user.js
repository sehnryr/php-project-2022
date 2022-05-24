import { getCookie, deleteCookie } from './utils.js'

// onload
$(() => {
	let cookie = getCookie('docto_session')
	if (cookie.length > 0) {

		// set user name
		$.ajax("api.php/user", {
			method: "GET", headers: {
				Authorization: 'Bearer ' + cookie
			}
		}).done((data) => {
			$("#username").html(data['firstname'] + ' ' + data['lastname'])
		})

		// set past appointments
		$.ajax("api.php/appointments", {
			method: "GET", headers: {
				Authorization: 'Bearer ' + cookie
			}
		}).done((data) => {
			// past appointments
			data.forEach((element) => {
				if (element['user_id'] != null && new Date(element['date_time']) < new Date()) {
					let card = `
					<div class="card mt-2" style="width: auto; margin:1em">
					<div class="card-body">
					<h5 class="card-title"><span class="badge rounded-pill text-black" style="background-color: #C4C4C4;">
					<img src="public_html/img/calendar_month_FILL0_wght400_GRAD0_opsz48.svg" alt="calendar">`
						+ new Date(element['date_time']).toDateString() +
						`<img src="public_html/img/schedule_FILL0_wght400_GRAD0_opsz48.svg" alt="clock">`
						+ element['date_time'].split(' ')[1] +
						`</span></h5><p class="card-text">Dr. `
						+ element['firstname'] + ' ' + element['lastname'] +
						`</p><p class="card-text">Spécialté : `
						+ element['specialty_name'] + `</p>
						<form method="GET" action="search.html">
						<input type="text" name="spe" value="`
						+ element['specialty_id'] + `" hidden>
						<input type="text" name="nom" value="`
						+ element['firstname'] + ' ' + element['lastname'] + `" hidden>
						<button class=" border-0" style="border-radius:5px; width:10em; background-color: #F6C844;">
						Reprendre un RDV ?
						</button>
						</form>
						</div></div>`

					$("#pastAppointments").append(card)
				}
			})
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