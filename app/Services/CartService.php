<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

/**
 * Guest shopping cart kept in the session.
 *
 * The session only stores product_id => quantity; prices and names are always
 * read live from the products table so the cart never shows a stale price.
 */
class CartService
{
    const KEY = 'cart';

    /** Raw [product_id => quantity] map from the session. */
    public function raw(): array
    {
        return session()->get(self::KEY, []);
    }

    protected function save(array $items): void
    {
        session()->put(self::KEY, $items);
    }

    /**
     * Add a medicine to the cart (or increase its quantity).
     */
    public function add(Product $product, int $quantity = 1): void
    {
        $items = $this->raw();
        $current = $items[$product->id] ?? 0;

        $items[$product->id] = $this->cap($product, $current + max(1, $quantity));

        $this->save($items);
    }

    /**
     * Set an exact quantity; a quantity of zero removes the line.
     */
    public function update(Product $product, int $quantity): void
    {
        if ($quantity < 1) {
            $this->remove($product->id);
            return;
        }

        $items = $this->raw();
        $items[$product->id] = $this->cap($product, $quantity);

        $this->save($items);
    }

    public function remove(int $product_id): void
    {
        $items = $this->raw();
        unset($items[$product_id]);

        $this->save($items);
    }

    public function clear(): void
    {
        session()->forget(self::KEY);
    }

    /**
     * Cart lines hydrated with their product, dropping any medicine that was
     * deleted from the catalogue in the meantime.
     *
     * @return Collection<int, object{product: Product, quantity: int, total: float}>
     */
    public function items(): Collection
    {
        $items = $this->raw();

        if (empty($items)) {
            return collect();
        }

        $products = Product::publiclyVisible()->with(['image', 'pharmacies'])->whereIn('id', array_keys($items))->get()->keyBy('id');

        // Forget lines whose medicine no longer exists
        $missing = array_diff(array_keys($items), $products->keys()->all());
        if ($missing) {
            $this->save(array_diff_key($items, array_flip($missing)));
        }

        return $products->map(function (Product $product) use ($items) {
            $quantity = (int) $items[$product->id];

            return (object) [
                'product'  => $product,
                'quantity' => $quantity,
                'total'    => round($product->price * $quantity, 2),
            ];
        })->values();
    }

    /** Total number of units in the cart (the floating badge). */
    public function count(): int
    {
        return (int) array_sum($this->raw());
    }

    /** Number of distinct medicines in the cart. */
    public function lines(): int
    {
        return count($this->raw());
    }

    public function isEmpty(): bool
    {
        return $this->lines() === 0;
    }

    public function total(): float
    {
        return round($this->items()->sum('total'), 2);
    }

    /**
     * True when every medicine in the cart shows its price publicly; when a
     * hidden-price medicine is in the cart the total is quoted on confirmation.
     */
    public function pricesVisible(): bool
    {
        return $this->items()->every(fn ($item) => (bool) $item->product->show_price);
    }

    /**
     * Cap the requested quantity at the stock on hand for medicines whose
     * stock is tracked publicly; untracked medicines are not limited.
     */
    protected function cap(Product $product, int $quantity): int
    {
        if ($product->show_stock) {
            return min($quantity, $product->available_quantity);
        }

        return $quantity;
    }
}
