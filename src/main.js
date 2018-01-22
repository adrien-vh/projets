import 'es6-promise/auto'

import Vue from 'vue'
import App from './composants/App.vue'
import store from './store.js'
import router from './router.js'
import Global from './composants/Global.vue'


require("./styles/ibm-type.css")
require("./styles/font-awesome.css")
require("./styles/_copic.scss")
require("./styles/styles.scss")
require("./vendor/bootstrap/bootstrap.bundle.min.js")
require("./vendor/bootstrap/bootstrap.scss")


const SERVER_URL = 'http://projets/server/';

console.log(C)
moment.locale('fr')

Vue.mixin(Global)

new Vue({
  el: '#app',
  router,
  store,
  render: h => h(App),
  mounted () {
    this.$store.dispatch('connectUserFromIp')
  }
})
