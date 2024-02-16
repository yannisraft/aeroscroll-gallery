import { createI18n, LocaleMessages, VueMessageType } from 'vue-i18n'
//import messages from '@/i18n';

import en_texts from './locale/en.js';
import el_texts from './locale/el.js';
import de_texts from './locale/de.js';
import fr_texts from './locale/fr.js';
import es_texts from './locale/es.js';
import pt_texts from './locale/pt.js';
import tr_texts from './locale/tr.js';
import ko_texts from './locale/ko.js';
import ja_texts from './locale/ja.js';
import zh_texts from './locale/zh.js';
import ru_texts from './locale/ru.js';
import it_texts from './locale/it.js';
import hi_texts from './locale/hi.js';


export default createI18n({
  locale: process.env.VUE_APP_I18N_LOCALE || 'en',
  fallbackLocale: process.env.VUE_APP_I18N_FALLBACK_LOCALE || 'en',
  legacy: false,
  warnHtmlMessage: false,
  messages: {
    en: en_texts,
    el: el_texts,
    'en_US': en_texts,
    'el_GR': el_texts,
    'de_DE': de_texts,
    'de_AT': de_texts,
    'de_CH': de_texts,
    'de_LU': de_texts,
    'fr_FR': fr_texts,
    'fr_BE': fr_texts,
    'fr_CA': fr_texts,
    'es_ES': es_texts,
    'es_BO': es_texts,
    'es_CR': es_texts,
    'es_VE': es_texts,
    'es_MX': es_texts,
    'es_EC': es_texts,
    'es_CO': es_texts,
    'es_DO': es_texts,
    'es_PE': es_texts,
    'es_CL': es_texts,
    'es_PR': es_texts,
    'es_UY': es_texts,
    'es_GT': es_texts,
    'es_AR': es_texts,
    'it_IT': it_texts,
    'pt_PT': pt_texts,
    'pt_BR': pt_texts,
    'hi_IN': hi_texts,
    'tr_TR': tr_texts,
    'ko_KR': ko_texts,
    'ja_JP': ja_texts,
    'ja': ja_texts,
    'zh_CN': zh_texts,
    'ru_RU': ru_texts,
  }
  /* messages: () => {
    const locales = require.context('./locale', true, /[A-Za-z0-9-_,\s]+\.json$/i)
    const messages = {}
    console.log("locales: ", locales);
    console.log("locale: ", locale);
    locales.keys().forEach(key => {
        const matched = key.match(/([A-Za-z0-9-_]+)\./i)
        if (matched && matched.length > 1) {
        const locale = matched[1]
        messages[locale] = locales(key).default
        }
    })
    return messages
  } */
  //messages: messages
})
