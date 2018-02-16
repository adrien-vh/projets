<template>
  <div id="app">
    <menuHaut v-show="$store.state.user.connected"></menuHaut>
    <div id="routerView" v-show="$store.state.user.connected">
      <router-view></router-view>
    </div>
    <loginForm v-show="!$store.state.user.connected"></loginForm>

    <popupMessages></popupMessages>
    <div class="modal fade" id="modaleGlobale" tabindex="-1" role="dialog" aria-hidden="true" :class="{grande : $store.state.modale.grande}">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{$store.state.modale.titre}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" v-html="$store.state.modale.contenu"></div>
          <div class="modal-footer">
            <button type="button" class="btn btn-success" @click="$store.state.modale.onConfirm" v-show="$store.state.modale.confirmation"  data-dismiss="modal">
              <i class="fa fa-check fa-lg" aria-hidden="true"></i> Confirmer
            </button>
            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times fa-lg" aria-hidden="true"></i> Fermer</button>
          </div>
        </div>
      </div>
    </div>
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

  #modaleGlobale {

    &.grande {
      .modal-dialog {
        height: 100%;
      }

      .modal-content {
        height: calc(100% - 50px);
      }
    }

    .modal-body {
      height: 100%;
      iframe {
        width: 100%;
        height: 100%;
      }
    }

    .modal-footer {
      button {
        cursor: pointer;
      }
    }
  }
  
</style>
