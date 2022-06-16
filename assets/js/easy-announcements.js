function get_easy_announcements_cookie(cookie) {
	if (typeof Cookies.get('easy_announcements') == 'undefined') {
		set_easy_announcements_cookie();
	}
	var visitor_cookie = Cookies.get('easy_announcements'); // get the cookie
	visitor_cookie = atob(visitor_cookie); // decrypt it
	visitor_cookie = JSON.parse(visitor_cookie); // convert to array
	return (typeof visitor_cookie[cookie] != 'undefined') ? visitor_cookie[cookie] : ''; // return either the specific cookie value or return blank
}

function check_easy_announcements_cookie(cookie) {
	return (
		typeof get_easy_announcements_cookie(cookie) != 'undefined' // does it exist?
		&& get_easy_announcements_cookie(cookie) != '' // is it blank?
		&& get_easy_announcements_cookie(cookie) != 'false' // is it blank?
	) ? true : false;
}

function set_easy_announcements_cookie(callback) {
	var visitor_cookie = {};
	for (const id of announcement_ids) {
		visitor_cookie['dismiss-' + id] = 'false';
	}
	visitor_cookie = JSON.stringify(visitor_cookie); // turn into string
	visitor_cookie = btoa(visitor_cookie); // encrypt it
	Cookies.set('easy_announcements', visitor_cookie, { expires: 7 });
	if (typeof callback === 'function') setTimeout(callback(), 100);
}

function update_easy_announcements_cookie(cookie, value, callback) {
	if (typeof Cookies.get('easy_announcements') == 'undefined') {
		set_easy_announcements_cookie();
	}
	var visitor_cookie = Cookies.get('easy_announcements'); // get the cookie
	visitor_cookie = atob(visitor_cookie); // decrypt it
	visitor_cookie = JSON.parse(visitor_cookie); // convert to array
	visitor_cookie[cookie] = value; // update the value in the array
	visitor_cookie = JSON.stringify(visitor_cookie); // turn back into string
	visitor_cookie = btoa(visitor_cookie); // encrypt it
	Cookies.set('easy_announcements', visitor_cookie, { expires: 7 }); // set the cookie
	if (typeof callback === 'function') setTimeout(callback(), 100);
}

jQuery(function ($) {
	$(document).on('click', '.site-announcements .announcement .dismiss', function (event) {
		event.preventDefault();
		var announcement_id = $(this).attr('href').replace('#dismiss-', '');
		update_easy_announcements_cookie('dismiss-' + announcement_id, 'true', function () {
			$('.announcement-' + announcement_id).slideUp();
		});
	});
});