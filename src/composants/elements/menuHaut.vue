<template>
  <div id="menuHaut">
    <ul>
      <!--<li><router-link to="/accueil" id="menuLogo">p<span>ro</span>j<span>ets</span></router-link></li>-->
      <li><router-link to="/accueil" :class="{ active: $route.name === 'accueil' }"><i class="fa fa-home fa-2x" aria-hidden="true"></i></router-link></li>
     <!-- <li><router-link to="/projet" :class="{ active: $route.name === 'projet' }">Projet</router-link></li>-->
      <!--<li><router-link to="/login">Configuration</router-link></li>-->
    </ul>
    <div id="connectionInfos">
      {{ $store.state.user.fullName }}<br>
      <a href="#" @click.prevent="$$logout()">Déconnexion</a>
    </div>
  </div>
</template>

<script>
export default { }
</script>

<style scoped lang="scss">
  @import "../../styles/copic";
  
  $baseBGColor: $CB99;
  $userBGColor: $CB06;
  $menuHeight: 40px;
  
  #menuHaut {
    position: fixed;
    z-index: 300;
    top: 0;
    left: 0;
    width: 100%;
    height: $menuHeight;
    background-color: $baseBGColor;
    
    #menuLogo {
      text-decoration: none;
      font-family: ibm-plex-serif;
      color: #fff;
      font-size: 16px;
      span {
        text-decoration: underline;
      }
    }
    
    ul {
      list-style-type: none;
      margin-bottom: 0;
      padding-left: 0;
      width: calc(100% - 300px);
      display: inline-block;
      height: $menuHeight;
      li {
        display: inline-block;
        a {
          display: block;
          height: $menuHeight;
          padding: 5px 10px;
          outline: none;
          
          color: lighten($baseBGColor, 30);
          
          &.active {
            background-color: darken($baseBGColor, 20);
            color: #fff;
            cursor: default;
          }
          
          &:hover {
            color: #fff;
            text-decoration: none;
          }
        }
      }
    }
    #connectionInfos {
      display: block;
      position: fixed;
      bottom: 20px;
      left: 20px;
      width: auto;
      padding: 5px 15px;
      border-radius: 10px;
      text-align: center;
      background-color: $userBGColor;
      height: auto;
      box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
      
      color: #fff;
      padding-top: 5px;
      
      i {
        margin-right: 5px;
      }
      
      a {
        color: #fff;
        text-decoration: underline;
        font-size: 12px;        
      }
    }
  }
</style>
