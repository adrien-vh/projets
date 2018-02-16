<script>
    let rq = 0
    export default {
      methods: {
        $$ServerCall (action, callback = null, datas = {}) {
          var me = this
          rq += 1
          $.post(C.SERVER_URL + action, datas, function (data) {
            var i
            rq -= 1
            for (i = 0; i < data[C.MESSAGES].length; i += 1) {
              me.$store.dispatch('displayMessage', data[C.MESSAGES][i])
            }

            if (typeof data[C.INFOS_FICHIERS] !== 'undefined') {
              for (i = 0; i < data[C.INFOS_FICHIERS].length; i += 1) {
                me.$store.commit('addInfoFichier', data[C.INFOS_FICHIERS][i])
              }
            }
            callback(data)
          })
        },
        $$runningQueries () {
          return rq
        },
        $$loadAxes () {
          var me = this
          this.$$ServerCall (C.AXES, function (data) {
            me.$store.commit('setAxes', data[C.AXES])
            me.$store.commit('setSousAxes', data[C.SOUS_AXES])
          })
        },
        $$loadTypesEtapes () {
          var me = this
          this.$$ServerCall (C.TYPES_ETAPES, function (data) {
            me.$store.commit('setTypesEtapes', data[C.TYPES_ETAPES])
          })
        },
        $$loadDirections () {
          var me = this
          this.$$ServerCall (C.DIRECTIONS, function (data) {
            me.$store.commit('setDirections', data[C.DIRECTIONS])
          })
        },
        $$connectUserFromIp () {
          var me = this
          this.$$ServerCall (C.IPUSER, function (data) {
            me.$store.commit('setUser', data[C.USER])
            me.$store.commit('setIpUser', data[C.USER])
          })
        },
        $$connectWithLoginPassword (loginDatas) {
          var me = this
          this.$$ServerCall (C.LOGIN, function (data) {
            me.$store.commit('setUser', data[C.USER])
            /*if (data[C.USER].login) {
              me.$store.dispatch('displayMessage', { type: 'success', text: 'Connexion réussie !' })
            } else {
              me.$store.dispatch('displayMessage', { type: 'error', text: "Erreur d'authentification" })
            }*/
          }, loginDatas)
        },
        $$loadUtilisateurs () {
          var me = this
          this.$$ServerCall (C.UTILISATEURS, function (data) {
            me.$store.commit('setUtilisateurs', data[C.UTILISATEURS])
          })
          
        }
      }
    }
</script>