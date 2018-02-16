<template>
    <inNumber
        v-model="currentValue"
        :maxLength="2"
        suffix="mois"
        @input="updateValue"
        :editable="editable"
        :defaultValue="defaultValue"
        :minValue="minValue"
    ></inNumber>
</template>

<script>
import inNumber from '../elements/inNumber'

export default {
    components: { inNumber },
    data () { return { currentValue : this.value ? this.value.asMonths() : this.defaultValue }},

    watch: { value (value) { this.currentValue = this.value ? this.value.asMonths() : this.defaultValue } },

    props: { value: Object, editable: Boolean, defaultValue: Number, minValue: Number },

    methods: {
        updateValue (value) { this.$emit('input', moment.duration(parseFloat(this.currentValue), 'months')) }
    }
}
</script>
