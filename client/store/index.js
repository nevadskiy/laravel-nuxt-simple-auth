export const actions = {
  nuxtServerInit ({ dispatch }) {
    return Promise.all([dispatch('auth/serverInit')])
  }
}
