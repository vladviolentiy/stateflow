import type { LocalizationInterface } from './LocalizationInterface'

const LocalizationUa: LocalizationInterface = {
  register: 'Реєстрація',
  logout: 'Вийти з системи',
  enter: 'Увійти',
  delete: 'Видалити',
  add: 'Додати',
  next: 'Далі',
  edit: 'Редагувати',
  validation: {
    fNameNull: "Ім'я не введено",
    lNameNull: 'прізвище не введено',
    dobNull: 'Дата народження не введена',
    emailNull: 'EmailController не введено',
    phoneNull: 'Телефон не введений',
    emailIncorrect: 'EmailController введено некоректно',
    phoneIncorrect: ' Телефон введено некоректно',
    dobIncorrect: 'Дата народження введена некоректно',
    passwordNotRepeat: 'Паролі не співпадають',
    passwordNull: 'Пароль не введено'
  },
  services: 'Сервіси',
  welcome: 'Ласкаво просимо',
  profile: 'Профіль',
  allowAuth: 'Дозволити авторизацію',
  configure: {
    phone: 'Налаштування телефонів',
    email: 'Налаштування email адрес',
    session: 'Управління сесіями'
  },
  phone: {
    generic: 'Телефон',
    add: 'Додати телефон',
    notAdded: 'Телефони не додані'
  },
  email: {
    add: 'Додати email',
    notAdded: 'EmailController адреси не додані'
  },
  errorCodes: {
    0: 'Помилка мережі',
    1: 'Помилка валідації',
    2: 'Помилка запиту до БД',
    3: 'Не знайдено',
    4: 'Пароль введено невірно',
    5: 'Неправильний формат введення',
    403: 'Доступ заборонено',
    500: 'Внутрішня помилка сервісу'
  }
}

export default LocalizationUa
