(function() {
	const { useEffect } = wp.element;
	const { useDispatch, useSelect } = wp.data;
	const apiFetch = wp.apiFetch;

	// Listen for form field changes in the meta box
	document.addEventListener('DOMContentLoaded', function() {
		const eaMeta = eaGutenbergMeta || {};
		const metaFields = eaMeta.metaFields || [];

		// Watch for changes to all registered meta fields
		metaFields.forEach(function(fieldName) {
			const selector = '[name="' + fieldName + '"]';
			const elements = document.querySelectorAll(selector);
			
			elements.forEach(function(element) {
				// Set up change listeners
				element.addEventListener('change', function() {
					updateMetaInStore(fieldName, element);
				});

				// Keep listening to input events for real-time updates
				if (element.tagName === 'INPUT' || element.tagName === 'SELECT' || element.tagName === 'TEXTAREA') {
					element.addEventListener('input', function() {
						updateMetaInStore(fieldName, element);
					});
				}
			});
		});

		// Also watch for form submission to ensure meta is saved
		const form = document.querySelector('form');
		if (form) {
			form.addEventListener('submit', function(e) {
				// Sync all meta fields before submit
				metaFields.forEach(function(fieldName) {
					const element = document.querySelector('[name="' + fieldName + '"]');
					if (element) {
						updateMetaInStore(fieldName, element);
					}
				});
			});
		}
	});

	// Function to get value from a form element
	function getElementValue(element) {
		if (!element) return null;

		if (element.type === 'checkbox') {
			return element.checked ? '1' : '0';
		} else if (element.type === 'radio') {
			const checked = document.querySelector('[name="' + element.name + '"]:checked');
			return checked ? checked.value : null;
		} else if (element.tagName === 'SELECT' && element.multiple) {
			return Array.from(element.selectedOptions).map(opt => opt.value);
		} else {
			return element.value || null;
		}
	}

	// Function to update meta in WordPress data store
	function updateMetaInStore(fieldName, element) {
		const value = getElementValue(element);
		
		if (value !== null) {
			// Use the REST API to update meta
			const postId = window.wp?.data?.select('core/editor')?.getCurrentPostId?.();
			if (!postId) return;

			// Dispatch to the core/editor store to update meta
			const { editPost } = wp.data.dispatch('core/editor');
			if (editPost) {
				editPost({ meta: { [fieldName]: value } });
			}
		}
	}

	// On page load, sync existing values from meta to form fields
	document.addEventListener('DOMContentLoaded', function() {
		setTimeout(function() {
			const eaMeta = eaGutenbergMeta || {};
			const metaFields = eaMeta.metaFields || [];

			metaFields.forEach(function(fieldName) {
				const element = document.querySelector('[name="' + fieldName + '"]');
				if (element) {
					updateMetaInStore(fieldName, element);
				}
			});
		}, 500);
	});
})();
