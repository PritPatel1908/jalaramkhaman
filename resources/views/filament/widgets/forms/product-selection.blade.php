<div x-data="{
    cart: {},
    products: @json(\App\Models\Product::all()->map(fn($p) => [
        'id' => $p->id,
        'name' => $p->name,
        'price' => $p->price,
        'unit' => $p->unit_in,
        'qty' => 0
    ])),
    addToCart(productId) {
        if (!this.cart[productId]) {
            let product = this.products.find(p => p.id === productId);
            this.cart[productId] = { ...product, qty: 1 };
        } else {
            this.cart[productId].qty++;
        }
    },
    removeFromCart(productId) {
        if (this.cart[productId] && this.cart[productId].qty > 1) {
            this.cart[productId].qty--;
        } else {
            delete this.cart[productId];
        }
    }
}">
    <div class="grid grid-cols-3 gap-4">
        <template x-for="product in products" :key="product.id">
            <div class="border p-4 rounded-lg shadow-md bg-gray-800 text-white">
                <p class="text-lg font-bold" x-text="product.name"></p>
                <p>₹<span x-text="product.price"></span> / <span x-text="product.unit"></span></p>

                <div class="flex items-center mt-2">
                    <button @click="removeFromCart(product.id)" class="bg-red-500 px-3 py-1 rounded-lg">-</button>
                    <span class="px-4" x-text="cart[product.id]?.qty || 0"></span>
                    <button @click="addToCart(product.id)" class="bg-green-500 px-3 py-1 rounded-lg">+</button>
                </div>
            </div>
        </template>
    </div>

    <!-- Hidden Input to Submit Data -->
    <input type="hidden" name="cart_data" :value="JSON.stringify(cart)">

    <!-- Cart Summary -->
    <div class="mt-4 p-4 border rounded-lg bg-gray-900 text-white" x-show="Object.keys(cart).length > 0">
        <h2 class="text-xl font-bold">Your Selection</h2>
        <template x-for="item in Object.values(cart)" :key="item.id">
            <p x-text="`${item.name} - ${item.qty} x ₹${item.price}`"></p>
        </template>
    </div>
</div>
