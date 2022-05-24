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
			let pastAppointments = []

			data.forEach((element) => {
				if (element['user_id'] != null && new Date(element['date_time']) < new Date()) {
					pastAppointments.push(element)
				}
			})

			if (pastAppointments.length > 0) {
				$('#pastAppointments').html('')
				pastAppointments.forEach((element) => {
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
				})
			}


			// current appointments
			let currentAppointments = []

			data.forEach((element) => {
				if (element['user_id'] != null && new Date(element['date_time']) >= new Date()) {
					currentAppointments.push(element)
				}
			})

			if (currentAppointments.length > 0) {
				$('#currentAppointments').html('')
				currentAppointments.forEach((element) => {
					let card = `
					<div class="card mt-2" style="width: 18rem;">
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
						<form class="cancelAppointment">
						<button type="submit" value="`
						+ element['id'] + `" class="bg-danger text-white border-0" style="transform: translate(7vw)">
						Annuler le rdv ?
						</button>
						</form>
						</div></div>`

					$("#currentAppointments").append(card)
				})
			}

			$(".cancelAppointment").on("submit", (event) => {
				event.preventDefault()
				let appointmentId = $(event.target.firstElementChild).val()
				console.log(appointmentId)
				$.ajax("api.php/appointment", {
					method: "DELETE", headers: {
						Authorization: 'Bearer ' + cookie
					}, data: {
						id: appointmentId
					}
				})
				$(event.target.parentElement.parentElement).remove()
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
