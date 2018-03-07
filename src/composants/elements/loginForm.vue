<template>
  <div id="loginFormContainer">
    <div>
      <div class="titre">p<span>ro</span>j<span>ets</span></div>
      <form id="loginForm" @submit.prevent="onSubmit" action="foo">

        <div class="form-group">
          <label for="inputLogin">Nom d'utilisateur :</label><br>
          <input type="text" id="inputLogin" class="form-control" placeholder="Nom d'utilisateur" required autofocus v-model="login">
        </div>
        <div class="form-group">
          <label for="inputPassword">Mot de passe :</label><br>
          <input type="password" id="inputPassword" class="form-control" placeholder="Mot de passe" required v-model="password">
        </div>
        <button 
          class="btn btn-primary btn-block" type="submit"
          :class="{ disabled: runningQueries > 0 }"
        >
          <i class="fa fa-spinner fa-pulse fa-fw" v-show="runningQueries > 0"></i>
          <span v-show="runningQueries === 0">S'identifier</span>
        </button>
      </form>
      <div id="authIp" v-show="$store.state.ipUser.login">
        <div id="divOu"><span>ou</span></div>
        <button class="btn btn-primary btn-block" @click.prevent="$store.commit('ipLogin')" >
          Se connecter en tant que <br> &laquo; {{ $store.state.ipUser.fullName }} &raquo;
        </button>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    data () {
      return {
        login: '',
        password: ''
      }
    },
    methods: {
      onSubmit () {
        var loginDatas = {}

        loginDatas[C.LOGIN] = this.login
        loginDatas[C.PASSWORD] = this.password
        this.$$connectWithLoginPassword(loginDatas)
      }
    },
    mounted () {

    }
  }
</script>

<style scoped lang="scss">
  @import "../../styles/copic";
  
  .alert {
    margin-top: 20px;
  }
  
  #loginForm {
    margin-top: 30px;
    padding: 0 20px;
    
    button {
      margin-top: 40px;
      cursor: pointer;
    }
    
    .form-group {
      text-align: left;
    }
  }  
  
  #authIp {
    padding: 0 20px;
    
    button {
      cursor: pointer;
    }
    
    #divOu {
      margin: 0 20px 20px;
      position: relative;
      color: #888;
      text-align: center;
      border-bottom: 1px solid #888;
      height: 25px;

      span {
        width: 50px;
        margin-left: -25px;
        background-color: #fff;
        display: inline-block;
        position: absolute;
        top: 10px;
        left: 50%;
      }
    }
  }
    
  div#loginFormContainer {
    & > div {
      background-color: #fff;
      box-shadow: 0px 1px 20px 0px #999;
      width: 450px;
      height: 100%;
      margin: 0 auto;
      
      .titre {
        font-family: ibm-plex-serif;
        font-size: 80px;
        color: $CC00;
        background-color: $CB69;
        padding: 20px 0 30px;
        text-align: center; 
        span {
          text-decoration: underline;
        }
      }
    }
    
    background: url("../../assets/images/fond-login.jpg") no-repeat center fixed; 
    background-size: cover;
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    
    
  }
</style>
