# Payment System Setup Guide

## Overview

This application includes a comprehensive payment system for tenant subscription management with support for both Stripe and Razorpay payment gateways.

## Features

-   **Multi-Gateway Support**: Stripe and Razorpay integration
-   **Plan Management**: Flexible subscription plans (LITE, PLUS, PREMIUM)
-   **Restaurant-based Pricing**: Base fee + per-restaurant charges
-   **Secure Checkout**: Modern, responsive payment interface
-   **Payment Tracking**: Complete payment history and status tracking
-   **User Experience**: Smooth flow for pending approval users

## Payment Flow

1. **Tenant Creation**: New tenants are created with `pending` status
2. **User Login**: Tenant admins can login with `pending_approval` status
3. **Payment Required**: Middleware restricts access until payment completion
4. **Plan Selection**: Users can choose from available subscription plans
5. **Secure Checkout**: Multiple payment methods (Card, UPI, Net Banking, Wallet)
6. **Payment Processing**: Stripe/Razorpay integration for secure transactions
7. **Account Activation**: Successful payment activates tenant and user accounts

## Configuration

### Environment Variables

Add these to your `.env` file:

```bash
# Stripe Configuration
STRIPE_KEY=pk_test_your_stripe_publishable_key
STRIPE_SECRET=sk_test_your_stripe_secret_key
STRIPE_WEBHOOK_SECRET=whsec_your_stripe_webhook_secret


### Stripe Setup

1. Create a Stripe account at https://stripe.com
2. Get your API keys from the Stripe Dashboard
3. Add the keys to your `.env` file
4. Test with Stripe test cards:
    - Success: `4242424242424242`
    - Decline: `4000000000000002`

### Razorpay Setup

1. Create a Razorpay account at https://razorpay.com
2. Get your API keys from the Razorpay Dashboard
3. Add the keys to your `.env` file
4. Configure webhook endpoints if needed

## Database Schema

### Subscription Payments Table

```sql
CREATE TABLE subscription_payments (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    tenant_id BIGINT NOT NULL,
    subscription_plan ENUM('LITE', 'PLUS', 'PREMIUM'),
    restaurant_count INT,
    base_amount DECIMAL(10,2),
    per_restaurant_amount DECIMAL(10,2),
    total_amount DECIMAL(10,2),
    billing_period_start DATE,
    billing_period_end DATE,
    due_date DATE,
    payment_method ENUM('card', 'upi', 'netbanking', 'wallet'),
    payment_gateway ENUM('stripe', 'razorpay'),
    gateway_payment_id VARCHAR(255),
    gateway_payment_status VARCHAR(100),
    gateway_transaction_id VARCHAR(255),
    status ENUM('pending', 'completed', 'failed', 'cancelled'),
    paid_at TIMESTAMP NULL,
    failure_reason TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## Usage

### For Developers

1. **Creating Payment**: Use `PaymentController@createPayment`
2. **Processing Success**: Use `PaymentController@paymentSuccess`
3. **Handling Failure**: Use `PaymentController@paymentFailure`
4. **Payment History**: Use `PaymentController@history`

### For Users

1. Navigate to the payment plans page
2. Review your current plan and pricing
3. Click "Pay Now" to proceed to checkout
4. Select payment method and gateway
5. Complete the secure payment process
6. Account is automatically activated upon successful payment

## Security Features

-   **CSRF Protection**: All forms include CSRF tokens
-   **Input Validation**: Comprehensive request validation
-   **SQL Injection Prevention**: Using Eloquent ORM
-   **XSS Prevention**: Blade template escaping
-   **Secure Payment**: HTTPS required for payment processing
-   **Gateway Security**: Official Stripe/Razorpay SDKs

## Troubleshooting

### Common Issues

1. **Payment Fails**: Check API keys and network connectivity
2. **Invalid Amount**: Ensure amount calculations are correct
3. **Gateway Errors**: Check gateway-specific error messages
4. **Database Errors**: Verify database connections and table structure

### Debug Steps

1. Check Laravel logs: `storage/logs/laravel.log`
2. Verify environment variables are loaded
3. Test with payment gateway test data
4. Check network connectivity to gateway APIs

## Testing

### Test Cards (Stripe)

-   **Success**: `4242424242424242`
-   **Decline**: `4000000000000002`
-   **Require 3D Secure**: `4000002500003155`

### Test Data (Razorpay)

-   Use Razorpay test mode for development
-   Check Razorpay documentation for test credentials

## Support

For payment gateway specific issues:

-   **Stripe**: https://stripe.com/docs
-   **Razorpay**: https://razorpay.com/docs

For application issues:

-   Check application logs
-   Review payment flow implementation
-   Verify environment configuration
