/* Easy Announcements - Vanilla JavaScript */

/* Cookie helpers - no external dependency */
function getCookie(name) {
	const nameEQ = name + '=';
	const cookies = document.cookie.split(';');
	for (let cookie of cookies) {
		cookie = cookie.trim();
		if (cookie.indexOf(nameEQ) === 0) {
			try {
				return JSON.parse(atob(cookie.substring(nameEQ.length)));
			} catch (e) {
				return {};
			}
		}
	}
	return {};
}

function setCookie(value) {
	const maxAge = 7 * 24 * 60 * 60; // 7 days in seconds
	document.cookie = 'easy_announcements=' + btoa(JSON.stringify(value)) + '; path=/; max-age=' + maxAge;
}

function initializeCookie() {
	const jar = getCookie('easy_announcements');
	if (Object.keys(jar).length === 0) {
		// Cookie doesn't exist or is empty, set initial empty value
		setCookie({});
	}
	return jar;
}

function getDismissal(id) {
	const jar = getCookie('easy_announcements');
	return jar['dismiss-' + id] === 'true';
}

function setDismissal(id) {
	const jar = getCookie('easy_announcements');
	jar['dismiss-' + id] = 'true';
	setCookie(jar);
}

/* Modal helpers */
let eaBackdrop = null;

function eaShowModal(modalId) {
	const modal = document.getElementById(modalId);
	if (!modal) return;

	if (!eaBackdrop) {
		eaBackdrop = document.createElement('div');
		eaBackdrop.className = 'ea-modal-backdrop show';
		eaBackdrop.setAttribute('aria-hidden', 'true');
		document.body.appendChild(eaBackdrop);
	}

	modal.classList.add('show');
	modal.removeAttribute('aria-hidden');
	modal.setAttribute('aria-modal', 'true');
	document.body.classList.add('ea-modal-open');
	modal.focus();
}

function eaDismissModal(modal) {
	modal.classList.remove('show');
	modal.removeAttribute('aria-modal');
	modal.setAttribute('aria-hidden', 'true');

	if (document.querySelectorAll('.ea-modal.show').length === 0 && eaBackdrop) {
		eaBackdrop.remove();
		eaBackdrop = null;
		document.body.classList.remove('ea-modal-open');
	}

	const event = new CustomEvent('ea.modal.dismiss');
	modal.dispatchEvent(event);
}

/* Main initialization function */
async function easyAnnouncementsInit(pageId) {
	try {
		// Initialize cookie
		initializeCookie();

		// Fetch announcements from REST API
		const response = await fetch(
			'/wp-json/easy-announcements/v1/announcements?page_id=' + pageId,
			{ method: 'GET', credentials: 'same-origin' }
		);

		if (!response.ok) {
			console.error('Easy Announcements: API request failed', response.status);
			return;
		}

		const data = await response.json();
		if (!data.success || !data.announcements) return;

		// Group announcements by placement and attachment
		const grouped = {};
		data.announcements.forEach(ann => {
			if (!grouped[ann.placement]) grouped[ann.placement] = {};
			if (!grouped[ann.placement][ann.attachment]) grouped[ann.placement][ann.attachment] = [];
			grouped[ann.placement][ann.attachment].push(ann);
		});

		// Render announcements
		Object.keys(grouped).forEach(placement => {
			Object.keys(grouped[placement]).forEach(attachment => {
				const announcements = grouped[placement][attachment];
				renderAnnouncementSection(placement, attachment, announcements);
			});
		});

		// Setup modal event handlers
		setupModalHandlers();
	} catch (error) {
		console.error('Easy Announcements error:', error);
	}
}

function renderAnnouncementSection(placement, attachment, announcements) {
	// Build class names
	const classes = [
		'site-announcements-' + placement,
		'site-announcements-' + attachment,
		'site-announcements-' + placement + '-' + attachment
	];

	// Create section wrapper
	const section = document.createElement('section');
	section.className = 'site-announcements ' + classes.join(' ');
	section.setAttribute('role', 'region');
	section.setAttribute('aria-label', 'Site Announcements');

	// Add announcements that aren't dismissed
	announcements.forEach(ann => {
		if (!getDismissal(ann.id)) {
			const temp = document.createElement('div');
			temp.innerHTML = ann.html;
			section.appendChild(temp.firstElementChild);
		}
	});

	// If section is empty, don't insert it
	if (section.children.length === 0) return;

	// Determine insertion point
	let target = document.querySelector('body');
	let method = 'append';

	const placementSettings = {
		'header': { selector: null, method: 'prepend' },
		'footer': { selector: null, method: 'append' },
		'content': { selector: null, method: 'append' },
		'popup': { selector: 'body', method: 'append' }
	};

	// For banners, try to find custom selector from settings
	// For now, using body as fallback
	if (placement === 'popup') {
		target = document.body;
		method = 'append';
	}

	if (method === 'append') {
		target.appendChild(section);
	} else if (method === 'prepend') {
		target.insertBefore(section, target.firstChild);
	}

	// Setup dismiss handlers for banners
	if (placement !== 'popup') {
		section.querySelectorAll('.announcement .dismiss').forEach(btn => {
			btn.addEventListener('click', (e) => {
				e.preventDefault();
				const id = btn.getAttribute('href').replace('#dismiss-', '');
				setDismissal(id);
				const ann = btn.closest('.announcement');
				ann.style.overflow = 'hidden';
				ann.style.height = ann.offsetHeight + 'px';
				setTimeout(() => {
					ann.style.transition = 'height 0.3s ease-out';
					ann.style.height = '0';
					ann.addEventListener('transitionend', () => {
						ann.remove();
						if (section.children.length === 0) {
							section.remove();
						}
					}, { once: true });
				}, 10);
			});
		});
	}
}

function setupModalHandlers() {
	// Close button
	document.addEventListener('click', (e) => {
		if (e.target.closest('[data-ea-dismiss="modal"]')) {
			const modal = e.target.closest('.ea-modal');
			if (modal) {
				eaDismissModal(modal);
				const dismissId = modal.id.replace('modal', '');
				setDismissal(dismissId);
			}
		}

		// Backdrop click
		if (e.target.classList && e.target.classList.contains('ea-modal-backdrop')) {
			const openModal = document.querySelector('.ea-modal.show');
			if (openModal) eaDismissModal(openModal);
		}
	});

	// ESC key
	document.addEventListener('keydown', (e) => {
		if (e.key === 'Escape') {
			const openModal = document.querySelector('.ea-modal.show');
			if (openModal) eaDismissModal(openModal);
		}
	});

	// Initialize modals with delay
	document.querySelectorAll('.ea-modal[id]').forEach(modal => {
		const delayMs = parseInt(modal.getAttribute('data-popup-delay') || 0, 10);
		if (delayMs > 0) {
			setTimeout(() => eaShowModal(modal.id), delayMs);
		} else {
			eaShowModal(modal.id);
		}
	});
}
