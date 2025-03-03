import type { errorCodeList } from '@/localization/CustomInterfaces'

interface successResponse<T> {
  success: true
  data: T
}

interface successResponseText {
  success: true
  text: string
}

interface successResponseNull {
  success: true
}

interface errorResponse {
  success: false
  text: string
  code: errorCodeList
}

type response<T> = successResponse<T> | errorResponse
type responseText = successResponseText | errorResponse
type responseNull = successResponseNull | errorResponse

export type { response, responseText, responseNull }
