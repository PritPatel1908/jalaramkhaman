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
            activeCategory: 'all',
            showSummaryModal: false,
            sortOption: 'name',
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
                // Add animation effect
                this.$refs[`product-${index}`].classList.add('scale-105');
                setTimeout(() => {
                    this.$refs[`product-${index}`].classList.remove('scale-105');
                }, 200);
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
            },

            getCategories() {
                return ['all', ...new Set(this.products.map(p => p.description.split(' ')[0] || 'uncategorized'))];
            },

            getFilteredProducts() {
                let filtered = this.products;

                // Apply search filter
                if (this.searchQuery) {
                    filtered = filtered.filter(p =>
                        p.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                        p.description.toLowerCase().includes(this.searchQuery.toLowerCase())
                    );
                }

                // Apply category filter
                if (this.activeCategory !== 'all') {
                    filtered = filtered.filter(p =>
                        (p.description.split(' ')[0] || 'uncategorized') === this.activeCategory
                    );
                }

                // Apply sorting
                if (this.sortOption === 'name') {
                    filtered.sort((a, b) => a.name.localeCompare(b.name));
                } else if (this.sortOption === 'price-low') {
                    filtered.sort((a, b) => a.price - b.price);
                } else if (this.sortOption === 'price-high') {
                    filtered.sort((a, b) => b.price - a.price);
                }

                return filtered;
            },

            getTotalItems() {
                return this.products.reduce((sum, p) => sum + p.qty, 0);
            },

            getTotalPrice() {
                return this.products.reduce((sum, p) => sum + (p.price * p.qty), 0).toFixed(2);
            },

            getUnitLabel(unitValue) {
                return this.unitOptions[unitValue] || '';
            },

            clearAll() {
                this.products = this.products.map(product => ({
                    ...product,
                    qty: 0
                }));
                this.updateState();
            }
        }"
        class="space-y-4"
        x-cloak
    >
        <!-- Sticky Header with Search and Filters -->
        <div class="sticky top-0 z-20 bg-white p-4 shadow-md rounded-lg mb-6">
            <div class="flex flex-col space-y-4">
                <!-- Search Bar with Icon -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input
                        type="text"
                        x-model="searchQuery"
                        placeholder="Search products by name or description..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 transition-all"
                    >
                </div>

                <!-- Filters and Sort -->
                <div class="flex flex-col sm:flex-row justify-between space-y-2 sm:space-y-0 sm:space-x-4">
                    <!-- Categories -->
                    <div class="flex overflow-x-auto pb-2 scrollbar-hide">
                        <template x-for="category in getCategories()" :key="category">
                            <button
                                type="button"
                                @click.prevent="activeCategory = category"
                                class="px-4 py-2 whitespace-nowrap rounded-full mr-2 text-sm font-medium transition-all"
                                :class="activeCategory === category ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                x-text="category.charAt(0).toUpperCase() + category.slice(1)"
                            ></button>
                        </template>
                    </div>

                    <!-- Sort Options -->
                    <select
                        x-model="sortOption"
                        class="rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500"
                    >
                        <option value="name">Sort by Name</option>
                        <option value="price-low">Price: Low to High</option>
                        <option value="price-high">Price: High to Low</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Product Grid with Animation -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            <template x-for="(product, index) in getFilteredProducts()" :key="index">
                <div
                    x-ref="product-${index}"
                    class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-all duration-300"
                    :class="{'border-primary-500 shadow-md': product.qty > 0}"
                >
                    <!-- Product Image with Hover Zoom -->
                    <div class="relative h-48 bg-gray-100 overflow-hidden group">
                        <img
                            :src="product.image_url"
                            :alt="product.name"
                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                        >
                        <div class="absolute bottom-0 right-0 bg-white px-3 py-1 m-2 rounded-lg shadow-sm text-sm font-medium">
                            ₹<span x-text="product.price"></span>/<span x-text="product.per"></span> <span x-text="product.unit_label"></span>
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-900 line-clamp-1" x-text="product.name"></h3>
                        <p class="text-sm text-gray-500 mt-1 line-clamp-2" x-text="product.description || 'No description available'"></p>

                        <!-- Quantity and Unit Controls -->
                        <div class="mt-4">
                            <!-- Quantity Controls -->
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center space-x-3">
                                    <button
                                        type="button"
                                        @click.prevent="decreaseQty(index)"
                                        class="w-9 h-9 flex items-center justify-center rounded-full border border-gray-300 hover:bg-gray-100 text-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500"
                                        :class="{ 'opacity-50 cursor-not-allowed': product.qty === 0 }"
                                    >
                                        <span class="text-xl font-bold">-</span>
                                    </button>

                                    <span
                                        x-text="product.qty"
                                        class="w-6 text-center font-medium text-lg"
                                    ></span>

                                    <button
                                        type="button"
                                        @click.prevent="increaseQty(index)"
                                        class="w-9 h-9 flex items-center justify-center rounded-full bg-primary-600 hover:bg-primary-700 text-white transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500"
                                    >
                                        <span class="text-xl font-bold">+</span>
                                    </button>
                                </div>

                                <div x-show="product.qty > 0" class="text-sm font-medium">
                                    Total: ₹<span x-text="(product.price * product.qty).toFixed(2)"></span>
                                </div>
                            </div>

                            <!-- Unit Selection (only visible when product is selected) -->
                            <div x-show="product.qty > 0" class="mt-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Select Unit:</label>
                                <select
                                    :id="'unit-select-' + index"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50"
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
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- No Results with Better Styling -->
        <div
            x-show="getFilteredProducts().length === 0"
            x-transition
            class="py-16 text-center bg-gray-50 rounded-xl border border-dashed border-gray-300"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="text-lg font-medium text-gray-700 mb-1">No products found</h3>
            <p class="text-gray-500">Try adjusting your search or filter criteria</p>
            <button
                type="button"
                @click.prevent="searchQuery = ''; activeCategory = 'all'; sortOption = 'name'"
                class="mt-4 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors"
            >
                Reset Filters
            </button>
        </div>

        <!-- Floating Cart Button -->
        <div
            x-show="products.some(p => p.qty > 0)"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-10"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            class="fixed bottom-6 right-6 z-30"
        >
            <button
                type="button"
                @click.prevent="showSummaryModal = true"
                class="bg-primary-600 text-white rounded-full p-4 shadow-lg hover:bg-primary-700 transition-all hover:scale-105 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
            >
                <div class="relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span
                        class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center"
                        x-text="getTotalItems()"
                    ></span>
                </div>
            </button>
        </div>

        <!-- Cart Summary Modal -->
        <div
            x-show="showSummaryModal"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto"
            style="display: none;"
        >
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div
                    x-show="showSummaryModal"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    @click.prevent="showSummaryModal = false"
                ></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div
                    x-show="showSummaryModal"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                    @click.away="showSummaryModal = false"
                >
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                    Selected Products
                                </h3>
                                <div class="mt-2 max-h-60 overflow-y-auto">
                                    <ul class="divide-y divide-gray-200">
                                        <template x-for="(product, index) in products.filter(p => p.qty > 0)" :key="index">
                                            <li class="py-3 flex justify-between items-center">
                                                <div class="flex items-center">
                                                    <img :src="product.image_url" :alt="product.name" class="h-10 w-10 rounded-md object-cover mr-3">
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900" x-text="product.name"></p>
                                                        <p class="text-xs text-gray-500">
                                                            ₹<span x-text="product.price"></span> × <span x-text="product.qty"></span>
                                                        </p>
                                                    </div>
                                                </div>
                                                <p class="text-sm font-medium text-gray-900">
                                                    ₹<span x-text="(product.price * product.qty).toFixed(2)"></span>
                                                </p>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <div class="flex justify-between items-center font-medium">
                                        <span>Total Items:</span>
                                        <span x-text="getTotalItems()"></span>
                                    </div>
                                    <div class="flex justify-between items-center font-medium text-lg mt-2">
                                        <span>Total Amount:</span>
                                        <span class="text-primary-600">₹<span x-text="getTotalPrice()"></span></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button
                            type="button"
                            @click.prevent="showSummaryModal = false"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            Continue Shopping
                        </button>
                        <button
                            type="button"
                            @click.prevent="clearAll()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            Clear All
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>