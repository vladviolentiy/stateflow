import type { LocalizationInterface } from './LocalizationInterface'

const LocalizationBy: LocalizationInterface = {
  register: 'Рэгістрацыя',
  authentication: 'Аўтарызацыя',
  logout: 'Выйсці з сістэмы',
  enterAuthString: 'Email, тэлефон або uuid',
  enterPassword: 'Увядзіце пароль',
  enter: 'Увайсці',
  delete: 'Выдаліць',
  next: 'Далей',
  close: 'Зачыніць',
  add: 'Дадаць',
  edit: 'Рэдагаваць',
  validation: {
    fNameNull: 'Імя не ўведзена',
    lNameNull: 'Прозвішча не ўведзена',
    dobNull: 'Дата нараджэння не ўведзена',
    emailNull: 'Email не ўведзены',
    phoneNull: 'Тэлефон не ўведзены',
    emailIncorrect: 'Email уведзены няправільна',
    phoneIncorrect: 'Тэлефон уведзены няправільна',
    dobIncorrect: 'Дата нараджэння ўведзена няправільна',
    passwordNull: 'Пароль не ўведзены',
    passwordNotRepeat: 'Паролі не супадаюць'
  },
  services: 'Сэрвісы',
  welcome: 'Добра пажаваць',
  profile: 'Прафіль',
  allowAuth: 'Дазволіць аўтарызацыю',
  configure: {
    phone: 'Наладка тэлефонаў',
    email: 'Наладка email адрасоў',
    session: 'Кіраванне сесіямі'
  },
  phone: {
    generic: 'Тэлефон',
    add: 'Дадаць тэлефон',
    notAdded: 'Тэлефоны не дададзены'
  },
  email: {
    add: 'Дадаць email',
    notAdded: 'Email адрасы не дададзены'
  },
  errorCodes: {
    0: 'Памылка сеткі',
    1: 'Памылка валідацыі',
    2: 'Памылка запыту да базы даных',
    3: 'Не знойдзена',
    4: 'Пароль уведзены няправільна',
    5: 'Няправільны фармат уводу',
    403: 'Доступ забаронены',
    500: 'Унутраная памылка сэрвісу'
  }
}

export default LocalizationBy
