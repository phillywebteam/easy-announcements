/* ----- Cookie helpers ----- */

function get_easy_announcements_cookie(cookie) {
	if (typeof Cookies.get('easy_announcements') === 'undefined') {
		set_easy_announcements_cookie();
	}
	var raw = Cookies.get('easy_announcements');
	var jar = JSON.parse(atob(raw));
	return (typeof jar[cookie] !== 'undefined') ? jar[cookie] : '';
}

function check_easy_announcements_cookie(cookie) {
	var val = get_easy_announcements_cookie(cookie);
	return (val !== '' && val !== 'false');
}

function set_easy_announcements_cookie(callback) {
	var jar = {};
	jQuery.each(announcement_ids, function(i, id) {
		jar['dismiss-' + id] = 'false';
	});
	Cookies.set('easy_announcements', btoa(JSON.stringify(jar)), { expires: 7 });
	if (typeof callback === 'function') setTimeout(callback, 100);
}

function update_easy_announcements_cookie(cookie, value, callback) {
	if (typeof Cookies.get('easy_announcements') === 'undefined') {
		set_easy_announcements_cookie();
	}
	var raw = Cookies.get('easy_announcements');
	var jar = JSON.parse(atob(raw));
	jar[cookie] = value;
	Cookies.set('easy_announcements', btoa(JSON.stringify(jar)), { expires: 7 });
	if (typeof callback === 'function') setTimeout(callback, 100);
}

/* ----- Modal helpers ----- */

var eaBackdrop = null;

function eaShowModal(modal_id) {
	var $modal = jQuery('#' + modal_id);
	if (!$modal.length) return;

	if (!eaBackdrop) {
		eaBackdrop = jQuery('<div class="ea-modal-backdrop show" aria-hidden="true"></div>').appendTo('body');
	}

	$modal.addClass('show').removeAttr('aria-hidden').attr('aria-modal', 'true');
	jQuery('body').addClass('ea-modal-open');
	$modal.trigger('focus');
}

function eaDismissModal($modal) {
	$modal.removeClass('show').removeAttr('aria-modal').attr('aria-hidden', 'true');

	if (jQuery('.ea-modal.show').length === 0 && eaBackdrop) {
		eaBackdrop.remove();
		eaBackdrop = null;
		jQuery('body').removeClass('ea-modal-open');
	}

	$modal.trigger('ea.modal.dismiss');
}

/* ----- Event bindings ----- */

jQuery(function($) {

	// Banner: dismiss
	$(document).on('click', '.site-announcements .announcement .dismiss', function(event) {
		event.preventDefault();
		var id = $(this).attr('href').replace('#dismiss-', '');
		update_easy_announcements_cookie('dismiss-' + id, 'true', function() {
			$('.announcement-' + id).slideUp();
		});
	});

	// Modal: close button
	$(document).on('click', '[data-ea-dismiss="modal"]', function() {
		eaDismissModal($(this).closest('.ea-modal'));
	});

	// Modal: backdrop click
	$(document).on('click', '.ea-modal-backdrop', function() {
		eaDismissModal($('.ea-modal.show').first());
	});

	// Modal: ESC key
	$(document).on('keydown', function(e) {
		if (e.key === 'Escape') {
			var $open = $('.ea-modal.show').first();
			if ($open.length) eaDismissModal($open);
		}
	});

});
