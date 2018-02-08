import Vue from 'vue'

Vue.use(Vuex)

export default new Vuex.Store ({
  state : {
    user : {
      login : null,
      connected : false,
      fullName : null
    },
    ipUser : {
      login: null,
      fullName: null
    },
    typesEtapes: [],
    axes: [],
    sousAxes: [],
    directions: [],
    server : {
      runningQueries : 0,
      call (action, callback = null, datas = {}) {
        var me = this
        this.runningQueries += 1
        $.post(C.SERVER_URL + action, datas, function (data) {
          me.runningQueries -= 1
          callback(data)
        })
      }
    },
    messages : []
  },
  mutations: {
    logout (state) {
      state.user.connected = false
    },
    setUser (state, user) {
      $.extend(state.user, user)
      state.user.connected = state.user.login ? true : false
    },
    setIpUser (state, user) {
      $.extend(state.ipUser, user)
    },
    ipLogin (state) {
      if (state.ipUser.login) {
        $.extend(state.user, state.ipUser)
        state.user.connected = true
      }
    },
    setTypesEtapes (state, typesEtapes) {
      state.typesEtapes = typesEtapes
    },
    setAxes (state, axes) {
      state.axes = axes
    },
    setSousAxes (state, sousAxes) {
      state.sousAxes = sousAxes
    },
    setDirections (state, directions) {
      state.directions = directions
    }
  },
  actions: {
    connectUserFromIp (context) {
      context.state.server.call (C.IPUSER, function (data) {
        context.commit('setUser', data[C.USER])
        context.commit('setIpUser', data[C.USER])
      })
    },
    displayMessage (context, message) {
      context.state.messages.push(message)
      setTimeout(
        function () { context.state.messages.shift() },
        3000
      )
    },
    connectWithLoginPassword (context, loginDatas) {
      context.state.server.call (C.LOGIN, function (data) {
        context.commit('setUser', data[C.USER])
        if (data[C.USER].login) {
          context.dispatch('displayMessage', { type: 'success', text: 'Connexion réussie !' })
        } else {
          context.dispatch('displayMessage', { type: 'error', text: "Erreur d'authentification" })
        }
      }, loginDatas)
    },
    loadTypesEtapes (context) {
      context.state.server.call (C.TYPES_ETAPES, function (data) {
        context.commit('setTypesEtapes', data[C.TYPES_ETAPES])
      })
    },
    loadAxes (context) {
      context.state.server.call (C.AXES, function (data) {
        context.commit('setAxes', data[C.AXES])
        context.commit('setSousAxes', data[C.SOUS_AXES])
      })
    },
    loadDirections (context) {
      context.state.server.call (C.DIRECTIONS, function (data) {
        context.commit('setDirections', data[C.DIRECTIONS])
      })
    }
  }
})