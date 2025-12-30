/**
 * Performance Analytics JavaScript
 * @package Nexus_Pro
 * @since 3.0.0
 */
(function($) {
'use strict';

const PerformanceAnalytics = {
ull,
ull,
tMetrics: null,
ge: 24,
it: function() {
dEvents();
itCharts();
dEvents: function() {
('click', () => this.loadMetrics());
ge').on('change', (e) => {
ge = parseInt($(e.target).val());
('click', () => this.exportReport());
('click', () => this.clearMetrics());
ction() {
exusAnalyticsData.ajaxUrl,
{
: 'nexus_get_metrics',
once: nexusAnalyticsData.nonce,
ge
se) => {
se.success) {
tMetrics = response.data;
itCharts: function() {
st vitalsCtx = document.getElementById('vitals-chart');
= new Chart(vitalsCtx, {
e',
data: [], borderColor: '#0073aa'}]
s: {responsive: true, maintainAspectRatio: false}
ction() {
this.currentMetrics?.timeline) {
st timeline = this.currentMetrics.timeline;
e.map(p => p.time);
e.map(p => p.lcp || 0);
ction() {
exusAnalyticsData.ajaxUrl,
{action: 'nexus_export_report', nonce: nexusAnalyticsData.nonce, format: 'csv'},
se) => {
se.success) {
st blob = new Blob([response.data.content], {type: response.data.mime});
st url = window.URL.createObjectURL(blob);
st a = document.createElement('a');
load = response.data.filename;
ction() {
firm(nexusAnalyticsData.i18n.confirmClear)) {
exusAnalyticsData.ajaxUrl,
{action: 'nexus_clear_metrics', nonce: nexusAnalyticsData.nonce},
this.loadMetrics()
t).ready(function() {
exus-analytics-dashboard').length) {
ceAnalytics.init();
uery);
