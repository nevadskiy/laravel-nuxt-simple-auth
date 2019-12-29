export default function ({ store, route, redirect }) {
  if (!store.getters['auth/auth']) {
    store.dispatch('auth/setIntendedUrl', route.fullPath)
    return redirect({ name: 'auth-signin' })
  }
}
