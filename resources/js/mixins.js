import { usePage } from '@inertiajs/vue3'

/**
 * @param {String} key
 * @param {Object} replace

 * @return String
 */
export function __(key, replace = {}) {
  let translations = usePage().props.translations
  if (translations && translations[key]) {
    key = translations[key]
  }
  Object.keys(replace).forEach(function (search) {
    key = key.replace(':' + search, replace[search])
  })

  return key
}
