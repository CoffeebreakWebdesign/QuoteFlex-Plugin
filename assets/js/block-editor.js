/**
 * QuoteFlex Gutenberg Block
 *
 * @package QuoteFlex
 * @since   1.0.0
 */

(function(wp) {
	const { registerBlockType } = wp.blocks;
	const { InspectorControls, useBlockProps } = wp.blockEditor;
	const { PanelBody, SelectControl, ToggleControl } = wp.components;
	const { __ } = wp.i18n;
	const { ServerSideRender } = wp.serverSideRender || wp.editor;

	/**
	 * Register QuoteFlex block.
	 */
	registerBlockType('quoteflex/quote-display', {
		title: __('QuoteFlex Quote', 'quoteflex'),
		description: __('Display a random quote from your collection', 'quoteflex'),
		category: 'widgets',
		icon: 'format-quote',
		keywords: [__('quote', 'quoteflex'), __('quotation', 'quoteflex'), __('inspiration', 'quoteflex')],
		
		attributes: {
			set: {
				type: 'string',
				default: ''
			},
			category: {
				type: 'string',
				default: ''
			},
			template: {
				type: 'string',
				default: 'default'
			},
			showAuthor: {
				type: 'boolean',
				default: true
			},
			showSource: {
				type: 'boolean',
				default: false
			},
			enableRefresh: {
				type: 'boolean',
				default: true
			},
			animation: {
				type: 'string',
				default: 'fade'
			}
		},

		edit: function(props) {
			const { attributes, setAttributes } = props;
			const blockProps = useBlockProps();

			// Build set options for dropdown.
			const setOptions = [
				{ value: '', label: __('All Quotes (Random)', 'quoteflex') }
			];

			if (quoteflexBlock.sets && quoteflexBlock.sets.length > 0) {
				quoteflexBlock.sets.forEach(function(set) {
					setOptions.push({ value: set.value, label: set.label });
				});
			}

			// Template options.
			const templateOptions = [
				{ value: 'default', label: __('Default', 'quoteflex') },
				{ value: 'boxed', label: __('Boxed', 'quoteflex') },
				{ value: 'card', label: __('Card', 'quoteflex') },
				{ value: 'minimal', label: __('Minimal', 'quoteflex') }
			];

			// Animation options.
			const animationOptions = [
				{ value: 'fade', label: __('Fade', 'quoteflex') },
				{ value: 'slide', label: __('Slide', 'quoteflex') },
				{ value: 'none', label: __('None', 'quoteflex') }
			];

			return (
				wp.element.createElement('div', blockProps,
					// Inspector Controls (Sidebar).
					wp.element.createElement(InspectorControls, null,
						wp.element.createElement(PanelBody, {
							title: __('Quote Settings', 'quoteflex'),
							initialOpen: true
						},
							wp.element.createElement(SelectControl, {
								label: __('Quote Set', 'quoteflex'),
								value: attributes.set,
								options: setOptions,
								onChange: function(value) {
									setAttributes({ set: value });
								}
							}),
							
							wp.element.createElement(SelectControl, {
								label: __('Template', 'quoteflex'),
								value: attributes.template,
								options: templateOptions,
								onChange: function(value) {
									setAttributes({ template: value });
								}
							}),
							
							wp.element.createElement(SelectControl, {
								label: __('Animation', 'quoteflex'),
								value: attributes.animation,
								options: animationOptions,
								onChange: function(value) {
									setAttributes({ animation: value });
								}
							})
						),
						
						wp.element.createElement(PanelBody, {
							title: __('Display Options', 'quoteflex'),
							initialOpen: false
						},
							wp.element.createElement(ToggleControl, {
								label: __('Show Author', 'quoteflex'),
								checked: attributes.showAuthor,
								onChange: function(value) {
									setAttributes({ showAuthor: value });
								}
							}),
							
							wp.element.createElement(ToggleControl, {
								label: __('Show Source', 'quoteflex'),
								checked: attributes.showSource,
								onChange: function(value) {
									setAttributes({ showSource: value });
								}
							}),
							
							wp.element.createElement(ToggleControl, {
								label: __('Enable Refresh Button', 'quoteflex'),
								checked: attributes.enableRefresh,
								onChange: function(value) {
									setAttributes({ enableRefresh: value });
								}
							})
						)
					),
					
					// Block Preview (uses ServerSideRender).
					wp.element.createElement('div', { className: 'quoteflex-block-preview' },
						wp.element.createElement(ServerSideRender, {
							block: 'quoteflex/quote-display',
							attributes: attributes
						})
					)
				)
			);
		},

		save: function() {
			// Server-side rendering, return null.
			return null;
		}
	});

})(window.wp);
