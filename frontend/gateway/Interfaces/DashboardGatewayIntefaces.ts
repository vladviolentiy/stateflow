interface checkAuthResponse{
    userId:number,
    lang:'ru'|'ua'|'by'|'en',
    ip:string,
    ua:string,
    acceptEncoding:string,
    acceptLang:string,
}

interface emailListResponseItem{
    id:number,
    email:string
}

interface phoneListResponseItem{
    id:number,
    phone:string
}

interface editItemGlobal{
    allowAuth:boolean,
    csrf:string
}

interface emailEditItem extends editItemGlobal{
    emailEncrypted:string
}
interface phoneEditItem extends editItemGlobal{
    phoneEncrypted:string
}

export type {checkAuthResponse,emailListResponseItem,emailEditItem,phoneListResponseItem,phoneEditItem}