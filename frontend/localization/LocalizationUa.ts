import type { LocalizationInterface } from './LocalizationInterface'

const LocalizationUa: LocalizationInterface = {
  register: 'Реєстрація',
  authentication: 'Авторизація',
  logout: 'Вийти',
  enter: 'Увійти',
  delete: 'Видалити',
  enterAuthString: 'Email, телефон або UUID',
  enterPassword: 'Введіть пароль',
  add: 'Додати',
  close: 'Закрити',
  next: 'Далі',
  edit: 'Редагувати',
  validation: {
    fNameNull: "Ім'я не введене",
    lNameNull: 'Прізвище не введене',
    dobNull: 'Дата народження не введена',
    emailNull: 'Email не введений',
    phoneNull: 'Телефон не введений',
    emailIncorrect: 'Email введений невірно',
    phoneIncorrect: 'Телефон введений невірно',
    dobIncorrect: 'Дата народження введена невірно',
    passwordNotRepeat: 'Паролі не співпадають',
    passwordNull: 'Пароль не введений'
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
    notAdded: 'Email адреси не додані'
  },
  errorCodes: {
    0: 'Помилка мережі',
    1: 'Помилка валідації',
    2: 'Помилка запиту до бази даних',
    3: 'Не знайдено',
    4: 'Невірний пароль',
    5: 'Невірний формат введення',
    403: 'Доступ заборонено',
    500: 'Внутрішня помилка сервісу'
  }
}

export default LocalizationUa
