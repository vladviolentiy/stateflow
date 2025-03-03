import Requests from './Requests'
import type {
  checkAuth,
  randomString,
  successAuth,
  successRegister
} from './Interfaces/AuthGatewayInterface'
import type { response } from './Interfaces/GeneralGatewayInterfaces'

class AuthGateway extends Requests {
  public preAuth(
    authString: string,
    type: 'uuid' | 'email' | 'phone'
  ): Promise<response<checkAuth>> {
    const formData = new FormData()
    formData.append('authString', authString)
    formData.append('type', type)
    return this.executePost('/api/id/checkIssetClient', formData)
  }

  public passwordAuth(
    authString: string,
    authStringType: 'uuid' | 'email' | 'phone',
    password: string
  ): Promise<response<successAuth>> {
    const formData = new FormData()
    formData.append('authString', authString)
    formData.append('authStringType', authStringType)
    formData.append('password', password)
    return this.executePost('/api/id/passwordAuth', formData)
  }

  public registerNewUser(
    passwordHash: string,
    iv: string,
    salt: string,
    publicKey: string,
    encryptedPrivateKey: string,
    fNameEncrypted: string,
    lNameEncrypted: string,
    bDayEncrypted: string,
    globalHash: string
  ): Promise<response<successRegister>> {
    const formData = new FormData()
    formData.append('password', passwordHash)
    formData.append('iv', iv)
    formData.append('salt', salt)
    formData.append('publicKey', publicKey)
    formData.append('encryptedPrivateKey', encryptedPrivateKey)
    formData.append('fNameEncrypted', fNameEncrypted)
    formData.append('lNameEncrypted', lNameEncrypted)
    formData.append('bDayEncrypted', bDayEncrypted)
    formData.append('hash', globalHash)
    return this.executePost('/api/id/register', formData)
  }

  public getRandom(): Promise<response<randomString>> {
    return this.executeGet('/api/id/getRandom')
  }
}

export default new AuthGateway()
