/**
 * Nexus Loop Builder JavaScript
 * 
 * Visual loop builder with live preview
 * 
 * @package Nexus_Theme
 * @since 1.4.0
 */

(function($) {
    'use strict';
    
    const LoopBuilder = {
        
        config: {
            query: {
                post_type: 'post',
                posts_per_page: 9,
                orderby: 'date',
                order: 'DESC',
                tax_query: [],
                meta_query: []
            },
            template: {
                layout: 'grid',
                columns: 3,
                gap: 30,
                elements: [
                    { type: 'featured_image', enabled: true },
                    { type: 'title', enabled: true },
                    { type: 'excerpt', enabled: true },
                    { type: 'button', enabled: true, text: 'Read More' }
                ]
            }
        },
        
        currentLoopId: 0,
        previewTimeout: null,
        
        init() {
            this.bindEvents();
            this.initSortable();
        },
        
        bindEvents() {
            // Create new loop
            $('#nexus-create-new-loop').on('click', () => this.createNewLoop());
            
            // Cancel loop
            $('#cancel-loop').on('click', () => this.cancelLoop());
            
            // Save loop
            $('#save-loop').on('click', () => this.saveLoop());
            
            // Query controls
            $('#loop-post-type, #loop-posts-per-page, #loop-orderby, #loop-order').on('change', () => {
                this.updateQuery();
                this.triggerPreview();
            });
            
            // Template controls
            $('.layout-option').on('click', (e) => this.selectLayout(e));
            $('#loop-columns, #loop-gap').on('input', (e) => {
                this.updateTemplate();
                this.triggerPreview();
            });
            
            // Add filters
            $('#add-taxonomy-filter').on('click', () => this.addTaxonomyFilter());
            $('#add-meta-filter').on('click', () => this.addMetaFilter());
            
            // Refresh preview
            $('#refresh-preview').on('click', () => this.refreshPreview());
            
            // Copy shortcode
            $(document).on('click', '.copy-shortcode', (e) => this.copyShortcode(e));
            
            // Edit loop
            $(document).on('click', '.edit-loop', (e) => this.editLoop(e));
            
            // Delete loop
            $(document).on('click', '.delete-loop', (e) => this.deleteLoop(e));
        },
        
        initSortable() {
            if ($.fn.sortable) {
                $('#card-elements').sortable({
                    handle: '.drag-handle',
                    update: () => {
                        this.updateTemplate();
                        this.triggerPreview();
                    }
                });
            }
        },
        
        createNewLoop() {
            this.currentLoopId = 0;
            $('.nexus-saved-loops').hide();
            $('.nexus-loop-builder-container').fadeIn();
            this.renderCardElements();
            this.triggerPreview();
        },
        
        cancelLoop() {
            $('.nexus-loop-builder-container').hide();
            $('.nexus-saved-loops').fadeIn();
        },
        
        updateQuery() {
            this.config.query.post_type = $('#loop-post-type').val();
            this.config.query.posts_per_page = parseInt($('#loop-posts-per-page').val());
            this.config.query.orderby = $('#loop-orderby').val();
            this.config.query.order = $('#loop-order').val();
        },
        
        selectLayout(e) {
            e.preventDefault();
            $('.layout-option').removeClass('active');
            $(e.currentTarget).addClass('active');
            
            const layout = $(e.currentTarget).data('layout');
            this.config.template.layout = layout;
            
            // Show/hide columns control based on layout
            if (layout === 'list') {
                $('#columns-control').hide();
            } else {
                $('#columns-control').show();
            }
            
            this.triggerPreview();
        },
        
        updateTemplate() {
            this.config.template.columns = parseInt($('#loop-columns').val());
            this.config.template.gap = parseInt($('#loop-gap').val());
            
            // Update value displays
            $('#loop-columns').next('.value-display').text(this.config.template.columns);
            $('#loop-gap').next('.value-display').text(this.config.template.gap + 'px');
        },
        
        addTaxonomyFilter() {
            const html = `
                <div class="filter-row">
                    <select class="tax-filter-taxonomy">
                        <option value="">Select Taxonomy</option>
                        <option value="category">Category</option>
                        <option value="post_tag">Tag</option>
                    </select>
                    <select class="tax-filter-term">
                        <option value="">Select Term</option>
                    </select>
                    <button class="button remove-filter">Remove</button>
                </div>
            `;
            $('#taxonomy-filters').append(html);
        },
        
        addMetaFilter() {
            const html = `
                <div class="filter-row">
                    <input type="text" class="meta-key" placeholder="Meta Key">
                    <select class="meta-compare">
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value=">">></option>
                        <option value="<"><</option>
                        <option value="LIKE">LIKE</option>
                    </select>
                    <input type="text" class="meta-value" placeholder="Value">
                    <button class="button remove-filter">Remove</button>
                </div>
            `;
            $('#meta-filters').append(html);
        },
        
        renderCardElements() {
            const container = $('#card-elements');
            container.empty();
            
            this.config.template.elements.forEach((element, index) => {
                const html = `
                    <div class="card-element" data-index="${index}">
                        <span class="drag-handle dashicons dashicons-move"></span>
                        <span class="element-type">${this.getElementLabel(element.type)}</span>
                        <label class="element-toggle">
                            <input type="checkbox" ${element.enabled ? 'checked' : ''}>
                            <span>Enabled</span>
                        </label>
                        <button class="button-small element-settings">Settings</button>
                    </div>
                `;
                container.append(html);
            });
        },
        
        getElementLabel(type) {
            const labels = {
                featured_image: 'Featured Image',
                title: 'Title',
                excerpt: 'Excerpt',
                content: 'Full Content',
                meta: 'Post Meta',
                taxonomies: 'Categories/Tags',
                button: 'CTA Button',
                custom_field: 'Custom Field'
            };
            return labels[type] || type;
        },
        
        triggerPreview() {
            clearTimeout(this.previewTimeout);
            this.previewTimeout = setTimeout(() => this.refreshPreview(), 500);
        },
        
        refreshPreview() {
            const container = $('#loop-preview-container');
            container.html('<div class="preview-loading"><span class="spinner is-active"></span></div>');
            
            $.ajax({
                url: nexusLoopBuilder.ajax_url,
                method: 'POST',
                data: {
                    action: 'nexus_loop_preview',
                    nonce: nexusLoopBuilder.nonce,
                    config: JSON.stringify(this.config)
                },
                success: (response) => {
                    if (response.success) {
                        container.html(response.data.html);
                    } else {
                        container.html('<p class="error">Preview failed. Please check your configuration.</p>');
                    }
                },
                error: () => {
                    container.html('<p class="error">Preview failed. Please try again.</p>');
                }
            });
        },
        
        saveLoop() {
            const title = $('#loop-title').val();
            
            if (!title) {
                alert('Please enter a name for this loop.');
                return;
            }
            
            const button = $('#save-loop');
            button.prop('disabled', true).text('Saving...');
            
            $.ajax({
                url: nexusLoopBuilder.ajax_url,
                method: 'POST',
                data: {
                    action: 'nexus_save_loop',
                    nonce: nexusLoopBuilder.nonce,
                    title: title,
                    config: JSON.stringify(this.config),
                    loop_id: this.currentLoopId
                },
                success: (response) => {
                    if (response.success) {
                        alert('Loop saved successfully!\n\nUse this shortcode: ' + response.data.shortcode);
                        location.reload();
                    } else {
                        alert('Failed to save loop: ' + response.data.message);
                    }
                },
                complete: () => {
                    button.prop('disabled', false).text('Save Loop');
                }
            });
        },
        
        copyShortcode(e) {
            e.preventDefault();
            const shortcode = $(e.currentTarget).data('shortcode');
            
            // Create temporary input
            const temp = $('<input>');
            $('body').append(temp);
            temp.val(shortcode).select();
            document.execCommand('copy');
            temp.remove();
            
            // Visual feedback
            $(e.currentTarget).text('Copied!');
            setTimeout(() => {
                $(e.currentTarget).text('Copy');
            }, 2000);
        },
        
        editLoop(e) {
            e.preventDefault();
            const loopId = $(e.currentTarget).data('id');
            
            // Load loop configuration via AJAX
            // For now, just show the builder
            this.currentLoopId = loopId;
            this.createNewLoop();
        },
        
        deleteLoop(e) {
            e.preventDefault();
            
            if (!confirm('Are you sure you want to delete this loop?')) {
                return;
            }
            
            const loopId = $(e.currentTarget).data('id');
            
            // Delete loop via AJAX
            $.ajax({
                url: nexusLoopBuilder.ajax_url,
                method: 'POST',
                data: {
                    action: 'nexus_delete_loop',
                    nonce: nexusLoopBuilder.nonce,
                    loop_id: loopId
                },
                success: (response) => {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Failed to delete loop: ' + response.data.message);
                    }
                }
            });
        }
    };
    
    // Initialize when document is ready
    $(document).ready(() => {
        if ($('.nexus-loop-builder-wrap').length) {
            LoopBuilder.init();
        }
    });
    
})(jQuery);
