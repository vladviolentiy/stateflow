import type { LocalizationInterface } from './LocalizationInterface'

const LocalizationBy: LocalizationInterface = {
  register: 'Рэгістрацыя',
  logout: 'Выйсці з сістэмы',
  enter: 'Увайсці',
  delete: 'Выдаліць',
  add: 'Выдаліць',
  next: 'Далей',
  edit: 'Рэдагаваць',
  validation: {
    fNameNull: "Ім'я не ўведзена",
    lNameNull: 'Прозвішча не ўведзена',
    dobNull: 'Дата нараджэння не ўведзена',
    emailNull: 'EmailController не ўведзены',
    phoneNull: 'тэлефон не ўведзены',
    emailIncorrect: 'EmailController уведзены некарэктна',
    phoneIncorrect: 'тэлефон уведзены некарэктна',
    dobIncorrect: 'Дата нараджэння ўведзена некарэктна',
    passwordNotRepeat: 'Паролі не супадаюць',
    passwordNull: 'Пароль не ўведзены'
  },
  services: 'Сэрвісы',
  welcome: 'Сардэчна запрашаем',
  profile: 'Профіль',
  allowAuth: 'Дазволіць аўтарызацыю',
  configure: {
    phone: 'Настройка тэлефонаў',
    email: 'Настройка email адрасоў',
    session: 'Кіраванне сесіямі'
  },
  phone: {
    generic: 'Тэлефон',
    add: 'Дадаць тэлефон',
    notAdded: 'Тэлефоны не дададзеныя'
  },
  email: {
    add: 'Дадаць email',
    notAdded: 'EmailController адрасы не дададзеныя'
  },
  errorCodes: {
    0: 'Памылка сеткі',
    1: 'Памылка валідацыі',
    2: 'Памылка запыту да БД',
    3: 'Не знойдзена',
    4: 'Пароль уведзены няправільна',
    5: 'Няправільны фармат уводу',
    403: 'Доступ забаронены',
    500: 'Унутраная памылка сэрвісу'
  }
}

export default LocalizationBy
