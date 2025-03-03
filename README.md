# 📦 Foodics Management System

A simple management system built with Laravel to handle product orders, update stock levels, and trigger low-stock notifications.

---

## 🚀 Features

- **Order Management:** Create orders and automatically adjust ingredient stock.
- **Stock Tracking:** Monitor stock levels and update quantities based on product requirements.
- **Low-Stock Alerts:** Trigger notifications when ingredient stock falls below a defined threshold.
- **Unit Conversion:** Supports different units (e.g., grams, kilograms) for ingredient quantities.
- **Testing:** Comprehensive tests for order creation, stock updates, and notification triggering.

---

## 🏗️ Installation

1. **Clone the repository:**

   ```bash
   git clone https://github.com/DaliaYounis/foodics-management-system.git
   cd foodics-management-system
   ```

2. **Install dependencies:**

   ```bash
   composer install
   ```

3. **Set up the environment:**

   `.env` file and configure your database:


4. **Generate the application key:**

   ```bash
   php artisan key:generate
   ```

5. **Run migrations:**

   ```bash
   php artisan migrate
   ```

6. **Seed the database:**

   ```bash
   php artisan db:seed
   ```

7. **Start the local server:**

   ```bash
   php artisan serve
   ```


---

## ✅ Testing

Run the test suite using PHPUnit:

```bash
php artisan test
```

### Example Tests

- **Order Creation & Stock Update**
- **Low-Stock Notification**

```php
public function test_notification_triggered_when_stock_below_threshold()
{
    Queue::fake();

    $ingredient = Ingredient::factory()->create(['alert_threshold_percentage' => 50]);
    StockIngredient::create(['ingredient_id' => $ingredient->id, 'quantity' => 0.4, 'unit_id' => 2, 'merchant_id' => 1]);

    $stockService = new StockService();
    $stockService->checkStockLevels(collect([$ingredient]));

    Queue::assertPushed(SendLowStockNotification::class);
}
```






