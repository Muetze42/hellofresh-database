/**
 * Settings Alpine.js Store
 *
 * Manages user settings with localStorage persistence.
 */
export default function settingsStore() {
    const storageKey = 'clickable_tags';

    return {
        clickableTags: false,

        init() {
            this.loadFromStorage();
            this.listenForUpdates();
        },

        loadFromStorage() {
            const stored = localStorage.getItem(storageKey);
            this.clickableTags = stored === 'true';
        },

        saveToStorage() {
            localStorage.setItem(storageKey, this.clickableTags.toString());
        },

        toggleClickableTags() {
            this.clickableTags = !this.clickableTags;
            this.saveToStorage();
        },

        setClickableTags(value) {
            this.clickableTags = value;
            this.saveToStorage();
        },

        listenForUpdates() {
            window.addEventListener('storage', (event) => {
                if (event.key === storageKey) {
                    this.loadFromStorage();
                }
            });
        }
    };
}
