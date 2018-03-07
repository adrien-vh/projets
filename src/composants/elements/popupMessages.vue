<template>
  <div id="popupMessage" :class="{ active: $store.state.messages.length > 0 }">
    <div v-for="m in $store.state.messages.length" :key="m">
      <i class="fa fa-check fa-3x" aria-hidden="true" v-if="$store.state.messages[m-1].type === 'success'"></i>
      <i class="fa fa-info fa-3x" aria-hidden="true" v-if="$store.state.messages[m-1].type === 'info'"></i>
      <i class="fa fa-times fa-3x" aria-hidden="true" v-if="$store.state.messages[m-1].type === 'error'"></i>
      <br>
      {{$store.state.messages[m-1].text}}
    </div>
  </div>
</template>

<script>
export default { }
</script>

<style scoped lang="scss">
  @import "../../styles/copic";
  
  #popupMessage {
    position: fixed;
    top: -150px;
    left: 50%;
    width: 300px;
    text-align: center;
    margin-left: -150px;
    border-radius: 0 0 10px 10px;
    background-color: #fff;
    box-shadow: 0 0 10px rgba(90,85,79,.7);
    padding: 0 5px 10px 5px;
    transition: all .3s ease-in-out;
    z-index: 400;
    
    i.fa-check { color: $CYG07; }
    i.fa-info { color: $CB26; }
    i.fa-times { color: $CR14; }
    
    &.active {
      top: 0px;
    }
  }
</style>
