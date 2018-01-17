import Accueil from './composants/pages/Accueil'
import Projet from './composants/pages/Projet'
import Vue from 'vue'
Vue.use(VueRouter)

var router = new VueRouter({ routes: [
  { path: '/', name:'accueil', component: Accueil },
  { path: '/Projet', name:'projet', component: Projet },
  { path: '/Projet/:num_projet', name:'projet', component: Projet, props: true },
  { path: '*', redirect: '/' }
]})


export default router