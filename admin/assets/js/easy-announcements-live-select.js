jQuery(function ($) {
	$('html').addClass('live-select');
	$('body').append('<div class="easy-announcements-live-selector"></div>');
	$(window).on('resize load', function () {
		var bWidth = $('body').outerWidth();
		$('body *:not(.easy-announcements-live-selector):not(.easy-announcements-live-selector *)').css('pointer-events', function () {
			if ($(this).outerWidth() < bWidth * 0.95) return 'none';
		});
	});
	function getUrlVars() {
		var vars = [], hash;
		var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
		for (var i = 0; i < hashes.length; i++) {
			hash = hashes[i].split('=');
			vars.push(hash[0]);
			vars[hash[0]] = hash[1];
		}
		return vars;
	}

	var known_themes = {
		'halena': {
			'name': 'Halena',
			'header': '#masthead',
			'content': '#content',
			'footer': '.site-footer',
		},
		'betheme': {
			'name': 'BeTheme',
			'header': '#Header',
			'content': '#Content',
			'footer': '#Footer',
		},
		'twentytwentytwo': {
			'name': 'Twenty Twenty-One',
			'header': '#Header',
			'content': '#Content',
			'footer': '#Footer',
		},
		'twentytwenty': {
			'name': 'Twenty Twenty',
			'header': '#site-header',
			'content': '#site-content',
			'footer': '#site-footer',
		} },
		known_selectors = {
			'header': ['#masthead', '#Header', '#site-header', '#page-header', '#main-header'],
			'content': ['#content', '#Content', '#site-content', '#page-content', '#main-content'],
			'footer': ['#footer', '#Footer', '#site-footer', '#page-footer', '#main-footer', '.site-footer'],
		},
		known_selector = false,
		known_theme = false,
		theme_info = {},
		manual_selector = false,
		select_type = getUrlVars()['live-select'];
	if (
		select_type != undefined
	) {
		select_type = select_type.replace(/[^\w]/g, '');
		select_type = select_type.trim();
		$.each(known_themes, function (theme, info) {
			if ( typeof ea_live_select_theme !== 'undefined' && ea_live_select_theme == theme ) {
				known_theme = theme;
				theme_info = info;
			}
		});
		$.each(known_selectors[select_type], function (i, selector) {
			if ($(selector).length) {
				known_selector = selector;
				el_selector = selector;
			}
		});
		if (
			known_theme != false &&
			known_theme != ''
		) {
			var el_selector = '';
			switch (select_type) {
				case 'header':
					el_selector = theme_info['header'];
					break;
				case 'content':
					el_selector = theme_info['content'];
					break;
				case 'footer':
					el_selector = theme_info['footer'];
					break;
			}

			$('.easy-announcements-live-selector').html('<div class="card selector shadow-lg"><div class="card-body"><p class="mb-0">Detected Theme:</p><h6 class="mt-0 mb-2">' + theme_info['name'] + '</h6><p class="mt-0 mb-0 text-capitalize">' + select_type + ' Selector:</p><p class="mb-3"><code class="fs-5">' + el_selector + '</code></p><a href="#" class="btn btn-primary confirm-live-select-found">Use Selector</a><a href="#" class="btn btn-secondary cancel-live-select-found">Try Again</a></div></div>');
		} else if (
			known_selector != false &&
			known_selector != ''
		) {
			$('.easy-announcements-live-selector').html('<div class="card selector shadow-lg"><div class="card-body"><p class="mt-0 mb-0 text-capitalize">Auto Detected Selector:</p><p class="mb-3"><code class="fs-5">' + el_selector + '</code></p><a href="#" class="btn btn-primary confirm-live-select-found">Use Selector</a><a href="#" class="btn btn-secondary cancel-live-select-found">Try Again</a></div></div>');
		} else {
			manual_selector = true;
		}
	} else {
		manual_selector = true;
	}
	if (manual_selector == false && el_selector != '') {
		$('body').addClass('live-select-found');
		
		$(document).on('click', '.easy-announcements-live-selector .confirm-live-select-found', function (event) {
			event.preventDefault();
			window.parent.postMessage({ 'channel': 'live-select', 'selector': el_selector }, '*');
		}).on('click', '.easy-announcements-live-selector .cancel-live-select-found', function (event) {
			event.preventDefault();
			window.location = '/?live-select';
		});
	} else {
		$('body').on('mouseover', function (event) {
			var el = $(document.elementFromPoint(event.pageX, event.pageY - $(window).scrollTop())),
				eTop = el.offset().top - $(window).scrollTop(),
				eLeft = el.offset().left,
				eWidth = el.outerWidth(),
				eHeight = el.outerHeight(),
				pWidth = $('body').outerWidth();

			if (!$('body').hasClass('live-select-found')) {
				if (eWidth >= pWidth * 0.95) {
					el.css('pointer-events', 'auto');
					$('.easy-announcements-live-selector').html('').css({
						'top': eTop,
						'left': eLeft,
						'width': eWidth,
						'height': eHeight
					});
				} else {
					$('.easy-announcements-live-selector').html('').css({
						'top': '',
						'left': '',
						'width': '',
						'height': ''
					});
				}
			}
		}).on('mouseout', function () {
			if (!$('body').hasClass('live-select-found')) {
				$('.easy-announcements-live-selector').html('').css({
					'top': '',
					'left': '',
					'width': '',
					'height': ''
				});
			}
		}).on('click', function (event) {
			event.preventDefault();
			if (!$('body').hasClass('live-select-found')) {
				var el = $(document.elementFromPoint(event.pageX, event.pageY - $(window).scrollTop())),
					eWidth = el.outerWidth(),
					eHeight = el.outerHeight(),
					pWidth = $('body').outerWidth(),
					el_selector = '';
		
				if (eWidth >= pWidth * 0.95) {
					if (typeof el.attr('id') !== typeof undefined && el.attr('id') !== false && el.attr('id') != '') {
						el_selector = '#' + el.attr('id');
					} else {
						if (typeof el.attr('class') !== typeof undefined && el.attr('class') !== false && el.attr('class') != '') {
							el_selectors = el.attr('class').split(/ /g);
							$.each(el_selectors, function (i, s) {
								if (s != '') {
									el_selector += '.' + s;
								}
							});
						}

						el.parents().each(function (i, parent) {
							if ($(parent).prop('tagName') != 'BODY' && $(parent).prop('tagName') != 'HTML') {
								var parent_selector = '';
								if (typeof $(parent).attr('id') !== typeof undefined && $(parent).attr('id') !== false && $(parent).attr('id') != '') {
									parent_selector = '#' + $(parent).attr('id');
									if (parent_selector != '') el_selector = parent_selector + ' ' + el_selector;
									return false;
								} else if (typeof $(parent).attr('class') !== typeof undefined && $(parent).attr('class') !== false && $(parent).attr('class') != '') {
									parent_selectors = $(parent).attr('class').split(/ /g);
									$.each(parent_selectors, function (i, s) {
										if (s != '') {
											parent_selector += '.' + s;
										}
									});
									if (parent_selector != '') el_selector = parent_selector + ' ' + el_selector;
								}
							}
						});
					}

					el_array = el_selector.split(' ');

					if (el_array.length > 0) {
						$.each(el_array, function (i) {
							if (el_array.length > 1) {
								var newWidth = $(el_array.join(' ')).outerWidth(),
									newHeight = $(el_array.join(' ')).outerHeight();
				
								if (newWidth == eWidth && newHeight == eHeight) {
									el_array.pop();
								} else {
									return false;
								}
							} else {
								return false;
							}
						});
					}

					el_selector = (el_array.length > 0) ? el_array.join(' ') : 'No Working Selectors';

					$('.easy-announcements-live-selector').html('<div class="card selector shadow-lg"><div class="card-body"><h6 class="mt-0 mb-2">Selector:</h6><p class="mb-3"><code>' + el_selector + '</code></p><a href="#" class="btn btn-primary confirm-live-select-found">Use Selector</a><a href="#" class="btn btn-secondary cancel-live-select-found">Try Again</a></div></div>');

					$('body').addClass('live-select-found');

					$(document).on('click', '.easy-announcements-live-selector .confirm-live-select-found', function (event) {
						event.preventDefault();
						window.parent.postMessage({
							'channel': 'live-select',
							'selector': el_selector
						}, '*');
					}).on('click', '.easy-announcements-live-selector .cancel-live-select-found', function (event) {
						event.preventDefault();
						$('body').removeClass('live-select-found');
						$('.easy-announcements-live-selector').html('').css({
							'top': '',
							'left': '',
							'width': '',
							'height': ''
						});
					});
				}
			}
		});
	}
});