export const state = () => ({
  token: '',
  user: null,
  intendedUrl: null
})

export const getters = {
  auth (state) {
    return !!state.user
  },

  guest (state) {
    return !state.user
  },

  user (state) {
    return state.user
  },

  token (state) {
    return state.token
  },

  intendedUrl (state) {
    return state.intendedUrl
  }
}

export const mutations = {
  SET_TOKEN (state, token) {
    state.token = token
  },

  FLUSH_TOKEN (state) {
    state.token = null
  },

  SET_USER (state, user) {
    state.user = user
  },

  SET_INTENDED_URL (state, url) {
    state.intendedUrl = url
  }
}

export const actions = {
  async nuxtServerInit ({ dispatch }) {
    const token = this.app.authToken.get()

    if (!token) {
      return
    }

    await dispatch('attempt', token)
  },

  signUp (_, { email, password }) {
    return this.$axios.post('/api/auth/signup', { email, password })
  },

  async signIn ({ dispatch }, { email, password }) {
    const response = await this.$axios.post('/api/auth/signin', { email, password })
    await dispatch('attempt', response.data.api_token)
  },

  async attempt ({ commit }, token) {
    console.log('attemping...')
    console.log(token)
    commit('SET_TOKEN', token)

    try {
      const response = await this.$axios.get(`/api/auth/user`)
      commit('SET_USER', response.data.data)
      console.log('AUTH SUCCESS')
      return response
    } catch (error) {
      commit('FLUSH_TOKEN')
      commit('SET_USER', null)
      console.log('AUTH FAILED')
    }
  },

  async signOut ({ commit }) {
    await this.$axios.delete('/api/auth/signout')
    commit('FLUSH_TOKEN')
    commit('SET_USER', null)
  },

  passwordForgot ({ dispatch }, { email }) {
    return this.$axios.post('/api/auth/password/forgot', { email })
  },

  passwordReset ({ dispatch }, { email, password, token }) {
    return this.$axios.put('/api/auth/password/reset', { email, password, token })
  },

  setIntendedUrl ({ commit }, url) {
    commit('SET_INTENDED_URL', url)
  }
}
