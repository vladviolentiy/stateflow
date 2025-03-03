class AuthenticationMethods {
  public logOut(): void {
    localStorage.removeItem('authToken')
    localStorage.removeItem('password')
    localStorage.removeItem('iv')
    localStorage.removeItem('salt')
  }
}

export default new AuthenticationMethods()
