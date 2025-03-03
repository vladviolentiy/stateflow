class Validation {
  public isEmail(value: string): boolean {
    const result = value.match(/^[a-zA-Z0-9]+(?:\.[a-zA-Z0-9]+)*@[a-zA-Z0-9]+(?:\.[a-zA-Z0-9]+)*$/)
    return result !== null
  }

  public isPhone(value: string): boolean {
    const result = value.match(/^[0-9]{10-15}$/)
    return result !== null
  }

  public isUUID(value: string): boolean {
    const result = value.match(/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/)
    return result !== null
  }
}

export default new Validation()
