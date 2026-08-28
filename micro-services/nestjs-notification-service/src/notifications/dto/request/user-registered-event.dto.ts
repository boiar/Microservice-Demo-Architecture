export class UserRegisteredEventDto{
  data: {
    id: string | number,
    name?: string,
    email: string
  }

  timestamp?: string
}