export default class Errors {
  /**
   * Errors constructor.
   * @param fields
   */
  constructor (fields) {
    this.fields = fields
    this.initErrors(fields)
  }

  /**
   * Init empty form errors.
   */
  initErrors (fields) {
    this.errors = {}

    fields.forEach((field) => {
      this.errors[field] = []
    })
  }

  /**
   * Determine whether the given field contains any error.
   * @param field
   * @returns {boolean}
   */
  has (field) {
    return this.errors[field] && this.errors[field].length
  }

  /**
   * TODO: include only errors from initial fields (ignore another fields)
   * Determine whether any field contains any error.
   * @returns {boolean}
   */
  any () {
    return Object.values(this.errors).some((errors) => {
      return errors.length
    })
  }

  /**
   * Get the first error of the given field.
   * @param field
   * @returns {null|*}
   */
  first (field) {
    if (this.errors[field]) {
      return this.errors[field][0]
    }

    return null
  }

  /**
   * Get all errors of the given field.
   * @param field
   * @returns {null|*}
   */
  get (field) {
    return this.errors[field]
  }

  /**
   * Assign errors.
   * @param errors
   */
  assign (errors) {
    Object.keys(errors).forEach((field) => {
      this.errors[field] = errors[field]
    })
  }

  /**
   * Clear the field errors.
   * @param field
   */
  clear (field) {
    this.errors[field] = []
  }

  /**
   * Reset errors for all fields
   */
  reset () {
    Object.keys(this.errors).forEach((field) => {
      this.errors[field] = []
    })
  }
}
