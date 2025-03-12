<div class="product-selector-container">
    {{-- <div class="bg-yellow-100 p-4 mb-4 rounded-lg text-xs" x-data="{show: true}" x-show="show">
        <div class="flex justify-between">
            <span class="font-bold">Debug Info</span>
            <button @click="show = false" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        <div>State Path: {{ $getStatePath() }}</div>
        <div>Current State:
            <pre>@json($getState() ?? [])</pre>
        </div>
    </div> --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @php
            $products = \App\Models\Product::query()
                ->when(auth()->user()->user_type == 'business', function($query) {
                    return $query->with('business_type_product_price');
                })
                ->when(auth()->user()->user_type == 'customer', function($query) {
                    return $query->with('customer_type_product_price');
                })
                ->get();
        @endphp

        @foreach($products as $product)
            @php
                $price = auth()->user()->user_type == 'business'
                    ? $product->business_type_product_price->first()->price ?? 0
                    : $product->customer_type_product_price->first()->price ?? 0;

                $per = auth()->user()->user_type == 'business'
                    ? $product->business_type_product_price->first()->per ?? ''
                    : $product->customer_type_product_price->first()->per ?? '';

                $unitIn = auth()->user()->user_type == 'business'
                    ? \App\Enums\UnitIn::from($product->business_type_product_price->first()->unit_in ?? 0)->getLabel()
                    : \App\Enums\UnitIn::from($product->customer_type_product_price->first()->unit_in ?? 0)->getLabel();
            @endphp

            <div class="product-card bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-lg p-5 border border-gray-100 relative overflow-hidden"
                 x-data="{
                    quantity: 0,
                    unitIn: 1,
                    showDetails: false,
                    addProduct() {
                        if (this.quantity === 0) {
                            this.quantity = 1;
                            this.addToOrder();
                        } else {
                            this.quantity++;
                            this.updateOrder();
                        }
                    },
                    removeProduct() {
                        if (this.quantity > 0) {
                            this.quantity--;
                            if (this.quantity === 0) {
                                this.removeFromOrder();
                            } else {
                                this.updateOrder();
                            }
                        }
                    },
                    addToOrder() {
                        $dispatch('add-product', {
                            productId: {{ $product->id }},
                            quantity: this.quantity,
                            unitIn: this.unitIn
                        });
                    },
                    updateOrder() {
                        $dispatch('update-product', {
                            productId: {{ $product->id }},
                            quantity: this.quantity,
                            unitIn: this.unitIn
                        });
                    },
                    removeFromOrder() {
                        $dispatch('remove-product', {
                            productId: {{ $product->id }}
                        });
                    },
                    init() {
                        // Check if product is already in order
                        const existingDetail = @json($getState() ?? []);
                        const detail = existingDetail.find(item => item.product_id == {{ $product->id }});
                        if (detail) {
                            this.quantity = detail.qty;
                            this.unitIn = detail.unit_in;
                        }
                    }
                 }"
                 :class="{'border-primary-200 ring-2 ring-primary-100': quantity > 0}">

                <!-- Decorative accent -->
                <div class="absolute top-0 right-0 w-16 h-16 -mr-8 -mt-8 bg-gradient-to-br from-primary-100 to-primary-200 rounded-full opacity-70"></div>

                <div class="flex justify-between items-start relative">
                    <div class="flex-1 pr-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-1">{{ $product->name }}</h3>
                        <p class="text-sm font-medium text-primary-600">₹{{ $price }}/{{ $per }} {{ $unitIn }}</p>

                        <p class="text-xs text-gray-500 mt-2 line-clamp-2 hover:line-clamp-none cursor-pointer transition-all duration-200"
                           x-show="showDetails || quantity > 0"
                           x-transition:enter="transition ease-out duration-200"
                           x-transition:enter-start="opacity-0 transform -translate-y-2"
                           x-transition:enter-end="opacity-100 transform translate-y-0">
                            {{ $product->description ?? 'No description available' }}
                        </p>

                        <button type="button"
                                @click="showDetails = !showDetails"
                                class="text-xs text-primary-500 hover:text-primary-700 mt-1 focus:outline-none"
                                x-show="!showDetails && quantity === 0">
                            View details
                        </button>
                    </div>

                    <div class="product-image w-20 h-20 bg-white rounded-lg shadow-md flex items-center justify-center overflow-hidden border border-gray-100">
                        @if($product->image)
                            <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="max-w-full max-h-full object-contain">
                        @else
                            <div class="bg-gradient-to-br from-primary-50 to-primary-100 w-full h-full flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="quantity-selector mt-5 flex items-center" x-cloak>
                    <template x-if="quantity === 0">
                        <button type="button"
                                @click="addProduct()"
                                class="w-full bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 transform hover:scale-[1.02] hover:shadow-md flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add to Order
                        </button>
                    </template>

                    <template x-if="quantity > 0">
                        <div class="w-full">
                            <div class="flex items-center justify-between mb-2">
                                <select x-model="unitIn"
                                        @change="updateOrder()"
                                        class="text-sm border-gray-300 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500 bg-white">
                                    @foreach(\App\Enums\UnitIn::cases() as $unit)
                                        <option value="{{ $unit->value }}">{{ $unit->getLabel() }}</option>
                                    @endforeach
                                </select>

                                <div class="flex items-center bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                                    <button type="button"
                                            @click="removeProduct()"
                                            class="px-3 py-1.5 text-gray-700 hover:bg-gray-100 transition-colors focus:outline-none">
                                        <svg x-show="quantity > 1" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                        </svg>
                                        <svg x-show="quantity === 1" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                    <span class="px-3 py-1.5 text-gray-800 font-medium min-w-[2rem] text-center" x-text="quantity"></span>
                                    <button type="button"
                                            @click="addProduct()"
                                            class="px-3 py-1.5 text-gray-700 hover:bg-gray-100 transition-colors focus:outline-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="text-right text-xs text-primary-600 font-medium">
                                Total: ₹<span x-text="({{ $price }} * quantity).toFixed(2)"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Order Summary Section -->
    <div class="order-summary bg-white rounded-xl shadow-lg p-5 border border-gray-100 mb-6" x-data="{
                details: @json($getState() ?? []),
                get totalItems() {
                    return this.details.reduce((sum, item) => sum + parseFloat(item.qty), 0);
                },
                get totalProducts() {
                    return this.details.length;
                },
                init() {
                    this.$watch('details', value => {
                        $wire.set('{{ $getStatePath() }}', value);

                        // Also update the hidden input as a fallback
                        document.getElementById('product_selector_data').value = JSON.stringify(value);
                    });

                    // ... rest of your init function ...
                }
            }" x-show="totalProducts > 0" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-4"
        x-transition:enter-end="opacity-100 transform translate-y-0" x-cloak>

        <!-- ... existing code ... -->
    </div>

    <!-- Hidden input to ensure data is submitted with the form -->
    <input type="hidden" id="product_selector_data" name="data[product_selector]" x-data="{
            init() {
                this.value = JSON.stringify(@json($getState() ?? []));
            }
        }">

    <!-- Empty State -->
    {{-- <div class="empty-state bg-gray-50 rounded-xl p-8 text-center"
         x-data="{
            get isEmpty() {
                return (@json($getState() ?? []).length === 0);
            }
         }"
         x-show="isEmpty"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-700 mb-1">Your order is empty</h3>
        <p class="text-sm text-gray-500 mb-4">Add products to create your recurring order</p>
    </div> --}}
</div>

<style>
    [x-cloak] { display: none !important; }

    .product-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .hover\:line-clamp-none:hover {
        -webkit-line-clamp: unset;
    }
</style>