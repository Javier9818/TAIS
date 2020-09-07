import {helpers} from 'vuelidate/lib/validators'

export const text = helpers.regex('alpha', /^[a-zA-Z0-9À-ÿ.\u00f1\u00d1\s]*$/)
export const nombreText = helpers.regex('alpha', /^[a-zA-ZÀ-ÿ\u00f1\u00d1\s]*$/)

export const nameObjects = helpers.regex('alpha', /^[a-zA-Z0-9À-ÿ\u00f1\u00d1\s]*$/)
export const decimalPositive = helpers.regex('alpha', /^[0-9]+\.?[0-9]*$/)

export default text