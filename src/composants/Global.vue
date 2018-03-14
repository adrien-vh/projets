<!--
  MIXIN GLOBAL
-->
<script>
    export default {
        methods: {
            /** 
            * Formattage d'une date moment
            * 
            * @param  {Moment}  date    Date à formatter
            *
            * @return {String} Date au formet "MMM YYYY"
            */
            $formatDate (date) { return date.format("MMM YYYY ") },

            /** 
            * Calcul de la fin d'une étape
            * 
            * @param  {Object}  step    Étape
            *
            * @return {Moment}  Date de fin de l'étape
            */
            $stepEndDate (step) { return moment(step.debut).add(step.duree).subtract(1, "d") },

            /** 
            * Affichage d'une fenêtre modale d'informations
            * 
            * @param  {String}   title                      Titre de la fenêtre modale
            * @param  {String}   content                    Contenu HTML de la fenêtre modale
            * @param  {Boolean}  [grande=false]             Taille de la fenêtre modale
            * @param  {Boolean}  [confirmation=false]       Bouton de confirmation ?
            * @param  {Function} [onConfirm=function(){}]   Fonction appelée lors de la confirmation
            *
            * @return {Moment}  Date de fin de l'étape
            */
            $showModal (title, content, grande = false, confirmation = false, onConfirm = function () {}) {
                this.$store.state.modale.titre = title
                this.$store.state.modale.contenu = content
                this.$store.state.modale.grande = grande
                this.$store.state.modale.confirmation = confirmation
                this.$store.state.modale.onConfirm = onConfirm
                $('#modaleGlobale').modal()
            },

            /** 
            * Retourne le nom complet d'un utilisateur à partir de son login
            * 
            * @param  {String}  login    Login de l'utilisateur
            *
            * @return {String}  Nom complet de l'utilisateur (login si pas trouvé)
            */
            $userName (login) {
                var i
                for (i = 0; i < this.$store.state.utilisateurs.length; i += 1) {
                    if (this.$store.state.utilisateurs[i].login === login) {
                        return this.$store.state.utilisateurs[i].nom
                    }
                }
                return login
            },

            /** 
            * Retourne le niveau d'accès à un projet pour l'utilisateur connecte
            * 
            * @param  {String}  num_projet    Index du projet
            *
            * @return {int}  Niveau d'accès
            */
            $niveauAcces (num_projet) {
                var i, num_projet = parseInt(num_projet)
                
                if (this.$store.state.user.connecte) {
                    for (i = 0; i < this.$store.state.user.droitsProjets.length; i += 1) {
                        if (parseInt(this.$store.state.user.droitsProjets[i].num_projet) == num_projet) {
                            return parseInt(this.$store.state.user.droitsProjets[i].niveau)
                        }
                    }
                }
                return -1
            },

            /**
            * Formatte récursivement des données pour les envoyer au serveur.
            * 
            * Transforme les membres :
            * - de type moment dans string YYYY-MM-DD
            * - de type duration en int nombre de mois
            *
            * @param    {Object}    element Objet à formatter
            *
            * @return   {Object}    Objet transformé
            */
            $stringify (element) {
                var i, prop

                if (moment.isMoment(element)) { return element.format("YYYY-MM-DD") }
                
                if (moment.isDuration(element)) { return element.asMonths() }

                if (typeof element === "string" || typeof element === "number" || typeof element === "boolean") {
                    return element
                }

                if (Array.isArray(element)) {
                    for (i = 0; i < element.length; i += 1) {
                        element[i] = this.$stringify(element[i])
                    }
                    return element
                }

                for(prop in element) {
                    if (element.hasOwnProperty(prop)) {
                        element[prop] = this.$stringify(element[prop])
                    }
                }
                return element

            }
        }
    }
</script>