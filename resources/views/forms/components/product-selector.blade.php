<x-dynamic-component
    :component="$getFieldWrapperView()"
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    @php
        // Get all available units from UnitIn enum
        $unitOptions = \App\Enums\UnitIn::cases();
        $unitArray = [];
        foreach ($unitOptions as $unit) {
            $unitArray[$unit->value] = $unit->getLabel();
        }
    @endphp

    <div
        x-data="{
            state: $wire.entangle('{{ $getStatePath() }}'),
            products: {{ json_encode($getProducts()) }},
            searchQuery: '',
            unitOptions: {{ json_encode($unitArray) }},

            init() {
                this.products = this.products.map(product => ({
                    ...product,
                    qty: 0,
                    selectedUnit: product.unit_in // Default to product's unit
                }));

                // Initialize quantities from state if available
                if (this.state && Array.isArray(this.state)) {
                    this.state.forEach(item => {
                        const productIndex = this.products.findIndex(p => p.id === item.product_id);
                        if (productIndex !== -1) {
                            this.products[productIndex].qty = item.qty;
                            this.products[productIndex].selectedUnit = item.unit_in;
                        }
                    });
                }
            },

            increaseQty(index) {
                this.products[index].qty++;
                this.updateState();
            },

            decreaseQty(index) {
                if (this.products[index].qty > 0) {
                    this.products[index].qty--;
                    this.updateState();
                }
            },

            setUnit(index, unitValue) {
                this.products[index].selectedUnit = parseInt(unitValue);
                this.updateState();
            },

            updateState() {
                const selectedProducts = this.products
                    .filter(p => p.qty > 0)
                    .map(p => ({
                        product_id: p.id,
                        qty: p.qty,
                        unit_in: p.selectedUnit
                    }));

                this.state = selectedProducts;
            }
        }"
        class="space-y-6"
    >
        <!-- Search Bar -->
        <div class="mb-4">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input
                    type="text"
                    x-model="searchQuery"
                    placeholder="Search products..."
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"
                >
            </div>
        </div>

        <!-- Product Grid - Now with 2 columns -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <template x-for="(product, index) in products.filter(p => p.name.toLowerCase().includes(searchQuery.toLowerCase()))" :key="index">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:border-primary-500 transition-all">
                    <div class="flex flex-row h-32">
                        <!-- Left side - Product Image -->
                        <div class="w-1/3 bg-gray-100">
                            <img :src="product.image_url" :alt="product.name" class="w-full h-full object-cover">
                        </div>

                        <!-- Right side - Product Info -->
                        <div class="w-2/3 p-3 flex flex-col justify-between">
                            <div>
                                <div class="flex justify-between items-start">
                                    <h3 class="text-base font-semibold text-gray-900 line-clamp-1" x-text="product.name"></h3>
                                    <div class="bg-primary-50 text-primary-700 text-xs font-medium px-2 py-1 rounded">
                                        ₹<span x-text="product.price"></span>/<span x-text="product.per"></span> <span x-text="product.unit_label"></span>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1 line-clamp-2" x-text="product.description || 'No description available'"></p>
                            </div>

                            <div class="flex items-center justify-between mt-2">
                                <!-- Quantity Controls -->
                                <div class="flex items-center space-x-2">
                                    <button
                                        type="button"
                                        @click="decreaseQty(index)"
                                        class="w-7 h-7 flex items-center justify-center rounded-full border border-gray-300 hover:bg-gray-100 text-gray-700 transition-colors"
                                        :class="{ 'opacity-50 cursor-not-allowed': product.qty === 0 }"
                                    >
                                        <span class="text-sm font-bold">-</span>
                                    </button>

                                    <span
                                        x-text="product.qty"
                                        class="w-5 text-center font-medium text-sm"
                                    ></span>

                                    <button
                                        type="button"
                                        @click="increaseQty(index)"
                                        class="w-7 h-7 flex items-center justify-center rounded-full bg-primary-600 hover:bg-primary-700 text-white transition-colors"
                                    >
                                        <span class="text-sm font-bold">+</span>
                                    </button>
                                </div>

                                <!-- Unit Selection Dropdown -->
                                <div x-show="product.qty > 0" class="ml-3">
                                    <select
                                        :id="'unit-select-' + index"
                                        class="text-xs border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50"
                                        @change="setUnit(index, $event.target.value)"
                                    >
                                        <template x-for="(label, value) in unitOptions" :key="value">
                                            <option
                                                :value="value"
                                                x-text="label"
                                                :selected="parseInt(value) === product.selectedUnit"
                                            ></option>
                                        </template>
                                    </select>
                                </div>

                                <!-- Total Price -->
                                <div x-show="product.qty > 0" class="text-xs font-medium text-primary-700">
                                    ₹<span x-text="(product.price * product.qty).toFixed(2)"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- No Results -->
        <div
            x-show="products.filter(p => p.name.toLowerCase().includes(searchQuery.toLowerCase())).length === 0"
            class="py-12 text-center text-gray-500"
        >
            <svg class="mx-auto h-10 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No products found</h3>
            <p class="mt-1 text-sm text-gray-500">Try adjusting your search to find what you're looking for.</p>
        </div>

        <!-- Selected Products Summary -->
        {{-- <div
            x-show="products.some(p => p.qty > 0)"
            class="fixed bottom-0 left-0 right-0 bg-white shadow-lg border-t border-gray-200 p-4 z-10"
        >
            <div class="container mx-auto flex items-center justify-between">
                <div class="flex items-center">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-primary-100 text-primary-800 mr-2">
                        <span x-text="products.filter(p => p.qty > 0).length"></span>
                    </span>
                    <span class="font-medium">Selected Products</span>
                </div>

                <div class="flex items-center">
                    <span class="text-gray-600 mr-2">Total:</span>
                    <span class="font-medium text-lg text-primary-700">₹<span x-text="products.reduce((sum, p) => sum + (p.price * p.qty), 0).toFixed(2)"></span></span>
                </div>
            </div>
        </div> --}}
    </div>
</x-dynamic-component>