<template>
  <div>
    <!--<input 
           type="text"
           :value="value"
           @input="updateValue($event.target.value)"
           v-show="editable"
    >-->
    <span v-for="typeEtape in $store.state.typesEtapes" :key="typeEtape.num_typeEtape" v-if="typeEtape.num_typeEtape == value">
      <a href="#" @click.prevent="editing = true" v-show="editable">
        <i class="fa fa-square" aria-hidden="true" :style="{ color: '#' + typeEtape.couleur }"></i> {{ typeEtape.intitule }}
      </a>
      <span v-show="!editable">
        <i class="fa fa-square" aria-hidden="true" :style="{ color: '#' + typeEtape.couleur }"></i> {{ typeEtape.intitule }}
      </span>
    </span>
    
    <div class="choix" v-show="editing">
      <span v-for="typeEtape in $store.state.typesEtapes" :key="typeEtape.num_typeEtape">
        <a href="#" @click.prevent="updateValue(typeEtape.num_typeEtape)">
          <i class="fa fa-square" aria-hidden="true" :style="{ color: '#' + typeEtape.couleur }"></i>&nbsp;{{ typeEtape.intitule }}
        </a>
      </span>
    </div>
  </div>
</template>

<script>
export default {
    props: {
        value: String,
        editable: Boolean
    },
    data () {
      return {
        editing: false
      }
    },
    methods: {
        updateValue (value) {
          this.$emit('input', value)
          this.editing = false
        },
    }
}
</script>

<style scoped lang="scss">
  @import "../../styles/copic";

  div {
    position: relative;

    .choix {
      position: absolute;
      z-index: 100;
      top: 0;
      left: -5px;
      background-color: #fff;
      padding: 5px;
      border: 1px solid $CW2;
      width: 130px;
      span {
        display: inline-block;
        width: 100%;
      }
    }
  }

  a {
      outline: none;
  }

</style>
