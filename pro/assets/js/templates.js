/**
 * Nexus Template Library JavaScript
 * 
 * @package Nexus_Theme
 * @since 1.5.0
 */

(function($) {
    'use strict';
    
    const TemplateLibrary = {
        
        init() {
            this.bindEvents();
            this.loadTemplates();
        },
        
        bindEvents() {
            // Tab switching
            $('.nav-tab').on('click', (e) => this.switchTab(e));
            
            // Browse templates
            $('#refresh-templates, #template-category, #template-type').on('change click', () => {
                this.loadTemplates();
            });
            
            // Template actions
            $(document).on('click', '.import-template', (e) => this.importTemplate(e));
            $(document).on('click', '.export-template', (e) => this.exportTemplate(e));
            $(document).on('click', '.delete-template', (e) => this.deleteTemplate(e));
            $(document).on('click', '.use-template', (e) => this.useTemplate(e));
            
            // Cloud sync
            $('.sync-checkbox').on('change', (e) => this.syncToCloud(e));
        },
        
        switchTab(e) {
            e.preventDefault();
            const tab = $(e.currentTarget).data('tab');
            
            $('.nav-tab').removeClass('nav-tab-active');
            $(e.currentTarget).addClass('nav-tab-active');
            
            $('.tab-content').removeClass('active');
            $('#tab-' + tab).addClass('active');
        },
        
        loadTemplates() {
            const grid = $('#templates-grid');
            grid.html('<div class="loading"><span class="spinner is-active"></span></div>');
            
            $.ajax({
                url: nexusTemplates.ajax_url,
                method: 'POST',
                data: {
                    action: 'nexus_browse_templates',
                    nonce: nexusTemplates.nonce,
                    category: $('#template-category').val(),
                    type: $('#template-type').val()
                },
                success: (response) => {
                    if (response.success) {
                        this.renderTemplates(response.data.templates);
                    }
                }
            });
        },
        
        renderTemplates(templates) {
            const grid = $('#templates-grid');
            grid.empty();
            
            if (templates.length === 0) {
                grid.html('<div class="no-templates"><p>No templates found.</p></div>');
                return;
            }
            
            templates.forEach(template => {
                const card = `
                    <div class="template-card" data-id="${template.id}">
                        <div class="template-thumbnail">
                            <img src="${template.thumbnail}" alt="${template.title}">
                            ${template.price !== 'free' ? `<span class="price-badge">${template.price}</span>` : '<span class="free-badge">Free</span>'}
                        </div>
                        <div class="template-info">
                            <h3>${template.title}</h3>
                            <p>${template.description}</p>
                            <div class="template-meta">
                                <span class="author">by ${template.author}</span>
                                <span class="downloads">↓ ${template.downloads}</span>
                                <span class="rating">★ ${template.rating}</span>
                            </div>
                        </div>
                        <div class="template-actions">
                            <button class="button button-primary import-template" data-id="${template.id}">
                                Import Template
                            </button>
                            <button class="button preview-template" data-id="${template.id}">
                                Preview
                            </button>
                        </div>
                    </div>
                `;
                grid.append(card);
            });
        },
        
        importTemplate(e) {
            e.preventDefault();
            const button = $(e.currentTarget);
            const templateId = button.data('id');
            
            button.prop('disabled', true).text('Importing...');
            
            $.ajax({
                url: nexusTemplates.ajax_url,
                method: 'POST',
                data: {
                    action: 'nexus_import_template',
                    nonce: nexusTemplates.nonce,
                    template_id: templateId
                },
                success: (response) => {
                    if (response.success) {
                        alert('Template imported successfully!');
                        location.reload();
                    } else {
                        alert('Failed to import template: ' + response.data.message);
                    }
                },
                complete: () => {
                    button.prop('disabled', false).text('Import Template');
                }
            });
        },
        
        exportTemplate(e) {
            e.preventDefault();
            const templateId = $(e.currentTarget).data('id');
            
            $.ajax({
                url: nexusTemplates.ajax_url,
                method: 'POST',
                data: {
                    action: 'nexus_export_template',
                    nonce: nexusTemplates.nonce,
                    template_id: templateId
                },
                success: (response) => {
                    if (response.success) {
                        // Download JSON file
                        const blob = new Blob([JSON.stringify(response.data.data, null, 2)], {type: 'application/json'});
                        const url = URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = response.data.filename;
                        a.click();
                        URL.revokeObjectURL(url);
                    }
                }
            });
        },
        
        deleteTemplate(e) {
            e.preventDefault();
            
            if (!confirm('Are you sure you want to delete this template?')) {
                return;
            }
            
            const templateId = $(e.currentTarget).data('id');
            
            $.ajax({
                url: nexusTemplates.ajax_url,
                method: 'POST',
                data: {
                    action: 'nexus_delete_template',
                    nonce: nexusTemplates.nonce,
                    template_id: templateId
                },
                success: (response) => {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Failed to delete template: ' + response.data.message);
                    }
                }
            });
        },
        
        useTemplate(e) {
            e.preventDefault();
            const templateId = $(e.currentTarget).data('id');
            
            // Redirect to page builder with template
            window.location.href = `admin.php?page=nexus-theme-builder&template=${templateId}`;
        },
        
        syncToCloud(e) {
            const checkbox = $(e.currentTarget);
            const templateId = checkbox.data('id');
            const sync = checkbox.is(':checked');
            
            // Check cloud limit
            if (sync && nexusTemplates.cloud_limit !== -1) {
                if (nexusTemplates.cloud_used >= nexusTemplates.cloud_limit) {
                    alert('Cloud storage limit reached. Upgrade to Advanced for unlimited templates.');
                    checkbox.prop('checked', false);
                    return;
                }
            }
            
            $.ajax({
                url: nexusTemplates.ajax_url,
                method: 'POST',
                data: {
                    action: 'nexus_sync_to_cloud',
                    nonce: nexusTemplates.nonce,
                    template_id: templateId,
                    sync: sync
                },
                success: (response) => {
                    if (response.success) {
                        nexusTemplates.cloud_used = response.data.cloud_count;
                        $('.cloud-status').text(`Cloud Templates: ${response.data.cloud_count} ${nexusTemplates.cloud_limit === -1 ? '(Unlimited)' : '/ ' + nexusTemplates.cloud_limit}`);
                        
                        // Update UI
                        if (sync) {
                            checkbox.closest('.sync-item').find('.synced-badge').remove();
                            checkbox.closest('.sync-item').find('label').append('<span class="synced-badge">✓ Synced</span>');
                        } else {
                            checkbox.closest('.sync-item').find('.synced-badge').remove();
                        }
                    }
                }
            });
        }
    };
    
    $(document).ready(() => {
        if ($('.nexus-templates-wrap').length) {
            TemplateLibrary.init();
        }
    });
    
})(jQuery);
