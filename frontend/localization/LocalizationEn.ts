import type { LocalizationInterface } from './LocalizationInterface'

const LocalizationEn: LocalizationInterface = {
  register: 'Register',
  authentication: 'Authentication',
  logout: 'Log out',
  enter: 'Sign in',
  enterAuthString: 'Email, phone or uuid',
  enterPassword: 'Enter password',
  next: 'Next',
  delete: 'Delete',
  close: 'Close',
  add: 'Add',
  edit: 'Edit',
  validation: {
    fNameNull: 'First name is not entered',
    lNameNull: 'Surname not entered',
    dobNull: 'Date of birth not entered',
    emailNull: 'Email not entered',
    phoneNull: 'Phone not entered',
    emailIncorrect: 'Email entered incorrectly',
    phoneIncorrect: 'Phone entered incorrectly',
    dobIncorrect: 'Date of birth entered incorrectly',
    passwordNotRepeat: "Passwords don't match",
    passwordNull: 'Password not entered'
  },
  services: 'Services',
  welcome: 'Welcome',
  profile: 'Profile',
  allowAuth: 'Allow authorization',
  configure: {
    phone: 'Configuring phones',
    email: 'Configuring email addresses',
    session: 'Session management'
  },
  phone: {
    generic: 'Phone',
    add: 'Add a phone number',
    notAdded: 'Phones not added'
  },
  email: {
    add: 'Add an email',
    notAdded: 'Email addresses have not been added'
  },
  errorCodes: {
    0: 'Network error',
    1: 'Validation Error',
    2: 'Error request to database',
    3: 'Not found',
    4: 'Incorrect password',
    5: 'Invalid input format',
    403: 'Access denied',
    500: 'Internal server error'
  }
}

export default LocalizationEn
