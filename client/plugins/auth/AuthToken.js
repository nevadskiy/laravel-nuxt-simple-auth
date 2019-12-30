export default class AuthToken {
  /**
   * Auth token constructor.
   * @param $axios
   * @param $cookie
   */
  constructor ($axios, $cookie) {
    this.$axios = $axios
    this.$cookie = $cookie
  }

  /**
   * Init auth token on client. Needed because on server is used another client instance.
   * @param token
   */
  initClient (token) {
    this.$axios.setToken(token, 'Bearer')
  }

  /**
   * Set the auth token.
   * @param token
   */
  set (token) {
    this.$axios.setToken(token, 'Bearer')
    this.$cookie.set('token', token, {
      path: '/',
      maxAge: 60 * 60 * 24 * 7 * 365
    })
  }

  /**
   * Get the auth token.
   * @returns {*}
   */
  get () {
    return this.$cookie.get('token')
  }

  /**
   * Remove the auth token.
   */
  remove () {
    this.$axios.setToken(null)
    this.$cookie.remove('token')
  }
}
