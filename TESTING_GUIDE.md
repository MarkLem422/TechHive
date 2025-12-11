# Testing Guide for Login & Dashboard

## Step 1: Set Up Database

### 1.1 Run Migrations
```bash
cd TechHive
php artisan migrate
```

This will create all necessary tables including:
- `users` table (for authentication)
- `customers`, `products`, `categories`, `orders`, etc.

### 1.2 Create a Test User

You have two options:

**Option A: Using Tinker (Recommended)**
```bash
php artisan tinker
```
Then run:
```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'Admin User',
    'email' => 'admin@techhive.com',
    'password' => Hash::make('password123'),
]);
```

**Option B: Using Database Seeder**
```bash
php artisan db:seed
```
This will create a user with:
- Email: `test@example.com`
- Password: `password` (default from factory)

## Step 2: Start the Development Server

```bash
php artisan serve
```

The application will be available at: `http://localhost:8000`

## Step 3: Test the Login Page

1. **Navigate to Login Page**
   - Go to: `http://localhost:8000/login`
   - Or click "Log in" link from the homepage

2. **Test Login Form**
   - Enter your test email (e.g., `admin@techhive.com` or `test@example.com`)
   - Enter your password (e.g., `password123` or `password`)
   - Click "Log in"

3. **Expected Results:**
   - ✅ Successful login redirects to `/dashboard`
   - ✅ Failed login shows error message
   - ✅ "Remember me" checkbox works (keeps you logged in)

## Step 4: Test the Dashboard

1. **After Login**
   - You should see the dashboard with statistics cards
   - All cards should show `0` initially (no data yet)

2. **Dashboard Features to Test:**
   - ✅ Statistics cards display correctly
   - ✅ Recent orders table (empty initially)
   - ✅ Logout button works
   - ✅ Navigation links work

## Step 5: Add Test Data (Optional)

To see the dashboard with actual data, you can add test records:

### Using Tinker:
```bash
php artisan tinker
```

```php
// Create a category
use App\Models\Category;
Category::create(['category_name' => 'Electronics', 'description' => 'Electronic products']);

// Create a product
use App\Models\Product;
Product::create([
    'product_name' => 'Laptop',
    'description' => 'High-performance laptop',
    'price' => 999.99,
    'category_id' => 1
]);

// Create a customer
use App\Models\Customer;
Customer::create([
    'first_name' => 'John',
    'last_name' => 'Doe',
    'email' => 'john@example.com',
    'phone' => '123-456-7890'
]);

// Create an order
use App\Models\Order;
Order::create([
    'customer_id' => 1,
    'order_date' => now(),
    'total_amount' => 999.99,
    'status' => 'pending'
]);
```

## Step 6: Test Logout

1. Click the "Logout" button in the dashboard header
2. You should be redirected to the homepage
3. Try accessing `/dashboard` directly - you should be redirected to login

## Troubleshooting

### Issue: "Table 'users' doesn't exist"
**Solution:** Run migrations:
```bash
php artisan migrate
```

### Issue: "Class 'App\Models\User' not found"
**Solution:** Clear cache:
```bash
php artisan config:clear
php artisan cache:clear
```

### Issue: Login not working
**Check:**
- User exists in database
- Password is hashed correctly
- Email matches exactly (case-sensitive)

### Issue: Dashboard shows errors
**Check:**
- All migrations are run
- Database connection is working
- Check browser console for JavaScript errors

## Quick Test Checklist

- [ ] Migrations run successfully
- [ ] Test user created
- [ ] Can access `/login` page
- [ ] Login form displays correctly
- [ ] Can login with valid credentials
- [ ] Redirects to dashboard after login
- [ ] Dashboard displays statistics
- [ ] Logout button works
- [ ] Cannot access dashboard when logged out
- [ ] Error messages display for invalid login

## Test Credentials (After Setup)

**User 1 (from seeder):**
- Email: `test@example.com`
- Password: `password`

**User 2 (if created manually):**
- Email: `admin@techhive.com`
- Password: `password123`

