import './../bootstrap';
import shoppingListStore from './stores/shopping-list';
import settingsStore from './stores/settings';

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

function initStores() {
    const countryCode = document.body.dataset.country;

    if (!window.Alpine) {
        return;
    }

    // Initialize settings store if not exists
    if (!Alpine.store('settings')) {
        Alpine.store('settings', settingsStore());
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

document.addEventListener('alpine:init', initStores);
document.addEventListener('livewire:navigated', initStores);

// Fix Safari bfcache issues with stale Livewire snapshots
window.addEventListener('pageshow', (event) => {
    if (event.persisted) {
        window.location.reload();
    }
});
