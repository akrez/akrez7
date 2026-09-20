import Alpine from 'alpinejs';

const OPTIONS_FILTER = 'options';
const RANGE_FILTER = 'range';

Alpine.data('presentPage', (config) => ({
    search: '',
    selectedCategory: '',
    activeFilters: {},
    basket: {},
    submitting: false,

    init() {
        (config.packages || []).forEach((packageId) => {
            this.basket[packageId] = 0;
        });
        this.resetFilters();
    },

    get categoryFilters() {
        if (this.selectedCategory === '') {
            return [];
        }

        return (config.properties || []).filter(
            (property) => String(property.category_id) === String(this.selectedCategory)
        );
    },

    get selectedCategoryName() {
        const category = (config.categories || []).find(
            (item) => String(item.id) === String(this.selectedCategory)
        );

        return category ? category.name : '';
    },

    filterTypeOf(property) {
        if (property.filter_type) {
            return property.filter_type.value;
        }

        const values = (property.property_values || []).map((option) => option.property_value);

        if (values.length > 0 && values.every((value) => value !== '' && !isNaN(parseFloat(value)))) {
            return RANGE_FILTER;
        }

        return OPTIONS_FILTER;
    },

    rangeValues(property) {
        const numbers = (property.property_values || [])
            .map((option) => parseFloat(option.property_value))
            .filter((number) => !isNaN(number));

        return [...new Set(numbers)].sort((a, b) => a - b);
    },

    selectCategory(categoryId) {
        this.selectedCategory = String(categoryId);
        this.resetFilters();
    },

    resetFilters() {
        const filters = {};

        this.categoryFilters.forEach((property) => {
            const type = this.filterTypeOf(property);

            if (type === RANGE_FILTER) {
                const values = this.rangeValues(property);

                filters[property.property_key] = {
                    type: RANGE_FILTER,
                    values,
                    minIndex: 0,
                    maxIndex: Math.max(0, values.length - 1),
                };
            } else {
                filters[property.property_key] = { type: OPTIONS_FILTER, selected: [] };
            }
        });

        this.activeFilters = filters;
    },

    setRangeIndex(propertyKey, bound, value) {
        const filter = this.activeFilters[propertyKey];

        if (!filter) {
            return;
        }

        const index = parseInt(value, 10);

        if (isNaN(index)) {
            return;
        }

        if (bound === 'min') {
            filter.minIndex = Math.min(index, filter.maxIndex);
        } else {
            filter.maxIndex = Math.max(index, filter.minIndex);
        }
    },

    toggleOption(propertyKey, value) {
        const filter = this.activeFilters[propertyKey];

        if (!filter) {
            return;
        }

        const index = filter.selected.indexOf(value);

        if (index === -1) {
            filter.selected.push(value);
        } else {
            filter.selected.splice(index, 1);
        }
    },

    matches(product) {
        const search = this.search.trim().toLowerCase();

        if (search !== '' && product.name.toLowerCase().indexOf(search) === -1) {
            return false;
        }

        if (
            this.selectedCategory !== '' &&
            String(product.category) !== String(this.selectedCategory)
        ) {
            return false;
        }

        for (const property of this.categoryFilters) {
            const filter = this.activeFilters[property.property_key];

            if (!filter) {
                continue;
            }

            const values = product.properties[property.property_key] || [];

            if (filter.type === RANGE_FILTER) {
                const numbers = values
                    .map((value) => parseFloat(value))
                    .filter((value) => !isNaN(value));

                if (numbers.length === 0) {
                    return false;
                }

                const min = filter.values[filter.minIndex];
                const max = filter.values[filter.maxIndex];

                if (Math.min(...numbers) < min || Math.max(...numbers) > max) {
                    return false;
                }
            } else if (
                filter.selected.length > 0 &&
                !filter.selected.some((selected) => values.indexOf(selected) !== -1)
            ) {
                return false;
            }
        }

        return true;
    },

    isVisible(productId) {
        const product = (config.products || []).find((item) => item.id === productId);

        return product ? this.matches(product) : true;
    },

    get visibleCount() {
        return (config.products || []).filter((product) => this.matches(product)).length;
    },

    incrementBasket(packageId) {
        this.basket[packageId] = (parseInt(this.basket[packageId], 10) || 0) + 1;
    },

    decrementBasket(packageId) {
        this.basket[packageId] = Math.max(0, (parseInt(this.basket[packageId], 10) || 0) - 1);
    },

    get basketCount() {
        return Object.values(this.basket).reduce(
            (total, count) => total + (parseInt(count, 10) || 0),
            0
        );
    },

    get basketIsEmpty() {
        return this.basketCount === 0;
    },

    resetBasket() {
        Object.keys(this.basket).forEach((packageId) => {
            this.basket[packageId] = 0;
        });
    },

    async submitInvoice(form) {
        if (this.submitting) {
            return;
        }

        const requiredMessages = {
            'invoice_delivery[name]': 'فیلد نام الزامی است.',
            'invoice_delivery[mobile]': 'فیلد شماره همراه الزامی است.',
            'invoice_delivery[city]': 'فیلد شهر الزامی است.',
            'invoice_delivery[address]': 'فیلد نشانی الزامی است.',
        };

        const oldFormData = new FormData(form);
        const formData = new FormData();
        const validationErrors = [];

        for (const [key, value] of oldFormData.entries()) {
            const itemMatch = key.match(/invoice_items\[(\d+)\]\[cnt\]/);

            if (itemMatch) {
                const packageId = itemMatch[1];
                const count = parseInt(value, 10) || 0;

                if (count > 0) {
                    formData.append(`invoice_items[${packageId}][package_id]`, packageId);
                    formData.append(`invoice_items[${packageId}][cnt]`, count);
                }

                continue;
            }

            if (
                Object.prototype.hasOwnProperty.call(requiredMessages, key) &&
                String(value).trim() === ''
            ) {
                validationErrors.push(requiredMessages[key]);
                continue;
            }

            formData.append(key, value);
        }

        if (this.basketIsEmpty) {
            validationErrors.push('سبد خرید شما خالی است.');
        }

        if (validationErrors.length > 0) {
            Swal.fire({
                icon: 'warning',
                html: validationErrors.join('<br>'),
                confirmButtonText: 'بستن',
            });

            return;
        }

        this.submitting = true;

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: { Accept: 'application/json' },
                body: formData,
            });
            const data = await response.json();

            if (data.status === 422) {
                let errorMessage = '';

                for (const key in data.errors) {
                    errorMessage += data.errors[key].join('<br>') + '<br>';
                }

                Swal.fire({ icon: 'warning', html: errorMessage, confirmButtonText: 'بستن' });
            } else if (data.status === 200 || data.status === 201) {
                this.resetBasket();
                document.querySelector('#invoice-modal .btn-close')?.click();
                Swal.fire({
                    icon: 'success',
                    title: data.message,
                    confirmButtonText: 'بستن',
                });
            } else {
                Swal.fire({ icon: 'error', title: data.message, confirmButtonText: 'بستن' });
            }
        } catch (error) {
            Swal.fire({ icon: 'error', title: 'خطا', confirmButtonText: 'بستن' });
        } finally {
            this.submitting = false;
        }
    },
}));
