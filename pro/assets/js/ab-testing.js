/**
 * A/B Testing Dashboard JavaScript
 * @package Nexus_Pro
 * @since 3.0.0
 */
(function($) {
'use strict';

const ABTesting = {
it: function() {
dEvents();
dEvents: function() {
('submit', (e) => this.handleCreateTest(e));
d-test').on('click', (e) => this.handleEndTest(e));
('click', (e) => this.handlePauseTest(e));
('click', (e) => this.handleDeleteTest(e));
t').on('click', () => this.addVariant());
('click', (e) => this.viewTestDetails(e));
ction() {
{
st testId = $(card).data('test-id');
card);
ction(testId, container) {
exusABData.ajaxUrl,
{
: 'nexus_ab_results',
once: nexusABData.nonce
se) => {
se.success) {
tainer, response.data);
derChart(testId, response.data);
ction(container, data) {
st $card = $(container);
st variants = data.variants || [];
ts.forEach(variant => {
d(`.variant-${variant.variant_id} .conversion-rate`)
t.conversion_rate + '%');
d(`.variant-${variant.variant_id} .conversions`)
t.conversions} / ${variant.visitors}`);
fidence >= 95) {
d('.winner-badge').show();
derChart: function(testId, data) {
st canvas = document.getElementById(`chart-${testId}`);
vas) return;

st ctx = canvas.getContext('2d');
st variants = data.variants || [];

();
new Chart(ctx, {
{
ts.map(v => `Variant ${v.variant_id}`),
version Rate (%)',
ts.map(v => v.conversion_rate),
dColor: ['#0073aa', '#7c3aed', '#059669', '#ea580c']
s: {
sive: true,
tainAspectRatio: false,
{
AtZero: true,
dleCreateTest: function(e) {
tDefault();
st formData = new FormData(e.target);
st data = {
: 'nexus_ab_create',
once: nexusABData.nonce,
ame: formData.get('test_name'),
pe'),
ts: this.collectVariants(formData)
exusABData.ajaxUrl,
data,
se) => {
se.success) {
dow.location.href = '?page=nexus-ab-testing';
ts: function(formData) {
st variants = [];
t-item').each((i, item) => {
ts.push({
ame: $(item).find('[name*="[name]"]').val(),
t($(item).find('[name*="[traffic]"]').val()),
tent: $(item).find('[name*="[content]"]').val()
 variants;
dleEndTest: function(e) {
firm('Are you sure you want to end this test?')) {
;
st testId = $(e.currentTarget).data('test-id');
exusABData.ajaxUrl,
{
: 'nexus_ab_end',
once: nexusABData.nonce
{
.reload();
dlePauseTest: function(e) {
st testId = $(e.currentTarget).data('test-id');
exusABData.ajaxUrl,
{
: 'nexus_ab_pause',
once: nexusABData.nonce
{
.reload();
dleDeleteTest: function(e) {
firm('Delete this test permanently?')) {
;
st testId = $(e.currentTarget).data('test-id');
exusABData.ajaxUrl,
{
: 'nexus_ab_delete',
once: nexusABData.nonce
{
tTarget).closest('tr').fadeOut();
t: function() {
st count = $('.variant-item').length;
st template = `
t-item" data-variant="${String.fromCharCode(65 + count)}">
t ${String.fromCharCode(65 + count)}</h3>
ame</label></th>
put type="text" name="variants[${count}][name]" value="Variant ${String.fromCharCode(65 + count)}" class="regular-text" required></td>
</label></th>
put type="number" name="variants[${count}][traffic]" value="50" min="0" max="100" class="small-text"> %</td>
tent</label></th>
ame="variants[${count}][content]" rows="5" class="large-text"></textarea></td>
ts-container').append(template);
ction(e) {
, a')) {
;
st testId = $(e.currentTarget).data('test-id');
dow.location.href = `?page=nexus-ab-test-view&test_id=${testId}`;
t).ready(() => {
exus-ab-testing-dashboard, .nexus-ab-test-create').length) {
g.init();
conversions on frontend
if (window.nexusABTests && window.nexusABTests.length) {
t).on('click', '[data-ab-test]', function() {
st testId = $(this).data('ab-test');
dow.nexusTrackConversion) {
dow.nexusTrackConversion(testId);
uery);
