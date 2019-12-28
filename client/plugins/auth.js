import AuthToken from '~/services/auth/AuthToken'

export default ({ store, app: { $axios, $cookie } }) => {
  const authToken = new AuthToken($axios, $cookie)

  if (!process.server) {
    authToken.init(store.getters['auth/token'])
  }

  store.subscribe((mutation) => {
    if (mutation.type === 'auth/SET_TOKEN') {
      authToken.set(mutation.payload)
    }

    if (mutation.type === 'auth/FLUSH_TOKEN') {
      authToken.remove()
    }
  })
}
