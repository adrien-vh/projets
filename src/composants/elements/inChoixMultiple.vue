<template>
  <div>
    <span v-for="element in listeElements" :key="element[champValeur]" v-if="element[champValeur] == value">
      <span v-show="editable">
        <i class="fa fa-square" aria-hidden="true" :style="{ color: '#' + element[champCouleur] }" v-if="champCouleur"></i>
        <input type="text" :value="element[champLabel]" @blur="onBlur" @focus="editing = true">
      </span>
      <span v-show="!editable">
        <i class="fa fa-square" aria-hidden="true" :style="{ color: '#' + element[champCouleur] }" v-if="champCouleur"></i> {{ element[champLabel] }}
      </span>
    </span>
    
    <div class="choix" v-show="editing">
      <span v-for="element in listeElements" :key="element[champValeur]">
        <a href="#" @click.prevent="updateValue(element[champValeur])" :data-valeur="element[champValeur]">
          <i class="fa fa-square" aria-hidden="true" :style="{ color: '#' + element[champCouleur] }" v-if="champCouleur"></i>&nbsp;{{ element[champLabel] }}
        </a>
      </span>
    </div>
  </div>
</template>

<script>
export default {
    props: {
        value: {},
        editable: Boolean,
        listeElements: Array,
        champValeur: String,
        champLabel: String,
        champCouleur: String
    },
    data () {
      return {
        editing: false
      }
    },
    methods: {
        updateValue (value) {
          this.$emit('input', value)
        },
        onBlur (event) {
          if (event.relatedTarget) if (event.relatedTarget.getAttribute("data-valeur")) {
            this.updateValue(event.relatedTarget.getAttribute("data-valeur"))
          }
          this.editing = false
        }
    }
}
</script>

<style scoped lang="scss">
  @import "../../styles/copic";

  div {
    position: relative;
    input {
      width: calc(100% - 20px) !important;
      background-color: transparent;
      border: none !important;
      color: $CB14 !important;
      cursor: pointer;
      text-decoration: underline;
      &:focus {
        outline: none;
      }
    }

    .choix {
      position: absolute;
      z-index: 100;
      top: 0;
      left: -5px;
      background-color: #fff;
      padding: 5px;
      border: 1px solid $CW2;
      /*width: 130px;*/
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
