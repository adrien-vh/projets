<template>
  <div>
    <input 
           type="text"
           :value="value"
           @input="updateValue($event.target.value)"
           v-show="editable"
    >
    <span v-show="!editable">{{ value }}</span>
  </div>
</template>

<script>
export default {
    props: {
        value: String,
        editable: Boolean
    },
    methods: {
        updateValue (value) {this.$emit('input', value) },
    }
}
</script>

<style scoped lang="scss">
  @import "../../styles/copic";
  
  div {
      display: inline-block;
      width: 100%;
  }

  input {
    border-width: 0 0 1px 0;
    border-color: rgb(175, 175, 175);
    color: #212529;
    width: 100%;
    background-color: transparent;
  }

  a {
      outline: none;
  }

</style>
