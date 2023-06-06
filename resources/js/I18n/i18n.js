import { createI18n } from 'vue-i18n'
import en from '@/I18n/messages/en'
import kh from '@/I18n/messages/kh'

let locale = 'en'

if (localStorage.getItem('locale')) {
    locale = localStorage.getItem('locale')
}
console.log(locale)

const  messages = {
    en: en,
    kh: kh
}

const i18n = createI18n({
    locale: locale,
    messages,
    legacy: false
});

export default i18n