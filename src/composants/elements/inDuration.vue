<template>
    <inNumber v-model="currentValue" :maxLength="2" suffix="mois" @input="updateValue"></inNumber>
</template>

<script>
import inNumber from '../elements/inNumber'

export default {
    components: { inNumber },
    data () { return { currentValue : this.value.asMonths() }},

    watch: { value (value) { this.currentValue = this.value.asMonths() } },

    props: { value: Object },

    methods: {
        updateValue (value) { console.log("ici"); this.$emit('input', moment.duration(parseFloat(this.currentValue), 'months')) }
    }
}
</script>
