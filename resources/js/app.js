import './bootstrap';
import shoppingListStore from './stores/shopping-list';

let currentCountryCode = null;

const emptyStore = {
    items: [],
    servings: {},
    count: 0,
    isEmpty: true,
    has: () => false,
    toggle: () => {},
    loadFromStorage: () => {},
};

function initShoppingListStore() {
    const countryCode = document.body.dataset.country;

    if (!window.Alpine) {
        return;
    }

    // No country code - set empty placeholder store
    if (!countryCode) {
        if (!Alpine.store('shoppingList')) {
            Alpine.store('shoppingList', emptyStore);
        }
        currentCountryCode = null;
        return;
    }

    // If same country, just reload from storage
    if (currentCountryCode === countryCode) {
        const store = Alpine.store('shoppingList');
        if (store && store.loadFromStorage) {
            store.loadFromStorage();
        }
        return;
    }

    // Different country - create new store
    currentCountryCode = countryCode;
    Alpine.store('shoppingList', shoppingListStore(countryCode));
}

document.addEventListener('alpine:init', initShoppingListStore);
document.addEventListener('livewire:navigated', initShoppingListStore);
