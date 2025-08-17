
# Frontend Developer Toolbox (Inertia.js + Vue 3 + Tailwind CSS)

This document outlines optional tools and enhancements for projects using this Laravel Starter Kit with Inertia.js and Vue 3. All listed packages are compatible with Vite and TypeScript.

---

## ✅ Already Included (Core Setup)

- **Vue 3 + Vite + TypeScript** — Fast, modern dev stack
- **Inertia.js** — SPA with Laravel backends
- **Tailwind CSS v4** — Utility-first styling
- **Sentry (`@sentry/vue`, `@sentry/vite-plugin`)** — Production error tracking
- **Prettier + ESLint + Tailwind Plugin** — Opinionated and consistent code formatting
- **`vue-tsc`** — Type checking for `.vue` files
- **`@norman-huth/support-js` + `vue-ray`** — DX utilities and debugging
- **`concurrently` + `vite-plugin-run`** — Multi-tasking in one terminal

---

## 🧩 Recommended Add-ons (Optional)

### 🔹 Auto Imports & Component Resolution

Auto-import Vue APIs and components on-demand:

```bash
pnpm add -D unplugin-auto-import unplugin-vue-components
```

> Great for reducing boilerplate with Composition API or Inertia-related helpers.

---

### 🔹 Global Store Management

Use **Pinia** instead of Vuex (official Vue 3 state library):

```bash
pnpm add pinia
```

> Optional – only needed if you're building global UI state.

---

### 🔹 Component Testing

Use Vitest + Vue Test Utils for lightweight component tests:

```bash
pnpm add -D vitest @vue/test-utils
```

> Consider adding `@vitest/ui` for a nice visual Testrunner.

---

### 🔹 PostCSS Plugins (optional)

Extend CSS capabilities with nesting or variables:

```bash
pnpm add -D postcss-nesting
```

> Mostly useful if you write custom CSS outside Tailwind.

---

### 🔹 Stylelint (Advanced Projects)

Optional CSS linter for large teams or design systems:

```bash
pnpm add -D stylelint stylelint-config-standard stylelint-config-prettier
```

---

## 🚀 Performance Tips

- Use `pnpm run type-check` before builds
- Enable [Vite production analysis](https://github.com/vitejs/vite-plugin-inspect) if needed
- Limit global state and DOM refs for better reactivity tracking

---

## 🧼 Recommended File Conventions

- `*.vue` should be named using PascalCase
- Group Inertia pages in `/Pages`, Vue components in `/Components`
- Use `<script setup lang="ts">` for all components

---

## 📁 Directory Suggestions

```
resources/
├── js/
│   ├── Components/
│   ├── Layouts/
│   ├── Pages/
│   └── support/
├── css/
│   └── app.css
└── views/
    └── app.blade.php
```

---

## ℹ️ Notes

- This toolbox is 100% optional. Your base setup is already production-grade.
- Choose add-ons that solve real problems in your project. Don’t over-engineer early.
