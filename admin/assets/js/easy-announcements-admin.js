jQuery(function ($) {
	$('.easy-announcements-color-picker').wpColorPicker();

	var getUrl = window.location,
		baseUrl = getUrl.protocol + "//" + getUrl.host + "/",
		easy_announcements_navigate = '<div class="live-select-toolbar"><button type="button" class="button button-secondary" disabled><span class="dashicons dashicons-admin-site"></span></button><input type="url" class="regular-text" value="' + baseUrl + '"><button type="button" class="button button-secondary live-select-go"><span class="dashicons dashicons-arrow-right-alt"></span></button></div>';

	$('.selectors-input').each(function () {
		var wrap = $(this),
			wrap_type = wrap.attr('class').replace('selectors-input easy-announcements-', '').replace('-selector', ''),
			easy_announcements_iframe = '<iframe src="' + baseUrl + '?live-select=' + wrap_type + '" class="live-select-frame"></iframe>';

		$('.live-select-toggle', wrap).on('click', function () {
			$('.live-select-frame, .live-select-toolbar').remove();

			if (wrap.hasClass('live-select-active')) {
				$(this).val('Live Select');
				$('.selectors-input').removeClass('live-select-active');
			} else {
				$(this).val('Close');
				$('.selectors-input').removeClass('live-select-active');
				wrap.addClass('live-select-active');
				$('td', wrap).append(easy_announcements_navigate + easy_announcements_iframe);
			}
		});
		wrap.on('click submit', '.live-select-toolbar .live-select-go', function (event) {
			event.preventDefault();
			var url = $('.live-select-toolbar input[type=url]', wrap).val();

			$('.live-select-frame', wrap).attr('src', url + '?live-select');
		}).on('keypress', '.live-select-toolbar input[type=url]', function (event) {
			if (event.which == 13) {
				event.preventDefault();
				$('.live-select-toolbar .live-select-go', wrap).trigger('click');
			}
		});
	});

	window.addEventListener('message', function(evt) {
		if (typeof evt.data['channel'] !== undefined && evt.data['channel'] == 'live-select') {
			console.log(evt.data['selector']);
			$('.live-select-active input[type=button]').val('Live Select');
			$('.live-select-active input[type=text]').val(evt.data['selector']);
			$('.live-select-frame, .live-select-toolbar').remove();
			$('.selectors-input').removeClass('live-select-active');
		}
	}, false);
	
});