abstract class Requests {
  private token: string | null
  private prefix = ''
  constructor(token: string | null = null) {
    this.token = token
  }

  /* eslint-disable-next-line @typescript-eslint/no-explicit-any */
  protected executeGet(url: string): Promise<any> {
    return fetch(this.prefix + url, {
      method: 'GET',
      headers: {
        Authorization: this.token ?? ''
      },
      credentials: 'include'
    }).then(response => response.json())
  }
  /* eslint-disable-next-line @typescript-eslint/no-explicit-any */
  protected executePost(url: string, params: FormData): Promise<any> {
    return fetch(this.prefix + url, {
      method: 'POST',
      headers: {
        Authorization: this.token ?? ''
      },
      credentials: 'include',
      body: params
    }).then(response => response.json())
  }
}

export default Requests
