import { createApp } from 'vue'
import App from './App.vue'
import store from './store'

import { Quasar } from 'quasar'
import quasarUserOptions from './quasar-user-options'
import i18n from './i18n.js'

const _win = (window as any);
const _page = _win.aeroscroll_page;

const app = createApp(App, { page: _page}).use(i18n).use(Quasar, quasarUserOptions);

// CREATE SHADOW
let wpMount = document.querySelector("#wp-vue-app-admin");
let holder = document.createElement("div");

if(wpMount)
{
    let shadow = wpMount.attachShadow({ mode: "open" });

    // ATTACH SHADOW CSS
    let cssholder = document.createElement("link");
    cssholder.type = 'text/css';
    cssholder.rel = 'stylesheet';
    cssholder.href = window['MEDIA_URL'] + 'dist/css/app.css';

    let extracssholder = document.createElement("link");
    extracssholder.type = 'text/css';
    extracssholder.rel = 'stylesheet';
    extracssholder.href = window['MEDIA_URL'] + 'css/aeroscroll-gallery-admin.css';

    shadow.appendChild(cssholder);
    shadow.appendChild(extracssholder);
    shadow.appendChild(holder);

    app.use(store).mount(holder);
}

