export default function ({ store, redirect }) {
  if (!store.getters['auth/guest']) {
    return redirect({ name: 'index' })
  }
}
