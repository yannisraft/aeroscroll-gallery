
//import './styles/quasar.css'
import './styles/quasar.scss';
//import './styles/quasar.scss';
import './styles/quasar-prefixed.scss';

/* 
import './styles/quasar.variables.scss' */


//import iconSet from 'quasar/icon-set/line-awesome.js'
//import iconSet from '@quasar/extras/material-icons-outlined/material-icons-outlined.css'
import iconSet from '@quasar/extras/material-icons-outlined/material-icons-outlined.css'

//import '@quasar/extras/roboto-font/roboto-font.css'

/* import '@quasar/extras/material-icons/material-icons.css' */
/* import '@quasar/extras/material-icons-outlined/material-icons-outlined.css'
import '@quasar/extras/material-icons-sharp/material-icons-sharp.css' */
import {
    Quasar,
    Dialog,
    Loading,
    LoadingBar,
    LocalStorage,
    SessionStorage
  } from 'quasar'

// To be used on app.use(Quasar, { ... })
export default {
    config: {
    },
    plugins: {
        Dialog,
        Loading,
        LoadingBar,
        LocalStorage,
        SessionStorage
    },
    extras: [
        'material-icons-outlined',
        'line-awesome'
    ],
    iconSet: iconSet
}