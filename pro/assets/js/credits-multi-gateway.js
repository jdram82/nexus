/**
 * Nexus Credits JavaScript - Multi-Gateway Support
 * 
 * @package Nexus_Theme
 * @since 1.6.0
 */

(function($) {
	'use strict';
	
	const CreditManager = {
		
		stripe: null,
		cardElement: null,
		currentOrderId: null,
		currentClientSecret: null,
		gateway: null,
		
		init() {
			this.gateway = nexusCredits.gateway || 'razorpay';
			this.initGateway();
			this.bindEvents();
			this.updateCustomPrice();
		},
		
		initGateway() {
			if (this.gateway === 'stripe' && typeof Stripe !== 'undefined' && nexusCredits.gatewayKey) {
				this.stripe = Stripe(nexusCredits.gatewayKey);
				const elements = this.stripe.elements();
				this.cardElement = elements.create('card');
			}
		},
		
		bindEvents() {
			// Buy package
			$('.buy-package').on('click', (e) => this.buyPackage(e));
			
			// Buy custom amount
			$('#buy-custom').on('click', () => this.buyCustom());
			
			// Custom credits input
			$('#custom-credits').on('input', () => this.updateCustomPrice());
			
			// Auto-refill toggle
			$('#enable-auto-refill').on('change', (e) => this.toggleAutoRefill(e));
			
			// Save auto-refill
			$('#save-auto-refill').on('click', () => this.saveAutoRefill());
			
			// Modal close
			$('.modal-close').on('click', () => this.closeModal());
		},
		
		buyPackage(e) {
			const $package = $(e.currentTarget).closest('.credit-package');
			const credits = parseInt($package.data('credits'));
			const price = parseFloat($package.data('price'));
			
			this.openPaymentModal(credits, price);
		},
		
		buyCustom() {
			const credits = parseInt($('#custom-credits').val());
			const price = parseFloat($('#custom-price').text().replace(/[^0-9.]/g, ''));
			
			if (credits < 10) {
				alert('Minimum purchase is 10 credits');
				return;
			}
			
			this.openPaymentModal(credits, price);
		},
		
		updateCustomPrice() {
			const credits = parseInt($('#custom-credits').val()) || 0;
			const pricePerCredit = parseFloat($('#custom-credits').data('price')) || 0.10;
			const total = (credits * pricePerCredit).toFixed(2);
			
			const currency = nexusCredits.currency || 'USD';
			const symbol = currency === 'INR' ? '₹' : '$';
			
			$('#custom-price').text(symbol + total);
		},
		
		openPaymentModal(credits, amount) {
			$('#payment-modal').fadeIn();
			$('#payment-credits').text(credits);
			
			const currency = nexusCredits.currency || 'USD';
			const symbol = currency === 'INR' ? '₹' : '$';
			$('#payment-amount').text(symbol + amount.toFixed(2));
			
			// Mount Stripe card element if using Stripe
			if (this.gateway === 'stripe' && this.cardElement && !this.cardElement._parent) {
				this.cardElement.mount('#card-element');
				$('#card-element').show();
				$('#submit-payment').on('click', () => this.initPayment(credits, amount));
			} else {
				$('#card-element').hide();
				// For Razorpay and Cashfree, payment initiates immediately
				this.initPayment(credits, amount);
			}
		},
		
		closeModal() {
			$('#payment-modal').fadeOut();
			if (this.cardElement && this.cardElement._parent) {
				this.cardElement.unmount();
			}
		},
		
		initPayment(credits, amount) {
			$.ajax({
				url: ajaxurl,
				method: 'POST',
				data: {
					action: 'nexus_create_payment_intent',
					nonce: nexusCredits.nonce,
					credits: credits,
					amount: amount
				},
				success: (response) => {
					if (response.success) {
						this.currentOrderId = response.data.order_id;
						const paymentData = response.data.payment_data;
						
						// Process payment based on gateway
						if (this.gateway === 'razorpay') {
							this.processRazorpay(paymentData, credits, amount);
						} else if (this.gateway === 'stripe') {
							this.processStripe(paymentData, credits, amount);
						} else if (this.gateway === 'cashfree') {
							this.processCashfree(paymentData, credits, amount);
						}
					} else {
						alert('Failed to initialize payment: ' + response.data.message);
					}
				},
				error: (xhr, status, error) => {
					alert('Payment initialization failed: ' + error);
				}
			});
		},
		
		processRazorpay(paymentData, credits, amount) {
			if (typeof Razorpay === 'undefined') {
				alert('Razorpay SDK not loaded');
				return;
			}
			
			const options = {
				key: nexusCredits.gatewayKey,
				amount: paymentData.amount, // Already in paise
				currency: paymentData.currency || 'INR',
				name: 'Nexus AI Credits',
				description: credits + ' AI Credits',
				order_id: paymentData.order_id,
				handler: (response) => {
					this.confirmPurchase(
						response.razorpay_order_id,
						response.razorpay_payment_id,
						response.razorpay_signature
					);
				},
				prefill: {
					email: nexusCredits.userEmail || '',
				},
				theme: {
					color: '#667eea'
				},
				modal: {
					ondismiss: () => {
						this.closeModal();
					}
				}
			};
			
			const rzp = new Razorpay(options);
			rzp.on('payment.failed', (response) => {
				alert('Payment failed: ' + response.error.description);
			});
			
			rzp.open();
		},
		
		processStripe(paymentData, credits, amount) {
			this.currentClientSecret = paymentData.client_secret;
			
			// Button already bound in openPaymentModal for Stripe
			$('#submit-payment').off('click').on('click', () => this.submitStripePayment());
		},
		
		async submitStripePayment() {
			const button = $('#submit-payment');
			button.prop('disabled', true).text('Processing...');
			
			try {
				const {error, paymentIntent} = await this.stripe.confirmCardPayment(
					this.currentClientSecret,
					{
						payment_method: {
							card: this.cardElement
						}
					}
				);
				
				if (error) {
					$('#card-errors').text(error.message);
					button.prop('disabled', false).text('Pay Now');
					return;
				}
				
				if (paymentIntent.status === 'succeeded') {
					this.confirmPurchase(this.currentOrderId, paymentIntent.id, '');
				}
			} catch (err) {
				console.error('Payment error:', err);
				button.prop('disabled', false).text('Pay Now');
				alert('Payment failed: ' + err.message);
			}
		},
		
		async processCashfree(paymentData, credits, amount) {
			if (typeof Cashfree === 'undefined') {
				alert('Cashfree SDK not loaded');
				return;
			}
			
			const cashfree = Cashfree({mode: 'sandbox'}); // Change to 'production' for live
			
			const checkoutOptions = {
				paymentSessionId: paymentData.payment_session_id,
				redirectTarget: '_modal'
			};
			
			try {
				const result = await cashfree.checkout(checkoutOptions);
				
				if (result.error) {
					alert('Payment failed: ' + result.error.message);
				} else if (result.paymentDetails) {
					this.confirmPurchase(
						this.currentOrderId,
						result.paymentDetails.paymentId,
						''
					);
				}
			} catch (err) {
				console.error('Cashfree error:', err);
				alert('Payment failed: ' + err.message);
			}
		},
		
		confirmPurchase(orderId, paymentId, signature) {
			$.ajax({
				url: ajaxurl,
				method: 'POST',
				data: {
					action: 'nexus_confirm_credit_purchase',
					nonce: nexusCredits.nonce,
					order_id: orderId,
					payment_id: paymentId,
					signature: signature
				},
				success: (response) => {
					if (response.success) {
						alert(response.data.message);
						this.closeModal();
						location.reload();
					} else {
						alert('Purchase confirmation failed: ' + response.data.message);
					}
				},
				error: (xhr, status, error) => {
					alert('Purchase confirmation failed: ' + error);
				}
			});
		},
		
		toggleAutoRefill(e) {
			const enabled = $(e.currentTarget).is(':checked');
			$('.auto-refill-settings').toggle(enabled);
		},
		
		saveAutoRefill() {
			const enabled = $('#enable-auto-refill').is(':checked');
			const threshold = parseInt($('#refill-threshold').val());
			const amount = parseInt($('#refill-amount').val());
			
			$.ajax({
				url: ajaxurl,
				method: 'POST',
				data: {
					action: 'nexus_setup_auto_refill',
					nonce: nexusCredits.nonce,
					enabled: enabled,
					threshold: threshold,
					amount: amount
				},
				success: (response) => {
					if (response.success) {
						alert('Auto-refill settings saved!');
					} else {
						alert('Failed to save settings: ' + response.data.message);
					}
				}
			});
		}
	};
	
	// Initialize on document ready
	$(document).ready(function() {
		CreditManager.init();
	});
	
})(jQuery);
