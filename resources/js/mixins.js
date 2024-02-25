import { usePage } from '@inertiajs/vue3'

// export default {
//   methods: {
//     __: function(key, replace = {}) {
//       let translations = usePage().props.translations
//       if (translations && translations[key]) {
//         key = translations[key]
//       }
//       Object.keys(replace).forEach(function (search) {
//         key = key.replace(':' + search, replace[search])
//       })
//       return key
//     }
//   }
// }
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
