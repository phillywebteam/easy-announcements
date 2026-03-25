// Gutenberg Sidebar Panel for Easy Announcements
const { registerPlugin } = wp.plugins;
const { PluginDocumentSettingPanel } = wp.editPost;
const { useEntityProp } = wp.coreData;
const { SelectControl, TextControl, CheckboxControl, TextareaControl, Notice } = wp.components;
const { select, useSelect } = wp.data;

console.log('Easy Announcements: Gutenberg sidebar script loading');

// Create sidebar panel component
const AnnouncementSettingsPanel = () => {
	const postType = useSelect(sel => sel('core/editor').getCurrentPostType());
	
	if (postType !== 'announcement') {
		return null;
	}

	console.log('Easy Announcements: Rendering sidebar panel for announcement');

	// Get current post meta values
	const [meta, setMeta] = useEntityProp('postType', 'announcement', 'meta');

	if (!meta) {
		console.warn('Easy Announcements: Meta object is not available yet');
	}

	const updateMeta = (key, value) => {
		console.log('Easy Announcements: Updating meta -', key, ':', value);
		setMeta({ ...meta, [key]: value });
	};

	return (
		<PluginDocumentSettingPanel name="announcement-settings" title="Announcement Settings" className="announcement-settings-panel">
			
			<SelectControl
				label="Placement"
				value={meta?.announcement_placement || ''}
				options={[
					{ label: '— Select —', value: '' },
					{ label: 'Header', value: 'header' },
					{ label: 'Footer', value: 'footer' },
					{ label: 'Content', value: 'content' },
					{ label: 'Popup', value: 'popup' },
				]}
				onChange={(value) => updateMeta('announcement_placement', value)}
			/>

			{meta?.announcement_placement !== 'popup' && (
				<SelectControl
					label="Attachment"
					value={meta?.announcement_attachment || 'after'}
					options={[
						{ label: 'Before', value: 'before' },
						{ label: 'After', value: 'after' },
					]}
					onChange={(value) => updateMeta('announcement_attachment', value)}
				/>
			)}

			{meta?.announcement_placement === 'popup' && (
				<TextControl
					label="Show After (seconds)"
					type="number"
					value={meta?.announcement_popup_delay || 0}
					onChange={(value) => updateMeta('announcement_popup_delay', parseInt(value) || 0)}
				/>
			)}

			<SelectControl
				label="Color"
				value={meta?.announcement_color || 'primary'}
				options={[
					{ label: 'Primary', value: 'primary' },
					{ label: 'Secondary', value: 'secondary' },
					{ label: 'Success', value: 'success' },
					{ label: 'Danger', value: 'danger' },
					{ label: 'Warning', value: 'warning' },
					{ label: 'Info', value: 'info' },
					{ label: 'Custom', value: 'custom' },
				]}
				onChange={(value) => updateMeta('announcement_color', value)}
			/>

			{meta?.announcement_color === 'custom' && (
				<>
					<TextControl
						label="Background Color (hex)"
						value={meta?.announcement_custom_color_background || ''}
						onChange={(value) => updateMeta('announcement_custom_color_background', value)}
						placeholder="#ffffff"
					/>
					<TextControl
						label="Text & Links Color (hex)"
						value={meta?.announcement_custom_color_content || ''}
						onChange={(value) => updateMeta('announcement_custom_color_content', value)}
						placeholder="Leave blank for auto"
					/>
				</>
			)}

			<SelectControl
				label="Size"
				value={meta?.announcement_size || 'default'}
				options={[
					{ label: 'Default', value: 'default' },
					{ label: 'Compact', value: 'compact' },
					{ label: 'Tall', value: 'tall' },
					{ label: 'No Padding', value: 'none' },
				]}
				onChange={(value) => updateMeta('announcement_size', value)}
			/>

			<SelectControl
				label="Text Alignment"
				value={meta?.announcement_text_alignment || ''}
				options={[
					{ label: 'Default', value: '' },
					{ label: 'Left', value: 'start' },
					{ label: 'Center', value: 'center' },
					{ label: 'Right', value: 'end' },
				]}
				onChange={(value) => updateMeta('announcement_text_alignment', value)}
			/>

			<SelectControl
				label="Text Size"
				value={meta?.announcement_text_size || 'default'}
				options={[
					{ label: 'Default', value: 'default' },
					{ label: 'Small', value: 'small' },
					{ label: 'Large', value: 'large' },
				]}
				onChange={(value) => updateMeta('announcement_text_size', value)}
			/>

			<TextControl
				label="Destination URL"
				type="url"
				value={meta?.announcement_url || ''}
				onChange={(value) => updateMeta('announcement_url', value)}
				placeholder="https://"
			/>

			<TextControl
				label="Expiration (datetime)"
				type="datetime-local"
				value={meta?.announcement_expiration || ''}
				onChange={(value) => updateMeta('announcement_expiration', value)}
			/>

			<CheckboxControl
				label="Show Title"
				checked={meta?.announcement_show_title === '1' || meta?.announcement_show_title === true}
				onChange={(value) => updateMeta('announcement_show_title', value ? '1' : '0')}
			/>

			<CheckboxControl
				label="Is Sticky"
				checked={meta?.announcement_sticky === '1' || meta?.announcement_sticky === true}
				onChange={(value) => updateMeta('announcement_sticky', value ? '1' : '0')}
				disabled={meta?.announcement_placement === 'popup'}
			/>

			<CheckboxControl
				label="Is Dismissable"
				checked={meta?.announcement_dismissable === '1' || meta?.announcement_dismissable === true}
				onChange={(value) => updateMeta('announcement_dismissable', value ? '1' : '0')}
			/>

		</PluginDocumentSettingPanel>
	);
};

// Register the plugin
registerPlugin('easy-announcements-sidebar', {
	render: AnnouncementSettingsPanel,
	icon: 'megaphone',
});
