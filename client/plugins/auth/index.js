import AuthToken from './AuthToken'

export default ({ store, app }) => {
  app.authToken = new AuthToken(app.$axios, app.$cookie)

  if (!process.server) {
    app.authToken.initClient(store.getters['auth/token'])
  }

  store.subscribe((mutation) => {
    if (mutation.type === 'auth/SET_TOKEN') {
      app.authToken.set(mutation.payload)
    }

    if (mutation.type === 'auth/FLUSH_TOKEN') {
      app.authToken.remove()
    }
  })
}
