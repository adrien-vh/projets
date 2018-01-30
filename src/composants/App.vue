<template>
  <div id="app">
    <menuHaut v-show="$store.state.user.connected"></menuHaut>
    <div id="routerView" v-show="$store.state.user.connected">
      <router-view></router-view>
    </div>
    <loginForm v-show="!$store.state.user.connected"></loginForm>
    <popupMessages></popupMessages>
  </div>
</template>

<script>
  import menuHaut from './elements/menuHaut'
  import loginForm from './elements/loginForm'
  import popupMessages from './elements/popupMessages'
  
  export default {
    components: { menuHaut, loginForm, popupMessages },
    name: 'app'
  }
</script>

<style lang="scss">

  #routerView {
    margin-top: 50px;
    padding: 0 0px;
  }
  
</style>
