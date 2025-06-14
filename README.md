# Laravel Custom Hook System

This documentation explains how to build and use a flexible and performant hook system in Laravel 12. It supports both **actions** and **filters**, similar to WordPress, and works across controllers, Livewire components, middleware, and modules.

---

## 🛠 Installation

To install the `creativesofttechsolutions/laravelhooks` package, use Composer:

```bash
composer require creativesofttechsolutions/laravelhooks
```

## 🧠 What Are Actions and Filters?

### 🔹 Actions
- Actions are event triggers.
- They allow you to **run additional code** when something happens (e.g., after user registration, after an order is placed).
- Actions do not modify any data — they just perform side effects.

```php
hooks()->addAction('after_user_register', function ($user) {
    Log::info("New user: " . $user->email);
});
```

### 🔸 Filters
- Filters are used to **modify and return data**.
- They allow other parts of the application or plugins to intercept, change, or validate values before they are used.

```php
hooks()->addFilter('post_title', function ($title) {
    return strtoupper($title);
});
```

## 🚀 Usage Examples

### Triggering an action:

```php
hooks()->doAction('after_user_register', $user);
```

### Adding an action (e.g., in a module):

```php
hooks()->addAction('after_user_register', function ($user) {
    Log::info("User Registered: " . $user->email);
});
```

### Adding and using filters:

```php
hooks()->addFilter('product_price', function ($price, $product) {
    return $product->discount ? $price * 0.9 : $price;
});

$price = hooks()->applyFilters('product_price', $originalPrice, $product);
```

---

## 📦 Where to Define Hooks in Modules

In `Modules/YourModule/Providers/ModuleServiceProvider.php`:

```php
public function boot(): void {
    hooks()->addAction('after_user_register', ...);
    hooks()->addFilter('modify_data', ...);
}
```

Or in a dedicated class like:

```php
Modules/YourModule/Hooks/HookRegistrar.php
```

And load them like:

```php
HookRegistrar::register();
```

---

## ⚙️ Using Hooks in Controllers, Livewire, Middleware

### In Controllers:

```php
hooks()->doAction('after_order_created', $order);
```

### In Livewire Components:

```php
$this->value = hooks()->applyFilters('modify_value', $this->value);
```

### In Middleware:

```php
$request = hooks()->applyFilters('modify_request', $request);
```

---

## ✅ Best Practices

- Use meaningful, unique hook names.
- Actions for side effects, Filters for modifying data.
- Use classes instead of closures for better readability and testing.
- Avoid heavy operations inside hook callbacks.

---

This system brings WordPress-style flexibility to Laravel while preserving Laravel’s clean architecture. You can dynamically extend your app and let plugins interact with core logic cleanly.
