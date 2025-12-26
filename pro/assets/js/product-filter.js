/**
 * Product Filter JavaScript
 * Nexus Pro - AJAX Product Filtering
 */

(function($) {
    'use strict';

    const NexusProductFilter = {
        /**
         * Initialize
         */
        init: function() {
            this.filterToggle();
            this.filterHandlers();
            this.priceRange();
            this.clearFilters();
            this.viewToggle();
            this.collapsibleSections();
        },

        /**
         * Mobile Filter Toggle
         */
        filterToggle: function() {
            $('.mobile-filter-toggle').on('click', function(e) {
                e.preventDefault();
                $('.product-filter-sidebar').addClass('active');
                $('.filter-overlay').addClass('active');
                $('body').css('overflow', 'hidden');
            });

            $('.filter-overlay').on('click', function() {
                $('.product-filter-sidebar').removeClass('active');
                $('.filter-overlay').removeClass('active');
                $('body').css('overflow', '');
            });
        },

        /**
         * Filter Handlers
         */
        filterHandlers: function() {
            const self = this;
            let filterTimeout;

            // Search input
            $('.filter-search input').on('input', function() {
                clearTimeout(filterTimeout);
                filterTimeout = setTimeout(function() {
                    self.applyFilters();
                }, 500);
            });

            // Sort select
            $('.filter-sort select').on('change', function() {
                self.applyFilters();
            });

            // Checkboxes and radios
            $('.filter-checkboxes input, .filter-radios input').on('change', function() {
                self.applyFilters();
            });

            // Pagination
            $(document).on('click', '.filter-pagination a', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                self.applyFilters(page);
            });
        },

        /**
         * Price Range Slider
         */
        priceRange: function() {
            const self = this;
            const minInput = $('.price-range-inputs input[name="min_price"]');
            const maxInput = $('.price-range-inputs input[name="max_price"]');

            // Update on input change
            minInput.add(maxInput).on('change', function() {
                self.applyFilters();
            });

            // Sync with slider if using range input
            if ($('input[type="range"]').length) {
                $('input[type="range"]').on('input', function() {
                    const min = $(this).data('min');
                    const max = $(this).data('max');
                    $('.price-range-values').html('<span>$' + min + '</span><span>$' + max + '</span>');
                });

                $('input[type="range"]').on('change', function() {
                    self.applyFilters();
                });
            }
        },

        /**
         * Apply Filters (AJAX)
         */
        applyFilters: function(page = 1) {
            const container = $('.products-grid');
            const filterData = this.getFilterData();
            filterData.page = page;

            // Show loading state
            container.addClass('products-loading');

            $.ajax({
                url: nexusFilterData.ajaxurl,
                type: 'POST',
                data: {
                    action: 'nexus_filter_products',
                    nonce: nexusFilterData.nonce,
                    ...filterData
                },
                success: function(response) {
                    if (response.success) {
                        // Update products
                        container.html(response.data.html);

                        // Update results count
                        $('.results-count').html(
                            'Showing <strong>' + response.data.found + '</strong> products'
                        );

                        // Update pagination
                        NexusProductFilter.updatePagination(response.data.max_pages, page);

                        // Update active filters
                        NexusProductFilter.updateActiveFilters(filterData);

                        // Scroll to top of results
                        $('html, body').animate({
                            scrollTop: $('.nexus-product-filter-container').offset().top - 100
                        }, 300);
                    } else {
                        container.html('<div class="no-results"><h3>No products found</h3><p>Try adjusting your filters</p></div>');
                    }
                },
                error: function() {
                    container.html('<div class="no-results"><h3>Error</h3><p>Something went wrong. Please try again.</p></div>');
                },
                complete: function() {
                    container.removeClass('products-loading');
                }
            });
        },

        /**
         * Get Filter Data
         */
        getFilterData: function() {
            const data = {};

            // Search
            const search = $('.filter-search input').val();
            if (search) data.search = search;

            // Sort
            const sort = $('.filter-sort select').val();
            if (sort) data.sort = sort;

            // Categories
            const categories = [];
            $('.filter-checkboxes.categories input:checked').each(function() {
                categories.push($(this).val());
            });
            if (categories.length) data.categories = categories;

            // Tags
            const tags = [];
            $('.filter-checkboxes.tags input:checked').each(function() {
                tags.push($(this).val());
            });
            if (tags.length) data.tags = tags;

            // Specifications
            const specs = {};
            $('.filter-checkboxes.specifications input:checked').each(function() {
                const key = $(this).data('spec-key');
                const value = $(this).val();
                if (!specs[key]) specs[key] = [];
                specs[key].push(value);
            });
            if (Object.keys(specs).length) data.specifications = specs;

            // Price range
            const minPrice = $('.price-range-inputs input[name="min_price"]').val();
            const maxPrice = $('.price-range-inputs input[name="max_price"]').val();
            if (minPrice) data.min_price = minPrice;
            if (maxPrice) data.max_price = maxPrice;

            return data;
        },

        /**
         * Update Pagination
         */
        updatePagination: function(maxPages, currentPage) {
            const pagination = $('.filter-pagination');
            if (!pagination.length || maxPages <= 1) {
                pagination.hide();
                return;
            }

            let html = '';

            // Previous button
            if (currentPage > 1) {
                html += '<a href="#" data-page="' + (currentPage - 1) + '">←</a>';
            }

            // Page numbers
            for (let i = 1; i <= maxPages; i++) {
                if (i === currentPage) {
                    html += '<span class="current">' + i + '</span>';
                } else if (
                    i === 1 || 
                    i === maxPages || 
                    (i >= currentPage - 2 && i <= currentPage + 2)
                ) {
                    html += '<a href="#" data-page="' + i + '">' + i + '</a>';
                } else if (i === currentPage - 3 || i === currentPage + 3) {
                    html += '<span>...</span>';
                }
            }

            // Next button
            if (currentPage < maxPages) {
                html += '<a href="#" data-page="' + (currentPage + 1) + '">→</a>';
            }

            pagination.html(html).show();
        },

        /**
         * Update Active Filters Display
         */
        updateActiveFilters: function(filterData) {
            const container = $('.active-filters');
            if (!container.length) return;

            container.empty();

            let hasFilters = false;

            // Search
            if (filterData.search) {
                hasFilters = true;
                container.append(
                    '<span class="active-filter-tag">Search: ' + filterData.search + 
                    '<button onclick="$(\".filter-search input\").val(\"\"); NexusProductFilter.applyFilters();">×</button></span>'
                );
            }

            // Categories
            if (filterData.categories) {
                hasFilters = true;
                filterData.categories.forEach(function(cat) {
                    const label = $('.filter-checkboxes.categories input[value="' + cat + '"]').parent().text().trim();
                    container.append(
                        '<span class="active-filter-tag">' + label + 
                        '<button data-filter="category" data-value="' + cat + '">×</button></span>'
                    );
                });
            }

            // Clear all button
            if (hasFilters) {
                container.append(
                    '<button class="clear-all-filters">Clear All</button>'
                );
            }

            // Bind remove handlers
            container.find('.active-filter-tag button').on('click', function() {
                const filterType = $(this).data('filter');
                const value = $(this).data('value');
                
                if (filterType === 'category') {
                    $('.filter-checkboxes.categories input[value="' + value + '"]').prop('checked', false);
                }
                
                NexusProductFilter.applyFilters();
            });
        },

        /**
         * Clear All Filters
         */
        clearFilters: function() {
            $(document).on('click', '.clear-all-filters', function(e) {
                e.preventDefault();
                $('.filter-search input').val('');
                $('.filter-sort select').val('');
                $('.filter-checkboxes input, .filter-radios input').prop('checked', false);
                $('.price-range-inputs input').val('');
                NexusProductFilter.applyFilters();
            });
        },

        /**
         * View Toggle (Grid/List)
         */
        viewToggle: function() {
            $('.view-toggle button').on('click', function() {
                $('.view-toggle button').removeClass('active');
                $(this).addClass('active');
                
                const view = $(this).data('view');
                const grid = $('.products-grid');
                
                if (view === 'list') {
                    grid.addClass('list-view').removeClass('grid-view');
                } else {
                    grid.addClass('grid-view').removeClass('list-view');
                }
            });
        },

        /**
         * Collapsible Filter Sections
         */
        collapsibleSections: function() {
            $('.filter-section h3').on('click', function() {
                $(this).parent().toggleClass('collapsed');
            });
        }
    };

    /**
     * Initialize on Document Ready
     */
    $(document).ready(function() {
        if ($('.nexus-product-filter-container').length) {
            NexusProductFilter.init();
        }
    });

    // Make accessible globally
    window.NexusProductFilter = NexusProductFilter;

})(jQuery);