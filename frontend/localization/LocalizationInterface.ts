import type { errorCodeList } from './CustomInterfaces'

type countryCodes = 'ru' | 'by' | 'ua' | 'en';

interface LocalizationInterface {
  register: string
  logout: string
  enter: string
  next: string
  delete: string
  add: string
  edit: string
  validation: {
    fNameNull: string
    lNameNull: string
    dobNull: string
    emailNull: string
    phoneNull: string
    phoneIncorrect: string
    emailIncorrect: string
    dobIncorrect: string
    passwordNull: string
    passwordNotRepeat: string
  }
  services: string
  welcome: string
  profile: string
  allowAuth: string
  configure: {
    phone: string
    email: string
    session: string
  }
  phone: {
    generic: string
    add: string
    notAdded: string
  }
  email: {
    add: string
    notAdded: string
  }
  errorCodes: Record<errorCodeList, string>
}

export type { LocalizationInterface, countryCodes }
