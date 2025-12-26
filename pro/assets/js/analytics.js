/**
 * Analytics - JavaScript
 * @package Nexus_Pro
 * @since 3.0.0
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        initCharts();
        
        $('#nexus-analytics-period').on('change', function() {
            loadAnalytics($(this).val());
        });

        $('#nexus-analytics-export').on('click', function() {
            exportData();
        });
    });

    function initCharts() {
        // Views chart
        var viewsCtx = document.getElementById('nexus-views-chart');
        if (viewsCtx) {
            new Chart(viewsCtx, {
                type: 'line',
                data: {
                    labels: getLast7Days(),
                    datasets: [{
                        label: nexusAnalytics.i18n.pageViews,
                        data: [120, 150, 180, 200, 175, 210, 230],
                        borderColor: '#2196f3',
                        backgroundColor: 'rgba(33, 150, 243, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {responsive: true, maintainAspectRatio: false}
            });
        }

        // Sources chart
        var sourcesCtx = document.getElementById('nexus-sources-chart');
        if (sourcesCtx) {
            new Chart(sourcesCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Direct', 'Search', 'Social', 'Referral'],
                    datasets: [{
                        data: [40, 30, 20, 10],
                        backgroundColor: ['#2196f3', '#4caf50', '#ff9800', '#e91e63']
                    }]
                },
                options: {responsive: true, maintainAspectRatio: false}
            });
        }
    }

    function getLast7Days() {
        var days = [];
        for (var i = 6; i >= 0; i--) {
            var d = new Date();
            d.setDate(d.getDate() - i);
            days.push(d.toLocaleDateString('en-US', {month: 'short', day: 'numeric'}));
        }
        return days;
    }

    function loadAnalytics(period) {
        $.ajax({
            url: nexusAnalytics.ajaxUrl,
            type: 'POST',
            data: {action: 'nexus_analytics_data', nonce: nexusAnalytics.nonce, period: period},
            success: function(response) {
                if (response.success) {
                    updateDashboard(response.data);
                }
            }
        });
    }

    function exportData() {
        $.ajax({
            url: nexusAnalytics.ajaxUrl,
            type: 'POST',
            data: {action: 'nexus_analytics_export', nonce: nexusAnalytics.nonce},
            success: function(response) {
                if (response.success) {
                    var dataStr = JSON.stringify(response.data.data, null, 2);
                    var dataUri = 'data:application/json;charset=utf-8,'+ encodeURIComponent(dataStr);
                    var exportFileDefaultName = 'analytics-export.json';
                    var linkElement = document.createElement('a');
                    linkElement.setAttribute('href', dataUri);
                    linkElement.setAttribute('download', exportFileDefaultName);
                    linkElement.click();
                }
            }
        });
    }

    function updateDashboard(data) {
        // Update stats here
    }

})(jQuery);
