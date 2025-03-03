import Requests from './Requests'
import type { response } from './Interfaces/GeneralGatewayInterfaces'
import type {
  checkAuthResponse,
  emailEditItem,
  emailListResponseItem,
  phoneEditItem,
  phoneListResponseItem,
  sessionListResponseItem
} from './Interfaces/DashboardGatewayIntefaces'

class DashboardGateway extends Requests {
  constructor(token: string) {
    super(token)
  }

  public checkAuth(): Promise<response<checkAuthResponse>> {
    return this.executeGet('/api/id/checkAuth')
  }

  public insertMetaHashInfo(
    ip: string,
    ua: string,
    al: string,
    ae: string,
    lastSeen: string
  ): Promise<response<emailListResponseItem[]>> {
    const formData = new FormData()
    formData.append('ip', ip)
    formData.append('ua', ua)
    formData.append('al', al)
    formData.append('ae', ae)
    formData.append('lastSeen', lastSeen)
    return this.executePost('/api/id/writeMeta', formData)
  }

  public killSession(
    hash: string,
    returnAvailable: boolean
  ): Promise<response<sessionListResponseItem[]>> {
    const formData = new FormData()
    formData.append('hash', hash)
    formData.append('returnAvailable', returnAvailable ? '1' : '0')
    return this.executePost('/api/id/killSession', formData)
  }

  public getEmailList(): Promise<response<emailListResponseItem[]>> {
    return this.executeGet('/api/id/email/get')
  }
  public getEmailItem(id: number): Promise<response<emailEditItem>> {
    const formData = new FormData()
    formData.append('id', String(id))
    return this.executePost('/api/id/email/getItem', formData)
  }

  public addNewEmail(
    emailEncrypted: string,
    emailHash: string,
    allowAuth: boolean
  ): Promise<response<emailListResponseItem[]>> {
    const formData = new FormData()
    formData.append('emailEncrypted', emailEncrypted)
    formData.append('emailHash', emailHash)
    formData.append('allowAuth', allowAuth ? '1' : '0')
    return this.executePost('/api/id/email/add', formData)
  }

  public editEmailItem(
    id: number,
    emailEncrypted: string,
    emailHash: string,
    allowAuth: boolean
  ): Promise<response<emailListResponseItem[]>> {
    const formData = new FormData()
    formData.append('itemId', String(id))
    formData.append('emailEncrypted', emailEncrypted)
    formData.append('emailHash', emailHash)
    formData.append('allowAuth', allowAuth ? '1' : '0')
    return this.executePost('/api/id/email/edit', formData)
  }

  public deleteEmail(id: number): Promise<response<emailListResponseItem[]>> {
    const formData = new FormData()
    formData.append('id', String(id))
    return this.executePost('/api/id/email/delete', formData)
  }

  public getPhoneList(): Promise<response<phoneListResponseItem[]>> {
    return this.executeGet('/api/id/phone/get')
  }

  public getSessionsList(): Promise<response<sessionListResponseItem[]>> {
    return this.executeGet('/api/id/session/get')
  }

  public addNewPhone(
    encryptedPhone: string,
    phoneHash: string,
    allowAuth: boolean
  ): Promise<response<phoneListResponseItem[]>> {
    const formData = new FormData()
    formData.append('phoneEncrypted', encryptedPhone)
    formData.append('phoneHash', phoneHash)
    formData.append('allowAuth', allowAuth ? '1' : '0')
    return this.executePost('/api/id/phone/add', formData)
  }

  public getPhoneItem(id: number): Promise<response<phoneEditItem>> {
    const formData = new FormData()
    formData.append('id', String(id))
    return this.executePost('/api/id/phone/getItem', formData)
  }

  public editPhoneItem(
    id: number,
    encryptedPhone: string,
    phoneHash: string,
    allowAuth: boolean
  ): Promise<response<phoneListResponseItem[]>> {
    const formData = new FormData()
    formData.append('itemId', String(id))
    formData.append('phoneEncrypted', encryptedPhone)
    formData.append('phoneHash', phoneHash)
    formData.append('allowAuth', allowAuth ? '1' : '0')
    return this.executePost('/api/id/phone/add', formData)
  }

  public deletePhone(id: number): Promise<response<phoneListResponseItem[]>> {
    const formData = new FormData()
    formData.append('id', String(id))
    return this.executePost('/api/id/phone/delete', formData)
  }

  public getGeneralInfo(): Promise<
    response<{ fNameEncrypted: string; lNameEncrypted: string; bDayEncrypted: string }>
  > {
    return this.executeGet('/api/id/getBasicInfo')
  }
}

export default DashboardGateway
