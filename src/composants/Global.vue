<script>
    export default {
        methods: {
            $test (val) { return "Retour de la sdfmethode" },
            $formatDate (date) { return date.format("MMM YYYY ") },
            $stepEndDate (step) {
                return moment(step.debut).add(step.duree).subtract(1, "d")
            }
        }
    }
</script>