<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EcommerceSeeder extends Seeder
{
    public function run()
    {
        /* -----------------------------
         *   CATEGORIES
         * -----------------------------*/
        DB::table('categories')->insert([
            ['id' => 1, 'category_name' => 'Mobile Phones', 'description' => 'Smartphones of all brands and models.', 'created_at' => '2025-12-10 05:54:35', 'updated_at' => '2025-12-10 05:54:35'],
            ['id' => 2, 'category_name' => 'Laptop', 'description' => 'Laptops, notebooks, and ultrabooks for work and play.', 'created_at' => '2025-12-10 05:54:35', 'updated_at' => '2025-12-10 05:54:35'],
            ['id' => 3, 'category_name' => 'Smart Watch', 'description' => 'Wearable smart devices to track health and notifications.', 'created_at' => '2025-12-10 05:54:35', 'updated_at' => '2025-12-10 05:54:35'],
            ['id' => 4, 'category_name' => 'Earbuds', 'description' => 'Wireless and wired earbuds for music and calls.', 'created_at' => '2025-12-10 05:54:35', 'updated_at' => '2025-12-10 05:54:35'],
        ]);

        /* -----------------------------
         *   SUPPLIERS
         * -----------------------------*/
        DB::table('suppliers')->insert([
            ['id' => 1, 'supplier_name' => 'TechWave Samsung Distribution', 'contact' => 'Jared Leon', 'phone' => '+63 917 452 8891', 'address' => "Unit 4B, Zenith Corporate Center,\r\nJ.P. Rizal Ave, Makati City, Metro Manila", 'created_at' => '2025-12-10 05:55:23', 'updated_at' => '2025-12-10 15:56:54'],
            ['id' => 2, 'supplier_name' => 'PocoGear Wholesale Center', 'contact' => 'Marvin Ortega', 'phone' => '+63 945 882 1194', 'address' => "Rm 12, Horizon Tech Park,\r\nOrtigas Center, Pasig City, Metro Manila", 'created_at' => '2025-12-10 16:15:09', 'updated_at' => '2025-12-10 16:15:09'],
        ]);

        /* -----------------------------
         *   PRODUCTS
         * -----------------------------*/
        DB::table('products')->insert([
            ['id' => 4, 'product_name' => 'Samsung S24 Ultra', 'description' => 'Selpon', 'price' => 60000.00, 'category_id' => 1, 'created_at' => '2025-12-10 06:03:16', 'updated_at' => '2025-12-11 01:56:20'],
            ['id' => 5, 'product_name' => 'Samsung S25 Ultra', 'description' => 'S25 Ultra', 'price' => 75000.00, 'category_id' => 1, 'created_at' => '2025-12-10 07:09:47', 'updated_at' => '2025-12-11 01:56:14'],
            ['id' => 6, 'product_name' => 'Samsung Galaxy Buds3 FE', 'description' => 'Experience comfort grounded in a proven and trusted design...', 'price' => 6690.00, 'category_id' => 4, 'created_at' => '2025-12-10 07:15:17', 'updated_at' => '2025-12-11 01:56:25'],
            ['id' => 7, 'product_name' => 'ASUS TUF A15', 'description' => 'ASUS TUF A15 FA506NCG-HN187WSM Gaming Laptop...', 'price' => 49000.00, 'category_id' => 2, 'created_at' => '2025-12-11 06:57:07', 'updated_at' => '2025-12-11 06:57:07'],
            ['id' => 8, 'product_name' => 'ASUS Vivobook 16', 'description' => 'ASUS Vivobook 16 X1607CA-MB110WSM...', 'price' => 48898.00, 'category_id' => 2, 'created_at' => '2025-12-11 07:06:21', 'updated_at' => '2025-12-11 07:06:21'],
            ['id' => 9, 'product_name' => 'Galaxy Fit3', 'description' => 'Galaxy Fit 3 is a lightweight, stylish fitness tracker...', 'price' => 3290.00, 'category_id' => 3, 'created_at' => '2025-12-11 07:12:35', 'updated_at' => '2025-12-11 07:12:35'],
        ]);

        /* -----------------------------
         *   VARIATIONS
         * -----------------------------*/
        DB::table('variations')->insert([
            ['id' => 1, 'product_id' => 4, 'variation_name' => 'Samsung S24 Ultra 256GB Black', 'price' => 60000, 'stock_quantity' => 61, 'sku' => 'S24U-256-BLK', 'created_at' => '2025-12-10 06:42:56', 'updated_at' => '2025-12-10 16:40:24'],
            ['id' => 2, 'product_id' => 4, 'variation_name' => 'Samsung S24 Ultra 512GB Grey', 'price' => 68000, 'stock_quantity' => 9, 'sku' => 'S24U-512-GRY', 'created_at' => '2025-12-10 06:55:20', 'updated_at' => '2025-12-11 02:02:12'],
            ['id' => 3, 'product_id' => 5, 'variation_name' => 'Titanium Black 256GB | 12gb', 'price' => 75000, 'stock_quantity' => 27, 'sku' => 'S25U-512-BLK', 'created_at' => '2025-12-10 07:09:47', 'updated_at' => '2025-12-11 01:55:29'],
            ['id' => 5, 'product_id' => 6, 'variation_name' => 'Black', 'price' => 6990, 'stock_quantity' => 23, 'sku' => 'GB3FE-BLK', 'created_at' => '2025-12-10 07:24:44', 'updated_at' => '2025-12-11 00:15:56'],
            ['id' => 6, 'product_id' => 6, 'variation_name' => 'Grey', 'price' => 6690, 'stock_quantity' => 2, 'sku' => 'GB3FE-GRY', 'created_at' => '2025-12-10 07:26:56', 'updated_at' => '2025-12-11 02:05:53'],
            ['id' => 8, 'product_id' => 5, 'variation_name' => '256GB Grey', 'price' => 76000, 'stock_quantity' => 8, 'sku' => 'S24U-256-Grey', 'created_at' => '2025-12-10 16:13:48', 'updated_at' => '2025-12-11 03:37:33'],
            ['id' => 14, 'product_id' => 5, 'variation_name' => 'Titanium White Silver | 256gb | 12gb', 'price' => 84990, 'stock_quantity' => 9, 'sku' => 'S25U-512-WHT', 'created_at' => '2025-12-11 01:51:56', 'updated_at' => '2025-12-11 02:05:53'],
            ['id' => 15, 'product_id' => 7, 'variation_name' => 'ASUS TUF A15/R7/8GB/512GB/15.6 Inches FHD/RTX3050', 'price' => 49000, 'stock_quantity' => 9, 'sku' => 'FA506NCG-HN187WSM', 'created_at' => '2025-12-11 06:57:07', 'updated_at' => '2025-12-11 06:59:26'],
            ['id' => 16, 'product_id' => 8, 'variation_name' => 'Intel Core 5/16GB/512GB/16 Inch', 'price' => 48898, 'stock_quantity' => 20, 'sku' => 'X1607CA-MB110WSM', 'created_at' => '2025-12-11 07:06:21', 'updated_at' => '2025-12-11 07:06:21'],
            ['id' => 17, 'product_id' => 9, 'variation_name' => 'Galaxy Fit 3 – Gray', 'price' => 3290, 'stock_quantity' => 20, 'sku' => 'GF3-GRY-004', 'created_at' => '2025-12-11 07:12:35', 'updated_at' => '2025-12-11 07:12:35'],
            ['id' => 18, 'product_id' => 9, 'variation_name' => 'Galaxy Fit 3 – Pink', 'price' => 3290, 'stock_quantity' => 25, 'sku' => 'GF3-PNK-003', 'created_at' => '2025-12-11 07:15:11', 'updated_at' => '2025-12-11 07:15:11'],
        ]);

        /* -----------------------------
         *   PRODUCT IMAGES
         * -----------------------------*/
        DB::table('product_images')->insert([
            ['id' => 1, 'product_id' => 6, 'image_path' => 'product-images/e7QG7oUe6NBi2Bcx1SGvlt47jlTgfDlp3U603ly9.jpg', 'is_primary' => 1, 'created_at' => '2025-12-10 22:55:10', 'updated_at' => '2025-12-11 01:56:25'],
            ['id' => 3, 'product_id' => 5, 'image_path' => 'product-images/AWN6S6y2y8JBWoEk0EYogE8rNjO5mRmhRl1AxRzu.png', 'is_primary' => 1, 'created_at' => '2025-12-11 01:57:58', 'updated_at' => '2025-12-11 01:57:58'],
            ['id' => 4, 'product_id' => 4, 'image_path' => 'product-images/ehv3jTaQCkLnOnX4LdUk4aZaETciwv8ZQ8GC6hFm.png', 'is_primary' => 1, 'created_at' => '2025-12-11 02:01:06', 'updated_at' => '2025-12-11 02:01:06'],
            ['id' => 5, 'product_id' => 7, 'image_path' => 'product-images/VIokQWJjRzwR7TBzRrF06XVEnED6rbknjdJjJyqf.png', 'is_primary' => 1, 'created_at' => '2025-12-11 06:57:07', 'updated_at' => '2025-12-11 06:57:07'],
            ['id' => 6, 'product_id' => 8, 'image_path' => 'product-images/PRYcyphLeKSxHWsQkhVeZQUSgOVJLwoRNfYXV6S3.png', 'is_primary' => 1, 'created_at' => '2025-12-11 07:06:53', 'updated_at' => '2025-12-11 07:06:53'],
            ['id' => 7, 'product_id' => 9, 'image_path' => 'product-images/ZM6WMxCHOx8mcGq5ZZc4r081u2RbMsmxJiSVSPcc.png', 'is_primary' => 1, 'created_at' => '2025-12-11 07:12:36', 'updated_at' => '2025-12-11 07:12:36'],
        ]);

        /* -----------------------------
         *   CUSTOMERS
         * -----------------------------*/
        DB::table('customers')->insert([
            ['id' => 11, 'first_name' => 'Mark Lemuel C. Peria', 'last_name' => '', 'email' => 'lemuelperia33@gmail.com', 'phone' => null, 'address' => null, 'created_at' => '2025-12-10 17:16:30', 'updated_at' => '2025-12-10 17:16:30'],
        ]);

        /* -----------------------------
         *   ORDERS
         * -----------------------------*/
        DB::table('orders')->insert([
            [ 'id'=>6, 'customer_id'=>11, 'order_date'=>'2025-12-11 01:16:30', 'total_amount'=>84100.00, 'status'=>'delivered','payment_method'=>null,'payment_status'=>'pending','shipping_name'=>'Mark Lemuel C. Peria','shipping_email'=>'lemuelperia33@gmail.com','shipping_phone'=>'09608479574','shipping_address'=>'Sitio, Paratong, Bacnotan, La Union','shipping_cost'=>100.00, 'tax'=>9000.00,'created_at'=>'2025-12-10 17:16:30','updated_at'=>'2025-12-10 19:53:45'],
            [ 'id'=>7, 'customer_id'=>11, 'order_date'=>'2025-12-11 02:06:07', 'total_amount'=>84100.00, 'status'=>'delivered','payment_method'=>'digital','payment_status'=>'paid','shipping_name'=>'Mark Lemuel C. Peria','shipping_email'=>'lemuelperia33@gmail.com','shipping_phone'=>'09608479574','shipping_address'=>'Sitio, Paratong, Bacnotan, La Union','shipping_cost'=>100.00, 'tax'=>9000.00,'created_at'=>'2025-12-10 18:06:07','updated_at'=>'2025-12-10 19:31:21'],
            [ 'id'=>8, 'customer_id'=>11, 'order_date'=>'2025-12-11 02:09:17', 'total_amount'=>84100.00, 'status'=>'delivered','payment_method'=>'cod','payment_status'=>'paid','shipping_name'=>'Mark Lemuel C. Peria','shipping_email'=>'lemuelperia33@gmail.com','shipping_phone'=>'09608479574','shipping_address'=>'Sitio, Paratong, Bacnotan, La Union','shipping_cost'=>100.00, 'tax'=>9000.00,'created_at'=>'2025-12-10 18:09:17','updated_at'=>'2025-12-10 19:28:34'],
            [ 'id'=>9, 'customer_id'=>11, 'order_date'=>'2025-12-11 07:34:42', 'total_amount'=>23586.40, 'status'=>'delivered','payment_method'=>'digital','payment_status'=>'paid','shipping_name'=>'Mark Lemuel C. Peria','shipping_email'=>'lemuelperia33@gmail.com','shipping_phone'=>'09608479574','shipping_address'=>'Sitio, Paratong, Bacnotan, La Union','shipping_cost'=>100.00, 'tax'=>2516.40,'created_at'=>'2025-12-10 23:34:42','updated_at'=>'2025-12-10 23:43:46'],
            [ 'id'=>10, 'customer_id'=>11, 'order_date'=>'2025-12-11 08:15:56', 'total_amount'=>7928.80, 'status'=>'delivered','payment_method'=>'cod','payment_status'=>'pending','shipping_name'=>'Mark Lemuel C. Peria','shipping_email'=>'lemuelperia33@gmail.com','shipping_phone'=>'09608479574','shipping_address'=>'Sitio, Paratong, Bacnotan, La Union','shipping_cost'=>100.00, 'tax'=>838.80,'created_at'=>'2025-12-11 00:15:56','updated_at'=>'2025-12-11 01:47:54'],
        ]);

        /* -----------------------------
         *   ORDER PRODUCTS
         * -----------------------------*/
        DB::table('order_product')->insert([
            ['order_id'=>6, 'product_id'=>5, 'variation_id'=>3, 'quantity'=>1, 'price_at_purchase'=>75000],
            ['order_id'=>7, 'product_id'=>5, 'variation_id'=>3, 'quantity'=>1, 'price_at_purchase'=>75000],
            ['order_id'=>8, 'product_id'=>5, 'variation_id'=>3, 'quantity'=>1, 'price_at_purchase'=>75000],
            ['order_id'=>9, 'product_id'=>6, 'variation_id'=>5, 'quantity'=>3, 'price_at_purchase'=>6990],
            ['order_id'=>10, 'product_id'=>6, 'variation_id'=>5, 'quantity'=>1, 'price_at_purchase'=>6990],
        ]);

        /* -----------------------------
         *   STOCKS
         * -----------------------------*/
        DB::table('stocks')->insert([
            ['id'=>1, 'product_id'=>4, 'supplier_id'=>1, 'quantity'=>1, 'last_updated'=>'2025-12-10 06:40:57', 'created_at'=>'2025-12-10 06:03:16','updated_at'=>'2025-12-10 06:40:57'],
        ]);

        /* -----------------------------
         *   RESTOCKS
         * -----------------------------*/
        DB::table('restocks')->insert([
            ['id'=>8, 'product_id'=>4,'variation_id'=>1,'supplier_id'=>1,'quantity'=>null,'quantity_added'=>50,'cost_per_unit'=>46000,'total_cost'=>2300000,'previous_stock'=>14,'new_stock'=>64,'restocked_at'=>'2025-12-10 16:40:24','note'=>null,'created_at'=>'2025-12-10 16:40:24','updated_at'=>'2025-12-10 16:40:24'],
            ['id'=>9, 'product_id'=>6,'variation_id'=>5,'supplier_id'=>1,'quantity'=>null,'quantity_added'=>5,'cost_per_unit'=>4000,'total_cost'=>20000,'previous_stock'=>30,'new_stock'=>35,'restocked_at'=>'2025-12-10 16:44:17','note'=>null,'created_at'=>'2025-12-10 16:44:17','updated_at'=>'2025-12-10 16:44:17'],
            ['id'=>10,'product_id'=>6,'variation_id'=>5,'supplier_id'=>1,'quantity'=>null,'quantity_added'=>6,'cost_per_unit'=>4000,'total_cost'=>24000,'previous_stock'=>35,'new_stock'=>41,'restocked_at'=>'2025-12-10 16:45:02','note'=>'grey ito','created_at'=>'2025-12-10 16:45:02','updated_at'=>'2025-12-10 16:45:02'],
            ['id'=>11,'product_id'=>4,'variation_id'=>2,'supplier_id'=>1,'quantity'=>null,'quantity_added'=>6,'cost_per_unit'=>45000,'total_cost'=>270000,'previous_stock'=>3,'new_stock'=>9,'restocked_at'=>'2025-12-10 16:49:56','note'=>'Silver S24 Ultra','created_at'=>'2025-12-10 16:49:56','updated_at'=>'2025-12-10 16:49:56'],
        ]);

        /* -----------------------------
         *   REVIEWS
         * -----------------------------*/
        DB::table('reviews')->insert([
            ['id'=>1,'product_id'=>6,'customer_id'=>11,'order_id'=>null,'rating'=>5,'comment'=>'Ayuss','is_approved'=>1,'created_at'=>'2025-12-10 19:01:33','updated_at'=>'2025-12-10 19:01:33'],
        ]);

        /* -----------------------------
         *   WISHLISTS
         * -----------------------------*/
        DB::table('wishlists')->insert([
            ['id'=>2,'customer_id'=>11,'product_id'=>5,'variation_id'=>null,'created_at'=>'2025-12-11 06:27:52','updated_at'=>'2025-12-11 06:27:52'],
        ]);

        /* Enable auto-increment to continue normally */
        DB::statement("ALTER TABLE products AUTO_INCREMENT = 10");
        DB::statement("ALTER TABLE categories AUTO_INCREMENT = 5");
        DB::statement("ALTER TABLE variations AUTO_INCREMENT = 19");
        DB::statement("ALTER TABLE suppliers AUTO_INCREMENT = 3");
        DB::statement("ALTER TABLE customers AUTO_INCREMENT = 12");
        DB::statement("ALTER TABLE orders AUTO_INCREMENT = 16");
        DB::statement("ALTER TABLE product_images AUTO_INCREMENT = 8");
        DB::statement("ALTER TABLE restocks AUTO_INCREMENT = 12");
        DB::statement("ALTER TABLE reviews AUTO_INCREMENT = 2");
        DB::statement("ALTER TABLE wishlists AUTO_INCREMENT = 3");
        DB::statement("ALTER TABLE stocks AUTO_INCREMENT = 2");
    }
}
