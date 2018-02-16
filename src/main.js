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

moment.locale('fr')

Vue.mixin(Global)
Vue.mixin(Server)

new Vue({
  el: '#app',
  router,
  store,
  render: h => h(App),
  mounted () {
    this.$$connectUserFromIp()
    this.$$loadTypesEtapes()
    this.$$loadAxes()
    this.$$loadDirections()
    this.$$loadUtilisateurs()
  }
})
