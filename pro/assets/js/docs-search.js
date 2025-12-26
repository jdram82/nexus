/**
 * Documentation Search JavaScript
 * Nexus Pro - AJAX Documentation Search
 */

(function($) {
    'use strict';

    const NexusDocsSearch = {
        searchTimeout: null,
        currentRequest: null,

        /**
         * Initialize
         */
        init: function() {
            this.searchInput();
            this.keyboardNavigation();
            this.closeResults();
            this.trackViews();
        },

        /**
         * Search Input Handler
         */
        searchInput: function() {
            const self = this;
            const searchInput = $('.nexus-docs-search input[type="search"]');
            const resultsContainer = $('.search-results');

            searchInput.on('input', function() {
                const query = $(this).val().trim();

                // Clear previous timeout
                clearTimeout(self.searchTimeout);

                // Abort previous AJAX request
                if (self.currentRequest) {
                    self.currentRequest.abort();
                }

                // Clear results if query is too short
                if (query.length < 2) {
                    resultsContainer.empty().hide();
                    return;
                }

                // Show loading state
                resultsContainer.html(
                    '<div class="search-loading">' +
                    '<span class="spinner"></span>' +
                    'Searching...' +
                    '</div>'
                ).show();

                // Debounce search
                self.searchTimeout = setTimeout(function() {
                    self.performSearch(query);
                }, 300);
            });

            // Clear on ESC
            searchInput.on('keydown', function(e) {
                if (e.key === 'Escape') {
                    resultsContainer.empty().hide();
                    $(this).blur();
                }
            });
        },

        /**
         * Perform AJAX Search
         */
        performSearch: function(query) {
            const self = this;
            const resultsContainer = $('.search-results');

            this.currentRequest = $.ajax({
                url: nexusDocsData.ajaxurl,
                type: 'POST',
                data: {
                    action: 'nexus_search_docs',
                    nonce: nexusDocsData.nonce,
                    query: query
                },
                success: function(response) {
                    if (response.success && response.data.results.length > 0) {
                        self.displayResults(response.data.results);
                    } else {
                        resultsContainer.html(
                            '<div class="no-search-results">' +
                            '<p>No documentation found for "' + query + '"</p>' +
                            '<p class="help-text">Try different keywords or browse categories</p>' +
                            '</div>'
                        );
                    }
                },
                error: function(xhr) {
                    if (xhr.statusText !== 'abort') {
                        resultsContainer.html(
                            '<div class="search-error">' +
                            '<p>Search error. Please try again.</p>' +
                            '</div>'
                        );
                    }
                }
            });
        },

        /**
         * Display Search Results
         */
        displayResults: function(results) {
            const resultsContainer = $('.search-results');
            let html = '<div class="search-results-list">';

            results.forEach(function(result, index) {
                html += '
                    <div class="search-result-item" data-index="' + index + '">
                        <a href="' + result.url + '">
                            <div class="result-header">
                                <h4>' + self.highlightQuery(result.title) + '</h4>
                                ' + (result.category ? '<span class="result-category">' + result.category + '</span>' : '') + '
                            </div>
                            ' + (result.excerpt ? '<p class="result-excerpt">' + self.highlightQuery(result.excerpt) + '</p>' : '') + '
                        </a>
                    </div>
                ';
            });

            html += '</div>';
            html += '<div class="view-all-results"><a href="#">View All Results</a></div>';

            resultsContainer.html(html).show();
        },

        /**
         * Highlight Search Query in Results
         */
        highlightQuery: function(text) {
            const query = $('.nexus-docs-search input[type="search"]').val().trim();
            if (!query) return text;

            const regex = new RegExp('(' + this.escapeRegex(query) + ')', 'gi');
            return text.replace(regex, '<mark>$1</mark>');
        },

        /**
         * Escape Regex Special Characters
         */
        escapeRegex: function(string) {
            return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        },

        /**
         * Keyboard Navigation
         */
        keyboardNavigation: function() {
            let currentIndex = -1;
            const searchInput = $('.nexus-docs-search input[type="search"]');

            searchInput.on('keydown', function(e) {
                const results = $('.search-result-item');
                if (!results.length) return;

                // Arrow Down
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    currentIndex = (currentIndex + 1) % results.length;
                    NexusDocsSearch.highlightResult(currentIndex);
                }

                // Arrow Up
                if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    currentIndex = currentIndex <= 0 ? results.length - 1 : currentIndex - 1;
                    NexusDocsSearch.highlightResult(currentIndex);
                }

                // Enter
                if (e.key === 'Enter' && currentIndex >= 0) {
                    e.preventDefault();
                    const link = results.eq(currentIndex).find('a').attr('href');
                    if (link) {
                        window.location.href = link;
                    }
                }
            });

            // Mouse hover
            $(document).on('mouseenter', '.search-result-item', function() {
                currentIndex = $(this).data('index');
                NexusDocsSearch.highlightResult(currentIndex);
            });
        },

        /**
         * Highlight Result
         */
        highlightResult: function(index) {
            $('.search-result-item').removeClass('highlighted');
            $('.search-result-item').eq(index).addClass('highlighted');
        },

        /**
         * Close Results on Outside Click
         */
        closeResults: function() {
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.nexus-docs-search').length) {
                    $('.search-results').empty().hide();
                }
            });
        },

        /**
         * Track Documentation Views
         */
        trackViews: function() {
            // Track page view for analytics
            if ($('.single-nexus_doc').length) {
                const docId = $('.single-nexus_doc').data('doc-id');
                
                if (docId) {
                    $.post(nexusDocsData.ajaxurl, {
                        action: 'nexus_track_doc_view',
                        nonce: nexusDocsData.nonce,
                        doc_id: docId
                    });
                }
            }
        },

        /**
         * Copy Code Blocks
         */
        copyCodeBlocks: function() {
            // Add copy button to code blocks
            $('pre code').each(function() {
                const code = $(this);
                const pre = code.parent();
                
                if (!pre.find('.copy-code-btn').length) {
                    const copyBtn = $(
                        '<button class="copy-code-btn" title="Copy code">' +
                        '<span class="dashicons dashicons-admin-page"></span>' +
                        '</button>'
                    );
                    
                    pre.css('position', 'relative').append(copyBtn);
                    
                    copyBtn.on('click', function() {
                        const codeText = code.text();
                        navigator.clipboard.writeText(codeText).then(function() {
                            copyBtn.addClass('copied');
                            copyBtn.find('.dashicons')
                                .removeClass('dashicons-admin-page')
                                .addClass('dashicons-yes');
                            
                            setTimeout(function() {
                                copyBtn.removeClass('copied');
                                copyBtn.find('.dashicons')
                                    .removeClass('dashicons-yes')
                                    .addClass('dashicons-admin-page');
                            }, 2000);
                        });
                    });
                }
            });
        },

        /**
         * Smooth Scroll to Anchors
         */
        smoothScroll: function() {
            $('.nexus-docs-toc a[href^="#"]').on('click', function(e) {
                e.preventDefault();
                const target = $($(this).attr('href'));
                
                if (target.length) {
                    $('html, body').animate({
                        scrollTop: target.offset().top - 100
                    }, 500);
                    
                    // Update active item
                    $('.nexus-docs-toc a').removeClass('active');
                    $(this).addClass('active');
                }
            });
        },

        /**
         * Sticky TOC
         */
        stickyTOC: function() {
            const toc = $('.nexus-docs-toc');
            if (!toc.length) return;

            const tocOffset = toc.offset().top;
            const tocHeight = toc.outerHeight();

            $(window).on('scroll', function() {
                const scrollTop = $(window).scrollTop();
                
                if (scrollTop > tocOffset - 100) {
                    toc.addClass('sticky');
                } else {
                    toc.removeClass('sticky');
                }
            });
        }
    };

    /**
     * Initialize on Document Ready
     */
    $(document).ready(function() {
        NexusDocsSearch.init();
        NexusDocsSearch.copyCodeBlocks();
        NexusDocsSearch.smoothScroll();
        NexusDocsSearch.stickyTOC();
    });

    // Make accessible globally for debugging
    window.NexusDocsSearch = NexusDocsSearch;

})(jQuery);