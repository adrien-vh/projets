import 'es6-promise/auto'

import Vue from 'vue'
import App from './composants/App.vue'
import store from './store.js'
import router from './router.js'
import Global from './composants/Global.vue'
import Server from './composants/Server.vue'


require("./styles/ibm-type.css")
require("./styles/font-awesome.css")
require("./styles/_copic.scss")
require("./styles/styles.scss")
require("./vendor/bootstrap/bootstrap.bundle.min.js")
require("./vendor/bootstrap/bootstrap.scss")
require("./vendor/hover-master/hover.scss")


const SERVER_URL = 'http://projets/server/';

// Moment.js en français
moment.locale('fr')

// Mixins globaux
Vue.mixin(Global)
Vue.mixin(Server)

// Racine de l'app
new Vue({
  el: '#app',
  router,
  store,
  render: h => h(App),
  mounted () {
    // SSO
    this.$$connectUserFromIp()

    // Chargement des contenus des listes déroulantes
    this.$$loadTypesEtapes()
    this.$$loadAxes()
    this.$$loadDirections()

    // Chargement de la liste des utilisateurs
    this.$$loadUtilisateurs()
  }
})
