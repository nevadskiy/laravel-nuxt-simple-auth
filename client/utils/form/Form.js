import Errors from './Errors'

/**
 * This class helps to handle API validation errors and prevents multiple submitting.
 */
export default class Form {
  /**
   * Form constructor.
   * @param data
   */
  constructor (data) {
    this.originalData = data
    this.isPending = false
    this.initFields(data)
    this.initErrors(data)
  }

  /**
   * Init form fields.
   * @param data
   */
  initFields (data) {
    Object.keys(data).forEach((field) => {
      this[field] = data[field]
    })
  }

  /**
   * Init form errors.
   * @param data
   */
  initErrors (data) {
    this.errors = new Errors(Object.keys(data))
  }

  /**
   * Get the form data without any non relevant fields.
   */
  data () {
    const data = {}

    Object.keys(this.originalData).forEach((property) => {
      data[property] = this[property]
    })

    return data
  }

  /**
   * Get the form field value.
   * @param field
   * @returns {null|*}
   */
  get (field) {
    return this[field]
  }

  /**
   * Transform the form data by merging with the given data object.
   * @param data
   */
  transform (data) {
    const transformed = this.data()

    Object.keys(data).forEach((property) => {
      transformed[property] = data[property]
    })

    return transformed
  }

  /**
   * Reset form fields and errors.
   */
  reset () {
    this.initFields(this.originalData)
    this.errors.clear()
  }

  /**
   * Set errors.
   * @param errors
   */
  setErrors (errors) {
    this.errors.assign(errors)
  }

  /**
   * Submit form using promised callback.
   * @param callback
   * @returns {Promise<*>}
   */
  async submitUsing (callback) {
    console.log('form is submitting...')

    if (this.isPending) {
      throw new Error('Request is pending...')
    }

    if (this.errors.any()) {
      throw new Error('Validation errors are not resolved.')
    }

    this.isPending = true

    try {
      await callback(this.data())
    } catch (error) {
      if (this.isValidationError(error)) {
        this.setErrors(error.response.data.errors)
      }
      throw error
    } finally {
      this.isPending = false
    }
  }

  isValidationError (error) {
    return error &&
      error.response &&
      error.response.data &&
      error.response.data.errors &&
      error.response.data.message === 'The given data was invalid.'
  }
}
