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
    modale: {
      titre: "",
      contenu: "",
      grande: true,
      confirmation: false,
      onConfirm: function () {}
    },
    typesEtapes: [],
    axes: [],
    sousAxes: [],
    directions: [],
    messages : [],
    utilisateurs: [],
    infosFichiers: [],
    niveauxDroits: [
      { niveau: 0, label: "lecture seule" },
      { niveau: 1, label: "lecture / écriture" },
      { niveau: 2, label: "lecture / écriture / validation" }
    ]
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
    },
    setUtilisateurs (state, utilisateurs) {
      state.utilisateurs = utilisateurs
    },
    addInfoFichier (state, infoFichier) {
      state.infosFichiers[parseInt(infoFichier.num_fichier)] = infoFichier
    }
  },
  actions: {
    displayMessage (context, message) {
      context.state.messages.push(message)
      setTimeout(
        function () { context.state.messages.shift() },
        3000
      )
    }
  }
})