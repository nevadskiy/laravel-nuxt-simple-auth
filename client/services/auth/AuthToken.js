export default class AuthToken {
  constructor ($axios, $cookie) {
    this.$axios = $axios
    this.$cookie = $cookie
  }

  init (token) {
    this.$axios.setToken(token, 'Bearer')
    console.log('initToken')
    console.log(token)
  }

  set (token) {
    this.$axios.setToken(token, 'Bearer')
    this.$cookie.set('token', token, { maxAge: 60 * 60 * 24 * 7 * 365 })
    console.log('setToken')
    console.log(token)
  }

  remove () {
    this.$axios.setToken(null)
    this.$cookie.remove('token')
    console.log('removeToken')
  }
}
