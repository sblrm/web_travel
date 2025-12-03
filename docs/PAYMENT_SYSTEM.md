# 💳 Payment System Documentation

Complete manual payment system for CulturalTrip without payment gateway integration.

## 📋 Overview

The payment system allows users to book destination tickets and make manual payments via bank transfer, e-wallet, or cash. Admin verifies payments through the Filament admin panel.

### Key Features

- **Manual Payment Processing**: No payment gateway required
- **Multiple Payment Methods**: Bank transfer, e-wallet (GoPay/OVO/DANA), cash
- **Proof Upload**: Users upload payment screenshots
- **Admin Verification**: Manual verification workflow
- **24-Hour Expiry**: Bookings expire if not paid within 24 hours
- **Automated Notifications**: Email + database notifications
- **Invoice Generation**: Printable invoices for verified payments

## 🗄️ Database Schema

### Tables

#### `payment_methods`
Stores available payment options configured by admin.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| name | string | Display name (e.g., "Transfer BCA") |
| type | string | Payment type: `bank_transfer`, `ewallet`, `cash` |
| code | string | Unique code (e.g., "bca", "gopay") |
| account_number | text | Bank account or phone number |
| account_name | string | Account holder name |
| instructions | text | Payment instructions for users |
| icon | string | Icon/logo path (optional) |
| is_active | boolean | Enable/disable method |
| sort_order | integer | Display order |

**Indexes**: `type`, `is_active`, unique(`code`)

#### `bookings`
User ticket bookings with expiry tracking.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| booking_code | string | Unique code (BK-YYYYMMDD-XXXX) |
| user_id | bigint | Foreign key to users |
| destination_id | bigint | Foreign key to destinations |
| visit_date | date | Planned visit date |
| quantity | integer | Number of tickets |
| unit_price | decimal | Price per ticket (snapshot) |
| total_amount | decimal | Total booking amount |
| visitor_name | string | Main visitor name |
| visitor_email | string | Contact email |
| visitor_phone | string | Contact phone |
| notes | text | Special requests (nullable) |
| status | enum | Booking status (see below) |
| expires_at | timestamp | 24-hour expiry deadline |
| cancelled_at | timestamp | Cancellation timestamp |
| cancellation_reason | text | Why cancelled |

**Status Enum**: `pending`, `awaiting_payment`, `paid`, `verified`, `completed`, `cancelled`, `expired`

**Indexes**: unique(`booking_code`), `user_id`, `destination_id`, `status`, `expires_at`

#### `payments`
Payment records with proof upload and verification.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| payment_code | string | Unique code (PAY-YYYYMMDD-XXXX) |
| booking_id | bigint | Foreign key to bookings |
| payment_method_id | bigint | Foreign key to payment_methods |
| amount | decimal | Payment amount |
| proof_image | string | Uploaded proof path |
| account_holder_name | string | Payer's account name |
| transfer_from | string | Source account/number |
| notes | text | Payment notes (nullable) |
| status | enum | Payment status (see below) |
| verified_by | bigint | Admin user ID who verified |
| verified_at | timestamp | Verification timestamp |
| rejection_reason | text | Why rejected |

**Status Enum**: `pending`, `uploaded`, `verified`, `rejected`

**Indexes**: unique(`payment_code`), `booking_id`, `payment_method_id`, `status`, `verified_by`

## 🔄 Booking Flow

### 1. User Creates Booking

**Route**: `POST /destinasi/{destination:slug}/booking`  
**Controller**: `BookingController@store`  
**Form Request**: `StoreBookingRequest`

**Process**:
1. Validate input (visit date, quantity, payment method, visitor details)
2. Create booking with status `awaiting_payment`
3. Set `expires_at` to 24 hours from now
4. Create associated payment record with status `pending`
5. Send `BookingConfirmationNotification` (email + database)
6. Redirect to booking detail page

**Validation Rules**:
```php
'visit_date' => 'required|date|after_or_equal:today'
'quantity' => 'required|integer|min:1|max:50'
'payment_method_id' => 'required|exists:payment_methods,id'
'visitor_name' => 'required|string|max:255'
'visitor_email' => 'required|email|max:255'
'visitor_phone' => 'required|string|max:20'
'notes' => 'nullable|string|max:500'
```

### 2. User Uploads Payment Proof

**Route**: `POST /payments/{booking}/upload`  
**Controller**: `PaymentController@upload`  
**Form Request**: `UploadPaymentProofRequest`

**Process**:
1. Authorize: User owns booking AND `canUploadPayment()` returns true
2. Validate proof image (jpg/jpeg/png, max 2MB)
3. Delete old proof if exists
4. Store new proof in `storage/app/public/payment-proofs/`
5. Update payment record with proof details
6. Change payment status to `uploaded`
7. Send notification to admin (TODO: implement)
8. Redirect back with success message

**Validation Rules**:
```php
'proof_image' => 'required|image|mimes:jpg,jpeg,png|max:2048'
'account_holder_name' => 'required|string|max:255'
'transfer_from' => 'required|string|max:255'
'notes' => 'nullable|string|max:500'
```

### 3. Admin Verifies Payment

**Location**: Filament Admin Panel → Payments Resource  
**Actions**: Verify, Reject

**Verify Process**:
1. Admin reviews uploaded proof
2. Clicks "Verify" action in Filament
3. Payment status → `verified`
4. Payment `verified_by` → admin user ID
5. Payment `verified_at` → current timestamp
6. Booking status → `paid`
7. Send `PaymentVerifiedNotification` to user
8. Invoice becomes available

**Reject Process**:
1. Admin enters rejection reason
2. Payment status → `rejected`
3. Send `PaymentRejectedNotification` to user
4. User can re-upload corrected proof

### 4. Booking Completion

After payment verification, booking moves through:

- **Verified** (`verified`): Ready for visit
- **Completed** (`completed`): User has visited (manual admin update)

## 🎯 Status State Machines

### Booking States

```
pending → awaiting_payment → paid → verified → completed
                  ↓
              cancelled
                  ↓
               expired
```

**Status Helpers** (in `Booking` model):
```php
isPending() → status === 'pending'
isAwaitingPayment() → status === 'awaiting_payment'
isPaid() → status === 'paid'
isVerified() → status === 'verified'
isCompleted() → status === 'completed'
isCancelled() → status === 'cancelled'
isExpired() → status === 'expired'
```

**Status Transitions**:
```php
markAsAwaitingPayment()
markAsPaid()
markAsVerified()
markAsCompleted()
markAsCancelled($reason)
markAsExpired()
```

**Business Logic Guards**:
```php
canCancel() → Only if awaiting_payment or pending
canUploadPayment() → Only if awaiting_payment and not expired
isExpiredTime() → Current time > expires_at
```

### Payment States

```
pending → uploaded → verified
                  ↓
               rejected
```

**Status Helpers** (in `Payment` model):
```php
isPending() → status === 'pending'
isUploaded() → status === 'uploaded'
isVerified() → status === 'verified'
isRejected() → status === 'rejected'
```

**Status Transitions**:
```php
markAsUploaded()
markAsVerified($verifiedBy) → Also calls booking->markAsPaid()
markAsRejected($reason)
```

**Business Logic Guards**:
```php
canUploadProof() → Only if pending or rejected
canVerify() → Only if uploaded
canReject() → Only if uploaded
```

## 📧 Notifications

All notifications implement `ShouldQueue` for async delivery.

### BookingConfirmationNotification

**Triggered**: On booking creation (`BookingObserver@created`)  
**Sent to**: Booking owner  
**Channels**: Email + Database

**Content**:
- Booking code
- Destination name
- Visit date
- Total amount
- Payment deadline (24 hours)
- Link to booking detail page

### PaymentUploadedNotification (TODO)

**Triggered**: When user uploads payment proof  
**Sent to**: Admin users  
**Channels**: Database

**Content**:
- Booking code
- Payment amount
- Upload timestamp
- Link to verify in admin panel

### PaymentVerifiedNotification (TODO)

**Triggered**: When admin verifies payment  
**Sent to**: Booking owner  
**Channels**: Email + Database

**Content**:
- Payment verification confirmation
- Booking details
- Link to download invoice

### PaymentRejectedNotification (TODO)

**Triggered**: When admin rejects payment  
**Sent to**: Booking owner  
**Channels**: Email + Database

**Content**:
- Rejection reason
- Instructions to re-upload
- Link to upload page

## 🔒 Authorization (BookingPolicy)

```php
viewAny(User $user) → true (any authenticated user)
view(User $user, Booking $booking) → user owns booking
create(User $user) → true (any authenticated user)
update(User $user, Booking $booking) → user owns booking AND canCancel()
cancel(User $user, Booking $booking) → user owns booking AND canCancel()
uploadPayment(User $user, Booking $booking) → user owns booking AND canUploadPayment()
```

## 🛣️ Routes

### Public Routes

```php
GET  /destinasi/{destination:slug}/booking → bookings.create
POST /destinasi/{destination:slug}/booking → bookings.store
```

### Authenticated Routes

```php
GET  /bookings → bookings.index (user's booking list)
GET  /bookings/{booking} → bookings.show (booking detail)
POST /bookings/{booking}/cancel → bookings.cancel

GET  /payments/{booking} → payments.show (payment detail)
POST /payments/{booking}/upload → payments.upload (upload proof)
GET  /payments/{booking}/invoice → payments.invoice (download invoice)
```

## 🎨 Blade Views (TODO)

### Booking Views

- `resources/views/bookings/create.blade.php` - Booking form
- `resources/views/bookings/show.blade.php` - Booking detail with payment status
- `resources/views/bookings/index.blade.php` - User's booking list

### Payment Views

- `resources/views/payments/show.blade.php` - Payment detail and upload form
- `resources/views/payments/invoice.blade.php` - Printable invoice

## 🛠️ Filament Admin Resources (TODO)

### BookingResource

**List View**:
- Filters: Status, date range, destination
- Columns: Booking code, destination, user, total amount, status, created date
- Actions: View, mark as completed, cancel

**Form View**:
- Read-only booking details
- Payment status indicator
- Link to payment verification

### PaymentResource

**List View**:
- Filters: Status, payment method, date range
- Columns: Payment code, booking code, amount, method, status, uploaded date
- Bulk actions: Verify selected, reject selected

**Form View**:
- Payment details
- Uploaded proof image viewer
- Verify/Reject actions with reason input

### PaymentMethodResource

**List View**:
- Columns: Name, type, code, account number, active status, order
- Drag-and-drop reordering
- Toggle active status

**Form View**:
- All payment method fields
- Instructions textarea with rich editor
- Icon upload

## 🔧 Model Methods Reference

### Booking Model (30 methods)

**Relationships**:
- `user()` → BelongsTo User
- `destination()` → BelongsTo Destination
- `payment()` → HasOne Payment

**Scopes**:
- `scopeActive()` → Not cancelled or expired
- `scopeExpired()` → Expired bookings
- `scopeAwaitingPayment()` → Status awaiting_payment
- `scopePaid()` → Status paid
- `scopeVerified()` → Status verified

**Status Helpers**: (see Status State Machines)

**Accessors**:
- `getFormattedTotalAmountAttribute()` → "Rp 150.000"
- `getFormattedUnitPriceAttribute()` → "Rp 50.000"
- `getFormattedVisitDateAttribute()` → "Senin, 25 Desember 2025"
- `getFormattedExpiresAtAttribute()` → "25 Des 2025, 14:30"
- `getStatusLabelAttribute()` → Localized status label
- `getStatusColorAttribute()` → Badge color for UI

**Code Generation**:
- Auto-generates `booking_code` on creation: `BK-20251202-0001`

### Payment Model (22 methods)

**Relationships**:
- `booking()` → BelongsTo Booking
- `paymentMethod()` → BelongsTo PaymentMethod
- `verifier()` → BelongsTo User (verified_by)

**Scopes**:
- `scopePending()` → Status pending
- `scopeUploaded()` → Status uploaded
- `scopeVerified()` → Status verified
- `scopeRejected()` → Status rejected

**Status Helpers**: (see Status State Machines)

**Accessors**:
- `getFormattedAmountAttribute()` → "Rp 150.000"
- `getProofImageUrlAttribute()` → Full URL to proof image
- `getStatusLabelAttribute()` → Localized status label
- `getStatusColorAttribute()` → Badge color for UI

**Code Generation**:
- Auto-generates `payment_code` on creation: `PAY-20251202-0001`

### PaymentMethod Model (13 methods)

**Relationships**:
- `payments()` → HasMany Payment

**Scopes**:
- `scopeActive()` → is_active = true
- `scopeOrdered()` → Order by sort_order, name

**Type Helpers**:
- `isBankTransfer()` → type === 'bank_transfer'
- `isEWallet()` → type === 'ewallet'
- `isCash()` → type === 'cash'

**Accessors**:
- `getFormattedAccountNumberAttribute()` → Masked for security
- `getTypeNameAttribute()` → Localized type name

## ⚙️ Configuration

### Default Payment Methods

Seeded by `PaymentMethodSeeder`:

**Bank Transfer**:
- BCA (1234567890)
- Mandiri (9876543210)
- BNI (0123456789)

**E-Wallet**:
- GoPay (081234567890)
- OVO (081234567891)
- DANA (081234567892)

**Cash**:
- Bayar di Lokasi (Cash)

### Booking Expiry

**Default**: 24 hours from creation  
**Configured in**: `Booking::boot()` method

To change expiry duration:
```php
protected static function boot()
{
    parent::boot();
    
    static::creating(function ($booking) {
        // Change 24 to desired hours
        $booking->expires_at = now()->addHours(48);
    });
}
```

### Storage Configuration

Payment proofs stored in: `storage/app/public/payment-proofs/`

**Web access**: `/storage/payment-proofs/{filename}`

Ensure storage is linked:
```bash
php artisan storage:link
```

## 🧪 Testing (TODO)

### Feature Tests to Implement

- `BookingCreationTest` - User can create booking
- `BookingCancellationTest` - User can cancel unpaid booking
- `PaymentProofUploadTest` - User can upload payment proof
- `PaymentVerificationTest` - Admin can verify payment
- `PaymentRejectionTest` - Admin can reject payment
- `BookingExpiryTest` - Bookings expire after 24 hours
- `BookingAuthorizationTest` - Users can only see own bookings
- `NotificationTest` - Notifications sent correctly

### Example Test Structure

```php
test('user can create booking', function () {
    $user = User::factory()->create();
    $destination = Destination::factory()->create(['ticket_price' => 50000]);
    $paymentMethod = PaymentMethod::factory()->create();
    
    $response = actingAs($user)->post(route('bookings.store', $destination), [
        'visit_date' => now()->addDays(7)->format('Y-m-d'),
        'quantity' => 2,
        'payment_method_id' => $paymentMethod->id,
        'visitor_name' => 'John Doe',
        'visitor_email' => 'john@example.com',
        'visitor_phone' => '081234567890',
    ]);
    
    $response->assertRedirect();
    assertDatabaseHas('bookings', [
        'user_id' => $user->id,
        'destination_id' => $destination->id,
        'quantity' => 2,
        'total_amount' => 100000,
    ]);
});
```

## 📝 TODO Implementation Checklist

### Backend (Completed ✅)

- ✅ Database migrations
- ✅ Booking model with state machine
- ✅ Payment model with verification workflow
- ✅ PaymentMethod model
- ✅ BookingController
- ✅ PaymentController
- ✅ Form request validation
- ✅ BookingPolicy authorization
- ✅ Routes registration
- ✅ BookingObserver for notifications
- ✅ PaymentObserver skeleton
- ✅ BookingConfirmationNotification
- ✅ PaymentMethodSeeder

### Frontend (Pending ⏳)

- ⏳ Booking creation form blade view
- ⏳ Booking detail page with payment status
- ⏳ User booking list page
- ⏳ Payment proof upload form
- ⏳ Invoice view (printable)
- ⏳ Add "Book Now" button to destination detail page
- ⏳ Add booking link to user dashboard

### Admin Panel (Pending ⏳)

- ⏳ BookingResource (Filament)
- ⏳ PaymentResource with verify/reject actions
- ⏳ PaymentMethodResource
- ⏳ Custom verify/reject actions in Filament
- ⏳ Payment proof image viewer in admin
- ⏳ Booking statistics widget

### Notifications (Partial ✅)

- ✅ BookingConfirmationNotification
- ⏳ PaymentUploadedNotification (admin notification)
- ⏳ PaymentVerifiedNotification
- ⏳ PaymentRejectedNotification
- ⏳ Complete PaymentObserver implementation

### Testing (Pending ⏳)

- ⏳ Feature tests for booking flow
- ⏳ Feature tests for payment flow
- ⏳ Authorization tests
- ⏳ Notification tests
- ⏳ Expiry logic tests

### Documentation (Completed ✅)

- ✅ This comprehensive documentation
- ✅ Updated copilot-instructions.md

## 🚀 Deployment Considerations

### Queue Worker Required

Notifications are queued. Ensure queue worker is running:

```bash
php artisan queue:work
```

For production, use Supervisor or Laravel Cloud's built-in queue management.

### Storage Permissions

Ensure `storage/app/public` is writable:

```bash
chmod -R 775 storage/app/public
chown -R www-data:www-data storage/app/public
```

### Cron Job for Expiry

Add cron job to mark expired bookings (TODO: implement artisan command):

```bash
* * * * * cd /path-to-project && php artisan booking:expire-old
```

### Environment Variables

No additional env variables required. All configuration in database.

---

**Last Updated**: December 2, 2025  
**Status**: Backend implementation complete, frontend and admin panel pending  
**Version**: 1.0.0
