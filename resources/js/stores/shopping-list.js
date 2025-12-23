/**
 * Shopping List Alpine.js Store
 *
 * Manages shopping list state with localStorage persistence and Reverb sync.
 */
export default function shoppingListStore(countryCode) {
    const storageKey = `shoppingList${countryCode.toUpperCase()}`;
    const servingsKey = `shoppingListServings${countryCode.toUpperCase()}`;
    const channelName = `shopping-list.${countryCode.toLowerCase()}`;

    return {
        items: [],
        servings: {},
        initialized: false,

        init() {
            if (this.initialized) {
                return;
            }

            this.loadFromStorage();
            this.listenForUpdates();
            this.initialized = true;
        },

        loadFromStorage() {
            const storedItems = localStorage.getItem(storageKey);
            const storedServings = localStorage.getItem(servingsKey);

            this.items = storedItems ? JSON.parse(storedItems) : [];
            this.servings = storedServings ? JSON.parse(storedServings) : {};
        },

        saveToStorage() {
            localStorage.setItem(storageKey, JSON.stringify(this.items));
            localStorage.setItem(servingsKey, JSON.stringify(this.servings));
        },

        add(recipeId) {
            recipeId = Number(recipeId);

            if (this.has(recipeId)) {
                return;
            }

            this.items.push(recipeId);
            this.saveToStorage();
            this.broadcastUpdate();
        },

        remove(recipeId) {
            recipeId = Number(recipeId);
            this.items = this.items.filter(id => id !== recipeId);
            delete this.servings[recipeId];
            this.saveToStorage();
            this.broadcastUpdate();
        },

        toggle(recipeId) {
            if (this.has(recipeId)) {
                this.remove(recipeId);
            } else {
                this.add(recipeId);
            }
        },

        has(recipeId) {
            return this.items.includes(Number(recipeId));
        },

        setServings(recipeId, amount) {
            recipeId = Number(recipeId);
            amount = Number(amount);

            if (!this.has(recipeId)) {
                return;
            }

            this.servings[recipeId] = amount;
            this.saveToStorage();
            this.broadcastUpdate();
        },

        getServings(recipeId, defaultValue = null) {
            return this.servings[Number(recipeId)] ?? defaultValue;
        },

        clear() {
            this.items = [];
            this.servings = {};
            this.saveToStorage();
            this.broadcastUpdate();
        },

        get count() {
            return this.items.length;
        },

        get isEmpty() {
            return this.items.length === 0;
        },

        broadcastUpdate() {
            if (typeof window.Echo === 'undefined') {
                return;
            }

            // Dispatch a custom browser event for same-window components
            window.dispatchEvent(new CustomEvent('shopping-list-updated', {
                detail: { items: this.items, servings: this.servings }
            }));
        },

        listenForUpdates() {
            // Listen for storage changes from other tabs
            window.addEventListener('storage', (event) => {
                if (event.key === storageKey || event.key === servingsKey) {
                    this.loadFromStorage();
                }
            });

            // Listen for same-window updates
            window.addEventListener('shopping-list-updated', () => {
                // Already updated, just for reactivity trigger if needed
            });
        }
    };
}
