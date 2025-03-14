<x-dynamic-component :component="$getFieldWrapperView()" :id="$getId()" :label="$getLabel()" :label-sr-only="$isLabelHidden()" :helper-text="$getHelperText()"
    :hint="$getHint()" :hint-icon="$getHintIcon()" :required="$isRequired()" :state-path="$getStatePath()">
    @php
        // Get all available units from UnitIn enum
        $unitOptions = \App\Enums\UnitIn::cases();
        $unitArray = [];
        foreach ($unitOptions as $unit) {
            $unitArray[$unit->value] = $unit->getLabel();
        }
    @endphp

    <div x-data="{
        state: $wire.entangle('{{ $getStatePath() }}'),
        products: {{ json_encode($getProducts()) }},
        searchQuery: '',
        unitOptions: {{ json_encode($unitArray) }},
        showFilters: false,
        selectedCategory: 'all',
        priceRange: { min: 0, max: 5000 },

        // Get all unique categories from products
        getCategories() {
            const categories = ['all'];
            this.products.forEach(product => {
                if (product.category && !categories.includes(product.category)) {
                    categories.push(product.category);
                }
            });
            return categories;
        },

        // Filter products based on search and price
        filteredProducts() {
            return this.products.filter(product => {
                // Search filter
                const matchesSearch = product.name.toLowerCase().includes(this.searchQuery.toLowerCase());

                // Price filter
                const price = parseFloat(product.price);
                const matchesPrice = price >= this.priceRange.min && price <= this.priceRange.max;

                return matchesSearch && matchesPrice;
            });
        },

        // Helper function to calculate price for a specific product
        calculateItemPrice(product) {
            if (!product || product.qty <= 0) return '0.00';

            const basePrice = parseFloat(product.price);
            const qty = parseFloat(product.qty);
            const baseUnit = parseInt(product.baseUnit);
            const selectedUnit = parseInt(product.selectedUnit);
            const per = parseFloat(product.per);

            let finalPrice = 0;

            // Same unit - simple calculation
            if (baseUnit === selectedUnit) {
                // If product is 100 rs per 100 gram and we select 10 gram
                // Price per 1 gram = 100 / 100 = 1 rs
                // Final price = 1 rs * 10 gram = 10 rs
                const pricePerUnit = basePrice / per;
                finalPrice = pricePerUnit * qty;
            }
            // Gram to Kg conversion
            else if (baseUnit === 1 && selectedUnit === 2) {
                // If product is 100 rs per 100 gram and we select 1 kg
                // Price per 1 gram = 100 / 100 = 1 rs
                // Price per 1 kg = 1 * 1000 = 1000 rs
                // Final price = 1000 * qty
                const pricePerGram = basePrice / per;
                finalPrice = pricePerGram * 1000 * qty;
            }
            // Kg to Gram conversion
            else if (baseUnit === 2 && selectedUnit === 1) {
                // If product is 1000 rs per 1 kg and we select 100 gram
                // Price per 1 gram = 1000 / 1000 = 1 rs
                // Final price = 1 * 100 * qty
                const pricePerGram = basePrice / 1000;
                finalPrice = pricePerGram * per * qty;
            }
            // Ml to Ltr conversion
            else if (baseUnit === 3 && selectedUnit === 4) {
                // Similar to gram to kg
                const pricePerMl = basePrice / per;
                finalPrice = pricePerMl * 1000 * qty;
            }
            // Ltr to Ml conversion
            else if (baseUnit === 4 && selectedUnit === 3) {
                // Similar to kg to gram
                const pricePerMl = basePrice / 1000;
                finalPrice = pricePerMl * per * qty;
            }
            // Default case - for 'no' unit or any other unit
            else {
                finalPrice = basePrice * qty;
            }

            return finalPrice.toFixed(2);
        },

        init() {
            // First, check if we have state data (for edit page)
            const hasStateData = this.state && Array.isArray(this.state) && this.state.length > 0;

            // Create a map of state items for quick lookup
            const stateMap = {};
            if (hasStateData) {
                this.state.forEach(item => {
                    stateMap[item.product_id] = item;
                });
            }

            // Initialize products with default values or state values
            this.products = this.products.map(product => {
                // Check if this product exists in state
                const stateItem = stateMap[product.id];

                // Create the product object with appropriate values
                const initializedProduct = {
                    ...product,
                    qty: stateItem ? stateItem.qty : 0,
                    selectedUnit: stateItem ? stateItem.unit_in : product.unit_in,
                    baseUnit: product.unit_in,
                    finalPrice: '0.00'
                };

                // Calculate the final price if product has quantity
                if (initializedProduct.qty > 0) {
                    initializedProduct.finalPrice = this.calculateItemPrice(initializedProduct);
                }

                return initializedProduct;
            });

            // Set initial price range based on products
            if (this.products.length > 0) {
                const prices = this.products.map(p => parseFloat(p.price));
                this.priceRange.min = Math.min(...prices);
                this.priceRange.max = Math.max(...prices);
            }
        },

        increaseQty(index) {
            this.products[index].qty++;
            this.updateProductPrice(index);
            this.updateState();
        },

        decreaseQty(index) {
            if (this.products[index].qty > 0) {
                this.products[index].qty--;
                this.updateProductPrice(index);
                this.updateState();
            }
        },

        setUnit(index, unitValue) {
            this.products[index].selectedUnit = parseInt(unitValue);
            this.updateProductPrice(index);
            this.updateState();
        },

        // Update the price for a specific product
        updateProductPrice(index) {
            const product = this.products[index];
            if (product && product.qty > 0) {
                product.finalPrice = this.calculateItemPrice(product);
            } else if (product) {
                product.finalPrice = '0.00';
            }
        },

        // This function is used in the template
        calculateFinalPrice(product) {
            // Return the pre-calculated price
            return product.finalPrice || '0.00';
        },

        getAvailableUnits(product) {
            // Return only relevant units based on product's base unit
            const baseUnit = parseInt(product.baseUnit);

            if (baseUnit === 1 || baseUnit === 2) {
                return [1, 2]; // gram and kg
            }
            else if (baseUnit === 3 || baseUnit === 4) {
                return [3, 4]; // ml and ltr
            }
            else {
                return [baseUnit]; // just the base unit (like 'no')
            }
        },

        truncateDescription(description, length = 50) {
            if (!description) return 'No description available';
            return description.length > length ? description.substring(0, length) + '...' : description;
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
    }" class="space-y-4">
        <!-- Search Bar and Filters in one line -->
        <div class="mb-4">
            <div class="flex flex-col md:flex-row gap-2 items-center">
                <!-- Search Bar -->
                <div class="relative flex-grow">
                    {{-- <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div> --}}
                    <input type="text" x-model="searchQuery" placeholder="Search products..."
                        class="w-full py-2 pr-4 border border-gray-300 dark:border-gray-600 rounded-lg pl-10 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                </div>

                <!-- Filter Toggle Button with Badge -->
                <button type="button" @click="showFilters = !showFilters"
                    class="relative px-4 py-2 bg-primary-600 hover:bg-primary-500 text-white rounded-lg flex items-center gap-2 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                    <span>Price Range</span>
                    <!-- Active Filter Badge -->
                    <span x-show="priceRange.min > 0 || priceRange.max < 5000"
                          class="absolute -top-2 -right-2 flex items-center justify-center w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </span>
                </button>
            </div>

            <!-- Enhanced Filter Panel with Animation -->
            <div x-show="showFilters"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform -translate-y-4"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform -translate-y-4"
                 class="mt-3 p-5 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-lg">

                <!-- Price Range Filter with Visual Indicator -->
                <div>
                    <div class="flex justify-between items-center mb-3">
                        <label class="text-base font-medium text-gray-800 dark:text-gray-200">Price Range</label>
                        <div class="px-3 py-1 bg-primary-100 dark:bg-primary-900 text-primary-800 dark:text-primary-300 rounded-full text-sm font-semibold">
                            ₹<span x-text="priceRange.min"></span> - ₹<span x-text="priceRange.max"></span>
                        </div>
                    </div>

                    <!-- Visual Price Range with Colored Track -->
                    <div class="relative pt-6 pb-6 px-2">
                        <!-- Background Track -->
                        <div class="absolute h-2 w-full bg-gray-200 dark:bg-gray-700 rounded-full"></div>

                        <!-- Active Track (colored portion between handles) -->
                        <div class="absolute h-2 bg-primary-500 dark:bg-primary-400 rounded-full"
                             :style="'left: ' + (priceRange.min / 50) + '%; right: ' + (100 - priceRange.max / 50) + '%'"></div>

                        <!-- Min Handle -->
                        <div class="absolute w-6 h-6 bg-white dark:bg-gray-200 border-2 border-primary-500 rounded-full shadow transform -translate-x-1/2 -translate-y-1/2 cursor-pointer flex items-center justify-center"
                             :style="'left: ' + (priceRange.min / 50) + '%'; 'top: 50%'">
                            <span class="text-xs font-bold text-primary-700">₹</span>
                        </div>

                        <!-- Max Handle -->
                        <div class="absolute w-6 h-6 bg-white dark:bg-gray-200 border-2 border-primary-500 rounded-full shadow transform -translate-x-1/2 -translate-y-1/2 cursor-pointer flex items-center justify-center"
                             :style="'left: ' + (priceRange.max / 50) + '%'; 'top: 50%'">
                            <span class="text-xs font-bold text-primary-700">₹</span>
                        </div>

                        <!-- Min Slider (invisible but functional) -->
                        <input type="range"
                            x-model.number="priceRange.min"
                            :min="0"
                            :max="5000"
                            step="50"
                            class="absolute w-full appearance-none bg-transparent pointer-events-auto cursor-pointer opacity-0"
                            style="height: 2rem; top: 0; z-index: 20;"
                            @input="if (priceRange.min > priceRange.max - 100) priceRange.min = priceRange.max - 100">

                        <!-- Max Slider (invisible but functional) -->
                        <input type="range"
                            x-model.number="priceRange.max"
                            :min="0"
                            :max="5000"
                            step="50"
                            class="absolute w-full appearance-none bg-transparent pointer-events-auto cursor-pointer opacity-0"
                            style="height: 2rem; top: 0; z-index: 20;"
                            @input="if (priceRange.max < priceRange.min + 100) priceRange.max = priceRange.min + 100">
                    </div>

                    <!-- Price Markers -->
                    <div class="flex justify-between mt-1 mb-4 px-2 text-xs text-gray-500 dark:text-gray-400">
                        <span>₹0</span>
                        <span>₹1000</span>
                        <span>₹2000</span>
                        <span>₹3000</span>
                        <span>₹4000</span>
                        <span>₹5000</span>
                    </div>

                    <!-- Quick Price Range Buttons -->
                    <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-2">
                        <button type="button" @click="priceRange.min = 0; priceRange.max = 500"
                            class="px-3 py-2 text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors flex items-center justify-center gap-1">
                            <span class="text-green-500">₹</span> Under 500
                        </button>
                        <button type="button" @click="priceRange.min = 500; priceRange.max = 1000"
                            class="px-3 py-2 text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors flex items-center justify-center gap-1">
                            <span class="text-blue-500">₹</span> 500 - 1000
                        </button>
                        <button type="button" @click="priceRange.min = 1000; priceRange.max = 2500"
                            class="px-3 py-2 text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors flex items-center justify-center gap-1">
                            <span class="text-yellow-500">₹</span> 1000 - 2500
                        </button>
                        <button type="button" @click="priceRange.min = 2500; priceRange.max = 5000"
                            class="px-3 py-2 text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors flex items-center justify-center gap-1">
                            <span class="text-red-500">₹</span> Above 2500
                        </button>
                    </div>

                    <!-- Reset Button -->
                    <div class="mt-4 flex justify-end">
                        <button type="button" @click="priceRange.min = 0; priceRange.max = 5000"
                            class="px-3 py-1 text-xs text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 flex items-center gap-1 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Reset to Default
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scrollable Product Grid Container -->
        <div class="overflow-y-auto" style="max-height: 70vh; padding-right: 6px;">
            <!-- Vertical Product List -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <template
                    x-for="(product, index) in filteredProducts()"
                    :key="index">
                    <div class="flex flex-col border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm overflow-hidden" style="height: 450px; width: 100%;">
                        <!-- First Partition: Image and Price -->
                        <div style="height: 180px; position: relative;" class="bg-gray-100 dark:bg-gray-800">
                            <!-- Fixed size image container -->
                            <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; overflow: hidden;" class="bg-gray-100 dark:bg-gray-800">
                                <img :src="product.image_url" :alt="product.name"
                                    style="width: 100%; height: 100%; object-fit: cover; object-position: center;">
                            </div>
                            <div class="absolute top-0 right-0 m-2 px-2 py-1 bg-primary-600 dark:bg-primary-500 text-white text-xs font-bold rounded">
                                ₹<span x-text="product.price"></span>/<span x-text="product.per"></span> <span x-text="product.unit_label"></span>
                            </div>
                            <!-- Category Badge -->
                            <div x-show="product.category" class="absolute bottom-0 left-0 m-2 px-2 py-1 bg-gray-800 bg-opacity-70 text-white text-xs font-medium rounded">
                                <span x-text="product.category"></span>
                            </div>
                        </div>

                        <!-- Second Partition: Name and Description -->
                        <div style="height: 100px; padding: 12px; border-bottom: 1px solid #e5e7eb; display: flex; flex-direction: column; overflow: hidden;" class="border-gray-200 dark:border-gray-700">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1 overflow-hidden text-ellipsis whitespace-nowrap" x-text="product.name"></h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 overflow-hidden line-clamp-4" x-text="truncateDescription(product.description)"></p>
                        </div>

                        <!-- Third Partition: Quantity, Unit Selection and Final Price -->
                        <div style="height: 170px; padding: 12px; display: flex; flex-direction: column; justify-content: space-between;">
                            <!-- Quantity Controls - Full Width -->
                            <div style="display: flex; flex-direction: column; gap: 10px;">
                                <div style="display: flex; align-items: center; width: 100%; gap: 12px;">
                                    <button type="button" @click="decreaseQty(index)"
                                        class="flex items-center justify-center w-10 h-10 text-gray-700 dark:text-gray-300 border-2 border-gray-300 dark:border-gray-600 rounded-full transition-colors"
                                        :class="{ 'opacity-50 cursor-not-allowed': product.qty === 0 }">
                                        <span class="text-xl font-bold">-</span>
                                    </button>

                                    <div class="flex-grow text-center">
                                        <span x-text="product.qty" class="text-xl font-semibold text-gray-900 dark:text-white"></span>
                                    </div>

                                    <button type="button" @click="increaseQty(index)"
                                        class="flex items-center justify-center w-10 h-10 text-white bg-primary-600 hover:bg-primary-500 dark:bg-primary-500 dark:hover:bg-primary-400 rounded-full transition-colors">
                                        <span class="text-xl font-bold">+</span>
                                    </button>
                                </div>

                                <!-- Unit Selection Dropdown - Below Quantity Controls -->
                                <div x-show="product.qty > 0" style="width: 100%;">
                                    <select :id="'unit-select-' + index"
                                        class="w-full px-3 py-2 text-sm border-2 border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-primary-500 focus:ring-primary-500"
                                        @change="setUnit(index, $event.target.value)">
                                        <template x-for="unitId in getAvailableUnits(product)" :key="unitId">
                                            <option :value="unitId" x-text="unitOptions[unitId]"
                                                :selected="parseInt(unitId) === parseInt(product.selectedUnit)"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>

                            <!-- Middle Section with Conversion Info and Final Price -->
                            <div style="flex-grow: 1; display: flex; flex-direction: column; justify-content: flex-end;">
                                <!-- Conversion Information -->
                                <div x-show="product.qty > 0 && parseInt(product.baseUnit) !== parseInt(product.selectedUnit)"
                                     class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                    <template x-if="parseInt(product.baseUnit) === 1 && parseInt(product.selectedUnit) === 2">
                                        <span>Converting from gram to kg</span>
                                    </template>
                                    <template x-if="parseInt(product.baseUnit) === 2 && parseInt(product.selectedUnit) === 1">
                                        <span>Converting from kg to gram</span>
                                    </template>
                                    <template x-if="parseInt(product.baseUnit) === 3 && parseInt(product.selectedUnit) === 4">
                                        <span>Converting from ml to ltr</span>
                                    </template>
                                    <template x-if="parseInt(product.baseUnit) === 4 && parseInt(product.selectedUnit) === 3">
                                        <span>Converting from ltr to ml</span>
                                    </template>
                                </div>

                                <!-- Final Price -->
                                <div x-show="product.qty > 0"
                                     class="flex justify-between items-center pt-2 border-t border-gray-200 dark:border-gray-700">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Final Price:</span>
                                    <span class="text-base font-bold text-primary-600 dark:text-primary-400">
                                        ₹<span x-text="calculateFinalPrice(product)"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- No Results -->
        <div x-show="filteredProducts().length === 0"
            class="py-12 text-center text-gray-500">
            <svg class="w-12 h-10 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                    d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No products found</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Try adjusting your search or filters to find what you're looking for.</p>
        </div>

        <!-- Selected Products Summary -->
        {{-- <div
            x-show="products.some(p => p.qty > 0)"
            class="fixed bottom-0 left-0 right-0 z-10 p-4 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 shadow-lg"
        >
            <div class="container flex flex-col sm:flex-row items-center justify-between mx-auto gap-2">
                <div class="flex items-center">
                    <span class="inline-flex items-center justify-center w-8 h-8 mr-2 rounded-full bg-primary-100 dark:bg-primary-900 text-primary-800 dark:text-primary-300">
                        <span x-text="products.filter(p => p.qty > 0).length"></span>
                    </span>
                    <span class="font-medium text-gray-900 dark:text-white">Selected Products</span>
                </div>

                <div class="flex items-center">
                    <span class="mr-2 text-gray-600 dark:text-gray-400">Total:</span>
                    <span class="text-lg font-medium text-primary-700 dark:text-primary-400">₹<span x-text="products.reduce((sum, p) => sum + parseFloat(calculateFinalPrice(p)), 0).toFixed(2)"></span></span>
                </div>
            </div>
        </div> --}}
    </div>
</x-dynamic-component>
