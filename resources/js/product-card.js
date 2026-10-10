// B Cafe Product Card Alpine.js Component & State Manager
// Supports reactive image gallery, quantity controls, cart sync, favorites & events

export function initProductCard(Alpine) {
    if (!Alpine) return;

    Alpine.data('productCard', (config = {}) => ({
        id: config.id,
        name: config.name || '',
        price: Number(config.price) || 0,
        originalPrice: config.originalPrice ? Number(config.originalPrice) : null,
        activeImage: config.image || (config.images && config.images.length ? config.images[0] : null),
        images: Array.isArray(config.images) ? config.images : (config.image ? [config.image] : []),
        stock: typeof config.stock === 'number' ? config.stock : 99,
        isFavorite: Boolean(config.isFavorite),
        quantity: Number(config.initialQuantity) || 0,
        currency: config.currency || 'تومان',
        hasImageError: false,
        hasDiscount: Boolean(config.hasDiscount),
        isDiscounted: Boolean(config.hasDiscount),
        discountRemainingSeconds: typeof config.discountRemainingSeconds === 'number' ? config.discountRemainingSeconds : null,
        discountRemainingHuman: config.discountRemainingHuman || null,
        discountCountdownText: config.discountRemainingHuman || '',
        timerInterval: null,

        init() {
            // Restore favorite state from localStorage if available
            try {
                const favs = JSON.parse(localStorage.getItem('bcafe_favorites') || '[]');
                if (favs.includes(this.id)) {
                    this.isFavorite = true;
                }
            } catch (e) {}

            // Initial cart sync
            this.syncQuantityFromCart();

            // Discount countdown ticker
            if (this.discountRemainingSeconds && this.discountRemainingSeconds > 0) {
                this.updateCountdownText();
                this.timerInterval = setInterval(() => {
                    if (this.discountRemainingSeconds > 0) {
                        this.discountRemainingSeconds--;
                        this.updateCountdownText();
                    } else {
                        clearInterval(this.timerInterval);
                        this.discountRemainingSeconds = 0;
                        this.discountCountdownText = '';
                        this.hasDiscount = false;
                        this.isDiscounted = false;
                        if (this.originalPrice) {
                            this.price = this.originalPrice;
                            this.originalPrice = null;
                        }
                    }
                }, 1000);
            }

            // Reactive listeners
            window.addEventListener('cart-updated', (e) => {
                if (e.detail && e.detail.source === 'productCard-' + this.id) return;
                this.syncQuantityFromCart();
            });

            window.addEventListener('favorite-toggled', (e) => {
                if (e.detail && e.detail.productId === this.id) {
                    this.isFavorite = e.detail.isFavorite;
                }
            });
        },

        updateCountdownText() {
            if (!this.discountRemainingSeconds || this.discountRemainingSeconds <= 0) {
                this.discountCountdownText = '';
                this.hasDiscount = false;
                this.isDiscounted = false;
                if (this.originalPrice) {
                    this.price = this.originalPrice;
                    this.originalPrice = null;
                }
                return;
            }
            const hours = Math.floor(this.discountRemainingSeconds / 3600);
            const minutes = Math.floor((this.discountRemainingSeconds % 3600) / 60);
            const seconds = this.discountRemainingSeconds % 60;
            const pad = (n) => String(n).padStart(2, '0');
            if (hours > 0) {
                this.discountCountdownText = `${hours}:${pad(minutes)}:${pad(seconds)} مانده`;
            } else {
                this.discountCountdownText = `${minutes}:${pad(seconds)} مانده`;
            }
        },

        get countdownHours() {
            if (!this.discountRemainingSeconds || this.discountRemainingSeconds <= 0) return '00';
            return String(Math.floor(this.discountRemainingSeconds / 3600)).padStart(2, '0');
        },

        get countdownMinutes() {
            if (!this.discountRemainingSeconds || this.discountRemainingSeconds <= 0) return '00';
            return String(Math.floor((this.discountRemainingSeconds % 3600) / 60)).padStart(2, '0');
        },

        get countdownSeconds() {
            if (!this.discountRemainingSeconds || this.discountRemainingSeconds <= 0) return '00';
            return String(this.discountRemainingSeconds % 60).padStart(2, '0');
        },

        get hasValidCountdown() {
            return Boolean(this.discountRemainingSeconds && this.discountRemainingSeconds > 0);
        },

        get currentQty() {
            // Check if parent scope has getItemQuantity (e.g. storefrontMenu)
            if (typeof this.getItemQuantity === 'function') {
                return this.getItemQuantity(this.id);
            }
            return this.quantity;
        },

        syncQuantityFromCart() {
            try {
                const cart = JSON.parse(localStorage.getItem('bcafe_cart') || '[]');
                const item = cart.find(i => (i.product_id === this.id || i.id === this.id));
                this.quantity = item ? item.quantity : 0;
            } catch (e) {
                this.quantity = 0;
            }
        },

        setActiveImage(img) {
            this.activeImage = img;
            this.hasImageError = false;
        },

        toggleFavorite() {
            this.isFavorite = !this.isFavorite;
            try {
                let favs = JSON.parse(localStorage.getItem('bcafe_favorites') || '[]');
                if (this.isFavorite) {
                    if (!favs.includes(this.id)) favs.push(this.id);
                } else {
                    favs = favs.filter(id => id !== this.id);
                }
                localStorage.setItem('bcafe_favorites', JSON.stringify(favs));
            } catch (e) {}

            // Backend sync if user is logged in
            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.content;
                if (token) {
                    fetch('/customer/favorites/toggle', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify({ product_id: this.id })
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data && data.success && typeof data.is_favorite === 'boolean') {
                            this.isFavorite = data.is_favorite;
                            let favs = JSON.parse(localStorage.getItem('bcafe_favorites') || '[]');
                            if (this.isFavorite) {
                                if (!favs.includes(this.id)) favs.push(this.id);
                            } else {
                                favs = favs.filter(id => id !== this.id);
                            }
                            localStorage.setItem('bcafe_favorites', JSON.stringify(favs));
                        }
                    })
                    .catch(() => {});
                }
            } catch (e) {}

            window.dispatchEvent(new CustomEvent('favorite-toggled', {
                detail: { productId: this.id, isFavorite: this.isFavorite }
            }));
        },

        handleCardClick(modalData) {
            if (typeof this.openProductModal === 'function' && modalData) {
                this.openProductModal(modalData);
            }
        },

        handleActionClick(modalData) {
            if (typeof this.openProductModal === 'function' && modalData) {
                this.openProductModal(modalData);
                return;
            }
            this.addToCart();
        },

        handleIncrement() {
            if (typeof this.increment === 'function' && this.increment !== this.handleIncrement) {
                // Call parent increment if available
                const parentIncrement = this.increment;
                if (parentIncrement.length > 0) {
                    parentIncrement.call(this, this.id);
                    return;
                }
            }
            this.localIncrement();
        },

        handleDecrement() {
            if (typeof this.decrement === 'function' && this.decrement !== this.handleDecrement) {
                // Call parent decrement if available
                const parentDecrement = this.decrement;
                if (parentDecrement.length > 0) {
                    parentDecrement.call(this, this.id);
                    return;
                }
            }
            this.localDecrement();
        },

        addToCart() {
            if (this.stock <= 0) return;
            try {
                let cart = JSON.parse(localStorage.getItem('bcafe_cart') || '[]');
                const existing = cart.find(i => (i.product_id === this.id || i.id === this.id));
                if (existing) {
                    existing.quantity++;
                    this.quantity = existing.quantity;
                } else {
                    cart.push({
                        id: this.id,
                        product_id: this.id,
                        name: this.name,
                        price: this.price,
                        base_price: this.price,
                        image: this.activeImage,
                        quantity: 1
                    });
                    this.quantity = 1;
                }
                localStorage.setItem('bcafe_cart', JSON.stringify(cart));
                window.dispatchEvent(new CustomEvent('cart-updated', {
                    detail: { source: 'productCard-' + this.id, cart }
                }));
            } catch (e) {}
        },

        localIncrement() {
            if (this.quantity >= this.stock) return;
            try {
                let cart = JSON.parse(localStorage.getItem('bcafe_cart') || '[]');
                const item = cart.find(i => (i.product_id === this.id || i.id === this.id));
                if (item) {
                    item.quantity++;
                    this.quantity = item.quantity;
                } else {
                    this.addToCart();
                    return;
                }
                localStorage.setItem('bcafe_cart', JSON.stringify(cart));
                window.dispatchEvent(new CustomEvent('cart-updated', {
                    detail: { source: 'productCard-' + this.id, cart }
                }));
            } catch (e) {}
        },

        localDecrement() {
            if (this.quantity <= 0) return;
            try {
                let cart = JSON.parse(localStorage.getItem('bcafe_cart') || '[]');
                const idx = cart.findIndex(i => (i.product_id === this.id || i.id === this.id));
                if (idx > -1) {
                    if (cart[idx].quantity > 1) {
                        cart[idx].quantity--;
                        this.quantity = cart[idx].quantity;
                    } else {
                        cart.splice(idx, 1);
                        this.quantity = 0;
                    }
                    localStorage.setItem('bcafe_cart', JSON.stringify(cart));
                    window.dispatchEvent(new CustomEvent('cart-updated', {
                        detail: { source: 'productCard-' + this.id, cart }
                    }));
                } else {
                    this.quantity = 0;
                }
            } catch (e) {}
        }
    }));
}

// Global browser window fallback
if (typeof window !== 'undefined') {
    window.initProductCard = initProductCard;
    if (window.Alpine) {
        initProductCard(window.Alpine);
    }
}
