export const state = () => ({
  token: '',
  user: null
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
  }
}

export const actions = {
  serverInit ({ dispatch }) {
    return dispatch('attempt', this.$cookie.get('token'))
  },

  signup (_, { email, password }) {
    return this.$axios.post('/api/auth/signup', { email, password })
  },

  async signin ({ dispatch }, { email, password }) {
    const response = await this.$axios.post('/api/auth/signin', { email, password })
    await dispatch('attempt', response.data.api_token)
  },

  async attempt ({ commit }, token) {
    commit('SET_TOKEN', token)

    try {
      const response = await this.$axios.get('/api/auth/user')
      commit('SET_USER', response.data.data)
      return response
    } catch (error) {
      commit('FLUSH_TOKEN')
      commit('SET_USER', null)
    }
  },

  async signout ({ commit }) {
    await this.$axios.delete('/api/auth/signout')
    commit('FLUSH_TOKEN')
    commit('SET_USER', null)
  },

  forgot ({ dispatch }, { email }) {
    return this.$axios.post('/api/auth/password/forgot', { email })
  },

  reset ({ dispatch }, { email, password, token }) {
    return this.$axios.put('/api/auth/password/reset', { email, password, token })
  }
}
